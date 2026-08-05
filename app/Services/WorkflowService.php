<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Models\WorkflowTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    public function workspaceId(): int { return app(SetupContext::class)->workspaceId(); }

    public function all()
    {
        return WorkflowTemplate::query()
            ->where('workspace_id', $this->workspaceId())
            ->with(['phases.taskPack','phases.documentCategory'])
            ->orderByDesc('is_default')->orderBy('name')->get();
    }

    public function saveWorkflow(array $data, ?int $id = null): WorkflowTemplate
    {
        $this->assertManage();
        $workspaceId = $this->workspaceId();
        $code = strtoupper(trim($data['code']));
        if (WorkflowTemplate::where('workspace_id', $workspaceId)->where('code', $code)->when($id, fn ($q) => $q->whereKeyNot($id))->exists()) {
            throw ValidationException::withMessages(['workflowCode' => 'This workflow code already exists.']);
        }

        return DB::transaction(function () use ($data, $id, $workspaceId, $code) {
            if ($id) {
                $template = WorkflowTemplate::where('workspace_id', $workspaceId)->findOrFail($id);
                $template->update([
                    'code' => $code,
                    'name' => trim($data['name']),
                    'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
                    'is_active' => (bool) ($data['is_active'] ?? true),
                    'version' => max(1, (int) ($data['version'] ?? 1)),
                ]);
                if (Schema::hasTable('workflows')) {
                    Workflow::updateOrCreate(['id' => $template->id], [
                        'name' => $template->name,
                        'slug' => Str::slug($template->name).'-'.strtolower($template->code),
                        'description' => $template->description,
                        'is_active' => $template->is_active,
                    ]);
                }
                return $template;
            }

            $legacyId = null;
            if (Schema::hasTable('workflows')) {
                $legacy = Workflow::create([
                    'name' => trim($data['name']),
                    'slug' => Str::slug($data['name']).'-'.strtolower($code).'-'.Str::lower(Str::random(4)),
                    'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
                    'is_active' => (bool) ($data['is_active'] ?? true),
                ]);
                $legacyId = $legacy->id;
            }

            return WorkflowTemplate::create([
                'id' => $legacyId,
                'workspace_id' => $workspaceId,
                'code' => $code,
                'name' => trim($data['name']),
                'description' => blank($data['description'] ?? null) ? null : trim($data['description']),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'is_default' => false,
                'version' => max(1, (int) ($data['version'] ?? 1)),
            ]);
        });
    }


    public function copyPhases(WorkflowTemplate $source, WorkflowTemplate $target): void
    {
        $this->assertManage();
        DB::transaction(function () use ($source, $target) {
            foreach ($source->phases()->orderBy('sequence')->get() as $phase) {
                $this->savePhase($target, [
                    'name' => $phase->name,
                    'short_name' => $phase->short_name,
                    'task_pack_id' => $phase->task_pack_id,
                    'document_category_id' => $phase->document_category_id,
                    'allow_job_start' => (bool) $phase->allow_job_start,
                    'is_skippable' => (bool) $phase->is_skippable,
                    'requires_approval' => (bool) $phase->requires_approval,
                    'auto_advance_on_ready' => (bool) $phase->auto_advance_on_ready,
                    'is_active' => (bool) $phase->is_active,
                    'entry_condition' => $phase->entry_condition,
                    'exit_condition' => $phase->exit_condition,
                    'sequence' => (int) $phase->sequence,
                ]);
            }
        });
    }

    public function setDefault(int $id): void
    {
        $this->assertManage();
        DB::transaction(function () use ($id) {
            WorkflowTemplate::where('workspace_id', $this->workspaceId())->update(['is_default' => false]);
            $workflow = WorkflowTemplate::where('workspace_id', $this->workspaceId())->findOrFail($id);
            $workflow->update(['is_default' => true, 'is_active' => true]);
            if (Schema::hasTable('workflows')) Workflow::whereKey($id)->update(['is_active' => true]);
        });
    }

    public function toggleWorkflow(int $id): void
    {
        $this->assertManage();
        $workflow = WorkflowTemplate::where('workspace_id', $this->workspaceId())->findOrFail($id);
        if ($workflow->is_default && $workflow->is_active) {
            throw ValidationException::withMessages(['workflow' => 'The default workflow cannot be deactivated. Set another default first.']);
        }
        $workflow->update(['is_active' => !$workflow->is_active]);
        if (Schema::hasTable('workflows')) Workflow::whereKey($id)->update(['is_active' => $workflow->is_active]);
    }

    public function deleteWorkflow(int $id): void
    {
        $this->assertManage();
        $workflow = WorkflowTemplate::where('workspace_id', $this->workspaceId())->findOrFail($id);
        if ($workflow->is_default) throw ValidationException::withMessages(['workflow' => 'The default workflow cannot be deleted.']);
        if (FlowJob::where('workflow_id', $id)->exists()) throw ValidationException::withMessages(['workflow' => 'Jobs already use this workflow. Deactivate it instead.']);
        DB::transaction(function () use ($workflow) {
            $workflow->phases()->delete();
            if (Schema::hasTable('workflows')) Workflow::whereKey($workflow->id)->delete();
            $workflow->delete();
        });
    }

    public function savePhase(WorkflowTemplate $workflow, array $data, ?WorkflowPhase $phase = null): WorkflowPhase
    {
        $this->assertManage();
        $document = !empty($data['document_category_id']) ? MasterRecord::find($data['document_category_id']) : null;

        // The phase modal does not expose sequence while editing. Preserve the
        // existing position for edits and append new phases to the end. This
        // keeps savePhase() safe for both the UI and service-level callers.
        if (!array_key_exists('sequence', $data) || $data['sequence'] === null || $data['sequence'] === '') {
            $data['sequence'] = $phase
                ? (int) $phase->sequence
                : (((int) $workflow->phases()->max('sequence')) + 1);
        }

        $payload = [
            'workflow_template_id' => $workflow->id,
            'task_pack_id' => $data['task_pack_id'] ?? null,
            'document_category_id' => $data['document_category_id'] ?? null,
            'name' => trim($data['name']),
            'short_name' => trim($data['short_name']),
            'sequence' => (int) $data['sequence'],
            'allow_job_start' => (bool) ($data['allow_job_start'] ?? false),
            'is_skippable' => (bool) ($data['is_skippable'] ?? false),
            'requires_approval' => (bool) ($data['requires_approval'] ?? false),
            'auto_advance_on_ready' => (bool) ($data['auto_advance_on_ready'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'entry_condition' => blank($data['entry_condition'] ?? null) ? null : trim($data['entry_condition']),
            'exit_condition' => blank($data['exit_condition'] ?? null) ? null : trim($data['exit_condition']),
        ];
        if (Schema::hasColumn('workflow_phases', 'workflow_id')) $payload['workflow_id'] = $workflow->id;
        if (Schema::hasColumn('workflow_phases', 'can_skip')) $payload['can_skip'] = (bool) ($data['is_skippable'] ?? false);
        if (Schema::hasColumn('workflow_phases', 'required_document')) $payload['required_document'] = $document?->name;
        if (Schema::hasColumn('workflow_phases', 'entry_rule')) $payload['entry_rule'] = blank($data['entry_condition'] ?? null) ? null : trim($data['entry_condition']);
        if (Schema::hasColumn('workflow_phases', 'exit_rule')) $payload['exit_rule'] = blank($data['exit_condition'] ?? null) ? null : trim($data['exit_condition']);

        return WorkflowPhase::query()->updateOrCreate(['id' => $phase?->id], $payload);
    }

    public function move(WorkflowPhase $phase, int $direction): void
    {
        $this->assertManage();
        DB::transaction(function () use ($phase, $direction) {
            $workflowId = $phase->workflow_template_id ?: $phase->workflow_id;
            $targetSequence = $phase->sequence + $direction;
            if ($targetSequence < 1) return;
            $target = WorkflowPhase::where('workflow_template_id', $workflowId)->where('sequence', $targetSequence)->first();
            if (!$target) return;
            $original = $phase->sequence;
            $phase->update(['sequence' => 9999]);
            $target->update(['sequence' => $original]);
            $phase->update(['sequence' => $targetSequence]);
        });
    }

    public function delete(WorkflowPhase $phase): void
    {
        $this->assertManage();
        if (FlowJob::where('workflow_phase_id', $phase->id)->orWhere('started_from_phase_id', $phase->id)->exists()) {
            throw ValidationException::withMessages(['phase' => 'This phase is already used by Jobs. Deactivate it instead of deleting it.']);
        }
        DB::transaction(function () use ($phase) {
            $workflowId = $phase->workflow_template_id ?: $phase->workflow_id;
            $sequence = $phase->sequence;
            $phase->delete();
            WorkflowPhase::where('workflow_template_id', $workflowId)->where('sequence', '>', $sequence)->orderBy('sequence')->get()->each(fn ($p) => $p->update(['sequence' => $p->sequence - 1]));
        });
    }

    public function syncLegacy(): void
    {
        if (!Schema::hasTable('workflows') || !Schema::hasTable('workflow_templates')) return;
        $workspaceId = $this->workspaceId();
        foreach (Workflow::query()->orderBy('id')->get() as $legacy) {
            WorkflowTemplate::firstOrCreate(['id' => $legacy->id], [
                'workspace_id' => $workspaceId,
                'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $legacy->slug ?: $legacy->name), 0, 20)) ?: 'WF'.$legacy->id,
                'name' => $legacy->name,
                'description' => $legacy->description,
                'is_active' => $legacy->is_active,
                'is_default' => false,
                'version' => 1,
            ]);
        }
        if (!WorkflowTemplate::where('workspace_id', $workspaceId)->where('is_default', true)->exists()) {
            WorkflowTemplate::where('workspace_id', $workspaceId)->where('is_active', true)->orderBy('id')->first()?->update(['is_default' => true]);
        }
        app(MasterDataService::class)->syncLegacy();
        if (Schema::hasColumn('workflow_phases', 'workflow_id')) {
            foreach (WorkflowPhase::query()->whereNotNull('workflow_id')->get() as $phase) {
                $changes = [];
                if (!$phase->workflow_template_id) $changes['workflow_template_id'] = $phase->workflow_id;
                if (Schema::hasColumn('workflow_phases','can_skip')) $changes['is_skippable'] = (bool) $phase->can_skip;
                if (Schema::hasColumn('workflow_phases','entry_rule') && blank($phase->entry_condition)) $changes['entry_condition'] = $phase->entry_rule;
                if (Schema::hasColumn('workflow_phases','exit_rule') && blank($phase->exit_condition)) $changes['exit_condition'] = $phase->exit_rule;
                if (Schema::hasColumn('workflow_phases','required_document') && !$phase->document_category_id && filled($phase->required_document)) {
                    $changes['document_category_id'] = MasterRecord::where('workspace_id',$workspaceId)->where('type','document_category')->where('name',$phase->required_document)->value('id');
                }
                if ($changes) $phase->update($changes);
            }
        }
    }
    private function assertManage(): void
    {
        $user = auth()->user();
        abort_unless($user && app(AccessControlService::class)->can($user, 'workflow', 'manage'), 403);
    }

}
