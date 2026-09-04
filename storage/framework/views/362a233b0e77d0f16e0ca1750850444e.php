<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'stageName' => null,
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
    'job',
    'stageName' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $richText = app(\App\Services\RichTextService::class);
    $reason = trim((string) ($job->cancellation_reason ?? ''));
    $plainReason = $richText->plainText($reason);
    $safeHtml = $richText->safeHtml($reason);
    $imageCount = $safeHtml ? preg_match_all('/<img\b/i', $safeHtml) : 0;

    $previewText = trim((string) preg_replace('/\s*\[Image\]\s*/i', ' ', $plainReason));
    $previewText = trim((string) preg_replace('/\s+/', ' ', $previewText));

    if ($previewText === '') {
        $previewText = $imageCount > 0
            ? 'Cancellation evidence was attached.'
            : 'No written cancellation reason was recorded.';
    }

    $previewText = \Illuminate\Support\Str::limit($previewText, 165);
    $cancelledAt = $job->cancelled_at ?: $job->updated_at;
    $cancelledByName = trim((string) ($job->cancelledBy?->name ?? 'System')) ?: 'System';
    $cancelledByInitial = mb_strtoupper(mb_substr($cancelledByName, 0, 1));
    $resolvedStageName = trim((string) ($stageName ?: $job->phase?->name ?: '—'));
?>

<section
    class="ft-order-cancellation-card"
    x-data="{ expanded: false }"
    aria-label="Order cancellation details"
>
    <div class="ft-order-cancellation-summary">
        <div class="ft-order-cancellation-icon" aria-hidden="true">⊘</div>

        <div class="ft-order-cancellation-main">
            <div class="ft-order-cancellation-heading-row">
                <div>
                    <div class="ft-order-cancellation-eyebrow">Cancellation record</div>
                    <h2>Order cancelled</h2>
                </div>

                <span class="ft-order-cancellation-status">Workflow locked</span>
            </div>

            <p class="ft-order-cancellation-preview"><?php echo e($previewText); ?></p>

            <div class="ft-order-cancellation-meta" aria-label="Cancellation metadata">
                <span class="ft-order-cancellation-meta-item">
                    <span class="ft-order-cancellation-avatar" aria-hidden="true"><?php echo e($cancelledByInitial); ?></span>
                    <span>
                        <small>Cancelled by</small>
                        <strong><?php echo e($cancelledByName); ?></strong>
                    </span>
                </span>

                <span class="ft-order-cancellation-meta-item">
                    <span class="ft-order-cancellation-meta-icon" aria-hidden="true">◷</span>
                    <span>
                        <small>Cancelled on</small>
                        <strong><?php echo e($cancelledAt ? \App\Support\UserLocalTime::format($cancelledAt, 'M j, Y · g:i A') : '—'); ?></strong>
                    </span>
                </span>

                <span class="ft-order-cancellation-meta-item">
                    <span class="ft-order-cancellation-meta-icon" aria-hidden="true">▣</span>
                    <span>
                        <small>Last stage</small>
                        <strong><?php echo e($resolvedStageName); ?></strong>
                    </span>
                </span>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageCount > 0): ?>
                    <span class="ft-order-cancellation-meta-item">
                        <span class="ft-order-cancellation-meta-icon" aria-hidden="true">▧</span>
                        <span>
                            <small>Evidence</small>
                            <strong><?php echo e($imageCount); ?> <?php echo e(\Illuminate\Support\Str::plural('image', $imageCount)); ?></strong>
                        </span>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <button
            type="button"
            class="ft-order-cancellation-toggle"
            x-on:click="expanded = !expanded"
            x-bind:aria-expanded="expanded ? 'true' : 'false'"
            aria-controls="order-cancellation-details-<?php echo e($job->id); ?>"
        >
            <span x-text="expanded ? 'Hide details' : 'View details'">View details</span>
            <span class="ft-order-cancellation-chevron" aria-hidden="true" x-bind:class="{ 'is-open': expanded }">⌄</span>
        </button>
    </div>

    <div
        id="order-cancellation-details-<?php echo e($job->id); ?>"
        class="ft-order-cancellation-details"
        x-cloak
        x-show="expanded"
        x-transition.opacity.duration.150ms
    >
        <div class="ft-order-cancellation-detail-block">
            <div class="ft-order-cancellation-detail-title">
                <span>Reason & evidence</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageCount > 0): ?>
                    <small>Click an image to enlarge it</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason !== ''): ?>
                <div class="ft-rich-text-content ft-order-cancellation-rich-content">
                    <?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $reason]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reason)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $attributes = $__attributesOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__attributesOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d83f45bf838052fadc84bf85b829e43)): ?>
<?php $component = $__componentOriginal1d83f45bf838052fadc84bf85b829e43; ?>
<?php unset($__componentOriginal1d83f45bf838052fadc84bf85b829e43); ?>
<?php endif; ?>
                </div>
            <?php else: ?>
                <p class="ft-order-cancellation-empty">No written reason was recorded for this cancellation.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-order-cancellation-note">
            <span aria-hidden="true">ⓘ</span>
            <span>The order is retained as history, while workflow progression remains blocked. Use <strong>Initiate Redo</strong> when a controlled replacement order is required.</span>
        </div>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/cancellation-card.blade.php ENDPATH**/ ?>