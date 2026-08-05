<div class="ft-admin-reference ft-workflow-reference">
    <div class="ft-admin-page-head">
        <div>
            <h1>Workflow Setup</h1>
            <p>Define phase sequence, allowed starting stages, Task Packs, documents and skip rules</p>
        </div>
        <div class="ft-admin-head-actions">
            @if($selected)
                <a href="{{ route('workflow.create', ['source' => $selected->id]) }}" wire:navigate class="ft-admin-outline">Duplicate</a>
            @else
                <span class="ft-admin-outline is-disabled">Duplicate</span>
            @endif
            <a href="{{ route('workflow.create') }}" wire:navigate class="ft-admin-primary">＋ New Workflow</a>
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
                        <a href="{{ route('workflow.edit', $selected->id) }}" wire:navigate class="ft-admin-outline">Edit Details</a>
                        <button type="button" class="ft-admin-danger" wire:click="deleteWorkflow({{ $selected->id }})" wire:confirm="Delete this Workflow? Workflows already used by Jobs cannot be deleted and must be deactivated instead.">Delete Workflow</button>
                        <button type="button" class="ft-admin-primary" wire:click="openPhase">＋ Add Phase</button>
                    </div>
                </div>

                <div class="ft-workflow-rule-note">
                    <b>Starting-stage rule</b>
                    <p>Only phases marked “Allow Job Start” appear in the New Job form. Earlier phases are recorded as skipped, completed outside the system, or migrated.</p>
                </div>

                <div class="ft-workflow-table-wrap">
                    <table class="ft-workflow-config-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Phase</th>
                                <th>Task Pack</th>
                                <th>Allow Job<br>Start</th>
                                <th>Can<br>Skip</th>
                                <th>Auto Move</th>
                                <th>Required<br>Document</th>
                                <th>Entry / Exit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selected->phases as $phase)
                                <tr wire:key="workflow-phase-row-{{ $phase->id }}">
                                    <td>
                                        <div class="ft-sequence-buttons">
                                            <button type="button" wire:click="move({{ $phase->id }}, -1)" @disabled($loop->first)>↑</button>
                                            <button type="button" wire:click="move({{ $phase->id }}, 1)" @disabled($loop->last)>↓</button>
                                        </div>
                                    </td>
                                    <td>
                                        <b>{{ $phase->name }}</b>
                                        <span>Stage {{ $phase->sequence }}</span>
                                    </td>
                                    <td>{{ $phase->taskPack?->name ?? 'No Task Pack' }}</td>
                                    <td>
                                        <label class="ft-table-check"><input type="checkbox" @checked($phase->allow_job_start) disabled><span>{{ $phase->allow_job_start ? 'Allowed' : 'Not allowed' }}</span></label>
                                    </td>
                                    <td>
                                        <label class="ft-table-check"><input type="checkbox" @checked($phase->is_skippable) disabled><span>{{ $phase->is_skippable ? 'Yes' : 'No' }}</span></label>
                                    </td>
                                    <td><span class="ft-auto-pill {{ $phase->auto_advance_on_ready ? 'automatic' : '' }}">{{ $phase->auto_advance_on_ready ? 'Automatic' : 'Manual' }}</span></td>
                                    <td>{{ $phase->documentCategory?->name ?? '—' }}</td>
                                    <td class="ft-entry-exit"><div><b>In:</b> {{ $phase->entry_condition ?: '—' }}</div><div><b>Out:</b> {{ $phase->exit_condition ?: '—' }}</div></td>
                                    <td>
                                        <div class="ft-row-action-buttons">
                                            <button type="button" wire:click="openPhase({{ $phase->id }})">Edit</button>
                                            <button type="button" wire:click="deletePhase({{ $phase->id }})" wire:confirm="Remove this workflow phase?">Remove</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="ft-workflow-empty-row">No phases configured. Add the first phase to this workflow.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="ft-admin-empty-wide">Create a Workflow to begin.</div>
            @endif
        </section>
    </div>

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
                    <div class="ft-admin-field">
                        <label>Task Pack</label>
                        <select wire:model="taskPackId">
                            <option value="">No Task Pack</option>
                            @foreach($taskPacks as $taskPack)<option value="{{ $taskPack->id }}">{{ $taskPack->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="ft-admin-field">
                        <label>Required document</label>
                        <select wire:model="documentCategoryId">
                            <option value="">None</option>
                            @foreach($documentCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                        </select>
                    </div>
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
                    <label><input type="checkbox" wire:model="allowJobStart"><span>Allow users to create a Job starting from this phase</span></label>
                    <label><input type="checkbox" wire:model="isSkippable"><span>Allow this phase to be skipped during normal progression</span></label>
                    <label><input type="checkbox" wire:model="autoAdvanceOnReady"><span>Automatically move the Job when all task, document and blocker gates are ready</span></label>
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
