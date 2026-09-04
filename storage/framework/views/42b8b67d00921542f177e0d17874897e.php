<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'documents' => collect(),
    'canExportDocument' => false,
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
    'documents' => collect(),
    'canExportDocument' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $archivedDocuments = collect($documents)->values();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($archivedDocuments->isNotEmpty()): ?>
    <section class="ft-order-archived-artwork" aria-labelledby="archived-artwork-title">
        <div class="ft-order-archived-artwork__head">
            <div class="ft-order-archived-artwork__title-row">
                <h3 id="archived-artwork-title">Archived Artwork</h3>
                <span class="ft-order-archived-artwork__count"><?php echo e($archivedDocuments->count()); ?> in history</span>
            </div>
            <p>View previous versions of artwork that have been replaced. Cancelled artwork is retained here for audit history.</p>
        </div>

        <div class="ft-order-archived-artwork__table-scroll">
            <table class="ft-order-archived-artwork__table">
                <thead>
                    <tr>
                        <th scope="col">Filename</th>
                        <th scope="col">Version</th>
                        <th scope="col">Uploaded by</th>
                        <th scope="col">Uploaded on</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="ft-order-archived-artwork__action-heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $archivedDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'archived-artwork-document-'.e($document->id).''; ?>wire:key="archived-artwork-document-<?php echo e($document->id); ?>">
                            <td>
                                <div class="ft-order-archived-artwork__file">
                                    <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['name' => $document->name,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($document->name),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $attributes = $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__attributesOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b)): ?>
<?php $component = $__componentOriginal8cc2d9c978b2c497e659881c0713db1b; ?>
<?php unset($__componentOriginal8cc2d9c978b2c497e659881c0713db1b); ?>
<?php endif; ?>
                                    <div class="ft-order-archived-artwork__file-copy">
                                        <span class="ft-order-archived-artwork__filename" title="<?php echo e($document->name); ?>"><?php echo e($document->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($document->artwork_revision_reason)): ?>
                                            <div class="ft-order-archived-artwork__reason" title="<?php echo e($document->artwork_revision_reason); ?>">
                                                <strong><?php echo e($document->artwork_archive_reason_label ?: 'Revision reason'); ?></strong>
                                                <span><?php echo e(\Illuminate\Support\Str::limit($document->artwork_revision_reason, 120)); ?></span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($document->artwork_cancelled_product_names)): ?>
                                            <div class="ft-order-archived-artwork__reason ft-order-archived-artwork__reason--products">
                                                <strong>Removed product<?php echo e(count($document->artwork_cancelled_product_names) === 1 ? '' : 's'); ?></strong>
                                                <span><?php echo e(implode(', ', $document->artwork_cancelled_product_names)); ?></span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="ft-order-archived-artwork__version">v<?php echo e(max(1, (int) $document->version)); ?></td>
                            <td><?php echo e($document->relationLoaded('uploader') ? ($document->uploader?->name ?? 'FlowTrack') : 'FlowTrack'); ?></td>
                            <td><?php echo e(\App\Support\UserLocalTime::format($document->created_at, 'M j, Y \\a\\t g:i A')); ?></td>
                            <td><span class="ft-order-archived-artwork__status <?php echo e((string) ($document->artwork_archive_status ?: 'Archived') === 'Cancelled' ? 'is-cancelled' : ''); ?>"><?php echo e($document->artwork_archive_status ?: 'Archived'); ?></span></td>
                            <td>
                                <div class="ft-order-archived-artwork__actions">
                                    <a href="<?php echo e(route('documents.open', $document)); ?>" target="_blank" rel="noopener">Open</a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocument): ?>
                                        <a href="<?php echo e(route('documents.download', $document)); ?>">Download</a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/archived-artwork.blade.php ENDPATH**/ ?>