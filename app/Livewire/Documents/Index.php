<?php

namespace App\Livewire\Documents;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\Document;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Services\AccessControlService;
use App\Services\ClientService;
use App\Services\DocumentService;
use App\Services\JobService;
use App\Services\MasterDataService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use UsesPagePlaceholder;
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $category = '';
    public string $client = '';
    public string $job = '';
    public string $phase = '';
    public string $status = '';
    public int $perPage = 25;
    public array $expandedJobs = [];
    public ?int $selectedDocumentId = null;

    public bool $showUpload = false;
    public array $documentUploads = [];
    public ?int $uploadJobId = null;
    public ?int $uploadTaskId = null;
    public string $uploadCategory = '';

    public function mount(): void
    {
        $this->job = request()->integer('job') ? (string) request()->integer('job') : '';
        $this->uploadJobId = request()->integer('job') ?: null;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search','category','client','job','phase','status','perPage'], true)) $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search','category','client','job','phase','status']);
        $this->resetPage();
    }

    public function clearFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['search','category','client','job','phase','status'], true), 422);
        $this->{$filter} = '';
        $this->resetPage();
    }

    public function toggleJob(int $jobId): void
    {
        $this->expandedJobs = in_array($jobId, $this->expandedJobs, true)
            ? array_values(array_diff($this->expandedJobs, [$jobId]))
            : array_values(array_unique([...$this->expandedJobs, $jobId]));
    }

    public function expandAll(): void
    {
        $this->expandedJobs = app(DocumentService::class)->query(auth()->user(), $this->filters())->whereNotNull('flow_job_id')->distinct()->pluck('flow_job_id')->map(fn ($id)=>(int)$id)->all();
    }

    public function collapseAll(): void { $this->expandedJobs = []; }

    public function selectDocument(int $id): void
    {
        $doc = app(AccessControlService::class)->applyDocumentScope(Document::query(), auth()->user())->findOrFail($id);
        $this->selectedDocumentId = $doc->id;
        if ($doc->flow_job_id && !in_array((int)$doc->flow_job_id, $this->expandedJobs, true)) $this->expandedJobs[] = (int)$doc->flow_job_id;
    }

    public function openUpload(): void
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        if ($this->uploadCategory === '') $this->uploadCategory = app(MasterDataService::class)->active('document_category')->first()?->name ?: 'Other';
        $this->showUpload = true;
    }

    public function closeUpload(): void
    {
        $this->showUpload = false;
        $this->documentUploads = [];
        $this->uploadTaskId = null;
    }

    public function updatedUploadJobId(): void { $this->uploadTaskId = null; }

    public function storeDocuments(): void
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        $this->validate([
            'documentUploads' => ['required','array','min:1'],
            'documentUploads.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
            'uploadJobId' => ['required','integer'],
            'uploadTaskId' => ['nullable','integer'],
            'uploadCategory' => ['required','string','max:100'],
        ]);

        $job = app(JobService::class)->findVisible(auth()->user(), (int)$this->uploadJobId);
        $task = $this->uploadTaskId ? app(\App\Services\TaskService::class)->visibleQuery(auth()->user())->where('flow_job_id',$job->id)->findOrFail($this->uploadTaskId) : null;

        foreach ($this->documentUploads as $file) {
            $doc = app(DocumentService::class)->store($file, [
                'flow_job_id' => $job->id,
                'client_id' => $job->client_id,
                'task_id' => $task?->id,
                'category' => $this->uploadCategory,
            ], auth()->user());
            $this->selectedDocumentId = $doc->id;
        }
        $this->expandedJobs[] = $job->id;
        $this->expandedJobs = array_values(array_unique($this->expandedJobs));
        $this->closeUpload();
        session()->flash('success', 'Document(s) uploaded successfully.');
    }

    public function deleteDocument(int $id): void
    {
        $doc = app(AccessControlService::class)->applyDocumentScope(Document::query(), auth()->user())->findOrFail($id);
        app(DocumentService::class)->delete($doc, auth()->user());
        if ($this->selectedDocumentId === $id) $this->selectedDocumentId = null;
    }

    private function filters(): array
    {
        return ['search'=>$this->search,'category'=>$this->category,'client'=>$this->client,'job'=>$this->job,'phase'=>$this->phase,'status'=>$this->status];
    }

    public function render()
    {
        return view('livewire.documents.index', $this->documentsPageData());
    }

    private function documentsPageData(): array
    {
        $user = auth()->user();
        $service = app(DocumentService::class);
        $access = app(AccessControlService::class);
        $documents = $service->paginate($user, $this->filters(), $this->perPage);
        $pageCollection = $documents->getCollection();
        $grouped = $pageCollection->groupBy(fn ($d) => $d->flow_job_id ?: 0);
        if (!$this->expandedJobs && $grouped->keys()->first()) $this->expandedJobs = [(int)$grouped->keys()->first()];

        $base = $service->query($user);
        $metrics = [
            'all' => (clone $base)->count(),
            'attention' => (clone $base)->where('is_final',false)->whereHas('task',fn($t)=>$t->where('needs_attention',true))->count(),
            'approval' => (clone $base)->where('is_final',false)->whereHas('task',fn($t)=>$t->whereIn('status',['In Review','Waiting for Internal Approval']))->count(),
            'recent' => (clone $base)->where('updated_at','>=',now()->subDays(7))->count(),
        ];

        $categories = $this->showUpload ? app(MasterDataService::class)->active('document_category') : collect();

        $selected = $this->selectedDocumentId
            ? $access->applyDocumentScope(Document::query()->with(['job.client','task.phase','task.assignee','uploader']),$user)->find($this->selectedDocumentId)
            : null;
        if (!$selected) $selected = $pageCollection->first();

        $optionService = app(\App\Services\FilterOptionService::class);
        $jobs = $this->showUpload
            ? app(JobService::class)->visibleQuery($user)->with('client:id,name')->orderByDesc('id')->limit(250)->get(['id','job_number','title','client_id'])
            : collect();
        $uploadTasks = $this->showUpload && $this->uploadJobId
            ? app(\App\Services\TaskService::class)->visibleQuery($user)->where('flow_job_id',$this->uploadJobId)->with('phase')->orderBy('id')->get()
            : collect();

        return [
            'documents' => $documents,
            'grouped' => $grouped,
            'metrics' => $metrics,
            'jobFilterOptions' => $optionService->options($user, 'jobs', 'documents', '', $this->job !== '' ? (int)$this->job : null, 5),
            'clientFilterOptions' => $optionService->options($user, 'clients', 'documents', '', $this->client !== '' ? (int)$this->client : null, 5),
            'phaseFilterOptions' => $optionService->options($user, 'phases', 'documents', '', $this->phase !== '' ? (int)$this->phase : null, 5),
            'categoryFilterOptions' => $optionService->options($user, 'document-categories', 'documents', '', $this->category, 5),
            'jobs' => $jobs,
            'categories' => $categories,
            'uploadTasks' => $uploadTasks,
            'selected' => $selected,
            'versions' => $selected ? $service->versions($selected, $user) : collect(),
        ];
    }
}
