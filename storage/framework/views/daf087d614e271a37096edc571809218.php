<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'supplier',
    'products' => collect(),
    'displayTimezone' => null,
    'canEdit' => false,
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
    'supplier',
    'products' => collect(),
    'displayTimezone' => null,
    'canEdit' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $products = collect($products);
    $name = trim((string) $supplier->name);
    $initials = collect(preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY))
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $contact = trim((string) data_get($supplier->metadata, 'contact_person'));
    $email = trim((string) data_get($supplier->metadata, 'email'));
    $updatedAt = $supplier->updated_at;
    if ($updatedAt && $displayTimezone) $updatedAt = $updatedAt->copy()->timezone($displayTimezone);
?>

<tr>
    <td>
        <div class="ft-supplier-list-entity">
            <span class="ft-supplier-list-avatar" aria-hidden="true"><?php echo e($initials ?: 'S'); ?></span>
            <span class="ft-supplier-list-entity-copy">
                <a href="<?php echo e(route('master-data', ['group' => 'supplier', 'supplier' => $supplier->id])); ?>" wire:navigate><?php echo e($supplier->name); ?></a>
                <small><?php echo e(number_format($products->count())); ?> product<?php echo e($products->count() === 1 ? '' : 's'); ?></small>
            </span>
        </div>
    </td>
    <td>
        <div class="ft-supplier-list-contact">
            <strong><?php echo e($contact !== '' ? $contact : '—'); ?></strong>
            <small><?php echo e($email !== '' ? $email : 'No email'); ?></small>
        </div>
    </td>
    <td><?php if (isset($component)) { $__componentOriginalbcded1d9601bed6242d6290d46330e2e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbcded1d9601bed6242d6290d46330e2e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.product-tags','data' => ['products' => $products]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.product-tags'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbcded1d9601bed6242d6290d46330e2e)): ?>
<?php $attributes = $__attributesOriginalbcded1d9601bed6242d6290d46330e2e; ?>
<?php unset($__attributesOriginalbcded1d9601bed6242d6290d46330e2e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbcded1d9601bed6242d6290d46330e2e)): ?>
<?php $component = $__componentOriginalbcded1d9601bed6242d6290d46330e2e; ?>
<?php unset($__componentOriginalbcded1d9601bed6242d6290d46330e2e); ?>
<?php endif; ?></td>
    <td><?php if (isset($component)) { $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.suppliers.status-badge','data' => ['status' => $supplier->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('suppliers.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->status)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $attributes = $__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__attributesOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51)): ?>
<?php $component = $__componentOriginale8c306dfc6cd88f394d6c49410ad1f51; ?>
<?php unset($__componentOriginale8c306dfc6cd88f394d6c49410ad1f51); ?>
<?php endif; ?></td>
    <td class="ft-supplier-list-updated"><?php echo e($updatedAt?->format('M j, Y') ?? '—'); ?></td>
    <td class="ft-supplier-list-row-action">
        <div class="ft-supplier-list-row-actions">
            <a
                href="<?php echo e(route('master-data', ['group' => 'supplier', 'supplier' => $supplier->id])); ?>"
                wire:navigate
                class="ft-supplier-list-link"
            >View</a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
                <a
                    href="<?php echo e(route('master-data', ['group' => 'supplier', 'edit_supplier' => $supplier->id])); ?>"
                    wire:navigate
                    class="ft-supplier-list-link"
                >Edit</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/suppliers/list-row.blade.php ENDPATH**/ ?>