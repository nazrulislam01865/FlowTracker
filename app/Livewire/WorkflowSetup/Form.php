<?php

namespace App\Livewire\WorkflowSetup;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Livewire\Concerns\RefreshesFromWorkspace;
use App\Models\Client;
use App\Models\WorkflowTemplate;
use App\Services\FilterOptionService;
use App\Services\WorkflowService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    use RefreshesFromWorkspace;
    use UsesPagePlaceholder;

    public ?int $workflowId = null;
    public ?int $sourceWorkflowId = null;
    public string $workflowName = '';
    public string $workflowCode = '';
    public string $workflowDescription = '';
    public int $workflowVersion = 1;
    public bool $workflowActive = true;

    public string $workflowAppliesTo = 'orders';
    public string $clientAvailability = 'all';
    public array $selectedClientIds = [];

    public function mount(?int $workflowId = null, ?int $sourceWorkflowId = null): void
    {
        $service = app(WorkflowService::class);
        $this->workflowId = $workflowId;
        $this->sourceWorkflowId = $sourceWorkflowId;

        if ($workflowId) {
            $workflow = WorkflowTemplate::query()
                ->where('workspace_id', $service->workspaceId())
                ->with('clients:id,name')
                ->findOrFail($workflowId);

            $this->workflowName = (string) $workflow->name;
            $this->workflowCode = (string) $workflow->code;
            $this->workflowDescription = (string) $workflow->description;
            $this->workflowVersion = (int) $workflow->version;
            $this->workflowActive = (bool) $workflow->is_active;
            $this->workflowAppliesTo = in_array($workflow->applies_to, ['inquiries', 'orders'], true)
                ? (string) $workflow->applies_to
                : 'orders';
            $this->clientAvailability = $workflow->client_availability === 'specific' ? 'specific' : 'all';
            $this->selectedClientIds = $workflow->clients->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
            $this->sourceWorkflowId = null;
        } elseif ($sourceWorkflowId) {
            WorkflowTemplate::query()
                ->where('workspace_id', $service->workspaceId())
                ->findOrFail($sourceWorkflowId);
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'workflowName' => ['required', 'string', 'max:255'],
            'workflowCode' => ['required', 'string', 'max:40'],
            'workflowDescription' => ['nullable', 'string', 'max:5000'],
            'workflowAppliesTo' => ['required', Rule::in(['inquiries', 'orders'])],
            'clientAvailability' => ['required', Rule::in(['all', 'specific'])],
            'selectedClientIds' => ['required_if:clientAvailability,specific', 'array', 'min:1'],
            'selectedClientIds.*' => ['integer', 'distinct', 'exists:clients,id'],
            'sourceWorkflowId' => ['nullable', 'integer', 'exists:workflow_templates,id'],
        ]);

        $clientIds = collect($data['selectedClientIds'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($data['clientAvailability'] === 'specific') {
            $activeClientCount = Client::query()
                ->where('is_active', true)
                ->whereIn('id', $clientIds)
                ->count();

            if ($activeClientCount !== $clientIds->count()) {
                $this->addError('selectedClientIds', 'Please select active clients only.');
                return;
            }
        }

        $service = app(WorkflowService::class);
        $workflow = $service->saveWorkflow([
            'code' => $data['workflowCode'],
            'name' => $data['workflowName'],
            'description' => $data['workflowDescription'],
            'is_active' => $this->workflowActive,
            'version' => $this->workflowVersion,
            'applies_to' => $data['workflowAppliesTo'],
            'client_availability' => $data['clientAvailability'],
            'client_ids' => $data['clientAvailability'] === 'specific' ? $clientIds->all() : [],
        ], $this->workflowId);

        if (!$this->workflowId && $data['sourceWorkflowId']) {
            $source = WorkflowTemplate::query()
                ->where('workspace_id', $service->workspaceId())
                ->findOrFail((int) $data['sourceWorkflowId']);
            $service->copyPhases($source, $workflow);
        }

        session()->flash('success', $this->workflowId ? 'Workflow updated.' : 'Workflow created.');
        app(\App\Services\NotificationService::class)->notifyUser(
            auth()->user(),
            $this->workflowId ? 'Workflow updated' : 'Workflow created',
            $workflow->name.' was saved.',
            'update',
            null,
            null,
            auth()->user(),
        );
        $this->redirectRoute('workflow.setup', ['workflow' => $workflow->id], navigate: true);
    }

    public function cancel(): void
    {
        $this->redirectRoute('workflow.setup', navigate: true);
    }

    public function render()
    {
        $clientOptions = collect();

        if ($this->clientAvailability === 'specific') {
            $actor = auth()->user();
            abort_unless($actor, 401);

            $page = app(FilterOptionService::class)->searchPage(
                user: $actor,
                type: 'clients',
                context: 'workflow-setup',
                page: 1,
                perPage: FilterOptionService::COMPACT_PER_PAGE,
                selectedIds: $this->selectedClientIds,
            );

            $clientOptions = $page->selectedItems
                ->concat($page->items)
                ->unique(fn (array $item) => (string) ($item['id'] ?? ''))
                ->values();
        }

        return view('livewire.workflow-setup.form', [
            'workflows' => app(WorkflowService::class)->all()->when($this->workflowId, fn ($rows) => $rows->where('id', '!=', $this->workflowId)),
            'clientOptions' => $clientOptions,
        ]);
    }
}
