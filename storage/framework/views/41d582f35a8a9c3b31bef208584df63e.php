<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'section',
    'rows' => 3,
    'message' => 'Loading this section when needed…',
    'rootMargin' => '240px 0px',
    'method' => 'loadCreateSection',
    'keyPrefix' => 'progressive-section',
    'contextType' => '',
    'contextId' => null,
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
    'section',
    'rows' => 3,
    'message' => 'Loading this section when needed…',
    'rootMargin' => '240px 0px',
    'method' => 'loadCreateSection',
    'keyPrefix' => 'progressive-section',
    'contextType' => '',
    'contextId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $contextKey = $contextType !== '' && $contextId !== null
        ? '-'.$contextType.'-'.$contextId
        : '';
?>

<div
    <?php echo e($attributes->class(['ft-progressive-section-placeholder'])); ?>

    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($keyPrefix).''.e($contextKey).'-'.e($method).'-'.e($section).''; ?>wire:key="<?php echo e($keyPrefix); ?><?php echo e($contextKey); ?>-<?php echo e($method); ?>-<?php echo e($section); ?>"
    role="status"
    aria-live="polite"
    aria-busy="true"
    x-data="{ requested: false, observer: null }"
    x-init="
        const invokeSectionLoad = () => {
            <?php if($contextType !== '' && $contextId !== null): ?>
                return $wire[<?php echo \Illuminate\Support\Js::from($method)->toHtml() ?>](<?php echo \Illuminate\Support\Js::from($section)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($contextType)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from((int) $contextId)->toHtml() ?>);
            <?php else: ?>
                return $wire[<?php echo \Illuminate\Support\Js::from($method)->toHtml() ?>](<?php echo \Illuminate\Support\Js::from($section)->toHtml() ?>);
            <?php endif; ?>
        };

        const loadSection = () => {
            if (requested || !$el.isConnected) return;
            requested = true;

            Promise.resolve(invokeSectionLoad()).catch(() => {
                // Allow a later viewport event to retry after a transient
                // Livewire/network failure instead of leaving the skeleton
                // permanently stuck.
                requested = false;
            });
        };

        // Disconnect observers before Livewire SPA navigation. We deliberately
        // avoid Alpine-only cleanup helpers here because this shared component
        // must also work during Livewire DOM morphs/navigation.
        document.addEventListener('livewire:navigating', () => {
            observer?.disconnect();
        }, { once: true });

        if (!window.IntersectionObserver) {
            loadSection();
            return;
        }

        observer = new IntersectionObserver((entries) => {
            if (!entries[0]?.isIntersecting) return;
            loadSection();
        }, { rootMargin: <?php echo \Illuminate\Support\Js::from($rootMargin)->toHtml() ?> });

        observer.observe($el);
    "
>
    <div class="ft-progressive-skeleton" aria-hidden="true">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($row = 0; $row < $rows; $row++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <span style="--ft-skeleton-width: <?php echo e(100 - (($row % 3) * 12)); ?>%"></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?>
        <small class="muted"><?php echo e($message); ?></small>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/progressive-section-loader.blade.php ENDPATH**/ ?>