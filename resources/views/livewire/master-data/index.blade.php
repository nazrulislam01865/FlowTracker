<div wire:init="loadMasterRecords">
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
                @include('livewire.shared.table-rows-placeholder', ['columns' => $group === 'product' ? 7 : 6, 'rows' => 8])
            @else
            <div class="table-wrap" wire:key="master-records-{{ $group }}">
                <table class="master-table">
                    <thead><tr><th>Order</th><th>Code</th><th>Name</th>@if($group === 'product')<th>Product Category</th>@endif<th>Description / Use</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td data-label="Order">{{ $r->sort_order }}</td>
                            <td data-label="Code"><b>{{ $r->code }}</b></td>
                            <td data-label="Name">{{ $r->name }}</td>
                            @if($group === 'product')<td data-label="Product Category">{{ $r->parent?->name ?? '—' }}</td>@endif
                            <td data-label="Description / Use">{{ $r->description ?: '—' }}</td>
                            <td data-label="Status"><x-ui.badge :label="$r->status==='active'?'Active':'Inactive'"/></td>
                            <td data-label="Actions"><div class="row-actions"><button class="mini-btn" wire:click="open({{ $r->id }})">Edit</button><button class="mini-btn" wire:click="toggle({{ $r->id }})">{{ $r->status==='active'?'Deactivate':'Activate' }}</button><button class="mini-btn" wire:click="deleteRecord({{ $r->id }})" wire:confirm="Delete this master record?">Delete</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $group === 'product' ? 7 : 6 }}"><div class="empty-state">No records found.</div></td></tr>
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
                    <div class="field"><label>Code *</label><input wire:model="code" maxlength="40">@error('code')<div class="validation-error">{{ $message }}</div>@enderror</div>
                    <div class="field"><label>Name *</label><input wire:model="name">@error('name')<div class="validation-error">{{ $message }}</div>@enderror</div>
                    @if($group === 'product')
                        <div class="field"><label>Product category</label><select wire:model="parentId"><option value="">No category</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>@error('parentId')<div class="validation-error">{{ $message }}</div>@enderror</div>
                    @endif
                    <div class="field"><label>Status</label><select wire:model="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                    <div class="field"><label>Sort order</label><input type="number" min="0" wire:model="sortOrder"></div>
                    <div class="field full"><label>Description</label><textarea wire:model="description" rows="3"></textarea></div>
                </div>
            </div>
            <div class="modal-foot"><button class="ghost" wire:click="close">Cancel</button><button class="primary" wire:click="save">Save Record</button></div>
        </div>
    @endif
</div>
