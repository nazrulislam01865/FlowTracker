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
                        <button type="button" class="ft-admin-danger-small" wire:click="deletePack({{ $pack->id }})" wire:confirm="Delete this Task Pack? This is only allowed when it is not mapped to a Workflow and has not generated Tasks.">Delete</button>
                    </div>
                </div>
                <p class="ft-taskpack-description">{{ $pack->description ?: 'No description' }}</p>

                <div class="ft-taskpack-items">
                    @forelse($pack->items as $item)
                        <div class="ft-taskpack-item-row">
                            <div>
                                <b>{{ $loop->iteration }}. {{ $item->title }}</b>
                                <small>
                                    {{ $item->defaultAssignee?->name ?? 'Unassigned' }} · Due set from Task details · {{ $item->priority?->name ?? 'Use Job priority' }}
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
</div>
