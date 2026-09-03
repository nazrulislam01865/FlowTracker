        <?php
            $inquiry = $selectedInquiry;
            $totalTasks = (int) $inquiry->tasks_count;
            $completedTasks = (int) $inquiry->completed_tasks_count;
            $readyForDecision = !$inquiry->result && $totalTasks > 0 && $completedTasks === $totalTasks;
            $currentTask = $inquiry->currentTask;
            $firstStartedTask = $inquiry->tasks->whereNotNull('started_at')->sortBy('started_at')->first();
            $lastCompletedTask = $inquiry->tasks->whereNotNull('completed_at')->sortByDesc('completed_at')->first();
            $inquiryStartAt = $inquiry->started_at ?: $firstStartedTask?->started_at;
            $inquiryStartLocal = \App\Support\UserLocalTime::localize($inquiryStartAt);
            $inquiryCompletedAt = $inquiry->completed_at ?: ($readyForDecision ? $lastCompletedTask?->completed_at : null);
            $detailStatus = match (true) {
                $inquiry->result === 'converted' => 'Converted',
                $inquiry->result === 'dead' => 'Closed',
                (string) $inquiry->status === 'Draft' => 'Draft',
                default => (string) ($inquiry->status ?: $inquiryDefaultStatus),
            };
            $detailStatusColor = $detailStatusColor ?? null;
            $detailPriorityColor = $masterData->displayColorFor('priority', (string) $inquiry->priority);
            $headerFlagTask = $currentTask?->needs_attention ? $currentTask : $inquiry->tasks->first(fn ($task) => (bool) $task->needs_attention);
            $headerFlagLabel = $inquiry->needs_attention
                ? 'Requires attention'
                : ($headerFlagTask ? 'Requires attention' : '');
            $headerFlagReason = $inquiry->needs_attention
                ? (string) ($inquiry->attention_reason ?? '')
                : (string) ($headerFlagTask?->attention_reason ?? '');
        ?>
        <section class="view inquiry-detail-view ft-detail-products-scope" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-detail-'.e($inquiry->id).''; ?>wire:key="inquiry-detail-<?php echo e($inquiry->id); ?>" x-data="{
            inquiryStatus:<?php echo \Illuminate\Support\Js::from($detailStatus)->toHtml() ?>,
            inquiryStatusColor:<?php echo \Illuminate\Support\Js::from($detailStatusColor)->toHtml() ?>,
            inquiryStartValue:<?php echo \Illuminate\Support\Js::from($inquiryStartLocal?->format('Y-m-d\TH:i') ?? '')->toHtml() ?>,
            inquiryStartDisplay:<?php echo \Illuminate\Support\Js::from($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—')->toHtml() ?>,
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
                    if(result?.inquiryColor)this.inquiryStatusColor=result.inquiryColor;
                    if(result && Object.prototype.hasOwnProperty.call(result,'inquiryStartValue')){
                        this.inquiryStartValue=result.inquiryStartValue || '';
                        this.inquiryStartDisplay=result.inquiryStartDisplay || '—';
                        window.dispatchEvent(new CustomEvent('flowtrack-inquiry-started',{detail:{value:this.inquiryStartValue,display:this.inquiryStartDisplay}}));
                    }
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
                        <span>/</span>
                        <span class="ft-copyable-id-wrap ft-inquiry-detail-code-wrap">
                            <span><?php echo e($inquiry->inquiry_number); ?></span>
                            <button type="button" class="ft-copy-id-btn" title="Copy Inquiry ID" aria-label="Copy <?php echo e($inquiry->inquiry_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($inquiry->inquiry_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'copy']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'copy']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button>
                        </span>
                    </div>
                    <div class="ft-task-title-line">
                        <h1
                            class="ft-editable-task-title ft-inline-edit-shell"
                            x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-title')->toHtml() ?>, label: 'Inquiry title', value: <?php echo \Illuminate\Support\Js::from($inquiry->subject)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->subject)->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                        >
                            <span x-show="!editing" x-text="display"><?php echo e($inquiry->subject); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil ft-detail-edit-button" aria-label="Edit Inquiry title" title="Edit Inquiry title" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryTitle.focus())"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button>
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
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span class="ft-client-inline-identity"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $inquiry->client,'name' => $inquiry->client?->name ?: 'Client','size' => 20]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->client?->name ?: 'Client'),'size' => 20]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?><span>Client <strong><?php echo e($inquiry->client?->name ?: '—'); ?></strong></span></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span>Client contact <strong><?php echo e($inquiry->client_contact ?: '—'); ?></strong></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item ft-inquiry-header-reference">
                            <span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.5h7l4 4V20.5H7z"></path><path d="M14 3.5v4h4"></path></svg></span>
                            <span>Reference <strong><?php echo e($inquiry->reference_number ?: '—'); ?></strong></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->reference_number): ?>
                                <button type="button" class="ft-copy-id-btn ft-inquiry-header-copy" title="Copy Reference Number" aria-label="Copy reference number <?php echo e($inquiry->reference_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($inquiry->reference_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'copy']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'copy']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5"></circle><path d="M5.5 19c.8-3.4 3-5.2 6.5-5.2s5.7 1.8 6.5 5.2"></path></svg></span><span>Created by <strong><?php echo e($inquiry->creator?->name ?: 'System'); ?></strong></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item"><span class="ft-inquiry-header-meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 10h16"></path></svg></span><span>Created <strong><?php echo e($inquiry->created_at ? \App\Support\UserLocalTime::format($inquiry->created_at, 'M j, Y') : '—'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->created_at): ?> at <?php echo e(\App\Support\UserLocalTime::format($inquiry->created_at, 'g:i A')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong></span></span>
                        <span class="ft-inquiry-header-meta-separator" aria-hidden="true">•</span>
                        <span class="ft-inquiry-header-meta-item ft-inquiry-header-action" title="<?php echo e($headerFlagReason ?: 'Request attention from the Inquiry creator and administrators'); ?>">
                            <span>Action:</span>
                            <button type="button" class="ft-inquiry-header-flag-button <?php echo e($headerFlagLabel !== '' ? 'is-flagged' : ''); ?>" wire:click="openInquiryAttentionReason" <?php if($inquiry->result): echo 'disabled'; endif; ?> aria-label="Request attention" title="<?php echo e($headerFlagLabel !== '' ? 'View or update attention request' : 'Request attention'); ?>">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 21V4"></path><path d="M7 5h10l-2 4 2 4H7"></path></svg>
                            </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($headerFlagLabel !== ''): ?><strong class="ft-inquiry-header-flag-label"><?php echo e($headerFlagLabel); ?></strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="tabs ft-inquiry-detail-tabs" role="tablist" aria-label="Inquiry detail sections">
                <button class="tab <?php echo e($detailTab === 'overview' ? 'active' : ''); ?>" type="button" wire:click="setDetailTab('overview')">Overview</button>
                <button class="tab <?php echo e($detailTab === 'rfq' ? 'active' : ''); ?>" type="button" wire:click="setDetailTab('rfq')">RFQ <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($inquiryRfqSummary['invited'] ?? 0) > 0): ?><span class="ft-inquiry-tab-count"><?php echo e($inquiryRfqSummary['invited']); ?> invitations</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></button>
                <button class="tab <?php echo e($detailTab === 'comparison' ? 'active' : ''); ?>" type="button" wire:click="setDetailTab('comparison')">Comparison statement <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($inquiryRfqSummary['submitted'] ?? 0) > 0): ?><span class="ft-inquiry-tab-count is-green"><?php echo e($inquiryRfqSummary['submitted']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab === 'overview'): ?>
                <div class="tabpane ft-task-detail-page ft-exact-task-detail ft-inquiry-task-overview-exact" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-detail-overview-'.e($inquiry->id).''; ?>wire:key="inquiry-detail-overview-<?php echo e($inquiry->id); ?>">
                    <section class="ft-task-property-grid ft-friendly-task-properties ft-inquiry-overview-properties">
                        <div class="ft-task-property ft-inquiry-auto-status-property">
                            <small>Status</small>
                            <div class="ft-task-property-display">
                                <span class="status-dot <?php echo e($detailStatusColor ? 'ft-master-color-dot' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($detailStatusColor)); ?>" x-bind:class="inquiryStatusColor ? 'ft-master-color-dot' : statusTone(inquiryStatus)" x-bind:style="inquiryStatusColor ? '--ft-master-color:'+inquiryStatusColor : ''"></span>
                                <b class="ft-property-value" x-text="inquiryStatus"><?php echo e($detailStatus); ?></b>
                            </div>
                        </div>

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="{ ...window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-priority')->toHtml() ?>, label: 'Inquiry priority', value: <?php echo \Illuminate\Support\Js::from($inquiry->priority)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->priority)->toHtml() ?> }), priorityColor: <?php echo \Illuminate\Support\Js::from($detailPriorityColor)->toHtml() ?> }"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Priority</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="status-dot ft-master-color-dot" style="<?php echo e(\App\Support\MasterColor::style($detailPriorityColor)); ?>" x-bind:style="priorityColor ? '--ft-master-color:'+priorityColor : ''"></span><b class="ft-property-value" x-text="display"><?php echo e($inquiry->priority); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?><button type="button" :disabled="status === 'saving'" class="ft-property-edit-button" title="Edit priority" aria-label="Edit Inquiry priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryPriority?.showPicker ? $refs.inquiryPriority.showPicker() : $refs.inquiryPriority?.focus())"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select data-master-color-select x-ref="inquiryPriority" x-model="draftValue" class="ft-task-property-inline-input ft-master-color" style="<?php echo e(\App\Support\MasterColor::style($detailPriorityColor)); ?>" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="const nextColor=String($event.target.selectedOptions[0]?.dataset?.color || ''); window.FlowTrack.ui.masterColor?.applySelect($event.target); commit($event.target.value, selectedLabel($event), () => $wire.updateInquiryField('priority', draftValue)).then(ok => { if(ok) priorityColor=nextColor; });"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($inquiryPriorities->contains(fn($priority) => (string) $priority->name === (string) $inquiry->priority))): ?><option value="<?php echo e($inquiry->priority); ?>" data-color="<?php echo e($masterData->displayColorFor('priority', (string) $inquiry->priority)); ?>"><?php echo e($inquiry->priority); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>" data-color="<?php echo e($masterData->displayColorFor('priority', $priority->name)); ?>"><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
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
                            x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-assignee')->toHtml() ?>, label: 'Inquiry assignee', value: <?php echo \Illuminate\Support\Js::from($inquiry->owner_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->owner?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($inquiry->owner?->profileImageUrl() ?? '')->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                            x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                            x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateInquiryField('owner_id', draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
                        >
                            <small>Assignee</small>
                            <div x-show="!editing" class="ft-task-property-display ft-inline-person-live">
                                <?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 26]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 26]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $attributes = $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127)): ?>
<?php $component = $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127; ?>
<?php unset($__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127); ?>
<?php endif; ?>
                                <b class="ft-property-value" x-text="display"><?php echo e($inquiry->owner?->name ?? 'Unassigned'); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && $canAssignInquiry && !$inquiry->result): ?><button type="button" :disabled="status === 'saving'" class="ft-property-edit-button" title="Edit assignee" aria-label="Edit Inquiry assignee" x-on:click.stop="openRemotePicker($event.currentTarget)"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && $canAssignInquiry && !$inquiry->result): ?>
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor ft-task-property-assignee-editor">
                                    <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $inquiry->owner_id ?? '','selectedLabel' => $inquiry->owner?->name ?? 'Unassigned','context' => 'inquiry-owner','parentType' => 'inquiry','parentId' => $inquiry->id,'searchPlaceholder' => 'Search assignee…','triggerClass' => 'ft-task-property-inline-input','variant' => 'compact','menuWidth' => 300,'fixedMenu' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->owner_id ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->owner?->name ?? 'Unassigned'),'context' => 'inquiry-owner','parent-type' => 'inquiry','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->id),'search-placeholder' => 'Search assignee…','trigger-class' => 'ft-task-property-inline-input','variant' => 'compact','menu-width' => 300,'fixed-menu' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $attributes = $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607)): ?>
<?php $component = $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607; ?>
<?php unset($__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607); ?>
<?php endif; ?>
                                </div>
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
                            x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-due-date')->toHtml() ?>, label: 'Inquiry next due date', value: <?php echo \Illuminate\Support\Js::from($currentTask?->due_date?->format('Y-m-d') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($currentTask?->due_date?->format('M j, Y') ?? '—')->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Due date</small>
                            <div x-show="!editing" class="ft-task-property-display"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'calendar','class' => 'ft-calendar-glyph']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','class' => 'ft-calendar-glyph']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?><b class="ft-property-value" x-text="display"><?php echo e($currentTask?->due_date?->format('M j, Y') ?? '—'); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentTask && $canEditActiveTask): ?><button type="button" :disabled="status === 'saving'" class="ft-property-edit-button" title="Edit due date" aria-label="Edit Inquiry due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryOverviewDue?.showPicker ? $refs.inquiryOverviewDue.showPicker() : $refs.inquiryOverviewDue?.focus())"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
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

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-start-at')->toHtml() ?>, label: 'Inquiry start date', value: <?php echo \Illuminate\Support\Js::from($inquiryStartLocal?->format('Y-m-d\TH:i') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—')->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                            x-on:flowtrack-inquiry-started.window="const v=String($event.detail?.value ?? ''); const d=String($event.detail?.display ?? '—'); serverValue=v; value=v; savedValue=v; draftValue=v; display=d; savedDisplay=d;"
                        >
                            <small>Start date</small>
                            <div x-show="!editing" class="ft-task-property-display">
                                <?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'calendar','class' => 'ft-calendar-glyph']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','class' => 'ft-calendar-glyph']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
                                <b class="ft-property-value" x-text="display"><?php echo e($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—'); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                    <button type="button" :disabled="status === 'saving'" class="ft-property-edit-button" title="Edit start date and time" aria-label="Edit Inquiry start date and time" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryStartAt?.showPicker ? $refs.inquiryStartAt.showPicker() : $refs.inquiryStartAt?.focus())"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor">
                                    <input x-ref="inquiryStartAt" x-model="draftValue" class="ft-task-property-inline-input" type="datetime-local" step="60"
                                        x-on:keydown.escape.prevent="cancelEdit()"
                                        x-on:change="commit($event.target.value, formatDateTime($event.target.value), () => $wire.updateInquiryStartInline(draftValue))">
                                </div>
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

                        <div class="ft-task-property ft-task-completed-property">
                            <small>Completed On</small>
                            <div class="ft-task-property-display"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'calendar','class' => 'ft-calendar-glyph']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','class' => 'ft-calendar-glyph']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?><b class="ft-property-value ft-completed-date-time"><span><?php echo e($inquiryCompletedAt ? \App\Support\UserLocalTime::format($inquiryCompletedAt, 'M j, Y') : '—'); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryCompletedAt): ?><span class="ft-completed-time"><?php echo e(\App\Support\UserLocalTime::format($inquiryCompletedAt, 'g:i A')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></b></div>
                        </div>
                    </section>

                    <section
                        class="ft-detail-card ft-inquiry-description-card ft-inline-edit-shell"
                        x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-description')->toHtml() ?>, label: 'Inquiry description', value: <?php echo \Illuminate\Support\Js::from($inquiry->requirement_notes ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->requirement_notes ?: 'No description has been provided for this Inquiry.')->toHtml() ?> })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <div class="ft-inquiry-description-head">
                            <h2>Description</h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button ft-detail-edit-button" title="Edit description" aria-label="Edit Inquiry description" x-on:click.stop="beginRichTextEdit($refs.inquiryDescription)"><?php if (isset($component)) { $__componentOriginal7a790559b5e43ef61a01a84d7976ba02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.detail-icon','data' => ['name' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.detail-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $attributes = $__attributesOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__attributesOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02)): ?>
<?php $component = $__componentOriginal7a790559b5e43ef61a01a84d7976ba02; ?>
<?php unset($__componentOriginal7a790559b5e43ef61a01a84d7976ba02); ?>
<?php endif; ?></button>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewInquiryProducts): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($inquiryDetailSectionsReady['products'] ?? false)): ?>
                    <?php
                        $inquiryProductOverview = $inquiryProductRfqOverview ?? [
                            'stats' => ['products' => 0, 'supplier_assignments' => 0, 'invitations_sent' => 0, 'quotations_received' => 0],
                            'rows' => collect(),
                            'product_count' => 0,
                            'total_units' => 0,
                        ];
                        $inquiryOverviewRows = collect($inquiryProductOverview['rows'] ?? []);
                    ?>
                    <?php if (isset($component)) { $__componentOriginal7fe1b9851f610f81b499d6fe69a56e6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7fe1b9851f610f81b499d6fe69a56e6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.product-rfq-overview','data' => ['id' => 'inquiry-products-card','overview' => $inquiryProductOverview,'canAdd' => $canCreateInquiryProducts,'showAddForm' => $showAddInquiryProductForm]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.product-rfq-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'inquiry-products-card','overview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductOverview),'can-add' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canCreateInquiryProducts),'show-add-form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showAddInquiryProductForm)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inquiryOverviewRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overviewRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal0829c255f5b69659334b1b1045912cb9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0829c255f5b69659334b1b1045912cb9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries.product-rfq-overview-row','data' => ['row' => $overviewRow,'canEdit' => $canEditInquiryProducts,'canDelete' => $canDeleteInquiryProducts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries.product-rfq-overview-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overviewRow),'can-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canEditInquiryProducts),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteInquiryProducts)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0829c255f5b69659334b1b1045912cb9)): ?>
<?php $attributes = $__attributesOriginal0829c255f5b69659334b1b1045912cb9; ?>
<?php unset($__attributesOriginal0829c255f5b69659334b1b1045912cb9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0829c255f5b69659334b1b1045912cb9)): ?>
<?php $component = $__componentOriginal0829c255f5b69659334b1b1045912cb9; ?>
<?php unset($__componentOriginal0829c255f5b69659334b1b1045912cb9); ?>
<?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiryProducts && (int) $editInquiryProductItemId === (int) ($overviewRow['item_id'] ?? 0)): ?>
                                <tr class="ft-detail-product-editor-row ft-inquiry-prq-editor-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-product-inline-editor-'.e((int) ($overviewRow['item_id'] ?? 0)).''; ?>wire:key="inquiry-product-inline-editor-<?php echo e((int) ($overviewRow['item_id'] ?? 0)); ?>">
                                    <td colspan="7">
                                        <?php if (isset($component)) { $__componentOriginal0413a3f88fce063db5626ac70d24ba80 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0413a3f88fce063db5626ac70d24ba80 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-edit','data' => ['wireKey' => 'inquiry-product-edit-'.(int) ($overviewRow['item_id'] ?? 0),'variant' => 'inquiry','recordLabel' => 'Inquiry','searchModel' => 'editInquiryProductSearch','searchValue' => $editInquiryProductSearch,'searchResults' => $editInquiryProductSearchResults,'searchSuppliers' => $editInquiryProductSearchSuppliers,'resultTotal' => $editInquiryProductResultTotal,'showAllResults' => $editInquiryProductShowAllResults,'showAllMethod' => 'showAllEditInquiryProductResults','selectMethod' => 'selectEditInquiryProduct','selectedProduct' => $editInquiryProductSelectedProduct,'selectedSupplier' => $editInquiryProductSelectedSupplier,'categoryValue' => $editInquiryProductCategory,'quantityModel' => 'editInquiryProductQuantity','quantityValue' => $editInquiryProductQuantity,'unitPriceValue' => $editInquiryProductUnitPrice,'notesModel' => 'editInquiryProductNotes','notesValue' => $editInquiryProductNotes,'supplierEditable' => false,'currencySymbol' => $inquiryCurrencySymbol,'closeMethod' => 'closeEditInquiryProduct','saveMethod' => 'saveEditInquiryProduct','selectedErrorKey' => 'editInquiryProductSelectedId','quantityErrorKey' => 'editInquiryProductQuantity','unitPriceErrorKey' => 'editInquiryProductUnitPrice','notesErrorKey' => 'editInquiryProductNotes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-edit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('inquiry-product-edit-'.(int) ($overviewRow['item_id'] ?? 0)),'variant' => 'inquiry','record-label' => 'Inquiry','search-model' => 'editInquiryProductSearch','search-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductSearch),'search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductSearchResults),'search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductSearchSuppliers),'result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductResultTotal),'show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductShowAllResults),'show-all-method' => 'showAllEditInquiryProductResults','select-method' => 'selectEditInquiryProduct','selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductSelectedProduct),'selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductSelectedSupplier),'category-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductCategory),'quantity-model' => 'editInquiryProductQuantity','quantity-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductQuantity),'unit-price-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductUnitPrice),'notes-model' => 'editInquiryProductNotes','notes-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editInquiryProductNotes),'supplier-editable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'currency-symbol' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryCurrencySymbol),'close-method' => 'closeEditInquiryProduct','save-method' => 'saveEditInquiryProduct','selected-error-key' => 'editInquiryProductSelectedId','quantity-error-key' => 'editInquiryProductQuantity','unit-price-error-key' => 'editInquiryProductUnitPrice','notes-error-key' => 'editInquiryProductNotes']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0413a3f88fce063db5626ac70d24ba80)): ?>
<?php $attributes = $__attributesOriginal0413a3f88fce063db5626ac70d24ba80; ?>
<?php unset($__attributesOriginal0413a3f88fce063db5626ac70d24ba80); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0413a3f88fce063db5626ac70d24ba80)): ?>
<?php $component = $__componentOriginal0413a3f88fce063db5626ac70d24ba80; ?>
<?php unset($__componentOriginal0413a3f88fce063db5626ac70d24ba80); ?>
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="ft-inquiry-prq-empty-row">
                                <td colspan="7">No products have been added to this Inquiry yet.</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                         <?php $__env->slot('afterTable', null, []); ?> 
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddInquiryProductForm && $canCreateInquiryProducts): ?>
                                <div class="ft-detail-products-inline-add ft-inquiry-prq-inline-add" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-add-product-inline-'.e($inquiry->id).''; ?>wire:key="inquiry-add-product-inline-<?php echo e($inquiry->id); ?>" x-data x-on:keydown.escape.window="$wire.closeAddInquiryProductForm()">
                                    <?php if (isset($component)) { $__componentOriginal5e4da558653258c1bfe993ad392b6247 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e4da558653258c1bfe993ad392b6247 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-add-product','data' => ['wireKey' => 'inquiry-detail-add-product-'.$inquiry->id,'searchModel' => 'inquiryProductSearch','searchValue' => $inquiryProductSearch,'searchResults' => $inquiryProductSearchResults,'searchSuppliers' => $inquiryProductSearchSuppliers,'resultTotal' => $inquiryProductResultTotal,'showAllResults' => $inquiryProductShowAllResults,'showAllMethod' => 'showAllInquiryProductResults','selectMethod' => 'selectInquiryProduct','selectedProduct' => $inquiryProductSelectedProduct,'selectedSupplier' => $inquiryProductSelectedSupplier,'categoryValue' => $inquiryProductCategory,'quantityModel' => 'inquiryProductQuantity','quantityValue' => $inquiryProductQuantity,'unitPriceModel' => 'inquiryProductUnitPrice','unitPriceValue' => $inquiryProductUnitPrice,'currencySymbol' => $inquiryCurrencySymbol,'closeMethod' => 'closeAddInquiryProductForm','saveMethod' => 'saveInquiryProduct','selectedErrorKey' => 'inquiryProductSelectedId','quantityErrorKey' => 'inquiryProductQuantity','unitPriceErrorKey' => 'inquiryProductUnitPrice','recordLabel' => 'Inquiry']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-add-product'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('inquiry-detail-add-product-'.$inquiry->id),'search-model' => 'inquiryProductSearch','search-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSearch),'search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSearchResults),'search-suppliers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSearchSuppliers),'result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductResultTotal),'show-all-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductShowAllResults),'show-all-method' => 'showAllInquiryProductResults','select-method' => 'selectInquiryProduct','selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSelectedProduct),'selected-supplier' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSelectedSupplier),'category-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductCategory),'quantity-model' => 'inquiryProductQuantity','quantity-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductQuantity),'unit-price-model' => 'inquiryProductUnitPrice','unit-price-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductUnitPrice),'currency-symbol' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryCurrencySymbol),'close-method' => 'closeAddInquiryProductForm','save-method' => 'saveInquiryProduct','selected-error-key' => 'inquiryProductSelectedId','quantity-error-key' => 'inquiryProductQuantity','unit-price-error-key' => 'inquiryProductUnitPrice','record-label' => 'Inquiry']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e4da558653258c1bfe993ad392b6247)): ?>
<?php $attributes = $__attributesOriginal5e4da558653258c1bfe993ad392b6247; ?>
<?php unset($__attributesOriginal5e4da558653258c1bfe993ad392b6247); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e4da558653258c1bfe993ad392b6247)): ?>
<?php $component = $__componentOriginal5e4da558653258c1bfe993ad392b6247; ?>
<?php unset($__componentOriginal5e4da558653258c1bfe993ad392b6247); ?>
<?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7fe1b9851f610f81b499d6fe69a56e6a)): ?>
<?php $attributes = $__attributesOriginal7fe1b9851f610f81b499d6fe69a56e6a; ?>
<?php unset($__attributesOriginal7fe1b9851f610f81b499d6fe69a56e6a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7fe1b9851f610f81b499d6fe69a56e6a)): ?>
<?php $component = $__componentOriginal7fe1b9851f610f81b499d6fe69a56e6a; ?>
<?php unset($__componentOriginal7fe1b9851f610f81b499d6fe69a56e6a); ?>
<?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'products','method' => 'loadDetailSection','keyPrefix' => 'inquiry-detail','contextType' => 'inquiry','contextId' => $inquiry->id,'rows' => 4,'message' => 'Loading Inquiry products when needed…','rootMargin' => '360px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'products','method' => 'loadDetailSection','key-prefix' => 'inquiry-detail','context-type' => 'inquiry','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->id),'rows' => 4,'message' => 'Loading Inquiry products when needed…','root-margin' => '360px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div id="tab-workflow" class="ft-inquiry-overview-taskflow ft-inquiry-workflow-pane">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('tasks', 'view')): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($inquiryDetailSectionsReady['taskflow'] ?? false)): ?>
                                <?php echo $__env->make('livewire.inquiries._taskflow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'taskflow','method' => 'loadDetailSection','keyPrefix' => 'inquiry-detail','contextType' => 'inquiry','contextId' => $inquiry->id,'rows' => 5,'message' => 'Loading Inquiry taskflow when needed…','rootMargin' => '360px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'taskflow','method' => 'loadDetailSection','key-prefix' => 'inquiry-detail','context-type' => 'inquiry','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->id),'rows' => 5,'message' => 'Loading Inquiry taskflow when needed…','root-margin' => '360px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <section class="panel"><div class="ft-inquiry-empty-workflow">Task access is not enabled for your role.</div></section>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents', 'view')): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($inquiryDetailSectionsReady['documents'] ?? false)): ?>
                            <?php echo $__env->make('livewire.inquiries._attachments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'documents','method' => 'loadDetailSection','keyPrefix' => 'inquiry-detail','contextType' => 'inquiry','contextId' => $inquiry->id,'rows' => 3,'message' => 'Loading Inquiry attachments when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'documents','method' => 'loadDetailSection','key-prefix' => 'inquiry-detail','context-type' => 'inquiry','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->id),'rows' => 3,'message' => 'Loading Inquiry attachments when needed…','root-margin' => '300px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($inquiryDetailSectionsReady['activity'] ?? false)): ?>
                        <?php echo $__env->make('livewire.inquiries._activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'activity','method' => 'loadDetailSection','keyPrefix' => 'inquiry-detail','contextType' => 'inquiry','contextId' => $inquiry->id,'rows' => 4,'message' => 'Loading Inquiry activity when needed…','rootMargin' => '300px 0px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'activity','method' => 'loadDetailSection','key-prefix' => 'inquiry-detail','context-type' => 'inquiry','context-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiry->id),'rows' => 4,'message' => 'Loading Inquiry activity when needed…','root-margin' => '300px 0px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $attributes = $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__attributesOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20)): ?>
<?php $component = $__componentOriginal07ce51f35701acdfae5fc6353e53cc20; ?>
<?php unset($__componentOriginal07ce51f35701acdfae5fc6353e53cc20); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
            <?php elseif($detailTab === 'rfq'): ?>
                <?php echo $__env->make('livewire.inquiries.sections.rfq', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php elseif($detailTab === 'comparison'): ?>
                <?php echo $__env->make('livewire.inquiries.sections.comparison', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRfqSettings): ?>
                <?php if (isset($component)) { $__componentOriginal24602fd4eed936d9b89b6132afca889a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24602fd4eed936d9b89b6132afca889a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inquiries-rfq-settings','data' => ['rfqReminderEnabled' => $rfqReminderEnabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inquiries-rfq-settings'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rfq-reminder-enabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rfqReminderEnabled)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24602fd4eed936d9b89b6132afca889a)): ?>
<?php $attributes = $__attributesOriginal24602fd4eed936d9b89b6132afca889a; ?>
<?php unset($__attributesOriginal24602fd4eed936d9b89b6132afca889a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24602fd4eed936d9b89b6132afca889a)): ?>
<?php $component = $__componentOriginal24602fd4eed936d9b89b6132afca889a; ?>
<?php unset($__componentOriginal24602fd4eed936d9b89b6132afca889a); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTaskDocumentModal && $taskDocumentModalTask): ?>
                <?php
                    $completeAfterTaskDocument = (bool) $taskDocumentModalTask->requires_submission
                        && ! $taskDocumentModalTask->completed_at;
                    $taskDocumentUploadName = $taskDocumentUpload?->getClientOriginalName();
                    $taskDocumentUploadType = $taskDocumentUploadName
                        ? (strtoupper((string) pathinfo($taskDocumentUploadName, PATHINFO_EXTENSION)) ?: 'FILE')
                        : null;
                    $taskDocumentUploadSize = $taskDocumentUpload
                        ? ($taskDocumentUpload->getSize() >= 1048576
                            ? number_format($taskDocumentUpload->getSize() / 1048576, 1).' MB'
                            : number_format(max(1, (int) ceil($taskDocumentUpload->getSize() / 1024))).' KB')
                        : null;
                ?>
                <div class="ft-inquiry-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-document-modal'; ?>wire:key="inquiry-task-document-modal" wire:click.self="closeTaskDocumentModal">
                    <section class="ft-inquiry-task-document-modal" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="task-document-modal-title">
                        <header class="ft-inquiry-task-document-modal-head">
                            <div>
                                <h2 id="task-document-modal-title"><?php echo e($completeAfterTaskDocument ? 'Required file needed to complete task' : 'Add new document to task'); ?></h2>
                                <p><?php echo e($completeAfterTaskDocument ? 'Add the required file now. The task will be completed automatically after the document is saved.' : 'Upload a new file to this task.'); ?></p>
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
                                    <span class="ft-inquiry-task-document-reference"><b>Inquiry Reference:</b> <?php echo e($inquiry->reference_number ?: '—'); ?></span>
                                </div>
                                <span class="ft-inquiry-task-document-target-lock">▣&nbsp; Task selected</span>
                            </div>

                            <div class="ft-inquiry-task-document-source-label">Document source</div>
                            <div class="ft-inquiry-task-document-source-tabs is-single-source">
                                <button type="button" class="active" disabled aria-current="true">
                                    <span>↥</span> Upload new
                                </button>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateDocuments): ?>
                                <div
                                    class="ft-inquiry-task-document-upload-field"
                                    x-data="{ uploading: false, progress: 0 }"
                                    x-on:livewire-upload-start="uploading = true; progress = 0"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                                    x-on:livewire-upload-finish="progress = 100; window.setTimeout(() => { uploading = false; progress = 0 }, 250)"
                                    x-on:livewire-upload-error="uploading = false; progress = 0"
                                    x-on:livewire-upload-cancel="uploading = false; progress = 0"
                                >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocumentUpload): ?>
                                        <div class="ft-inquiry-attachment-selected-count">1 file selected</div>
                                        <div class="ft-inquiry-attachment-selected-file">
                                            <span class="ft-inquiry-attachment-selected-check" aria-hidden="true">✓</span>
                                            <span class="ft-inquiry-attachment-selected-copy">
                                                <strong title="<?php echo e($taskDocumentUploadName); ?>"><?php echo e($taskDocumentUploadName); ?></strong>
                                                <small><?php echo e($taskDocumentUploadType); ?> · <?php echo e($taskDocumentUploadSize); ?> · Ready to upload</small>
                                            </span>
                                            <button type="button" wire:click="$set('taskDocumentUpload', null)" wire:loading.attr="disabled" wire:target="taskDocumentUpload">Remove</button>
                                        </div>
                                    <?php else: ?>
                                        <div class="ft-inquiry-attachment-field-label">File attachment</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <label class="ft-inquiry-task-document-dropzone ft-inquiry-attachment-dropzone <?php echo e($taskDocumentUpload ? 'is-compact' : ''); ?>">
                                        <input type="file" wire:model="taskDocumentUpload" accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>" aria-label="<?php echo e($taskDocumentUpload ? 'Add another file' : 'Choose a file to upload'); ?>">
                                        <svg class="ft-inquiry-attachment-upload-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 16l-4-4-4 4M12 12v9M20.4 17.5A5 5 0 0 0 18 8.2 7 7 0 0 0 4.3 10.8 4.5 4.5 0 0 0 5.5 19H7"/></svg>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocumentUpload): ?>
                                            <strong>Add another file</strong>
                                            <b>Drag &amp; drop or <span>browse</span></b>
                                        <?php else: ?>
                                            <strong>Drag &amp; drop a file here</strong>
                                            <b>or choose from your computer</b>
                                            <span class="ft-inquiry-attachment-browse">Browse files</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <small><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></small>
                                    </label>

                                    <div class="ft-inquiry-task-document-upload-progress" x-cloak x-show="uploading" x-transition.opacity.duration.120ms>
                                        <div class="ft-inquiry-upload-progress-meta">
                                            <span>Uploading file...</span>
                                            <b x-text="`${progress}%`">0%</b>
                                        </div>
                                        <div class="ft-inquiry-upload-progress-track" role="progressbar" aria-label="File upload progress" aria-valuemin="0" aria-valuemax="100" x-bind:aria-valuenow="progress">
                                            <span x-bind:style="`width: ${progress}%`"></span>
                                        </div>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskDocumentUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-task-document-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
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
                                <p>
                                    This document will appear directly under <strong><?php echo e($taskDocumentModalTask->title); ?></strong>.
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($completeAfterTaskDocument): ?> Saving it will also mark the task as Completed. <?php elseif($taskDocumentModalTask->completed_at): ?> Adding a document will not reopen or change the completed task. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <footer class="ft-inquiry-task-document-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskDocumentModal">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskDocument" wire:loading.attr="disabled" wire:target="saveTaskDocument,taskDocumentUpload"
                                <?php if(!$taskDocumentUpload): echo 'disabled'; endif; ?>>
                                <span wire:loading.remove wire:target="saveTaskDocument"><?php echo e($taskDocumentUpload ? 'Add 1 document' : 'Add document'); ?></span>
                                <span wire:loading wire:target="saveTaskDocument"><?php echo e($completeAfterTaskDocument ? 'Adding & completing...' : 'Adding...'); ?></span>
                            </button>
                        </footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showInquiryAttentionModal): ?>
                <div class="ft-inquiry-attention-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-attention-modal'; ?>wire:key="inquiry-attention-modal" wire:click.self="closeInquiryAttentionReason">
                    <section class="ft-inquiry-attention-modal" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="inquiry-attention-modal-title">
                        <header class="ft-inquiry-attention-modal-head">
                            <div>
                                <h2 id="inquiry-attention-modal-title">Request attention</h2>
                                <p><?php echo e($inquiry->inquiry_number); ?> · Admin, Super Admin and the Inquiry creator will be notified.</p>
                            </div>
                            <button type="button" class="ft-inquiry-attention-modal-close" wire:click="closeInquiryAttentionReason" aria-label="Close">×</button>
                        </header>
                        <div class="ft-inquiry-attention-modal-body ft-mention-host">
                            <label for="inquiry-attention-reason">Reason for flag *</label>
                            <textarea id="inquiry-attention-reason" class="ft-mention-input" wire:model="inquiryAttentionReason" rows="5" maxlength="2000" autocomplete="off" data-mention-users="<?php echo e(json_encode($inquiryMentionUsers->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>" placeholder="Explain what needs attention. Type @ to mention a user..."></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['inquiryAttentionReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-attention-modal-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <p class="ft-inquiry-attention-modal-help">The reason is added to Inquiry comments. Use <b>@</b> to mention specific users in addition to the automatic Admin/Super Admin/creator notification.</p>
                        </div>
                        <footer class="ft-inquiry-attention-modal-actions">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiry->needs_attention): ?><button type="button" class="ft-inquiry-attention-clear" wire:click="clearInquiryAttention" wire:loading.attr="disabled" wire:target="clearInquiryAttention">Clear flag</button><?php else: ?><span></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div>
                                <button type="button" class="secondary" wire:click="closeInquiryAttentionReason">Cancel</button>
                                <button type="button" class="primary" wire:click="saveInquiryAttentionReason" wire:loading.attr="disabled" wire:target="saveInquiryAttentionReason">
                                    <span wire:loading.remove wire:target="saveInquiryAttentionReason">Request attention</span>
                                    <span wire:loading wire:target="saveInquiryAttentionReason">Saving...</span>
                                </button>
                            </div>
                        </footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTaskAttentionModal && $taskAttentionTaskId): ?>
                <?php
                    $attentionTask = $inquiry->tasks->firstWhere('id', (int) $taskAttentionTaskId);
                ?>
                <div class="ft-inquiry-attention-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-attention-modal'; ?>wire:key="inquiry-task-attention-modal" wire:click.self="closeTaskAttentionReason">
                    <section class="ft-inquiry-attention-modal" data-ft-feedback-scope="form" role="dialog" aria-modal="true" aria-labelledby="task-attention-modal-title">
                        <header class="ft-inquiry-attention-modal-head">
                            <div>
                                <h2 id="task-attention-modal-title">Why is attention required?</h2>
                                <p><?php echo e($attentionTask?->title ?: 'Inquiry task'); ?> · <?php echo e($attentionTask?->status ?: 'Attention required'); ?></p>
                            </div>
                            <button type="button" class="ft-inquiry-attention-modal-close" wire:click="closeTaskAttentionReason" aria-label="Close">×</button>
                        </header>
                        <div class="ft-inquiry-attention-modal-body ft-mention-host">
                            <label for="inquiry-task-attention-reason">Reason for flag *</label>
                            <textarea id="inquiry-task-attention-reason" class="ft-mention-input" wire:model="taskAttentionReason" rows="5" maxlength="2000" autocomplete="off" data-mention-users="<?php echo e(json_encode($inquiryMentionUsers->values()->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>" placeholder="Explain what is blocking the task or what needs attention. Type @ to mention a user..."></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['taskAttentionReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ft-inquiry-attention-modal-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <p class="ft-inquiry-attention-modal-help">Saving this reason adds it to Inquiry comments, notifies Admin/Super Admin/the Inquiry creator, and supports <b>@mentions</b>.</p>
                        </div>
                        <footer class="ft-inquiry-attention-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskAttentionReason">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskAttentionReason" wire:loading.attr="disabled" wire:target="saveTaskAttentionReason">
                                <span wire:loading.remove wire:target="saveTaskAttentionReason">Save reason</span>
                                <span wire:loading wire:target="saveTaskAttentionReason">Saving...</span>
                            </button>
                        </footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/sections/detail.blade.php ENDPATH**/ ?>