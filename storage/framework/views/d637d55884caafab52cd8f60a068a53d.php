<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['job','users'=>collect()]));

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

foreach (array_filter((['job','users'=>collect()]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $blockers = \App\Support\JobDetailPresenter::blockers($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $requiredTasks = $currentTasks->filter(fn($task) => ($task->setupTemplate?->is_required ?? $task->template?->is_required ?? true) !== false)->values();
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $requiredDone = \App\Support\JobDetailPresenter::completedCount($requiredTasks);
    $next = \App\Support\JobDetailPresenter::nextPhase($job);
    $rows = \App\Support\JobDetailPresenter::phaseHistoryRows($job);
    $currentRequired = \App\Support\JobDetailPresenter::phaseRequiredDocuments($job,$job->phase);
    $receivedCurrent = $currentRequired->where('complete',true)->count();
    $missingCurrent = $currentRequired->where('complete',false);
    $blockingTask = $requiredTasks->first(fn($task) => !$task->completed_at && $task->status !== 'Completed');
    $progress = $currentTasks->count() ? round($done/max(1,$currentTasks->count())*100) : 0;
    $tasksReady = $requiredTasks->filter(fn($task) => !$task->completed_at && $task->status !== 'Completed')->isEmpty();
    $documentsReady = $missingCurrent->isEmpty();
    $canChangeJobStatus = app(\App\Services\AccessControlService::class)->canChangeVisibleJobStatus(auth()->user(), $job);
?>
<div class="ft-workflow-detail-section ft-exact-workflow">
    <div class="ft-section-title-row"><div><h2>Workflow</h2><p><?php echo e($job->workflow->name); ?> · Version 1 · <?php echo e(\App\Support\UserLocalTime::format($job->created_at, 'M j, Y')); ?></p></div></div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($blockers->isNotEmpty()): ?>
        <div class="ft-warning-banner">
            <span>!</span>
            <div>
                <b><?php echo e($blockers->count() === 1 ? $blockers->first()->label : $blockers->count().' Task Pack requirements block the next phase'); ?></b>
                <p><?php echo e($blockers->pluck('description')->implode(' ')); ?></p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($blockingTask): ?><button type="button" wire:click="openTask(<?php echo e($blockingTask->id); ?>)">View blocking task</button><?php else: ?><button type="button" wire:click="setDetailTab('overview')">Review tasks</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="ft-success-banner"><span>✓</span><div><b>Ready for the next phase</b><p>All required Task Pack tasks and Task Pack documents are complete.</p></div></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-workflow-stepper-card"><div class="ft-workflow-stepper">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $job->workflow->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $phaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                $phaseDone = \App\Support\JobDetailPresenter::completedCount($phaseTasks);
                $phaseComplete = \App\Support\JobDetailPresenter::isPhaseComplete($job,$phase);
            ?>
            <div class="ft-workflow-step <?php echo e($phaseComplete ? 'done' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($phase->color)); ?>" <?php if((int) $phase->id === (int) $job->workflow_phase_id): ?> aria-current="step" <?php endif; ?>>
                <span><?php echo e($phaseComplete ? '✓' : $phase->sequence); ?></span>
                <small><?php echo e($phase->short_name); ?></small>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phase->id === $job->phase->id): ?><em>Current · <?php echo e($phaseDone); ?>/<?php echo e($phaseTasks->count()); ?></em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div></section>

    <div class="ft-workflow-main-full">
            <section class="ft-detail-card ft-current-phase-card">
                <div class="ft-card-row-head">
                    <div>
                        <h2>Current phase · <?php echo e($job->phase->name); ?></h2>
                        <div class="ft-phase-progress-copy"><span><?php echo e($done); ?> of <?php echo e($currentTasks->count()); ?> tasks complete</span><div class="ft-line-progress"><span style="width:<?php echo e($progress); ?>%"></span></div><b><?php echo e($progress); ?>%</b></div>
                    </div>
                    <span class="ft-phase-count-pill">Phase <?php echo e($job->phase->sequence); ?> of <?php echo e($job->workflow->phases->count()); ?></span>
                </div>
                <div class="ft-readiness-table ft-taskpack-readiness-only">
                    <div>
                        <span class="<?php echo e($tasksReady ? 'ok' : 'warn'); ?>"><?php echo e($tasksReady ? '✓' : '!'); ?></span><b>1</b><span>Task Pack tasks</span>
                        <strong><?php echo e($requiredDone); ?> of <?php echo e($requiredTasks->count()); ?> required complete</strong>
                        <em class="<?php echo e($tasksReady ? 'complete' : 'remain'); ?>"><?php echo e($tasksReady ? 'Complete' : max(0,$requiredTasks->count()-$requiredDone).' required remaining'); ?></em>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($blockingTask): ?><button wire:click="openTask(<?php echo e($blockingTask->id); ?>)">Open task</button><?php else: ?><button type="button" wire:click="setDetailTab('overview')">View tasks</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <span class="<?php echo e($documentsReady ? 'ok' : 'warn'); ?>"><?php echo e($documentsReady ? '✓' : '!'); ?></span><b>2</b><span>Task Pack documents</span>
                        <strong><?php echo e($currentRequired->isEmpty() ? 'Not required' : $receivedCurrent.' of '.$currentRequired->count().' received'); ?></strong>
                        <em class="<?php echo e($documentsReady ? 'complete' : 'blocked'); ?>"><?php echo e($documentsReady ? 'Complete' : 'Review'); ?></em>
                        <button wire:click="setDetailTab('overview')">Review tasks</button>
                    </div>
                </div>

                <div class="ft-next-phase-box">
                    <span>▣</span>
                    <div><b>Next phase: <?php echo e($next?->name ?? 'Completed'); ?></b><p><?php echo e($blockers->isEmpty() ? 'All Task Pack requirements are ready.' : 'Complete the remaining Task Pack requirements.'); ?></p></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($blockingTask): ?><button class="ft-outline-btn" type="button" wire:click="openTask(<?php echo e($blockingTask->id); ?>)">Open blocking task</button><?php elseif(!$documentsReady): ?><button class="ft-outline-btn" type="button" wire:click="setDetailTab('overview')">Review required files</button><?php else: ?><button class="ft-outline-btn" type="button" wire:click="setDetailTab('overview')">Review</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canChangeJobStatus): ?>
                        <button class="<?php echo e($blockers->isEmpty() ? 'ft-new-job-btn' : 'ft-disabled-btn'); ?>" wire:click="completePhase" <?php if($blockers->isNotEmpty()): echo 'disabled'; endif; ?>>Move to <?php echo e($next?->name ?? 'Completed'); ?></button>
                    <?php else: ?>
                        <span class="ft-permission-note">Only the assigned Job owner can move this Job to another phase.</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phaseCompletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ft-warning-banner slim"><span>!</span><p><?php echo e($message); ?></p></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-detail-card ft-history-card">
                <h2>Phase history</h2><p>Each phase is calculated only from its selected Task Pack tasks and Task Pack document requirements.</p>
                <table class="ft-history-table"><thead><tr><th>Phase</th><th>Status</th><th>Entered</th><th>Completed</th><th>Time in phase</th><th>Outcome</th></tr></thead><tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td data-label="Phase"><?php if (isset($component)) { $__componentOriginal9414ddaaf6095649bba169634abf8f57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9414ddaaf6095649bba169634abf8f57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.phase-label','data' => ['phase' => $row->phase,'short' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.phase-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['phase' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row->phase),'short' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $attributes = $__attributesOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__attributesOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9414ddaaf6095649bba169634abf8f57)): ?>
<?php $component = $__componentOriginal9414ddaaf6095649bba169634abf8f57; ?>
<?php unset($__componentOriginal9414ddaaf6095649bba169634abf8f57); ?>
<?php endif; ?></td>
                            <td data-label="Status"><span class="ft-soft-pill <?php echo e($row->status==='Completed'?'green':($row->status==='Current'?'blue':'gray')); ?>"><?php echo e($row->status); ?></span></td>
                            <td data-label="Entered"><?php echo e(\App\Support\UserLocalTime::format($row->entered, 'M j Y')); ?></td>
                            <td data-label="Completed"><?php echo e(\App\Support\UserLocalTime::format($row->completed, 'M j Y')); ?></td>
                            <td data-label="Time in phase"><?php echo e($row->time ? $row->time.' day'.($row->time>1?'s':'') : '—'); ?></td>
                            <td data-label="Outcome" class="<?php echo e($row->outcome==='Passed'?'green-text':($row->outcome==='Blocked'?'warn-text':'')); ?>"><?php echo e($row->outcome); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody></table>
            </section>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail-workflow.blade.php ENDPATH**/ ?>