@props([
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'mentionUsers'=>collect(),
])
@php
    $selectedClient = $clients->firstWhere('id', (int)$clientId);
    $selectedWorkflow = $workflows->firstWhere('id', (int)$workflowId);
    $selectedOwnerOption = collect($ownerFilterOptions)->first(fn($item) => (int)($item['id'] ?? 0) === (int)($ownerId ?? 0));
    $allowedPhases = $selectedWorkflow?->phases?->where('allow_job_start', true) ?? collect();
    $taskCount = $selectedWorkflow?->phases?->sum(fn($phase) => $phase->taskPack?->templates?->count() ?? 0) ?? 0;
    $totalUnits = collect($jobItems)->sum(fn($item)=>(int)($item['quantity'] ?? 0));
    $createReady = $catalogReady && $assignmentReady && $workflowReady;
@endphp
<div {{ $attributes->class('ft-create-job-page') }}>
    <div class="ft-create-shell">
        <div class="ft-create-breadcrumb">Orders / Create order</div>
        <div class="ft-create-title"><h1>Create new order</h1><p>Set the order scope, products, ownership and workflow.</p></div>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>1</span><h2>Order basics</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field"><b>Order code</b><div class="ft-locked-input">Generated automatically <span>♙</span></div></label>
                <div class="ft-create-field">
                    <x-ui.remote-filter
                        class="ft-create-remote-select"
                        label="Client *"
                        property="clientId"
                        type="clients"
                        context="create-job"
                        action="setCreateSelector"
                        :value="$clientId"
                        placeholder="Select client"
                        :selected-label="$selectedClient?->name"
                        :initial-options="$clientFilterOptions"
                        :clearable="false"
                        wire:key="create-client-selector"
                    />
                    @error('clientId')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
                <label class="ft-create-field"><b>Client contact</b><input value="{{ $selectedClient?->contact_name ?? 'No contact recorded' }}" readonly></label>
                <label class="ft-create-field"><b>Order title *</b><input wire:model="jobTitle" placeholder="e.g. Conference merchandise order">@error('jobTitle')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field ft-mention-host"><b>Request description</b><textarea class="ft-mention-input" data-rich-text wire:model="description" rows="4" autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}" placeholder="Type @ to mention a user. Add specifications, target price or customization requirements..."></textarea></label>
            </div>
        </section>

        @if($catalogReady)
        <section class="ft-create-section" wire:key="create-catalog-ready">
            <div class="ft-create-section-title"><span>2</span><h2>Products & quantities</h2></div>
            <div class="ft-product-rows">
                @foreach($jobItems as $index => $item)
                    @php
                        $selectedCategory = $item['category'] ?? '';
                        $selectedProduct = $item['product'] ?? '';
                    @endphp
                    <div class="ft-product-row" wire:key="job-product-{{ $index }}">
                        <div>
                            <x-ui.remote-filter
                                class="ft-create-remote-select"
                                label="Product category"
                                property="jobItems.{{ $index }}.category"
                                type="product-categories"
                                context="create-job"
                        action="setCreateSelector"
                                :value="$selectedCategory"
                                :selected-label="$selectedCategory ?: null"
                                placeholder="Select category"
                                :initial-options="$categoryFilterOptions"
                                :clearable="false"
                                wire:key="create-category-selector-{{ $index }}"
                            />
                            @error("jobItems.$index.category")<small class="validation-error">{{ $message }}</small>@enderror
                        </div>
                        <div>
                            <x-ui.remote-filter
                                class="ft-create-remote-select"
                                label="Product"
                                property="jobItems.{{ $index }}.product"
                                type="products"
                                context="create-job"
                        action="setCreateSelector"
                                :value="$selectedProduct"
                                :selected-label="$selectedProduct ?: null"
                                :placeholder="$selectedCategory ? 'Select product' : 'Select category first'"
                                :params="['category' => $selectedCategory]"
                                :disabled="blank($selectedCategory)"
                                wire:key="create-product-selector-{{ $index }}-{{ md5($selectedCategory) }}"
                            />
                            @error("jobItems.$index.product")<small class="validation-error">{{ $message }}</small>@enderror
                        </div>
                        <label class="ft-qty-field"><b>Quantity</b><input type="number" min="1" wire:model.live="jobItems.{{ $index }}.quantity">@error("jobItems.$index.quantity")<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <button type="button" class="ft-product-delete" wire:click="removeProductRow({{ $index }})" @disabled(count($jobItems) <= 1) title="Remove product">▱</button>
                    </div>
                @endforeach
            </div>
            <div class="ft-product-row-footer"><button type="button" wire:click="addProductRow">＋ Add product</button><span>{{ count($jobItems) }} {{ \Illuminate\Support\Str::plural('product',count($jobItems)) }} · {{ number_format($totalUnits) }} total units</span></div>
        </section>
        @else
            <x-jobs.create-section-placeholder number="2" title="Products & quantities" section="catalog" :rows="3" />
        @endif

        @if($assignmentReady)
        <section class="ft-create-section" wire:key="create-assignment-ready">
            <div class="ft-create-section-title"><span>3</span><h2>Schedule & owner</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.deliveryDate?.showPicker?.(); $refs.deliveryDate?.focus(); }"><b>Required delivery date *</b><input x-ref="deliveryDate" type="date" wire:model="deliveryDate">@error('deliveryDate')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field"><b>Priority</b><select wire:model="priority">@foreach($priorities as $priority)<option value="{{ $priority->name }}">{{ $priority->name }}</option>@endforeach</select></label>
                <div class="ft-create-field">
                    <x-ui.remote-filter
                        class="ft-create-remote-select"
                        label="Order owner *"
                        property="ownerId"
                        type="users"
                        context="create-job"
                        action="setCreateSelector"
                        :value="$ownerId"
                        placeholder="Select owner"
                        :selected-label="$selectedOwnerOption['label'] ?? null"
                        :initial-options="$ownerFilterOptions"
                        :clearable="false"
                        wire:key="create-owner-selector"
                    />
                    <small>Accountable for overall delivery.</small>
                    @error('ownerId')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
            </div>
        </section>
        @else
            <x-jobs.create-section-placeholder number="3" title="Schedule & owner" section="assignment" :rows="3" />
        @endif

        @if($workflowReady)
        <section class="ft-create-section" wire:key="create-workflow-ready">
            <div class="ft-create-section-title"><span>4</span><h2>Workflow</h2></div>
            <div class="ft-create-fields">
                <div class="ft-create-field">
                    <x-ui.remote-filter
                        class="ft-create-remote-select"
                        label="Workflow"
                        property="workflowId"
                        type="workflows"
                        context="create-job"
                        action="setCreateSelector"
                        :value="$workflowId"
                        placeholder="Select workflow"
                        :selected-label="$selectedWorkflow?->name"
                        :initial-options="$workflowFilterOptions"
                        :params="['client_id' => $clientId]"
                        :clearable="false"
                        wire:key="create-workflow-selector-{{ $clientId ?: 'none' }}"
                    />
                </div>
                <label class="ft-create-field"><b>Starting phase</b><select wire:model.live="workflowPhaseId">@foreach($allowedPhases as $phase)<option value="{{ $phase->id }}">{{ $phase->sequence }}. {{ $phase->name }}</option>@endforeach</select>@error('workflowPhaseId')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-workflow-summary"><span>ⓘ {{ $selectedWorkflow?->phases?->count() ?? 0 }} phases · {{ $taskCount }} tasks will be created</span>@if(auth()->user()->canAccess('workflow.manage'))<a href="{{ route('workflow.setup') }}" wire:navigate>Preview workflow ↗</a>@else<span>Preview workflow ↗</span>@endif</div>
                <p class="ft-create-note">Workflow and starting phase are fixed after creation; transitions are managed from the Workflow tab.</p>
            </div>
        </section>
        @else
            <x-jobs.create-section-placeholder number="4" title="Workflow" section="workflow" :rows="2" />
        @endif

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>5</span><h2>Attachments</h2></div>
            @if(auth()->user()->canModule('documents','create'))
                <div class="ft-create-upload-wrap">
                <div class="ft-create-upload ft-livewire-upload-zone" data-file-dropzone>
                    <span class="ft-create-paperclip">⌕</span>
                    <div><b>Drop files here or <label for="job-create-files">browse</label></b><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    @if(auth()->user()->canModule('documents','view'))<a href="{{ route('documents.index') }}" wire:navigate>Open Documents</a>@endif
                    <input id="job-create-files" type="file" wire:model="jobAttachments" multiple hidden accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                </div>
                </div>
            @else
                <div class="ft-create-note">Your role does not allow document uploads during Order creation.</div>
            @endif
            @if(count($jobAttachments))<div class="ft-create-upload-list">@foreach($jobAttachments as $file)<span>{{ $file->getClientOriginalName() }}</span>@endforeach</div>@endif
            @error('jobAttachments.*')<small class="validation-error">{{ $message }}</small>@enderror
        </section>

        <div class="ft-create-actions">
            <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
            <button type="button" class="ft-create-draft" wire:click="saveDraft" @disabled(!$createReady)>Save draft</button>
            <button type="button" class="ft-create-primary" wire:click="createJob" @disabled(!$createReady)>Create order</button>
        </div>
        @error('createLoading')<div class="validation-error">{{ $message }}</div>@enderror
    </div>
</div>
