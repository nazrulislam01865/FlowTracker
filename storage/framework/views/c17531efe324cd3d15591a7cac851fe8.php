<div
    id="my-work-app"
    x-data="{ metrics: <?php echo \Illuminate\Support\Js::from($metrics)->toHtml() ?>, groupsExpanded: true }"
    x-on:my-work-metrics.window="metrics = $event.detail"
>


    <div class="page-head">
        <div>
            <h1>My Tasks</h1>
            <p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sourceFilter === 'inquiries' && $statusFilter !== ''): ?>
                    Only your assigned Inquiry tasks matching the selected dashboard status filter.
                <?php else: ?>
                    Only your currently active assigned tasks are shown here. The same personal assignment rule applies to every role, including Admin and Super Admin.
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
    </div>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sourceFilter === 'inquiries' && $statusFilter !== ''): ?>
    <section class="work-view" aria-busy="false">
        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search filtered Inquiry tasks" aria-label="Search filtered Inquiry tasks">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="quick-filters" aria-label="Active dashboard task filter">
                    <span class="chip active">Inquiry tasks</span>
                    <span class="chip active">Status: <?php echo e($statusFilter); ?></span>
                </div>
            </div>
            <div class="toolbar-secondary">
                <select class="sort" wire:model.live="sort" aria-label="Sort filtered Inquiry tasks">
                    <option value="action">Sort: Action priority</option>
                    <option value="due">Sort: Due soon</option>
                    <option value="job">Sort: Inquiry number</option>
                </select>
                <button type="button" class="chip clear-filters" wire:click="clearStatusFilter">Clear status filter</button>
            </div>
        </div>

        <div class="load-state">
            <span><strong><?php echo e($statusFilter); ?></strong> Inquiry tasks</span>
            <span class="loading-copy">
                <span wire:loading.delay.long wire:target="search,sort,clearSearch,clearStatusFilter"><i class="spinner"></i> Updating tasks…</span>
            </span>
        </div>

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,sort,clearSearch,clearStatusFilter" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My Inquiry Tasks filtered by status" wire:loading.class="is-refreshing" wire:target="search,sort,clearSearch,clearStatusFilter">
            <div class="task-table-scroll">
                <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>
                <div>
                    <?php echo $__env->make('livewire.my-work._inquiry-groups', ['inquiryGroups' => $inquiryGroups], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryGroups->isEmpty()): ?>
                        <div class="empty"><strong>No matching Inquiry tasks</strong>No My Task records currently use the selected status.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <footer class="footer">
                <span><?php echo e($inquiryVisibleTaskCount); ?> <?php echo e($inquiryVisibleTaskCount === 1 ? 'task' : 'tasks'); ?> with status “<?php echo e($statusFilter); ?>”</span>
            </footer>
        </section>
    </section>
    <?php else: ?>

    
    <?php if (isset($component)) { $__componentOriginalee5bb7364c37061cbe535f4c41f9060f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee5bb7364c37061cbe535f4c41f9060f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.orders.workflow-stage-overview','data' => ['stages' => $taskStages,'selectedStageValue' => $phaseFilter,'mode' => 'wire-filter','filterMethod' => 'setPhaseFilter','title' => 'My tasks by workflow stage','description' => 'Click a stage to filter the tasks below on this page.','countLabel' => 'Open tasks']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('orders.workflow-stage-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStages),'selected-stage-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilter),'mode' => 'wire-filter','filter-method' => 'setPhaseFilter','title' => 'My tasks by workflow stage','description' => 'Click a stage to filter the tasks below on this page.','count-label' => 'Open tasks']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee5bb7364c37061cbe535f4c41f9060f)): ?>
<?php $attributes = $__attributesOriginalee5bb7364c37061cbe535f4c41f9060f; ?>
<?php unset($__attributesOriginalee5bb7364c37061cbe535f4c41f9060f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee5bb7364c37061cbe535f4c41f9060f)): ?>
<?php $component = $__componentOriginalee5bb7364c37061cbe535f4c41f9060f; ?>
<?php unset($__componentOriginalee5bb7364c37061cbe535f4c41f9060f); ?>
<?php endif; ?>

    <section class="work-view" aria-busy="false">
        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search tasks, Orders, clients or flags" aria-label="Search my work">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phaseFilter !== ''): ?>
            <?php
                $hiddenTaskStatusFilters = [
                    '', 'not start', 'not started', 'not ready', 'locked', 'skipped',
                    'not applicable', 'n/a', 'completed', 'cancelled', 'canceled',
                    'waiting for sample approval', 'waiting for qc issue resolution',
                ];
                $stageTaskStatusOptions = collect($statusOptions)
                    ->filter(fn ($statusOption) => ! in_array(mb_strtolower(trim((string) $statusOption)), $hiddenTaskStatusFilters, true))
                    ->values();
                $selectedMyTaskStage = collect($taskStages ?? [])->first(
                    fn ($stage) => mb_strtolower(trim((string) data_get($stage, 'name'))) === mb_strtolower(trim($phaseFilter))
                );
                $selectedMyTaskStageSequence = (int) data_get($selectedMyTaskStage, 'sequence', 0);
            ?>
            <div class="ft-order-list-v5 my-task-stage-filter-parity">
                <div class="stage-inline-controls" aria-label="<?php echo e($phaseFilter); ?> task filters">
                    <span class="stage-inline-label"><?php echo e($phaseFilter); ?></span>
                    <div class="stage-inline-quick" role="group" aria-label="Task status">
                        <button
                            type="button"
                            class="stage-inline-chip <?php echo e($statusFilter === '' ? 'active' : ''); ?>"
                            style="--quick-color:#0F8F7C"
                            wire:click="setTaskStatusFilter('')"
                            aria-pressed="<?php echo e($statusFilter === '' ? 'true' : 'false'); ?>"
                        >
                            <span class="stage-inline-check" aria-hidden="true">✓</span>
                            <span>All</span>
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stageTaskStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $taskStatusColor = \App\Support\MasterColor::normalize(
                                    app(\App\Services\MasterDataService::class)->colorFor('order_task_status', (string) $statusOption)
                                ) ?: '#0F8F7C';
                            ?>
                            <button
                                type="button"
                                class="stage-inline-chip <?php echo e($statusFilter === $statusOption ? 'active' : ''); ?>"
                                style="--quick-color:<?php echo e($taskStatusColor); ?>"
                                wire:click='setTaskStatusFilter(<?php echo \Illuminate\Support\Js::from($statusOption)->toHtml() ?>)'
                                aria-pressed="<?php echo e($statusFilter === $statusOption ? 'true' : 'false'); ?>"
                            >
                                <span class="stage-inline-check" aria-hidden="true">✓</span>
                                <span><?php echo e($statusOption); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                    <span class="stage-view-note">Row colors match task status</span>

                    <div class="stage-inline-selects">
                        <div class="stage-filter-field">
                            <span class="stage-filter-caption">Supplier</span>
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-order-v5-stage-search-select ft-order-v5-supplier-filter','label' => 'Supplier','property' => 'stageSupplier','type' => 'suppliers','context' => 'order-list','value' => $stageSupplier,'placeholder' => 'All suppliers','initialOptions' => $stageSupplierOptions,'searchPlaceholder' => 'Search supplier...','footerMessage' => 'Type 2 characters to search suppliers.','hideLabel' => true,'fixedMenu' => true,'menuWidth' => 320,'wire:key' => 'my-task-stage-supplier-'.e($selectedMyTaskStageSequence).'-'.e(filled($stageSupplier) ? $stageSupplier : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-v5-stage-search-select ft-order-v5-supplier-filter','label' => 'Supplier','property' => 'stageSupplier','type' => 'suppliers','context' => 'order-list','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stageSupplier),'placeholder' => 'All suppliers','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stageSupplierOptions),'search-placeholder' => 'Search supplier...','footer-message' => 'Type 2 characters to search suppliers.','hide-label' => true,'fixed-menu' => true,'menu-width' => 320,'wire:key' => 'my-task-stage-supplier-'.e($selectedMyTaskStageSequence).'-'.e(filled($stageSupplier) ? $stageSupplier : 'all').'']); ?>
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

                        <div class="stage-filter-field stage-filter-field-user">
                            <span class="stage-filter-caption"><?php echo e($phaseFilter); ?> assignee</span>
                            <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-order-v5-stage-search-select ft-order-v5-stage-assignee-filter','label' => $phaseFilter.' assignee','property' => 'stageAssignee','type' => 'users','context' => 'order-list-user-filter','value' => $stageAssignee,'placeholder' => 'All '.strtolower($phaseFilter).' assignees','initialOptions' => $stageAssigneeOptions,'showAvatar' => true,'searchPlaceholder' => 'Search user...','footerMessage' => 'All active FlowTrack users are available.','hideLabel' => true,'fixedMenu' => true,'menuWidth' => 340,'wire:key' => 'my-task-stage-assignee-'.e($selectedMyTaskStageSequence).'-'.e(filled($stageAssignee) ? $stageAssignee : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-v5-stage-search-select ft-order-v5-stage-assignee-filter','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilter.' assignee'),'property' => 'stageAssignee','type' => 'users','context' => 'order-list-user-filter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stageAssignee),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('All '.strtolower($phaseFilter).' assignees'),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stageAssigneeOptions),'show-avatar' => true,'search-placeholder' => 'Search user...','footer-message' => 'All active FlowTrack users are available.','hide-label' => true,'fixed-menu' => true,'menu-width' => 340,'wire:key' => 'my-task-stage-assignee-'.e($selectedMyTaskStageSequence).'-'.e(filled($stageAssignee) ? $stageAssignee : 'all').'']); ?>
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
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="load-state">
            <span></span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.delay.long wire:target="search,phaseFilter,statusFilter,stageSupplier,stageAssignee,quick,sort,hideCompleted,setMetricFilter,setPhaseFilter,setTaskStatusFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Updating tasks…</span>
                </span>
                <span class="group-controls" aria-label="Order group controls">
                    <button type="button" class="group-control" x-on:click="groupsExpanded = true" title="Expand all Orders" aria-label="Expand all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 6 5 5 5-5M5 11l5 5 5-5"/></svg>
                    </button>
                    <button type="button" class="group-control" x-on:click="groupsExpanded = false" title="Collapse all Orders" aria-label="Collapse all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 14 5-5 5 5M5 9l5-5 5 5"/></svg>
                    </button>
                </span>
            </span>
        </div>

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,phaseFilter,statusFilter,stageSupplier,stageAssignee,sort,hideCompleted,setMetricFilter,setTaskStatusFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My Tasks grouped by Order" wire:loading.class="is-refreshing" wire:target="search,phaseFilter,statusFilter,stageSupplier,stageAssignee,sort,hideCompleted,setMetricFilter,setTaskStatusFilter,setQuick,clearFilters,clearSearch,gotoPage,previousPage,nextPage">
            <div class="task-table-scroll">
                <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>

                <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $workGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <article class="order-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-order-'.e($group['id']).''; ?>wire:key="my-work-order-<?php echo e($group['id']); ?>" x-data="{ open: true }" x-effect="open = groupsExpanded">
                        <header class="order-head">
                            <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Collapse <?php echo e($group['number']); ?>"><span x-text="open ? '⌄' : '›'">⌄</span></button>
                            <span class="order-identity">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?><a class="order-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a><?php else: ?><span class="order-id"><?php echo e($group['number']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="order-title"><?php echo e($group['title']); ?></span>
                            </span>
                            <span class="order-client"><?php echo e($group['client']); ?></span>
                            <span class="order-stage"><?php echo e($group['stage']); ?></span>
                            <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                            <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                        </header>

                        <div class="task-rows" x-show="open">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div
                                    class="task-row"
                                    style="<?php echo e(\App\Support\MasterColor::style($task['taskColor'] ?? null)); ?>border-left:4px solid var(--ft-master-color,#2563EB)"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-task-'.e($task['id']).''; ?>wire:key="my-work-task-<?php echo e($task['id']); ?>"
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
                                                if(!result?.ok){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);return;}
                                                this.currentStatus=result.status||next;
                                                this.version=result.version||this.version;
                                                // Keep the renderless status update, but re-query once when
                                                // completion changes list membership. This removes the task now,
                                                // and removes its Order group too if it was the final visible task.
                                                if(result.refresh || (result.completed && <?php echo \Illuminate\Support\Js::from($hideCompleted)->toHtml() ?>))await $wire.$refresh();
                                            }catch(error){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);}
                                            finally{this.saving=false;select.disabled=false;}
                                        }
                                    }"
                                    x-bind:class="{ 'saving': saving }"
                                    x-on:my-work-task-version.stop="if ($event.detail?.version) version = String($event.detail.version)"
                                >
                                    <div class="task-main">
                                        <a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a>
                                        <span class="task-ref"><?php echo e($task['number']); ?></span>
                                    </div>
                                    <span class="phase ft-phase-color-label" data-label="Phase" style="<?php echo e(\App\Support\MasterColor::style($task['phaseColor'] ?? null)); ?>"><?php echo e($task['phase']); ?></span>
                                    <div
                                        class="assignee assignee-editor ft-inline-edit-shell"
                                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'my-work-task-'.e($task['id']).'-assignee-'.e($task['assigneeId'] ?: 0).''; ?>wire:key="my-work-task-<?php echo e($task['id']); ?>-assignee-<?php echo e($task['assigneeId'] ?: 0); ?>"
                                        data-label="Assignee"
                                        title="<?php echo e($task['assignee']); ?>"
                                        x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-assignee')->toHtml() ?>, label: 'task assignee', value: <?php echo \Illuminate\Support\Js::from($task['assigneeId'] ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['assignee'])->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($task['assigneeAvatar'] ?? '')->toHtml() ?> })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        x-on:click.outside="if (editing) cancelEdit()"
                                        x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                        x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssignee(<?php echo e($task['id']); ?>, draftValue, version), { avatarUrl: String($event.detail?.avatarUrl ?? '') }).then(async (ok) => { if (!ok) return; if (lastResponse?.version) $dispatch('my-work-task-version', { version: lastResponse.version }); if (lastResponse?.refresh) await $wire.$refresh(); })"
                                    >
                                        <div class="assignee-display" x-show="!editing">
                                            <span class="assignee-avatar"><?php if (isset($component)) { $__componentOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e0f6ebe9ec45ba5e5c94e141751127 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-live-avatar','data' => ['size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-live-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 22]); ?>
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
<?php endif; ?></span>
                                            <span class="assignee-name" x-text="display"><?php echo e($task['assignee']); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canAssign']): ?>
                                                <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact assignee-edit-button" title="Edit assignee" aria-label="Edit assignee for <?php echo e($task['title']); ?>" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canAssign']): ?>
                                            <div x-cloak x-show="editing" class="assignee-picker">
                                                <?php if (isset($component)) { $__componentOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c33be8c92a6f6cbf6403b5c3f28e607 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-user','data' => ['value' => $task['assigneeId'] ?? '','selectedLabel' => $task['assignee'],'parentType' => 'job','parentId' => $group['id'],'triggerClass' => 'assignee-picker-trigger','variant' => 'compact','menuWidth' => 280]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeId'] ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'parent-type' => 'job','parent-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group['id']),'trigger-class' => 'assignee-picker-trigger','variant' => 'compact','menu-width' => 280]); ?>
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
                                    <span
                                        class="due-editor ft-inline-edit-shell <?php echo e($task['dueTone']); ?>" data-label="Due"
                                        x-data="window.FlowTrack.ui.inlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task['dueValue'])->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['dueDisplay'])->toHtml() ?> })"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    >
                                        <span x-show="!editing" x-text="display" class="ft-task-inline-display"><?php echo e($task['dueDisplay']); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['canEdit']): ?>
                                            <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button compact" title="Edit due date" aria-label="Edit due date for <?php echo e($task['title']); ?>" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.myWorkDue.showPicker ? $refs.myWorkDue.showPicker() : $refs.myWorkDue.focus())">✎</button>
                                            <input x-ref="myWorkDue" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input" type="date"
                                                x-on:keydown.escape.prevent="cancelEdit()"
                                                x-on:blur="if (editing) cancelEdit()"
                                                x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDate(<?php echo e($task['id']); ?>, draftValue))">
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
                                    </span>
                                    <span class="status-wrap" data-label="Status">
                                        <select data-master-color-select class="status-select <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event); window.FlowTrack.ui.masterColor?.applySelect($event.currentTarget)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $statusOptions, true)): ?><option value="<?php echo e($task['status']); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status'])); ?>" selected><?php echo e($task['status']); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </span>
                                    <span class="flag <?php echo e($task['flagColor'] ? 'ft-master-color' : $task['flagTone']); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['flagColor'])); ?>" data-label="Flag"><?php echo e($task['flag']); ?></span>
                                    <span class="updated" data-label="Updated"><?php echo e($task['updated']); ?></span>
                                    <a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate><span class="row-action-desktop">Open</span><span class="row-action-mobile">Details</span><span class="row-action-arrow" aria-hidden="true">→</span></a>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workGroups->isEmpty()): ?>
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, or flag.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <footer class="footer">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workPaginator->total()): ?>
                        Orders <?php echo e($workPaginator->firstItem()); ?>–<?php echo e($workPaginator->lastItem()); ?> of <?php echo e($workPaginator->total()); ?> · <?php echo e($visibleTaskCount); ?> tasks on this page
                    <?php else: ?>
                        My Orders and tasks
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php
                    $currentPage = $workPaginator->currentPage();
                    $lastPage = max(1, $workPaginator->lastPage());
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                ?>
                <nav class="pages" aria-label="Pagination">
                    <button type="button" class="page-button" wire:click="previousPage('workPage')" <?php if($workPaginator->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="page-button <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'workPage')" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" class="page-button" wire:click="nextPage('workPage')" <?php if(!$workPaginator->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                </nav>
            </footer>
        </section>
    </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/my-work/index.blade.php ENDPATH**/ ?>