<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'purchaseOrderUpload' => null,
    'jobAttachments' => [],
    'canUpload' => false,
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
    'purchaseOrderUpload' => null,
    'jobAttachments' => [],
    'canUpload' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section <?php echo e($attributes->class(['ft-create-section', 'ft-order-documents-section'])); ?>>
    <div class="ft-order-documents-heading">
        <div class="ft-create-section-title ft-order-documents-title">
            <span>5</span>
            <h2>Documents</h2>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($purchaseOrderUpload ? 1 : 0) + count($jobAttachments) > 0): ?>
            <span class="ft-order-documents-count">
                <?php echo e(($purchaseOrderUpload ? 1 : 0) + count($jobAttachments)); ?>

                <?php echo e(\Illuminate\Support\Str::plural('file', ($purchaseOrderUpload ? 1 : 0) + count($jobAttachments))); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <p class="ft-order-documents-intro">Upload the purchase order when available and add any supporting files for this order.</p>

    <div class="ft-order-documents-panel">
        <div class="ft-order-document-group ft-order-document-group--po">
            <div class="ft-order-document-group-heading">
                <strong>Purchase order</strong>
                <span class="ft-order-document-optional">Optional</span>
            </div>
            <p>Upload the official customer purchase order when available. One file only.</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUpload): ?>
                <?php if (isset($component)) { $__componentOriginal6341f0ed212d869a5635165bbc977ad7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6341f0ed212d869a5635165bbc977ad7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.create-attachment-dropzone','data' => ['inputId' => 'job-create-purchase-order','model' => 'purchaseOrderUpload','variant' => 'order-document','headline' => 'Drop purchase order here','browseText' => 'browse','browseButton' => 'Browse file','helper' => \App\Support\AttachmentUpload::helperText(20),'accept' => \App\Support\AttachmentUpload::accept(\App\Support\AttachmentUpload::DOCUMENTS_WITH_AI),'progressLabel' => 'Uploading purchase order...','progressAriaLabel' => 'Purchase order upload progress']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.create-attachment-dropzone'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['input-id' => 'job-create-purchase-order','model' => 'purchaseOrderUpload','variant' => 'order-document','headline' => 'Drop purchase order here','browse-text' => 'browse','browse-button' => 'Browse file','helper' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::helperText(20)),'accept' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::accept(\App\Support\AttachmentUpload::DOCUMENTS_WITH_AI)),'progress-label' => 'Uploading purchase order...','progress-aria-label' => 'Purchase order upload progress']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6341f0ed212d869a5635165bbc977ad7)): ?>
<?php $attributes = $__attributesOriginal6341f0ed212d869a5635165bbc977ad7; ?>
<?php unset($__attributesOriginal6341f0ed212d869a5635165bbc977ad7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6341f0ed212d869a5635165bbc977ad7)): ?>
<?php $component = $__componentOriginal6341f0ed212d869a5635165bbc977ad7; ?>
<?php unset($__componentOriginal6341f0ed212d869a5635165bbc977ad7); ?>
<?php endif; ?>
            <?php else: ?>
                <div class="ft-create-note ft-order-document-permission-note">Your role does not allow purchase order uploads during Order creation.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchaseOrderUpload): ?>
                <div class="ft-order-document-list-block">
                    <small class="ft-order-document-list-label">Uploaded purchase order</small>
                    <?php if (isset($component)) { $__componentOriginal8d9b27818821d89a739dd389f9ad5226 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d9b27818821d89a739dd389f9ad5226 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.document-file-row','data' => ['meta' => \App\Support\CreateOrderDocumentPresenter::fileMeta($purchaseOrderUpload),'inputId' => 'job-create-purchase-order','purchaseOrder' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.document-file-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\CreateOrderDocumentPresenter::fileMeta($purchaseOrderUpload)),'input-id' => 'job-create-purchase-order','purchase-order' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d9b27818821d89a739dd389f9ad5226)): ?>
<?php $attributes = $__attributesOriginal8d9b27818821d89a739dd389f9ad5226; ?>
<?php unset($__attributesOriginal8d9b27818821d89a739dd389f9ad5226); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d9b27818821d89a739dd389f9ad5226)): ?>
<?php $component = $__componentOriginal8d9b27818821d89a739dd389f9ad5226; ?>
<?php unset($__componentOriginal8d9b27818821d89a739dd389f9ad5226); ?>
<?php endif; ?>
                </div>

                <div class="ft-order-document-success">
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="6.2" fill="currentColor" opacity=".13"/><path d="m5.4 8 1.65 1.65L10.8 5.9" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Purchase order attached and ready.</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['purchaseOrderUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-order-document-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-document-divider" aria-hidden="true"></div>

        <div class="ft-order-document-group ft-order-document-group--other">
            <div class="ft-order-document-group-heading">
                <strong>Other documents</strong>
                <span class="ft-order-document-optional">Optional</span>
            </div>
            <p>Add specifications, artwork, references or approvals. Multiple files allowed.</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUpload): ?>
                <?php if (isset($component)) { $__componentOriginal6341f0ed212d869a5635165bbc977ad7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6341f0ed212d869a5635165bbc977ad7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.create-attachment-dropzone','data' => ['inputId' => 'job-create-files','model' => 'jobAttachments','multiple' => true,'variant' => 'order-document','headline' => 'Drop supporting files here','browseText' => 'browse','browseButton' => 'Browse files','helper' => \App\Support\AttachmentUpload::helperText(20),'accept' => \App\Support\AttachmentUpload::accept(\App\Support\AttachmentUpload::DOCUMENTS_WITH_AI),'progressLabel' => 'Uploading selected files...','progressAriaLabel' => 'Order document upload progress']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.create-attachment-dropzone'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['input-id' => 'job-create-files','model' => 'jobAttachments','multiple' => true,'variant' => 'order-document','headline' => 'Drop supporting files here','browse-text' => 'browse','browse-button' => 'Browse files','helper' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::helperText(20)),'accept' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::accept(\App\Support\AttachmentUpload::DOCUMENTS_WITH_AI)),'progress-label' => 'Uploading selected files...','progress-aria-label' => 'Order document upload progress']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6341f0ed212d869a5635165bbc977ad7)): ?>
<?php $attributes = $__attributesOriginal6341f0ed212d869a5635165bbc977ad7; ?>
<?php unset($__attributesOriginal6341f0ed212d869a5635165bbc977ad7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6341f0ed212d869a5635165bbc977ad7)): ?>
<?php $component = $__componentOriginal6341f0ed212d869a5635165bbc977ad7; ?>
<?php unset($__componentOriginal6341f0ed212d869a5635165bbc977ad7); ?>
<?php endif; ?>
            <?php else: ?>
                <div class="ft-create-note ft-order-document-permission-note">Your role does not allow supporting document uploads during Order creation.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($jobAttachments) > 0): ?>
                <div class="ft-order-document-list-block">
                    <small class="ft-order-document-list-label">Uploaded files (<?php echo e(count($jobAttachments)); ?>)</small>
                    <div class="ft-order-document-file-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal8d9b27818821d89a739dd389f9ad5226 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d9b27818821d89a739dd389f9ad5226 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.create.document-file-row','data' => ['meta' => \App\Support\CreateOrderDocumentPresenter::fileMeta($file),'inputId' => 'job-create-files','index' => $index]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.create.document-file-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\CreateOrderDocumentPresenter::fileMeta($file)),'input-id' => 'job-create-files','index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d9b27818821d89a739dd389f9ad5226)): ?>
<?php $attributes = $__attributesOriginal8d9b27818821d89a739dd389f9ad5226; ?>
<?php unset($__attributesOriginal8d9b27818821d89a739dd389f9ad5226); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d9b27818821d89a739dd389f9ad5226)): ?>
<?php $component = $__componentOriginal8d9b27818821d89a739dd389f9ad5226; ?>
<?php unset($__componentOriginal8d9b27818821d89a739dd389f9ad5226); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobAttachments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-order-document-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jobAttachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error ft-order-document-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-document-archive-note">
            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="6.2" fill="currentColor" opacity=".12"/><path d="M8 7.1v4M8 4.7h.01" stroke="currentColor" stroke-width="1.45" stroke-linecap="round"/></svg>
            <span>Purchase orders and supporting documents will be saved to the order's Document Archive.</span>
        </div>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/documents.blade.php ENDPATH**/ ?>