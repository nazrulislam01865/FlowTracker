<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job']));

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

foreach (array_filter((['job']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<section class="ft-detail-card"><h2>Commercial</h2><div class="ft-overview-metrics"><div class="ft-overview-metric"><span class="ft-metric-icon red">$</span><div><small>Commercial value</small><b><?php echo e($job->commercial_value > 0 ? $job->currency.' '.number_format($job->commercial_value,0) : 'Quotation pending'); ?></b><p><?php echo e($job->commercial_value > 0 ? 'Recorded' : 'Not recorded yet'); ?></p></div></div><div class="ft-overview-metric"><span class="ft-metric-icon blue">▣</span><div><small>Invoice status</small><b><?php echo e($job->commercial_value > 0 ? 'Draft' : 'Not created'); ?></b><p><?php echo e($job->phase?->short_name); ?></p></div></div><div class="ft-overview-metric"><span class="ft-metric-icon purple">⌘</span><div><small>Client balance</small><b><?php echo e($job->currency); ?> <?php echo e(number_format($job->client?->outstanding_balance ?? 0,0)); ?></b><p><?php echo e($job->client?->name); ?></p></div></div></div></section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-commercial.blade.php ENDPATH**/ ?>