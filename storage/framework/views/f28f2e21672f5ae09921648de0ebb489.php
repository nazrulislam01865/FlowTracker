<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'model',
    'options' => [],
    'disabled' => false,
    'placeholder' => 'Select roles',
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
    'model',
    'options' => [],
    'disabled' => false,
    'placeholder' => 'Select roles',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $componentId = 'ft-multi-role-'.md5($model.'-'.json_encode($options));
    $normalizedOptions = collect($options)->map(fn ($role) => [
        'id' => (string) data_get($role, 'id'),
        'name' => (string) data_get($role, 'name'),
    ])->values()->all();
?>
<div
    class="ft-multi-role-select"
    x-data="{
        open: false,
        search: '',
        selected: $wire.entangle('<?php echo e($model); ?>').live,
        options: <?php echo \Illuminate\Support\Js::from($normalizedOptions)->toHtml() ?>,
        nameFor(id) { return this.options.find(role => String(role.id) === String(id))?.name || String(id); },
        visible(role) { return !this.search || role.name.toLowerCase().includes(this.search.toLowerCase()); }
    }"
    x-on:keydown.escape.window="open = false"
>
    <button id="<?php echo e($componentId); ?>" type="button" class="ft-multi-role-trigger" x-on:click="open = !open" <?php if($disabled): echo 'disabled'; endif; ?>>
        <span class="ft-multi-role-chips" x-show="selected && selected.length">
            <template x-for="roleId in selected" :key="String(roleId)">
                <span class="ft-multi-role-chip" x-text="nameFor(roleId)"></span>
            </template>
        </span>
        <span class="ft-multi-role-placeholder" x-show="!selected || !selected.length"><?php echo e($placeholder); ?></span>
        <span class="ft-multi-role-caret" aria-hidden="true">⌄</span>
    </button>

    <div class="ft-multi-role-menu" x-show="open" x-cloak x-on:click.outside="open = false">
        <div class="ft-multi-role-search-wrap">
            <input type="search" x-model="search" placeholder="Search roles..." aria-label="Search roles">
        </div>
        <div class="ft-multi-role-options">
            <template x-for="role in options" :key="role.id">
                <label class="ft-multi-role-option" x-show="visible(role)">
                    <input type="checkbox" :value="role.id" x-model="selected" <?php if($disabled): echo 'disabled'; endif; ?>>
                    <span x-text="role.name"></span>
                </label>
            </template>
            <div class="ft-multi-role-empty" x-show="options.filter(role => visible(role)).length === 0">No roles found.</div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/multi-role-select.blade.php ENDPATH**/ ?>