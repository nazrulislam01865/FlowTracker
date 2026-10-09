<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'holdFrom' => 'client',
    'reason' => '',
    'mentionUsers' => collect(),
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
    'holdFrom' => 'client',
    'reason' => '',
    'mentionUsers' => collect(),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $holdOptions = \App\Models\OrderHold::HOLD_FROM_OPTIONS;
    $mentionOptions = collect($mentionUsers)->values()->all();
?>
<div
    class="ft-order-modal-backdrop ft-order-hold-modal-backdrop"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-hold-modal-'.e($job->id).''; ?>wire:key="order-hold-modal-<?php echo e($job->id); ?>"
    wire:click.self="closeOrderHoldModal"
>
    <section
        class="ft-order-modal ft-order-hold-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="order-hold-modal-title"
        x-data="{ selected: <?php echo \Illuminate\Support\Js::from($holdFrom)->toHtml() ?>, reason: <?php echo \Illuminate\Support\Js::from($reason)->toHtml() ?> }"
        x-on:keydown.escape.window="$wire.closeOrderHoldModal()"
    >
        <header class="ft-order-hold-modal__header">
            <div>
                <h2 id="order-hold-modal-title">Hold order</h2>
                <p>Pause this order and lock all order and task activities until the order is unheld.</p>
            </div>
            <button type="button" wire:click="closeOrderHoldModal" aria-label="Close hold order dialog">×</button>
        </header>

        <div class="ft-order-modal-body ft-order-hold-modal__body">
            <fieldset class="ft-order-hold-fieldset">
                <legend>Hold from <span aria-hidden="true">*</span></legend>
                <div class="ft-order-hold-options">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $holdOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="ft-order-hold-option" :class="selected === <?php echo \Illuminate\Support\Js::from($value)->toHtml() ?> ? 'is-selected' : ''">
                            <input
                                type="radio"
                                name="order_hold_from"
                                value="<?php echo e($value); ?>"
                                x-model="selected"
                                wire:model="orderHoldFrom"
                            >
                            <span class="ft-order-hold-option__radio" aria-hidden="true"></span>
                            <span><?php echo e($label); ?></span>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderHoldFrom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="validation-error"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </fieldset>

            <div class="ft-order-hold-reason-field ft-mention-host">
                <div class="ft-order-hold-reason-label-row">
                    <label for="order-hold-reason">Reason <span class="ft-order-hold-optional">Optional</span></label>
                    <span>Type <b>@</b> to mention a user</span>
                </div>
                <textarea
                    id="order-hold-reason"
                    class="ft-mention-input"
                    rows="5"
                    maxlength="500"
                    autocomplete="off"
                    x-model="reason"
                    wire:model="orderHoldReason"
                    data-mention-users="<?php echo e(json_encode($mentionOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
                    placeholder="Add an optional reason or mention someone with @..."
                ></textarea>
                <div class="ft-order-hold-reason-count"><span x-text="reason.length"><?php echo e(mb_strlen($reason)); ?></span>/500</div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['orderHoldReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="validation-error"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <footer class="ft-order-hold-modal__footer">
            <button type="button" class="secondary" wire:click="closeOrderHoldModal" wire:loading.attr="disabled" wire:target="placeOrderOnHold" data-ft-feedback="off">
                Cancel
            </button>
            <button type="button" class="ft-order-hold-submit" wire:click="placeOrderOnHold" wire:loading.attr="disabled" wire:target="placeOrderOnHold" data-ft-feedback="off">
                <span wire:loading.remove wire:target="placeOrderOnHold" class="ft-order-hold-button-content">
                    <span class="ft-order-hold-pause-icon" aria-hidden="true"><i></i><i></i></span>
                    <span>Hold order</span>
                </span>
                <span wire:loading wire:target="placeOrderOnHold" class="ft-order-hold-button-content">
                    <span class="ft-order-hold-mini-spinner" aria-hidden="true"></span>
                    <span>Holding...</span>
                </span>
            </button>
        </footer>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/jobs/order-detail/hold-modal.blade.php ENDPATH**/ ?>