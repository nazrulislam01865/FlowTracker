@props([
    'clients','workflows','categories','priorities','clientId','workflowId','ownerId','jobItems','jobAttachments',
    'priority'=>'Medium','productionUrgencies'=>collect(),'shipmentUrgencies'=>collect(),'productionUrgencyIds'=>[],'shipmentUrgencyIds'=>[],'isRepeatedOrder'=>false,'repeatedOrderNumber'=>'',
    'clientFilterOptions'=>collect(),'ownerFilterOptions'=>collect(),'workflowFilterOptions'=>collect(),'categoryFilterOptions'=>collect(),
    'productCategories'=>collect(),'productSearchResults'=>collect(),'selectedProductDetails'=>collect(),'activeProductCount'=>0,'productResultTotal'=>0,
    'canUseOrderProductSelector'=>false,'canCreateCatalogProduct'=>false,'canViewProductCategories'=>false,'canCreateProductCategory'=>false,'duplicateProduct'=>null,'newProductCategoryMatches'=>collect(),'newProductSimilarCategories'=>collect(),
    'newProductSimilarProducts'=>collect(),'newProductSelectedCategory'=>null,'newProductHasExactCategory'=>false,'newProductImagePreview'=>null,
    'createProductSearch'=>'','createProductCategoryFilter'=>'','createProductShowAllResults'=>false,'showCreateOrderProductModal'=>false,
    'newProductCode'=>'','newProductCategoryId'=>null,'newProductCategorySearch'=>'','newProductCategoryName'=>'','newProductName'=>'',
    'catalogReady'=>false,'assignmentReady'=>false,'workflowReady'=>false,'workflowSelectorVersion'=>0,'workflowPhaseId'=>null,'mentionUsers'=>collect(),
])
@php
    $selectedClient = $clients->firstWhere('id', (int)$clientId);
    $selectedWorkflow = $workflows->firstWhere('id', (int)$workflowId);
    $selectedOwnerOption = collect($ownerFilterOptions)->first(fn($item) => (int)($item['id'] ?? 0) === (int)($ownerId ?? 0));
    $allowedPhases = $selectedWorkflow?->phases?->where('is_active', true)->where('allow_job_start', true) ?? collect();
    $taskCount = $selectedWorkflow?->phases?->sum(fn($phase) => $phase->taskPack?->templates?->count() ?? 0) ?? 0;
    $totalUnits = collect($jobItems)->sum(fn($item)=>(int)($item['quantity'] ?? 0));
    $createReady = $catalogReady && $assignmentReady && $workflowReady && $canUseOrderProductSelector;
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
                        wire:key="create-client-selector-{{ $clientId ?: 'none' }}"
                    />
                    @error('clientId')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
                <label class="ft-create-field"><b>Client contact</b><input value="{{ $selectedClient?->contact_name ?? 'No contact recorded' }}" readonly></label>
                <label class="ft-create-field"><b>Reference number</b><input wire:model="referenceNumber" placeholder="e.g. REF-00028 or customer PO number">@error('referenceNumber')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-create-field ft-repeat-order-option">
                    <b>Repeated order</b>
                    <label class="ft-repeat-order-check">
                        <input type="checkbox" wire:model.live="isRepeatedOrder">
                        <span>Is this a repeated order?</span>
                    </label>
                    @error('isRepeatedOrder')<small class="validation-error">{{ $message }}</small>@enderror
                </div>
                @if($isRepeatedOrder)
                    <label class="ft-create-field" wire:key="repeated-order-number-field">
                        <b>Previous reference number *</b>
                        <input wire:model="repeatedOrderNumber" placeholder="Enter the previous order reference number">
                        @error('repeatedOrderNumber')<small class="validation-error">{{ $message }}</small>@enderror
                    </label>
                @endif
                <label class="ft-create-field"><b>Order title *</b><input wire:model="jobTitle" placeholder="e.g. Conference merchandise order">@error('jobTitle')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-create-field ft-mention-host"><b>Request description</b><textarea class="ft-mention-input" data-rich-text wire:model="description" rows="4" autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}" placeholder="Type @ to mention a user. Add specifications, target price or customization requirements..."></textarea>@error('description')<small class="validation-error">{{ $message }}</small>@enderror</div>
            </div>
        </section>

        @include('components.jobs.create-products')

        @if($assignmentReady)
        <section class="ft-create-section" wire:key="create-assignment-ready">
            <div class="ft-create-section-title"><span>3</span><h2>Schedule & owner</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.deliveryDate?.showPicker?.(); $refs.deliveryDate?.focus(); }"><b>Customer required delivery date</b><input x-ref="deliveryDate" type="date" wire:model="deliveryDate">@error('deliveryDate')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.estimatedDeliveryDate?.showPicker?.(); $refs.estimatedDeliveryDate?.focus(); }"><b>Estimated Delivery date</b><input x-ref="estimatedDeliveryDate" type="date" wire:model="estimatedDeliveryDate">@error('estimatedDeliveryDate')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-create-urgency-grid">
                    <div class="ft-create-field ft-create-urgency-field">
                        <b>Select order production urgency</b>
                        <div class="ft-create-urgency-control" role="group" aria-label="Select order production urgency">
                            @forelse($productionUrgencies as $urgency)
                                <label class="ft-create-urgency-check" wire:key="production-urgency-{{ $urgency->id }}">
                                    <input type="checkbox" value="{{ $urgency->id }}" wire:model="productionUrgencyIds">
                                    <span>{{ $urgency->name }}</span>
                                </label>
                            @empty
                                <small>No active Production Urgency options in Master Data.</small>
                            @endforelse
                        </div>
                        @error('productionUrgencyIds')<small class="validation-error">{{ $message }}</small>@enderror
                        @error('productionUrgencyIds.*')<small class="validation-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="ft-create-field ft-create-urgency-field">
                        <b>Select order shipment urgency</b>
                        <div class="ft-create-urgency-control" role="group" aria-label="Select order shipment urgency">
                            @forelse($shipmentUrgencies as $urgency)
                                <label class="ft-create-urgency-check" wire:key="shipment-urgency-{{ $urgency->id }}">
                                    <input type="checkbox" value="{{ $urgency->id }}" wire:model="shipmentUrgencyIds">
                                    <span>{{ $urgency->name }}</span>
                                </label>
                            @empty
                                <small>No active Shipment Urgency options in Master Data.</small>
                            @endforelse
                        </div>
                        @error('shipmentUrgencyIds')<small class="validation-error">{{ $message }}</small>@enderror
                        @error('shipmentUrgencyIds.*')<small class="validation-error">{{ $message }}</small>@enderror
                    </div>
                </div>
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
            <x-jobs.create-section-placeholder number="3" title="Schedule & owner" section="assignment" :rows="5" />
        @endif

        @if($workflowReady)
            <x-ui.create-workflow-picker
                class="ft-create-section"
                step="4"
                title="What happens next"
                :workflow-options="$workflowFilterOptions"
                :selected-workflow-id="$workflowId"
                :selected-workflow-name="$selectedWorkflow?->name ?? 'Select workflow'"
                :phase-count="$selectedWorkflow?->phases?->where('is_active', true)->count() ?? 0"
                :task-count="$taskCount"
                selection-property="workflowId"
                option-fallback="Order workflow"
                footnote="Tasks are created when you select Create order. Workflow and starting phase are fixed after creation."
                :preview-allowed="auth()->user()->canAccess('workflow.view')"
                error-field="workflowId"
                :start-phases="$allowedPhases"
                :start-phase-id="$workflowPhaseId"
                start-phase-property="workflowPhaseId"
                start-phase-error-field="workflowPhaseId"
                wire:key="create-order-workflow-picker-{{ $clientId ?: 'none' }}-{{ $workflowSelectorVersion }}"
            />
        @else
            <x-jobs.create-section-placeholder number="4" title="What happens next" section="workflow" :rows="2" />
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
