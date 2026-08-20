<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'number' => null,
    'shipmentUrgencies' => collect(),
    'selectedUrgencies' => [],
    'pickerOpen' => false,
    'pickerSelection' => [],
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
    'number' => null,
    'shipmentUrgencies' => collect(),
    'selectedUrgencies' => [],
    'pickerOpen' => false,
    'pickerSelection' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $urgencies = collect($shipmentUrgencies)->values();
    $urgenciesById = $urgencies->keyBy(fn ($urgency) => (int) $urgency->id);
    $selectedIds = collect($selectedUrgencies)
        ->pluck('shipment_urgency_id')
        ->map(fn ($value) => (int) $value)
        ->filter()
        ->values();
    $pendingIds = collect($pickerSelection)->map(fn ($value) => (int) $value)->filter()->unique()->values();
?>
<?php if (isset($component)) { $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.product-section','data' => ['number' => $number,'title' => 'Shipping urgencies','subtitle' => 'Choose only the urgency levels this product supports. Add a product-specific extra charge when needed.','class' => 'ft-product-shipping-section']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.product-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['number' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($number),'title' => 'Shipping urgencies','subtitle' => 'Choose only the urgency levels this product supports. Add a product-specific extra charge when needed.','class' => 'ft-product-shipping-section']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedIds->isNotEmpty()): ?>
        <div class="ft-product-shipping-selected-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedUrgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urgencyIndex => $selectedUrgency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $urgencyId = (int) data_get($selectedUrgency, 'shipment_urgency_id', 0);
                    $urgency = $urgenciesById->get($urgencyId);
                    $urgencyName = $urgency?->name ?: 'Shipping urgency';
                    $urgencyCode = $urgency?->code ?: '';
                    $urgencyColor = \App\Support\MasterColor::normalize($urgency?->color) ?: '#00897B';
                ?>
                <article class="ft-product-shipping-selected-card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-shipment-urgency-'.e(data_get($selectedUrgency, 'key', $urgencyIndex)).''; ?>wire:key="product-shipment-urgency-<?php echo e(data_get($selectedUrgency, 'key', $urgencyIndex)); ?>">
                    <div class="ft-product-shipping-selected-head">
                        <span class="ft-product-shipping-selected-icon" style="--urgency-color: <?php echo e($urgencyColor); ?>">↗</span>
                        <div>
                            <strong><?php echo e($urgencyName); ?></strong>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urgencyCode): ?><small><?php echo e($urgencyCode); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button type="button" class="ft-product-shipping-card-remove" wire:click="removeProductShipmentUrgency(<?php echo e($urgencyIndex); ?>)" aria-label="Remove <?php echo e($urgencyName); ?>">×</button>
                    </div>
                    <label class="ft-product-field ft-product-shipping-card-charge">
                        <span>Extra charge <em>Optional</em></span>
                        <input type="number" min="0" step="0.01" inputmode="decimal" wire:model.blur="productShipmentUrgencies.<?php echo e($urgencyIndex); ?>.extra_charge" placeholder="0.00">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productShipmentUrgencies.'.$urgencyIndex.'.extra_charge'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productShipmentUrgencies.'.$urgencyIndex.'.shipment_urgency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="ft-product-shipping-empty-state">
            <span class="ft-product-shipping-empty-icon">↗</span>
            <div>
                <strong>No shipping urgencies added</strong>
                <small>Add only the urgency levels available for this product.</small>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-product-shipping-actions">
        <button
            type="button"
            class="ft-product-shipping-add"
            wire:click="addProductShipmentUrgency"
            <?php if($urgencies->isEmpty() || $selectedIds->count() >= 20): echo 'disabled'; endif; ?>
        >
            <span>+</span> Add shipping urgency
        </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedIds->isNotEmpty()): ?>
            <small><?php echo e($selectedIds->count()); ?> selected</small>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urgencies->isEmpty()): ?>
        <div class="ft-product-shipping-master-empty">Add active Shipment Urgencies in Master Data before assigning them to a product.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['productShipmentUrgencies'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><b class="validation-error ft-product-shipping-error"><?php echo e($message); ?></b><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pickerOpen): ?>
        <div class="ft-product-shipping-picker-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-shipping-urgency-picker'; ?>wire:key="product-shipping-urgency-picker" wire:click.self="closeProductShipmentUrgencyPicker">
            <section class="ft-product-shipping-picker" role="dialog" aria-modal="true" aria-labelledby="product-shipping-urgency-picker-title" x-data="{ urgencySearch: '' }" x-on:keydown.escape.window="$wire.closeProductShipmentUrgencyPicker()">
                <header class="ft-product-shipping-picker-head">
                    <div>
                        <span class="ft-product-shipping-picker-kicker">Shipping setup</span>
                        <h3 id="product-shipping-urgency-picker-title">Add shipping urgencies</h3>
                        <p>Select one or more urgency levels for this product.</p>
                    </div>
                    <button type="button" wire:click="closeProductShipmentUrgencyPicker" aria-label="Close shipping urgency picker">×</button>
                </header>

                <div class="ft-product-shipping-picker-tools">
                    <label class="ft-product-shipping-picker-search">
                        <span>⌕</span>
                        <input type="search" x-model="urgencySearch" placeholder="Search shipping urgencies…" autocomplete="off">
                    </label>
                    <small><?php echo e($selectedIds->count()); ?> already added</small>
                </div>

                <div class="ft-product-shipping-picker-body">
                    <div class="ft-product-shipping-picker-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urgencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urgency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $urgencyId = (int) $urgency->id;
                                $alreadyAdded = $selectedIds->contains($urgencyId);
                                $pending = $pendingIds->contains($urgencyId);
                                $urgencyColor = \App\Support\MasterColor::normalize($urgency->color) ?: '#00897B';
                                $searchText = mb_strtolower(trim($urgency->name.' '.$urgency->code.' '.$urgency->description));
                            ?>
                            <button
                                type="button"
                                class="ft-product-shipping-picker-card <?php echo e($alreadyAdded ? 'is-added' : ''); ?> <?php echo e($pending ? 'is-selected' : ''); ?>"
                                style="--urgency-color: <?php echo e($urgencyColor); ?>"
                                x-show="!urgencySearch || <?php echo \Illuminate\Support\Js::from($searchText)->toHtml() ?>.includes(urgencySearch.toLowerCase())"
                                <?php if(!$alreadyAdded): ?> wire:click="toggleProductShipmentUrgencyPickerSelection(<?php echo e($urgencyId); ?>)" <?php endif; ?>
                                <?php if($alreadyAdded): echo 'disabled'; endif; ?>
                                aria-pressed="<?php echo e($pending ? 'true' : 'false'); ?>"
                            >
                                <span class="ft-product-shipping-picker-card-mark"><?php echo e($alreadyAdded || $pending ? '✓' : ''); ?></span>
                                <span class="ft-product-shipping-picker-card-icon">↗</span>
                                <span class="ft-product-shipping-picker-card-copy">
                                    <strong><?php echo e($urgency->name); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urgency->code): ?><small><?php echo e($urgency->code); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urgency->description): ?><em><?php echo e(\Illuminate\Support\Str::limit($urgency->description, 78)); ?></em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <span class="ft-product-shipping-picker-card-status"><?php echo e($alreadyAdded ? 'Added' : ($pending ? 'Selected' : 'Select')); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                <footer class="ft-product-shipping-picker-footer">
                    <div>
                        <strong><?php echo e($pendingIds->count()); ?></strong>
                        <span><?php echo e(\Illuminate\Support\Str::plural('urgency', $pendingIds->count())); ?> selected to add</span>
                    </div>
                    <div>
                        <button type="button" class="is-secondary" wire:click="closeProductShipmentUrgencyPicker">Cancel</button>
                        <button type="button" class="is-primary" wire:click="confirmProductShipmentUrgencies" <?php if($pendingIds->isEmpty()): echo 'disabled'; endif; ?>>Add selected</button>
                    </div>
                </footer>
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $attributes = $__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__attributesOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc)): ?>
<?php $component = $__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc; ?>
<?php unset($__componentOriginalcb4fd92d2b0803c2b9ae1e7ce7b1c4dc); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/catalog/product-shipment-urgencies.blade.php ENDPATH**/ ?>