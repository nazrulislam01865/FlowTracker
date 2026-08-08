<div wire:init="loadBoardCards" class="<?php echo e($mode === 'jobs' ? 'ft-board-page ft-operations-board' : ''); ?>" x-data="{ draggedTask:null, draggedJob:null, allGroupsOpen:true, phaseClosed:{} }">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
    <div class="ft-board-sticky-header">
        <div class="ft-board-page-head">
            <div>
                <h1>Operations Board</h1>
                <p><?php echo e($mode === 'tasks'
                    ? ($taskPackAdministratorView ? 'All active Job Task Pack lists' : 'Full Task Pack lists for Jobs associated with your assigned tasks')
                    : 'Track work across all active Jobs'); ?></p>
            </div>
            <div class="ft-board-head-actions"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','create')): ?><a class="ft-new-job-btn ft-dashboard-action-match" href="<?php echo e(route('jobs.index', ['create'=>1])); ?>" wire:navigate><span class="ft-dashboard-action-match-icon">+</span>New Order</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?><div class="flash"><?php echo e($message); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-board-tabs ft-reference-tabs">
            <button type="button" class="<?php echo e($mode === 'jobs' ? 'active' : ''); ?>" wire:click="setMode('jobs')">Job Board</button>
            <button type="button" class="<?php echo e($mode === 'tasks' ? 'active' : ''); ?>" wire:click="setMode('tasks')">Task Board</button>
        </div>

        <section class="ft-board-control-card ft-reference-filter-card ft-list-filter-shell">
            <div class="ft-list-filter-grid">
                <?php if (isset($component)) { $__componentOriginal5dde89dca5800d8916043e79c051883f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5dde89dca5800d8916043e79c051883f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.list-search','data' => ['property' => 'search','value' => $search,'placeholder' => 'Order ID, task, client or assignee…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.list-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['property' => 'search','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'placeholder' => 'Order ID, task, client or assignee…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5dde89dca5800d8916043e79c051883f)): ?>
<?php $attributes = $__attributesOriginal5dde89dca5800d8916043e79c051883f; ?>
<?php unset($__attributesOriginal5dde89dca5800d8916043e79c051883f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5dde89dca5800d8916043e79c051883f)): ?>
<?php $component = $__componentOriginal5dde89dca5800d8916043e79c051883f; ?>
<?php unset($__componentOriginal5dde89dca5800d8916043e79c051883f); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Order','property' => 'job','type' => 'jobs','context' => $mode === 'jobs' ? 'board' : 'board-task-pack','value' => $job,'placeholder' => 'All orders','initialOptions' => $jobFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Order','property' => 'job','type' => 'jobs','context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'jobs' ? 'board' : 'board-task-pack'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'placeholder' => 'All orders','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobFilterOptions)]); ?>
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
                <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Client','property' => 'client','type' => 'clients','context' => $mode === 'jobs' ? 'board' : 'board-task-pack','value' => $client,'placeholder' => 'All clients','initialOptions' => $clientFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Client','property' => 'client','type' => 'clients','context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'jobs' ? 'board' : 'board-task-pack'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions)]); ?>
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
                <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Assignee','property' => 'assignee','type' => 'users','context' => $mode === 'jobs' ? 'board' : 'board-task-pack','value' => $assignee,'placeholder' => 'Anyone','initialOptions' => $assigneeFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Assignee','property' => 'assignee','type' => 'users','context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'jobs' ? 'board' : 'board-task-pack'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assignee),'placeholder' => 'Anyone','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assigneeFilterOptions)]); ?>
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
                <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Status','property' => 'status','type' => $mode === 'jobs' ? 'job-statuses' : 'task-statuses','context' => $mode === 'jobs' ? 'board' : 'board-task-pack','value' => $status,'placeholder' => 'All statuses','initialOptions' => $statusFilterOptions,'wire:key' => 'board-status-filter-'.e($mode).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status','property' => 'status','type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'jobs' ? 'job-statuses' : 'task-statuses'),'context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode === 'jobs' ? 'board' : 'board-task-pack'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status),'placeholder' => 'All statuses','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusFilterOptions),'wire:key' => 'board-status-filter-'.e($mode).'']); ?>
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
                <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Due','property' => 'due','value' => $due,'placeholder' => 'Any date','options' => collect([['id'=>'overdue','label'=>'Overdue'],['id'=>'today','label'=>'Due today'],['id'=>'week','label'=>'Due this week'],['id'=>'month','label'=>'Next 30 days'],['id'=>'none','label'=>'No due date']])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due','property' => 'due','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($due),'placeholder' => 'Any date','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect([['id'=>'overdue','label'=>'Overdue'],['id'=>'today','label'=>'Due today'],['id'=>'week','label'=>'Due this week'],['id'=>'month','label'=>'Next 30 days'],['id'=>'none','label'=>'No due date']]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
                    <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Workflow','property' => 'workflow','type' => 'workflows','context' => 'board','value' => $workflow,'placeholder' => 'Select workflow','clearable' => false,'initialOptions' => $workflowFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Workflow','property' => 'workflow','type' => 'workflows','context' => 'board','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflow),'placeholder' => 'Select workflow','clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workflowFilterOptions)]); ?>
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
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Sort','property' => 'sort','value' => $sort,'placeholder' => 'Delivery date','clearable' => false,'options' => collect([['id'=>'delivery','label'=>'Delivery date'],['id'=>'updated','label'=>'Recently updated'],['id'=>'priority','label'=>'Priority']])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Sort','property' => 'sort','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sort),'placeholder' => 'Delivery date','clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect([['id'=>'delivery','label'=>'Delivery date'],['id'=>'updated','label'=>'Recently updated'],['id'=>'priority','label'=>'Priority']]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Sort','property' => 'taskSort','value' => $taskSort,'placeholder' => 'Action priority','clearable' => false,'options' => collect([['id'=>'action','label'=>'Action priority'],['id'=>'due','label'=>'Due soon'],['id'=>'job','label'=>'Order number'],['id'=>'updated','label'=>'Recently updated']])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Sort','property' => 'taskSort','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskSort),'placeholder' => 'Action priority','clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect([['id'=>'action','label'=>'Action priority'],['id'=>'due','label'=>'Due soon'],['id'=>'job','label'=>'Order number'],['id'=>'updated','label'=>'Recently updated']]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php
                $chips = collect();
                if($search) $chips->push(['key'=>'search','label'=>'Search: '.$search]);
                if($job) $chips->push(['key'=>'job','label'=>'Job: '.(collect($jobFilterOptions)->firstWhere('id',(int)$job)['label'] ?? 'Selected')]);
                if($client) $chips->push(['key'=>'client','label'=>'Client: '.(collect($clientFilterOptions)->firstWhere('id',(int)$client)['label'] ?? 'Selected')]);
                if($assignee) $chips->push(['key'=>'assignee','label'=>'Assignee: '.(collect($assigneeFilterOptions)->firstWhere('id',(int)$assignee)['label'] ?? 'Selected')]);
                if($status) $chips->push(['key'=>'status','label'=>'Status: '.$status]);
                if($due) $chips->push(['key'=>'due','label'=>'Due: '.(['overdue'=>'Overdue','today'=>'Due today','week'=>'Due this week','month'=>'Next 30 days','none'=>'No due date'][$due] ?? $due)]);
            ?>
            <div class="ft-list-active-row">
                <div class="ft-list-filter-chips"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $chips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span class="ft-list-filter-chip"><?php echo e($chip['label']); ?><button type="button" wire:click="clearFilter('<?php echo e($chip['key']); ?>')">×</button></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><span>No filters applied</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <div class="ft-list-filter-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($chips->isNotEmpty()): ?><button type="button" class="ft-list-clear-all" wire:click="clearFilters">Clear all filters</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>
                        <span class="ft-board-group-controls" aria-label="Order group controls"><button type="button" class="ft-filter-collapse" wire:click="expandVisibleJobs('<?php echo e($jobs->pluck('id')->implode(',')); ?>')" title="Expand all order cards"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button><button type="button" class="ft-filter-collapse" wire:click="collapseAll" title="Collapse all order cards"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button></span>
                    <?php else: ?>
                        <span class="ft-board-group-controls" aria-label="Task order group controls"><button type="button" class="ft-filter-collapse" wire:click="expandAllTaskGroups" title="Expand all orders"><svg viewBox="0 0 24 24"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg></button><button type="button" class="ft-filter-collapse" wire:click="collapseAllTaskGroups" title="Collapse all orders"><svg viewBox="0 0 24 24"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg></button></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs'): ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$cardsReady): ?>
            <?php echo $__env->make('livewire.shared.board-cards-placeholder', ['columns' => max(3, $phases->count())], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
        <div class="ft-lane-sticky-header">
            <div class="ft-board-horizontal-scroll ft-lane-header-scroll" x-ref="jobHeaderScroll" x-on:scroll="$refs.jobBodyScroll && ($refs.jobBodyScroll.scrollLeft = $event.target.scrollLeft)">
                <div class="ft-job-board-header-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $phaseJobs = $jobs->filter(fn($job) => (int)($job->source_workflow_phase_id ?: $job->workflow_phase_id) === (int)$phase->id);
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hideEmptyPhases && $phaseJobs->isEmpty()): ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button type="button" class="ft-board-column-head ft-external-lane-head" x-on:click="phaseClosed[<?php echo e($phase->id); ?>]=!phaseClosed[<?php echo e($phase->id); ?>]">
                            <span><?php echo e(strtoupper($phase->short_name)); ?></span><b><?php echo e($phaseJobs->count()); ?></b>
                            <svg :class="{'rotated':phaseClosed[<?php echo e($phase->id); ?>]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="ft-board-horizontal-scroll ft-board-lanes-scroll ft-board-body-scroll ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,job,client,assignee,status,due,workflow,sort" x-ref="jobBodyScroll" x-on:scroll="$refs.jobHeaderScroll && ($refs.jobHeaderScroll.scrollLeft = $event.target.scrollLeft)">
            <div class="ft-job-board-grid ft-job-board-body-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $phaseJobs = $jobs->filter(fn($job) => (int)($job->source_workflow_phase_id ?: $job->workflow_phase_id) === (int)$phase->id);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hideEmptyPhases && $phaseJobs->isEmpty()): ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <section class="ft-board-column ft-job-column ft-board-column-nohead" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-phase-'.e($phase->id).''; ?>wire:key="job-phase-<?php echo e($phase->id); ?>">
                        <button type="button" class="ft-mobile-phase-head" x-on:click="phaseClosed[<?php echo e($phase->id); ?>]=!phaseClosed[<?php echo e($phase->id); ?>]">
                            <span><?php echo e($phase->short_name); ?></span><b><?php echo e($phaseJobs->count()); ?></b>
                            <svg :class="{'rotated':phaseClosed[<?php echo e($phase->id); ?>]}" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="ft-board-column-list" x-show="!phaseClosed[<?php echo e($phase->id); ?>]" x-on:dragover.prevent x-on:drop.prevent="if(draggedJob){$wire.moveJob(draggedJob,<?php echo e($phase->id); ?>);draggedJob=null}">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $phaseJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $canMoveJob = app(\App\Services\AccessControlService::class)->canChangeVisibleJobStatus(auth()->user(), $jobRow);
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canMoveJob): ?>
                                    <?php if (isset($component)) { $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.job-card','data' => ['job' => $jobRow,'expanded' => in_array($jobRow->id,$expandedJobs,true),'draggable' => 'true','xOn:dragstart' => 'draggedJob='.e($jobRow->id).'','xOn:dragend' => 'draggedJob=null','wire:key' => 'job-card-'.e($jobRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.job-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobRow),'expanded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array($jobRow->id,$expandedJobs,true)),'draggable' => 'true','x-on:dragstart' => 'draggedJob='.e($jobRow->id).'','x-on:dragend' => 'draggedJob=null','wire:key' => 'job-card-'.e($jobRow->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $attributes = $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $component = $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <?php if (isset($component)) { $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.board.job-card','data' => ['job' => $jobRow,'expanded' => in_array($jobRow->id,$expandedJobs,true),'draggable' => 'false','wire:key' => 'job-card-'.e($jobRow->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('board.job-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobRow),'expanded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array($jobRow->id,$expandedJobs,true)),'draggable' => 'false','wire:key' => 'job-card-'.e($jobRow->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $attributes = $__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__attributesOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d)): ?>
<?php $component = $__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d; ?>
<?php unset($__componentOriginalcbcc5eff7de3b88c3d9ef252a4a9284d); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div class="ft-board-empty-column">No Orders</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <div
            id="my-work-app"
            x-data="{ metrics: <?php echo \Illuminate\Support\Js::from($taskPackMetrics)->toHtml() ?> }"
            x-on:board-task-metrics.window="metrics = $event.detail"
        >
<style>
/* FlowTrack My Work content follows the supplied prototype; the application shell/sidebar inherits the shared FlowTrack layout. */
body{background:#f3f6fb;color:#172033}
.topbar{height:55px;display:flex;align-items:center;justify-content:flex-end;gap:7px;padding:0 22px;border-bottom:1px solid #dbe3ed;background:#fff;position:sticky;top:0;z-index:20}
.top-actions{margin-left:auto;display:flex;align-items:center;gap:7px}
.lang-btn{height:32px;min-width:32px;padding:0 10px;border:1px solid #dce4ef;border-radius:9px;background:#fff;color:#374257;font-size:11px;display:grid;place-items:center}
.icon-btn{width:32px;height:32px;min-width:32px;padding:0;border:1px solid #dce4ef;border-radius:9px;background:#fff;color:#374257}
.icon-btn svg{width:16px}
.content{width:100%;max-width:1680px;margin:0 auto;padding:21px clamp(12px,2vw,28px) 35px}
#my-work-app .page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:0}
#my-work-app .page-head h1{margin:0;font-size:23px;letter-spacing:-.025em}
#my-work-app .page-head p{margin:4px 0 0;color:#60708b;font-size:10px}
#my-work-app .page-tabs{display:flex;gap:4px;margin-top:12px;border-bottom:1px solid #dbe3ed}
#my-work-app .page-tab{position:relative;min-height:36px;padding:0 11px;border:0;background:transparent;color:#69778e;font-size:9px;font-weight:750}
#my-work-app .page-tab.active{color:#155ce9}
#my-work-app .page-tab.active::after{content:"";position:absolute;right:9px;bottom:-1px;left:9px;height:2px;background:#2463eb}
#my-work-app .metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:9px;margin-top:12px;margin-bottom:0}
#my-work-app .metric{display:flex;min-width:0;align-items:center;justify-content:space-between;gap:8px;min-height:66px;padding:10px 11px;border:1px solid #dbe3ed;border-radius:10px;background:#fff;text-align:left;position:static;overflow:visible;transform:none;box-shadow:none}
#my-work-app .metric:hover,#my-work-app .metric.active{border-color:#91afea;background:#fbfdff;transform:none;box-shadow:none}
#my-work-app .metric span{min-width:0}
#my-work-app .metric small{display:block;overflow:hidden;color:#718097;font-size:8px;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .metric strong{display:block;margin-top:3px;font-size:18px;line-height:1}
#my-work-app .metric i{display:grid;width:27px;height:27px;flex:0 0 27px;place-items:center;border-radius:8px;background:#edf3ff;color:#2463eb;font-size:10px;font-style:normal}
#my-work-app .metric.red i{background:#fff0f0;color:#c43f3f}
#my-work-app .metric.amber i{background:#fff6e5;color:#9a6208}
#my-work-app .toolbar{display:flex;align-items:center;gap:8px;margin-top:10px;margin-bottom:0;padding:10px;border:1px solid #dbe3ed;border-radius:11px;background:#fff;flex-wrap:nowrap}
#my-work-app .search-wrap{position:relative;min-width:240px;flex:1}
#my-work-app .search-icon{position:absolute;left:11px;top:50%;color:#718198;transform:translateY(-50%)}
#my-work-app .search{position:static;display:block;max-width:none;width:100%;height:38px;padding:0 62px 0 34px;border:1px solid #ccd8e8;border-radius:9px;background:#fff;outline:0;font-size:9px;flex:none}
#my-work-app .search:focus{border-color:#78a0f2;box-shadow:0 0 0 3px rgba(36,99,235,.08)}
#my-work-app .clear{position:absolute;right:7px;top:50%;padding:4px 6px;border:0;background:transparent;color:#65748a;font-size:7.5px;transform:translateY(-50%)}
#my-work-app .quick-filters{display:flex;gap:5px}
#my-work-app .chip{min-height:33px;padding:0 9px;border:1px solid #d2dce9;border-radius:8px;background:#fff;color:#526078;font-size:8px;font-weight:720}
#my-work-app .chip.active{border-color:#91afea;background:#edf3ff;color:#245fcf}
#my-work-app .sort{height:35px;min-width:150px;padding:0 28px 0 9px;border:1px solid #d2dce9;border-radius:8px;background:#fff;color:#37445a;font-size:8px}
#my-work-app .load-state{display:flex;min-height:28px;align-items:center;justify-content:space-between;gap:8px;padding:0 3px;color:#758298;font-size:7.5px}
#my-work-app .spinner{display:inline-block;width:12px;height:12px;border:2px solid #dce5f2;border-top-color:#2463eb;border-radius:50%;animation:spin .6s linear infinite}
#my-work-app .loading-copy{display:flex;align-items:center;gap:6px}
#my-work-app .list-shell{overflow:hidden;border:1px solid #d8e1ec;border-radius:11px;background:#fff}
#my-work-app .task-head,#my-work-app .task-row{display:grid;grid-template-columns:minmax(230px,1.75fr) minmax(115px,.75fr) 92px 124px minmax(95px,.65fr) minmax(92px,.62fr) 48px;align-items:center;gap:8px}
#my-work-app .task-head{min-height:32px;padding:0 11px;border-bottom:1px solid #dce3ec;background:#f8fafc;color:#69778e;font-size:7px;font-weight:780;letter-spacing:.04em;text-transform:uppercase}
#my-work-app .order-group{border-bottom:1px solid #dce3ec;content-visibility:auto;contain:layout paint style;contain-intrinsic-size:205px}
#my-work-app .order-group:last-child{border-bottom:0}
#my-work-app .order-head{display:grid;grid-template-columns:22px minmax(180px,1.3fr) minmax(130px,.8fr) minmax(110px,.7fr) minmax(100px,.7fr) minmax(120px,.8fr) auto;align-items:center;gap:8px;min-height:49px;padding:7px 11px;background:#fbfcfe}
#my-work-app .order-head:hover{background:#f8faff}
#my-work-app .collapse{display:grid;width:22px;height:22px;place-items:center;border:0;background:transparent;color:#506078;font-size:11px;padding:0}
#my-work-app .order-id{color:#155ce9;font-size:9px;font-weight:800;text-decoration:none}
#my-work-app .order-title{display:block;margin-top:2px;overflow:hidden;color:#253248;font-size:9px;font-weight:730;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .order-client,#my-work-app .order-stage{color:#64738a;font-size:8px}
#my-work-app .health,#my-work-app .flag{display:inline-flex;max-width:100%;align-items:center;padding:3px 6px;border-radius:8px;font-size:7px;font-weight:750;white-space:nowrap}
#my-work-app .health:before{content:none}
#my-work-app .health.green,#my-work-app .flag.green{background:#edf9f4;color:#147e5b}
#my-work-app .health.amber,#my-work-app .flag.amber{background:#fff6e5;color:#9a6208}
#my-work-app .health.red,#my-work-app .flag.red{background:#fff0f0;color:#c43f3f}
#my-work-app .flag.blue{background:#edf3ff;color:#245fcf}
#my-work-app .order-progress{display:flex;align-items:center;gap:6px;color:#67768c;font-size:7.5px}
#my-work-app .progress-track{width:54px;height:5px;overflow:hidden;border-radius:5px;background:#e7ecf3}
#my-work-app .progress-track i{display:block;height:100%;border-radius:5px;background:#2463eb}
#my-work-app .task-count{color:#607087;font-size:7.5px;text-align:right}
#my-work-app .task-row{position:relative;min-height:54px;padding:6px 11px;border-top:1px solid #e5eaf1;background:#fff}
#my-work-app .task-row:hover{background:#fbfdff}
#my-work-app .task-row.saving{opacity:.55}
#my-work-app .task-row.saving::after{content:"";position:absolute;right:8px;top:8px;width:10px;height:10px;border:2px solid #dce5f2;border-top-color:#2463eb;border-radius:50%;animation:spin .6s linear infinite}
#my-work-app .task-main{min-width:0}
#my-work-app .task-link{display:block;overflow:hidden;color:#263248;font-size:9px;font-weight:750;text-decoration:none;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .task-link:hover{color:#155ce9;text-decoration:underline}
#my-work-app .task-ref{display:block;margin-top:3px;color:#7c899c;font-size:7px}
#my-work-app .phase{overflow:hidden;color:#5a687f;font-size:8px;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .due{font-size:8px}
#my-work-app .due.overdue{color:#c43f3f;font-weight:750}
#my-work-app .due.today{color:#9a6208;font-weight:750}
#my-work-app .status-select{width:100%;height:30px;padding:0 22px 0 7px;border:1px solid #cfdbea;border-radius:7px;background:#fff;color:#344057;font-size:7.5px}
#my-work-app .updated{color:#718097;font-size:7.5px}
#my-work-app .row-action{display:grid;width:42px;height:28px;place-items:center;border:1px solid #cbd8e8;border-radius:7px;background:#fff;color:#155ce9;font-size:7.5px;font-weight:750;text-decoration:none}
#my-work-app .empty{padding:40px 15px;color:#718097;font-size:9px;text-align:center}
#my-work-app .empty strong{display:block;margin-bottom:5px;color:#2d3950;font-size:11px}
#my-work-app .footer{display:flex;align-items:center;justify-content:space-between;gap:10px;min-height:50px;padding:8px 11px;border-top:1px solid #dce3ec;background:#fbfcfe;color:#69778e;font-size:8px}
#my-work-app .pages{display:flex;gap:5px}
#my-work-app .page-button{min-width:31px;height:31px;padding:0 8px;border:1px solid #cfdbea;border-radius:7px;background:#fff;color:#40506a;font-size:8px;font-weight:750}
#my-work-app .page-button.active{border-color:#2463eb;background:#2463eb;color:#fff}
#my-work-app .page-button:disabled{opacity:.45;cursor:default}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes reveal{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
#my-work-app .metric,#my-work-app .list-shell{animation:reveal .25s ease both}
#my-work-app .metric:nth-child(2){animation-delay:.03s}
#my-work-app .metric:nth-child(3){animation-delay:.06s}
#my-work-app .metric:nth-child(4){animation-delay:.09s}
#my-work-app .metric:nth-child(5){animation-delay:.12s}
@media(max-width:1180px){
    #my-work-app .metrics{grid-template-columns:repeat(3,minmax(0,1fr))}
    #my-work-app .task-head{display:none}
    #my-work-app .task-row{grid-template-columns:minmax(190px,1.35fr) minmax(105px,.75fr) 84px 116px 88px 85px 48px}
    #my-work-app .order-head{grid-template-columns:22px minmax(170px,1fr) minmax(110px,.7fr) minmax(100px,.7fr) minmax(85px,.6fr) auto}
    #my-work-app .order-progress{display:none}
}
@media(max-width:820px){
    .content{padding-inline:11px}
    #my-work-app .metrics{grid-template-columns:repeat(2,minmax(0,1fr))}
    #my-work-app .toolbar{align-items:stretch;flex-wrap:wrap}
    #my-work-app .search-wrap{width:100%;flex-basis:100%}
    #my-work-app .sort{flex:1}
    #my-work-app .task-row{grid-template-columns:minmax(0,1.4fr) 86px 108px 44px;grid-template-areas:"task due status action" "phase updated flag action";row-gap:6px;min-height:78px}
    #my-work-app .task-main{grid-area:task}
    #my-work-app .phase{grid-area:phase}
    #my-work-app .due{grid-area:due}
    #my-work-app .status-select{grid-area:status}
    #my-work-app .updated{grid-area:updated}
    #my-work-app .task-row>.flag{grid-area:flag;justify-self:start}
    #my-work-app .row-action{grid-area:action}
    #my-work-app .order-head{grid-template-columns:22px minmax(0,1fr) auto;grid-template-areas:"toggle order count" "toggle client health";row-gap:4px}
    #my-work-app .collapse{grid-area:toggle}
    #my-work-app .order-identity{grid-area:order}
    #my-work-app .order-client{grid-area:client}
    #my-work-app .order-stage{display:none}
    #my-work-app .order-head>.health{grid-area:health;justify-self:end}
    #my-work-app .task-count{grid-area:count}
}
@media(max-width:520px){
    .topbar{padding-inline:10px}
    .content{padding:14px 8px 28px}
    #my-work-app .page-head p{display:none}
    #my-work-app .metrics{grid-template-columns:repeat(2,minmax(0,1fr))}
    #my-work-app .metric{min-height:59px;padding:8px}
    #my-work-app .metric:nth-child(5){grid-column:1/-1}
    #my-work-app .toolbar{padding:8px}
    #my-work-app .quick-filters{width:100%;overflow:auto}
    #my-work-app .chip{flex:0 0 auto}
    #my-work-app .task-row{grid-template-columns:minmax(0,1fr) 95px 42px;grid-template-areas:"task status action" "phase due action" "updated flag action";padding:9px;min-height:105px}
    #my-work-app .footer{align-items:flex-start;flex-direction:column}
    #my-work-app .pages{width:100%}
    #my-work-app .page-button{flex:1}
    #my-work-app .order-title{white-space:normal}
}
@media(prefers-reduced-motion:reduce){#my-work-app *,#my-work-app *::before,#my-work-app *::after{animation:none!important;transition:none!important}}

/* Readability adjustment only for My Work content; shared shell/sidebar typography remains global. */
.lang-btn{font-size:12px}
#my-work-app .page-head h1{font-size:24px}
#my-work-app .page-head p{font-size:11px}
#my-work-app .page-tab{font-size:10px}
#my-work-app .metric small{font-size:9px}
#my-work-app .metric strong{font-size:19px}
#my-work-app .metric i{font-size:11px}
#my-work-app .search{font-size:10px}
#my-work-app .clear{font-size:8.5px}
#my-work-app .chip{font-size:9px}
#my-work-app .sort{font-size:9px}
#my-work-app .load-state{font-size:8.5px}
#my-work-app .task-head{font-size:8px}
#my-work-app .collapse{font-size:12px}
#my-work-app .order-id,#my-work-app .order-title{font-size:10px}
#my-work-app .order-client,#my-work-app .order-stage{font-size:9px}
#my-work-app .health,#my-work-app .flag{font-size:8px}
#my-work-app .order-progress,#my-work-app .task-count{font-size:8.5px}
#my-work-app .task-link{font-size:10px}
#my-work-app .task-ref{font-size:8px}
#my-work-app .phase,#my-work-app .due{font-size:9px}
#my-work-app .status-select,#my-work-app .updated,#my-work-app .row-action{font-size:8.5px}
#my-work-app .empty{font-size:10px}
#my-work-app .empty strong{font-size:12px}
#my-work-app .footer,#my-work-app .page-button{font-size:9px}
</style>

            <div class="page-head">
                <div>
                    <h1>Task Board</h1>
                    <p><?php echo e($taskPackAdministratorView
                        ? 'All active Job tasks, grouped by Order and ranked by what needs action first.'
                        : 'Tasks from Jobs associated with your assigned work, grouped by Order and ranked by what needs action first.'); ?></p>
                </div>
            </div>

            <div class="ft-board-tabs ft-reference-tabs" aria-label="Board navigation">
                <button type="button" class="<?php echo e($mode === 'jobs' ? 'active' : ''); ?>" wire:click="setMode('jobs')">Job Board</button>
                <button type="button" class="<?php echo e($mode === 'tasks' ? 'active' : ''); ?>" wire:click="setMode('tasks')">Task Board</button>
            </div>

            <section class="work-view" aria-busy="false">
                <div class="metrics" aria-label="Task Board work summary">
                    <button type="button" class="metric amber <?php echo e($taskQuick === 'attention' ? 'active' : ''); ?>" wire:click="setTaskQuick('attention')"><span><small>Needs my action</small><strong x-text="metrics.attention"><?php echo e($taskPackMetrics['attention'] ?? 0); ?></strong></span><i>⚑</i></button>
                    <button type="button" class="metric red <?php echo e($taskQuick === 'overdue' ? 'active' : ''); ?>" wire:click="setTaskQuick('overdue')"><span><small>Overdue</small><strong x-text="metrics.overdue"><?php echo e($taskPackMetrics['overdue'] ?? 0); ?></strong></span><i>!</i></button>
                    <button type="button" class="metric amber <?php echo e($taskQuick === 'today' ? 'active' : ''); ?>" wire:click="setTaskQuick('today')"><span><small>Due today</small><strong x-text="metrics.today"><?php echo e($taskPackMetrics['today'] ?? 0); ?></strong></span><i>◷</i></button>
                    <button type="button" class="metric <?php echo e($taskQuick === 'upcoming' ? 'active' : ''); ?>" wire:click="setTaskQuick('upcoming')"><span><small>Upcoming</small><strong x-text="metrics.upcoming"><?php echo e($taskPackMetrics['upcoming'] ?? 0); ?></strong></span><i>→</i></button>
                    <button type="button" class="metric <?php echo e($taskQuick === 'waiting' ? 'active' : ''); ?>" wire:click="setTaskQuick('waiting')"><span><small>Waiting</small><strong x-text="metrics.waiting"><?php echo e($taskPackMetrics['waiting'] ?? 0); ?></strong></span><i>⌛</i></button>
                </div>

                <div class="toolbar">
                    <label class="search-wrap">
                        <span class="search-icon">⌕</span>
                        <input class="search" type="search" wire:model.live.debounce.400ms="search" autocomplete="off" placeholder="Search my tasks, Orders, clients or flags" aria-label="Search Task Board">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearTaskSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <div class="quick-filters">
                        <button type="button" class="chip <?php echo e($taskQuick === 'attention' ? 'active' : ''); ?>" wire:click="setTaskQuick('attention')">Needs action</button>
                        <button type="button" class="chip <?php echo e($taskQuick === 'all' ? 'active' : ''); ?>" wire:click="setTaskQuick('all')">All my tasks</button>
                        <button type="button" class="chip <?php echo e($taskQuick === 'mentions' ? 'active' : ''); ?>" wire:click="setTaskQuick('mentions')">Mentions (<span x-text="metrics.mentions"><?php echo e($taskPackMetrics['mentions'] ?? 0); ?></span>)</button>
                    </div>
                    <select class="sort" wire:model.live="taskSort" aria-label="Sort Task Board">
                        <option value="action">Sort: Action priority</option>
                        <option value="due">Sort: Due soon</option>
                        <option value="job">Sort: Order number</option>
                    </select>
                </div>

                <div class="load-state">
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskPackPaginator && $taskPackPaginator->total()): ?>
                            Showing <?php echo e($taskPackGroups->count()); ?> of <?php echo e($taskPackPaginator->total()); ?> matching Orders · <?php echo e($taskPackTaskCount); ?> visible tasks
                        <?php elseif($taskPackAdministratorView): ?>
                            Showing all active Job Task Packs
                        <?php else: ?>
                            Showing associated Job Task Packs only
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <span class="loading-copy">
                        <span wire:loading.remove wire:target="search,taskQuick,taskSort,setTaskQuick,clearTaskSearch,gotoPage,previousPage,nextPage">Results update after 400 ms</span>
                        <span wire:loading.delay.long wire:target="search,taskQuick,taskSort,setTaskQuick,clearTaskSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Searching all visible work…</span>
                    </span>
                </div>

                <section class="list-shell" aria-label="Task Board tasks grouped by Order">
                    <div class="task-head"><span>Task</span><span>Phase</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>

                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $taskPackGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <article class="order-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-order-'.e($group['id']).''; ?>wire:key="board-task-order-<?php echo e($group['id']); ?>" x-data="{ open: true }">
                                <header class="order-head">
                                    <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Collapse <?php echo e($group['number']); ?>"><span x-text="open ? '⌄' : '›'">⌄</span></button>
                                    <span class="order-identity">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?><a class="order-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a><?php else: ?><span class="order-id"><?php echo e($group['number']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="order-title"><?php echo e($group['title']); ?></span>
                                    </span>
                                    <span class="order-client"><?php echo e($group['client']); ?></span>
                                    <span class="order-stage"><?php echo e($group['stage']); ?></span>
                                    <span class="health <?php echo e($group['healthTone']); ?>"><?php echo e($group['health']); ?></span>
                                    <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                                    <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                                </header>

                                <div class="task-rows" x-show="open">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div
                                            class="task-row"
                                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-'.e($task['id']).''; ?>wire:key="board-task-<?php echo e($task['id']); ?>"
                                            x-data="{
                                                saving:false,
                                                version:<?php echo \Illuminate\Support\Js::from($task['version'])->toHtml() ?>,
                                                currentStatus:<?php echo \Illuminate\Support\Js::from($task['status'])->toHtml() ?>,
                                                async saveStatus(event){
                                                    const select=event.currentTarget;
                                                    const previous=this.currentStatus;
                                                    const next=select.value;
                                                    if(next===previous||this.saving)return;
                                                    this.saving=true;
                                                    select.disabled=true;
                                                    try{
                                                        const result=await $wire.updateTaskStatus(<?php echo e($task['id']); ?>,next,this.version);
                                                        if(!result?.ok){select.value=previous;return;}
                                                        this.currentStatus=result.status||next;
                                                        this.version=result.version||this.version;
                                                        if(result.metrics)window.dispatchEvent(new CustomEvent('board-task-metrics',{detail:result.metrics}));
                                                    }catch(error){select.value=previous;}
                                                    finally{this.saving=false;select.disabled=false;}
                                                }
                                            }"
                                            x-bind:class="{ 'saving': saving }"
                                        >
                                            <div class="task-main">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?><a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a><?php else: ?><span class="task-link"><?php echo e($task['title']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <span class="task-ref"><?php echo e($task['number']); ?></span>
                                            </div>
                                            <span class="phase"><?php echo e($task['phase']); ?></span>
                                            <time class="due <?php echo e($task['dueTone']); ?>"><?php echo e($task['due']); ?></time>
                                            <select class="status-select" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $taskPackStatusOptions, true)): ?><option value="<?php echo e($task['status']); ?>" selected><?php echo e($task['status']); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPackStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </select>
                                            <span class="flag <?php echo e($task['flagTone']); ?>"><?php echo e($task['flag']); ?></span>
                                            <span class="updated"><?php echo e($task['updated']); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?><a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a><?php else: ?><span class="row-action" aria-disabled="true">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </article>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="empty"><strong>No matching work</strong>Try another task, Order, client, or flag.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <footer class="footer">
                        <span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskPackPaginator && $taskPackPaginator->total()): ?>
                                Orders <?php echo e($taskPackPaginator->firstItem()); ?>–<?php echo e($taskPackPaginator->lastItem()); ?> of <?php echo e($taskPackPaginator->total()); ?> · <?php echo e($taskPackTaskCount); ?> tasks on this page
                            <?php elseif($taskPackAdministratorView): ?>
                                All active Job tasks
                            <?php else: ?>
                                Associated Job tasks
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <?php
                            $currentPage = $taskPackPaginator?->currentPage() ?? 1;
                            $lastPage = max(1, $taskPackPaginator?->lastPage() ?? 1);
                            $pageStart = max(1, $currentPage - 2);
                            $pageEnd = min($lastPage, $currentPage + 2);
                        ?>
                        <nav class="pages" aria-label="Pagination">
                            <button type="button" class="page-button" wire:click="previousPage('taskPackPage')" <?php if(!$taskPackPaginator || $taskPackPaginator->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button type="button" class="page-button <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'taskPackPage')" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <button type="button" class="page-button" wire:click="nextPage('taskPackPage')" <?php if(!$taskPackPaginator || !$taskPackPaginator->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                        </nav>
                    </footer>
                </section>
            </section>
        </div>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'jobs' && $cardsReady && $hasMoreCards): ?>
        <div class="ft-board-load-more">
            <button type="button" class="ft-outline-btn" wire:click="loadMore">Load 60 more</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/board/index.blade.php ENDPATH**/ ?>