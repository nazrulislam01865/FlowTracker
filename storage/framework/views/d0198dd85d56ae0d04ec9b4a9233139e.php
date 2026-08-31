<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'canEditJob' => false,
    'shipmentUrgencyOptions' => collect(),
    'context' => [],
    'variant' => 'planning',
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
    'canEditJob' => false,
    'shipmentUrgencyOptions' => collect(),
    'context' => [],
    'variant' => 'planning',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $urgencyId = (string) ($context['shipmentUrgencyId'] ?? '');
    $urgencyName = (string) ($context['shipmentUrgencyName'] ?? 'Normal Service');
    $tone = (string) ($context['shipmentUrgencyTone'] ?? 'normal');
    $options = collect($shipmentUrgencyOptions)
        ->map(fn ($option) => [
            'id' => (int) data_get($option, 'id'),
            'name' => (string) data_get($option, 'name'),
        ])
        ->filter(fn ($option) => $option['id'] > 0 && $option['name'] !== '')
        ->values();
    $keySuffix = $variant === 'header' ? 'header-shipment-urgency' : 'planning-shipment-urgency';
?>

<div
    <?php echo e($attributes->class([
        'ft-order-urgency-inline',
        'ft-order-urgency-inline--header' => $variant === 'header',
        'ft-order-urgency-inline--planning' => $variant !== 'header',
        'ft-inline-edit-shell',
    ])); ?>

    x-data="{
        ...window.FlowTrack.ui.inlineEdit({
            key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-'.$keySuffix)->toHtml() ?>,
            label: 'shipment urgency',
            value: <?php echo \Illuminate\Support\Js::from($urgencyId)->toHtml() ?>,
            display: <?php echo \Illuminate\Support\Js::from($urgencyName)->toHtml() ?>
        }),
        options: <?php echo \Illuminate\Support\Js::from($options->all())->toHtml() ?>,
        currentTone: <?php echo \Illuminate\Support\Js::from($tone)->toHtml() ?>,
        toneFor(name) {
            const normalized = String(name || '').toLowerCase();
            if (normalized.includes('super')) return 'super-urgent';
            if (normalized.includes('urgent')) return 'urgent';
            return 'normal';
        },
        syncUrgency(detail) {
            if (!detail || Number(detail.jobId) !== Number(<?php echo e($job->id); ?>)) return;
            const nextId = String(detail.id || '');
            const nextName = String(detail.name || 'Normal Service');
            this.value = nextId;
            this.draftValue = nextId;
            this.display = nextName;
            this.currentTone = this.toneFor(nextName);
        },
        nameFor(id) {
            const found = this.options.find((option) => String(option.id) === String(id));
            return found ? found.name : 'Normal Service';
        },
        async saveUrgency() {
            const nextId = String(this.draftValue || '');
            const ok = await this.commit(
                nextId,
                this.nameFor(nextId),
                () => $wire.updateJobUrgencies(<?php echo e($job->id); ?>, 'shipment', nextId ? [Number(nextId)] : [])
            );

            if (ok) {
                const nextName = this.nameFor(nextId);
                this.syncUrgency({ jobId: <?php echo e($job->id); ?>, id: nextId, name: nextName });
                window.dispatchEvent(new CustomEvent('ft-shipment-urgency-updated', {
                    detail: { jobId: <?php echo e($job->id); ?>, id: nextId, name: nextName }
                }));
                await $wire.$refresh();
            }
        }
    }"
    :class="{
        'is-inline-saving': status === 'saving',
        'is-inline-error': status === 'error',
        'is-editing': editing
    }"
    x-on:click.outside="if (editing && status !== 'saving') cancelEdit()"
    x-on:ft-shipment-urgency-updated.window="syncUrgency($event.detail)"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-'.e($job->id).'-'.e($keySuffix).'-'.e($urgencyId ?: 'normal').''; ?>wire:key="job-<?php echo e($job->id); ?>-<?php echo e($keySuffix); ?>-<?php echo e($urgencyId ?: 'normal'); ?>"
>
    <span x-show="!editing" class="ft-order-urgency-display">
        <span
            class="urgency-badge"
            :class="{ 'su': currentTone === 'super-urgent', 'u': currentTone === 'urgent', 'n': currentTone === 'normal' }"
            x-text="display"
            title="Shipment urgency controls packing, carrier-booking, and dispatch priority."
        ><?php echo e($urgencyName); ?></span>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
            <button
                type="button"
                class="inline-edit <?php echo e($variant === 'header' ? 'ft-order-command-icon-btn' : ''); ?>"
                x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.urgencySelect.focus())"
                title="Edit shipment urgency"
                aria-label="Edit shipment urgency"
            >✎</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditJob): ?>
        <span x-cloak x-show="editing" class="ft-order-urgency-editor">
            <select
                x-ref="urgencySelect"
                x-model="draftValue"
                :disabled="status === 'saving'"
                x-on:keydown.escape.prevent.stop="cancelEdit()"
                x-on:keydown.enter.prevent.stop="saveUrgency()"
                aria-label="Shipment urgency"
            >
                <option value="">Normal Service</option>
                <template x-for="option in options" :key="option.id">
                    <option :value="String(option.id)" x-text="option.name"></option>
                </template>
            </select>

            <span class="ft-order-urgency-editor-actions">
                <button
                    type="button"
                    class="ft-order-inline-icon-action ft-order-inline-icon-action--confirm"
                    :disabled="status === 'saving'"
                    x-on:click.stop="saveUrgency()"
                    title="Save shipment urgency"
                    aria-label="Save shipment urgency"
                ><span x-show="status !== 'saving'">✓</span><span x-cloak x-show="status === 'saving'">…</span></button>

                <button
                    type="button"
                    class="ft-order-inline-icon-action ft-order-inline-icon-action--cancel"
                    :disabled="status === 'saving'"
                    x-on:click.stop="cancelEdit()"
                    title="Cancel shipment urgency edit"
                    aria-label="Cancel shipment urgency edit"
                >×</button>
            </span>
        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/shipment-urgency-inline.blade.php ENDPATH**/ ?>