<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'task' => null,
    'details' => [],
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
    'task' => null,
    'details' => [],
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
    $supplierDeliveryDate = trim((string) ($details['supplierDeliveryDate'] ?? ''));
    $productionIssueNote = trim((string) ($details['productionIssueNote'] ?? ''));
    $displayDate = '—';
    if ($supplierDeliveryDate !== '') {
        try {
            $displayDate = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $supplierDeliveryDate)->format('d/m/Y');
        } catch (\Throwable $exception) {
            $displayDate = $supplierDeliveryDate;
        }
    }
    $canInlineEdit = (bool) $canEdit && $task;
?>

<div class="ft-production-monitor-summary" aria-label="Saved production monitor details">
    <div
        class="ft-production-monitor-summary-date ft-inline-edit-shell"
        x-data="{
            ...window.FlowTrack.ui.inlineEdit({
                key: <?php echo \Illuminate\Support\Js::from('production-monitor-'.($task?->id ?? 'saved').'-supplier-date')->toHtml() ?>,
                label: 'supplier delivery date',
                value: <?php echo \Illuminate\Support\Js::from($supplierDeliveryDate)->toHtml() ?>,
                display: <?php echo \Illuminate\Support\Js::from($displayDate)->toHtml() ?>
            }),
            formatDmy(value) {
                if (!value) return '—';
                const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})$/);
                return match ? `${match[3]}/${match[2]}/${match[1]}` : String(value);
            }
        }"
        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
    >
        <span class="ft-production-monitor-summary-label">Supplier Delivery Date</span>
        <div class="ft-production-monitor-summary-date-value">
            <div x-show="!editing" class="ft-production-monitor-summary-inline-display">
                <strong class="ft-production-monitor-summary-value" x-text="display"><?php echo e($displayDate); ?></strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canInlineEdit): ?>
                    <button
                        type="button"
                        class="ft-inline-edit-button compact ft-production-monitor-summary-edit"
                        title="Edit supplier delivery date"
                        aria-label="Edit supplier delivery date"
                        :disabled="status === 'saving'"
                        x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.productionMonitorDate.focus({ preventScroll: true }); if (typeof $refs.productionMonitorDate.showPicker === 'function') { try { $refs.productionMonitorDate.showPicker(); } catch (e) {} } })"
                    >✎</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canInlineEdit): ?>
                <input
                    x-ref="productionMonitorDate"
                    x-cloak
                    x-show="editing"
                    x-model="draftValue"
                    type="date"
                    class="ft-production-monitor-summary-date-input ft-prototype-clickable-date"
                    onclick="this.focus({ preventScroll: true }); if (typeof this.showPicker === 'function') { try { this.showPicker(); } catch (e) {} }"
                    x-on:keydown.escape.prevent="cancelEdit()"
                    x-on:blur="if (editing) cancelEdit()"
                    x-on:change="commit($event.target.value, formatDmy($event.target.value), () => $wire.updateCompletedProductionMonitorSupplierDate(<?php echo e((int) $task->id); ?>, draftValue))"
                    aria-label="Supplier Delivery Date. Click anywhere in the field to open the calendar."
                >
                <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div
        class="ft-production-monitor-summary-note ft-inline-edit-shell"
        x-data="window.FlowTrack.ui.inlineEdit({
            key: <?php echo \Illuminate\Support\Js::from('production-monitor-'.($task?->id ?? 'saved').'-issue-note')->toHtml() ?>,
            label: 'production issue note',
            value: <?php echo \Illuminate\Support\Js::from($productionIssueNote)->toHtml() ?>,
            display: <?php echo \Illuminate\Support\Js::from($productionIssueNote !== '' ? $productionIssueNote : '—')->toHtml() ?>
        })"
        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
    >
        <div class="ft-production-monitor-summary-note-head">
            <span class="ft-production-monitor-summary-label">Production Issue Note <em>(Optional)</em></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canInlineEdit): ?>
                <button
                    x-show="!editing"
                    type="button"
                    class="ft-inline-edit-button compact ft-production-monitor-summary-edit"
                    title="Edit production issue note"
                    aria-label="Edit production issue note"
                    :disabled="status === 'saving'"
                    x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.productionMonitorNote.focus())"
                >✎</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div x-show="!editing" class="ft-production-monitor-summary-note-value" x-text="display"><?php echo e($productionIssueNote !== '' ? $productionIssueNote : '—'); ?></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canInlineEdit): ?>
            <div x-cloak x-show="editing" class="ft-production-monitor-summary-note-editor">
                <textarea
                    x-ref="productionMonitorNote"
                    x-model="draftValue"
                    rows="3"
                    maxlength="10000"
                    class="ft-production-monitor-summary-note-input"
                    placeholder="Add note about the production issue, resolution, or communication with supplier..."
                    x-on:keydown.escape.prevent="cancelEdit()"
                ></textarea>
                <div class="ft-production-monitor-summary-note-actions">
                    <button type="button" class="btn small" x-on:click="cancelEdit()">Cancel</button>
                    <button
                        type="button"
                        class="btn small primary"
                        :disabled="status === 'saving'"
                        x-on:click="commit(draftValue, String(draftValue || '').trim() || '—', () => $wire.updateCompletedProductionMonitorIssueNote(<?php echo e((int) $task->id); ?>, draftValue))"
                    >Save</button>
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/production-monitor-summary.blade.php ENDPATH**/ ?>