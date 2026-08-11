<?php

namespace App\Livewire\WorkflowSetup;

use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\TaskPack;
use App\Models\WorkflowPhase;
use App\Models\WorkflowTemplate;
use App\Services\MasterDataService;
use App\Services\TaskPackService;
use App\Services\WorkflowService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Index extends Component
{
    use UsesPagePlaceholder;
    public ?int $selectedWorkflowId = null;
    public bool $showWorkflowModal = false;
    public ?int $editWorkflowId = null;
    public string $workflowCode = '';
    public string $workflowName = '';
    public string $workflowDescription = '';
    public bool $workflowActive = true;
    public int $workflowVersion = 1;
    public bool $showWorkflowDeleteModal = false;
    public ?int $deleteWorkflowId = null;
    public array $workflowDeleteImpact = [];

    public ?int $editPhaseId = null;
    public bool $showPhaseModal = false;
    public string $phaseName = '';
    public string $shortName = '';
    public ?int $taskPackId = null;
    public ?int $documentCategoryId = null;
    public bool $allowJobStart = false;
    public bool $isSkippable = false;
    public bool $requiresApproval = false;
    public bool $autoAdvanceOnReady = false;
    public bool $phaseActive = true;
    public string $entryCondition = '';
    public string $exitCondition = '';

    public function mount(): void
    {
        $all = app(WorkflowService::class)->all();
        $requested = request()->integer('workflow');
        $this->selectedWorkflowId = $requested && $all->contains('id', $requested)
            ? $requested
            : ($all->firstWhere('is_default', true)?->id ?? $all->first()?->id);
    }

    public function selectWorkflow(int $id): void { $this->selectedWorkflowId = $id; $this->resetValidation(); }

    public function openWorkflow(?int $id = null): void
    {
        abort_unless(auth()->user()?->canModule('workflow', $id ? 'edit' : 'create'), 403);
        $this->showWorkflowModal = true; $this->editWorkflowId = $id; $this->resetValidation();
        if ($id) {
            $w=WorkflowTemplate::findOrFail($id);
            $this->workflowCode=$w->code; $this->workflowName=$w->name; $this->workflowDescription=(string)$w->description; $this->workflowActive=(bool)$w->is_active; $this->workflowVersion=(int)$w->version;
        } else {
            $this->reset(['workflowCode','workflowName','workflowDescription']); $this->workflowActive=true; $this->workflowVersion=1;
        }
    }

    public function closeWorkflow(): void { $this->showWorkflowModal=false; $this->resetValidation(); }

    public function saveWorkflow(): void
    {
        $data=$this->validate([
            'workflowCode'=>['required','string','max:40'], 'workflowName'=>['required','string','max:255'], 'workflowDescription'=>['nullable','string','max:5000'], 'workflowActive'=>['boolean'], 'workflowVersion'=>['required','integer','min:1','max:9999'],
        ]);
        $workflow=app(WorkflowService::class)->saveWorkflow(['code'=>$data['workflowCode'],'name'=>$data['workflowName'],'description'=>$data['workflowDescription'],'is_active'=>$data['workflowActive'],'version'=>$data['workflowVersion']],$this->editWorkflowId);
        $this->selectedWorkflowId=$workflow->id; $this->showWorkflowModal=false; session()->flash('success','Workflow saved.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Workflow updated', $workflow->name.' was saved.', 'update', null, null, auth()->user());
    }

    public function setDefault(int $id): void { $workflow=WorkflowTemplate::findOrFail($id); app(WorkflowService::class)->setDefault($id); session()->flash('success','Default workflow updated.'); app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Default workflow updated', $workflow->name.' is now the default workflow.', 'update', null, null, auth()->user()); }
    public function toggleWorkflow(int $id): void
    {
        try { $workflow=WorkflowTemplate::findOrFail($id); app(WorkflowService::class)->toggleWorkflow($id); session()->flash('success','Workflow status updated.'); app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Workflow status updated', $workflow->name.' status was changed.', 'update', null, null, auth()->user()); }
        catch(ValidationException $e){ $this->addError('workflow',collect($e->errors())->flatten()->first()); }
    }
    public function requestDeleteWorkflow(int $id): void
    {
        $this->resetValidation('workflow');

        try {
            $this->workflowDeleteImpact = app(WorkflowService::class)->workflowDeleteImpact($id);
            $this->deleteWorkflowId = $id;
            $this->showWorkflowDeleteModal = true;
        } catch (ValidationException $e) {
            $this->addError('workflow', collect($e->errors())->flatten()->first());
        } catch (\Throwable $e) {
            report($e);
            $this->addError('workflow', 'FlowTrack could not check this Workflow safely. Please refresh the page and try again.');
        }
    }

    public function closeWorkflowDelete(): void
    {
        $this->showWorkflowDeleteModal = false;
        $this->deleteWorkflowId = null;
        $this->workflowDeleteImpact = [];
    }

    public function confirmDeleteWorkflow(): void
    {
        if (!$this->deleteWorkflowId) {
            $this->closeWorkflowDelete();
            $this->addError('workflow', 'The Workflow selected for deletion is no longer available. Please try again.');
            return;
        }

        try {
            $result = app(WorkflowService::class)->deleteWorkflow($this->deleteWorkflowId);
            $this->closeWorkflowDelete();
            $this->selectedWorkflowId = WorkflowTemplate::query()
                ->where('workspace_id', app(WorkflowService::class)->workspaceId())
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->value('id');

            $message = $result['workflow_name'].' permanently deleted. Existing Jobs and Tasks were preserved in their own snapshots.';
            if ($result['job_count'] > 0) {
                $message .= ' '.$result['job_count'].' older Job'.($result['job_count'] === 1 ? '' : 's').' were detached from the setup Workflow before deletion.';
            }

            session()->flash('success', $message);

            try {
                app(\App\Services\NotificationService::class)->notifyUser(
                    auth()->user(),
                    'Workflow permanently deleted',
                    $message,
                    'update',
                    null,
                    null,
                    auth()->user(),
                );
            } catch (\Throwable $notificationError) {
                report($notificationError);
            }
        } catch (ValidationException $e) {
            $this->closeWorkflowDelete();
            $this->addError('workflow', collect($e->errors())->flatten()->first());
        } catch (\Throwable $e) {
            $this->closeWorkflowDelete();
            report($e);
            $this->addError(
                'workflow',
                'This Workflow could not be deleted right now. Existing Jobs and Tasks were not intentionally removed. Please refresh and try again.'
            );
        }
    }

    public function move(int $id,int $direction):void { app(WorkflowService::class)->move(WorkflowPhase::findOrFail($id),$direction); }

    public function openPhase(?int $id=null):void
    {
        abort_unless(auth()->user()?->canModule('workflow', 'edit'), 403);
        abort_unless($this->selectedWorkflowId,422);
        $this->resetValidation(); $this->showPhaseModal=true; $this->editPhaseId=$id;
        if($id){
            $p=WorkflowPhase::where('workflow_template_id',$this->selectedWorkflowId)->findOrFail($id);
            $this->phaseName=$p->name; $this->shortName=$p->short_name; $this->taskPackId=$p->task_pack_id; $this->documentCategoryId=$p->document_category_id;
            $this->allowJobStart=(bool)$p->allow_job_start; $this->isSkippable=(bool)$p->is_skippable; $this->requiresApproval=(bool)$p->requires_approval; $this->autoAdvanceOnReady=(bool)$p->auto_advance_on_ready; $this->phaseActive=(bool)$p->is_active;
            $this->entryCondition=(string)$p->entry_condition; $this->exitCondition=(string)$p->exit_condition;
        }else{
            $this->reset(['phaseName','shortName','taskPackId','documentCategoryId']);
            $this->entryCondition='Previous phase complete'; $this->exitCondition='Required work complete';
            $this->allowJobStart=true; $this->isSkippable=false; $this->requiresApproval=false; $this->autoAdvanceOnReady=false; $this->phaseActive=true;
        }
    }

    public function closePhase():void { $this->showPhaseModal=false; $this->resetValidation(); }

    public function savePhase():void
    {
        $data=$this->validate([
            'phaseName'=>['required','string','max:255'], 'shortName'=>['required','string','max:50'], 'taskPackId'=>['nullable','exists:task_packs,id'], 'documentCategoryId'=>['nullable','exists:master_records,id'],
            'entryCondition'=>['nullable','string','max:255'], 'exitCondition'=>['nullable','string','max:255'], 'allowJobStart'=>['boolean'], 'isSkippable'=>['boolean'], 'requiresApproval'=>['boolean'], 'autoAdvanceOnReady'=>['boolean'], 'phaseActive'=>['boolean'],
        ]);
        $workflow=WorkflowTemplate::findOrFail($this->selectedWorkflowId);
        app(WorkflowService::class)->savePhase($workflow,[
            'name'=>$data['phaseName'],'short_name'=>$data['shortName'],'task_pack_id'=>$data['taskPackId'],'document_category_id'=>$data['documentCategoryId'],
            'allow_job_start'=>$data['allowJobStart'],'is_skippable'=>$data['isSkippable'],'requires_approval'=>$data['requiresApproval'],'auto_advance_on_ready'=>$data['autoAdvanceOnReady'],'is_active'=>$data['phaseActive'],
            'entry_condition'=>$data['entryCondition'],'exit_condition'=>$data['exitCondition'],
        ],$this->editPhaseId?WorkflowPhase::find($this->editPhaseId):null);
        $this->showPhaseModal=false; session()->flash('success','Workflow phase saved.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Workflow phase updated', $data['phaseName'].' was saved.', 'update', null, null, auth()->user());
    }

    public function deletePhase(int $id):void
    {
        try { app(WorkflowService::class)->delete(WorkflowPhase::findOrFail($id)); session()->flash('success','Workflow phase deleted.'); app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Workflow phase deleted', 'A workflow phase was deleted.', 'update', null, null, auth()->user()); }
        catch(ValidationException $e){ $this->addError('phase',collect($e->errors())->flatten()->first()); }
    }

    public function render()
    {
        if ($this->showWorkflowDeleteModal) {
            return view('livewire.workflow-setup.index', $this->emptyPageData());
        }

        return view('livewire.workflow-setup.index', $this->workflowPageData());
    }

    private function workflowPageData(): array
    {
        $all = app(WorkflowService::class)->all();
        if (!$this->selectedWorkflowId && $all->isNotEmpty()) {
            $this->selectedWorkflowId = $all->first()?->id;
        }

        $selected = $all->firstWhere('id', $this->selectedWorkflowId);
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $selectedPhases = $selected?->phases ?? collect();

        return [
            'workflows' => $all,
            'selected' => $selected,
            'activeTemplates' => $all->where('is_active', true)->count(),
            'selectedPhaseCount' => $selectedPhases->count(),
            'allowedStartingStages' => $selectedPhases->where('is_active', true)->where('allow_job_start', true)->count(),
            'automaticTransitions' => $selectedPhases->where('is_active', true)->where('auto_advance_on_ready', true)->count(),
            'taskPacks' => $this->showPhaseModal
                ? TaskPack::query()->where('workspace_id', $workspaceId)->where('is_snapshot', false)->where('is_active', true)->orderBy('name')->get(['id', 'name'])
                : collect(),
            'documentCategories' => $this->showPhaseModal
                ? app(MasterDataService::class)->active('document_category')
                : collect(),
        ];
    }

    private function emptyPageData(): array
    {
        return [
            'workflows' => collect(),
            'selected' => null,
            'activeTemplates' => 0,
            'selectedPhaseCount' => 0,
            'allowedStartingStages' => 0,
            'automaticTransitions' => 0,
            'taskPacks' => collect(),
            'documentCategories' => collect(),
        ];
    }
}
