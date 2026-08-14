<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'mentionUsers'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'jobTaskSearch'=>'','activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'jobDocumentUploads'=>[],'overviewTaskDocumentModalTask'=>null,'overviewTaskAvailableDocuments'=>collect(),'showOverviewTaskDocumentModal'=>false,'overviewTaskDocumentSource'=>'upload','overviewTaskDocumentUpload'=>null,'overviewTaskExistingDocumentId'=>null,'overviewTaskLinkFormTaskId'=>null,'showAddOrderTaskForm'=>false,'newOrderTaskAssigneeId'=>null]));

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

foreach (array_filter((['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'mentionUsers'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'jobTaskSearch'=>'','activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'jobDocumentUploads'=>[],'overviewTaskDocumentModalTask'=>null,'overviewTaskAvailableDocuments'=>collect(),'showOverviewTaskDocumentModal'=>false,'overviewTaskDocumentSource'=>'upload','overviewTaskDocumentUpload'=>null,'overviewTaskExistingDocumentId'=>null,'overviewTaskLinkFormTaskId'=>null,'showAddOrderTaskForm'=>false,'newOrderTaskAssigneeId'=>null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $productRows = \App\Support\JobDetailPresenter::products($job);
    $completedProductRows = $productRows->filter(fn ($item) => filled($item->product_name ?? null));
    $nextTask = \App\Support\JobDetailPresenter::nextTask($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditJob = $accessControl->canEditVisibleJob(auth()->user(), $job);
    $canViewOrderProducts = $accessControl->can(auth()->user(), 'catalog_products', 'view');
    $canEditOrderProducts = $canEditJob && $canViewOrderProducts && $accessControl->can(auth()->user(), 'catalog_products', 'edit');
    $canCreateOrderProducts = $canEditJob && $canViewOrderProducts && $accessControl->can(auth()->user(), 'catalog_products', 'create');
    $canDeleteOrderProducts = $canEditJob && $canViewOrderProducts && $accessControl->can(auth()->user(), 'catalog_products', 'delete');
    $canChangeJobOwner = $accessControl->isAdministrator(auth()->user());
    $canAddOrderTask = $accessControl->canCreateJobTask(auth()->user(), $job)
        && !$job->completed_at
        && $job->status !== 'Completed'
        && !in_array($job->status, \App\Services\JobService::INACTIVE_STATUSES, true);
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $canUploadDocument = $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $accessControl->can(auth()->user(), 'documents', 'link');
    $requiredDocuments = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $configuredTasks = $job->workflow->phases->flatMap(fn($phase) => \App\Support\JobDetailPresenter::phaseTasks($job,$phase))->values();
    $masterData = app(\App\Services\MasterDataService::class);
    $jobPriorityColor = $masterData->displayColorFor('priority', (string) $job->priority);
?>
<div class="ft-job-overview-section ft-exact-overview">
    <div class="ft-overview-metrics">
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">▣</span><div><small>Current phase</small><b><?php echo e($job->phase?->name); ?> · Phase <?php echo e($job->phase?->sequence); ?> of <?php echo e($job->workflow->phases->count()); ?></b><p><?php echo e($currentTasks->count()); ?> tasks · <?php echo e($done); ?> of <?php echo e($currentTasks->count()); ?> complete</p></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">↗</span><div><small>Overall progress</small><b><?php echo e($job->progress); ?>%</b><div class="ft-line-progress"><span style="width:<?php echo e($job->progress); ?>%"></span></div></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">⌘</span><div><small>Next required action</small><b><?php echo e($nextTask?->title ?? ($job->next_action ?: 'Review client requirement')); ?></b><p><?php echo e($nextTask?->assignee?->name ?? $job->coordinator?->name ?? 'Unassigned'); ?></p></div></div>
    </div>

    <div class="ft-overview-top-grid">
        <section class="ft-detail-card ft-overview-card">
            <h2>Order overview</h2>
            <div
                class="ft-editable-copy ft-editable-description ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-description')->toHtml() ?>, label: 'Order description', value: <?php echo \Illuminate\Support\Js::from($job->description ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->description ?: 'No order description recorded.')->toHtml() ?> })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <div class="ft-edit-display-row" x-show="!editing">
                    <div class="ft-rich-text-content ft-editable-rich-display">
                        <div x-show="!hasRichTextOverride"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->description): ?><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $job->description]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->description)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $attributes = $__attributesOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $component = $__componentOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__componentOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?><?php else: ?> No order description recorded. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div x-cloak x-show="hasRichTextOverride" x-html="richTextOverrideHtml"></div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                        <button type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit order description" title="Edit" x-on:click.stop="beginRichTextEdit($refs.descriptionEditor)">✎</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                    <div x-cloak x-show="editing" class="ft-inline-description-editor">
                        <textarea x-ref="descriptionEditor" rows="3" class="ft-mention-input" data-rich-text autocomplete="off" data-mention-users="<?php echo e($mentionUsers->toJson()); ?>"><?php echo e($job->description ?? ''); ?></textarea>
                        <div class="ft-inline-description-actions">
                            <button type="button" class="ft-outline-btn" x-on:click="cancelRichTextEdit($refs.descriptionEditor)">Cancel</button>
                            <button type="button" class="ft-new-job-btn" data-rich-text-submit :disabled="status === 'saving'" x-on:click="saveRichText($refs.descriptionEditor, 'No order description recorded.', (clean) => $wire.updateJobTextField(<?php echo e($job->id); ?>, 'description', clean))">Save</button>
                        </div>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewOrderProducts): ?>
            <div class="ft-card-section-head"><b>Products &amp; quantities</b><span><?php echo e($completedProductRows->count()); ?> product · <?php echo e(number_format($completedProductRows->sum('quantity'))); ?> total units</span></div>
            <table class="ft-mini-grid-table ft-inline-product-table">
                <thead><tr><th>Category</th><th>Product</th><th>Quantity</th><th class="ft-product-delete-column"><span class="sr-only">Action</span></th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isDraftItem = filled($item->id) && blank($item->product_name);
                        $categoryNeedsSelection = filled($item->id) && blank($item->category_name);
                        $productNeedsSelection = filled($item->id) && filled($item->category_name) && blank($item->product_name);
                        $categoryLabel = $item->category_name ?: 'Select category';
                        $productLabel = $item->product_name ?: (blank($item->category_name) ? 'Select category first' : 'Select product');
                        $productPickerKey = 'job-item-'.$item->id.'-product-'.md5((string) ($item->category_name ?? '').'|'.(string) ($item->product_name ?? ''));
                    ?>
                    <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-item-'.e($item->id ?? $loop->index).''; ?>wire:key="job-item-<?php echo e($item->id ?? $loop->index); ?>" x-data="{ categorySaving: false, productSaving: false, quantitySaving: false, draftProductReady: <?php echo \Illuminate\Support\Js::from(filled($item->product_name))->toHtml() ?> }" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-inline-product-draft-row' => $isDraftItem]); ?>">
                        <td data-label="Category">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id): ?>
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-item-'.e($item->id).'-category-'.e(md5((string) ($item->category_name ?? ''))).''; ?>wire:key="job-item-<?php echo e($item->id); ?>-category-<?php echo e(md5((string) ($item->category_name ?? ''))); ?>"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-item-'.$item->id.'-category')->toHtml() ?>, label: 'product category', value: <?php echo \Illuminate\Support\Js::from($item->category_name ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($categoryLabel)->toHtml() ?> })"
                                    x-init="if (<?php echo \Illuminate\Support\Js::from($canEditOrderProducts && $categoryNeedsSelection)->toHtml() ?>) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing && !<?php echo \Illuminate\Support\Js::from($categoryNeedsSelection)->toHtml() ?>) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="if (!<?php echo \Illuminate\Support\Js::from($categoryNeedsSelection)->toHtml() ?>) cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select category'); const changed = nextValue !== savedValue; categorySaving = true; commit(nextValue, nextLabel, () => $wire.updateJobItem(<?php echo e($item->id); ?>, 'category_name', nextValue)).then(async (ok) => { if (ok && changed) await $wire.$refresh(); categorySaving = false })"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display"><?php echo e($categoryLabel); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditOrderProducts): ?>
                                        <button x-show="!editing" :disabled="status === 'saving' || productSaving || quantitySaving" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                            <?php if (isset($component)) { $__componentOriginalbe44f191c92266098874e73cf7cdcd43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe44f191c92266098874e73cf7cdcd43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-catalog','data' => ['type' => 'product-categories','value' => $item->category_name ?? '','selectedLabel' => $categoryLabel,'placeholder' => 'Select category','searchLabel' => 'product category','menuWidth' => 320]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-catalog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'product-categories','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->category_name ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryLabel),'placeholder' => 'Select category','search-label' => 'product category','menu-width' => 320]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $attributes = $__attributesOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $component = $__componentOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__componentOriginalbe44f191c92266098874e73cf7cdcd43); ?>
<?php endif; ?>
                                        </div>
                                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php echo e($item->category_name); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td data-label="Product">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id): ?>
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($productPickerKey).''; ?>wire:key="<?php echo e($productPickerKey); ?>"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-item-'.$item->id.'-product')->toHtml() ?>, label: 'product', value: <?php echo \Illuminate\Support\Js::from($item->product_name ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($productLabel)->toHtml() ?> })"
                                    x-init="if (<?php echo \Illuminate\Support\Js::from($canEditOrderProducts && $productNeedsSelection)->toHtml() ?>) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing && !<?php echo \Illuminate\Support\Js::from($productNeedsSelection)->toHtml() ?>) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="if (!<?php echo \Illuminate\Support\Js::from($productNeedsSelection)->toHtml() ?>) cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select product'); productSaving = true; commit(nextValue, nextLabel, () => $wire.updateJobItem(<?php echo e($item->id); ?>, 'product_name', nextValue)).then((ok) => { productSaving = false; if (ok) { draftProductReady = true; $nextTick(() => setTimeout(() => { const input = $el.closest('tr')?.querySelector('[data-job-item-quantity]'); input?.focus(); input?.select(); }, 0)) } })"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display"><?php echo e($productLabel); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditOrderProducts): ?>
                                        <button x-show="!editing" :disabled="status === 'saving' || categorySaving || quantitySaving || <?php echo \Illuminate\Support\Js::from(blank($item->category_name))->toHtml() ?>" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="<?php echo e(blank($item->category_name) ? 'Select a category first' : 'Edit product'); ?>" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                            <?php if (isset($component)) { $__componentOriginalbe44f191c92266098874e73cf7cdcd43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe44f191c92266098874e73cf7cdcd43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-catalog','data' => ['type' => 'products','value' => $item->product_name ?? '','selectedLabel' => $productLabel,'placeholder' => blank($item->category_name) ? 'Select category first' : 'Select product','searchLabel' => 'product','params' => ['category' => (string) ($item->category_name ?? '')],'disabled' => blank($item->category_name),'menuWidth' => 340]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-catalog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'products','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->product_name ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productLabel),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(blank($item->category_name) ? 'Select category first' : 'Select product'),'search-label' => 'product','params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['category' => (string) ($item->category_name ?? '')]),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(blank($item->category_name)),'menu-width' => 340]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $attributes = $__attributesOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $component = $__componentOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__componentOriginalbe44f191c92266098874e73cf7cdcd43); ?>
<?php endif; ?>
                                        </div>
                                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php echo e($item->product_name); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="ft-product-quantity-cell" data-label="Quantity">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id): ?>
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell ft-inline-product-quantity-editor"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-item-'.$item->id.'-quantity')->toHtml() ?>, label: 'quantity', value: <?php echo \Illuminate\Support\Js::from((string) $item->quantity)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from(number_format((int)$item->quantity))->toHtml() ?> })"
                                    <?php if($canEditOrderProducts && $isDraftItem): ?> x-init="editing = true" <?php endif; ?>
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span x-show="!editing" class="ft-inline-field-value" x-text="display"><?php echo e(number_format((int)$item->quantity)); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditOrderProducts): ?>
                                        <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving" type="button" class="ft-inline-edit-button" title="Edit quantity" aria-label="Edit product quantity" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.quantityInput.focus(); $refs.quantityInput.select(); })">✎</button>
                                        <input x-ref="quantityInput" data-job-item-quantity x-cloak x-show="editing" x-model="draftValue" class="ft-inline-cell-input quantity" type="number" min="1" :disabled="categorySaving || productSaving"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:keydown.enter.prevent="$event.target.blur()"
                                            x-on:blur="if (editing && !categorySaving && !productSaving && !quantitySaving) { const next = positiveInteger(draftValue); if (next !== value) { quantitySaving = true; commit(next, numberLabel(next), () => $wire.updateJobItem(<?php echo e($item->id); ?>, 'quantity', next)).then(async (ok) => { quantitySaving = false; if (ok && <?php echo \Illuminate\Support\Js::from($isDraftItem)->toHtml() ?>) await $wire.$refresh(); else if (!ok) editing = true }) } else { editing = false; if (<?php echo \Illuminate\Support\Js::from($isDraftItem)->toHtml() ?> && draftProductReady) $wire.$refresh() } }">
                                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php echo e(number_format((int)$item->quantity)); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="ft-product-delete-cell" data-label="Action">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id && $canDeleteOrderProducts): ?>
                                <button
                                    type="button"
                                    class="ft-inline-product-delete"
                                    title="Remove product"
                                    aria-label="Remove product"
                                    wire:click.stop="removeJobItem(<?php echo e($item->id); ?>)"
                                    wire:confirm="Remove this product from the Order?"
                                    wire:loading.attr="disabled"
                                    wire:target="removeJobItem(<?php echo e($item->id); ?>)"
                                    :disabled="categorySaving || productSaving || quantitySaving"
                                >×</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateOrderProducts): ?>
                <div class="ft-product-actions"><button class="ft-link-blue ft-add-product-inline" type="button" wire:click="addJobItem(<?php echo e($job->id); ?>)" wire:loading.attr="disabled" wire:target="addJobItem(<?php echo e($job->id); ?>)">＋ Add product</button></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <aside class="ft-detail-card ft-side-panel ft-planning-panel">
            <h2>Planning &amp; ownership</h2>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-delivery-date')->toHtml() ?>, label: 'delivery date', value: <?php echo \Illuminate\Support\Js::from($job->delivery_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->delivery_date?->format('M j, Y') ?? 'Not set')->toHtml() ?> })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Required delivery</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" x-text="display"><?php echo e($job->delivery_date?->format('M j, Y') ?? 'Not set'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit required delivery" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.deliveryInput.showPicker ? $refs.deliveryInput.showPicker() : $refs.deliveryInput.focus())">✎</button>
                        <input x-ref="deliveryInput" x-cloak x-show="editing" x-model="draftValue" type="date"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:blur="if (editing) cancelEdit()"
                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateJobDeliveryDate(<?php echo e($job->id); ?>, draftValue))">
                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="{ ...window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-priority')->toHtml() ?>, label: 'priority', value: <?php echo \Illuminate\Support\Js::from($job->priority)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->priority)->toHtml() ?> }), priorityColor: <?php echo \Illuminate\Support\Js::from($jobPriorityColor)->toHtml() ?> }"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Priority</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-master-priority-display"><i class="ft-master-color-dot" style="<?php echo e(\App\Support\MasterColor::style($jobPriorityColor)); ?>" x-bind:style="priorityColor ? '--ft-master-color:'+priorityColor : ''"></i><span x-text="display"><?php echo e($job->priority); ?></span></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit priority" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.prioritySelect.focus())">✎</button>
                        <select data-master-color-select x-ref="prioritySelect" x-cloak x-show="editing" x-model="draftValue" class="ft-master-color" style="<?php echo e(\App\Support\MasterColor::style($jobPriorityColor)); ?>" x-on:keydown.escape.prevent="cancelEdit()" x-on:blur="if (editing) cancelEdit()" x-on:change="const nextColor=String($event.target.selectedOptions[0]?.dataset?.color || ''); window.FlowTrackMasterColor?.applySelect($event.target); commit($event.target.value, selectedLabel($event), () => $wire.updateJobPriority(<?php echo e($job->id); ?>, draftValue)).then(ok => { if(ok) priorityColor=nextColor; });">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>" data-color="<?php echo e($masterData->displayColorFor('priority', $priority->name)); ?>"><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-owner')->toHtml() ?>, label: 'Order owner', value: <?php echo \Illuminate\Support\Js::from($job->owner_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->owner?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($job->owner?->profileImageUrl() ?? '')->toHtml() ?> })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                x-on:click.outside="if (editing) cancelEdit()"
                x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateJobOwner(<?php echo e($job->id); ?>, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
            >
                <span>Order owner</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-inline-person-live ft-planning-person-value">
                        <?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 24]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
                        <span x-text="display"><?php echo e($job->owner?->name ?? 'Unassigned'); ?></span>
                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeJobOwner): ?>
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit job owner" title="Edit" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                        <div x-cloak x-show="editing" class="ft-task-inline-assignee-picker">
                            <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $job->owner_id ?? '','selectedLabel' => $job->owner?->name ?? 'Unassigned','context' => 'job-owner','parentType' => 'job','parentId' => $job->id,'searchPlaceholder' => 'Search owner…','triggerClass' => 'ft-planning-inline-select','variant' => 'compact','menuWidth' => 300]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner?->name ?? 'Unassigned'),'context' => 'job-owner','parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'search-placeholder' => 'Search owner…','trigger-class' => 'ft-planning-inline-select','variant' => 'compact','menu-width' => 300]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div class="ft-side-row"><span>Workflow</span><b>▣ <?php echo e($job->workflow?->name); ?></b></div>
            <div class="ft-side-row"><span>Created</span><b><?php echo e(\App\Support\UserLocalTime::format($job->created_at, 'M j, Y, g:i A')); ?></b></div>
        </aside>
    </div>

    <section class="ft-workflow-mini-line ft-overview-workflow-line">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button type="button" class="<?php echo e($phase->sequence < $job->phase->sequence ? 'done' : ($phase->id === $job->phase->id ? 'current' : '')); ?>" disabled aria-disabled="true" title="Workflow page is temporarily disabled">
                <span><?php echo e($phase->sequence < $job->phase->sequence ? '✓' : $phase->sequence); ?></span><small><?php echo e($phase->short_name); ?></small>
            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </section>

    <section class="ft-detail-card ft-phase-table-card ft-overview-task-card">
        <div class="ft-card-row-head ft-task-card-heading">
            <div><h2>All phase tasks</h2><p><?php echo e($configuredTasks->count()); ?> tasks across <?php echo e($job->workflow->phases->count()); ?> phases</p></div>
            <div class="ft-row-actions ft-order-taskflow-controls" aria-label="Order taskflow controls">
                <span class="ft-order-task-count-pill"><?php echo e($configuredTasks->count()); ?> Tasks</span>
                <span class="ft-order-taskflow-badge">Taskflow</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAddOrderTask): ?>
                    <button type="button" class="ft-order-add-task-button" wire:click="openAddOrderTaskForm">＋ Add Task</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="ft-phase-toolbar-icons" aria-label="Phase task controls">
                    <button type="button" class="ft-phase-toolbar-icon" wire:click="expandAllJobPhases" title="Expand all phases" aria-label="Expand all phases">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg>
                    </button>
                    <button type="button" class="ft-phase-toolbar-icon" wire:click="collapseAllJobPhases" title="Collapse all phases" aria-label="Collapse all phases">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="ft-phase-load-note"><span>◉ All <?php echo e($configuredTasks->count()); ?> configured and added tasks are loaded</span><span>Task status changes save automatically</span></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddOrderTaskForm && $canAddOrderTask): ?>
            <div class="ft-order-add-task" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-add-task-form'; ?>wire:key="order-add-task-form">
                <div class="ft-order-add-task-head">
                    <div>
                        <strong>Add taskflow task</strong>
                        <span>The task will be added to the current phase<?php echo e($job->phase?->name ? ': '.$job->phase->name : ''); ?>.</span>
                    </div>
                    <button class="ft-order-add-task-close" type="button" wire:click="cancelAddOrderTask" aria-label="Close add task form">×</button>
                </div>
                <div class="ft-order-add-task-grid">
                    <label class="ft-order-add-task-field ft-order-add-task-field-wide">
                        <span>Task name *</span>
                        <input type="text" wire:model="newOrderTaskName" placeholder="Task name" maxlength="255">
                    </label>
                    <?php
                        $newOrderTaskAssignee = $users->firstWhere('id', $newOrderTaskAssigneeId);
                    ?>
                    <div
                        class="ft-order-add-task-field ft-order-add-task-assignee"
                        x-data
                        x-on:ft-inline-remote-selected.stop="const raw = String($event.detail?.value ?? ''); $wire.$set('newOrderTaskAssigneeId', raw === '' ? null : Number(raw));"
                    >
                        <span>Assignee</span>
                        <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $newOrderTaskAssigneeId ?? '','selectedLabel' => $newOrderTaskAssignee?->name ?? 'Unassigned','context' => 'task-assignee','parentType' => 'job','parentId' => $job->id,'searchPlaceholder' => 'Search assignee…','triggerClass' => 'ft-order-add-task-assignee-trigger','variant' => 'compact','menuWidth' => 320,'wire:key' => 'order-add-task-assignee-'.e($job->id).'-'.e($newOrderTaskAssigneeId ?? 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newOrderTaskAssigneeId ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newOrderTaskAssignee?->name ?? 'Unassigned'),'context' => 'task-assignee','parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'search-placeholder' => 'Search assignee…','trigger-class' => 'ft-order-add-task-assignee-trigger','variant' => 'compact','menu-width' => 320,'wire:key' => 'order-add-task-assignee-'.e($job->id).'-'.e($newOrderTaskAssigneeId ?? 'none').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
                    </div>
                    <label class="ft-order-add-task-field">
                        <span>Due date</span>
                        <input type="date" wire:model="newOrderTaskDueDate" onclick="this.showPicker && this.showPicker()">
                    </label>
                    <label class="ft-order-add-task-field ft-order-add-task-field-description">
                        <span>Instructions</span>
                        <textarea data-rich-text wire:model="newOrderTaskDescription" placeholder="Describe what must be completed for this task or paste screenshots here."></textarea>
                    </label>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newOrderTaskName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-order-add-task-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newOrderTaskAssigneeId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-order-add-task-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newOrderTaskDueDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-order-add-task-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="ft-order-add-task-actions">
                    <button class="ft-outline-btn" type="button" wire:click="cancelAddOrderTask">Cancel</button>
                    <button class="ft-order-add-task-submit" type="button" wire:click="addOrderTask" wire:loading.attr="disabled" wire:target="addOrderTask">
                        <span wire:loading.remove wire:target="addOrderTask">Add Task</span>
                        <span wire:loading wire:target="addOrderTask">Adding…</span>
                    </button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-phase-task-table ft-order-overview-taskflow">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $allPhaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                    $completed = \App\Support\JobDetailPresenter::completedCount($allPhaseTasks);
                    $phaseProgress = $allPhaseTasks->count() ? round($completed/max(1,$allPhaseTasks->count())*100) : 0;
                    $phaseTasks = $allPhaseTasks;
                    $expanded = in_array((int) $phase->id, array_map('intval', $expandedPhaseIds), true);
                    $phaseTone = ((max(1, (int) $phase->sequence) - 1) % 6) + 1;
                ?>
                <div class="ft-phase-group ft-phase-tone-<?php echo e($phaseTone); ?> <?php echo e($expanded ? 'open' : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-phase-'.e($phase->id).''; ?>wire:key="job-phase-<?php echo e($phase->id); ?>">
                    <div class="ft-phase-group-head ft-order-phase-head">
                        <b class="<?php echo e($phase->id === $job->phase->id ? 'current-number' : ''); ?>"><?php echo e($phase->sequence); ?></b>
                        <strong><?php echo e($phase->name); ?></strong>
                        <small><?php echo e($completed); ?> of <?php echo e($allPhaseTasks->count()); ?> complete</small>
                        <em style="--phase-progress:<?php echo e($phaseProgress); ?>%"></em>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expanded): ?>
                        <div class="ft-phase-task-columns"><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Files</span><span>Action</span></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $taskAccess = app(\App\Services\AccessControlService::class);
                                $canEditTask = $taskAccess->canEditVisibleTask(auth()->user(), $task);
                                $canAssignTask = $taskAccess->canAssignTask(auth()->user(), $task);
                                $canDeleteTask = $taskAccess->can(auth()->user(), 'tasks', 'delete');
                                $taskDocuments = $job->documents->where('task_id', $task->id)->sortByDesc('created_at')->values();
                                $taskLinks = $task->relationLoaded('links') ? $task->links : collect();
                                $taskRequirement = $requiredDocuments->first(fn ($requirement) => (int) ($requirement->task?->id ?? 0) === (int) $task->id);
                                $effectiveTaskDescription = $task->description ?: $task->setupTemplate?->description;
                                $taskStripeClass = $loop->odd ? 'is-green' : 'is-white';
                            ?>
                            <div class="ft-phase-task-line ft-editable-task-line ft-order-taskflow-row <?php echo e($taskStripeClass); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-task-'.e($task->id).''; ?>wire:key="job-task-<?php echo e($task->id); ?>">
                                <span class="ft-order-task-number"><?php echo e($phase->sequence); ?>.<?php echo e($loop->iteration); ?></span>
                                <div class="ft-order-task-copy">
                                    <button class="ft-inline-task-link" type="button" wire:click="openTask(<?php echo e($task->id); ?>)"><?php echo e($task->title); ?></button>
                                    <span class="ft-order-task-description"><?php echo e($effectiveTaskDescription ? \Illuminate\Support\Str::limit(strip_tags((string) $effectiveTaskDescription), 110) : 'No instructions added.'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskRequirement): ?>
                                        <span class="ft-order-task-required-file <?php echo e($taskRequirement->complete ? 'is-complete' : ''); ?>"><?php echo e($taskRequirement->complete ? '✓ File submitted' : '□ Required file: '.$taskRequirement->name); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell ft-order-task-assignee-inline"
                                    data-field-label="Assignee"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-assignee')->toHtml() ?>, label: 'task assignee', value: <?php echo \Illuminate\Support\Js::from($task->assignee_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->assignee?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($task->assignee?->profileImageUrl() ?? '')->toHtml() ?> })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeFromJob(<?php echo e($task->id); ?>, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
                                >
                                    <div class="ft-order-inline-display-row" x-show="!editing">
                                        <div class="ft-order-assignee-display">
                                            <span class="ft-inline-avatar-slot"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 28]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?></span>
                                            <span class="ft-order-assignee-name" x-text="display"><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></span>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?>
                                            <button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?>
                                        <div x-cloak x-show="editing" class="ft-order-assignee-picker">
                                            <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task->assignee_id ?? '','parentType' => 'job','parentId' => $job->id,'selectedLabel' => $task->assignee?->name ?? 'Unassigned','triggerClass' => 'ft-order-task-inline-input','variant' => 'compact','menuWidth' => 260]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee_id ?? ''),'parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->id),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee?->name ?? 'Unassigned'),'trigger-class' => 'ft-order-task-inline-input','variant' => 'compact','menu-width' => 260]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
                                        </div>
                                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell ft-order-task-date-inline"
                                    data-field-label="Due date"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->due_date?->format('M j, Y') ?? 'Set due date')->toHtml() ?> })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <div class="ft-order-inline-display-row" x-show="!editing">
                                        <span x-text="display" class="ft-order-inline-value <?php echo e(($task->due_date && \App\Support\UserLocalTime::isDatePast($task->due_date)) && !$task->completed_at ? 'danger-text' : ''); ?>"><?php echo e($task->due_date?->format('M j, Y') ?? 'Set due date'); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                                            <button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskDue.showPicker ? $refs.taskDue.showPicker() : $refs.taskDue.focus())">✎</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                                        <input x-ref="taskDue" x-cloak x-show="editing" x-model="draftValue" class="ft-order-task-inline-input" type="date"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDateFromJob(<?php echo e($task->id); ?>, draftValue))">
                                        <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <span
                                    class="ft-task-inline-status-shell ft-inline-edit-shell"
                                    data-field-label="Status"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('task-'.$task->id.'-status')->toHtml() ?>, label: 'task status', value: <?php echo \Illuminate\Support\Js::from($task->status)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task->status)->toHtml() ?> })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <?php
                                        $taskStatusColor = app(\App\Services\MasterDataService::class)->colorFor('task_status', (string) $task->status);
                                    ?>
                                    <select data-master-color-select class="ft-inline-task-status <?php echo e($taskStatusColor ? 'ft-master-color' : \App\Support\JobDetailPresenter::taskStatusClass($task->status)); ?>" style="<?php echo e(\App\Support\MasterColor::style($taskStatusColor)); ?>" x-model="draftValue"
                                        x-on:change="window.FlowTrackMasterColor?.applySelect($event.target); commit($event.target.value, selectedLabel($event), () => $wire.updateTaskStatusFromJob(<?php echo e($task->id); ?>, draftValue))"
                                        :disabled="status === 'saving'" <?php if(!$canEditTask): echo 'disabled'; endif; ?>>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($status); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('task_status', $status)); ?>"><?php echo e($status); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <div class="ft-order-task-files" data-field-label="Files">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                                        <div class="ft-order-task-resource-add-actions" aria-label="Add task resource">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument || $canLinkDocument): ?>
                                                <button class="ft-order-task-resource-add-icon" type="button" wire:click="openOverviewTaskDocumentModal(<?php echo e($task->id); ?>)" title="Add file" aria-label="Add file to <?php echo e($task->title); ?>">
                                                    <span class="ft-order-task-resource-plus">+</span>
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h6"/></svg>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <button class="ft-order-task-resource-add-icon <?php echo e((int) $overviewTaskLinkFormTaskId === (int) $task->id ? 'is-active' : ''); ?>" type="button" wire:click="openOverviewTaskLinkForm(<?php echo e($task->id); ?>)" title="Add link" aria-label="Add external link to <?php echo e($task->title); ?>">
                                                <span class="ft-order-task-resource-plus">+</span>
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                            </button>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="ft-order-task-file-count"><b><?php echo e($taskDocuments->count()); ?></b> file<?php echo e($taskDocuments->count() === 1 ? '' : 's'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskLinks->isNotEmpty()): ?> · <b><?php echo e($taskLinks->count()); ?></b> link<?php echo e($taskLinks->count() === 1 ? '' : 's'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                                </div>
                                <div class="ft-task-action-wrap" x-data="{ open: false }" x-on:click.stop>
                                    <button class="ft-table-kebab" type="button" x-on:click="open = !open" aria-label="Task actions" :aria-expanded="open ? 'true' : 'false'">•••</button>
                                    <div class="ft-task-action-menu" x-cloak x-show="open" x-on:click.outside="open = false">
                                        <button type="button" x-on:click="open = false" wire:click.stop="viewTask(<?php echo e($task->id); ?>)">View</button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                                            <button type="button" x-on:click="open = false" wire:click.stop="editTask(<?php echo e($task->id); ?>)">Edit</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteTask): ?>
                                            <button type="button" class="danger" x-on:click="open = false" wire:click.stop="deleteTaskFromJob(<?php echo e($task->id); ?>)" wire:confirm="Delete this task? The task will be removed from this Job and its phase progress will be recalculated.">Delete</button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $overviewTaskLinkFormTaskId === (int) $task->id || $taskDocuments->isNotEmpty() || $taskLinks->isNotEmpty()): ?>
                                <div class="ft-order-task-resource-list <?php echo e($taskStripeClass); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-task-resources-'.e($task->id).''; ?>wire:key="job-task-resources-<?php echo e($task->id); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $overviewTaskLinkFormTaskId === (int) $task->id && $canEditTask): ?>
                                        <form class="ft-order-task-link-form" wire:submit.prevent="saveOverviewTaskLink(<?php echo e($task->id); ?>)" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-task-link-form-'.e($task->id).''; ?>wire:key="job-task-link-form-<?php echo e($task->id); ?>">
                                            <div class="ft-order-task-link-input-wrap">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                <input type="text" inputmode="url" wire:model="overviewTaskLinkUrl" placeholder="Paste link, e.g. https://drive.google.com/..." autocomplete="url" autofocus aria-label="External link">
                                            </div>
                                            <div class="ft-order-task-link-form-actions">
                                                <button class="secondary" type="button" wire:click="cancelOverviewTaskLinkForm">Cancel</button>
                                                <button class="primary" type="submit" wire:loading.attr="disabled" wire:target="saveOverviewTaskLink(<?php echo e($task->id); ?>)">Add</button>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskLinkUrl'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-order-task-link-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="ft-order-task-document-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-task-document-'.e($taskDocument->id).''; ?>wire:key="job-task-document-<?php echo e($taskDocument->id); ?>">
                                            <span class="ft-order-task-file-type"><?php echo e(strtoupper(pathinfo($taskDocument->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span>
                                            <div class="ft-order-task-file-copy">
                                                <b title="<?php echo e($taskDocument->name); ?>"><?php echo e($taskDocument->name); ?></b>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocument->note): ?><span class="ft-order-task-file-note"><?php echo e($taskDocument->note); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <small><?php echo e($taskDocument->category ?: 'Task attachment'); ?> · <?php echo e($taskDocument->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($taskDocument->created_at, 'M j, Y, g:i A')); ?></small>
                                            </div>
                                            <div class="ft-order-task-file-actions">
                                                <a href="<?php echo e(route('documents.open', $taskDocument)); ?>" target="_blank" rel="noopener">Open</a>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','export')): ?><a href="<?php echo e(route('documents.download', $taskDocument)); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?>
                                                    <button type="button" wire:click="deleteJobDocument(<?php echo e($taskDocument->id); ?>)" wire:loading.attr="disabled" wire:target="deleteJobDocument(<?php echo e($taskDocument->id); ?>)" wire:confirm="Delete this document link?" title="Remove attachment" aria-label="Remove <?php echo e($taskDocument->name); ?>">×</button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="ft-order-task-link-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-task-link-'.e($taskLink->id).''; ?>wire:key="job-task-link-<?php echo e($taskLink->id); ?>">
                                            <span class="ft-order-task-link-type" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></span>
                                            <div class="ft-order-task-link-copy">
                                                <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo e($taskLink->url); ?>"><?php echo e(\Illuminate\Support\Str::limit($taskLink->url, 110)); ?></a>
                                                <small><?php echo e($taskLink->created_at ? \App\Support\UserLocalTime::format($taskLink->created_at, 'M j, Y, g:i A') : '—'); ?></small>
                                            </div>
                                            <div class="ft-order-task-link-actions">
                                                <a href="<?php echo e($taskLink->url); ?>" target="_blank" rel="noopener noreferrer">Open ↗</a>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?><button type="button" wire:click="deleteOverviewTaskLink(<?php echo e($task->id); ?>, <?php echo e($taskLink->id); ?>)" wire:confirm="Remove this link from the task?" title="Remove link" aria-label="Remove link">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="ft-phase-empty-row">No configured tasks in this phase.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </section>

    <section class="ft-detail-card ft-attachment-card ft-job-overview-attachments">
        <h2>Attachments <span><?php echo e($job->documents->count()); ?></span></h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($requiredDocuments->isNotEmpty()): ?>
            <div class="ft-upload-zone compact ft-task-upload-zone ft-job-overview-dropzone">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                    <label class="ft-task-upload-drop ft-livewire-upload-zone <?php echo e($errors->has('jobDocumentUploads') || $errors->has('jobDocumentUploads.*') ? 'has-upload-error' : ''); ?>" data-file-dropzone data-auto-upload-method="uploadJobOverviewDocuments" for="jobOverviewDocumentUpload-<?php echo e($job->id); ?>">
                        <input id="jobOverviewDocumentUpload-<?php echo e($job->id); ?>" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                        <span class="ft-paperclip">⌕</span>
                        <div>Drop files here or <strong>browse</strong><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    </label>
                <?php else: ?>
                    <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to Job attachments.</small></div></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobDocumentUploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="ft-upload-field-error validation-error" role="alert">
                        <span><?php echo e($message); ?></span>
                        <button type="button" wire:click="clearJobDocumentUploads">Remove failed file</button>
                    </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUploadDocument && count($jobDocumentUploads ?? [])): ?>
                <div class="ft-pending-upload-list" aria-label="Files selected for upload">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobDocumentUploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uploadIndex => $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $uploadName = method_exists($upload, 'getClientOriginalName') ? $upload->getClientOriginalName() : ('File '.($uploadIndex + 1));
                        ?>
                        <div class="ft-pending-upload-item <?php echo e($errors->has('jobDocumentUploads.'.$uploadIndex) ? 'has-error' : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-overview-doc-pending-'.e($job->id).'-'.e($uploadIndex).'-'.e(md5($uploadName)).''; ?>wire:key="job-overview-doc-pending-<?php echo e($job->id); ?>-<?php echo e($uploadIndex); ?>-<?php echo e(md5($uploadName)); ?>">
                            <div class="ft-pending-upload-copy">
                                <b><?php echo e($uploadName); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobDocumentUploads.'.$uploadIndex];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error" role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <button type="button" class="ft-pending-upload-remove" wire:click="removeJobDocumentUpload(<?php echo e($uploadIndex); ?>)">Remove</button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <div class="ft-upload-ready-row ft-auto-upload-state" aria-live="polite">
                    <span>Uploading and linking <?php echo e(count($jobDocumentUploads ?? [])); ?> file<?php echo e(count($jobDocumentUploads ?? [])===1?'':'s'); ?> automatically…</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="ft-empty-taskpack-docs">No Task Pack document requirement is configured for this Job. Open Documents to review the document setup.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="ft-job-file-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-overview-document-'.e($doc->id).''; ?>wire:key="job-overview-document-<?php echo e($doc->id); ?>">
                <span class="ft-file-type"><?php echo e(strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span>
                <div class="ft-job-file-main">
                    <b title="<?php echo e($doc->name); ?>"><?php echo e($doc->name); ?></b>
                    <small><?php echo e($doc->task?->title ?: 'Job document'); ?> · <?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A')); ?></small>
                </div>
                <div class="ft-job-file-actions">
                    <a class="ft-link-blue" href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','export')): ?>
                        <a class="ft-link-blue" href="<?php echo e(route('documents.download',$doc)); ?>">Download</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?>
                        <button type="button" class="ft-job-file-delete" wire:click="deleteJobDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?" title="Remove attachment" aria-label="Remove <?php echo e($doc->name); ?>">×</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </section>

    <?php if (isset($component)) { $__componentOriginal07a0efd5a24a9992f46129ce7eefcae9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07a0efd5a24a9992f46129ce7eefcae9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-activity','data' => ['job' => $job,'mentionUsers' => $mentionUsers,'compact' => 'true','activityTab' => $activityTab,'activityPage' => $activityPage,'focusComment' => $focusComment]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-activity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'compact' => 'true','activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07a0efd5a24a9992f46129ce7eefcae9)): ?>
<?php $attributes = $__attributesOriginal07a0efd5a24a9992f46129ce7eefcae9; ?>
<?php unset($__attributesOriginal07a0efd5a24a9992f46129ce7eefcae9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07a0efd5a24a9992f46129ce7eefcae9)): ?>
<?php $component = $__componentOriginal07a0efd5a24a9992f46129ce7eefcae9; ?>
<?php unset($__componentOriginal07a0efd5a24a9992f46129ce7eefcae9); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showOverviewTaskDocumentModal && $overviewTaskDocumentModalTask): ?>
        <div class="ft-order-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-document-modal'; ?>wire:key="order-task-document-modal" wire:click.self="closeOverviewTaskDocumentModal">
            <section class="ft-order-task-document-modal" role="dialog" aria-modal="true" aria-labelledby="order-task-document-modal-title">
                <header class="ft-order-task-document-modal-head">
                    <div>
                        <h2 id="order-task-document-modal-title">Add new document to task</h2>
                        <p>Upload a new file or choose a document that already exists.</p>
                    </div>
                    <button type="button" class="ft-order-task-document-modal-close" wire:click="closeOverviewTaskDocumentModal" aria-label="Close">×</button>
                </header>

                <div class="ft-order-task-document-modal-body">
                    <div class="ft-order-task-document-target">
                        <span class="ft-order-task-document-target-icon">▣</span>
                        <div>
                            <small>ATTACHING TO</small>
                            <strong><?php echo e($overviewTaskDocumentModalTask->title); ?></strong>
                            <span><?php echo e($overviewTaskDocumentModalTask->task_number ?: 'TASK-'.str_pad((string) $overviewTaskDocumentModalTask->id, 5, '0', STR_PAD_LEFT)); ?> &nbsp;·&nbsp; <?php echo e($overviewTaskDocumentModalTask->phase?->name ?? 'Order Taskflow'); ?></span>
                            <span class="ft-order-task-document-reference"><b>Order Reference:</b> <?php echo e($job->order_number ?: '—'); ?></span>
                        </div>
                        <span class="ft-order-task-document-target-lock">▣&nbsp; Task selected</span>
                    </div>

                    <div class="ft-order-task-document-source-label">Document source</div>
                    <div class="ft-order-task-document-source-tabs">
                        <button type="button" class="<?php echo e($overviewTaskDocumentSource === 'upload' ? 'active' : ''); ?>" wire:click="setOverviewTaskDocumentSource('upload')" <?php if(!$canUploadDocument): echo 'disabled'; endif; ?>><span>↥</span> Upload new</button>
                        <button type="button" class="<?php echo e($overviewTaskDocumentSource === 'existing' ? 'active' : ''); ?>" wire:click="setOverviewTaskDocumentSource('existing')" <?php if(!$canLinkDocument): echo 'disabled'; endif; ?>><span>▤</span> Choose existing</button>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overviewTaskDocumentSource === 'upload' && $canUploadDocument): ?>
                        <label class="ft-order-task-document-dropzone">
                            <input type="file" wire:model="overviewTaskDocumentUpload" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai">
                            <span class="ft-order-task-document-upload-icon">⇧</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overviewTaskDocumentUpload): ?>
                                <strong><?php echo e($overviewTaskDocumentUpload->getClientOriginalName()); ?></strong>
                                <b>File selected — choose another file</b>
                                <small><?php echo e(number_format(max(1, (int) ceil($overviewTaskDocumentUpload->getSize() / 1024)))); ?> KB · ready to add</small>
                            <?php else: ?>
                                <strong>Drop a file here</strong>
                                <b>or browse files</b>
                                <small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskDocumentUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-order-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="ft-order-task-document-existing">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overviewTaskAvailableDocuments->isEmpty()): ?>
                                <div class="ft-order-task-document-existing-empty">No existing client documents are available.</div>
                            <?php else: ?>
                                <label>
                                    <span>Choose an existing document</span>
                                    <select wire:model="overviewTaskExistingDocumentId">
                                        <option value="">Select a document...</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $overviewTaskAvailableDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sourceDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($sourceDocument->id); ?>"><?php echo e($sourceDocument->name); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskExistingDocumentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-order-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <label class="ft-order-task-document-note">
                        <span>Document note (optional)</span>
                        <input type="text" wire:model="overviewTaskDocumentNote" placeholder="Add a short note about this document...">
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['overviewTaskDocumentNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-order-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ft-order-task-document-info">
                        <span>ⓘ</span>
                        <p>This document will appear directly under <strong><?php echo e($overviewTaskDocumentModalTask->title); ?></strong> and in Order Documents.</p>
                    </div>
                </div>

                <footer class="ft-order-task-document-modal-actions">
                    <button type="button" class="secondary" wire:click="closeOverviewTaskDocumentModal">Cancel</button>
                    <button type="button" class="primary" wire:click="saveOverviewTaskDocument" wire:loading.attr="disabled" wire:target="saveOverviewTaskDocument,overviewTaskDocumentUpload"
                        <?php if($overviewTaskDocumentSource === 'upload' ? !$overviewTaskDocumentUpload : !$overviewTaskExistingDocumentId): echo 'disabled'; endif; ?>>
                        <span wire:loading.remove wire:target="saveOverviewTaskDocument">Add document</span>
                        <span wire:loading wire:target="saveOverviewTaskDocument">Adding...</span>
                    </button>
                </footer>
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-overview.blade.php ENDPATH**/ ?>