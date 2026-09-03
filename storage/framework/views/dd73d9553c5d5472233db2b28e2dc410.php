<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'invitation', 'token', 'quote', 'products', 'documents', 'documentTypes',
    'requiredDocumentTypes', 'requiredDocumentCount', 'requiredDocumentTotal',
    'supportingInformationOptions', 'supportingInformation', 'locked' => false,
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
    'invitation', 'token', 'quote', 'products', 'documents', 'documentTypes',
    'requiredDocumentTypes', 'requiredDocumentCount', 'requiredDocumentTotal',
    'supportingInformationOptions', 'supportingInformation', 'locked' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $inquiry = $invitation->inquiry;
    $firstProduct = collect($products)->first();
    $documentCollection = collect($documents);
?>
<div class="ft-rfq-portal-stack">
    <section class="ft-rfq-portal-card ft-rfq-documents-intro">
        <div class="ft-rfq-portal-card__header is-compact">
            <div>
                <h2>Quotation documents</h2>
                <p>Upload supporting files for <?php echo e($firstProduct['name'] ?? 'the requested product'); ?>.</p>
            </div>
        </div>
        <div class="ft-rfq-documents-meta">
            <div><small>Inquiry reference</small><strong><?php echo e($inquiry->inquiry_number); ?></strong></div>
            <div><small>Supplier</small><strong><?php echo e($invitation->supplier?->name ?: '—'); ?></strong></div>
            <div>
                <small>Product &amp; quantity</small>
                <strong class="ft-rfq-documents-product-pill"><?php echo e($firstProduct['code'] ?? 'Product'); ?> <span>·</span> <?php echo e(number_format((float) ($firstProduct['quantity'] ?? 0), 0)); ?> <?php echo e($firstProduct['unit'] ?? 'units'); ?></strong>
            </div>
        </div>
        <div class="ft-rfq-info-strip"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?> Upload files that support your pricing, specifications and production capability. You can add more files until the quotation is submitted.</div>
    </section>

    <section class="ft-rfq-portal-card ft-rfq-required-docs">
        <div class="ft-rfq-section-title-row">
            <h2>Required documents</h2>
            <span class="ft-rfq-complete-pill"><?php echo e($requiredDocumentCount); ?> of <?php echo e($requiredDocumentTotal); ?> complete</span>
        </div>
        <div class="ft-rfq-required-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $requiredDocumentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requiredType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $present = $documentCollection->contains('document_type', $requiredType);
                    $description = $requiredType === 'formal_quotation'
                        ? 'PDF or DOCX showing commercial pricing and validity.'
                        : 'XLSX or PDF with unit pricing and any volume breaks.';
                ?>
                <div class="ft-rfq-required-row <?php echo e($present ? 'is-complete' : ''); ?>">
                    <span class="ft-rfq-status-dot"><?php echo e($present ? '✓' : $loop->iteration); ?></span>
                    <div><strong><?php echo e($loop->iteration); ?>. <?php echo e($documentTypes[$requiredType]); ?></strong><small><?php echo e($description); ?></small></div>
                    <span class="ft-rfq-uploaded-status"><?php echo e($present ? '✓ Uploaded' : 'Required'); ?></span>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($locked)): ?>
        <form method="post" action="<?php echo e(route('rfq.public.respond', ['token' => $token])); ?>" enctype="multipart/form-data" id="rfq-documents-form" class="ft-rfq-portal-stack">
            <?php echo csrf_field(); ?>
            <section class="ft-rfq-portal-card ft-rfq-upload-section">
                <h2>Upload documents</h2>
                <label class="ft-rfq-dropzone" data-rfq-dropzone>
                    <input type="file" name="documents[]" multiple accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>" data-rfq-document-input data-upload-url="<?php echo e(route('rfq.public.documents.upload', ['token' => $token])); ?>">
                    <span class="ft-rfq-upload-icon"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'upload-cloud']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'upload-cloud']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?></span>
                    <span class="ft-rfq-dropzone__copy"><strong>Drop files here or <b>browse</b></strong><small><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></small></span>
                    <span class="ft-rfq-browse-btn">Browse files</span>
                </label>

                <div class="ft-rfq-uploaded-heading">Uploaded files (<?php echo e($documentCollection->count()); ?>)</div>
                <div class="ft-rfq-doc-table-wrap">
                    <table class="ft-rfq-doc-table">
                        <thead><tr><th>Document</th><th>File name</th><th>Size</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $documentCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $extension = strtolower(pathinfo((string) $document->name, PATHINFO_EXTENSION));
                                $size = (int) $document->size;
                                $sizeLabel = $size >= 1048576 ? number_format($size / 1048576, 1).' MB' : number_format(max(1, (int) ceil($size / 1024)), 0).' KB';
                            ?>
                            <tr>
                                <td>
                                    <select name="document_types[<?php echo e($document->id); ?>]">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($value); ?>" <?php if(old('document_types.'.$document->id, $document->document_type) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                </td>
                                <td><div class="ft-rfq-file-name"><span class="ft-rfq-file-icon is-<?php echo e($extension); ?>"><?php echo e(strtoupper(substr($extension ?: 'FILE', 0, 4))); ?></span><strong><?php echo e($document->name); ?></strong></div></td>
                                <td><?php echo e($sizeLabel); ?></td>
                                <td><span class="ft-rfq-ready-status">✓ Uploaded</span></td>
                                <td>
                                    <div class="ft-rfq-doc-actions">
                                        <a href="<?php echo e(route('rfq.public.documents.preview', ['token' => $token, 'document' => $document->id])); ?>" target="_blank" rel="noopener">Preview</a>
                                        <a href="<?php echo e(route('rfq.public.documents.download', ['token' => $token, 'document' => $document->id])); ?>">Download</a>
                                        <button type="submit" form="rfq-remove-doc-<?php echo e($document->id); ?>">Remove</button>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="5"><div class="ft-rfq-empty-docs">No documents uploaded yet.</div></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="ft-rfq-portal-card ft-rfq-supporting-section">
                <h2>Optional supporting information</h2>
                <div class="ft-rfq-supporting-checks">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $supportingInformationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label><input type="checkbox" name="supporting_information[]" value="<?php echo e($value); ?>" <?php if(in_array($value, old('supporting_information', $supportingInformation), true)): echo 'checked'; endif; ?>><span><?php echo e($label); ?></span></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <label class="ft-rfq-document-notes"><span>Document notes (optional)</span><textarea name="document_notes" rows="3"><?php echo e(old('document_notes', $quote?->document_notes)); ?></textarea></label>
            </section>

            <div class="ft-rfq-portal-bottom-actions">
                <a class="ft-rfq-btn is-secondary" href="<?php echo e(route('rfq.public.show', ['token' => $token, 'step' => 'pricing'])); ?>"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?> Back to pricing</a>
                <div>
                    <button type="submit" class="ft-rfq-btn is-secondary" name="action" value="save_documents"><?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'save']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'save']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?> Save draft</button>
                    <button type="submit" class="ft-rfq-btn is-primary" name="action" value="continue_review">Continue to review <?php if (isset($component)) { $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rfq.public.icon','data' => ['name' => 'chevron-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rfq.public.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $attributes = $__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__attributesOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f)): ?>
<?php $component = $__componentOriginal19d5a25445acdc2666ec5d32271cdd6f; ?>
<?php unset($__componentOriginal19d5a25445acdc2666ec5d32271cdd6f); ?>
<?php endif; ?></button>
                </div>
            </div>
        </form>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <form method="post" action="<?php echo e(route('rfq.public.documents.remove', ['token' => $token, 'document' => $document->id])); ?>" id="rfq-remove-doc-<?php echo e($document->id); ?>" class="ft-rfq-hidden-form"><?php echo csrf_field(); ?></form>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php else: ?>
        <section class="ft-rfq-portal-card"><div class="ft-rfq-locked-note">This quotation is locked and documents can no longer be changed.</div></section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/rfq/public/documents.blade.php ENDPATH**/ ?>