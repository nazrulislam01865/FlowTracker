<div
    id="my-work-app"
    x-data="{ metrics: <?php echo \Illuminate\Support\Js::from($metrics)->toHtml() ?>, groupsExpanded: true }"
    x-on:my-work-metrics.window="metrics = $event.detail"
>


    <div class="page-head">
        <div>
            <h1>My Work</h1>
            <p>Your assigned tasks, grouped by Order and ranked by what needs action first.</p>
        </div>
        <a class="row-action" style="width:auto;padding:0 10px" href="<?php echo e(route('all-tasks')); ?>" wire:navigate>All Tasks</a>
    </div>

    <nav class="page-tabs" aria-label="My Work view">
        <button type="button" class="page-tab active" aria-current="page">Task list</button>
    </nav>

    <section class="work-view" aria-busy="false">
        <div class="metrics" aria-label="Personal work summary">
            <button type="button" class="metric amber <?php echo e($quick === 'attention' ? 'active' : ''); ?>" wire:click="setQuick('attention')"><span><small>Needs my action</small><strong x-text="metrics.attention ?? '—'"><?php echo e($metrics['attention'] ?? '—'); ?></strong></span><i>⚑</i></button>
            <button type="button" class="metric red <?php echo e($quick === 'overdue' ? 'active' : ''); ?>" wire:click="setQuick('overdue')"><span><small>Overdue</small><strong x-text="metrics.overdue ?? '—'"><?php echo e($metrics['overdue'] ?? '—'); ?></strong></span><i>!</i></button>
            <button type="button" class="metric amber <?php echo e($quick === 'today' ? 'active' : ''); ?>" wire:click="setQuick('today')"><span><small>Due today</small><strong x-text="metrics.today ?? '—'"><?php echo e($metrics['today'] ?? '—'); ?></strong></span><i>◷</i></button>
            <button type="button" class="metric <?php echo e($quick === 'upcoming' ? 'active' : ''); ?>" wire:click="setQuick('upcoming')"><span><small>Upcoming</small><strong x-text="metrics.upcoming ?? '—'"><?php echo e($metrics['upcoming'] ?? '—'); ?></strong></span><i>→</i></button>
            <button type="button" class="metric <?php echo e($quick === 'waiting' ? 'active' : ''); ?>" wire:click="setQuick('waiting')"><span><small>Waiting</small><strong x-text="metrics.waiting ?? '—'"><?php echo e($metrics['waiting'] ?? '—'); ?></strong></span><i>⌛</i></button>
        </div>

        <div class="toolbar">
            <label class="search-wrap">
                <span class="search-icon">⌕</span>
                <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search my tasks, Orders, clients or flags" aria-label="Search my work">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </label>
            <div class="quick-filters">
                <button type="button" class="chip <?php echo e($quick === 'attention' ? 'active' : ''); ?>" wire:click="setQuick('attention')">Needs action</button>
                <button type="button" class="chip <?php echo e($quick === 'all' ? 'active' : ''); ?>" wire:click="setQuick('all')">All my tasks</button>
                <button type="button" class="chip <?php echo e($quick === 'mentions' ? 'active' : ''); ?>" wire:click="setQuick('mentions')">Mentions (<span x-text="metrics.mentions ?? '—'"><?php echo e($metrics['mentions'] ?? '—'); ?></span>)</button>
            </div>
            <label class="completed-toggle <?php echo e($hideCompleted ? 'active' : ''); ?>">
                <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed tasks">
                <span class="completed-check" aria-hidden="true">✓</span>
                <span>Hide completed</span>
            </label>
            <select class="sort" wire:model.live="sort" aria-label="Sort work">
                <option value="action">Sort: Action priority</option>
                <option value="due">Sort: Due soon</option>
                <option value="job">Sort: Order number</option>
            </select>
        </div>

        <div class="load-state">
            <span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchNeedsMoreCharacters): ?>
                    Type 3 characters to search broadly. Order and task reference prefixes can be searched sooner.
                <?php elseif($workPaginator->total()): ?>
                    Showing <?php echo e($workGroups->count()); ?> of <?php echo e($workPaginator->total()); ?> matching Orders · <?php echo e($visibleTaskCount); ?> visible tasks
                <?php else: ?>
                    Showing personal work only
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.remove wire:target="search,quick,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage">Results update after 650 ms</span>
                    <span wire:loading.delay.long wire:target="search,quick,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Searching all visible work…</span>
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

        <div class="work-progress" wire:loading.delay.long.flex wire:target="search,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage" aria-live="polite"><span></span> Updating tasks…</div>

        <section class="list-shell" aria-label="My tasks grouped by Order" wire:loading.class="is-refreshing" wire:target="search,sort,hideCompleted,setQuick,clearSearch,gotoPage,previousPage,nextPage">
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
                            <span class="health <?php echo e($group['healthTone']); ?>"><?php echo e($group['health']); ?></span>
                            <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                            <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                        </header>

                        <div class="task-rows" x-show="open">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div
                                    class="task-row"
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
                                                if(!result?.ok){select.value=previous;return;}
                                                this.currentStatus=result.status||next;
                                                this.version=result.version||this.version;
                                                // Keep the renderless status update, but re-query once when
                                                // completion changes list membership. This removes the task now,
                                                // and removes its Order group too if it was the final visible task.
                                                if(result.completed && <?php echo \Illuminate\Support\Js::from($hideCompleted)->toHtml() ?>)await $wire.$refresh();
                                            }catch(error){select.value=previous;}
                                            finally{this.saving=false;select.disabled=false;}
                                        }
                                    }"
                                    x-bind:class="{ 'saving': saving }"
                                >
                                    <div class="task-main">
                                        <a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a>
                                        <span class="task-ref"><?php echo e($task['number']); ?></span>
                                    </div>
                                    <span class="phase" data-label="Phase"><?php echo e($task['phase']); ?></span>
                                    <span class="assignee" data-label="Assignee" title="<?php echo e($task['assignee']); ?>">
                                        <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task['assignee'],'src' => $task['assigneeAvatar'],'size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeAvatar']),'size' => 22]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
                                        <span class="assignee-name"><?php echo e($task['assignee']); ?></span>
                                    </span>
                                    <span
                                        class="due-editor ft-inline-edit-shell <?php echo e($task['dueTone']); ?>" data-label="Due"
                                        x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('my-work-task-'.$task['id'].'-due-date')->toHtml() ?>, label: 'task due date', value: <?php echo \Illuminate\Support\Js::from($task['dueValue'])->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($task['dueDisplay'])->toHtml() ?> })"
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
                                        <select class="status-select" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $statusOptions, true)): ?><option value="<?php echo e($task['status']); ?>" selected><?php echo e($task['status']); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </span>
                                    <span class="flag <?php echo e($task['flagTone']); ?>" data-label="Flag"><?php echo e($task['flag']); ?></span>
                                    <span class="updated" data-label="Updated"><?php echo e($task['updated']); ?></span>
                                    <a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workGroups->isEmpty()): ?>
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, or flag.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/my-work/index.blade.php ENDPATH**/ ?>