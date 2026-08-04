<div>
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
                    <b>{{ $label }}</b><small>{{ \App\Models\MasterRecord::where('workspace_id',app(\App\Services\MasterDataService::class)->workspaceId())->where('type',$key)->count() }} records</small>
                </button>
            @endforeach
        </div>

        <div class="card section-card">
            <div class="master-head">
                <div><h3 style="margin:0 0 4px">{{ $labels[$group] }}</h3><div class="small muted">All values are read from the database and become available immediately after saving.</div></div>
                <input wire:model.live.debounce.300ms="search" style="max-width:280px;border:1px solid var(--line);border-radius:8px;padding:9px" placeholder="Search records…">
            </div>
            <div class="table-wrap">
                <table class="master-table">
                    <thead><tr><th>Order</th><th>Code</th><th>Name</th><th>Parent</th><th>Description / Use</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->sort_order }}</td>
                            <td><b>{{ $r->code }}</b></td>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->parent?->name ?? '—' }}</td>
                            <td>{{ $r->description ?: '—' }}</td>
                            <td><x-ui.badge :label="$r->status==='active'?'Active':'Inactive'"/></td>
                            <td><div class="row-actions"><button class="mini-btn" wire:click="open({{ $r->id }})">Edit</button><button class="mini-btn" wire:click="toggle({{ $r->id }})">{{ $r->status==='active'?'Deactivate':'Activate' }}</button><button class="mini-btn" wire:click="deleteRecord({{ $r->id }})" wire:confirm="Delete this master record?">Delete</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">No records found.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
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
                    <div class="field"><label>Parent record</label><select wire:model="parentId"><option value="">No parent</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Status</label><select wire:model="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                    <div class="field"><label>Sort order</label><input type="number" min="0" wire:model="sortOrder"></div>
                    <div class="field full"><label>Description</label><textarea wire:model="description" rows="3"></textarea></div>
                    <div class="field full"><label>Metadata (JSON)</label><textarea wire:model="metadataJson" rows="4" placeholder='{"color":"#F97366"}'></textarea>@error('metadataJson')<div class="validation-error">{{ $message }}</div>@enderror</div>
                </div>
            </div>
            <div class="modal-foot"><button class="ghost" wire:click="close">Cancel</button><button class="primary" wire:click="save">Save Record</button></div>
        </div>
    @endif
</div>
