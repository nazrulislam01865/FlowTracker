<?php

namespace App\Livewire\WorkflowSetup;

use App\Models\WorkflowTemplate;
use App\Services\WorkflowService;
use Livewire\Component;

class Form extends Component
{
    public ?int $workflowId = null;
    public ?int $sourceWorkflowId = null;
    public string $workflowName = '';
    public string $workflowCode = '';
    public string $workflowDescription = '';
    public int $workflowVersion = 1;
    public bool $workflowActive = true;

    public function mount(?int $workflowId = null, ?int $sourceWorkflowId = null): void
    {
        $service = app(WorkflowService::class);
        $service->syncLegacy();
        $this->workflowId = $workflowId;
        $this->sourceWorkflowId = $sourceWorkflowId;

        if ($workflowId) {
            $workflow = WorkflowTemplate::query()
                ->where('workspace_id', $service->workspaceId())
                ->findOrFail($workflowId);
            $this->workflowName = (string) $workflow->name;
            $this->workflowCode = (string) $workflow->code;
            $this->workflowDescription = (string) $workflow->description;
            $this->workflowVersion = (int) $workflow->version;
            $this->workflowActive = (bool) $workflow->is_active;
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
            'workflowName' => ['required','string','max:255'],
            'workflowCode' => ['required','string','max:40'],
            'workflowDescription' => ['nullable','string','max:5000'],
            'sourceWorkflowId' => ['nullable','integer','exists:workflow_templates,id'],
        ]);

        $service = app(WorkflowService::class);
        $workflow = $service->saveWorkflow([
            'code' => $data['workflowCode'],
            'name' => $data['workflowName'],
            'description' => $data['workflowDescription'],
            'is_active' => $this->workflowActive,
            'version' => $this->workflowVersion,
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
        return view('livewire.workflow-setup.form', [
            'workflows' => app(WorkflowService::class)->all()->when($this->workflowId, fn ($rows) => $rows->where('id', '!=', $this->workflowId)),
        ]);
    }
}
