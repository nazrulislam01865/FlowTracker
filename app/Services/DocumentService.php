<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Task;
use App\Models\User;
use App\Support\StoredFileResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class DocumentService
{
    public function query(User $user, array $filters = [], string $permissionModule = 'documents')
    {
        $query = app(AccessControlService::class)->applyDocumentScope(Document::query(), $user, $permissionModule)
            ->with(['job.client','job.phase','job.tasks' => fn ($q) => app(AccessControlService::class)->applyTaskScope($q, $user),'task.phase','task.assignee','uploader']);

        return $query
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(fn ($x) => $x
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('document_number', "%{$search}%")
                ->orWhereHas('job', fn ($j) => $j->whereLike('job_number', "%{$search}%")->orWhereLike('title', "%{$search}%")->orWhereHas('client', fn ($client) => $client->whereLike('name', "%{$search}%")))
                ->orWhereHas('client', fn ($client) => $client->whereLike('name', "%{$search}%"))
                ->orWhereHas('task', fn ($t) => $t->whereLike('title', "%{$search}%"))))
            ->when($filters['category'] ?? null, fn ($q, $value) => $q->where('category', $value))
            ->when($filters['client'] ?? null, fn ($q, $value) => $q->where('client_id', $value))
            ->when($filters['job'] ?? null, fn ($q, $value) => $q->where('flow_job_id', $value))
            ->when($filters['phase'] ?? null, fn ($q, $value) => $q->whereHas('task.phase', fn ($phase) => $phase->where(fn ($x) => $x->where('workflow_phases.id', $value)->orWhere('workflow_phases.source_workflow_phase_id', $value))))
            ->when($filters['status'] ?? null, function ($q, $value) {
                match ($value) {
                    'approved' => $q->where('is_final', true),
                    'needs_action' => $q->where('is_final', false)->whereHas('task', fn ($t) => $t->where('needs_attention', true)),
                    'awaiting_approval' => $q->where('is_final', false)->whereHas('task', fn ($t) => $t->whereIn('status', ['In Review','Waiting for Internal Approval'])),
                    'recent' => $q->where('updated_at', '>=', now()->subDays(7)),
                    'current' => $q->where('is_final', false)->where(fn ($x) => $x->whereDoesntHave('task')->orWhereHas('task', fn ($t) => $t->where('needs_attention', false))),
                    default => null,
                };
            });
    }

    public function list(User $user, array $filters = [], string $permissionModule = 'documents')
    {
        return $this->query($user, $filters, $permissionModule)->latest()->limit(250)->get();
    }

    public function paginate(User $user, array $filters = [], int $perPage = 25, string $permissionModule = 'documents')
    {
        return $this->query($user, $filters, $permissionModule)->latest()->paginate($perPage);
    }

    public function store(UploadedFile $file, array $data, User $user, string $permissionModule = 'documents'): Document
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($user, $permissionModule, 'create'), 403);

        $task = null;
        if (!empty($data['task_id'])) {
            $task = Task::with(['job','documentCategory','setupTemplate.documentCategory'])->findOrFail((int) $data['task_id']);
            if ($permissionModule === 'document_archive') {
                $access->applyDocumentArchiveTaskScope(Task::query()->whereKey($task->id), $user)->firstOrFail();
                $access->applyDocumentArchiveJobScope(\App\Models\FlowJob::query()->whereKey($task->flow_job_id), $user)->firstOrFail();
            } else {
                $access->applyTaskScope(Task::query()->whereKey($task->id), $user)->firstOrFail();
                app(JobService::class)->findVisible($user, (int) $task->flow_job_id);
            }
            if (!empty($data['flow_job_id'])) abort_unless((int) $task->flow_job_id === (int) $data['flow_job_id'], 422, 'The selected document task does not belong to this Job.');
            if (($data['require_task_pack_requirement'] ?? false) === true) abort_unless($this->taskHasRequirement($task), 422, 'This task has no Task Pack document requirement.');
        } elseif (!empty($data['flow_job_id'])) {
            if ($permissionModule === 'document_archive') {
                $access->applyDocumentArchiveJobScope(\App\Models\FlowJob::query()->whereKey((int) $data['flow_job_id']), $user)->firstOrFail();
            } else {
                app(JobService::class)->findVisible($user, (int) $data['flow_job_id']);
            }
        }

        $jobId = $task?->flow_job_id ?: ($data['flow_job_id'] ?? 'general');
        $stored = app(SecureDocumentStorage::class)->store($file, 'flowtrack/documents/'.$jobId);
        $path = $stored['path'];

        $category = $task?->documentCategory?->name
            ?: $task?->setupTemplate?->documentCategory?->name
            ?: ($data['category'] ?? ($task ? 'Task attachment' : 'Other'));

        $isArtworkTask = $task
            && app(OrderWorkflowActionService::class)->automationKey($task) === 'ART_PREPARE_UPLOAD';
        $batchVersion = max(0, (int) ($data['artwork_batch_version'] ?? 0));
        $version = $isArtworkTask && $batchVersion > 0
            ? $batchVersion
            : $this->nextDocumentVersion(
                $task,
                $task?->flow_job_id ?: ($data['flow_job_id'] ?? null),
                $task?->id ?: ($data['task_id'] ?? null),
                $category,
                $file->getClientOriginalName(),
            );

        $document = Document::create([
            'document_number' => $this->nextNumber(),
            'flow_job_id' => $task?->flow_job_id ?: ($data['flow_job_id'] ?? null),
            'client_id' => $task?->job?->client_id ?: ($data['client_id'] ?? null),
            'task_id' => $task?->id ?: ($data['task_id'] ?? null),
            'uploaded_by' => $user->id,
            'category' => $category,
            'name' => $file->getClientOriginalName(),
            'note' => filled($data['note'] ?? null) ? trim((string) $data['note']) : null,
            'path' => $path,
            'mime_type' => StoredFileResponse::mimeType($file->getClientOriginalName(), $stored['mime']),
            'size' => $stored['size'],
            'version' => $version,
            'is_final' => false,
        ]);

        $this->recordDocumentActivity($document, $user, 'uploaded');
        $this->notifyDocumentChange($document, $user, 'uploaded');
        return $document;
    }

    /**
     * Store a user-selected file set. Artwork files selected together are one
     * revision and therefore share a version number; later artwork batches
     * increment that version once. Other document types retain their existing
     * per-file version behaviour.
     *
     * @param array<int,UploadedFile> $files
     * @return Collection<int,Document>
     */
    public function storeMany(array $files, array $data, User $user, string $permissionModule = 'documents'): Collection
    {
        $documents = collect();
        $artworkBatchVersion = null;

        foreach (array_values($files) as $file) {
            $fileData = $data;
            if ($artworkBatchVersion !== null) {
                $fileData['artwork_batch_version'] = $artworkBatchVersion;
            }

            $document = $this->store($file, $fileData, $user, $permissionModule);
            $documents->push($document);

            $document->loadMissing('task.setupTemplate');
            if ($artworkBatchVersion === null
                && $document->task
                && app(OrderWorkflowActionService::class)->automationKey($document->task) === 'ART_PREPARE_UPLOAD') {
                $artworkBatchVersion = max(1, (int) $document->version);
            }
        }

        return $documents;
    }

    public function linkExisting(Document $source, Task $task, User $user, bool $allowGenericAttachment = false, ?string $note = null): Document
    {
        abort_unless(app(AccessControlService::class)->can($user, 'documents', 'link'), 403);
        app(AccessControlService::class)->applyDocumentScope(Document::query()->whereKey($source->id), $user)->firstOrFail();
        app(AccessControlService::class)->applyTaskScope(Task::query()->whereKey($task->id), $user)->firstOrFail();
        app(JobService::class)->findVisible($user, (int) $task->flow_job_id);
        $task->loadMissing(['job','documentCategory','setupTemplate.documentCategory']);
        if (!$allowGenericAttachment) abort_unless($this->taskHasRequirement($task), 422, 'The selected task does not define a Task Pack document requirement.');

        $existing = Document::where('task_id', $task->id)->where('path', $source->path)->first();
        if ($existing) return $existing;
        $category = $task->documentCategory?->name ?: $task->setupTemplate?->documentCategory?->name ?: 'Task attachment';
        $version = $this->nextDocumentVersion(
            $task,
            $task->flow_job_id,
            $task->id,
            $category,
            $source->name,
        );

        $document = Document::create([
            'document_number' => $this->nextNumber(), 'flow_job_id' => $task->flow_job_id, 'client_id' => $task->job?->client_id,
            'task_id' => $task->id, 'uploaded_by' => $user->id, 'category' => $category, 'name' => $source->name,
            'note' => filled($note) ? trim((string) $note) : null, 'path' => $source->path, 'mime_type' => $source->mime_type, 'size' => $source->size, 'version' => max(1, $version), 'is_final' => (bool) $source->is_final,
        ]);
        $this->recordDocumentActivity($document, $user, 'linked');
        $this->notifyDocumentChange($document, $user, 'linked');
        return $document;
    }

    public function delete(Document $document, ?User $actor = null, string $permissionModule = 'documents'): void
    {
        if ($actor) {
            abort_unless(app(AccessControlService::class)->can($actor, $permissionModule, 'delete'), 403);
            app(AccessControlService::class)->applyDocumentScope(Document::query()->whereKey($document->id), $actor, $permissionModule)->firstOrFail();
        }
        $document->loadMissing(['task','job']);
        $name = $document->name; $path = $document->path;
        if ($actor) {
            if ($document->task) $document->task->activities()->create(['user_id'=>$actor->id,'event'=>'task.document_deleted','description'=>'Document removed: '.$name,'meta'=>['document_id'=>$document->id,'name'=>$name]]);
            if ($document->job) $document->job->activities()->create(['user_id'=>$actor->id,'event'=>'job.document_deleted','description'=>'Document removed'.($document->task?' from '.$document->task->title:'').': '.$name,'meta'=>['document_id'=>$document->id,'name'=>$name,'task_id'=>$document->task_id]]);
            $this->notifyDocumentChange($document, $actor, 'removed');
        }
        $document->delete();
        if ($path && !Document::where('path', $path)->exists()) app(SecureDocumentStorage::class)->delete($path);
    }

    public function taskHasRequirement(Task $task): bool
    {
        return (bool) ($task->document_category_id ?: $task->setupTemplate?->document_category_id);
    }

    public function rename(Document $document, string $name, User $user, string $permissionModule = 'documents'): void
    {
        abort_unless(app(AccessControlService::class)->can($user, $permissionModule, 'edit'), 403);
        app(AccessControlService::class)->applyDocumentScope(Document::query()->whereKey($document->id), $user, $permissionModule)->firstOrFail();

        $name = trim($name);
        abort_if($name === '' || str_contains($name, '/') || str_contains($name, '\\'), 422, 'Enter a valid file name.');

        $oldName = (string) $document->name;
        Document::query()
            ->where('flow_job_id', $document->flow_job_id)
            ->where('task_id', $document->task_id)
            ->where('category', $document->category)
            ->where('name', $oldName)
            ->update(['name' => $name, 'updated_at' => now()]);

        // The grouped rename intentionally uses one SQL UPDATE, so Eloquent
        // model observers do not fire for the affected Document rows. Publish
        // one shared invalidation explicitly instead.
        app(WorkspaceRefreshService::class)->touch('Document:renamed');

        $document->name = $name;
        $document->loadMissing(['task', 'job']);
        if ($document->task) {
            $document->task->activities()->create([
                'user_id' => $user->id,
                'event' => 'task.document_renamed',
                'description' => 'Document renamed from '.$oldName.' to '.$name.'.',
                'meta' => ['document_id' => $document->id, 'old_name' => $oldName, 'name' => $name],
            ]);
        }
        if ($document->job) {
            $document->job->activities()->create([
                'user_id' => $user->id,
                'event' => 'job.document_renamed',
                'description' => 'Document renamed from '.$oldName.' to '.$name.'.',
                'meta' => ['document_id' => $document->id, 'old_name' => $oldName, 'name' => $name, 'task_id' => $document->task_id],
            ]);
        }
    }

    public function storeVersion(Document $document, UploadedFile $file, User $user, string $permissionModule = 'documents'): Document
    {
        $access = app(AccessControlService::class);
        abort_unless($access->can($user, $permissionModule, 'create'), 403);
        $access->applyDocumentScope(Document::query()->whereKey($document->id), $user, $permissionModule)->firstOrFail();

        $document->loadMissing(['task.job', 'job']);
        if ($document->task) {
            if ($permissionModule === 'document_archive') {
                $access->applyDocumentArchiveTaskScope(Task::query()->whereKey($document->task_id), $user)->firstOrFail();
            } else {
                $access->applyTaskScope(Task::query()->whereKey($document->task_id), $user)->firstOrFail();
            }
        } elseif ($document->flow_job_id) {
            if ($permissionModule === 'document_archive') {
                $access->applyDocumentArchiveJobScope(\App\Models\FlowJob::query()->whereKey($document->flow_job_id), $user)->firstOrFail();
            } else {
                app(JobService::class)->findVisible($user, (int) $document->flow_job_id);
            }
        }

        $jobId = $document->flow_job_id ?: 'general';
        $stored = app(SecureDocumentStorage::class)->store($file, 'flowtrack/documents/'.$jobId);
        $path = $stored['path'];

        $version = $this->nextDocumentVersion(
            $document->task,
            $document->flow_job_id,
            $document->task_id,
            (string) $document->category,
            (string) $document->name,
        );

        $created = Document::create([
            'document_number' => $this->nextNumber(),
            'flow_job_id' => $document->flow_job_id,
            'client_id' => $document->client_id,
            'task_id' => $document->task_id,
            'uploaded_by' => $user->id,
            'category' => $document->category,
            'name' => $document->name,
            'note' => $document->note,
            'path' => $path,
            'mime_type' => StoredFileResponse::mimeType($document->name, $stored['mime']),
            'size' => $stored['size'],
            'version' => max(1, $version),
            'is_final' => false,
        ]);

        $this->recordDocumentActivity($created, $user, 'uploaded');
        $this->notifyDocumentChange($created, $user, 'uploaded');

        return $created;
    }

    public function versions(Document $document, User $user, string $permissionModule = 'documents')
    {
        $document->loadMissing('task.setupTemplate');

        $query = app(AccessControlService::class)->applyDocumentScope(Document::query(), $user, $permissionModule)
            ->with('uploader')
            ->where('flow_job_id', $document->flow_job_id)
            ->where('task_id', $document->task_id)
            ->where('category', $document->category);

        // Artwork revisions may be uploaded with completely different original
        // filenames. They still belong to one version history, so show every
        // artwork upload for the same Artwork task. Other document types keep
        // the existing filename-based version grouping.
        $isArtworkTask = $document->task
            && app(OrderWorkflowActionService::class)->automationKey($document->task) === 'ART_PREPARE_UPLOAD';

        if (! $isArtworkTask) {
            $query->where('name', $document->name);
        }

        return $query
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->get();
    }

    private function nextDocumentVersion(?Task $task, mixed $flowJobId, mixed $taskId, string $category, string $name): int
    {
        $query = Document::query()
            ->where('flow_job_id', $flowJobId)
            ->where('task_id', $taskId)
            ->where('category', $category);

        // Artwork revisions are one continuous version history even when the
        // designer uploads a revised file with a different original filename.
        // Other document categories keep the existing filename-based version
        // grouping so this change is isolated to the Artwork upload task.
        $isArtworkTask = $task
            && app(OrderWorkflowActionService::class)->automationKey($task) === 'ART_PREPARE_UPLOAD';

        if (! $isArtworkTask) {
            $query->where('name', $name);
        }

        return max(1, ((int) $query->max('version')) + 1);
    }

    private function recordDocumentActivity(Document $document, User $user, string $action): void
    {
        $document->loadMissing(['task','job']);
        $verb = $action === 'linked' ? 'linked' : 'uploaded';
        if ($document->task) $document->task->activities()->create(['user_id'=>$user->id,'event'=>'task.document_'.$verb,'description'=>'Document '.$verb.': '.$document->name,'meta'=>['document_id'=>$document->id,'name'=>$document->name]]);
        if ($document->job) $document->job->activities()->create(['user_id'=>$user->id,'event'=>'job.document_'.$verb,'description'=>'Document '.$verb.($document->task?' to '.$document->task->title:'').': '.$document->name,'meta'=>['document_id'=>$document->id,'name'=>$document->name,'task_id'=>$document->task_id]]);
    }

    private function notifyDocumentChange(Document $document, User $actor, string $action): void
    {
        $document->loadMissing(['task.job','job']);
        $message = 'Document '.$action.': '.$document->name;
        if ($document->task) {
            app(NotificationService::class)->notifyTaskParticipants(
                $document->task,
                'Document '.$action.' on '.$document->task->title,
                $message,
                'update',
                $actor,
            );
            return;
        }
        if ($document->job) {
            app(NotificationService::class)->notifyJobParticipants(
                $document->job,
                'Job document '.$action,
                $document->job->job_number.' · '.$message,
                'update',
                $actor,
            );
        }
    }

    private function nextNumber(): string
    {
        return 'DOC-'.str_pad((string) ((int) Document::max('id') + 1), 6, '0', STR_PAD_LEFT);
    }
}
