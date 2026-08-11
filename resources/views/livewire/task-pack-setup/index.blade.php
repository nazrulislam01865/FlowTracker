@php
    $masterData = app(\App\Services\MasterDataService::class);
@endphp
<div wire:init="loadTaskPacks" class="ft-admin-reference ft-taskpack-reference">
    <div class="ft-admin-page-head">
        <div>
            <h1>Task Pack Setup</h1>
            <p>Create reusable task sequences that activate when a Job enters a workflow phase</p>
        </div>
        <a href="{{ route('task-pack.create') }}" wire:navigate class="ft-admin-primary">＋ Add Task Pack</a>
    </div>

    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @error('pack')<div class="flash error">{{ $message }}</div>@enderror
    @error('item')<div class="flash error">{{ $message }}</div>@enderror

    @if(!$showPackDeleteModal)
    <div class="ft-admin-stats">
        <div><span>Total Task Packs</span><b>{{ $packsReady ? $totalPacks : '…' }}</b></div>
        <div><span>Active Task Packs</span><b>{{ $packsReady ? $activePacks : '…' }}</b></div>
        <div><span>Configured Tasks</span><b>{{ $packsReady ? $configuredTasks : '…' }}</b></div>
        <div><span>Mapped Phases</span><b>{{ $packsReady ? $mappedPhases : '…' }}</b></div>
    </div>

    @if(!$packsReady)
        @include('livewire.shared.card-list-placeholder', ['cards' => 4])
    @else
    <div class="ft-taskpack-grid">
        @forelse($packs as $pack)
            <section class="ft-taskpack-card">
                <div class="ft-taskpack-card-head">
                    <div>
                        <h2>{{ $pack->name }}</h2>
                        <p>{{ $pack->code }} · {{ $pack->items->count() }} predefined task{{ $pack->items->count() === 1 ? '' : 's' }} · {{ $pack->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                    <div class="ft-taskpack-card-actions">
                        <a class="ft-admin-outline-small" href="{{ route('task-pack.edit', $pack->id) }}" wire:navigate>Edit</a>
                        <button type="button" class="ft-admin-danger-small" wire:click="requestDeletePack({{ $pack->id }})" wire:loading.attr="disabled" wire:target="requestDeletePack">Delete</button>
                    </div>
                </div>
                <p class="ft-taskpack-description">{{ $pack->description ?: 'No description' }}</p>

                <div class="ft-taskpack-items">
                    @forelse($pack->items as $item)
                        <div class="ft-taskpack-item-row">
                            <div>
                                <b>{{ $loop->iteration }}. {{ $item->title }}</b>
                                <small>
                                    {{ $item->defaultAssignee?->name ?? 'Unassigned' }} · Due set from Task details ·
                                    @if($item->priority)
                                        @php
                                            $itemPriorityColor = $masterData->displayColorFor('priority', $item->priority->name);
                                        @endphp
                                        <span class="ft-master-color-text" style="{{ \App\Support\MasterColor::style($itemPriorityColor) }}">{{ $item->priority->name }}</span>
                                    @else
                                        Use Job priority
                                    @endif
                                    @if($item->documentCategory) · Required file: {{ $item->documentCategory->name }} @endif
                                </small>
                            </div>
                            <span class="{{ $item->is_required ? 'is-required' : 'is-optional' }}">{{ $item->is_required ? 'Mandatory' : 'Optional' }}</span>
                        </div>
                    @empty
                        <div class="ft-taskpack-empty">No predefined tasks.</div>
                    @endforelse
                </div>
            </section>
        @empty
            <div class="ft-admin-empty-wide">No Task Packs configured. Use “Add Task Pack” to create the first one.</div>
        @endforelse
    </div>
    @endif

    @endif

    @if($showPackDeleteModal)
        <div class="ft-reference-overlay" wire:click.self="closePackDelete"></div>
        <div class="ft-phase-reference-modal" role="alertdialog" aria-modal="true" aria-label="Delete Task Pack permanently" style="width:min(720px,calc(100vw - 32px))">
            <div class="ft-phase-modal-head">
                <h2>Delete Task Pack permanently?</h2>
                <button type="button" wire:click="closePackDelete">×</button>
            </div>
            <div class="ft-phase-modal-body">
                <div class="flash error" style="margin:0">
                    This permanently deletes this reusable Task Pack setup. Existing Job snapshots and Job Tasks are not deleted.
                </div>

                <div>
                    <b style="display:block;font-size:15px;color:#15263e">{{ $packDeleteImpact['name'] ?? 'Task Pack' }}</b>
                    <span style="display:block;margin-top:4px;color:#61748e;font-size:11px">
                        FlowTrack checked Workflow mappings and Jobs that originated from those Workflows before allowing deletion.
                    </span>
                </div>

                <div class="ft-admin-stats" style="margin:0">
                    <div><span>Mapped phases</span><b>{{ $packDeleteImpact['mapped_phase_count'] ?? 0 }}</b></div>
                    <div><span>Jobs preserved</span><b>{{ $packDeleteImpact['job_count'] ?? 0 }}</b></div>
                    <div><span>Tasks preserved</span><b>{{ $packDeleteImpact['task_count'] ?? 0 }}</b></div>
                </div>

                @if(($packDeleteImpact['mapped_phase_count'] ?? 0) > 0)
                    <div style="border:1px solid #d9e4f2;background:#f8fbff;border-radius:10px;padding:12px">
                        <b style="display:block;font-size:12px;color:#263b58;margin-bottom:8px">Workflow phases using this Task Pack</b>
                        <div style="display:grid;gap:6px">
                            @foreach(($packDeleteImpact['mapped_phases'] ?? []) as $phase)
                                <span style="font-size:10.5px;color:#526780"><b style="color:#24364f">{{ $phase['workflow_name'] }}</b> · Stage {{ $phase['sequence'] }} · {{ $phase['name'] }}</span>
                            @endforeach
                        </div>
                        @if(($packDeleteImpact['mapped_phase_count'] ?? 0) > count($packDeleteImpact['mapped_phases'] ?? []))
                            <small style="display:block;margin-top:8px;color:#6c7d92">And {{ ($packDeleteImpact['mapped_phase_count'] ?? 0) - count($packDeleteImpact['mapped_phases'] ?? []) }} more mapped phases.</small>
                        @endif
                        <small style="display:block;margin-top:9px;color:#526780">These Workflow phases will remain, but their Task Pack assignment will be removed.</small>
                    </div>
                @endif

                @if(($packDeleteImpact['job_count'] ?? 0) > 0)
                    <div style="border:1px solid #f0d2cf;background:#fffafa;border-radius:10px;padding:12px">
                        <b style="display:block;font-size:12px;color:#a72822;margin-bottom:8px">Jobs that remain independent of this Task Pack</b>
                        <div style="display:grid;gap:7px;max-height:190px;overflow:auto">
                            @foreach(($packDeleteImpact['jobs'] ?? []) as $job)
                                <div style="display:flex;justify-content:space-between;gap:12px;border-bottom:1px solid #f2e4e2;padding-bottom:6px">
                                    <span style="font-size:11px"><b>{{ $job['job_number'] }}</b> · {{ $job['title'] }}</span>
                                    @if($job['trashed'] ?? false)<small style="color:#8a6a67">Already trashed</small>@endif
                                </div>
                            @endforeach
                        </div>
                        @if(($packDeleteImpact['job_count'] ?? 0) > count($packDeleteImpact['jobs'] ?? []))
                            <small style="display:block;margin-top:8px;color:#6c7d92">And {{ ($packDeleteImpact['job_count'] ?? 0) - count($packDeleteImpact['jobs'] ?? []) }} more linked Jobs.</small>
                        @endif
                    </div>
                @endif

                <p style="margin:0;color:#526780;font-size:11px;line-height:1.5">
                    Deleting this reusable Task Pack does not delete existing Job Tasks. Older Jobs are snapshotted first when needed, and each Job keeps its own copied phase/task definitions.
                </p>
            </div>
            <div class="ft-phase-modal-footer">
                <button type="button" class="ft-admin-cancel" wire:click="closePackDelete">Cancel</button>
                <button type="button" class="ft-admin-danger" wire:click="confirmDeletePack" wire:loading.attr="disabled" wire:target="confirmDeletePack">
                    <span wire:loading.remove wire:target="confirmDeletePack">Delete Task Pack only</span>
                    <span wire:loading wire:target="confirmDeletePack">Deleting…</span>
                </button>
            </div>
        </div>
    @endif
</div>
