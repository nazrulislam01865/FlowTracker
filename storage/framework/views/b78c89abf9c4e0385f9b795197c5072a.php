<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['property' => 'search', 'value' => '', 'placeholder' => 'Search…', 'label' => 'Search']));

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

foreach (array_filter((['property' => 'search', 'value' => '', 'placeholder' => 'Search…', 'label' => 'Search']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="ft-jl-control ft-jl-control-search" x-data x-on:keydown.window="if ($event.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement?.tagName)) { $event.preventDefault(); $refs.search.focus(); }">
    <label><?php echo e($label); ?></label>
    <div class="ft-jl-search-wrap">
        <span class="ft-jl-search-icon" aria-hidden="true">⌕</span>
        
        <input x-ref="search" class="ft-jl-search" type="text" role="searchbox" inputmode="search" wire:model.live.debounce.300ms="<?php echo e($property); ?>" placeholder="<?php echo e($placeholder); ?>" autocomplete="off" x-on:keydown.escape="if ($el.value) { $wire.set(<?php echo \Illuminate\Support\Js::from($property)->toHtml() ?>, ''); }">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($value)): ?><button type="button" class="ft-jl-clear-search" wire:click="$set('<?php echo e($property); ?>', '')" aria-label="Clear search">×</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/list-search.blade.php ENDPATH**/ ?>