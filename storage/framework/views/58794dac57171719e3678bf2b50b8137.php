<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value' => 0,
    'icon' => 'created',
    'tone' => 'teal',
    'caption' => '',
    'active' => false,
    'displayValue' => null,
    'valueExpression' => null,
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
    'label',
    'value' => 0,
    'icon' => 'created',
    'tone' => 'teal',
    'caption' => '',
    'active' => false,
    'displayValue' => null,
    'valueExpression' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if (! $__env->hasRenderedOnce('64ef0629-752e-4cfd-9caa-c61e822ec11e')): $__env->markAsRenderedOnce('64ef0629-752e-4cfd-9caa-c61e822ec11e'); ?>
    <style>
        .ft-summary-card-grid{display:grid!important;grid-template-columns:repeat(var(--ft-summary-columns,6),minmax(0,1fr))!important;gap:10px!important;margin-bottom:12px!important}
        .ft-summary-card{--ft-summary-accent:#0f8f8a;--ft-summary-soft:#edf9f8;position:relative!important;display:grid!important;grid-template-columns:minmax(0,1fr) 36px!important;grid-template-rows:auto 1fr auto!important;gap:2px 8px!important;width:100%!important;min-width:0!important;min-height:92px!important;margin:0!important;padding:10px 12px!important;overflow:hidden!important;border:1px solid #dbe3ed!important;border-radius:11px!important;background:#fff!important;color:#172033!important;text-align:left!important;font:inherit!important;appearance:none!important;cursor:pointer!important;transition:border-color .15s ease,background-color .15s ease,box-shadow .15s ease!important;transform:none!important;box-shadow:none!important}
        .ft-summary-card:hover{border-color:#cad6e5!important;background:#fff!important;transform:none!important;box-shadow:none!important}
        .ft-summary-card:focus-visible{outline:2px solid color-mix(in srgb,var(--ft-summary-accent) 50%,white)!important;outline-offset:2px!important}
        .ft-summary-card.is-active,.ft-summary-card.is-active:hover{border-color:var(--ft-summary-accent)!important;background:color-mix(in srgb,var(--ft-summary-soft) 60%,white)!important;box-shadow:0 0 0 1px color-mix(in srgb,var(--ft-summary-accent) 12%,transparent)!important}
        .ft-summary-card.tone-slate{--ft-summary-accent:#526174;--ft-summary-soft:#f2f4f7}
        .ft-summary-card.tone-blue{--ft-summary-accent:#147d88;--ft-summary-soft:#edf9fa}
        .ft-summary-card.tone-amber{--ft-summary-accent:#c47a00;--ft-summary-soft:#fff7e8}
        .ft-summary-card.tone-green{--ft-summary-accent:#21823b;--ft-summary-soft:#eef9f0}
        .ft-summary-card.tone-red{--ft-summary-accent:#c82424;--ft-summary-soft:#fff0f0}
        .ft-summary-card.tone-purple{--ft-summary-accent:#7253c9;--ft-summary-soft:#f4f0ff}
        .ft-summary-card-label{grid-column:1;grid-row:1;display:block!important;min-width:0!important;padding-top:2px!important;overflow:hidden!important;color:#344155!important;font-size:10.5px!important;font-weight:700!important;line-height:1.25!important;text-overflow:ellipsis!important;white-space:normal!important}
        .ft-summary-card-icon{grid-column:2;grid-row:1 / span 2;display:grid!important;width:36px!important;height:36px!important;place-items:center!important;justify-self:end!important;border:1px solid color-mix(in srgb,var(--ft-summary-accent) 20%,#e5e7eb)!important;border-radius:10px!important;background:var(--ft-summary-soft)!important;color:var(--ft-summary-accent)!important;font-style:normal!important;line-height:1!important}
        .ft-summary-card-icon svg{width:18px!important;height:18px!important;stroke:currentColor!important;stroke-width:1.8!important;fill:none!important;stroke-linecap:round!important;stroke-linejoin:round!important}
        .ft-summary-card-value{grid-column:1;grid-row:2;align-self:end!important;display:block!important;margin:3px 0 0!important;color:var(--ft-summary-accent)!important;font-size:23px!important;font-weight:800!important;line-height:1!important;letter-spacing:-.025em!important}
        .ft-summary-card-caption{grid-column:1 / -1;grid-row:3;display:block!important;min-width:0!important;margin-top:6px!important;overflow:hidden!important;color:#6b788c!important;font-size:8.8px!important;font-weight:500!important;line-height:1.25!important;text-overflow:ellipsis!important;white-space:nowrap!important}
        @media(max-width:1180px){.ft-summary-card-grid{grid-template-columns:repeat(3,minmax(0,1fr))!important}.ft-summary-card-grid.ft-summary-card-grid-4{grid-template-columns:repeat(2,minmax(0,1fr))!important}.ft-summary-card{min-height:92px!important}}
        @media(max-width:720px){.ft-summary-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:8px!important}.ft-summary-card{min-height:92px!important;padding:10px 11px!important}.ft-summary-card-value{font-size:22px!important}}
        @media(max-width:430px){.ft-summary-card-grid{grid-template-columns:1fr!important}.ft-summary-card{min-height:84px!important;grid-template-rows:auto auto auto!important}.ft-summary-card-caption{white-space:normal!important}}
    </style>
<?php endif; ?>

<?php
    $icons = [
        'created' => '<path d="M12 3v6m-3-3h6"/><path d="M5 3h3m8 0h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2"/>',
        'not-started' => '<circle cx="12" cy="12" r="9"/><path d="M9 9v6m6-6v6"/>',
        'in-progress' => '<circle cx="12" cy="12" r="9"/><path d="m10 8 4 4-4 4m-3-4h7"/>',
        'due-week' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'completed' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>',
        'attention' => '<path d="M5 4v16m0-14h10l-1.5 3L15 12H5"/><path d="M19 6v4"/>',
        'clients' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'orders' => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 11h18M10 11v2h4v-2"/>',
        'money' => '<circle cx="12" cy="12" r="9"/><path d="M16 8.5c-.8-.9-2-1.5-3.5-1.5-1.9 0-3.5 1-3.5 2.5s1.4 2.1 3.4 2.5c2 .4 3.6 1 3.6 2.5S14.4 17 12.5 17C11 17 9.6 16.4 8.7 15.4M12 5v14"/>',
    ];
?>

<button
    type="button"
    <?php echo e($attributes->class(['metric-filter-card', 'ft-summary-card', 'tone-'.$tone, 'active' => $active, 'is-active' => $active])); ?>

>
    <span class="ft-summary-card-label"><?php echo e($label); ?></span>
    <i class="ft-summary-card-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><?php echo $icons[$icon] ?? $icons['created']; ?></svg>
    </i>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($valueExpression): ?>
        <strong class="ft-summary-card-value" x-text="<?php echo e($valueExpression); ?>"><?php echo e($displayValue !== null ? $displayValue : number_format((int) $value)); ?></strong>
    <?php else: ?>
        <strong class="ft-summary-card-value"><?php echo e($displayValue !== null ? $displayValue : number_format((int) $value)); ?></strong>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <small class="ft-summary-card-caption"><?php echo e($caption); ?></small>
</button>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/summary-card.blade.php ENDPATH**/ ?>