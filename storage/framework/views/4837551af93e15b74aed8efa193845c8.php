<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'revisionNote',
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
    'revisionNote',
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
    $revisionComment = trim((string) data_get($revisionNote->meta, 'revision_comment', ''));
    if ($revisionComment === '') {
        $description = (string) $revisionNote->description;
        $revisionComment = str_contains($description, ': ')
            ? \Illuminate\Support\Str::after($description, ': ')
            : $description;
    }

    $referenceDocument = $revisionNote->relationLoaded('referenceDocument')
        ? $revisionNote->getRelation('referenceDocument')
        : null;
    $revisionDocuments = $revisionNote->relationLoaded('revisionDocuments')
        ? collect($revisionNote->getRelation('revisionDocuments'))->values()
        : ($referenceDocument ? collect([$referenceDocument]) : collect());
    $extension = $referenceDocument
        ? strtoupper(pathinfo((string) $referenceDocument->name, PATHINFO_EXTENSION) ?: 'FILE')
        : 'FILE';
?>

<section class="ft-order-artwork-revision-card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-task-artwork-revision-'.e($revisionNote->id).''; ?>wire:key="order-task-artwork-revision-<?php echo e($revisionNote->id); ?>">
    <div class="ft-order-artwork-revision-head">
        <span class="ft-order-artwork-revision-alert" aria-hidden="true">
            <span>!</span>
        </span>

        <div class="ft-order-artwork-revision-heading">
            <div class="ft-order-artwork-revision-kicker">Revision required</div>
            <div class="ft-order-artwork-revision-title">Artwork revision issue</div>
            <div class="ft-order-artwork-revision-meta">
                Requested by <?php echo e($revisionNote->user?->name ?? 'FlowTrack'); ?> · <?php echo e(\App\Support\UserLocalTime::format($revisionNote->created_at, 'M j, Y, g:i A')); ?>

            </div>
        </div>

        <span class="ft-order-artwork-revision-badge">Revision</span>
    </div>

    <div class="ft-order-artwork-revision-grid">
        <div class="ft-order-artwork-revision-field">
            <div class="ft-order-artwork-revision-label">Required change</div>
            <div class="ft-order-artwork-revision-change"><?php echo e($revisionComment); ?></div>
        </div>

        <div class="ft-order-artwork-revision-field">
            <div class="ft-order-artwork-revision-label">Artwork selected for revision</div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revisionDocuments->isNotEmpty()): ?>
                <div class="ft-order-artwork-revision-attachment-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $revisionDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revisionDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $revisionExtension = strtoupper(pathinfo((string) $revisionDocument->name, PATHINFO_EXTENSION) ?: 'FILE'); ?>
                        <div class="ft-order-artwork-revision-attachment">
                            <span class="file-icon ft-order-file-icon ft-order-artwork-revision-file-icon"><?php echo e($revisionExtension); ?></span>
                            <span class="ft-order-artwork-revision-file-copy">
                                <b><?php echo e($revisionDocument->name); ?> · Version <?php echo e(max(1, (int) $revisionDocument->version)); ?></b>
                                <small><?php echo e($revisionExtension); ?> · Only this selected artwork needs replacement</small>
                            </span>
                            <span class="ft-order-artwork-revision-file-actions">
                                <a href="<?php echo e(route('documents.open', $revisionDocument)); ?>" target="_blank" rel="noopener">Open</a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocument): ?>
                                    <span class="ft-order-artwork-revision-action-divider" aria-hidden="true"></span>
                                    <a href="<?php echo e(route('documents.download', $revisionDocument)); ?>">Download</a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="ft-order-artwork-revision-attachment ft-order-artwork-revision-attachment--empty">
                    No reference attachment was available for this revision request.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/artwork-revision-card.blade.php ENDPATH**/ ?>