<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job', 'context' => [], 'form' => [], 'mentionUsers' => collect()]));

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

foreach (array_filter((['job', 'context' => [], 'form' => [], 'mentionUsers' => collect()]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $step = (int) ($form['step'] ?? 1);
    $preview = $form['preview'] ?? [];
    $suppliers = collect($form['supplierOptions'] ?? []);
    $evidence = collect($form['evidence'] ?? []);
    $redoMentionUsers = collect($mentionUsers)->values();
    $currency = (string) ($job->currency ?: 'USD');
    $money = fn ($value) => ($currency === 'USD' ? '$' : $currency.' ').number_format((float) $value, 2);
    $redoIssueSource = (string) ($form['issueSource'] ?? 'Customer');
    $redoIssueCategory = (string) ($form['issueCategory'] ?? 'Artwork / production mismatch');
    $redoAffectedQuantity = (int) ($form['affectedQuantity'] ?? 1);
    $redoIssueDescription = (string) ($form['issueDescription'] ?? '');
    $redoScope = (string) ($form['scope'] ?? 'artwork');
    $redoQuantity = (int) ($form['quantity'] ?? 1);
    $redoSupplierId = $form['supplierId'] ?? null;
    $redoInstructions = (string) ($form['instructions'] ?? '');
    $redoCustomerResolution = (string) ($form['customerResolution'] ?? 'free');
    $redoCustomerDiscount = (string) ($form['customerDiscount'] ?? '20');
    $redoSupplierChargePercent = (string) ($form['supplierChargePercent'] ?? '40');
    $redoDeductFreight = (bool) ($form['deductFreight'] ?? true);
    $redoFreightAmount = (string) ($form['freightAmount'] ?? '0.00');
    $nextSequence = ((int) ($context['redoOrderCount'] ?? $context['redoCount'] ?? 0)) + 1;
    $nextOrderNumber = $job->displayOrderNumber().'-R'.$nextSequence;
    $isDiscountScope = $redoScope === 'discount';
    $scopeLabel = match ($redoScope) {
        'production' => 'Production phase → QC & Dispatch',
        'discount' => 'No workflow restart · customer discount only',
        default => 'Artwork phase → Production → QC & Dispatch',
    };
    $customerLabel = $isDiscountScope || $redoCustomerResolution === 'discount'
        ? $redoCustomerDiscount.'% customer discount instead of redo'
        : 'Free redo';
    $recoveryLabel = ($isDiscountScope ? $redoSupplierChargePercent.'% supplier recovery' : $redoSupplierChargePercent.'% redo charge')
        .($redoDeductFreight && (float) $redoFreightAmount > 0 ? ' + '.$money($redoFreightAmount).' freight deduction' : '');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($form['show'] ?? false)): ?>
    <div class="ft-redo-modalwrap show" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-redo-modal'; ?>wire:key="order-redo-modal" wire:click.self="closeRedoModal">
        <section class="ft-redo-modal" role="dialog" aria-modal="true" aria-labelledby="order-redo-modal-title">
            <header class="ft-redo-modalhead">
                <div>
                    <h2 id="order-redo-modal-title">Initiate redo</h2>
                    <div class="sub">Create a controlled redo or resolve the issue with a customer discount.</div>
                </div>
                <button type="button" class="ft-redo-close" wire:click="closeRedoModal" aria-label="Close">×</button>
            </header>

            <div class="ft-redo-steps" aria-hidden="true">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= 4; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <i class="ft-redo-step <?php echo e($i <= $step ? 'on' : ''); ?>"></i>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <div class="ft-redo-stepnames">
                <span class="<?php echo e($step === 1 ? 'on' : ''); ?>">1 · Issue</span>
                <span class="<?php echo e($step === 2 ? 'on' : ''); ?>">2 · Scope</span>
                <span class="<?php echo e($step === 3 ? 'on' : ''); ?>">3 · Commercial</span>
                <span class="<?php echo e($step === 4 ? 'on' : ''); ?>">4 · Confirm</span>
            </div>

            <div class="ft-redo-modalbody">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 1): ?>
                    <div class="ft-redo-pane show">
                        <h3>Record the redo issue</h3>
                        <p>Capture who reported the issue and why the order needs corrective action.</p>

                        <div class="ft-redo-formgrid">
                            <label class="ft-redo-field">
                                <span>Issue reported by *</span>
                                <select wire:model="redoIssueSource">
                                    <option>Customer</option>
                                    <option>Quality Control</option>
                                    <option>Internal Team</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoIssueSource'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>

                            <label class="ft-redo-field">
                                <span>Issue category *</span>
                                <select wire:model="redoIssueCategory">
                                    <option>Artwork / production mismatch</option>
                                    <option>Production quality defect</option>
                                    <option>Incorrect specification</option>
                                    <option>Wrong quantity or size</option>
                                    <option>Shipping damage</option>
                                    <option>Other</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoIssueCategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>

                            <label class="ft-redo-field">
                                <span>Reported date</span>
                                <input value="<?php echo e(app(\App\Services\WorkspaceSettingsService::class)->localToday()->format('M j, Y')); ?>" disabled>
                            </label>

                            <label class="ft-redo-field">
                                <span>Affected quantity *</span>
                                <input type="number" min="1" max="<?php echo e(max(1, (int) $job->quantity)); ?>" wire:model.blur="redoAffectedQuantity">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoAffectedQuantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>

                            
                            <div class="ft-redo-field wide ft-mention-host">
                                <span id="redo-issue-description-label">Issue description *</span>
                                <textarea
                                    class="ft-mention-input"
                                    data-rich-text
                                    rows="5"
                                    wire:model="redoIssueDescription"
                                    autocomplete="off"
                                    aria-labelledby="redo-issue-description-label"
                                    data-mention-users="<?php echo e(json_encode($redoMentionUsers->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
                                    placeholder="Describe the customer/QC issue. Type @ to mention someone, paste an image/screenshot, or use the editor tools."
                                ></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoIssueDescription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ft-redo-field wide">
                                <span>Evidence</span>
                                <div class="ft-redo-choice selected ft-redo-evidence">
                                    <span aria-hidden="true">📎</span>
                                    <div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($evidence->isNotEmpty()): ?>
                                            <b><?php echo e($evidence->first()); ?></b>
                                            <small>Latest artwork attached to the source Order. Archived artwork versions are not shown here.</small>
                                        <?php else: ?>
                                            <b>No latest artwork available</b>
                                            <small>Upload artwork on the source Order before using it as Redo evidence.</small>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif($step === 2): ?>
                    <div class="ft-redo-pane show">
                        <h3>Choose the redo scope</h3>
                        <p>Choose where the workflow restarts, or resolve the issue financially without creating a redo order.</p>

                        <div class="ft-redo-choicegrid">
                            <label class="ft-redo-choice <?php echo e($redoScope === 'artwork' ? 'selected' : ''); ?>">
                                <input type="radio" value="artwork" wire:model.live="redoScope">
                                <div>
                                    <b>Artwork + production redo</b>
                                    <small>Reopen Artwork, require revised approval, then repeat Production and QC.</small>
                                </div>
                            </label>
                            <label class="ft-redo-choice <?php echo e($redoScope === 'production' ? 'selected' : ''); ?>">
                                <input type="radio" value="production" wire:model.live="redoScope">
                                <div>
                                    <b>Production-only redo</b>
                                    <small>Keep the existing approved artwork and restart directly from Production.</small>
                                </div>
                            </label>
                            <label class="ft-redo-choice <?php echo e($redoScope === 'discount' ? 'selected' : ''); ?>">
                                <input type="radio" value="discount" wire:model.live="redoScope">
                                <div>
                                    <b>Discount (instead of redo)</b>
                                    <small>Do not restart any workflow phase. Give a discount to the client and record the financial adjustment only.</small>
                                </div>
                            </label>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoScope'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="validation-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="ft-redo-formgrid ft-redo-formgrid-spaced">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope): ?>
                                <div class="ft-redo-discount-note wide">
                                    <b>No workflow restart</b>
                                    <small><?php echo e(number_format($redoAffectedQuantity)); ?> affected unit<?php echo e($redoAffectedQuantity === 1 ? '' : 's'); ?> will be used to calculate the client discount. No redo Order or redo tasks will be created.</small>
                                </div>
                            <?php else: ?>
                                <label class="ft-redo-field">
                                    <span>Redo quantity</span>
                                    <input type="number" min="1" max="<?php echo e(max(1, (int) $job->quantity)); ?>" wire:model.live.debounce.250ms="redoQuantity">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoQuantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>

                                <label class="ft-redo-field">
                                    <span>Responsible supplier</span>
                                    <select wire:model="redoSupplierId">
                                        <option value="">Supplier not decided</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($supplier['id']); ?>"><?php echo e($supplier['label']); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoSupplierId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <label class="ft-redo-field wide">
                                <span><?php echo e($isDiscountScope ? 'Internal note' : 'Internal instructions'); ?></span>
                                <textarea rows="4" wire:model="redoInstructions" placeholder="<?php echo e($isDiscountScope ? 'Reason or approval note for the customer discount.' : 'Instructions for revised artwork, replacement production and repeat QC.'); ?>"></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoInstructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>
                        </div>
                    </div>
                <?php elseif($step === 3): ?>
                    <div class="ft-redo-pane show">
                        <h3>Set customer resolution and supplier recovery</h3>
                        <p>Customer treatment and supplier recovery are recorded separately.</p>

                        <div class="ft-redo-formgrid">
                            <div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDiscountScope): ?>
                                    <div class="ft-redo-fixed-resolution">
                                        <span>Customer resolution</span>
                                        <b>Discount instead of redo</b>
                                        <small>No replacement Order or workflow restart will be created.</small>
                                    </div>

                                    <label class="ft-redo-field">
                                        <span>Customer discount *</span>

                                        <div class="ft-redo-percent-input">
                                            <input
                                                type="number"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                inputmode="decimal"
                                                wire:model.live.debounce.250ms="redoCustomerDiscount"
                                                placeholder="Enter discount"
                                            >

                                            <span class="ft-redo-percent-suffix">%</span>
                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoCustomerDiscount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <small class="validation-error"><?php echo e($message); ?></small>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </label>
                                <?php else: ?>
                                    <label class="ft-redo-field">
                                        <span>Customer resolution *</span>
                                        <select wire:model.live="redoCustomerResolution">
                                            <option value="free">Free redo</option>
                                            <option value="discount">Discount instead of redo</option>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoCustomerResolution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </label>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redoCustomerResolution === 'discount'): ?>
                                        <label class="ft-redo-field" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'redo-'.e($redoScope).'-customer-discount'; ?>wire:key="redo-<?php echo e($redoScope); ?>-customer-discount">
                                            <span>Customer discount *</span>

                                            <div class="ft-redo-percent-input">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    inputmode="decimal"
                                                    wire:model.live.debounce.250ms="redoCustomerDiscount"
                                                    placeholder="Enter discount"
                                                    aria-label="Customer discount percentage"
                                                >

                                                <span class="ft-redo-percent-suffix" aria-hidden="true">%</span>
                                            </div>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoCustomerDiscount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="validation-error"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </label>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <label class="ft-redo-field">
                                    <span>Supplier recovery</span>

                                    <div class="ft-redo-percent-input">
                                        <input
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            inputmode="decimal"
                                            wire:model.live.debounce.250ms="redoSupplierChargePercent"
                                            placeholder="Enter supplier recovery"
                                        >

                                        <span class="ft-redo-percent-suffix">%</span>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoSupplierChargePercent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="validation-error">
                                            <?php echo e($message); ?>

                                        </small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>

                                <label class="ft-redo-check">
                                    <input type="checkbox" wire:model.live="redoDeductFreight">
                                    <span>
                                        <b>Deduct freight charge from supplier</b>
                                        <small>Record freight as a separate supplier deduction.</small>
                                    </span>
                                </label>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redoDeductFreight): ?>
                                    <label class="ft-redo-field ft-redo-field-top">
                                        <span>Freight amount</span>
                                        <input type="number" min="0" step="0.01" wire:model.live.debounce.250ms="redoFreightAmount">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['redoFreightAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </label>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ft-redo-amounts">
                                <h4>Financial preview</h4>
                                <div class="ft-redo-calc"><span>Affected order value</span><b><?php echo e($money($preview['affectedValue'] ?? 0)); ?></b></div>
                                <div class="ft-redo-calc"><span>Customer charge / credit</span><b><?php echo e($redoCustomerResolution === 'discount' ? '-'.$money($preview['customerImpact'] ?? 0) : $money(0)); ?></b></div>
                                <div class="ft-redo-calc"><span><?php echo e($isDiscountScope ? 'Supplier recovery' : 'Supplier redo charge'); ?></span><b><?php echo e($money($preview['supplierCharge'] ?? 0)); ?></b></div>
                                <div class="ft-redo-calc"><span>Freight deduction</span><b><?php echo e($money($preview['freight'] ?? 0)); ?></b></div>
                                <div class="ft-redo-calc total"><span>Total supplier recovery</span><b><?php echo e($money($preview['recovery'] ?? 0)); ?></b></div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="ft-redo-pane show">
                        <h3><?php echo e($isDiscountScope ? 'Review and record the customer discount' : 'Review and create the redo order'); ?></h3>
                        <p>
                            <?php echo e($isDiscountScope
                                ? 'The original Order and its workflow remain exactly where they are. Only a financial discount adjustment will be recorded.'
                                : 'The original Order remains intact. A linked redo Order and audit record will be created.'); ?>

                        </p>

                        <div class="ft-redo-confirmbox">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($isDiscountScope)): ?>
                                <div class="ft-redo-confirmrow"><span>New order number</span><b><?php echo e($nextOrderNumber); ?></b></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="ft-redo-confirmrow"><span>Action</span><b><?php echo e($isDiscountScope ? 'Discount instead of redo' : '↻ Redo order'); ?></b></div>
                            <div class="ft-redo-confirmrow"><span>Original order</span><b><?php echo e($job->displayOrderNumber()); ?></b></div>
                            <div class="ft-redo-confirmrow"><span>Issue</span><b><?php echo e($redoIssueSource); ?> · <?php echo e($redoIssueCategory); ?> · <?php echo e(number_format($isDiscountScope ? $redoAffectedQuantity : $redoQuantity)); ?> units</b></div>
                            <div class="ft-redo-confirmrow"><span>Workflow restart</span><b><?php echo e($scopeLabel); ?></b></div>
                            <div class="ft-redo-confirmrow"><span>Customer resolution</span><b><?php echo e($customerLabel); ?></b></div>
                            <div class="ft-redo-confirmrow"><span>Supplier recovery</span><b><?php echo e($recoveryLabel); ?></b></div>
                        </div>

                        <div class="ft-redo-warning">
                            <?php echo e($isDiscountScope
                                ? 'No redo Order or replacement tasks will be created. The current workflow stays unchanged. The customer credit is recorded as a financial adjustment and can be reviewed in Invoices & Payments.'
                                : 'Creating the redo will not change the original invoice or payment. Financial adjustments are stored against the redo Order and can be reviewed in Invoices & Payments.'); ?>

                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <footer class="ft-redo-modalfoot">
                <span class="sub">Step <?php echo e($step); ?> of 4</span>
                <div class="ft-redo-modal-actions">
                    <button type="button" class="btn" wire:click="previousRedoStep" <?php if($step === 1): echo 'disabled'; endif; ?>>Back</button>
                    <button type="button" class="btn primary" wire:click="nextRedoStep" wire:loading.attr="disabled" wire:target="nextRedoStep,createRedoOrder">
                        <span wire:loading.remove wire:target="nextRedoStep,createRedoOrder"><?php echo e($step === 4 ? ($isDiscountScope ? 'Record discount' : 'Create redo order') : 'Continue'); ?></span>
                        <span wire:loading wire:target="nextRedoStep,createRedoOrder"><?php echo e($step === 4 ? ($isDiscountScope ? 'Recording...' : 'Creating...') : 'Working...'); ?></span>
                    </button>
                </div>
            </footer>
        </section>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/order-detail/redo-modal.blade.php ENDPATH**/ ?>