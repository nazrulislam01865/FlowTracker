<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'jobTaskSearch'=>'','activityTab'=>'all']));

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

foreach (array_filter((['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'jobTaskSearch'=>'','activityTab'=>'all']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $productRows = \App\Support\JobDetailPresenter::products($job);
    $nextTask = \App\Support\JobDetailPresenter::nextTask($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditJob = $accessControl->canEditJob(auth()->user(), $job);
    $canAssignJob = $accessControl->canAssignJob(auth()->user(), $job);
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $configuredTasks = $job->workflow->phases->flatMap(fn($phase) => \App\Support\JobDetailPresenter::phaseTasks($job,$phase))->values();
?>
<div class="ft-job-overview-section ft-exact-overview">
    <div class="ft-overview-metrics">
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">▣</span><div><small>Current phase</small><b><?php echo e($job->phase?->name); ?> · Phase <?php echo e($job->phase?->sequence); ?> of <?php echo e($job->workflow->phases->count()); ?></b><p><?php echo e($currentTasks->count()); ?> tasks · <?php echo e($done); ?> of <?php echo e($currentTasks->count()); ?> complete</p></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">↗</span><div><small>Overall progress</small><b><?php echo e($job->progress); ?>%</b><div class="ft-line-progress"><span style="width:<?php echo e($job->progress); ?>%"></span></div></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">⌘</span><div><small>Next required action</small><b><?php echo e($nextTask?->title ?? ($job->next_action ?: 'Review client requirement')); ?></b><p><?php echo e($nextTask?->assignee?->name ?? $job->coordinator?->name ?? 'Unassigned'); ?></p></div></div>
    </div>

    <div class="ft-overview-top-grid">
        <section class="ft-detail-card ft-overview-card">
            <h2>Job overview</h2>
            <div class="ft-editable-copy ft-editable-description" x-data="{ editing:false }">
                <div class="ft-edit-display-row" x-show="!editing">
                    <span><?php echo e($job->description ?: 'No job description recorded.'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                        <button type="button" class="ft-inline-edit-button" aria-label="Edit job description" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.descriptionEditor.focus())">✎</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                    <textarea x-ref="descriptionEditor" x-show="editing" rows="3"
                        x-on:keydown.escape="editing=false"
                        x-on:blur="editing=false"
                        wire:change="updateJobTextField(<?php echo e($job->id); ?>, 'description', $event.target.value)"><?php echo e($job->description); ?></textarea>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-card-section-head"><b>Products &amp; quantities</b><span><?php echo e($productRows->count()); ?> product · <?php echo e(number_format($productRows->sum('quantity'))); ?> total units</span></div>
            <table class="ft-mini-grid-table ft-inline-product-table">
                <thead><tr><th>Category</th><th>Product</th><th>Quantity</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-item-'.e($item->id ?? $loop->index).''; ?>wire:key="job-item-<?php echo e($item->id ?? $loop->index); ?>">
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id): ?>
                                <div class="ft-inline-field-editor" x-data="{ editing:false }">
                                    <span class="ft-inline-field-value" x-show="!editing"><?php echo e($item->category_name ?: 'Uncategorised'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="editing=true; $nextTick(() => $refs.categorySelect.focus())">✎</button>
                                        <select x-ref="categorySelect" x-show="editing" class="ft-inline-cell-input product"
                                            x-on:keydown.escape="editing=false"
                                            x-on:blur="editing=false"
                                            x-on:change="editing=false"
                                            wire:change="updateJobItem(<?php echo e($item->id); ?>, 'category_name', $event.target.value)">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($category->name); ?>" <?php if($item->category_name===$category->name): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php echo e($item->category_name); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id): ?>
                                <div class="ft-inline-field-editor" x-data="{ editing:false }">
                                    <span class="ft-inline-field-value" x-show="!editing"><?php echo e($item->product_name); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.productInput.focus())">✎</button>
                                        <select x-ref="productInput" x-show="editing" class="ft-inline-cell-input product"
                                            x-on:keydown.escape="editing=false"
                                            x-on:blur="editing=false"
                                            x-on:change="editing=false"
                                            wire:change="updateJobItem(<?php echo e($item->id); ?>, 'product_name', $event.target.value)">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($product->name); ?>" <?php if($item->product_name===$product->name): echo 'selected'; endif; ?>><?php echo e($product->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php echo e($item->product_name); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->id): ?>
                                <div class="ft-inline-field-editor" x-data="{ editing:false }">
                                    <span class="ft-inline-field-value" x-show="!editing"><?php echo e(number_format((int)$item->quantity)); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit quantity" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.quantityInput.focus())">✎</button>
                                        <input x-ref="quantityInput" x-show="editing" class="ft-inline-cell-input quantity" type="number" min="1" value="<?php echo e((int)$item->quantity); ?>"
                                            x-on:keydown.escape="editing=false"
                                            x-on:keydown.enter="$event.target.blur()"
                                            x-on:blur="editing=false"
                                            wire:change="updateJobItem(<?php echo e($item->id); ?>, 'quantity', $event.target.value)">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php echo e(number_format((int)$item->quantity)); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                <div class="ft-product-actions"><button class="ft-link-blue ft-add-product-inline" type="button" wire:click="addJobItem(<?php echo e($job->id); ?>)">＋ Add product</button></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <aside class="ft-detail-card ft-side-panel ft-planning-panel">
            <h2>Planning &amp; ownership</h2>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Required delivery</span>
                <b class="ft-planning-value">
                    <span x-show="!editing"><?php echo e($job->delivery_date?->format('M j, Y') ?? 'Not set'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit required delivery" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.deliveryInput.showPicker ? $refs.deliveryInput.showPicker() : $refs.deliveryInput.focus())">✎</button>
                        <input x-ref="deliveryInput" x-show="editing" type="date" value="<?php echo e($job->delivery_date?->format('Y-m-d')); ?>"
                            x-on:keydown.escape="editing=false"
                            x-on:blur="editing=false"
                            wire:change="updateJobDeliveryDate(<?php echo e($job->id); ?>, $event.target.value)">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Priority</span>
                <b class="ft-planning-value">
                    <span x-show="!editing"><?php echo e($job->priority); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit priority" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.prioritySelect.focus())">✎</button>
                        <select x-ref="prioritySelect" x-show="editing" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateJobPriority(<?php echo e($job->id); ?>, $event.target.value)" x-on:change="editing=false">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>" <?php if($job->priority===$priority->name): echo 'selected'; endif; ?>><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Job owner</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-planning-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $job->owner?->name ?? 'Unassigned','size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner?->name ?? 'Unassigned'),'size' => 24]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php echo e($job->owner?->name ?? 'Unassigned'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignJob): ?>
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit job owner" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.ownerSelect.focus())">✎</button>
                        <select x-ref="ownerSelect" x-show="editing" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateJobOwner(<?php echo e($job->id); ?>, $event.target.value)" x-on:change="editing=false">
                            <option value="">Unassigned</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($user->id); ?>" <?php if((int)$job->owner_id===(int)$user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div class="ft-side-row ft-inline-planning-row" x-data="{ editing:false }">
                <span>Coordinator</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-planning-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $job->coordinator?->name ?? 'Unassigned','size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->coordinator?->name ?? 'Unassigned'),'size' => 24]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php echo e($job->coordinator?->name ?? 'Unassigned'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignJob): ?>
                        <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit coordinator" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.coordinatorSelect.focus())">✎</button>
                        <select x-ref="coordinatorSelect" x-show="editing" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateJobCoordinator(<?php echo e($job->id); ?>, $event.target.value)" x-on:change="editing=false">
                            <option value="">Unassigned</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($user->id); ?>" <?php if((int)$job->coordinator_id===(int)$user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </b>
            </div>
            <div class="ft-side-row"><span>Workflow</span><b>▣ <?php echo e($job->workflow?->name); ?></b></div>
            <div class="ft-side-row"><span>Created</span><b><?php echo e($job->created_at?->format('M j, Y, H:i')); ?></b></div>
        </aside>
    </div>

    <section class="ft-workflow-mini-line ft-overview-workflow-line">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button type="button" class="<?php echo e($phase->sequence < $job->phase->sequence ? 'done' : ($phase->id === $job->phase->id ? 'current' : '')); ?>" wire:click="setDetailTab('workflow')">
                <span><?php echo e($phase->sequence < $job->phase->sequence ? '✓' : $phase->sequence); ?></span><small><?php echo e($phase->short_name); ?></small>
            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </section>

    <section class="ft-detail-card ft-phase-table-card ft-overview-task-card">
        <div class="ft-card-row-head ft-task-card-heading">
            <div><h2>All phase tasks</h2><p><?php echo e($configuredTasks->count()); ?> tasks across <?php echo e($job->workflow->phases->count()); ?> phases</p></div>
            <div class="ft-row-actions">
                <label class="ft-inline-search"><span>⌕</span><input wire:model.live.debounce.250ms="jobTaskSearch" placeholder="Search tasks"></label>
                <button class="ft-outline-btn" type="button" wire:click="collapseAllJobPhases">⌃ Collapse all</button>
                <button class="ft-new-job-btn" type="button" wire:click="expandAllJobPhases">↗ Expand all</button>
            </div>
        </div>
        <div class="ft-phase-load-note"><span>◉ All <?php echo e($configuredTasks->count()); ?> configured Task Pack tasks are loaded</span><span><?php echo e(count($expandedPhaseIds)); ?> phase<?php echo e(count($expandedPhaseIds)===1?'':'s'); ?> expanded</span><span>Task status changes save automatically</span></div>
        <div class="ft-phase-task-table">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $allPhaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                    $completed = \App\Support\JobDetailPresenter::completedCount($allPhaseTasks);
                    $phaseProgress = $allPhaseTasks->count() ? round($completed/max(1,$allPhaseTasks->count())*100) : 0;
                    $phaseTasks = $allPhaseTasks;
                    if(trim($jobTaskSearch)!=='') {
                        $needle = \Illuminate\Support\Str::lower(trim($jobTaskSearch));
                        $phaseTasks = $phaseTasks->filter(fn($task) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($task->title.' '.($task->assignee?->name ?? '')), $needle))->values();
                    }
                    $expanded = in_array((int)$phase->id, array_map('intval',$expandedPhaseIds), true);
                ?>
                <div class="ft-phase-group <?php echo e($expanded ? 'open' : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-phase-'.e($phase->id).''; ?>wire:key="job-phase-<?php echo e($phase->id); ?>">
                    <button type="button" class="ft-phase-group-head ft-phase-toggle" wire:click="toggleJobPhase(<?php echo e($phase->id); ?>)" aria-expanded="<?php echo e($expanded?'true':'false'); ?>">
                        <span><?php echo e($expanded ? '⌄' : '›'); ?></span>
                        <b class="<?php echo e($phase->id === $job->phase->id ? 'current-number' : ''); ?>"><?php echo e($phase->sequence); ?></b>
                        <strong><?php echo e($phase->name); ?></strong>
                        <small><?php echo e($completed); ?> of <?php echo e($allPhaseTasks->count()); ?> complete</small>
                        <em style="--phase-progress:<?php echo e($phaseProgress); ?>%"></em>
                        <i><?php echo e($expanded ? '−' : '+'); ?></i>
                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expanded): ?>
                        <div class="ft-phase-task-columns"><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Action</span></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php ($taskAccess = app(\App\Services\AccessControlService::class)); ?>
                            <?php ($canEditTask = $taskAccess->canEditTask(auth()->user(), $task)); ?>
                            <?php ($canAssignTask = $taskAccess->canAssignTask(auth()->user(), $task)); ?>
                            <div class="ft-phase-task-line ft-editable-task-line" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-task-'.e($task->id).''; ?>wire:key="job-task-<?php echo e($task->id); ?>">
                                <span><?php echo e($phase->sequence); ?>.<?php echo e($loop->iteration); ?></span>
                                <button class="ft-inline-task-link" type="button" wire:click="openTask(<?php echo e($task->id); ?>)"><?php echo e($task->title); ?></button>
                                <span class="ft-task-inline-editor" x-data="{ editing:false }">
                                    <span x-show="!editing" class="ft-task-inline-display"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task->assignee?->name ?? 'Unassigned','size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee?->name ?? 'Unassigned'),'size' => 24]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?><?php echo e($task->assignee?->name ?? 'Unassigned'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTask): ?>
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="editing=true; $nextTick(() => $refs.taskAssignee.focus())">✎</button>
                                        <select x-ref="taskAssignee" x-show="editing" class="ft-task-inline-input" x-on:keydown.escape="editing=false" x-on:blur="editing=false" x-on:change="editing=false" wire:change="updateTaskAssigneeFromJob(<?php echo e($task->id); ?>, $event.target.value)">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($user->id); ?>" <?php if((int)$task->assignee_id===(int)$user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <span class="ft-task-inline-editor" x-data="{ editing:false }">
                                    <span x-show="!editing" class="ft-task-inline-display <?php echo e($task->due_date?->isPast() && !$task->completed_at ? 'danger-text' : ''); ?>"><?php echo e($task->due_date?->format('M j, Y') ?? 'Set due date'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTask): ?>
                                        <button x-show="!editing" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="editing=true; $nextTick(() => $refs.taskDue.showPicker ? $refs.taskDue.showPicker() : $refs.taskDue.focus())">✎</button>
                                        <input x-ref="taskDue" x-show="editing" class="ft-task-inline-input" type="date" value="<?php echo e($task->due_date?->format('Y-m-d')); ?>" x-on:keydown.escape="editing=false" x-on:blur="editing=false" wire:change="updateTaskDueDateFromJob(<?php echo e($task->id); ?>, $event.target.value)">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <select class="ft-inline-task-status <?php echo e(\App\Support\JobDetailPresenter::taskStatusClass($task->status)); ?>" wire:change="updateTaskStatusFromJob(<?php echo e($task->id); ?>, $event.target.value)" <?php if(!$canEditTask): echo 'disabled'; endif; ?>>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($status); ?>" <?php if($task->status===$status): echo 'selected'; endif; ?>><?php echo e($status); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <button class="ft-table-kebab" type="button" wire:click="openTask(<?php echo e($task->id); ?>)">•••</button>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="ft-phase-empty-row">No configured tasks match this phase/search.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </section>

    <section class="ft-detail-card ft-attachment-card ft-job-overview-attachments">
        <h2>Attachments <span><?php echo e($job->documents->count()); ?></span></h2>
        <div class="ft-upload-zone compact"><span class="ft-paperclip">⌕</span><div>Upload documents against the Task Pack requirement that needs them.<small>Documents are linked to the selected task and stored permanently.</small></div><button class="ft-outline-btn" type="button" wire:click="setDetailTab('documents')">Add / choose document</button></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ft-job-file-row"><span class="ft-file-type"><?php echo e(strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE')); ?></span><div><b><?php echo e($doc->name); ?></b><small><?php echo e($doc->task?->title ?: 'Job document'); ?> · <?php echo e($doc->uploader?->name ?? 'FlowTrack'); ?> · <?php echo e($doc->created_at?->format('M j, Y, H:i')); ?></small></div><a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener">Open</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDocument): ?><button type="button" wire:click="deleteJobDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document link?">Delete</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </section>

    <?php if (isset($component)) { $__componentOriginal07a0efd5a24a9992f46129ce7eefcae9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07a0efd5a24a9992f46129ce7eefcae9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-activity','data' => ['job' => $job,'compact' => 'true','activityTab' => $activityTab]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-activity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'compact' => 'true','activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityTab)]); ?>
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
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-overview.blade.php ENDPATH**/ ?>