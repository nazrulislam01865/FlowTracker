<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\WorkflowPhase;
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
    public array $files = [];
    public ?int $uploadJobId = null;
    public ?int $uploadTaskId = null;
    public string $uploadCategory = '';

    public function mount(): void
    {
        $this->job = request()->integer('job') ? (string) request()->integer('job') : '';
        $this->uploadJobId = request()->integer('job') ?: null;
        $this->uploadCategory = app(MasterDataService::class)->active('document_category')->first()?->name ?: 'Other';
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
        $this->showUpload = true;
    }

    public function closeUpload(): void
    {
        $this->showUpload = false;
        $this->files = [];
        $this->uploadTaskId = null;
    }

    public function updatedUploadJobId(): void { $this->uploadTaskId = null; }

    public function upload(): void
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        $this->validate([
            'files' => ['required','array','min:1'],
            'files.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
            'uploadJobId' => ['required','integer'],
            'uploadTaskId' => ['nullable','integer'],
            'uploadCategory' => ['required','string','max:100'],
        ]);

        $job = app(JobService::class)->findVisible(auth()->user(), (int)$this->uploadJobId);
        $task = $this->uploadTaskId ? app(\App\Services\TaskService::class)->visibleQuery(auth()->user())->where('flow_job_id',$job->id)->findOrFail($this->uploadTaskId) : null;

        foreach ($this->files as $file) {
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
        $service = app(DocumentService::class);
        $access = app(AccessControlService::class);
        $documents = $service->paginate(auth()->user(), $this->filters(), $this->perPage);
        $pageCollection = $documents->getCollection();
        $grouped = $pageCollection->groupBy(fn ($d) => $d->flow_job_id ?: 0);
        if (!$this->expandedJobs && $grouped->keys()->first()) $this->expandedJobs = [(int)$grouped->keys()->first()];

        $base = $service->query(auth()->user());
        $metrics = [
            'all' => (clone $base)->count(),
            'attention' => (clone $base)->where('is_final',false)->whereHas('task',fn($t)=>$t->where('needs_attention',true))->count(),
            'approval' => (clone $base)->where('is_final',false)->whereHas('task',fn($t)=>$t->whereIn('status',['In Review','Waiting for Internal Approval']))->count(),
            'recent' => (clone $base)->where('updated_at','>=',now()->subDays(7))->count(),
        ];

        $jobs = app(JobService::class)->visibleQuery(auth()->user())->with('client')->orderByDesc('id')->limit(250)->get();
        $clients = app(ClientService::class)->visibleQuery(auth()->user())->where('is_active',true)->orderBy('name')->get(['id','name']);
        $phases = WorkflowPhase::whereIn('workflow_id',$jobs->pluck('workflow_id')->filter()->unique())->where('is_active',true)->orderBy('sequence')->get();
        $categories = app(MasterDataService::class)->active('document_category');
        $uploadTasks = $this->uploadJobId
            ? app(\App\Services\TaskService::class)->visibleQuery(auth()->user())->where('flow_job_id',$this->uploadJobId)->with('phase')->orderBy('id')->get()
            : collect();

        $selected = $this->selectedDocumentId ? $access->applyDocumentScope(Document::query()->with(['job.client','task.phase','task.assignee','uploader']),auth()->user())->find($this->selectedDocumentId) : null;
        if (!$selected) $selected = $pageCollection->first();
        $versions = $selected ? $service->versions($selected, auth()->user()) : collect();

        return view('livewire.documents.index', compact('documents','grouped','metrics','jobs','clients','phases','categories','uploadTasks','selected','versions'));
    }
}
