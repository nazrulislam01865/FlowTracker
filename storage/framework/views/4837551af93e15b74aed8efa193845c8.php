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
    $revisionAttachments = $revisionNote->relationLoaded('revisionAttachments')
        ? collect($revisionNote->getRelation('revisionAttachments'))->values()
        : collect();

    $revisionDocumentsById = $revisionDocuments->keyBy(fn($document) => (int) $document->id);
    $revisionAttachmentsById = $revisionAttachments->keyBy(fn($document) => (int) $document->id);
    $richText = app(\App\Services\RichTextService::class);

    $revisionItems = collect(data_get($revisionNote->meta, 'revision_items', []))
        ->map(fn($item) => (array) $item)
        ->filter(fn($item) => (int) ($item['document_id'] ?? 0) > 0)
        ->values();

    // Compatibility for revision activities created before per-artwork items
    // were persisted. A single older revision still renders as one paired row.
    if ($revisionItems->isEmpty()) {
        $revisionItems = $revisionDocuments->map(fn($document, $index) => [
            'document_id' => (int) $document->id,
            'document_name' => (string) $document->name,
            'comment' => $index === 0 ? $revisionComment : '',
            'revision_attachment_document_ids' => $index === 0
                ? $revisionAttachments->pluck('id')->map(fn($id) => (int) $id)->all()
                : [],
        ])->values();
    }
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

    <div class="ft-order-artwork-revision-item-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $revisionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revisionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $revisionDocumentId = (int) ($revisionItem['document_id'] ?? 0);
                $revisionDocument = $revisionDocumentsById->get($revisionDocumentId);
                $revisionDocumentName = (string) ($revisionDocument?->name ?: ($revisionItem['document_name'] ?? 'Artwork'));
                $revisionExtension = strtoupper(pathinfo($revisionDocumentName, PATHINFO_EXTENSION) ?: 'FILE');
                $itemComment = trim((string) ($revisionItem['comment'] ?? ''));
                if ($itemComment === '' && $revisionItems->count() === 1) {
                    $itemComment = $revisionComment;
                }
                $requiredChangeText = $richText->withoutImages($itemComment);
                $requiredChangeImages = collect($richText->imageAttachments($itemComment));
                $itemAttachmentIds = collect($revisionItem['revision_attachment_document_ids'] ?? [])
                    ->map(fn($id) => (int) $id)
                    ->filter(fn($id) => $id > 0)
                    ->unique()
                    ->values();
                $itemAttachments = $itemAttachmentIds
                    ->map(fn($id) => $revisionAttachmentsById->get($id))
                    ->filter()
                    ->values();
            ?>

            <div class="ft-order-artwork-revision-item" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-artwork-revision-item-'.e($revisionNote->id).'-'.e($revisionDocumentId).''; ?>wire:key="order-artwork-revision-item-<?php echo e($revisionNote->id); ?>-<?php echo e($revisionDocumentId); ?>">
                <div class="ft-order-artwork-revision-item-artwork">
                    <div class="ft-order-artwork-revision-label">Artwork selected for revision</div>
                    <div class="ft-order-artwork-revision-attachment">
                        <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $revisionExtension,'class' => 'ft-order-artwork-revision-file-icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($revisionExtension),'class' => 'ft-order-artwork-revision-file-icon']); ?>
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
                        <span class="ft-order-artwork-revision-file-copy">
                            <b><?php echo e($revisionDocumentName); ?></b>
                            <small><?php echo e($revisionExtension); ?> · This artwork requires replacement</small>
                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revisionDocument): ?>
                            <span class="ft-order-artwork-revision-file-actions">
                                <a href="<?php echo e(route('documents.open', $revisionDocument)); ?>" target="_blank" rel="noopener">Open</a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocument): ?>
                                    <span class="ft-order-artwork-revision-action-divider" aria-hidden="true"></span>
                                    <a href="<?php echo e(route('documents.download', $revisionDocument)); ?>">Download</a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="ft-order-artwork-revision-pair-grid">
                    <div class="ft-order-artwork-revision-field ft-order-artwork-revision-change-field">
                        <div class="ft-order-artwork-revision-label">Required change</div>
                        <div class="ft-order-artwork-revision-change ft-rich-text-content">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($requiredChangeText)): ?>
                                <?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $requiredChangeText]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($requiredChangeText)]); ?>
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
<?php endif; ?>
                            <?php else: ?>
                                <span class="ft-order-artwork-revision-empty-copy">No written change provided.</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="ft-order-artwork-revision-field ft-order-artwork-revision-supporting-field">
                        <div class="ft-order-artwork-revision-label">Supporting attachments</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($requiredChangeImages->isNotEmpty() || $itemAttachments->isNotEmpty()): ?>
                            <div class="ft-order-artwork-revision-reviewer-file-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $requiredChangeImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="ft-order-artwork-revision-reviewer-file">
                                        <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $image['extension'],'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($image['extension']),'size' => 'sm']); ?>
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
                                        <span class="ft-order-artwork-revision-file-copy">
                                            <b><?php echo e($image['name']); ?></b>
                                            <small><?php echo e($image['extension']); ?> · Reference</small>
                                        </span>
                                        <span class="ft-order-artwork-revision-file-actions">
                                            <a href="<?php echo e($image['url']); ?>" target="_blank" rel="noopener">Open</a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocument): ?>
                                                <span class="ft-order-artwork-revision-action-divider" aria-hidden="true"></span>
                                                <a href="<?php echo e($image['download_url']); ?>">Download</a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $itemAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php $attachmentExtension = strtoupper(pathinfo((string) $attachment->name, PATHINFO_EXTENSION) ?: 'FILE'); ?>
                                    <div class="ft-order-artwork-revision-reviewer-file">
                                        <?php if (isset($component)) { $__componentOriginal8cc2d9c978b2c497e659881c0713db1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc2d9c978b2c497e659881c0713db1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.file-type-badge','data' => ['extension' => $attachmentExtension,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.file-type-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extension' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attachmentExtension),'size' => 'sm']); ?>
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
                                        <span class="ft-order-artwork-revision-file-copy">
                                            <b><?php echo e($attachment->name); ?></b>
                                            <small><?php echo e($attachmentExtension); ?> · Revision reference</small>
                                        </span>
                                        <span class="ft-order-artwork-revision-file-actions">
                                            <a href="<?php echo e(route('documents.open', $attachment)); ?>" target="_blank" rel="noopener">Open</a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canExportDocument): ?>
                                                <span class="ft-order-artwork-revision-action-divider" aria-hidden="true"></span>
                                                <a href="<?php echo e(route('documents.download', $attachment)); ?>">Download</a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="ft-order-artwork-revision-no-support">No supporting attachment</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-order-artwork-revision-attachment ft-order-artwork-revision-attachment--empty">
                The artwork revision details are unavailable. Refresh the Order to load the current request.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/artwork-revision-card.blade.php ENDPATH**/ ?>