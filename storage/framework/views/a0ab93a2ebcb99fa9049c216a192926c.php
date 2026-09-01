<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'workspace',
    'canManage' => false,
    'canEditSuppliers' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'workspace',
    'canManage' => false,
    'canEditSuppliers' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rows = collect($workspace['rows'] ?? []);
    $selectedCount = (int) ($workspace['selected_count'] ?? 0);
    $failedCount = (int) ($workspace['failed_count'] ?? 0);
    $visibleSelectableIds = $workspace['selectable_visible_ids'] ?? [];
    $allVisibleSelected = (bool) ($workspace['all_visible_selected'] ?? false);
?>

<section class="ft-rfq-workspace-card" aria-labelledby="rfq-workspace-title">
    <header class="ft-rfq-workspace-head">
        <div class="ft-rfq-workspace-heading">
            <h2 id="rfq-workspace-title">Request for quotation</h2>
            <p>Invite suppliers to submit a quotation through a secure email link.</p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
            <button type="button" class="ft-rfq-add-supplier-btn" wire:click="openRfqSupplierPicker" wire:loading.attr="disabled" wire:target="openRfqSupplierPicker">
                <span wire:loading.remove wire:target="openRfqSupplierPicker">Add supplier</span>
                <span wire:loading wire:target="openRfqSupplierPicker">Opening…</span>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </header>

    <div class="ft-rfq-workspace-toolbar" role="search" aria-label="Filter RFQ suppliers">
        <label class="ft-rfq-workspace-search">
            <span class="sr-only">Search suppliers</span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path></svg>
            <input type="search" wire:model.live.debounce.300ms="rfqTableSearch" placeholder="Search suppliers" autocomplete="off">
        </label>

        <label class="ft-rfq-workspace-filter">
            <span class="sr-only">Email status</span>
            <select wire:model.live="rfqEmailStatusFilter">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($workspace['filter_options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 9.5 5 5 5-5"></path></svg>
        </label>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
        <button type="button" class="ft-rfq-email-settings-btn" wire:click="openRfqSettings">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 8.5 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 8.5a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15.5 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.12.36.33.69.6 1 .3.27.68.42 1.09.4H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51.6Z"></path>
            </svg>
            <span>RFQ settings</span>
        </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedCount > 0): ?>
        <div class="ft-rfq-selection-bar">
            <div class="ft-rfq-selection-copy">
                <span class="ft-rfq-selection-check" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="m7 12 3 3 7-7"></path></svg>
                </span>
                <strong><?php echo e($selectedCount); ?> <?php echo e(\Illuminate\Support\Str::plural('supplier', $selectedCount)); ?> selected</strong>
            </div>
            <div class="ft-rfq-selection-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                    <button type="button" class="ft-rfq-send-selected-btn" wire:click="sendSelectedRfqEmails" wire:loading.attr="disabled" wire:target="sendSelectedRfqEmails">
                        <span wire:loading.remove wire:target="sendSelectedRfqEmails">Send <?php echo e($selectedCount); ?> <?php echo e(\Illuminate\Support\Str::plural('email', $selectedCount)); ?></span>
                        <span wire:loading wire:target="sendSelectedRfqEmails">Sending…</span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" class="ft-rfq-clear-selection-btn" wire:click="clearRfqSelection">Clear selection</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rfqDelivery'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="ft-rfq-workspace-alert is-danger" role="alert" x-data="{ visible: true }" x-show="visible" x-transition.opacity>
            <span class="ft-rfq-workspace-alert-icon" aria-hidden="true">!</span>
            <span><?php echo e($message); ?></span>
            <button type="button" x-on:click="visible = false" aria-label="Dismiss RFQ email error">×</button>
        </div>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($failedCount > 0): ?>
            <div class="ft-rfq-workspace-alert is-danger" role="status" x-data="{ visible: true }" x-show="visible" x-transition.opacity>
                <span class="ft-rfq-workspace-alert-icon" aria-hidden="true">!</span>
                <span><?php echo e($failedCount); ?> <?php echo e(\Illuminate\Support\Str::plural('email', $failedCount)); ?> failed to send. Review the error and retry.</span>
                <button type="button" x-on:click="visible = false" aria-label="Dismiss failed email alert">×</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-rfq-management-table-shell">
        <div class="ft-rfq-management-scroll">
            <table class="ft-rfq-management-table">
                <thead>
                    <tr>
                        <th class="ft-rfq-checkbox-col">
                            <button
                                type="button"
                                class="ft-rfq-master-checkbox <?php echo e($allVisibleSelected ? 'is-checked' : ''); ?>"
                                wire:click='toggleVisibleRfqSelection(<?php echo json_encode($visibleSelectableIds, 15, 512) ?>)'
                                <?php if($visibleSelectableIds === []): echo 'disabled'; endif; ?>
                                aria-label="<?php echo e($allVisibleSelected ? 'Clear visible supplier selection' : 'Select visible suppliers'); ?>"
                                aria-pressed="<?php echo e($allVisibleSelected ? 'true' : 'false'); ?>"
                            >
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allVisibleSelected): ?>
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6.5 12 3.5 3.5 7.5-8"></path></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </button>
                        </th>
                        <th>Supplier</th>
                        <th>Email</th>
                        <th class="ft-rfq-email-status-col">Email status</th>
                        <th>RFQ status</th>
                        <th>Last activity</th>
                        <th class="ft-rfq-actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal8f39057c90681565c4165fe318869019 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f39057c90681565c4165fe318869019 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.rfq-management-row','data' => ['row' => $row,'canManage' => $canManage,'canEditSupplier' => $canEditSuppliers,'selectedIds' => $workspace['selected_ids'] ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.rfq-management-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row),'can-manage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canManage),'can-edit-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditSuppliers),'selected-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workspace['selected_ids'] ?? [])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f39057c90681565c4165fe318869019)): ?>
<?php $attributes = $__attributesOriginal8f39057c90681565c4165fe318869019; ?>
<?php unset($__attributesOriginal8f39057c90681565c4165fe318869019); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f39057c90681565c4165fe318869019)): ?>
<?php $component = $__componentOriginal8f39057c90681565c4165fe318869019; ?>
<?php unset($__componentOriginal8f39057c90681565c4165fe318869019); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7">
                                <div class="ft-rfq-management-empty">
                                    <strong>No suppliers found</strong>
                                    <span>Try another search or email-status filter.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="ft-rfq-management-footer">
            <p>Only suppliers with a configured email can be selected for bulk sending.</p>
            <div class="ft-rfq-pagination">
                <span><?php echo e(number_format((int) ($workspace['first'] ?? 0))); ?>–<?php echo e(number_format((int) ($workspace['last'] ?? 0))); ?> of <?php echo e(number_format((int) ($workspace['total'] ?? 0))); ?></span>
                <button type="button" wire:click="setRfqTablePage(<?php echo e(max(1, (int) ($workspace['current_page'] ?? 1) - 1)); ?>)" <?php if(! ($workspace['has_previous'] ?? false)): echo 'disabled'; endif; ?> aria-label="Previous RFQ supplier page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14.5 6.5-5.5 5.5 5.5 5.5"></path></svg>
                </button>
                <button type="button" wire:click="setRfqTablePage(<?php echo e(min((int) ($workspace['last_page'] ?? 1), (int) ($workspace['current_page'] ?? 1) + 1)); ?>)" <?php if(! ($workspace['has_next'] ?? false)): echo 'disabled'; endif; ?> aria-label="Next RFQ supplier page">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9.5 6.5 5.5 5.5-5.5 5.5"></path></svg>
                </button>
            </div>
        </footer>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/inquiries/rfq-workspace.blade.php ENDPATH**/ ?>