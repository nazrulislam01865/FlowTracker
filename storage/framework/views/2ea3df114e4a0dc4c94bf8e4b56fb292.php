<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'groups' => collect(),
    'paginator' => null,
    'statusOptions' => [],
    'taskCount' => 0,
    'allGroupsExpanded' => true,
    'groupStateKey' => 'open',
    'administratorView' => false,
    'embedded' => false,
    'showFooter' => true,
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
    'groups' => collect(),
    'paginator' => null,
    'statusOptions' => [],
    'taskCount' => 0,
    'allGroupsExpanded' => true,
    'groupStateKey' => 'open',
    'administratorView' => false,
    'embedded' => false,
    'showFooter' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section class="ft-board-taskpack-list-shell <?php echo e($embedded ? 'is-embedded' : ''); ?>" aria-label="Task Pack tasks grouped by Order">
    <div class="ft-board-taskpack-task-head">
        <span>Task</span>
        <span>Assignee</span>
        <span>Phase</span>
        <span>Due</span>
        <span>Status</span>
        <span>Flag</span>
        <span>Updated</span>
        <span>View</span>
    </div>

    <div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <article
                class="ft-board-taskpack-job-group"
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-pack-job-'.e($group['id']).'-'.e($groupStateKey).''; ?>wire:key="board-task-pack-job-<?php echo e($group['id']); ?>-<?php echo e($groupStateKey); ?>"
                x-data="{ open: <?php echo \Illuminate\Support\Js::from((bool) $allGroupsExpanded)->toHtml() ?> }"
            >
                <header class="ft-board-taskpack-job-head">
                    <button
                        type="button"
                        class="ft-board-taskpack-collapse"
                        x-on:click="open = !open"
                        x-bind:aria-expanded="open.toString()"
                        aria-label="Toggle <?php echo e($group['number']); ?>"
                    ><span x-text="open ? '⌄' : '›'">⌄</span></button>

                    <span class="ft-board-taskpack-job-identity">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?>
                            <a class="ft-board-taskpack-job-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a>
                        <?php else: ?>
                            <span class="ft-board-taskpack-job-id"><?php echo e($group['number']); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="ft-board-taskpack-job-title"><?php echo e($group['title']); ?></span>
                    </span>

                    <span class="ft-board-taskpack-job-client"><?php echo e($group['client']); ?></span>
                    <span class="ft-board-taskpack-job-stage"><?php echo e($group['stage']); ?></span>
                    <span class="ft-board-taskpack-progress"><i><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                    <span class="ft-board-taskpack-task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                </header>

                <div class="ft-board-taskpack-task-rows" x-show="open">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div
                            class="ft-board-taskpack-task-row"
                            style="<?php echo e(\App\Support\MasterColor::style($task['taskColor'] ?? null)); ?>border-left:4px solid var(--ft-master-color,#2563EB)"
                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-pack-task-'.e($task['id']).''; ?>wire:key="board-task-pack-task-<?php echo e($task['id']); ?>"
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
                                    }catch(error){select.value=previous;window.FlowTrack.ui.masterColor?.applySelect(select);}
                                    finally{this.saving=false;select.disabled=<?php echo e($task['canEdit'] ? 'false' : 'true'); ?>;}
                                }
                            }"
                            x-bind:class="{ 'saving': saving }"
                        >
                            <div class="ft-board-taskpack-task-main">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?>
                                    <a class="ft-board-taskpack-task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a>
                                <?php else: ?>
                                    <span class="ft-board-taskpack-task-link is-static"><?php echo e($task['title']); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="ft-board-taskpack-task-ref"><?php echo e($task['number']); ?></span>
                            </div>

                            <span class="ft-board-taskpack-assignee">
                                <b><?php echo e($task['assignee']); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['isMine']): ?><small>You</small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>

                            <span class="ft-board-taskpack-phase ft-phase-color-label" style="<?php echo e(\App\Support\MasterColor::style($task['phaseColor'] ?? null)); ?>"><?php echo e($task['phase']); ?></span>
                            <time class="ft-board-taskpack-due <?php echo e($task['dueTone']); ?>"><?php echo e($task['due']); ?></time>

                            <select
                                data-master-color-select
                                class="ft-board-taskpack-status-select <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>"
                                style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>"
                                <?php if($task['canEdit']): ?> x-on:change="saveStatus($event); window.FlowTrack.ui.masterColor?.applySelect($event.currentTarget)" <?php else: ?> disabled title="Read only" <?php endif; ?>
                                aria-label="Status for <?php echo e($task['title']); ?>"
                            >
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $statusOptions, true)): ?>
                                    <option value="<?php echo e($task['status']); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status'])); ?>" selected><?php echo e($task['status']); ?></option>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>

                            <span class="ft-board-taskpack-flag <?php echo e($task['flagColor'] ? 'ft-master-color' : $task['flagTone']); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['flagColor'])); ?>"><?php echo e($task['flag']); ?></span>
                            <span class="ft-board-taskpack-updated"><?php echo e($task['updated']); ?></span>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?>
                                <a class="ft-board-taskpack-row-action" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a>
                            <?php else: ?>
                                <span class="ft-board-taskpack-row-action is-disabled" title="Order detail access is not enabled for your role">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </article>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-board-taskpack-empty">
                <strong>No matching Task Pack work</strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($administratorView): ?>
                    Try another Order, client, assignee, status, or due-date filter.
                <?php else: ?>
                    Only Orders containing at least one task assigned to you are available here.
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paginator && $showFooter): ?>
        <footer class="ft-board-taskpack-footer">
            <span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paginator->total()): ?>
                    Orders <?php echo e($paginator->firstItem()); ?>–<?php echo e($paginator->lastItem()); ?> of <?php echo e($paginator->total()); ?> · <?php echo e($taskCount); ?> tasks on this page
                <?php elseif($administratorView): ?>
                    All active Order task lists
                <?php else: ?>
                    Associated Order task lists
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>

            <?php
                $currentPage = $paginator->currentPage();
                $lastPage = max(1, $paginator->lastPage());
                $pageStart = max(1, $currentPage - 2);
                $pageEnd = min($lastPage, $currentPage + 2);
            ?>

            <nav class="ft-board-taskpack-pages" aria-label="Task Pack pagination">
                <button type="button" class="ft-board-taskpack-page-button" wire:click="previousPage('taskPackPage')" <?php if($paginator->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button
                        type="button"
                        class="ft-board-taskpack-page-button <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>"
                        wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'taskPackPage')"
                        <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>
                    ><?php echo e($pageNumber); ?></button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <button type="button" class="ft-board-taskpack-page-button" wire:click="nextPage('taskPackPage')" <?php if(!$paginator->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
            </nav>
        </footer>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/board/task-pack-list.blade.php ENDPATH**/ ?>