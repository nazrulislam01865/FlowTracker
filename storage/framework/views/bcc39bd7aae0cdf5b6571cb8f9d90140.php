<?php
    $masterData = app(\App\Services\MasterDataService::class);
    $inquiryService = app(\App\Services\InquiryService::class);
    $tone = static function (string $status): string {
        return match (true) {
            str_contains($status, 'Converted'), str_contains($status, 'Completed') => 'green',
            str_contains($status, 'Dead'), str_contains($status, 'Closed') => 'red',
            str_contains($status, 'Ready'), str_contains($status, 'On Hold') => 'amber',
            str_contains($status, 'Waiting') => 'purple',
            default => 'blue',
        };
    };
    $priorityTone = static function (string $priority): string {
        return match (strtolower(trim($priority))) {
            'critical', 'urgent' => 'red',
            'high' => 'amber',
            'low' => 'green',
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
    $inquiryToolbarIsClear = trim((string) $search) === ''
        && $quick === 'all'
        && $listStatus === ''
        && $listClient === ''
        && $dateFrom === ''
        && $dateTo === ''
        && ! $hideCompleted;
    $inquiryAnyFilterActive = $metricFilter !== '' || ! $inquiryToolbarIsClear;
?>

<div class="ft-inquiry-prototype">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash-inline"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode !== 'create' && $errors->any()): ?><div class="error-inline"><?php echo e($errors->first()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'list'): ?>
        <section class="view">
            <div class="pagehead">
                <div><h1>Inquiries</h1><p>Manage client requests from first inquiry through tasks, conversion, or closure.</p></div>
                <div class="actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('inquiries','create')): ?><button class="primary" type="button" wire:click="openCreate">＋ New Inquiry</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="metrics ft-summary-card-grid" aria-label="Inquiry summary filters">
                <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Created Today','value' => $metrics['createdToday'] ?? 0,'icon' => 'created','tone' => 'blue','caption' => 'New inquiries received','active' => $metricFilter === 'createdToday','wire:click' => 'setMetricFilter(\'createdToday\')','ariaPressed' => ''.e($metricFilter === 'createdToday' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Created Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['createdToday'] ?? 0),'icon' => 'created','tone' => 'blue','caption' => 'New inquiries received','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'createdToday'),'wire:click' => 'setMetricFilter(\'createdToday\')','aria-pressed' => ''.e($metricFilter === 'createdToday' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Not Started','value' => $metrics['notStarted'] ?? 0,'icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => $metricFilter === 'notStarted','wire:click' => 'setMetricFilter(\'notStarted\')','ariaPressed' => ''.e($metricFilter === 'notStarted' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Not Started','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['notStarted'] ?? 0),'icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'notStarted'),'wire:click' => 'setMetricFilter(\'notStarted\')','aria-pressed' => ''.e($metricFilter === 'notStarted' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'In Progress','value' => $metrics['inProgress'] ?? 0,'icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => $metricFilter === 'inProgress','wire:click' => 'setMetricFilter(\'inProgress\')','ariaPressed' => ''.e($metricFilter === 'inProgress' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['inProgress'] ?? 0),'icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'inProgress'),'wire:click' => 'setMetricFilter(\'inProgress\')','aria-pressed' => ''.e($metricFilter === 'inProgress' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Due This Week','value' => $metrics['dueThisWeek'] ?? 0,'icon' => 'due-week','tone' => 'amber','caption' => 'Required date this week','active' => $metricFilter === 'dueThisWeek','wire:click' => 'setMetricFilter(\'dueThisWeek\')','ariaPressed' => ''.e($metricFilter === 'dueThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['dueThisWeek'] ?? 0),'icon' => 'due-week','tone' => 'amber','caption' => 'Required date this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'dueThisWeek'),'wire:click' => 'setMetricFilter(\'dueThisWeek\')','aria-pressed' => ''.e($metricFilter === 'dueThisWeek' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Completed This Week','value' => $metrics['completedThisWeek'] ?? 0,'icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => $metricFilter === 'completedThisWeek','wire:click' => 'setMetricFilter(\'completedThisWeek\')','ariaPressed' => ''.e($metricFilter === 'completedThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Completed This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['completedThisWeek'] ?? 0),'icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'completedThisWeek'),'wire:click' => 'setMetricFilter(\'completedThisWeek\')','aria-pressed' => ''.e($metricFilter === 'completedThisWeek' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Needs Attention','value' => $metrics['attention'] ?? 0,'icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => $metricFilter === 'attention','wire:click' => 'setMetricFilter(\'attention\')','ariaPressed' => ''.e($metricFilter === 'attention' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['attention'] ?? 0),'icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'attention'),'wire:click' => 'setMetricFilter(\'attention\')','aria-pressed' => ''.e($metricFilter === 'attention' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            </div>

            <div class="shell inquiry-list-v2">
                <div class="toolbar">
                    <div class="search"><span>⌕</span><input wire:model.live.debounce.350ms="search" placeholder="Search inquiry, title, client, task or assignee"></div>
                    <div class="filters inquiry-filter-controls">
                        <button class="chip <?php echo e($metricFilter === '' && $inquiryToolbarIsClear ? 'active' : ''); ?>" type="button" wire:click="setQuick('all')" aria-pressed="<?php echo e($metricFilter === '' && $inquiryToolbarIsClear ? 'true' : 'false'); ?>">All</button>
                        <button class="chip ft-inquiry-attention-filter <?php echo e($quick === 'attention' ? 'active' : ''); ?>" type="button" wire:click="setQuick('attention')" aria-pressed="<?php echo e($quick === 'attention' ? 'true' : 'false'); ?>">
                            <span aria-hidden="true">⚠</span> Attention needed
                        </button>
                        <label class="ft-inquiry-status-filter">
                            <select wire:model.live="listStatus" aria-label="Filter inquiries by task status">
                                <option value="">All task statuses</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $listStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($statusOption); ?>"><?php echo e($statusOption); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <span class="ft-inquiry-status-filter-chevron" aria-hidden="true">⌄</span>
                        </label>
                        <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-inquiry-list-client-filter','label' => 'Client','property' => 'listClient','type' => 'clients','context' => 'inquiries','action' => 'setInquiryListFilter','value' => $listClient,'placeholder' => 'All clients','selectedLabel' => $listClientLabel ?: null,'initialOptions' => $listClientFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'inquiry-list-client-filter-'.e($listClient ?: 'all').'-'.e(substr(md5($listClientLabel ?: 'all'), 0, 8)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-inquiry-list-client-filter','label' => 'Client','property' => 'listClient','type' => 'clients','context' => 'inquiries','action' => 'setInquiryListFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listClient),'placeholder' => 'All clients','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listClientLabel ?: null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listClientFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'inquiry-list-client-filter-'.e($listClient ?: 'all').'-'.e(substr(md5($listClientLabel ?: 'all'), 0, 8)).'']); ?>
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
                        <label class="completed-toggle <?php echo e($hideCompleted ? 'active' : ''); ?>">
                            <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed inquiries">
                            <span class="completed-check" aria-hidden="true">✓</span>
                            <span>Hide completed</span>
                        </label>
                        <?php if (isset($component)) { $__componentOriginalfddc3e752d626ff4464d9025a0e0b874 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfddc3e752d626ff4464d9025a0e0b874 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.date-range-filter','data' => ['class' => 'ft-inquiry-date-range','fromProperty' => 'dateFrom','toProperty' => 'dateTo','fromValue' => $dateFrom,'toValue' => $dateTo,'label' => 'Created date','fromLabel' => 'From','toLabel' => 'To']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.date-range-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-inquiry-date-range','from-property' => 'dateFrom','to-property' => 'dateTo','from-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateFrom),'to-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateTo),'label' => 'Created date','from-label' => 'From','to-label' => 'To']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfddc3e752d626ff4464d9025a0e0b874)): ?>
<?php $attributes = $__attributesOriginalfddc3e752d626ff4464d9025a0e0b874; ?>
<?php unset($__attributesOriginalfddc3e752d626ff4464d9025a0e0b874); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfddc3e752d626ff4464d9025a0e0b874)): ?>
<?php $component = $__componentOriginalfddc3e752d626ff4464d9025a0e0b874; ?>
<?php unset($__componentOriginalfddc3e752d626ff4464d9025a0e0b874); ?>
<?php endif; ?>
                        <button
                            class="chip ft-inquiry-clear-filter"
                            type="button"
                            wire:click="clearFilters"
                            <?php if(! $inquiryAnyFilterActive): echo 'disabled'; endif; ?>
                            aria-label="Clear active inquiry filter"
                        >
                            <span aria-hidden="true">×</span> Clear filter
                        </button>
                    </div>
                </div>
                <div class="inquiry-list-table" role="region" aria-label="Inquiry list" tabindex="0">
                    <div class="listhead">
                        <div>Inquiry</div>
                        <div>Title</div>
                        <div>Client / Item</div>
                        <div>Priority</div>
                        <div>Due Date</div>
                        <div>Status</div>
                        <div>Flag</div>
                        <div>Current Task</div>
                        <div>Assignee</div>
                        <div>Task Status</div>
                        <div>Started At</div>
                        <div>Progress</div>
                        <div>Updated At</div>
                        <div>View</div>
                        <div aria-label="Actions"></div>
                    </div>
                    <div class="inquiry-list-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inquiryRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $clientCode = strtoupper(trim((string) ($row['clientCode'] ?? '')));
                                $clientName = strtoupper(trim((string) ($row['client'] ?? '')));
                                $clientRowTone = ($clientCode === 'IID' || preg_match('/\bIID\b/i', $clientName))
                                    ? 'iid'
                                    : (($clientCode === 'NEP' || preg_match('/\bNEP\b/i', $clientName)) ? 'nep' : '');
                            ?>
                            <article class="row <?php echo e($clientRowTone ? 'ft-client-row-'.$clientRowTone : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-list-'.e($row['id']).''; ?>wire:key="inquiry-list-<?php echo e($row['id']); ?>">
                                <div class="cell ft-inquiry-list-identity" data-label="Inquiry">
                                    <span class="ft-copyable-id-wrap ft-inquiry-list-code-wrap">
                                        <a class="id" href="<?php echo e(route('inquiries.index', ['open' => $row['id']])); ?>" wire:navigate><?php echo e($row['number']); ?></a>
                                        <button type="button" class="ft-copy-id-btn" title="Copy Inquiry ID" aria-label="Copy <?php echo e($row['number']); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($row['number'])->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                                    </span>
                                    <span class="sub ft-inquiry-created-by" title="Created by <?php echo e($row['createdBy']); ?>">Created by <?php echo e($row['createdBy']); ?></span>
                                    <span class="sub ft-inquiry-created-at"><?php echo e($row['createdDate']); ?> · <?php echo e($row['createdTime']); ?></span>
                                </div>
                                <div class="cell ft-inquiry-list-title-cell" data-label="Title">
                                    <span class="title ft-inquiry-title-preview ft-inquiry-title-desktop" title="<?php echo e($row['title']); ?>"><?php echo e($row['titlePreview']); ?></span>
                                    <span class="title ft-inquiry-title-mobile" title="<?php echo e($row['title']); ?>"><?php echo e($row['title']); ?></span>
                                    <span class="sub ft-inquiry-mobile-created">Created by <?php echo e($row['createdBy']); ?> · <?php echo e($row['createdDate']); ?> · <?php echo e($row['createdTime']); ?></span>
                                </div>
                                <div class="ft-inquiry-mobile-separator ft-inquiry-mobile-separator-before-task" aria-hidden="true"></div>
                                <div class="cell ft-inquiry-list-client-cell" data-label="Client / Item">
                                    <span class="ft-client-name-with-logo"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['name' => $row['client'],'src' => $row['clientLogoUrl'] ?? null,'size' => 24]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['client']),'src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['clientLogoUrl'] ?? null),'size' => 24]); ?>
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
<?php endif; ?><span class="title"><?php echo e($row['client']); ?></span></span>
                                    <span class="sub">Contact: <?php echo e($row['clientContact'] ?: '—'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['item']): ?><span class="sub"><?php echo e($row['item']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php
                                    $rowTaskStatusColor = $masterData->displayColorFor('inquiry_task_status', $row['taskStatus']);
                                    $rowTaskFlagTone = match ($row['flag']) {
                                        'Requires attention', 'Overdue' => 'red',
                                        'Due Today' => 'amber',
                                        'No flag' => 'green',
                                        default => 'blue',
                                    };
                                    $rowInquiryPriorityColor = $masterData->displayColorFor('priority', $row['priority']);
                                    $rowInquiryStatusColor = $inquiryService->inquiryStatusColor($row['status'], $row['taskStatus']);
                                ?>
                                <div class="cell ft-inquiry-list-priority-cell" data-label="Priority"><span class="pill <?php echo e($rowInquiryPriorityColor ? 'ft-master-color' : $priorityTone($row['priority'])); ?>" style="<?php echo e(\App\Support\MasterColor::style($rowInquiryPriorityColor)); ?>"><?php echo e($row['priority']); ?></span></div>
                                <div class="cell ft-inquiry-list-due-cell" data-label="Due Date"><span class="title"><?php echo e($row['due']); ?></span></div>
                                <div class="cell ft-inquiry-list-status-cell" data-label="Status"><span class="pill <?php echo e($rowInquiryStatusColor ? 'ft-master-color' : $tone($row['status'])); ?>" style="<?php echo e(\App\Support\MasterColor::style($rowInquiryStatusColor)); ?>"><?php echo e($row['status']); ?></span></div>
                                <div class="cell ft-inquiry-list-flag-cell" data-label="Flag">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['flag'] === 'No flag'): ?>
                                        <span class="ft-inquiry-no-flag">No flag</span>
                                    <?php else: ?>
                                        <span class="pill <?php echo e($rowTaskFlagTone); ?>"><?php echo e($row['flag']); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="cell ft-inquiry-list-task-cell" data-label="Current Task"><span class="title"><?php echo e($row['currentTask']); ?></span><span class="sub"><?php echo e($row['taskCaption']); ?></span></div>
                                <div class="cell ft-inquiry-list-assignee-cell" data-label="Assignee">
                                    <div class="ownerline">
                                        <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['class' => 'ft-inquiry-assignee-avatar','name' => $row['assignee'],'src' => $row['assigneeAvatar'] ?? null,'size' => 34]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-inquiry-assignee-avatar','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['assignee']),'src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['assigneeAvatar'] ?? null),'size' => 34]); ?>
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
                                        <span class="title" title="<?php echo e($row['assignee']); ?>"><?php echo e($row['assignee']); ?></span>
                                    </div>
                                </div>
                                <div class="cell ft-inquiry-list-task-status-cell" data-label="Task Status"><span class="pill <?php echo e($rowTaskStatusColor ? 'ft-master-color' : $tone($row['taskStatus'])); ?>" style="<?php echo e(\App\Support\MasterColor::style($rowTaskStatusColor)); ?>"><?php echo e($row['taskStatus']); ?></span></div>
                                <div class="ft-inquiry-mobile-separator ft-inquiry-mobile-separator-after-task" aria-hidden="true"></div>
                                <div class="cell ft-inquiry-list-started-cell" data-label="Started At">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['hasStarted']): ?>
                                        <span class="title"><?php echo e($row['startedDate']); ?></span>
                                        <span class="sub"><?php echo e($row['startedTime']); ?></span>
                                    <?php else: ?>
                                        <span class="title ft-inquiry-not-started">Not Started</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="cell ft-inquiry-list-progress-cell" data-label="Progress">
                                    <div class="ft-inquiry-list-progress">
                                        <div class="ft-inquiry-list-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo e($row['progressPercent']); ?>" aria-label="<?php echo e($row['progress']); ?> of <?php echo e($row['total']); ?> tasks completed"><span style="width:<?php echo e($row['progressPercent']); ?>%"></span></div>
                                        <b><?php echo e($row['progress']); ?>/<?php echo e($row['total']); ?></b>
                                    </div>
                                </div>
                                <div class="cell ft-inquiry-list-updated-cell" data-label="Updated At">
                                    <span class="title"><?php echo e($row['updatedDate']); ?></span>
                                    <span class="sub"><?php echo e($row['updatedTime']); ?></span>
                                </div>
                                <div class="ft-inquiry-mobile-separator ft-inquiry-mobile-separator-before-footer" aria-hidden="true"></div>
                                <div class="cell ft-inquiry-list-view-cell" data-label="View"><a class="openbtn openbtn-link" href="<?php echo e(route('inquiries.index', ['open' => $row['id']])); ?>" aria-label="View details for <?php echo e($row['number']); ?>" wire:navigate><span class="ft-inquiry-view-label-desktop">View</span><span class="ft-inquiry-view-label-mobile">Details</span><span aria-hidden="true">→</span></a></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('inquiries', 'delete')): ?>
                                    <div class="cell ft-inquiry-list-actions-cell" data-label="Actions" x-data="{ open: false }">
                                        <button
                                            class="ft-inquiry-row-action-trigger"
                                            type="button"
                                            :aria-expanded="open ? 'true' : 'false'"
                                            aria-haspopup="menu"
                                            aria-controls="inquiry-actions-<?php echo e($row['id']); ?>"
                                            aria-label="Actions for <?php echo e($row['number']); ?>"
                                            x-on:click.stop="
                                                const menu = $refs.menu;
                                                if (menu.matches(':popover-open')) { menu.hidePopover(); return; }
                                                const rect = $el.getBoundingClientRect();
                                                const menuWidth = 166;
                                                const menuHeight = 46;
                                                const edge = 10;
                                                const gap = 6;
                                                const left = Math.min(window.innerWidth - menuWidth - edge, Math.max(edge, rect.right - menuWidth));
                                                const openAbove = (window.innerHeight - rect.bottom) < (menuHeight + gap + edge) && rect.top > (menuHeight + gap + edge);
                                                const top = openAbove ? rect.top - menuHeight - gap : rect.bottom + gap;
                                                menu.style.left = `${left}px`;
                                                menu.style.top = `${Math.max(edge, top)}px`;
                                                menu.showPopover();
                                            "
                                        >⋮</button>
                                        <div
                                            id="inquiry-actions-<?php echo e($row['id']); ?>"
                                            class="ft-inquiry-row-action-menu"
                                            x-ref="menu"
                                            popover="auto"
                                            role="menu"
                                            x-on:toggle="open = $event.newState === 'open'"
                                        >
                                            <button type="button" role="menuitem" wire:click="deleteInquiry(<?php echo e($row['id']); ?>)" wire:confirm="Delete <?php echo e($row['number']); ?>? This removes the inquiry from active lists. Any converted order remains available.">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                                                <span>Delete inquiry</span>
                                            </button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="cell ft-inquiry-list-actions-cell" aria-hidden="true"></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
            $selectedWorkflow = collect($workflowFilterOptions)->first(fn ($item) => (int) ($item['id'] ?? 0) === (int) $createWorkflowId);
            $selectedWorkflowName = (string) ($selectedWorkflow['label'] ?? $selectedWorkflowLabel ?: 'Select workflow');
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
                                <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-owner-selector','label' => 'Assigned to','property' => 'createOwnerId','type' => 'users','context' => 'create-inquiry','action' => 'setCreateSelector','value' => $createOwnerId,'placeholder' => 'Search or select assignee...','selectedLabel' => $selectedOwnerLabel ?: null,'initialOptions' => $ownerFilterOptions,'clearable' => false,'wire:key' => 'inquiry-create-owner-selector-'.e($createOwnerId ?: 'none').'-'.e(substr(md5($selectedOwnerLabel ?: 'none'), 0, 8)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-create-remote-select inquiry-create-remote ft-inquiry-owner-selector','label' => 'Assigned to','property' => 'createOwnerId','type' => 'users','context' => 'create-inquiry','action' => 'setCreateSelector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($createOwnerId),'placeholder' => 'Search or select assignee...','selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOwnerLabel ?: null),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ownerFilterOptions),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'wire:key' => 'inquiry-create-owner-selector-'.e($createOwnerId ?: 'none').'-'.e(substr(md5($selectedOwnerLabel ?: 'none'), 0, 8)).'']); ?>
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
                            <label>Priority *</label>
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
            $inquiryStartAt = $inquiry->started_at ?: $firstStartedTask?->started_at;
            $inquiryStartLocal = \App\Support\UserLocalTime::localize($inquiryStartAt);
            $inquiryCompletedAt = $inquiry->completed_at ?: ($readyForDecision ? $lastCompletedTask?->completed_at : null);
            $detailStatus = match (true) {
                $inquiry->result === 'converted' => 'Converted',
                $inquiry->result === 'dead' => 'Closed',
                (string) $inquiry->status === 'Draft' => 'Draft',
                default => (string) ($inquiry->status ?: \App\Services\InquiryService::AUTO_READY_STATUS),
            };
            $detailStatusColor = $inquiryService->inquiryStatusColor($detailStatus, (string) ($currentTask?->status ?: ''));
            $detailPriorityColor = $masterData->displayColorFor('priority', (string) $inquiry->priority);
            $headerFlagTask = $currentTask?->needs_attention ? $currentTask : $inquiry->tasks->first(fn ($task) => (bool) $task->needs_attention);
            $headerFlagLabel = $inquiry->needs_attention
                ? 'Requires attention'
                : ($headerFlagTask ? 'Requires attention' : '');
            $headerFlagReason = $inquiry->needs_attention
                ? (string) ($inquiry->attention_reason ?? '')
                : (string) ($headerFlagTask?->attention_reason ?? '');
        ?>
        <section class="view inquiry-detail-view ft-detail-products-scope" x-data="{
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
                            <button type="button" class="ft-copy-id-btn" title="Copy Inquiry ID" aria-label="Copy <?php echo e($inquiry->inquiry_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($inquiry->inquiry_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
                        </span>
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
                                <button type="button" class="ft-copy-id-btn ft-inquiry-header-copy" title="Copy Reference Number" aria-label="Copy reference number <?php echo e($inquiry->reference_number); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($inquiry->reference_number)->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
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

            <div class="tabs">
                <button class="tab active" type="button">Overview</button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab === 'overview'): ?>
                <div class="tabpane ft-task-detail-page ft-exact-task-detail ft-inquiry-task-overview-exact">
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
                            x-data="{ ...window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-priority')->toHtml() ?>, label: 'Inquiry priority', value: <?php echo \Illuminate\Support\Js::from($inquiry->priority)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->priority)->toHtml() ?> }), priorityColor: <?php echo \Illuminate\Support\Js::from($detailPriorityColor)->toHtml() ?> }"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                        >
                            <small>Priority</small>
                            <div x-show="!editing" class="ft-task-property-display"><span class="status-dot ft-master-color-dot" style="<?php echo e(\App\Support\MasterColor::style($detailPriorityColor)); ?>" x-bind:style="priorityColor ? '--ft-master-color:'+priorityColor : ''"></span><b class="ft-property-value" x-text="display"><?php echo e($inquiry->priority); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?><button type="button" :disabled="status === 'saving'" title="Edit priority" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryPriority?.showPicker ? $refs.inquiryPriority.showPicker() : $refs.inquiryPriority?.focus())">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                <div x-cloak x-show="editing" class="ft-task-property-inline-editor"><select data-master-color-select x-ref="inquiryPriority" x-model="draftValue" class="ft-task-property-inline-input ft-master-color" style="<?php echo e(\App\Support\MasterColor::style($detailPriorityColor)); ?>" x-on:keydown.escape.prevent="cancelEdit()" x-on:change="const nextColor=String($event.target.selectedOptions[0]?.dataset?.color || ''); window.FlowTrackMasterColor?.applySelect($event.target); commit($event.target.value, selectedLabel($event), () => $wire.updateInquiryField('priority', draftValue)).then(ok => { if(ok) priorityColor=nextColor; });"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($inquiryPriorities->contains(fn($priority) => (string) $priority->name === (string) $inquiry->priority))): ?><option value="<?php echo e($inquiry->priority); ?>" data-color="<?php echo e($masterData->displayColorFor('priority', (string) $inquiry->priority)); ?>"><?php echo e($inquiry->priority); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($priority->name); ?>" data-color="<?php echo e($masterData->displayColorFor('priority', $priority->name)); ?>"><?php echo e($priority->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
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
                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-assignee')->toHtml() ?>, label: 'Inquiry assignee', value: <?php echo \Illuminate\Support\Js::from($inquiry->owner_id ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiry->owner?->name ?? 'Unassigned')->toHtml() ?>, avatarUrl: <?php echo \Illuminate\Support\Js::from($inquiry->owner?->profileImageUrl() ?? '')->toHtml() ?> })"
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && $canAssignInquiry && !$inquiry->result): ?><button type="button" :disabled="status === 'saving'" title="Edit assignee" aria-label="Edit Inquiry assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

                        <div
                            class="ft-task-property ft-inline-edit-shell"
                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-'.$inquiry->id.'-start-at')->toHtml() ?>, label: 'Inquiry start date', value: <?php echo \Illuminate\Support\Js::from($inquiryStartLocal?->format('Y-m-d\TH:i') ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—')->toHtml() ?> })"
                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            x-on:click.outside="if (editing) cancelEdit()"
                            x-on:flowtrack-inquiry-started.window="const v=String($event.detail?.value ?? ''); const d=String($event.detail?.display ?? '—'); serverValue=v; value=v; savedValue=v; draftValue=v; display=d; savedDisplay=d;"
                        >
                            <small>Start date</small>
                            <div x-show="!editing" class="ft-task-property-display">
                                <span class="ft-calendar-glyph">▣</span>
                                <b class="ft-property-value" x-text="display"><?php echo e($inquiryStartLocal?->format('M j, Y · g:i A') ?? '—'); ?></b>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiry && !$inquiry->result): ?>
                                    <button type="button" :disabled="status === 'saving'" title="Edit start date and time" aria-label="Edit Inquiry start date and time" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryStartAt?.showPicker ? $refs.inquiryStartAt.showPicker() : $refs.inquiryStartAt?.focus())">✎</button>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewInquiryProducts): ?>
                    <?php
                        // Only persisted products belong in the details table. The shared
                        // Add Product panel owns the temporary selection state, exactly as
                        // it does on Order Details, so unfinished legacy draft rows stay out.
                        $inquiryItemRows = collect($inquiry->items ?? collect())
                            ->filter(fn ($item) => filled($item->item_name))
                            ->values();
                        $inquiryItemCount = $inquiryItemRows->count();
                        $inquiryItemUnits = (float) $inquiryItemRows->sum('quantity');
                    ?>
                    <?php if (isset($component)) { $__componentOriginalba811f0c8eda75848d52d470099ca258 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalba811f0c8eda75848d52d470099ca258 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-products-card','data' => ['id' => 'inquiry-products-card','variant' => 'inquiry','count' => $inquiryItemCount,'totalUnits' => $inquiryItemUnits]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-products-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'inquiry-products-card','variant' => 'inquiry','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryItemCount),'total-units' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryItemUnits)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryItemRows->isEmpty()): ?>
                            <tr class="ft-order-product-empty-row"><td colspan="7">No products have been added to this Inquiry yet.</td></tr>
                        <?php else: ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $inquiryItemRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $isDraftInquiryItem = blank($item->item_name);
                                    $categoryNeedsSelection = filled($item->id) && blank($item->category);
                                    $productNeedsSelection = filled($item->id) && filled($item->category) && blank($item->item_name);
                                    $categoryLabel = $item->category ?: 'Select category';
                                    $productLabel = $item->item_name ?: (blank($item->category) ? 'Select category first' : 'Select product');
                                    $productPickerKey = 'inquiry-item-'.$item->id.'-product-'.md5((string) ($item->category ?? '').'|'.(string) ($item->item_name ?? ''));
                                    $productMaster = $inquiryProductMasters->get(mb_strtolower(trim((string) ($item->item_name ?? ''))));
                                    $productImageUrl = $productMaster?->productImageUrl();
                                    $productCode = $productMaster?->productDisplayCode();
                                    $productReference = $productMaster?->productReferenceCode();
                                    $classificationParts = collect([
                                        $productMaster?->productMainCategory(),
                                        ...array_filter(array_map('trim', preg_split('/\s*>\s*/', (string) ($productMaster?->productClassificationPath() ?? '')) ?: [])),
                                    ])->filter()->unique()->values();
                                    if ($classificationParts->isEmpty() && filled($item->category)) $classificationParts = collect([$item->category]);
                                    $categoryDisplay = $classificationParts->implode(' › ') ?: $categoryLabel;
                                    $unitPrice = $item->unit_price !== null ? (float) $item->unit_price : null;
                                    $unitPriceValue = $unitPrice !== null ? number_format($unitPrice, 2, '.', '') : '';
                                    $unitPriceDisplay = $unitPrice !== null ? $inquiryCurrencySymbol.number_format($unitPrice, 2) : '—';
                                    $updatedDate = $item->updated_at ? \App\Support\UserLocalTime::format($item->updated_at, 'M j, Y') : '—';
                                    $updatedTime = $item->updated_at ? \App\Support\UserLocalTime::format($item->updated_at, 'g:i A') : null;
                                ?>
                                <tr
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-product-detail-'.e($item->id).''; ?>wire:key="inquiry-product-detail-<?php echo e($item->id); ?>"
                                    x-data="{ categorySaving: false, productSaving: false, quantitySaving: false, priceSaving: false, notesSaving: false, actionOpen: false, draftProductReady: <?php echo \Illuminate\Support\Js::from(filled($item->item_name))->toHtml() ?> }"
                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-order-product-draft-row' => $isDraftInquiryItem]); ?>"
                                >
                                    <td data-label="Product">
                                        <?php if (isset($component)) { $__componentOriginale5d0c9e6668574836a4427e7246d2066 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5d0c9e6668574836a4427e7246d2066 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-identity','data' => ['imageUrl' => $productImageUrl,'alt' => $item->item_name ?? '','code' => $productCode,'reference' => $productReference,'fallbackMeta' => 'Inquiry product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-identity'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['image-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productImageUrl),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->item_name ?? ''),'code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productCode),'reference' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productReference),'fallback-meta' => 'Inquiry product']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            <div
                                                class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor ft-order-product-name-editor"
                                                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = ''.e($productPickerKey).''; ?>wire:key="<?php echo e($productPickerKey); ?>"
                                                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-item-'.$item->id.'-product')->toHtml() ?>, label: 'product', value: <?php echo \Illuminate\Support\Js::from($item->item_name ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($productLabel)->toHtml() ?> })"
                                                x-init="if (<?php echo \Illuminate\Support\Js::from($canEditInquiryProducts && $productNeedsSelection)->toHtml() ?>) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                                x-on:click.outside="if (editing && !<?php echo \Illuminate\Support\Js::from($productNeedsSelection)->toHtml() ?>) cancelEdit()"
                                                x-on:ft-inline-remote-cancel.stop="if (!<?php echo \Illuminate\Support\Js::from($productNeedsSelection)->toHtml() ?>) cancelEdit()"
                                                x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select product'); productSaving = true; commit(nextValue, nextLabel, () => $wire.updateInquiryItem(<?php echo e($item->id); ?>, 'item_name', nextValue)).then(async (ok) => { productSaving = false; if (ok) { draftProductReady = true; await $wire.$refresh(); } })"
                                            >
                                                <span class="ft-order-product-name" x-show="!editing" x-text="display"><?php echo e($productLabel); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiryProducts): ?>
                                                    <button x-show="!editing" :disabled="status === 'saving' || categorySaving || quantitySaving || priceSaving || notesSaving || <?php echo \Illuminate\Support\Js::from(blank($item->category))->toHtml() ?>" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="<?php echo e(blank($item->category) ? 'Select a category first' : 'Edit product'); ?>" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                                    <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                                        <?php if (isset($component)) { $__componentOriginalbe44f191c92266098874e73cf7cdcd43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe44f191c92266098874e73cf7cdcd43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-catalog','data' => ['type' => 'products','context' => 'inquiry-detail','value' => $item->item_name ?? '','selectedLabel' => $productLabel,'placeholder' => blank($item->category) ? 'Select category first' : 'Select product','searchLabel' => 'product','params' => ['category' => (string) ($item->category ?? '')],'disabled' => blank($item->category),'menuWidth' => 360,'fixedMenu' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-catalog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'products','context' => 'inquiry-detail','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->item_name ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productLabel),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(blank($item->category) ? 'Select category first' : 'Select product'),'search-label' => 'product','params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['category' => (string) ($item->category ?? '')]),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(blank($item->category)),'menu-width' => 360,'fixed-menu' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $attributes = $__attributesOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $component = $__componentOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__componentOriginalbe44f191c92266098874e73cf7cdcd43); ?>
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
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5d0c9e6668574836a4427e7246d2066)): ?>
<?php $attributes = $__attributesOriginale5d0c9e6668574836a4427e7246d2066; ?>
<?php unset($__attributesOriginale5d0c9e6668574836a4427e7246d2066); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5d0c9e6668574836a4427e7246d2066)): ?>
<?php $component = $__componentOriginale5d0c9e6668574836a4427e7246d2066; ?>
<?php unset($__componentOriginale5d0c9e6668574836a4427e7246d2066); ?>
<?php endif; ?>
                                    </td>
                                    <td data-label="Category">
                                        <div
                                            class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor ft-order-product-category-editor"
                                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-item-'.e($item->id).'-category-'.e(md5((string) ($item->category ?? ''))).''; ?>wire:key="inquiry-item-<?php echo e($item->id); ?>-category-<?php echo e(md5((string) ($item->category ?? ''))); ?>"
                                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-item-'.$item->id.'-category')->toHtml() ?>, label: 'product category', value: <?php echo \Illuminate\Support\Js::from($item->category ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($categoryDisplay)->toHtml() ?> })"
                                            x-init="if (<?php echo \Illuminate\Support\Js::from($canEditInquiryProducts && $categoryNeedsSelection)->toHtml() ?>) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                            x-on:click.outside="if (editing && !<?php echo \Illuminate\Support\Js::from($categoryNeedsSelection)->toHtml() ?>) cancelEdit()"
                                            x-on:ft-inline-remote-cancel.stop="if (!<?php echo \Illuminate\Support\Js::from($categoryNeedsSelection)->toHtml() ?>) cancelEdit()"
                                            x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select category'); const changed = nextValue !== savedValue; categorySaving = true; commit(nextValue, nextLabel, () => $wire.updateInquiryItem(<?php echo e($item->id); ?>, 'category', nextValue)).then(async (ok) => { if (ok && changed) await $wire.$refresh(); categorySaving = false })"
                                        >
                                            <span class="ft-order-product-category-path" x-show="!editing" x-text="display"><?php echo e($categoryDisplay); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiryProducts): ?>
                                                <button x-show="!editing" :disabled="status === 'saving' || productSaving || quantitySaving || priceSaving || notesSaving" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                                <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                                    <?php if (isset($component)) { $__componentOriginalbe44f191c92266098874e73cf7cdcd43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe44f191c92266098874e73cf7cdcd43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-remote-catalog','data' => ['type' => 'product-categories','context' => 'inquiry-detail','value' => $item->category ?? '','selectedLabel' => $categoryLabel,'placeholder' => 'Select category','searchLabel' => 'product category','menuWidth' => 340,'fixedMenu' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-remote-catalog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'product-categories','context' => 'inquiry-detail','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->category ?? ''),'selected-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryLabel),'placeholder' => 'Select category','search-label' => 'product category','menu-width' => 340,'fixed-menu' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $attributes = $__attributesOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__attributesOriginalbe44f191c92266098874e73cf7cdcd43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe44f191c92266098874e73cf7cdcd43)): ?>
<?php $component = $__componentOriginalbe44f191c92266098874e73cf7cdcd43; ?>
<?php unset($__componentOriginalbe44f191c92266098874e73cf7cdcd43); ?>
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
                                    </td>
                                    <td class="ft-order-product-quantity" data-label="Quantity">
                                        <div
                                            class="ft-inline-field-editor ft-inline-edit-shell"
                                            x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-item-'.$item->id.'-quantity')->toHtml() ?>, label: 'quantity', value: <?php echo \Illuminate\Support\Js::from((string) max(1, (int) $item->quantity))->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from(number_format((int) max(1, (int) $item->quantity)).' units')->toHtml() ?> })"
                                            x-init="if (<?php echo \Illuminate\Support\Js::from($canEditInquiryProducts && $isDraftInquiryItem)->toHtml() ?>) editing = true"
                                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        >
                                            <span x-show="!editing" class="ft-order-product-edit-value" x-text="display"><?php echo e(number_format((int) max(1, (int) $item->quantity))); ?> units</span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiryProducts): ?>
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || priceSaving || notesSaving" type="button" class="ft-inline-edit-button" title="Edit quantity" aria-label="Edit product quantity" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.quantityInput.focus(); $refs.quantityInput.select(); })">✎</button>
                                                <input x-ref="quantityInput" data-inquiry-item-quantity x-cloak x-show="editing" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-number-input" type="number" min="1" max="999999999" step="1" :disabled="categorySaving || productSaving"
                                                    x-on:keydown.escape.prevent="cancelEdit()"
                                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                                    x-on:blur="if (editing && !categorySaving && !productSaving && !quantitySaving) { const next = positiveInteger(draftValue); quantitySaving = true; commit(next, Number(next).toLocaleString() + ' units', () => $wire.updateInquiryItem(<?php echo e($item->id); ?>, 'quantity', next)).then(async (ok) => { quantitySaving = false; if (ok && <?php echo \Illuminate\Support\Js::from($isDraftInquiryItem)->toHtml() ?>) await $wire.$refresh(); else if (!ok) editing = true; }) }"
                                                >
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
                                    </td>
                                    <td class="ft-order-product-price" data-label="Unit price">
                                        <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-item-'.$item->id.'-unit-price')->toHtml() ?>, label: 'unit price', value: <?php echo \Illuminate\Support\Js::from($unitPriceValue)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($unitPriceDisplay)->toHtml() ?> })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                            <span x-show="!editing" class="ft-order-product-edit-value" x-text="display"><?php echo e($unitPriceDisplay); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiryProducts): ?>
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || quantitySaving || notesSaving" type="button" class="ft-inline-edit-button" title="Edit unit price" aria-label="Edit unit price" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.priceInput.focus(); $refs.priceInput.select(); })">✎</button>
                                                <div x-cloak x-show="editing" class="ft-order-product-price-input-wrap">
                                                    <span><?php echo e($inquiryCurrencySymbol); ?></span>
                                                    <input x-ref="priceInput" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-number-input" type="number" min="0" step="0.01"
                                                        x-on:keydown.escape.prevent="cancelEdit()"
                                                        x-on:keydown.enter.prevent="$event.target.blur()"
                                                        x-on:blur="if (editing && !priceSaving) { const raw = String(draftValue ?? '').trim(); const parsed = raw === '' ? '' : Number(raw); const next = raw === '' ? '' : (Number.isFinite(parsed) ? Math.max(0, parsed).toFixed(2) : ''); priceSaving = true; commit(next, next === '' ? '—' : <?php echo \Illuminate\Support\Js::from($inquiryCurrencySymbol)->toHtml() ?> + Number(next).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}), () => $wire.updateInquiryItem(<?php echo e($item->id); ?>, 'unit_price', next)).then((ok) => { priceSaving = false; if (!ok) editing = true; }) }"
                                                    >
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
                                    </td>
                                    <td class="ft-order-product-notes" data-label="Notes">
                                        <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('inquiry-item-'.$item->id.'-notes')->toHtml() ?>, label: 'product notes', value: <?php echo \Illuminate\Support\Js::from($item->notes ?? '')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($item->notes ?: 'Add notes')->toHtml() ?> })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                            <span x-show="!editing" class="ft-order-product-note-value" :class="{ 'is-empty': !value }" x-text="display"><?php echo e($item->notes ?: 'Add notes'); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditInquiryProducts): ?>
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || quantitySaving || priceSaving" type="button" class="ft-inline-edit-button" title="Edit notes" aria-label="Edit product notes" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.notesInput.focus(); $refs.notesInput.select(); })">✎</button>
                                                <input x-ref="notesInput" x-cloak x-show="editing" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-notes-input" type="text" maxlength="2000" placeholder="Product notes"
                                                    x-on:keydown.escape.prevent="cancelEdit()"
                                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                                    x-on:blur="if (editing && !notesSaving) { const next = String(draftValue || '').trim(); notesSaving = true; commit(next, next || 'Add notes', () => $wire.updateInquiryItem(<?php echo e($item->id); ?>, 'notes', next)).then((ok) => { notesSaving = false; if (!ok) editing = true; }) }"
                                                >
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
                                    </td>
                                    <?php if (isset($component)) { $__componentOriginalf8e22e549d64313bce97b5ba6b14d89a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-updated','data' => ['primary' => $updatedDate,'secondary' => $updatedTime]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-updated'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['primary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($updatedDate),'secondary' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($updatedTime)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a)): ?>
<?php $attributes = $__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a; ?>
<?php unset($__attributesOriginalf8e22e549d64313bce97b5ba6b14d89a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8e22e549d64313bce97b5ba6b14d89a)): ?>
<?php $component = $__componentOriginalf8e22e549d64313bce97b5ba6b14d89a; ?>
<?php unset($__componentOriginalf8e22e549d64313bce97b5ba6b14d89a); ?>
<?php endif; ?>
                                    <td class="ft-order-product-actions-cell" data-label="Actions">
                                        <?php if (isset($component)) { $__componentOriginal769c4590c1dc590e97b31bc706ef7701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal769c4590c1dc590e97b31bc706ef7701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-product-actions','data' => ['itemId' => $item->id,'canDelete' => $canDeleteInquiryProducts,'removeMethod' => 'removeInquiryItem','confirmText' => 'Remove this product from the Inquiry?']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-product-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['item-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->id),'can-delete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canDeleteInquiryProducts),'remove-method' => 'removeInquiryItem','confirm-text' => 'Remove this product from the Inquiry?']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal769c4590c1dc590e97b31bc706ef7701)): ?>
<?php $attributes = $__attributesOriginal769c4590c1dc590e97b31bc706ef7701; ?>
<?php unset($__attributesOriginal769c4590c1dc590e97b31bc706ef7701); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal769c4590c1dc590e97b31bc706ef7701)): ?>
<?php $component = $__componentOriginal769c4590c1dc590e97b31bc706ef7701; ?>
<?php unset($__componentOriginal769c4590c1dc590e97b31bc706ef7701); ?>
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                         <?php $__env->slot('afterTable', null, []); ?> 
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddInquiryProductForm && $canCreateInquiryProducts): ?>
                                <?php if (isset($component)) { $__componentOriginal5e4da558653258c1bfe993ad392b6247 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e4da558653258c1bfe993ad392b6247 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catalog.detail-add-product','data' => ['wireKey' => 'inquiry-detail-add-product-'.$inquiry->id,'searchModel' => 'inquiryProductSearch','searchValue' => $inquiryProductSearch,'searchResults' => $inquiryProductSearchResults,'resultTotal' => $inquiryProductResultTotal,'showAllMethod' => 'showAllInquiryProductResults','selectMethod' => 'selectInquiryProduct','selectedProduct' => $inquiryProductSelectedProduct,'categoryValue' => $inquiryProductCategory,'quantityModel' => 'inquiryProductQuantity','unitPriceModel' => 'inquiryProductUnitPrice','currencySymbol' => $inquiryCurrencySymbol,'closeMethod' => 'closeAddInquiryProductForm','saveMethod' => 'saveInquiryProduct','selectedErrorKey' => 'inquiryProductSelectedId','quantityErrorKey' => 'inquiryProductQuantity','unitPriceErrorKey' => 'inquiryProductUnitPrice']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catalog.detail-add-product'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('inquiry-detail-add-product-'.$inquiry->id),'search-model' => 'inquiryProductSearch','search-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSearch),'search-results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSearchResults),'result-total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductResultTotal),'show-all-method' => 'showAllInquiryProductResults','select-method' => 'selectInquiryProduct','selected-product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductSelectedProduct),'category-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryProductCategory),'quantity-model' => 'inquiryProductQuantity','unit-price-model' => 'inquiryProductUnitPrice','currency-symbol' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inquiryCurrencySymbol),'close-method' => 'closeAddInquiryProductForm','save-method' => 'saveInquiryProduct','selected-error-key' => 'inquiryProductSelectedId','quantity-error-key' => 'inquiryProductQuantity','unit-price-error-key' => 'inquiryProductUnitPrice']); ?>
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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                         <?php $__env->endSlot(); ?>

                         <?php $__env->slot('footer', null, []); ?> 
                            <span>Product and quantity changes are recorded in inquiry activity.</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateInquiryProducts && !$showAddInquiryProductForm): ?>
                                <button type="button" class="ft-outline-btn ft-order-product-add-another" wire:click="openAddInquiryProductForm" wire:loading.attr="disabled" wire:target="openAddInquiryProductForm">＋ Add another product</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalba811f0c8eda75848d52d470099ca258)): ?>
<?php $attributes = $__attributesOriginalba811f0c8eda75848d52d470099ca258; ?>
<?php unset($__attributesOriginalba811f0c8eda75848d52d470099ca258); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalba811f0c8eda75848d52d470099ca258)): ?>
<?php $component = $__componentOriginalba811f0c8eda75848d52d470099ca258; ?>
<?php unset($__componentOriginalba811f0c8eda75848d52d470099ca258); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div id="tab-workflow" class="ft-inquiry-overview-taskflow ft-inquiry-workflow-pane">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('tasks', 'view')): ?>
                            <?php echo $__env->make('livewire.inquiries._taskflow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <section class="panel"><div class="ft-inquiry-empty-workflow">Task access is not enabled for your role.</div></section>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents', 'view')): ?><?php echo $__env->make('livewire.inquiries._attachments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php echo $__env->make('livewire.inquiries._activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTaskDocumentModal && $taskDocumentModalTask): ?>
                <?php
                    $completeAfterTaskDocument = (int) ($pendingCompletionTaskId ?? 0) === (int) $taskDocumentModalTask->id;
                ?>
                <div class="ft-inquiry-task-document-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-task-document-modal'; ?>wire:key="inquiry-task-document-modal" wire:click.self="closeTaskDocumentModal">
                    <section class="ft-inquiry-task-document-modal" role="dialog" aria-modal="true" aria-labelledby="task-document-modal-title">
                        <header class="ft-inquiry-task-document-modal-head">
                            <div>
                                <h2 id="task-document-modal-title"><?php echo e($completeAfterTaskDocument ? 'Required file needed to complete task' : 'Add new document to task'); ?></h2>
                                <p><?php echo e($completeAfterTaskDocument ? 'Add the required file now. The task will be completed automatically after the document is saved.' : 'Upload a new file or choose a document that already exists.'); ?></p>
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
                            <div class="ft-inquiry-task-document-source-tabs">
                                <button type="button" class="<?php echo e($taskDocumentSource === 'upload' ? 'active' : ''); ?>" wire:click="setTaskDocumentSource('upload')" <?php if(!$canCreateDocuments): echo 'disabled'; endif; ?>>
                                    <span>↥</span> Upload new
                                </button>
                                <button type="button" class="<?php echo e($taskDocumentSource === 'existing' ? 'active' : ''); ?>" wire:click="setTaskDocumentSource('existing')" <?php if(!$canLinkDocuments): echo 'disabled'; endif; ?>>
                                    <span>▤</span> Choose existing
                                </button>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskDocumentSource === 'upload' && $canCreateDocuments): ?>
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
                                <p>
                                    This document will appear directly under <strong><?php echo e($taskDocumentModalTask->title); ?></strong>.
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($completeAfterTaskDocument): ?> Saving it will also mark the task as Completed. <?php elseif($taskDocumentModalTask->completed_at): ?> Adding a document will not reopen or change the completed task. <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <footer class="ft-inquiry-task-document-modal-actions">
                            <button type="button" class="secondary" wire:click="closeTaskDocumentModal">Cancel</button>
                            <button type="button" class="primary" wire:click="saveTaskDocument" wire:loading.attr="disabled" wire:target="saveTaskDocument,taskDocumentUpload"
                                <?php if($taskDocumentSource === 'upload' ? !$taskDocumentUpload : !$taskExistingDocumentId): echo 'disabled'; endif; ?>>
                                <span wire:loading.remove wire:target="saveTaskDocument"><?php echo e($completeAfterTaskDocument ? 'Add file & complete' : 'Add document'); ?></span>
                                <span wire:loading wire:target="saveTaskDocument"><?php echo e($completeAfterTaskDocument ? 'Adding & completing...' : 'Adding...'); ?></span>
                            </button>
                        </footer>
                    </section>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showInquiryAttentionModal): ?>
                <div class="ft-inquiry-attention-modal-backdrop" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'inquiry-attention-modal'; ?>wire:key="inquiry-attention-modal" wire:click.self="closeInquiryAttentionReason">
                    <section class="ft-inquiry-attention-modal" role="dialog" aria-modal="true" aria-labelledby="inquiry-attention-modal-title">
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
                    <section class="ft-inquiry-attention-modal" role="dialog" aria-modal="true" aria-labelledby="task-attention-modal-title">
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/inquiries/index.blade.php ENDPATH**/ ?>