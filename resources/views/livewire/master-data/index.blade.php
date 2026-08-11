<div wire:init="loadMasterRecords">
    @php
        $hasParent = in_array($group, ['product', 'state'], true);
        $hasColor = in_array($group, \App\Services\MasterDataService::COLOR_TYPES, true);
        $columnCount = 6 + ($hasParent ? 1 : 0) + ($hasColor ? 1 : 0);
        $colorUsageLabel = match ($group) {
            'task_status' => 'task status',
            'task_flag' => 'task flag',
            'priority' => 'priority',
            'inquiry_status' => 'inquiry status',
            default => 'master data',
        };
    @endphp
    <x-ui.page-head title="Master Data" subtitle="Maintain the values used across jobs, Task Packs, workflows, documents, production and shipment">
        <x-slot:actions><button class="primary" wire:click="open">＋ Add Record</button></x-slot:actions>
    </x-ui.page-head>

    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @error('record')<div class="flash error">{{ $message }}</div>@enderror

    <div class="config-summary">
        <div class="card config-stat"><span>Master Categories</span><b>{{ count($labels) }}</b></div>
        <div class="card config-stat"><span>Total Records</span><b>{{ $total }}</b></div>
        <div class="card config-stat"><span>Active Records</span><b>{{ $active }}</b></div>
        <div class="card config-stat"><span>Selected Category</span><b style="font-size:15px">{{ $labels[$group] }}</b></div>
    </div>

    <div class="master-layout">
        <div class="card master-categories">
            @foreach($labels as $key=>$label)
                <button class="{{ $key===$group?'active':'' }}" wire:click="selectGroup('{{ $key }}')">
                    <b>{{ $label }}</b><small>{{ $groupCounts[$key] ?? 0 }} records</small>
                </button>
            @endforeach
        </div>

        <div class="card section-card">
            <div class="master-head">
                <div><h3 style="margin:0 0 4px">{{ $labels[$group] }}</h3><div class="small muted">All values are read from the database and become available immediately after saving.</div></div>
                <input wire:model.live.debounce.300ms="search" style="max-width:280px;border:1px solid var(--line);border-radius:8px;padding:9px" placeholder="Search records…">
            </div>
            @if(!$recordsReady)
                @include('livewire.shared.table-rows-placeholder', ['columns' => $columnCount, 'rows' => 8])
            @else
            <div class="table-wrap" wire:key="master-records-{{ $group }}">
                <table class="master-table">
                    <thead><tr><th>Order</th><th>Code</th><th>Name</th>@if($hasParent)<th>{{ $group === 'state' ? 'Country' : 'Product Category' }}</th>@endif<th>Description / Use</th>@if($hasColor)<th>Color</th>@endif<th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td data-label="Order">{{ $r->sort_order }}</td>
                            <td data-label="Code"><b>{{ $r->code }}</b></td>
                            <td data-label="Name">{{ $r->name }}</td>
                            @if($hasParent)<td data-label="{{ $group === 'state' ? 'Country' : 'Product Category' }}">{{ $r->parent?->name ?? '—' }}</td>@endif
                            <td data-label="Description / Use">{{ $r->description ?: '—' }}</td>
                            @if($hasColor)
                                @php
                                    $rowColor = \App\Support\MasterColor::normalize($r->color) ?: \App\Support\MasterColor::defaultFor($group, $r->name);
                                @endphp
                                <td data-label="Color">
                                    <label class="ft-master-color-chip" style="{{ \App\Support\MasterColor::style($rowColor) }}" title="Choose color for {{ $r->name }}">
                                        <input
                                            class="ft-master-inline-color"
                                            type="color"
                                            value="{{ $rowColor }}"
                                            wire:change="updateColor({{ $r->id }}, $event.target.value)"
                                            wire:loading.attr="disabled"
                                            aria-label="Choose color for {{ $r->name }}"
                                        >
                                        <span>{{ $rowColor }}</span>
                                    </label>
                                </td>
                            @endif
                            <td data-label="Status"><x-ui.badge :label="$r->status==='active'?'Active':'Inactive'"/></td>
                            <td data-label="Actions"><div class="row-actions"><button class="mini-btn" wire:click="open({{ $r->id }})">Edit</button><button class="mini-btn" wire:click="toggle({{ $r->id }})">{{ $r->status==='active'?'Deactivate':'Activate' }}</button><button class="mini-btn" wire:click="deleteRecord({{ $r->id }})" wire:confirm="Delete this master record?">Delete</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $columnCount }}"><div class="empty-state">No records found.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($rows->total() > 30)
                <div class="ft-list-pagination ft-master-pagination">
                    <span>Showing <b>{{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }}</b> of {{ $rows->total() }} records</span>
                    <div class="ft-page-actions">
                        <button type="button" wire:click="previousPage('masterPage')" @disabled($rows->onFirstPage())>Previous</button>
                        <span>Page {{ $rows->currentPage() }} of {{ $rows->lastPage() }}</span>
                        <button type="button" wire:click="nextPage('masterPage')" @disabled(!$rows->hasMorePages())>Next</button>
                    </div>
                </div>
            @endif
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="overlay livewire-overlay" wire:click.self="close"></div>
        <div class="modal livewire-modal">
            <div class="modal-head"><h2>{{ $editId?'Edit':'Add' }} {{ $labels[$group] }} Record</h2><button class="close-btn" wire:click="close">×</button></div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Code</label>
                        <div class="ft-admin-locked">{{ $code }}</div>
                        <small class="small muted">{{ $editId ? 'System code is permanently locked.' : 'Automatically generated and permanently locked.' }}</small>
                        @error('code')<div class="validation-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field"><label>Name *</label><input wire:model="name">@error('name')<div class="validation-error">{{ $message }}</div>@enderror</div>
                    @if($group === 'product')
                        <div class="field"><label>Product category</label><select wire:model="parentId"><option value="">No category</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>@error('parentId')<div class="validation-error">{{ $message }}</div>@enderror</div>
                    @elseif($group === 'state')
                        <div class="field"><label>Country *</label><select wire:model="parentId"><option value="">Select country</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>@error('parentId')<div class="validation-error">{{ $message }}</div>@enderror</div>
                    @endif
                    <div class="field"><label>Status</label><select wire:model="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                    <div class="field"><label>Sort order</label><input type="number" min="0" wire:model="sortOrder"></div>
                    @if($hasColor)
                        <div class="field full">
                            <label>Color *</label>
                            <div class="ft-master-color-picker-row" style="{{ \App\Support\MasterColor::style($color) }}">
                                <input class="ft-master-color-picker" type="color" wire:model.live="color" aria-label="Choose {{ $labels[$group] }} color">
                                <input type="text" maxlength="7" wire:model.blur="color" placeholder="#2563EB" aria-label="Hex color code">
                                <span class="ft-master-color-preview"><i class="ft-master-color-dot"></i><span>This color will be used for {{ $colorUsageLabel }} labels across FlowTrack.</span></span>
                            </div>
                            @error('color')<div class="validation-error">{{ $message }}</div>@enderror
                        </div>
                    @endif
                    <div class="field full"><label>Description</label><textarea wire:model="description" rows="3"></textarea></div>
                </div>
            </div>
            <div class="modal-foot"><button class="ghost" wire:click="close">Cancel</button><button class="primary" wire:click="save">Save Record</button></div>
        </div>
    @endif
</div>
