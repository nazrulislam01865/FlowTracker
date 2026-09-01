<div class="ft-admin-reference ft-workflow-form-page">
    <div class="ft-admin-form-top ft-workflow-create-top">
        <div>
            <div class="ft-admin-breadcrumb"><?php echo e($workflowId ? 'Edit Workflow' : 'New Workflow'); ?></div>
            <h1><?php echo e($workflowId ? 'Edit Workflow' : 'Create New Workflow'); ?></h1>
            <p>Configure Inquiry and Order workflows here. Task Packs are managed separately in Task Pack Setup.</p>
        </div>
        <a href="<?php echo e(route('workflow.setup')); ?>" wire:navigate class="ft-admin-back">← Back to Workflow Setup</a>
    </div>

    <form wire:submit="save" class="ft-admin-form-card ft-workflow-create-card" data-ft-feedback-scope="form">
        <section class="ft-workflow-form-section ft-workflow-details-section">
            <div class="ft-workflow-section-heading">
                <h2>1. Workflow details</h2>
                <p>Name and describe this workflow.</p>
            </div>

            <div class="ft-workflow-details-grid">
                <div class="ft-admin-field">
                    <label for="workflow-name">Workflow name *</label>
                    <input id="workflow-name" type="text" wire:model="workflowName" placeholder="e.g. Fast Track Order" autocomplete="off">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['workflowName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-admin-field">
                    <label for="workflow-code">Workflow code *</label>
                    <input id="workflow-code" type="text" wire:model="workflowCode" placeholder="e.g. FAST_TRACK" autocomplete="off">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['workflowCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="ft-admin-field ft-workflow-description-field">
                    <label for="workflow-description">Description</label>
                    <textarea id="workflow-description" wire:model="workflowDescription" rows="3" placeholder="Describe when this workflow should be used..."></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['workflowDescription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ft-workflow-form-section ft-workflow-scope-section">
            <div class="ft-workflow-section-heading">
                <h2>2. Workflow scope</h2>
                <p>Choose where this workflow is available.</p>
            </div>

            <fieldset class="ft-workflow-choice-group">
                <legend>Workflow applies to *</legend>
                <div class="ft-workflow-choice-grid">
                    <label class="ft-workflow-choice-card <?php echo e($workflowAppliesTo === 'inquiries' ? 'is-selected' : ''); ?>">
                        <input type="radio" value="inquiries" wire:model.live="workflowAppliesTo" <?php if($workflowId): echo 'disabled'; endif; ?>>
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M9.8 9.2a2.45 2.45 0 0 1 4.75.8c0 1.85-2.55 2.08-2.55 3.65"></path>
                                <path d="M12 17.2h.01"></path>
                            </svg>
                        </span>
                        <span class="ft-workflow-choice-copy">
                            <strong>Inquiries</strong>
                            <small>Use this workflow when managing new client inquiries.</small>
                        </span>
                    </label>

                    <label class="ft-workflow-choice-card <?php echo e($workflowAppliesTo === 'orders' ? 'is-selected' : ''); ?>">
                        <input type="radio" value="orders" wire:model.live="workflowAppliesTo" <?php if($workflowId): echo 'disabled'; endif; ?>>
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 8.5h16v10.5H4z"></path>
                                <path d="M8.2 8.5V6.8A2.8 2.8 0 0 1 11 4h2a2.8 2.8 0 0 1 2.8 2.8v1.7"></path>
                                <path d="M4 12.3h16"></path>
                            </svg>
                        </span>
                        <span class="ft-workflow-choice-copy">
                            <strong>Orders</strong>
                            <small>Use this workflow after an inquiry becomes an order.</small>
                        </span>
                    </label>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowId): ?><small>Workflow scope is locked after creation so existing Inquiry/Order records keep the correct runtime behavior.</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['workflowAppliesTo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </fieldset>

            <fieldset class="ft-workflow-choice-group ft-workflow-client-availability">
                <legend>Client availability *</legend>
                <div class="ft-workflow-choice-grid">
                    <label class="ft-workflow-choice-card ft-workflow-client-choice <?php echo e($clientAvailability === 'all' ? 'is-selected' : ''); ?>">
                        <input type="radio" value="all" wire:model.live="clientAvailability">
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-copy">
                            <strong>All clients</strong>
                            <small>Available to every current and future client.</small>
                        </span>
                    </label>

                    <label class="ft-workflow-choice-card ft-workflow-client-choice <?php echo e($clientAvailability === 'specific' ? 'is-selected' : ''); ?>">
                        <input type="radio" value="specific" wire:model.live="clientAvailability">
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-copy">
                            <strong>Specific clients</strong>
                            <small>Available only to clients you select.</small>
                        </span>
                    </label>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientAvailability'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </fieldset>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientAvailability === 'specific'): ?>
                <div class="ft-admin-field ft-workflow-client-field">
                    <?php if (isset($component)) { $__componentOriginalb73b05fb764f63a65626f13f8ab62da9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb73b05fb764f63a65626f13f8ab62da9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.multi-select','data' => ['label' => 'Select clients','property' => 'selectedClientIds','type' => 'clients','context' => 'workflow-setup','values' => $selectedClientIds,'initialOptions' => $clientOptions,'placeholder' => 'Search and select clients','fixedMenu' => true,'menuWidth' => 380,'maxSelected' => 100,'class' => 'ft-workflow-client-multi-select']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.multi-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Select clients','property' => 'selectedClientIds','type' => 'clients','context' => 'workflow-setup','values' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedClientIds),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientOptions),'placeholder' => 'Search and select clients','fixed-menu' => true,'menu-width' => 380,'max-selected' => 100,'class' => 'ft-workflow-client-multi-select']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb73b05fb764f63a65626f13f8ab62da9)): ?>
<?php $attributes = $__attributesOriginalb73b05fb764f63a65626f13f8ab62da9; ?>
<?php unset($__attributesOriginalb73b05fb764f63a65626f13f8ab62da9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb73b05fb764f63a65626f13f8ab62da9)): ?>
<?php $component = $__componentOriginalb73b05fb764f63a65626f13f8ab62da9; ?>
<?php unset($__componentOriginalb73b05fb764f63a65626f13f8ab62da9); ?>
<?php endif; ?>
                    <small><?php echo e(count($selectedClientIds)); ?> <?php echo e(\Illuminate\Support\Str::plural('client', count($selectedClientIds))); ?> selected. Search results are loaded in bounded pages.</small>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['selectedClientIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php if (isset($component)) { $__componentOriginalce11a07acd8b47e338d25689bef957cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce11a07acd8b47e338d25689bef957cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.validation-message','data' => ['message' => $message]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.validation-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $attributes = $__attributesOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__attributesOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $component = $__componentOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__componentOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['selectedClientIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php if (isset($component)) { $__componentOriginalce11a07acd8b47e338d25689bef957cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce11a07acd8b47e338d25689bef957cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.validation-message','data' => ['message' => $message]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.validation-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $attributes = $__attributesOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__attributesOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $component = $__componentOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__componentOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($workflowId)): ?>
            <section class="ft-workflow-form-section ft-workflow-start-section">
                <div class="ft-workflow-section-heading">
                    <h2>3. Start from</h2>
                    <p>Existing workflow templates are fetched only when this section is needed.</p>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sourceOptionsReady): ?>
                    <div class="ft-admin-field" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'workflow-source-options-ready'; ?>wire:key="workflow-source-options-ready">
                        <label for="workflow-source">Start from</label>
                        <select id="workflow-source" wire:model="sourceWorkflowId">
                            <option value="">Blank workflow</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workflow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($workflow->id); ?>"><?php echo e($workflow->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowAppliesTo === 'orders'): ?>
                                A blank Order workflow automatically receives the fixed seven Order stages and separate stage Task Packs. Duplicating an Order workflow clones its Task Packs so each workflow remains independently editable.
                            <?php else: ?>
                                Duplicating copies the Inquiry phase sequence and configuration, but not Inquiry history.
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </small>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sourceWorkflowId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal07ce51f35701acdfae5fc6353e53cc20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07ce51f35701acdfae5fc6353e53cc20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.progressive-section-loader','data' => ['section' => 'source-workflows','rows' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.progressive-section-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => 'source-workflows','rows' => 3]); ?>
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
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workflowAppliesTo === 'orders'): ?>
            <div class="ft-workflow-scope-summary" style="margin-bottom:10px">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10" cy="10" r="7"></circle><path d="M10 9v4"></path><path d="M10 6.7h.01"></path></svg>
                <span><b>Order runtime is protected:</b> New Order, Artwork, Production, QC, Shipment, Billing and Payment are created automatically. Configure the tasks inside their mapped Task Packs from Task Pack Setup.</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-workflow-scope-summary">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="10" cy="10" r="7"></circle><path d="M10 9v4"></path><path d="M10 6.7h.01"></path>
            </svg>
            <span>
                This workflow will be available for <?php echo e($workflowAppliesTo === 'inquiries' ? 'Inquiries' : 'Orders'); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientAvailability === 'specific'): ?>
                    from <?php echo e(count($selectedClientIds)); ?> selected <?php echo e(\Illuminate\Support\Str::plural('client', count($selectedClientIds))); ?>.
                <?php else: ?>
                    for all clients.
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
        </div>

        <div class="ft-admin-form-footer ft-workflow-create-footer">
            <button type="button" class="ft-admin-cancel" wire:click="cancel">Cancel</button>
            <button type="submit" class="ft-admin-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save"><?php echo e($workflowId ? 'Save Workflow' : 'Create Workflow'); ?></span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/workflow-setup/form.blade.php ENDPATH**/ ?>