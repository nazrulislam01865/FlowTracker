<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'selectedInquiries' => collect(),
    'initialOptions' => collect(),
    'clientId' => null,
    'selectorVersion' => 0,
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
    'selectedInquiries' => collect(),
    'initialOptions' => collect(),
    'clientId' => null,
    'selectorVersion' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $selected = collect($selectedInquiries)
        ->filter(fn ($item) => is_array($item) && filled($item['id'] ?? null) && filled($item['label'] ?? null))
        ->values();
    $selectedIds = $selected->pluck('id')->map(fn ($id) => (int) $id)->filter()->unique()->values();
    $excludeIds = $selectedIds->implode(',');
?>

<div
    class="ft-create-field ft-create-inquiry-link"
    data-create-inquiry-link
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-inquiry-link-'.e($selectorVersion).''; ?>wire:key="create-inquiry-link-<?php echo e($selectorVersion); ?>"
    x-data="{ editing: <?php echo \Illuminate\Support\Js::from($selected->isEmpty())->toHtml() ?> }"
    x-on:flowtrack-create-inquiry-picker-open.window="editing = true"
>
    <b>Search and Link Inquiry</b>
    <small class="ft-create-inquiry-help">Search and link this order to existing inquiries (optional).</small>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->isNotEmpty()): ?>
        <div class="ft-create-inquiry-selected-list" aria-label="Selected inquiries">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selected; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $inquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="ft-create-inquiry-selected" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-order-selected-inquiry-'.e((int) $inquiry['id']).''; ?>wire:key="create-order-selected-inquiry-<?php echo e((int) $inquiry['id']); ?>">
                    <span class="ft-create-inquiry-link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M9.5 14.5 14.5 9.5"/>
                            <path d="m7.4 16.6-1.2 1.2a3.4 3.4 0 0 1-4.8-4.8l3.7-3.7a3.4 3.4 0 0 1 4.8 0"/>
                            <path d="m16.6 7.4 1.2-1.2a3.4 3.4 0 0 1 4.8 4.8l-3.7 3.7a3.4 3.4 0 0 1-4.8 0"/>
                        </svg>
                    </span>
                    <span class="ft-create-inquiry-selected-copy">
                        <strong title="<?php echo e($inquiry['label']); ?>"><?php echo e($inquiry['label']); ?></strong>
                        <small>
                            <i aria-hidden="true"></i>
                            <?php echo e($index === 0 ? 'Primary linked inquiry' : 'Linked inquiry'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($inquiry['meta'] ?? null)): ?>
                                <span class="ft-create-inquiry-meta">· <?php echo e($inquiry['meta']); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </small>
                    </span>
                    <span class="ft-create-inquiry-row-actions">
                        <button
                            type="button"
                            class="ft-create-inquiry-change"
                            wire:click="openCreateInquiryPicker(<?php echo e((int) $inquiry['id']); ?>)"
                            wire:loading.attr="disabled"
                            wire:target="openCreateInquiryPicker"
                        >Change</button>
                        <button
                            type="button"
                            class="ft-create-inquiry-remove"
                            wire:click="removeCreateInquiry(<?php echo e((int) $inquiry['id']); ?>)"
                            wire:loading.attr="disabled"
                            wire:target="removeCreateInquiry"
                            aria-label="Remove <?php echo e($inquiry['label']); ?>"
                            title="Remove inquiry"
                        >×</button>
                    </span>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="ft-create-inquiry-actions">
            <span class="ft-create-inquiry-count"><?php echo e($selected->count()); ?> <?php echo e($selected->count() === 1 ? 'inquiry' : 'inquiries'); ?> selected</span>
            <button
                type="button"
                class="ft-create-inquiry-add"
                wire:click="openCreateInquiryPicker"
                wire:loading.attr="disabled"
                wire:target="openCreateInquiryPicker"
            >+ Add another inquiry</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div
        class="ft-create-inquiry-picker-wrap"
        x-ref="picker"
        x-cloak
        x-show="editing || <?php echo \Illuminate\Support\Js::from($selected->isEmpty())->toHtml() ?>"
    >
        <div class="ft-create-inquiry-picker">
            <svg class="ft-create-inquiry-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"/>
                <path d="m16 16 4 4"/>
            </svg>
            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-create-inquiry-select','label' => 'Search and Link Inquiry','property' => 'createInquiryId','type' => 'inquiries','context' => 'create-job','action' => 'setCreateSelector','value' => null,'placeholder' => 'Search by inquiry number or title','searchPlaceholder' => 'Search by inquiry number or title','selectedLabel' => null,'initialOptions' => $initialOptions,'params' => ['client_id' => $clientId, 'exclude_ids' => $excludeIds],'clearable' => false,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 620,'infiniteScroll' => true,'wire:key' => 'create-inquiry-picker-'.e($selectorVersion).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-inquiry-select','label' => 'Search and Link Inquiry','property' => 'createInquiryId','type' => 'inquiries','context' => 'create-job','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'placeholder' => 'Search by inquiry number or title','search-placeholder' => 'Search by inquiry number or title','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($initialOptions),'params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['client_id' => $clientId, 'exclude_ids' => $excludeIds]),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hide-label' => true,'fixed-menu' => true,'menu-width' => 620,'infinite-scroll' => true,'wire:key' => 'create-inquiry-picker-'.e($selectorVersion).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->isNotEmpty()): ?>
            <div class="ft-create-inquiry-picker-footer">
                <small>Select an Inquiry to add it or replace the one you chose to change.</small>
                <button
                    type="button"
                    class="ft-create-inquiry-cancel"
                    wire:click="cancelCreateInquiryPicker"
                    x-on:click="editing = false"
                >Cancel</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createInquiryId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createInquiryIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createInquiryIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/inquiry-link.blade.php ENDPATH**/ ?>