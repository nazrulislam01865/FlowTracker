<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Task;
use App\Models\User;
use App\Support\StoredFileResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function query(User $user, array $filters = [])
    {
        $query = app(AccessControlService::class)->applyDocumentScope(Document::query(), $user)
            ->with(['job.client','job.phase','job.tasks' => fn ($q) => app(AccessControlService::class)->applyTaskScope($q, $user),'task.phase','task.assignee','uploader']);

        return $query
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(fn ($x) => $x
                ->where('name', 'like', "%{$search}%")
                ->orWhere('document_number', 'like', "%{$search}%")
                ->orWhereHas('job', fn ($j) => $j->where('job_number', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%")->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%")))
                ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))
                ->orWhereHas('task', fn ($t) => $t->where('title', 'like', "%{$search}%"))))
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

    public function list(User $user, array $filters = [])
    {
        return $this->query($user, $filters)->latest()->limit(250)->get();
    }

    public function paginate(User $user, array $filters = [], int $perPage = 25)
    {
        return $this->query($user, $filters)->latest()->paginate($perPage);
    }

    public function store(UploadedFile $file, array $data, User $user): Document
    {
        abort_unless(app(AccessControlService::class)->can($user, 'documents', 'create'), 403);

        $task = null;
        if (!empty($data['task_id'])) {
            $task = Task::with(['job','documentCategory','setupTemplate.documentCategory'])->findOrFail((int) $data['task_id']);
            app(AccessControlService::class)->applyTaskScope(Task::query()->whereKey($task->id), $user)->firstOrFail();
            app(JobService::class)->findVisible($user, (int) $task->flow_job_id);
            if (!empty($data['flow_job_id'])) abort_unless((int) $task->flow_job_id === (int) $data['flow_job_id'], 422, 'The selected document task does not belong to this Job.');
            if (($data['require_task_pack_requirement'] ?? false) === true) abort_unless($this->taskHasRequirement($task), 422, 'This task has no Task Pack document requirement.');
        } elseif (!empty($data['flow_job_id'])) {
            app(JobService::class)->findVisible($user, (int) $data['flow_job_id']);
        }

        $disk = (string) config('flowtrack.document_disk', 'public');
        $jobId = $task?->flow_job_id ?: ($data['flow_job_id'] ?? 'general');
        $extension = strtolower(trim((string) $file->getClientOriginalExtension()));
        $storedName = Str::uuid()->toString().($extension !== '' ? '.'.$extension : '');
        $path = $file->storeAs('flowtrack/documents/'.$jobId, $storedName, $disk);
        abort_if(!$path, 500, 'The document could not be stored.');

        $category = $task?->documentCategory?->name
            ?: $task?->setupTemplate?->documentCategory?->name
            ?: ($data['category'] ?? ($task ? 'Task attachment' : 'Other'));

        $versionQuery = Document::where('flow_job_id', $task?->flow_job_id ?: ($data['flow_job_id'] ?? null))
            ->where('task_id', $task?->id ?: ($data['task_id'] ?? null))
            ->where('category', $category)
            ->where('name', $file->getClientOriginalName());
        $version = max(1, ((int) $versionQuery->max('version')) + 1);

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
            'mime_type' => StoredFileResponse::mimeType($file->getClientOriginalName(), $file->getMimeType()),
            'size' => $file->getSize(),
            'version' => $version,
            'is_final' => false,
        ]);

        $this->recordDocumentActivity($document, $user, 'uploaded');
        $this->notifyDocumentChange($document, $user, 'uploaded');
        return $document;
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
        $version = ((int) Document::where('task_id', $task->id)->where('category', $category)->where('name', $source->name)->max('version')) + 1;

        $document = Document::create([
            'document_number' => $this->nextNumber(), 'flow_job_id' => $task->flow_job_id, 'client_id' => $task->job?->client_id,
            'task_id' => $task->id, 'uploaded_by' => $user->id, 'category' => $category, 'name' => $source->name,
            'note' => filled($note) ? trim((string) $note) : null, 'path' => $source->path, 'mime_type' => $source->mime_type, 'size' => $source->size, 'version' => max(1, $version), 'is_final' => (bool) $source->is_final,
        ]);
        $this->recordDocumentActivity($document, $user, 'linked');
        $this->notifyDocumentChange($document, $user, 'linked');
        return $document;
    }

    public function delete(Document $document, ?User $actor = null): void
    {
        if ($actor) {
            abort_unless(app(AccessControlService::class)->can($actor, 'documents', 'delete'), 403);
            app(AccessControlService::class)->applyDocumentScope(Document::query()->whereKey($document->id), $actor)->firstOrFail();
        }
        $document->loadMissing(['task','job']);
        $name = $document->name; $path = $document->path;
        if ($actor) {
            if ($document->task) $document->task->activities()->create(['user_id'=>$actor->id,'event'=>'task.document_deleted','description'=>'Document removed: '.$name,'meta'=>['document_id'=>$document->id,'name'=>$name]]);
            if ($document->job) $document->job->activities()->create(['user_id'=>$actor->id,'event'=>'job.document_deleted','description'=>'Document removed'.($document->task?' from '.$document->task->title:'').': '.$name,'meta'=>['document_id'=>$document->id,'name'=>$name,'task_id'=>$document->task_id]]);
            $this->notifyDocumentChange($document, $actor, 'removed');
        }
        $document->delete();
        if ($path && !Document::where('path', $path)->exists()) Storage::disk((string) config('flowtrack.document_disk', 'public'))->delete($path);
    }

    public function taskHasRequirement(Task $task): bool
    {
        return (bool) ($task->document_category_id ?: $task->setupTemplate?->document_category_id);
    }

    public function versions(Document $document, User $user)
    {
        return app(AccessControlService::class)->applyDocumentScope(Document::query(), $user)
            ->with('uploader')
            ->where('flow_job_id', $document->flow_job_id)
            ->where('task_id', $document->task_id)
            ->where('category', $document->category)
            ->where('name', $document->name)
            ->orderByDesc('version')->get();
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
