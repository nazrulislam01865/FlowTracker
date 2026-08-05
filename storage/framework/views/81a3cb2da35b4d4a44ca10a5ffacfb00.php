<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['number', 'title', 'section', 'rows' => 3]));

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

foreach (array_filter((['number', 'title', 'section', 'rows' => 3]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section
    class="ft-create-section ft-progressive-section-placeholder"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-'.e($section).'-placeholder'; ?>wire:key="create-<?php echo e($section); ?>-placeholder"
    role="status"
    aria-live="polite"
    aria-busy="true"
    x-data
    x-init="
        if (!window.IntersectionObserver) {
            $wire.loadCreateSection(<?php echo \Illuminate\Support\Js::from($section)->toHtml() ?>);
            return;
        }
        const observer = new IntersectionObserver((entries) => {
            if (!entries[0]?.isIntersecting) return;
            observer.disconnect();
            $wire.loadCreateSection(<?php echo \Illuminate\Support\Js::from($section)->toHtml() ?>);
        }, { rootMargin: '240px 0px' });
        observer.observe($el);
    "
>
    <div class="ft-create-section-title"><span><?php echo e($number); ?></span><h2><?php echo e($title); ?></h2></div>
    <div class="ft-progressive-skeleton" aria-hidden="true">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($row = 0; $row < $rows; $row++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <span style="--ft-skeleton-width: <?php echo e(100 - (($row % 3) * 12)); ?>%"></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <small class="muted">Loading this section when needed…</small>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create-section-placeholder.blade.php ENDPATH**/ ?>