@props(['clients','workflows','users','categories','products','priorities','clientId','workflowId','jobItems','jobAttachments'])
@php
    $selectedClient = $clients->firstWhere('id', (int)$clientId);
    $selectedWorkflow = $workflows->firstWhere('id', (int)$workflowId);
    $allowedPhases = $selectedWorkflow?->phases?->where('allow_job_start', true) ?? collect();
    $taskCount = $selectedWorkflow?->phases?->sum(fn($phase) => $phase->taskPack?->templates?->count() ?? 0) ?? 0;
    $totalUnits = collect($jobItems)->sum(fn($item)=>(int)($item['quantity'] ?? 0));
@endphp
<div {{ $attributes->class('ft-create-job-page') }}>
    <div class="ft-create-shell">
        <div class="ft-create-breadcrumb">Jobs / Create job</div>
        <div class="ft-create-title"><h1>Create new job</h1><p>Set the job scope, products, ownership and workflow.</p></div>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>1</span><h2>Job basics</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field"><b>Job code</b><div class="ft-locked-input">Generated automatically <span>♙</span></div></label>
                <label class="ft-create-field"><b>Client *</b><select wire:model.live="clientId">@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>@error('clientId')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field"><b>Client contact</b><input value="{{ $selectedClient?->contact_name ?? 'No contact recorded' }}" readonly></label>
                <label class="ft-create-field"><b>Job title *</b><input wire:model="jobTitle" placeholder="e.g. Conference merchandise order">@error('jobTitle')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field"><b>Request description</b><textarea wire:model="description" rows="4" placeholder="Specifications, target price, printing or customization requirements..."></textarea></label>
            </div>
        </section>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>2</span><h2>Products & quantities</h2></div>
            <div class="ft-product-rows">
                @foreach($jobItems as $index => $item)
                    @php
                        $selectedCategory = $item['category'] ?? '';
                        $filteredProducts = $products->filter(function($product) use ($selectedCategory) {
                            if(!$selectedCategory) return false;
                            $haystack = strtolower(($product->description ?? '').' '.$product->name);
                            return str_contains($haystack, strtolower($selectedCategory));
                        });
                        if($selectedCategory && $filteredProducts->isEmpty()) $filteredProducts = $products;
                    @endphp
                    <div class="ft-product-row" wire:key="job-product-{{ $index }}">
                        <label><b>Product category</b><select wire:model.live="jobItems.{{ $index }}.category"><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->name }}">{{ $category->name }}</option>@endforeach</select>@error("jobItems.$index.category")<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <label><b>Product</b><select wire:model="jobItems.{{ $index }}.product" @disabled(empty($selectedCategory))><option value="">{{ $selectedCategory ? 'Select product' : 'Select category first' }}</option>@foreach($filteredProducts as $product)<option value="{{ $product->name }}">{{ $product->name }}</option>@endforeach</select>@error("jobItems.$index.product")<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <label class="ft-qty-field"><b>Quantity</b><input type="number" min="1" wire:model.live="jobItems.{{ $index }}.quantity">@error("jobItems.$index.quantity")<small class="validation-error">{{ $message }}</small>@enderror</label>
                        <button type="button" class="ft-product-delete" wire:click="removeProductRow({{ $index }})" @disabled(count($jobItems) <= 1) title="Remove product">▱</button>
                    </div>
                @endforeach
            </div>
            <div class="ft-product-row-footer"><button type="button" wire:click="addProductRow">＋ Add product</button><span>{{ count($jobItems) }} {{ \Illuminate\Support\Str::plural('product',count($jobItems)) }} · {{ number_format($totalUnits) }} total units</span></div>
        </section>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>3</span><h2>Schedule & owner</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field ft-clickable-date-field" x-data x-on:click="if (!$event.target.closest('.validation-error')) { $refs.deliveryDate?.showPicker?.(); $refs.deliveryDate?.focus(); }"><b>Required delivery date *</b><input x-ref="deliveryDate" type="date" wire:model="deliveryDate">@error('deliveryDate')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <label class="ft-create-field"><b>Priority</b><select wire:model="priority">@foreach($priorities as $priority)<option value="{{ $priority->name }}">{{ $priority->name }}</option>@endforeach</select></label>
                <label class="ft-create-field"><b>Job owner *</b><select wire:model="ownerId">@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><small>Accountable for overall delivery.</small>@error('ownerId')<small class="validation-error">{{ $message }}</small>@enderror</label>
            </div>
        </section>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>4</span><h2>Workflow</h2></div>
            <div class="ft-create-fields">
                <label class="ft-create-field"><b>Workflow</b><select wire:model.live="workflowId">@foreach($workflows as $workflow)<option value="{{ $workflow->id }}">{{ $workflow->name }}</option>@endforeach</select></label>
                <label class="ft-create-field"><b>Starting phase</b><select wire:model.live="workflowPhaseId">@foreach($allowedPhases as $phase)<option value="{{ $phase->id }}">{{ $phase->sequence }}. {{ $phase->name }}</option>@endforeach</select>@error('workflowPhaseId')<small class="validation-error">{{ $message }}</small>@enderror</label>
                <div class="ft-workflow-summary"><span>ⓘ {{ $selectedWorkflow?->phases?->count() ?? 0 }} phases · {{ $taskCount }} tasks will be created</span>@if(auth()->user()->canAccess('workflow.manage'))<a href="{{ route('workflow.setup') }}" wire:navigate>Preview workflow ↗</a>@else<span>Preview workflow ↗</span>@endif</div>
                <p class="ft-create-note">Workflow and starting phase are fixed after creation; transitions are managed from the Workflow tab.</p>
            </div>
        </section>

        <section class="ft-create-section">
            <div class="ft-create-section-title"><span>5</span><h2>Attachments</h2></div>
            @if(auth()->user()->canModule('documents','create'))
                <div class="ft-create-upload-wrap">
                <div class="ft-create-upload">
                    <span class="ft-create-paperclip">⌕</span>
                    <div><b>Drop files here or <label for="job-create-files">browse</label></b><small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    @if(auth()->user()->canModule('documents','view'))<a href="{{ route('documents.index') }}" wire:navigate>Open Documents</a>@endif
                    <input id="job-create-files" type="file" wire:model="jobAttachments" multiple hidden accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                </div>
                </div>
            @else
                <div class="ft-create-note">Your role does not allow document uploads during Job creation.</div>
            @endif
            @if(count($jobAttachments))<div class="ft-create-upload-list">@foreach($jobAttachments as $file)<span>{{ $file->getClientOriginalName() }}</span>@endforeach</div>@endif
            @error('jobAttachments.*')<small class="validation-error">{{ $message }}</small>@enderror
        </section>

        <div class="ft-create-actions">
            <button type="button" class="ft-create-cancel" wire:click="closeCreate">Cancel</button>
            <button type="button" class="ft-create-draft" wire:click="saveDraft">Save draft</button>
            <button type="button" class="ft-create-primary" wire:click="createJob">Create job</button>
        </div>
    </div>
</div>
