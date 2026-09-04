        <?php
            $selectedWorkflow = collect($workflowFilterOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $createWorkflowId);
            $selectedWorkflowName = (string) ($selectedWorkflow['label'] ?? $selectedWorkflowLabel ?: 'Select workflow');
        ?>
        <section class="view ft-inquiry-create-v3 ft-form-standard ft-form-standard--inquiry" data-ft-feedback-scope="form" x-on:keydown.meta.enter.window="$wire.createInquiry()" x-on:keydown.ctrl.enter.window="$wire.createInquiry()">
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
                                <label for="inquiry-received-date">Received *</label>
                                <div class="ft-inquiry-received-control">
                                    <input id="inquiry-received-date" type="date" wire:model="createReceivedDate" aria-describedby="inquiry-received-help">
                                </div>
                                <small id="inquiry-received-help" class="ft-inquiry-field-help">Defaults to today. Change it when the inquiry was received on another date.</small>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createReceivedDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid ft-inquiry-create-grid-client">
                            <div class="ft-inquiry-create-field">
                                <label>Client *</label>
                                <div class="ft-inquiry-client-control-row">
                                    <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-client-selector','label' => 'Client','property' => 'clientId','type' => 'clients','context' => 'create-inquiry','action' => 'setCreateSelector','value' => $clientId,'placeholder' => 'Search or select client...','selectedLabel' => $selectedClientLabel ?: null,'initialOptions' => $clientFilterOptions,'clearable' => false,'wire:key' => 'inquiry-create-client-selector-'.e($clientId ?: 'none').'-'.e(substr(md5($selectedClientLabel ?: 'none'), 0, 8)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-client-selector','label' => 'Client','property' => 'clientId','type' => 'clients','context' => 'create-inquiry','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientId),'placeholder' => 'Search or select client...','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedClientLabel ?: null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'inquiry-create-client-selector-'.e($clientId ?: 'none').'-'.e(substr(md5($selectedClientLabel ?: 'none'), 0, 8)).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
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
                                <label>Client contact *</label>
                                <div class="ft-inquiry-client-control-row">
                                    <div class="ft-inquiry-contact-select-wrap">
                                        <select wire:model="clientContact" <?php if(!$clientId || empty($clientContactOptions)): echo 'disabled'; endif; ?> aria-required="true">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$clientId): ?>
                                                <option value="">Select a client first</option>
                                            <?php elseif(empty($clientContactOptions)): ?>
                                                <option value="">No contact recorded</option>
                                            <?php else: ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clientContactOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contactOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <option value="<?php echo e($contactOption['value']); ?>"><?php echo e($contactOption['label']); ?><?php echo e($contactOption['primary'] ? ' · Primary' : ''); ?><?php echo e($contactOption['meta'] ? ' · '.$contactOption['meta'] : ''); ?></option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientId && empty($clientContactOptions)): ?>
                                    <small class="ft-inquiry-field-help">This client has no contact. Add a contact from Clients before creating the Inquiry.</small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="ft-inquiry-create-grid">
                            <label class="ft-inquiry-create-field">
                                <span>Reference number</span>
                                <input wire:model="referenceNumber" placeholder="Enter the client-provided ES or NEQ number">
                            </label>

                            <div class="ft-inquiry-create-field">
                                <label>Assigned to *</label>
                                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-owner-selector','label' => 'Assigned to','property' => 'createOwnerId','type' => 'users','context' => 'create-inquiry','action' => 'setCreateSelector','value' => $createOwnerId,'placeholder' => 'Search or select assignee...','selectedLabel' => $selectedOwnerLabel ?: null,'initialOptions' => $ownerFilterOptions,'clearable' => false,'wire:key' => 'inquiry-create-owner-selector-'.e($createOwnerId ?: 'none').'-'.e(substr(md5($selectedOwnerLabel ?: 'none'), 0, 8)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-owner-selector','label' => 'Assigned to','property' => 'createOwnerId','type' => 'users','context' => 'create-inquiry','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createOwnerId),'placeholder' => 'Search or select assignee...','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOwnerLabel ?: null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'inquiry-create-owner-selector-'.e($createOwnerId ?: 'none').'-'.e(substr(md5($selectedOwnerLabel ?: 'none'), 0, 8)).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createOwnerId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <?php
                            $createPriorityColor = optional($createPriorityOptions->first(
                                fn ($priority) => (string) $priority->name === (string) $createPriority
                            ))->color;
                        ?>
                        <div class="ft-inquiry-create-field ft-inquiry-create-field-full">
                            <label>Shipment Priority *</label>
                            <select
                                data-master-color-select
                                wire:model="createPriority"
                                class="<?php echo e($createPriorityColor ? 'ft-master-color' : ''); ?>"
                                style="<?php echo e(\App\Support\MasterColor::style($createPriorityColor)); ?>"
                                aria-label="Inquiry priority"
                            >
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $createPriorityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($priority->name); ?>" data-color="<?php echo e($priority->color); ?>"><?php echo e($priority->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <option value="">No active priorities</option>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createPriority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUseInquiryProductSelector): ?>
                        <?php echo $__env->make('components.inquiries.create-products', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                    <section class="section ft-inquiry-create-section ft-inquiry-attachments-section">
                        <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                            <span><?php echo e($canUseInquiryProductSelector ? 3 : 2); ?></span><h2>Attachments</h2><p>Add emails, specifications, artwork or reference images.</p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal6341f0ed212d869a5635165bbc977ad7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6341f0ed212d869a5635165bbc977ad7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.create-attachment-dropzone','data' => ['inputId' => 'inquiry-create-attachments','model' => 'createAttachments','multiple' => true,'headline' => 'Drop client files here','browseText' => 'browse files','helper' => \App\Support\AttachmentUpload::helperText(20),'progressLabel' => 'Uploading files...','progressAriaLabel' => 'Inquiry attachment upload progress']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.create-attachment-dropzone'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['input-id' => 'inquiry-create-attachments','model' => 'createAttachments','multiple' => true,'headline' => 'Drop client files here','browse-text' => 'browse files','helper' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\AttachmentUpload::helperText(20)),'progress-label' => 'Uploading files...','progress-aria-label' => 'Inquiry attachment upload progress']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6341f0ed212d869a5635165bbc977ad7)): ?>
<?php $attributes = $__attributesOriginal6341f0ed212d869a5635165bbc977ad7; ?>
<?php unset($__attributesOriginal6341f0ed212d869a5635165bbc977ad7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6341f0ed212d869a5635165bbc977ad7)): ?>
<?php $component = $__componentOriginal6341f0ed212d869a5635165bbc977ad7; ?>
<?php unset($__componentOriginal6341f0ed212d869a5635165bbc977ad7); ?>
<?php endif; ?>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($createWorkflowReady): ?>
                        <?php if (isset($component)) { $__componentOriginaldc75731e81ba1cac015b7a03337954d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc75731e81ba1cac015b7a03337954d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.create-workflow-picker','data' => ['class' => 'section ft-inquiry-create-section ft-inquiry-next-section','step' => $canUseInquiryProductSelector ? 4 : 3,'title' => 'What happens next','workflowOptions' => $workflowFilterOptions,'selectedWorkflowId' => $createWorkflowId,'selectedWorkflowName' => $selectedWorkflowName,'phaseCount' => $createWorkflowPhaseCount,'taskCount' => $createWorkflowTaskCount,'selectionProperty' => 'createWorkflowId','optionFallback' => 'Inquiry workflow','footnote' => 'Tasks are created when you select Create inquiry.','previewAllowed' => auth()->user()->canAccess('workflow.view'),'emptyMessage' => $createWorkflowId && $createWorkflowTaskCount === 0 ? 'This Workflow has no active Task Pack tasks.' : null,'errorField' => 'createWorkflowId','wire:key' => 'create-inquiry-workflow-picker']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.create-workflow-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'section ft-inquiry-create-section ft-inquiry-next-section','step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canUseInquiryProductSelector ? 4 : 3),'title' => 'What happens next','workflow-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowFilterOptions),'selected-workflow-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createWorkflowId),'selected-workflow-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedWorkflowName),'phase-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createWorkflowPhaseCount),'task-count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createWorkflowTaskCount),'selection-property' => 'createWorkflowId','option-fallback' => 'Inquiry workflow','footnote' => 'Tasks are created when you select Create inquiry.','preview-allowed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->canAccess('workflow.view')),'empty-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createWorkflowId && $createWorkflowTaskCount === 0 ? 'This Workflow has no active Task Pack tasks.' : null),'error-field' => 'createWorkflowId','wire:key' => 'create-inquiry-workflow-picker']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc75731e81ba1cac015b7a03337954d0)): ?>
<?php $attributes = $__attributesOriginaldc75731e81ba1cac015b7a03337954d0; ?>
<?php unset($__attributesOriginaldc75731e81ba1cac015b7a03337954d0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc75731e81ba1cac015b7a03337954d0)): ?>
<?php $component = $__componentOriginaldc75731e81ba1cac015b7a03337954d0; ?>
<?php unset($__componentOriginaldc75731e81ba1cac015b7a03337954d0); ?>
<?php endif; ?>
                    <?php else: ?>
                        <section class="section ft-inquiry-create-section ft-inquiry-next-section" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'create-inquiry-workflow-placeholder'; ?>wire:key="create-inquiry-workflow-placeholder">
                            <div class="sectiontitle ft-inquiry-step-title ft-inquiry-step-title-inline">
                                <span><?php echo e($canUseInquiryProductSelector ? 4 : 3); ?></span><h2>What happens next</h2><p>Workflow options load only when this section is needed.</p>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'workflow','rows' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'workflow','rows' => 3]); ?>
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
                        </section>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/sections/create.blade.php ENDPATH**/ ?>