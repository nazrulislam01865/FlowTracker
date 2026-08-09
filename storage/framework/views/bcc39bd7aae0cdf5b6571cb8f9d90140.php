<?php
    $tone = static function (string $status): string {
        return match (true) {
            str_contains($status, 'Converted'), str_contains($status, 'Completed') => 'green',
            str_contains($status, 'Dead'), str_contains($status, 'Closed') => 'red',
            str_contains($status, 'Ready'), str_contains($status, 'On Hold') => 'amber',
            str_contains($status, 'Waiting') => 'purple',
            default => 'blue',
        };
    };
    $initials = static function (?string $name): string {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        return strtoupper(substr(implode('', array_map(fn ($part) => substr($part, 0, 1), $parts)), 0, 2)) ?: '—';
    };
    $mentionText = static function (?string $text): string {
        $escaped = e((string) $text);
        return preg_replace('/(?<![\pL\pN._-])@([\pL\pN][\pL\pN._-]*)/u', '<span class="mention">@$1</span>', $escaped) ?? $escaped;
    };
?>

<div class="ft-inquiry-prototype">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash-inline"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?><div class="error-inline"><?php echo e($errors->first()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'list'): ?>
        <section class="view">
            <div class="pagehead">
                <div><h1>Inquiries</h1><p>Manage client requests from first inquiry through tasks, conversion, or closure.</p></div>
                <div class="actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('inquiries','create')): ?><button class="primary" type="button" wire:click="openCreate">＋ New Inquiry</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="metrics">
                <div class="metric"><i>?</i><span><small>Active inquiries</small><strong><?php echo e($metrics['active']); ?></strong></span></div>
                <div class="metric"><i>✓</i><span><small>Converted to order</small><strong><?php echo e($metrics['converted']); ?></strong></span></div>
                <div class="metric"><i>×</i><span><small>Closed inquiries</small><strong><?php echo e($metrics['dead']); ?></strong></span></div>
                <div class="metric"><i>⌁</i><span><small>Tasks due today</small><strong><?php echo e($metrics['dueToday']); ?></strong></span></div>
            </div>

            <div class="shell inquiry-list-v2">
                <div class="toolbar">
                    <div class="search"><span>⌕</span><input wire:model.live.debounce.350ms="search" placeholder="Search inquiry, title, client, task or assignee"></div>
                    <div class="filters">
                        <button class="chip <?php echo e($quick === 'all' ? 'active' : ''); ?>" type="button" wire:click="setQuick('all')">All</button>
                        <button class="chip <?php echo e($quick === 'active' ? 'active' : ''); ?>" type="button" wire:click="setQuick('active')">Active</button>
                        <button class="chip <?php echo e($quick === 'converted' ? 'active' : ''); ?>" type="button" wire:click="setQuick('converted')">Converted</button>
                        <button class="chip <?php echo e($quick === 'dead' ? 'active' : ''); ?>" type="button" wire:click="setQuick('dead')">Closed</button>
                    </div>
                </div>
                <div class="inquiry-list-table" role="region" aria-label="Inquiry list" tabindex="0">
                    <div class="listhead">
                        <div>Inquiry</div>
                        <div>Title</div>
                        <div>Client / Item</div>
                        <div>Current Task</div>
                        <div>Progress</div>
                        <div>Assignee</div>
                        <div>Due Date</div>
                        <div>Started At</div>
                        <div>Status</div>
                        <div>View</div>
                    </div>
                    <div class="inquiry-list-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inquiryRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <article class="row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-list-'.e($row['id']).''; ?>wire:key="inquiry-list-<?php echo e($row['id']); ?>">
                                <div class="cell ft-inquiry-list-identity" data-label="Inquiry">
                                    <a class="id" href="<?php echo e(route('inquiries.index', ['open' => $row['id']])); ?>" wire:navigate><?php echo e($row['number']); ?></a>
                                    <span class="sub ft-inquiry-created-by" title="Created by <?php echo e($row['createdBy']); ?>">Created by <?php echo e($row['createdBy']); ?></span>
                                    <span class="sub ft-inquiry-created-at"><?php echo e($row['createdDate']); ?> · <?php echo e($row['createdTime']); ?></span>
                                </div>
                                <div class="cell ft-inquiry-list-title-cell" data-label="Title">
                                    <span class="title ft-inquiry-title-preview" title="<?php echo e($row['title']); ?>"><?php echo e($row['titlePreview']); ?></span>
                                </div>
                                <div class="cell ft-inquiry-list-client-cell" data-label="Client / Item">
                                    <span class="title"><?php echo e($row['client']); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['item']): ?><span class="sub"><?php echo e($row['item']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="cell ft-inquiry-list-task-cell" data-label="Current Task"><span class="title"><?php echo e($row['currentTask']); ?></span><span class="sub"><?php echo e($row['taskCaption']); ?></span></div>
                                <div class="cell ft-inquiry-list-progress-cell" data-label="Progress">
                                    <div class="ft-inquiry-list-progress">
                                        <div class="ft-inquiry-list-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo e($row['progressPercent']); ?>" aria-label="<?php echo e($row['progress']); ?> of <?php echo e($row['total']); ?> tasks completed"><span style="width:<?php echo e($row['progressPercent']); ?>%"></span></div>
                                        <b><?php echo e($row['progress']); ?>/<?php echo e($row['total']); ?></b>
                                    </div>
                                </div>
                                <div class="cell ft-inquiry-list-assignee-cell" data-label="Assignee"><div class="ownerline"><span class="avatar"><?php echo e($initials($row['assignee'])); ?></span><span class="title"><?php echo e($row['assignee']); ?></span></div></div>
                                <div class="cell ft-inquiry-list-due-cell" data-label="Due Date"><span class="title"><?php echo e($row['due']); ?></span></div>
                                <div class="cell ft-inquiry-list-started-cell" data-label="Started At"><span class="title"><?php echo e($row['startedDate']); ?></span><span class="sub"><?php echo e($row['startedTime']); ?></span></div>
                                <div class="cell ft-inquiry-list-status-cell" data-label="Status"><span class="pill <?php echo e($tone($row['status'])); ?>"><?php echo e($row['status']); ?></span></div>
                                <div class="cell ft-inquiry-list-view-cell" data-label="View"><a class="openbtn openbtn-link" href="<?php echo e(route('inquiries.index', ['open' => $row['id']])); ?>" aria-label="View <?php echo e($row['number']); ?>" wire:navigate>View <span aria-hidden="true">→</span></a></div>
                            </article>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="ft-inquiry-list-empty">No matching inquiries.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="footer">
                    <span>Showing <?php echo e($inquiryPaginator->firstItem() ?? 0); ?>–<?php echo e($inquiryPaginator->lastItem() ?? 0); ?> of <?php echo e($inquiryPaginator->total()); ?> inquiries</span>
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryPaginator->lastPage() > 1): ?>
                            <button class="chip" type="button" wire:click="previousPage('inquiryPage')" <?php if($inquiryPaginator->onFirstPage()): echo 'disabled'; endif; ?>>←</button>
                            Page <?php echo e($inquiryPaginator->currentPage()); ?> of <?php echo e($inquiryPaginator->lastPage()); ?>

                            <button class="chip" type="button" wire:click="nextPage('inquiryPage')" <?php if(!$inquiryPaginator->hasMorePages()): echo 'disabled'; endif; ?>>→</button>
                        <?php else: ?>
                            Page 1 of 1
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
            </div>
        </section>

    <?php elseif($mode === 'create'): ?>
        <?php
            $createNow = app(\App\Services\WorkspaceSettingsService::class)->localNow();
            $selectedWorkflow = collect($workflowFilterOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $createWorkflowId);
            $selectedWorkflowName = (string) ($selectedWorkflow['label'] ?? $selectedWorkflowLabel ?: 'Select workflow');
            $workflowOptionCount = count($workflowFilterOptions);
            $selectedOwner = collect($userOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $createOwnerId);
            $selectedOwnerName = (string) ($selectedOwner['name'] ?? auth()->user()->name);
        ?>
        <section class="view ft-inquiry-create-v3" x-on:keydown.meta.enter.window="$wire.createInquiry()" x-on:keydown.ctrl.enter.window="$wire.createInquiry()">
            <div class="formwrap ft-inquiry-create-shell">
                <div class="crumb">Inquiries / New Inquiry</div>
                <div class="formtop ft-inquiry-create-heading">
                    <div>
                        <h1>Create Inquiry</h1>
                        <p>Capture a new client request from email or phone. The inquiry workflow starts automatically.</p>
                    </div>
                </div>

                <div class="formcard ft-inquiry-create-card">
                    <section class="section ft-inquiry-create-section ft-inquiry-create-details">
                        <div class="sectiontitle ft-inquiry-step-title"><span>1</span><h2>Inquiry details</h2></div>

                        <div class="ft-inquiry-create-grid ft-inquiry-create-grid-top">
                            <div class="ft-inquiry-create-field">
                                <label>How was this inquiry received? *</label>
                                <div class="ft-inquiry-source-switch" role="group" aria-label="How was this inquiry received?">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Email' => '✉', 'Phone' => '☎', 'Other' => '•••']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source => $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <button type="button" class="<?php echo e($requestSource === $source ? 'is-active' : ''); ?>" wire:click="$set('requestSource', '<?php echo e($source); ?>')">
                                            <span aria-hidden="true"><?php echo e($icon); ?></span><?php echo e($source); ?>

                                        </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['requestSource'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ft-inquiry-create-field">
                                <label>Received</label>
                                <div class="ft-inquiry-received-control">
                                    <span>Today, <?php echo e($createNow->format('g:i A')); ?></span>
                                    <button type="button" title="Received time is set automatically" aria-label="Received time is set automatically">✎</button>
                                </div>
                                <small class="ft-inquiry-field-help">Set automatically</small>
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid ft-inquiry-create-grid-client">
                            <div class="ft-inquiry-create-field">
                                <label>Client *</label>
                                <div class="ft-inquiry-client-control-row">
                                    <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-client-selector','label' => 'Client','property' => 'clientId','type' => 'clients','context' => 'create-inquiry','action' => 'setCreateSelector','value' => $clientId,'placeholder' => 'Search or select client...','selectedLabel' => $selectedClientLabel ?: null,'initialOptions' => $clientFilterOptions,'clearable' => false,'wire:key' => 'inquiry-create-client-selector-'.e($clientId ?: 'none').'-'.e(substr(md5($selectedClientLabel ?: 'none'), 0, 8)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-client-selector','label' => 'Client','property' => 'clientId','type' => 'clients','context' => 'create-inquiry','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'placeholder' => 'Search or select client...','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedClientLabel ?: null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'inquiry-create-client-selector-'.e($clientId ?: 'none').'-'.e(substr(md5($selectedClientLabel ?: 'none'), 0, 8)).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('clients','create')): ?>
                                        <button class="secondary ft-inquiry-new-client-btn" type="button" wire:click="openCreateClientModal">＋ New client</button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ft-inquiry-create-field">
                                <label>Client contact</label>
                                <div class="ft-inquiry-client-control-row">
                                    <div class="ft-inquiry-contact-select-wrap">
                                        <select wire:model="clientContact" <?php if(!$clientId || !$clientContact): echo 'disabled'; endif; ?>>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientId && $clientContact): ?>
                                                <option value="<?php echo e($clientContact); ?>"><?php echo e($clientContact); ?></option>
                                            <?php else: ?>
                                                <option value=""><?php echo e($clientId ? 'No contact recorded' : 'Select a client first'); ?></option>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                    </div>
                                    <button class="secondary ft-inquiry-new-contact-btn" type="button" wire:click="openCreateContactModal" <?php if(!$clientId): echo 'disabled'; endif; ?>>＋ New contact</button>
                                </div>
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid">
                            <label class="ft-inquiry-create-field">
                                <span>Reference number</span>
                                <input wire:model="referenceNumber" placeholder="Email or client reference (optional)">
                            </label>

                            <label class="ft-inquiry-create-field">
                                <span>Assigned to</span>
                                <select wire:model="createOwnerId">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $userOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($userOption['id']); ?>"><?php echo e((int) $userOption['id'] === (int) auth()->id() ? 'Me · ' : ''); ?><?php echo e($userOption['name']); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createOwnerId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>
                        </div>

                        <label class="ft-inquiry-create-field ft-inquiry-create-field-full">
                            <span>Inquiry title *</span>
                            <input wire:model="subject" placeholder="e.g. 5,000 embroidered polo shirts for September">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>

                        <div class="ft-inquiry-create-field ft-inquiry-create-field-full ft-inquiry-request-details">
                            <label>Request details</label>
                            <textarea data-rich-text wire:model="requirementNotes" placeholder="Paste or summarize the client's request, including quantities, specifications, target date and any special instructions..."></textarea>
                            <small class="ft-inquiry-field-tip"><b>Tip:</b> Include quantity, product, deadline and delivery location.</small>
                        </div>
                    </section>

                    <section class="section ft-inquiry-create-section ft-inquiry-attachments-section">
                        <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                            <span>2</span><h2>Attachments</h2><p>Add emails, specifications, artwork or reference images.</p>
                        </div>
                        <div
                            class="inquiry-dropzone ft-inquiry-prototype-dropzone"
                            x-data="{ dragging: false }"
                            x-bind:class="{ 'is-dragging': dragging }"
                            x-on:dragenter.prevent="dragging = true"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="if (!$el.contains($event.relatedTarget)) dragging = false"
                            x-on:drop.prevent="dragging = false; const files = $event.dataTransfer.files; if (files.length) { $refs.createAttachmentInput.files = files; $refs.createAttachmentInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                            x-on:click="$refs.createAttachmentInput.click()"
                            role="button"
                            tabindex="0"
                            x-on:keydown.enter.prevent="$refs.createAttachmentInput.click()"
                            x-on:keydown.space.prevent="$refs.createAttachmentInput.click()"
                        >
                            <input x-ref="createAttachmentInput" class="file-input" type="file" wire:model="createAttachments" multiple>
                            <div class="inquiry-dropzone-icon" aria-hidden="true">⇧</div>
                            <div class="inquiry-dropzone-copy">
                                <strong>Drop client files here</strong>
                                <span class="ft-inquiry-drop-or">or <b>browse files</b></span>
                                <small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB per file</small>
                            </div>
                            <button class="secondary inquiry-dropzone-button" type="button" x-on:click.stop="$refs.createAttachmentInput.click()">Choose files</button>
                        </div>
                        <div class="inquiry-upload-state" wire:loading wire:target="createAttachments">Uploading files…</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($createAttachments)): ?>
                            <div class="inquiry-selected-files ft-inquiry-selected-files">
                                <div class="inquiry-selected-files-title">Selected files <span><?php echo e(count($createAttachments)); ?></span></div>
                                <div class="ft-inquiry-selected-file-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $createAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php
                                            $attachmentName = (string) $upload->getClientOriginalName();
                                            $attachmentExtension = strtolower((string) pathinfo($attachmentName, PATHINFO_EXTENSION));
                                            $attachmentMime = method_exists($upload, 'getMimeType') ? (string) $upload->getMimeType() : '';
                                            $attachmentIsImage = str_starts_with($attachmentMime, 'image/')
                                                || in_array($attachmentExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                                            $attachmentPreviewUrl = $attachmentIsImage && method_exists($upload, 'temporaryUrl')
                                                ? $upload->temporaryUrl()
                                                : null;
                                            $attachmentSize = method_exists($upload, 'getSize') ? (int) $upload->getSize() : 0;
                                            $attachmentSizeLabel = $attachmentSize >= 1048576
                                                ? number_format($attachmentSize / 1048576, 1).' MB'
                                                : ($attachmentSize > 0 ? max(1, (int) round($attachmentSize / 1024)).' KB' : 'Selected file');
                                        ?>
                                        <article class="ft-inquiry-selected-file-card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-attachment-'.e($loop->index).'-'.e(md5($attachmentName)).''; ?>wire:key="create-attachment-<?php echo e($loop->index); ?>-<?php echo e(md5($attachmentName)); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attachmentPreviewUrl): ?>
                                                <a
                                                    class="ft-inquiry-selected-file-preview"
                                                    href="<?php echo e($attachmentPreviewUrl); ?>"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Open image preview"
                                                    aria-label="Open preview of <?php echo e($attachmentName); ?>"
                                                >
                                                    <img src="<?php echo e($attachmentPreviewUrl); ?>" alt="Preview of <?php echo e($attachmentName); ?>">
                                                    <span>Preview</span>
                                                </a>
                                            <?php else: ?>
                                                <div class="ft-inquiry-selected-file-type" aria-hidden="true">
                                                    <span>▤</span>
                                                    <b><?php echo e($attachmentExtension !== '' ? strtoupper($attachmentExtension) : 'FILE'); ?></b>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <div class="ft-inquiry-selected-file-meta">
                                                <strong title="<?php echo e($attachmentName); ?>"><?php echo e($attachmentName); ?></strong>
                                                <span><?php echo e($attachmentExtension !== '' ? strtoupper($attachmentExtension) : 'FILE'); ?> · <?php echo e($attachmentSizeLabel); ?></span>
                                            </div>
                                        </article>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>

                    <section class="section ft-inquiry-create-section ft-inquiry-next-section" x-data="{ workflowOpen: false }">
                        <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                            <span>3</span><h2>What happens next</h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowOptionCount > 0): ?><em><?php echo e($workflowOptionCount); ?> <?php echo e(\Illuminate\Support\Str::plural('workflow', $workflowOptionCount)); ?> available</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ft-inquiry-workflow-card" :class="{ 'is-open': workflowOpen }">
                            <div class="ft-inquiry-workflow-selected" role="button" tabindex="0" x-on:click="workflowOpen = !workflowOpen" x-on:keydown.enter.prevent="workflowOpen = !workflowOpen" x-on:keydown.space.prevent="workflowOpen = !workflowOpen" :aria-expanded="workflowOpen.toString()">
                                <span class="ft-inquiry-workflow-icon">✓</span>
                                <span class="ft-inquiry-workflow-copy">
                                    <small>Default workflow</small>
                                    <strong><?php echo e($selectedWorkflowName); ?></strong>
                                    <span><?php echo e($createWorkflowPhaseCount); ?> <?php echo e(\Illuminate\Support\Str::plural('phase', $createWorkflowPhaseCount)); ?> · <?php echo e($createWorkflowTaskCount); ?> <?php echo e(\Illuminate\Support\Str::plural('task', $createWorkflowTaskCount)); ?> will be created</span>
                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('workflow.manage')): ?>
                                    <a href="<?php echo e(route('workflow.setup')); ?>" wire:navigate x-on:click.stop>Preview workflow ↗</a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="ft-inquiry-workflow-chevron" aria-hidden="true">⌄</span>
                            </div>

                            <div class="ft-inquiry-workflow-options" x-cloak x-show="workflowOpen">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $workflowFilterOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflowOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button type="button" class="ft-inquiry-workflow-option <?php echo e((int) ($workflowOption['id'] ?? 0) === (int) $createWorkflowId ? 'is-selected' : ''); ?>"
                                        wire:click="setCreateSelector('createWorkflowId', '<?php echo e($workflowOption['id']); ?>')"
                                        x-on:click="workflowOpen = false">
                                        <span class="ft-inquiry-workflow-radio"></span>
                                        <span><strong><?php echo e($workflowOption['label']); ?></strong><small><?php echo e($workflowOption['meta'] ?: 'Inquiry workflow'); ?></small></span>
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($createWorkflowId && $createWorkflowTaskCount === 0): ?>
                            <small class="field-error">This Workflow has no active Task Pack tasks.</small>
                        <?php else: ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createWorkflowId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="ft-inquiry-workflow-footnote">Tasks are created when you select Create inquiry.</p>
                    </section>

                    <div class="formactions ft-inquiry-create-actions">
                        <span>Required fields are marked with *</span>
                        <div>
                            <button class="secondary" type="button" wire:click="cancelCreate">Cancel</button>
                            <button class="secondary" type="button" wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft">Save draft</button>
                            <button class="primary" type="button" wire:click="createInquiry" wire:loading.attr="disabled" wire:target="createInquiry">Create inquiry <kbd>⌘ Enter</kbd></button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreateClientModal): ?>
                <div class="ft-inquiry-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-quick-client-modal'; ?>wire:key="inquiry-quick-client-modal" wire:click.self="closeCreateClientModal">
                    <section class="ft-inquiry-quick-client-modal" role="dialog" aria-modal="true" aria-labelledby="quick-client-title">
                        <header>
                            <div><h2 id="quick-client-title">Add new client</h2><p>Create the client with minimum information. You can complete the profile later.</p></div>
                            <button type="button" wire:click="closeCreateClientModal" aria-label="Close">×</button>
                        </header>
                        <div class="ft-inquiry-quick-client-body">
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Client name *</span><input wire:model="newClientName" placeholder="Company or client name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newClientName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><small>This is the only required field.</small></label>

                            <div class="ft-inquiry-modal-divider"></div>
                            <div class="ft-inquiry-modal-subhead"><strong>Primary contact (optional)</strong><span>Add contact details if they were provided with the inquiry.</span></div>
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Contact name</span><input wire:model="newClientContactName" placeholder="Full name"></label>
                            <div class="ft-inquiry-modal-grid">
                                <label class="ft-inquiry-modal-field"><span>Email</span><input type="email" wire:model="newClientEmail" placeholder="name@company.com"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newClientEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <label class="ft-inquiry-modal-field"><span>Phone</span><input wire:model="newClientPhone" placeholder="Phone number"></label>
                            </div>
                            <label class="ft-inquiry-contact-checkbox"><input type="checkbox" wire:model="useNewClientContactForInquiry"><span>Use this person as the inquiry contact</span></label>
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Country / region</span><input list="ft-country-regions" wire:model="newClientCountry" placeholder="Select country or region"><datalist id="ft-country-regions"><option value="Bangladesh"><option value="China"><option value="Hong Kong"><option value="India"><option value="United Kingdom"><option value="United States"><option value="Vietnam"><option value="Cambodia"><option value="Pakistan"><option value="Sri Lanka"><option value="United Arab Emirates"></datalist></label>
                            <div class="ft-inquiry-client-info">ⓘ <span>The new client will be selected automatically in this inquiry.</span></div>
                        </div>
                        <footer>
                            <span>Required fields are marked with *</span>
                            <div><button type="button" class="secondary" wire:click="closeCreateClientModal">Cancel</button><button type="button" class="primary" wire:click="createClientAndSelect" wire:loading.attr="disabled" wire:target="createClientAndSelect">Add &amp; select client</button></div>
                        </footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreateContactModal): ?>
                <div class="ft-inquiry-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-quick-contact-modal'; ?>wire:key="inquiry-quick-contact-modal" wire:click.self="closeCreateContactModal">
                    <section class="ft-inquiry-quick-client-modal ft-inquiry-quick-contact-modal" role="dialog" aria-modal="true" aria-labelledby="quick-contact-title">
                        <header><div><h2 id="quick-contact-title">Add client contact</h2><p>Add the primary contact for <?php echo e($selectedClientLabel ?: 'this client'); ?> and use it in this inquiry.</p></div><button type="button" wire:click="closeCreateContactModal" aria-label="Close">×</button></header>
                        <div class="ft-inquiry-quick-client-body">
                            <label class="ft-inquiry-modal-field ft-inquiry-modal-field-full"><span>Contact name *</span><input wire:model="newContactName" placeholder="Full name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newContactName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                            <div class="ft-inquiry-modal-grid">
                                <label class="ft-inquiry-modal-field"><span>Email</span><input type="email" wire:model="newContactEmail" placeholder="name@company.com"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newContactEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <label class="ft-inquiry-modal-field"><span>Phone</span><input wire:model="newContactPhone" placeholder="Phone number"></label>
                            </div>
                        </div>
                        <footer><span></span><div><button type="button" class="secondary" wire:click="closeCreateContactModal">Cancel</button><button type="button" class="primary" wire:click="saveCreateContact" wire:loading.attr="disabled" wire:target="saveCreateContact">Add contact</button></div></footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

    <?php else: ?>
        <?php
            $inquiry = $selectedInquiry;
            $totalTasks = (int) $inquiry->tasks_count;
            $completedTasks = (int) $inquiry->completed_tasks_count;
            $readyForDecision = !$inquiry->result && $totalTasks > 0 && $completedTasks === $totalTasks;
            $currentTask = $inquiry->currentTask;
            $firstStartedTask = $inquiry->tasks->whereNotNull('started_at')->sortBy('started_at')->first();
            $lastCompletedTask = $inquiry->tasks->whereNotNull('completed_at')->sortByDesc('completed_at')->first();
            $inquiryStartAt = $firstStartedTask?->started_at;
            $inquiryCompletedAt = $inquiry->completed_at ?: ($readyForDecision ? $lastCompletedTask?->completed_at : null);
            $detailStatus = match (true) {
                $inquiry->result === 'converted' => 'Converted',
                $inquiry->result === 'dead' => 'Closed',
                (string) $inquiry->status === 'Draft' => 'Draft',
                $readyForDecision => \App\Services\InquiryService::AUTO_COMPLETED_STATUS,
                $completedTasks > 0 || $inquiryStartAt !== null => \App\Services\InquiryService::AUTO_IN_PROGRESS_STATUS,
                default => \App\Services\InquiryService::AUTO_READY_STATUS,
            };
        ?>
        <section class="view inquiry-detail-view" x-data="{
            inquiryStatus:<?php echo \Illuminate\Support\Js::from($detailStatus)->toHtml() ?>,
            statusTone(status){
                if (String(status).includes('Converted') || String(status).includes('Completed')) return 'green';
                if (String(status).includes('Dead') || String(status).includes('Closed')) return 'red';
                if (String(status).includes('Ready') || String(status).includes('On Hold')) return 'amber';
                if (String(status).includes('Waiting')) return 'purple';
                return 'blue';
            },
            async saveTaskStatus(event, taskId){
                const previous=this.inquiryStatus;
                try{
                    const result=await $wire.updateTaskStatusInline(taskId,event.currentTarget.value);
                    if(result?.inquiryStatus)this.inquiryStatus=result.inquiryStatus;
                }catch(error){
                    this.inquiryStatus=previous;
                    window.location.reload();
                }
            }
        }">
            <div class="ft-detail-toolbar task-toolbar ft-exact-task-header ft-inquiry-exact-header">
                <div class="ft-task-heading-copy">
                    <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                        <a href="<?php echo e(route('inquiries.index')); ?>" wire:navigate>Inquiries</a>
                        <span>/</span><span><?php echo e($inquiry->inquiry_number); ?></span>
                    </div>
                    <div class="ft-task-title-line">
                        <h1
                            class="ft-editable-task-title ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-title')->toHtml() ?>, label: 'Inquiry title', value: <?php echo \Illuminate\Support\Js::from($inquiry->subject)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->subject)->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                        >
                            <span x-show="!editing" x-text="display"><?php echo e($inquiry->subject); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit Inquiry title" title="Edit Inquiry title" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryTitle.focus())">✎</button>
                                <input x-ref="inquiryTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                                    x-on:keydown.escape.prevent="cancelEdit()"
                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                    x-on:blur="if (editing) commit(draftValue.trim(), draftValue.trim(), () => $wire.updateInquiryField('subject', draftValue.trim()))">
                                <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                        </h1>
                    </div>
                    <div class="ft-inquiry-header-meta" aria-label="Inquiry information">
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span>Client <strong><?php echo e($inquiry->client?->name ?: '—'); ?></strong></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item ft-inquiry-header-reference">
                            <span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.5h7l4 4V20.5H7z"></path><path d="M14 3.5v4h4"></path></svg></span>
                            <span>Reference <strong><?php echo e($inquiry->reference_number ?: '—'); ?></strong></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->reference_number): ?>
                                <button type="button" class="ft-copy-id-btn ft-inquiry-header-copy" title="Copy Reference Number" aria-label="Copy reference number <?php echo e($inquiry->reference_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($inquiry->reference_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span>Created by <strong><?php echo e($inquiry->creator?->name ?: 'System'); ?></strong></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 10h16"></path></svg></span><span>Created <strong><?php echo e($inquiry->created_at ? \App\Support\UserLocalTime::format($inquiry->created_at, 'M j, Y') : '—'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->created_at): ?> at <?php echo e(\App\Support\UserLocalTime::format($inquiry->created_at, 'g:i A')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong></span></span>
                    </div>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" type="button">Overview</button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab === 'overview'): ?>
                <div class="tabpane ft-task-detail-page ft-exact-task-detail ft-inquiry-task-overview-exact">
                    <section class="ft-task-property-grid ft-friendly-task-properties ft-inquiry-overview-properties">
                        <div class="ft-task-property ft-inquiry-auto-status-property">
                            <small>Status</small>
                            <div class="ft-task-property-display">
                                <span class="status-dot" x-bind:class="statusTone(inquiryStatus)"></span>
                                <b class="ft-property-value" x-text="inquiryStatus"><?php echo e($detailStatus); ?></b>
                            </div>
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-priority')->toHtml() ?>, label: 'Inquiry priority', value: <?php echo \Illuminate\Support\Js::from($inquiry->priority)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->priority)->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Priority</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="status-dot amber"></span><b class="ft-property-value" x-text="display"><?php echo e($inquiry->priority); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?><button type="button" :disabled="status === 'saving'" title="Edit priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryPriority?.showPicker ? $refs.inquiryPriority.showPicker() : $refs.inquiryPriority?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select x-ref="inquiryPriority" x-model="draftValue" class="ft-task-property-inline-input" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateInquiryField('priority', draftValue))"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($inquiryPriorities->contains(fn($priority) => (string) $priority->name === (string) $inquiry->priority))): ?><option value="<?php echo e($inquiry->priority); ?>"><?php echo e($inquiry->priority); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>"><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
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

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-due-date')->toHtml() ?>, label: 'Inquiry next due date', value: <?php echo \Illuminate\Support\Js::from($currentTask?->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($currentTask?->due_date?->format('M j, Y') ?? '—')->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Due date</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value" x-text="display"><?php echo e($currentTask?->due_date?->format('M j, Y') ?? '—'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentTask && $canEditActiveTask): ?><button type="button" :disabled="status === 'saving'" title="Edit due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryOverviewDue?.showPicker ? $refs.inquiryOverviewDue.showPicker() : $refs.inquiryOverviewDue?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentTask && $canEditActiveTask): ?>
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><input x-ref="inquiryOverviewDue" x-model="draftValue" class="ft-task-property-inline-input" type="date" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueInline(<?php echo e($currentTask->id); ?>, draftValue))"></div>
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

                        <div class="ft-task-property">
                            <small>Start date</small>
                            <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value"><?php echo e($inquiryStartAt ? \App\Support\UserLocalTime::format($inquiryStartAt, 'M j, Y') : '—'); ?></b></div>
                        </div>

                        <div class="ft-task-property ft-task-completed-property">
                            <small>Completed On</small>
                            <div class="ft-task-property-display"><span class="ft-calendar-glyph">▣</span><b class="ft-property-value ft-completed-date-time"><span><?php echo e($inquiryCompletedAt ? \App\Support\UserLocalTime::format($inquiryCompletedAt, 'M j, Y') : '—'); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryCompletedAt): ?><span class="ft-completed-time"><?php echo e(\App\Support\UserLocalTime::format($inquiryCompletedAt, 'g:i A')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></b></div>
                        </div>
                    </section>

                    <section
                        class="ft-detail-card ft-inquiry-description-card ft-inline-edit-shell"
                        x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-description')->toHtml() ?>, label: 'Inquiry description', value: <?php echo \Illuminate\Support\Js::from($inquiry->requirement_notes ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->requirement_notes ?: 'No description has been provided for this Inquiry.')->toHtml() ?> })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <div class="ft-inquiry-description-head">
                            <h2>Description</h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit description" aria-label="Edit Inquiry description" x-on:click.stop="beginRichTextEdit($refs.inquiryDescription)">✎</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div x-show="!editing" class="ft-rich-text-content ft-inquiry-description-content">
                            <div x-show="!hasRichTextOverride"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->requirement_notes): ?><?php if (isset($component)) { $__componentOriginal1d83f45bf838052fadc84bf85b829e43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d83f45bf838052fadc84bf85b829e43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.mention-text','data' => ['text' => $inquiry->requirement_notes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.mention-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->requirement_notes)]); ?>
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
<?php endif; ?><?php else: ?> No description has been provided for this Inquiry. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <div x-cloak x-show="hasRichTextOverride" x-html="richTextOverrideHtml"></div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                            <div x-cloak x-show="editing" class="ft-inquiry-description-editor ft-inline-description-editor">
                                <textarea x-ref="inquiryDescription" data-rich-text placeholder="Add the client requirement or Inquiry description, or paste screenshots here..."><?php echo e($inquiry->requirement_notes ?? ''); ?></textarea>
                                <div class="ft-inquiry-description-editor-actions">
                                    <button type="button" class="secondary" x-on:click="cancelRichTextEdit($refs.inquiryDescription)">Cancel</button>
                                    <button type="button" class="primary" data-rich-text-submit :disabled="status === 'saving'" x-on:click="saveRichText($refs.inquiryDescription, 'No description has been provided for this Inquiry.', (clean) => $wire.updateInquiryField('requirement_notes', clean))">Save</button>
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
                    </section>

                    <div id="tab-workflow" class="ft-inquiry-overview-taskflow ft-inquiry-workflow-pane">
                        <?php echo $__env->make('livewire.inquiries._taskflow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>

                    <?php echo $__env->make('livewire.inquiries._attachments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('livewire.inquiries._activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTaskDocumentModal && $taskDocumentModalTask): ?>
                <div class="ft-inquiry-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-document-modal'; ?>wire:key="inquiry-task-document-modal" wire:click.self="closeTaskDocumentModal">
                    <section class="ft-inquiry-task-document-modal" role="dialog" aria-modal="true" aria-labelledby="task-document-modal-title">
                        <header class="ft-inquiry-task-document-modal-head">
                            <div>
                                <h2 id="task-document-modal-title">Add new document to task</h2>
                                <p>Upload a new file or choose a document that already exists.</p>
                            </div>
                            <button type="button" class="ft-inquiry-task-document-modal-close" wire:click="closeTaskDocumentModal" aria-label="Close">×</button>
                        </header>

                        <div class="ft-inquiry-task-document-modal-body">
                            <div class="ft-inquiry-task-document-target">
                                <span class="ft-inquiry-task-document-target-icon">▣</span>
                                <div>
                                    <small>ATTACHING TO</small>
                                    <strong><?php echo e($taskDocumentModalTask->title); ?></strong>
                                    <span>INQ-TASK-<?php echo e(str_pad((string) $taskDocumentModalTask->id, 5, '0', STR_PAD_LEFT)); ?> &nbsp;·&nbsp; <?php echo e($inquiry->sourceWorkflow?->name ?? 'Inquiry Taskflow'); ?></span>
                                </div>
                                <span class="ft-inquiry-task-document-target-lock">▣&nbsp; Task selected</span>
                            </div>

                            <div class="ft-inquiry-task-document-source-label">Document source</div>
                            <div class="ft-inquiry-task-document-source-tabs">
                                <button type="button" class="<?php echo e($taskDocumentSource === 'upload' ? 'active' : ''); ?>" wire:click="setTaskDocumentSource('upload')">
                                    <span>↥</span> Upload new
                                </button>
                                <button type="button" class="<?php echo e($taskDocumentSource === 'existing' ? 'active' : ''); ?>" wire:click="setTaskDocumentSource('existing')" <?php if(!$canLinkDocuments): echo 'disabled'; endif; ?>>
                                    <span>▤</span> Choose existing
                                </button>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocumentSource === 'upload'): ?>
                                <label class="ft-inquiry-task-document-dropzone">
                                    <input type="file" wire:model="taskDocumentUpload" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai">
                                    <span class="ft-inquiry-task-document-upload-icon">⇧</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocumentUpload): ?>
                                        <strong><?php echo e($taskDocumentUpload->getClientOriginalName()); ?></strong>
                                        <b>File selected — choose another file</b>
                                        <small><?php echo e(number_format(max(1, (int) ceil($taskDocumentUpload->getSize() / 1024)))); ?> KB · ready to add</small>
                                    <?php else: ?>
                                        <strong>Drop a file here</strong>
                                        <b>or browse files</b>
                                        <small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskDocumentUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <div class="ft-inquiry-task-document-existing">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availableTaskDocuments->isEmpty()): ?>
                                        <div class="ft-inquiry-task-document-existing-empty">No existing client documents are available.</div>
                                    <?php else: ?>
                                        <label>
                                            <span>Choose an existing document</span>
                                            <select wire:model="taskExistingDocumentId">
                                                <option value="">Select a document...</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableTaskDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sourceDocument): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <option value="<?php echo e($sourceDocument->id); ?>"><?php echo e($sourceDocument->name); ?></option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </select>
                                        </label>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskExistingDocumentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <label class="ft-inquiry-task-document-note">
                                <span>Document note (optional)</span>
                                <input type="text" wire:model="taskDocumentNote" placeholder="Add a short note about this document...">
                            </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskDocumentNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="ft-inquiry-task-document-info">
                                <span>ⓘ</span>
                                <p>This document will appear directly under <strong><?php echo e($taskDocumentModalTask->title); ?></strong>.</p>
                            </div>
                        </div>

                        <footer class="ft-inquiry-task-document-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskDocumentModal">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskDocument" wire:loading.attr="disabled" wire:target="saveTaskDocument,taskDocumentUpload"
                                <?php if($taskDocumentSource === 'upload' ? !$taskDocumentUpload : !$taskExistingDocumentId): echo 'disabled'; endif; ?>>
                                <span wire:loading.remove wire:target="saveTaskDocument">Add document</span>
                                <span wire:loading wire:target="saveTaskDocument">Adding...</span>
                            </button>
                        </footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/index.blade.php ENDPATH**/ ?>