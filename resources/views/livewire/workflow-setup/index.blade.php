@php
    $canCreateWorkflow = auth()->user()->canModule('workflow', 'create');
    $canEditWorkflow = auth()->user()->canModule('workflow', 'edit');
    $canDeleteWorkflow = auth()->user()->canModule('workflow', 'delete');
@endphp
<div class="ft-admin-reference ft-workflow-reference">
    @if(!$showWorkflowDeleteModal)
    <div class="ft-admin-page-head">
        <div>
            <h1>Workflow Setup</h1>
            <p>Define phase sequence, Task Packs and the rules that control each stage</p>
        </div>
        <div class="ft-admin-head-actions">
            @if($canCreateWorkflow && $selected)
                <a href="{{ route('workflow.create', ['source' => $selected->id]) }}" wire:navigate class="ft-admin-outline">Duplicate</a>
            @elseif($canCreateWorkflow)
                <span class="ft-admin-outline is-disabled">Duplicate</span>
            @endif
            @if($canCreateWorkflow)<a href="{{ route('workflow.create') }}" wire:navigate class="ft-admin-primary">＋ New Workflow</a>@endif
        </div>
    </div>

    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @error('workflow')<div class="flash error">{{ $message }}</div>@enderror
    @error('phase')<div class="flash error">{{ $message }}</div>@enderror

    <div class="ft-admin-stats">
        <div><span>Active Templates</span><b>{{ $activeTemplates }}</b></div>
        <div><span>Phases in Selected Workflow</span><b>{{ $selectedPhaseCount }}</b></div>
        <div><span>Allowed Starting Stages</span><b>{{ $allowedStartingStages }}</b></div>
        <div><span>Automatic Transitions</span><b>{{ $automaticTransitions }}</b></div>
    </div>

    <div class="ft-workflow-admin-layout">
        <aside class="ft-workflow-template-list">
            <div class="ft-workflow-list-label">WORKFLOW TEMPLATES</div>
            @forelse($workflows as $workflow)
                <button type="button" class="{{ $workflow->id === $selectedWorkflowId ? 'active' : '' }}" wire:click="selectWorkflow({{ $workflow->id }})">
                    <b>{{ $workflow->name }}</b>
                    <span>{{ $workflow->phases->count() }} phases · {{ $workflow->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
            @empty
                <div class="ft-workflow-list-empty">No workflows configured.</div>
            @endforelse
        </aside>

        <section class="ft-workflow-editor-card">
            @if($selected)
                <div class="ft-workflow-editor-head">
                    <div>
                        <h2>{{ $selected->name }}</h2>
                        <p>{{ $selected->description ?: 'No description' }}</p>
                    </div>
                    <div class="ft-workflow-editor-actions">
                        @if($canEditWorkflow)<a href="{{ route('workflow.edit', $selected->id) }}" wire:navigate class="ft-admin-outline">Edit Details</a>@endif
                        @if($canDeleteWorkflow)<button type="button" class="ft-admin-danger" wire:click="requestDeleteWorkflow({{ $selected->id }})" wire:loading.attr="disabled" wire:target="requestDeleteWorkflow">Delete Workflow</button>@endif
                        @if($canEditWorkflow)<button type="button" class="ft-admin-primary" wire:click="openPhase">＋ Add Phase</button>@endif
                        @if(!$canEditWorkflow && !$canDeleteWorkflow)<span class="small muted">View only</span>@endif
                    </div>
                </div>

                <div class="ft-workflow-rule-note">
                    <b>Automatic phase controls</b>
                    <p>Active phases automatically use the standard Job-start, skip and auto-move settings. Task Pack requirements remain the gate for phase completion.</p>
                </div>

                <div class="ft-workflow-table-wrap">
                    <table class="ft-workflow-config-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Phase</th>
                                <th>Task Pack</th>
                                <th>Entry / Exit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selected->phases as $phase)
                                <tr wire:key="workflow-phase-row-{{ $phase->id }}">
                                    <td>
                                        @if($canEditWorkflow)
                                            <div class="ft-sequence-buttons">
                                                <button type="button" wire:click="move({{ $phase->id }}, -1)" @disabled($loop->first)>↑</button>
                                                <button type="button" wire:click="move({{ $phase->id }}, 1)" @disabled($loop->last)>↓</button>
                                            </div>
                                        @else
                                            <span>{{ $phase->sequence }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{ $phase->name }}</b>
                                        <span>Stage {{ $phase->sequence }}</span>
                                    </td>
                                    <td>{{ $phase->taskPack?->name ?? 'No Task Pack' }}</td>
                                    <td class="ft-entry-exit"><div><b>In:</b> {{ $phase->entry_condition ?: '—' }}</div><div><b>Out:</b> {{ $phase->exit_condition ?: '—' }}</div></td>
                                    <td><span class="ft-auto-pill {{ $phase->is_active ? 'automatic' : '' }}">{{ $phase->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td>
                                        <div class="ft-row-action-buttons">
                                            @if($canEditWorkflow)<button type="button" wire:click="openPhase({{ $phase->id }})">Edit</button>@endif
                                            @if($canDeleteWorkflow)<button type="button" wire:click="deletePhase({{ $phase->id }})" wire:confirm="Remove this workflow phase?">Remove</button>@endif
                                            @if(!$canEditWorkflow && !$canDeleteWorkflow)<span class="small muted">View only</span>@endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="ft-workflow-empty-row">No phases configured. Add the first phase to this workflow.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="ft-admin-empty-wide">Create a Workflow to begin.</div>
            @endif
        </section>
    </div>


    @endif

    @if($showWorkflowDeleteModal)
        <div class="ft-reference-overlay" wire:click.self="closeWorkflowDelete"></div>
        <div class="ft-phase-reference-modal" role="alertdialog" aria-modal="true" aria-label="Delete Workflow permanently" style="width:min(720px,calc(100vw - 32px))">
            <div class="ft-phase-modal-head">
                <h2>Delete Workflow permanently?</h2>
                <button type="button" wire:click="closeWorkflowDelete">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="flash error" style="margin:0">
                    This permanently deletes this reusable Workflow setup. Existing Job snapshots and Job Tasks are not deleted.
                </div>

                <div>
                    <b style="display:block;font-size:15px;color:#15263e">{{ $workflowDeleteImpact['name'] ?? 'Workflow' }}</b>
                    <span style="display:block;margin-top:4px;color:#61748e;font-size:11px">
                        FlowTrack checked Jobs created from this Workflow. Existing Jobs use private snapshots and will not be deleted.
                    </span>
                </div>

                @if(!empty($workflowDeleteImpact['replacement_default']))
                    <div class="flash success" style="margin:0">
                        This is the current default Workflow. After deletion,
                        <b>{{ $workflowDeleteImpact['replacement_default']['name'] }}</b> will become the active default automatically.
                    </div>
                @elseif($workflowDeleteImpact['will_leave_no_default'] ?? false)
                    <div class="flash success" style="margin:0">
                        This is the last Workflow. It can be deleted; the next Workflow you create will become the default automatically.
                    </div>
                @endif

                @if(!($workflowDeleteImpact['can_delete'] ?? true))
                    <div class="flash error" style="margin:0">
                        {{ $workflowDeleteImpact['blocked_reason'] ?? 'This Workflow cannot be deleted.' }}
                    </div>
                @else
                    <div class="ft-admin-stats" style="margin:0">
                        <div><span>Workflow phases</span><b>{{ $workflowDeleteImpact['phase_count'] ?? 0 }}</b></div>
                        <div><span>Jobs preserved</span><b>{{ $workflowDeleteImpact['job_count'] ?? 0 }}</b></div>
                        <div><span>Tasks preserved</span><b>{{ $workflowDeleteImpact['task_count'] ?? 0 }}</b></div>
                    </div>

                    @if(($workflowDeleteImpact['job_count'] ?? 0) > 0)
                        <div style="border:1px solid #f0d2cf;background:#fffafa;border-radius:10px;padding:12px">
                            <b style="display:block;font-size:12px;color:#a72822;margin-bottom:8px">Jobs that will remain unchanged</b>
                            <div style="display:grid;gap:7px;max-height:190px;overflow:auto">
                                @foreach(($workflowDeleteImpact['jobs'] ?? []) as $job)
                                    <div style="display:flex;justify-content:space-between;gap:12px;border-bottom:1px solid #f2e4e2;padding-bottom:6px">
                                        <span style="font-size:11px"><b>{{ $job['job_number'] }}</b> · {{ $job['title'] }}</span>
                                        @if($job['trashed'] ?? false)<small style="color:#8a6a67">Already trashed</small>@endif
                                    </div>
                                @endforeach
                            </div>
                            @if(($workflowDeleteImpact['job_count'] ?? 0) > count($workflowDeleteImpact['jobs'] ?? []))
                                <small style="display:block;margin-top:8px;color:#6c7d92">And {{ ($workflowDeleteImpact['job_count'] ?? 0) - count($workflowDeleteImpact['jobs'] ?? []) }} more linked Jobs.</small>
                            @endif
                        </div>
                    @endif

                    @if(($workflowDeleteImpact['task_count'] ?? 0) > 0)
                        <div style="border:1px solid #e1e7ef;border-radius:10px;padding:12px">
                            <b style="display:block;font-size:12px;color:#263b58;margin-bottom:8px">Tasks included in those Jobs</b>
                            <div style="display:grid;gap:6px">
                                @foreach(($workflowDeleteImpact['tasks'] ?? []) as $task)
                                    <span style="font-size:10.5px;color:#526780"><b style="color:#24364f">{{ $task['task_number'] }}</b> · {{ $task['title'] }} @if($task['job_number']) · {{ $task['job_number'] }} @endif</span>
                                @endforeach
                            </div>
                            @if(($workflowDeleteImpact['task_count'] ?? 0) > count($workflowDeleteImpact['tasks'] ?? []))
                                <small style="display:block;margin-top:8px;color:#6c7d92">And {{ ($workflowDeleteImpact['task_count'] ?? 0) - count($workflowDeleteImpact['tasks'] ?? []) }} more Tasks.</small>
                            @endif
                        </div>
                    @endif

                    <p style="margin:0;color:#526780;font-size:11px;line-height:1.5">
                        Continuing deletes only the reusable Workflow setup and its setup phases. Any older Job that still points directly to this Workflow is first converted to its own private snapshot. No Job, Task, document, comment, or history record is deleted.
                    </p>
                @endif
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closeWorkflowDelete">Cancel</button>
                @if($workflowDeleteImpact['can_delete'] ?? false)
                    <button type="button" class="ft-admin-danger" wire:click="confirmDeleteWorkflow" wire:loading.attr="disabled" wire:target="confirmDeleteWorkflow">
                        <span wire:loading.remove wire:target="confirmDeleteWorkflow">Delete Workflow only</span>
                        <span wire:loading wire:target="confirmDeleteWorkflow">Deleting…</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    @if($showPhaseModal)
        <div class="ft-reference-overlay" wire:click.self="closePhase"></div>
        <div class="ft-phase-reference-modal" role="dialog" aria-modal="true" aria-label="{{ $editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase' }}">
            <div class="ft-phase-modal-head">
                <h2>{{ $editPhaseId ? 'Edit Workflow Phase' : 'Add Workflow Phase' }}</h2>
                <button type="button" wire:click="closePhase">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="ft-phase-two-col">
                    <div class="ft-admin-field">
                        <label>Phase name *</label>
                        <input type="text" wire:model="phaseName" placeholder="New Phase">
                        @error('phaseName')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="ft-admin-field">
                        <label>Short label *</label>
                        <input type="text" wire:model="shortName" placeholder="New">
                        @error('shortName')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="ft-admin-field">
                    <label>Task Pack</label>
                    <select wire:model="taskPackId">
                        <option value="">No Task Pack</option>
                        @foreach($taskPacks as $taskPack)<option value="{{ $taskPack->id }}">{{ $taskPack->name }}</option>@endforeach
                    </select>
                </div>
                <div class="ft-admin-field">
                    <label>Entry rule</label>
                    <input type="text" wire:model="entryCondition" placeholder="Previous phase complete">
                </div>
                <div class="ft-admin-field">
                    <label>Exit control</label>
                    <input type="text" wire:model="exitCondition" placeholder="Required work complete">
                </div>

                <div class="ft-phase-checks">
                    <label><input type="checkbox" wire:model="phaseActive"><span>Phase active</span></label>
                </div>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closePhase">Cancel</button>
                <button type="button" class="ft-admin-primary" wire:click="savePhase">Save Phase</button>
            </div>
        </div>
    @endif
</div>
