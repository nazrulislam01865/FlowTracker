<div class="ft-order-workflow-page" data-ft-feedback-scope="form">
    <header class="ft-order-workflow-page-head">
        <div>
            <div class="ft-order-workflow-breadcrumb">Administration / Order Workflow Setup</div>
            <h1>Order Workflow Setup</h1>
            <p>Manage the seven order stages and the tasks inside each stage from one simple screen. Task dependencies are handled automatically by the backend.</p>
        </div>
        <div class="ft-order-workflow-top-actions">
            <button
                type="button"
                class="ft-order-workflow-btn"
                wire:click="resetWorkflow"
                wire:confirm="Reset the Order workflow to the prototype default stages and tasks? The reset is not published until you save the workflow."
            >Reset demo</button>
            <button type="button" class="ft-order-workflow-btn ft-order-workflow-btn--primary" wire:click="saveWorkflow" wire:loading.attr="disabled" wire:target="saveWorkflow">
                <span wire:loading.remove wire:target="saveWorkflow">Save workflow</span>
                <span wire:loading wire:target="saveWorkflow">Saving…</span>
            </button>
        </div>
    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['stages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-order-workflow-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-order-workflow-stats">
        <?php if (isset($component)) { $__componentOriginal49d83fd54b581fe95669c8a1230e8061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49d83fd54b581fe95669c8a1230e8061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.stat-card','data' => ['label' => 'Workflow','value' => $workflowName]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Workflow','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowName)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $attributes = $__attributesOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $component = $__componentOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__componentOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal49d83fd54b581fe95669c8a1230e8061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49d83fd54b581fe95669c8a1230e8061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.stat-card','data' => ['label' => 'Stages','value' => '7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Stages','value' => '7']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $attributes = $__attributesOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $component = $__componentOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__componentOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal49d83fd54b581fe95669c8a1230e8061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49d83fd54b581fe95669c8a1230e8061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.stat-card','data' => ['label' => 'Total tasks','value' => collect($stages)->sum(fn ($stage) => count($stage['tasks'] ?? []))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total tasks','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($stages)->sum(fn ($stage) => count($stage['tasks'] ?? [])))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $attributes = $__attributesOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $component = $__componentOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__componentOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal49d83fd54b581fe95669c8a1230e8061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49d83fd54b581fe95669c8a1230e8061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.stat-card','data' => ['label' => 'Dependency mode','value' => 'Automatic']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Dependency mode','value' => 'Automatic']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $attributes = $__attributesOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__attributesOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49d83fd54b581fe95669c8a1230e8061)): ?>
<?php $component = $__componentOriginal49d83fd54b581fe95669c8a1230e8061; ?>
<?php unset($__componentOriginal49d83fd54b581fe95669c8a1230e8061); ?>
<?php endif; ?>
    </section>

    <?php if (isset($component)) { $__componentOriginalc773fc849f9d33f0860fb891527f95fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc773fc849f9d33f0860fb891527f95fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.notice','data' => ['icon' => '⚙','title' => 'No dependency setup required.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '⚙','title' => 'No dependency setup required.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        The backend opens the first task of a stage, then unlocks the next applicable task when the previous required task is completed. Special branches such as artwork revision, sample approval, production/QC issues and payment are handled by workflow rules in code/service configuration.
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc773fc849f9d33f0860fb891527f95fc)): ?>
<?php $attributes = $__attributesOriginalc773fc849f9d33f0860fb891527f95fc; ?>
<?php unset($__attributesOriginalc773fc849f9d33f0860fb891527f95fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc773fc849f9d33f0860fb891527f95fc)): ?>
<?php $component = $__componentOriginalc773fc849f9d33f0860fb891527f95fc; ?>
<?php unset($__componentOriginalc773fc849f9d33f0860fb891527f95fc); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc773fc849f9d33f0860fb891527f95fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc773fc849f9d33f0860fb891527f95fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.notice','data' => ['icon' => '📎','title' => 'Documents are configured per task.','tone' => 'document']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.notice'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '📎','title' => 'Documents are configured per task.','tone' => 'document']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Turn on Document upload only for tasks that need a file. Choose the document type and whether the task must be blocked until the document is uploaded.
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc773fc849f9d33f0860fb891527f95fc)): ?>
<?php $attributes = $__attributesOriginalc773fc849f9d33f0860fb891527f95fc; ?>
<?php unset($__attributesOriginalc773fc849f9d33f0860fb891527f95fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc773fc849f9d33f0860fb891527f95fc)): ?>
<?php $component = $__componentOriginalc773fc849f9d33f0860fb891527f95fc; ?>
<?php unset($__componentOriginalc773fc849f9d33f0860fb891527f95fc); ?>
<?php endif; ?>

    <section class="ft-order-workflow-card">
        <div class="ft-order-workflow-card-head">
            <div>
                <h2>7-stage order workflow</h2>
                <p>Stage order stays fixed. You can change colors, task names, default team, due offset and whether a task is required.</p>
            </div>
            <span class="ft-order-workflow-state">Active</span>
        </div>

        <div class="ft-order-workflow-stage-strip">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald17418d7ddaafafb66207eeb9bfd4d7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald17418d7ddaafafb66207eeb9bfd4d7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.stage-tile','data' => ['stage' => $stage,'index' => $index]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.stage-tile'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stage),'index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald17418d7ddaafafb66207eeb9bfd4d7c)): ?>
<?php $attributes = $__attributesOriginald17418d7ddaafafb66207eeb9bfd4d7c; ?>
<?php unset($__attributesOriginald17418d7ddaafafb66207eeb9bfd4d7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald17418d7ddaafafb66207eeb9bfd4d7c)): ?>
<?php $component = $__componentOriginald17418d7ddaafafb66207eeb9bfd4d7c; ?>
<?php unset($__componentOriginald17418d7ddaafafb66207eeb9bfd4d7c); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="ft-order-workflow-stage-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal51ea3b7c02a38968640ad54ae67e8354 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ea3b7c02a38968640ad54ae67e8354 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.stage-row','data' => ['stage' => $stage,'index' => $index]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.stage-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stage),'index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ea3b7c02a38968640ad54ae67e8354)): ?>
<?php $attributes = $__attributesOriginal51ea3b7c02a38968640ad54ae67e8354; ?>
<?php unset($__attributesOriginal51ea3b7c02a38968640ad54ae67e8354); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ea3b7c02a38968640ad54ae67e8354)): ?>
<?php $component = $__componentOriginal51ea3b7c02a38968640ad54ae67e8354; ?>
<?php unset($__componentOriginal51ea3b7c02a38968640ad54ae67e8354); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="ft-order-workflow-save-bar">
            <span>
                <b><?php echo e($dirty ? 'Unsaved changes' : 'No unsaved changes'); ?></b>
                <small> Active Orders use this saved workflow; completed/cancelled Orders keep their historical workflow.</small>
            </span>
            <button type="button" class="ft-order-workflow-btn ft-order-workflow-btn--primary" wire:click="saveWorkflow" wire:loading.attr="disabled" wire:target="saveWorkflow">
                <span wire:loading.remove wire:target="saveWorkflow">Save workflow</span>
                <span wire:loading wire:target="saveWorkflow">Saving…</span>
            </button>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showStageModal && $editingStageIndex !== null && isset($stages[$editingStageIndex])): ?>
        <?php if (isset($component)) { $__componentOriginal7762953202be6518eecd1cfbd075bf2f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7762953202be6518eecd1cfbd075bf2f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'order-workflow-stage-editor','title' => 'Edit '.$stages[$editingStageIndex]['name'],'size' => 'lg','open' => true,'class' => 'ft-order-workflow-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'order-workflow-stage-editor','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Edit '.$stages[$editingStageIndex]['name']),'size' => 'lg','open' => true,'class' => 'ft-order-workflow-modal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('close', null, []); ?> 
                <button type="button" class="ft-order-workflow-modal-close" wire:click="closeStageEditor" aria-label="Close">×</button>
             <?php $__env->endSlot(); ?>

            <div class="ft-order-workflow-modal-grid">
                <div class="ft-order-workflow-editor-field">
                    <label>Stage name</label>
                    <input type="text" value="<?php echo e($stages[$editingStageIndex]['name']); ?>" disabled>
                    <small>The seven stage names and order remain fixed.</small>
                </div>
                <div class="ft-order-workflow-editor-field">
                    <label>Stage color</label>
                    <?php if (isset($component)) { $__componentOriginal3606f3fe52333140874051de244bafee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3606f3fe52333140874051de244bafee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.setup.color-picker','data' => ['model' => 'editingStageColor','label' => 'Choose stage color','inputClass' => '','containerClass' => 'ft-order-workflow-color-control','showText' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('setup.color-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'editingStageColor','label' => 'Choose stage color','input-class' => '','container-class' => 'ft-order-workflow-color-control','show-text' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3606f3fe52333140874051de244bafee)): ?>
<?php $attributes = $__attributesOriginal3606f3fe52333140874051de244bafee; ?>
<?php unset($__attributesOriginal3606f3fe52333140874051de244bafee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3606f3fe52333140874051de244bafee)): ?>
<?php $component = $__componentOriginal3606f3fe52333140874051de244bafee; ?>
<?php unset($__componentOriginal3606f3fe52333140874051de244bafee); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editingStageColor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="validation-error"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="ft-order-workflow-task-editor-head">
                <div>
                    <b>Tasks in this stage</b>
                    <p>Put tasks in the normal working order. Turn on Document upload only where a file is needed.</p>
                </div>
                <button type="button" class="ft-order-workflow-btn ft-order-workflow-btn--small" wire:click="addTask">＋ Add task</button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editingTasks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-order-workflow-error ft-order-workflow-error--compact"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-order-workflow-task-edit-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $editingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taskIndex => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal499ea745113ca839c437ea597d049bab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal499ea745113ca839c437ea597d049bab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.order-workflow.task-edit-row','data' => ['task' => $task,'index' => $taskIndex,'count' => count($editingTasks),'departmentOptions' => $departmentOptions,'documentCategoryOptions' => $documentCategoryOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order-workflow.task-edit-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['task' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task),'index' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskIndex),'count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($editingTasks)),'department-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($departmentOptions),'document-category-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($documentCategoryOptions)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal499ea745113ca839c437ea597d049bab)): ?>
<?php $attributes = $__attributesOriginal499ea745113ca839c437ea597d049bab; ?>
<?php unset($__attributesOriginal499ea745113ca839c437ea597d049bab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal499ea745113ca839c437ea597d049bab)): ?>
<?php $component = $__componentOriginal499ea745113ca839c437ea597d049bab; ?>
<?php unset($__componentOriginal499ea745113ca839c437ea597d049bab); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="ft-order-workflow-doc-help">
                <b>📎 Document behavior</b>
                <p>If “Must upload before completion” is enabled, FlowTrack blocks task completion until at least one valid document or task link in the selected category is attached. Files remain available in Order document history.</p>
            </div>

            <div class="ft-order-workflow-auto-dependency">
                <b>Automatic dependency rule</b>
                <p>When this stage opens, the first applicable required task becomes Ready. When it completes, the backend unlocks the next applicable required task. Users do not configure dependency links here.</p>
            </div>

             <?php $__env->slot('footer', null, []); ?> 
                <button type="button" class="ft-order-workflow-btn" wire:click="closeStageEditor">Cancel</button>
                <button type="button" class="ft-order-workflow-btn ft-order-workflow-btn--primary" wire:click="saveStageEditor">Save stage</button>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7762953202be6518eecd1cfbd075bf2f)): ?>
<?php $attributes = $__attributesOriginal7762953202be6518eecd1cfbd075bf2f; ?>
<?php unset($__attributesOriginal7762953202be6518eecd1cfbd075bf2f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7762953202be6518eecd1cfbd075bf2f)): ?>
<?php $component = $__componentOriginal7762953202be6518eecd1cfbd075bf2f; ?>
<?php unset($__componentOriginal7762953202be6518eecd1cfbd075bf2f); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/order-workflow-setup/index.blade.php ENDPATH**/ ?>