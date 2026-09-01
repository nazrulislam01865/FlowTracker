<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'results'=>collect(),
    'search'=>'',
    'selectedInquiry'=>null,
    'showLinkConfirm'=>false,
    'showUnlinkConfirm'=>false,
    'canManage'=>false,
    'linkedInquiryCanOpen'=>false,
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
    'results'=>collect(),
    'search'=>'',
    'selectedInquiry'=>null,
    'showLinkConfirm'=>false,
    'showUnlinkConfirm'=>false,
    'canManage'=>false,
    'linkedInquiryCanOpen'=>false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $linked = $job->relationLoaded('sourceInquiry') ? $job->sourceInquiry : null;
    $phaseName = $job->phase?->name ?? $job->status;
    $phaseSequence = (int) ($job->phase?->sequence ?? 1);
    $progress = max(0, min(100, (int) ($job->progress ?? 0)));
    $nextAction = trim((string) ($job->next_action ?? '')) ?: 'No action currently required';
    $selectedClientMatch = $selectedInquiry && (int) $selectedInquiry->client_id === (int) $job->client_id;
    $searchLength = mb_strlen(trim((string) $search));
    $masterData = app(\App\Services\MasterDataService::class);
    $inquiryService = app(\App\Services\InquiryService::class);
?>

<div class="ft-order-inquiry-link" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-inquiry-link-'.e($job->id).''; ?>wire:key="order-inquiry-link-<?php echo e($job->id); ?>">
    <section class="ft-oil-summary" aria-label="Order summary">
        <div class="ft-oil-summary-card">
            <span class="ft-oil-summary-icon">▣</span>
            <span><small>Current phase</small><strong><?php echo e($phaseName); ?> · Phase <?php echo e(max(1, $phaseSequence)); ?></strong></span>
        </div>
        <div class="ft-oil-summary-card">
            <span class="ft-oil-summary-icon">↗</span>
            <span><small>Overall progress</small><strong><?php echo e($progress); ?>%</strong><span class="ft-oil-progress"><i style="width:<?php echo e($progress); ?>%"></i></span></span>
        </div>
        <div class="ft-oil-summary-card">
            <span class="ft-oil-summary-icon">⌘</span>
            <span><small>Next required action</small><strong><?php echo e($nextAction); ?></strong></span>
        </div>
    </section>

    <section class="ft-oil-panel">
        <header class="ft-oil-panel-head">
            <div><h2>Linked inquiry</h2><p>Connect the source inquiry to this order without duplicating its files or data.</p></div>
            <div class="ft-oil-security-note"><i>⌑</i><span>Inquiry permissions remain unchanged</span></div>
        </header>

        <div class="ft-oil-panel-body">
            <div class="ft-oil-callout">
                <span class="ft-oil-callout-icon">i</span>
                <span><strong>Recommended relationship: one source inquiry per order</strong><span>The link gives traceability from quotation to delivery. Inquiry documents remain visible only to users who already have permission.</span></span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['inquiryLink'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="ft-oil-error" role="alert"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$linked): ?>
                <div class="ft-oil-link-layout">
                    <section class="ft-oil-search-section" aria-busy="false">
                        <div class="ft-oil-section-head"><h3>Find the source inquiry</h3><p>Search all eligible inquiries after entering at least 2 characters.</p></div>
                        <div class="ft-oil-search-wrap">
                            <span class="ft-oil-search-icon">⌕</span>
                            <input
                                class="ft-oil-search"
                                type="search"
                                autocomplete="off"
                                placeholder="Inquiry number, client, subject, product or offer text"
                                aria-label="Search inquiries"
                                wire:model.live.debounce.400ms="inquirySearch"
                                <?php if(!$canManage): echo 'disabled'; endif; ?>
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?>
                                <button class="ft-oil-clear" type="button" wire:click="clearInquirySearch">Clear</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="ft-oil-search-meta">
                            <span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canManage): ?>
                                    You do not have permission to link inquiries to this order
                                <?php elseif($searchLength < 2): ?>
                                    <?php echo e($searchLength ? 'Enter at least 2 characters' : 'Start with an inquiry number or client name'); ?>

                                <?php else: ?>
                                    Results for “<?php echo e(trim($search)); ?>”
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span class="ft-oil-status-wrap">
                                <i class="ft-oil-spinner" wire:loading wire:target="inquirySearch" aria-hidden="true"></i>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchLength >= 2 && $canManage): ?><span><?php echo e($results->count()); ?> found</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>

                        <div class="ft-oil-results" wire:loading.class="is-updating" wire:target="inquirySearch">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchLength < 2 || !$canManage): ?>
                                <div class="ft-oil-empty"><span><strong>No search yet</strong>Try an inquiry number, client name, subject, or product.</span></div>
                            <?php elseif($results->isEmpty()): ?>
                                <div class="ft-oil-empty"><span><strong>No matching inquiry</strong>Check the number or search with a client, product, or offer term.</span></div>
                            <?php else: ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $linkedOrder = $inquiry->sourceOrder ?: $inquiry->convertedJob;
                                        $eligible = !$linkedOrder && (string) $inquiry->result !== 'dead';
                                        $isSelected = $selectedInquiry && (int) $selectedInquiry->id === (int) $inquiry->id;
                                        $clientMatch = (int) $inquiry->client_id === (int) $job->client_id;
                                        $product = $inquiry->items->pluck('item_name')->filter()->take(2)->join(', ');
                                        $updated = \App\Support\UserLocalTime::format($inquiry->updated_at, 'M j, Y');
                                    ?>
                                    <button
                                        type="button"
                                        class="ft-oil-result <?php echo e($isSelected ? 'selected' : ''); ?> <?php echo e(!$eligible ? 'disabled' : ''); ?>"
                                        wire:click="selectInquiryForLink(<?php echo e($inquiry->id); ?>)"
                                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-inquiry-result-'.e($inquiry->id).''; ?>wire:key="order-inquiry-result-<?php echo e($inquiry->id); ?>"
                                        <?php if(!$eligible): echo 'disabled'; endif; ?>
                                    >
                                        <span class="ft-oil-radio"></span>
                                        <span>
                                            <span class="ft-oil-result-title"><?php echo e($inquiry->inquiry_number); ?></span>
                                            <span class="ft-oil-result-subject"><?php echo e($inquiry->subject); ?></span>
                                            <span class="ft-oil-result-meta"><?php echo e($product ?: ($inquiry->reference_number ?: 'Inquiry')); ?> · Updated <?php echo e($updated); ?></span>
                                        </span>
                                        <span class="ft-oil-result-client"><strong><?php echo e($inquiry->client?->name ?: 'No client'); ?></strong><small><?php echo e($clientMatch ? 'Client match' : 'Different client'); ?></small></span>
                                        <?php
                                            $resultInquiryStatusColor = $inquiryService->inquiryStatusColor((string) $inquiry->status);
                                        ?>
                                        <span class="ft-oil-result-owner"><strong class="ft-master-color-inline-label" style="<?php echo e(\App\Support\MasterColor::style($resultInquiryStatusColor)); ?>"><?php echo e($inquiry->status); ?></strong><small><?php echo e($eligible ? 'Owner: '.($inquiry->owner?->name ?: 'Unassigned') : ($linkedOrder ? 'Linked to '.$linkedOrder->displayOrderNumber() : 'Not eligible')); ?></small></span>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>

                    <aside class="ft-oil-selection" aria-label="Selected inquiry">
                        <div class="ft-oil-section-head"><h3>Review before linking</h3><p>Verify the client and conversion status.</p></div>
                        <div class="ft-oil-selection-body">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$selectedInquiry): ?>
                                <div class="ft-oil-selection-empty"><span><i>↗</i>Select one eligible inquiry from the results.</span></div>
                            <?php else: ?>
                                <div class="ft-oil-selected-card">
                                    <a class="ft-oil-selected-id" href="<?php echo e(route('inquiries.index', ['open'=>$selectedInquiry->id])); ?>" wire:navigate><?php echo e($selectedInquiry->inquiry_number); ?></a>
                                    <h4><?php echo e($selectedInquiry->subject); ?></h4>
                                    <div class="ft-oil-checks">
                                        <div class="ft-oil-check"><span>Client</span><b class="<?php echo e($selectedClientMatch ? '' : 'warn'); ?>"><?php echo e($selectedClientMatch ? 'Match' : 'Different client'); ?></b></div>
                                        <div class="ft-oil-check"><span>Inquiry availability</span><b>Eligible</b></div>
                                        <div class="ft-oil-check"><span>Files &amp; permissions</span><b>Unchanged</b></div>
                                    </div>
                                    <button class="ft-oil-primary" type="button" wire:click="openInquiryLinkConfirm">Link selected inquiry</button>
                                    <p class="ft-oil-helper">Linking does not copy quotation files, change inquiry status, or grant additional access.</p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </aside>
                </div>
            <?php else: ?>
                <div class="ft-oil-linked show">
                    <div class="ft-oil-linked-card">
                        <div>
                            <div class="ft-oil-linked-main">
                                <span class="ft-oil-linked-icon">✓</span>
                                <span>
                                    <span class="ft-oil-linked-label">Source inquiry linked</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkedInquiryCanOpen): ?>
                                        <a class="ft-oil-linked-id" href="<?php echo e(route('inquiries.index', ['open'=>$linked->id])); ?>" wire:navigate><?php echo e($linked->inquiry_number); ?></a>
                                    <?php else: ?>
                                        <span class="ft-oil-linked-id"><?php echo e($linked->inquiry_number); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="ft-oil-linked-title"><?php echo e($linked->subject); ?></span>
                                    <span class="ft-oil-linked-meta">
                                        <span><?php echo e($linked->client?->name ?: 'No client'); ?></span>
                                        <?php
                                            $linkedInquiryStatusColor = $inquiryService->inquiryStatusColor((string) $linked->status);
                                        ?>
                                        <span class="ft-master-color-inline-label" style="<?php echo e(\App\Support\MasterColor::style($linkedInquiryStatusColor)); ?>"><?php echo e($linked->status); ?></span>
                                        <span>Owner: <?php echo e($linked->owner?->name ?: 'Unassigned'); ?></span>
                                        <span>Linked to <?php echo e($job->displayOrderNumber()); ?></span>
                                    </span>
                                </span>
                            </div>
                            <div class="ft-oil-permission"><span>⌑</span><span><b>Access remains permission-based.</b> Users without Inquiry access can see the linked inquiry number but cannot open restricted Inquiry records or files.</span></div>
                        </div>
                        <div class="ft-oil-linked-actions">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkedInquiryCanOpen): ?>
                                <a class="ft-oil-secondary" href="<?php echo e(route('inquiries.index', ['open'=>$linked->id])); ?>" wire:navigate>Open inquiry ↗</a>
                            <?php else: ?>
                                <button class="ft-oil-secondary" type="button" disabled>Open inquiry ↗</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                                <button class="ft-oil-danger" type="button" wire:click="openInquiryUnlinkConfirm">Unlink</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showLinkConfirm && $selectedInquiry): ?>
        <div class="ft-oil-modal show" role="dialog" aria-modal="true" aria-labelledby="ft-inquiry-link-confirm-title" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-link-confirm-'.e($selectedInquiry->id).''; ?>wire:key="inquiry-link-confirm-<?php echo e($selectedInquiry->id); ?>">
            <div class="ft-oil-modal-card">
                <header class="ft-oil-modal-head"><h3 id="ft-inquiry-link-confirm-title">Link inquiry to this order?</h3><button class="ft-oil-close" type="button" wire:click="closeInquiryLinkConfirm" aria-label="Close">×</button></header>
                <div class="ft-oil-modal-body">
                    <div class="ft-oil-pair">
                        <div class="ft-oil-pair-card"><small>Inquiry</small><strong><?php echo e($selectedInquiry->inquiry_number); ?></strong></div>
                        <div class="ft-oil-pair-arrow">→</div>
                        <div class="ft-oil-pair-card"><small>Order</small><strong><?php echo e($job->displayOrderNumber()); ?></strong></div>
                    </div>
                    <div class="ft-oil-modal-note"><?php echo e($selectedClientMatch ? 'This creates a traceable relationship only. No files are copied and existing access permissions remain unchanged.' : 'The inquiry belongs to a different client. Confirm the relationship is intentional before linking; this event will be recorded.'); ?></div>
                </div>
                <footer class="ft-oil-modal-actions"><button class="ft-oil-cancel" type="button" wire:click="closeInquiryLinkConfirm">Cancel</button><button class="ft-oil-confirm" type="button" wire:click="confirmInquiryLink" wire:loading.attr="disabled" wire:target="confirmInquiryLink">Confirm link</button></footer>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUnlinkConfirm && $linked): ?>
        <div class="ft-oil-modal show" role="dialog" aria-modal="true" aria-labelledby="ft-inquiry-unlink-title" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-unlink-confirm-'.e($linked->id).''; ?>wire:key="inquiry-unlink-confirm-<?php echo e($linked->id); ?>">
            <div class="ft-oil-modal-card">
                <header class="ft-oil-modal-head"><h3 id="ft-inquiry-unlink-title">Unlink this inquiry?</h3><button class="ft-oil-close" type="button" wire:click="closeInquiryUnlinkConfirm" aria-label="Close">×</button></header>
                <div class="ft-oil-modal-body"><div class="ft-oil-modal-note">The inquiry and order will remain unchanged, but their traceability link will be removed. This action is recorded in order activity.</div></div>
                <footer class="ft-oil-modal-actions"><button class="ft-oil-cancel" type="button" wire:click="closeInquiryUnlinkConfirm">Cancel</button><button class="ft-oil-danger" type="button" wire:click="confirmInquiryUnlink" wire:loading.attr="disabled" wire:target="confirmInquiryUnlink">Unlink inquiry</button></footer>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-inquiry.blade.php ENDPATH**/ ?>