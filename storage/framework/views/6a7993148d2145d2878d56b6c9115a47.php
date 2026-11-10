<?php if (isset($component)) { $__componentOriginalcfea599f97a0d6266449c21c198d875e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcfea599f97a0d6266449c21c198d875e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.management-theme','data' => ['class' => 'ft-mgmt-dashboard','dataDashboardProgressive' => '1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.management-theme'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-dashboard','data-dashboard-progressive' => '1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="ft-mgmt-page-head">
        <div>
            <h1>Management Dashboard</h1>
            <p>Live operational overview across inquiries, orders, tasks, clients and product data.</p>
        </div>
        <div class="ft-mgmt-head-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateOrder): ?><a class="ft-mgmt-btn primary" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate>＋ Create Order</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateInquiry): ?><a class="ft-mgmt-btn" href="<?php echo e(route('inquiries.index', ['create' => 1])); ?>" wire:navigate>＋ Create Inquiry</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canCreateClient): ?><a class="ft-mgmt-btn" href="<?php echo e(route('clients.index', ['create' => 1])); ?>" wire:navigate>＋ Add Client</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <section class="ft-mgmt-control-bar" aria-label="Dashboard filters">
        <div class="ft-mgmt-range" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-range-control'; ?>wire:key="dashboard-range-control" wire:loading.class="is-loading" wire:target="setRange">
            <button type="button" wire:click="setRange(1)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="<?php echo e($rangeDays === 1 ? 'true' : 'false'); ?>" class="<?php echo e($rangeDays === 1 ? 'active' : ''); ?>">Today</button>
            <button type="button" wire:click="setRange(7)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="<?php echo e($rangeDays === 7 ? 'true' : 'false'); ?>" class="<?php echo e($rangeDays === 7 ? 'active' : ''); ?>">7 days</button>
            <button type="button" wire:click="setRange(30)" wire:loading.attr="disabled" wire:target="setRange" aria-pressed="<?php echo e($rangeDays === 30 ? 'true' : 'false'); ?>" class="<?php echo e($rangeDays === 30 ? 'active' : ''); ?>">30 days</button>
        </div>
        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-mgmt-remote-filter ft-mgmt-client-filter','label' => 'Client','property' => 'clientFilter','type' => 'clients','context' => 'dashboard','action' => 'setDashboardFilter','value' => $clientFilter,'placeholder' => 'All clients','initialOptions' => $dashboardClientFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'dashboard-client-filter-'.e($clientFilter ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-remote-filter ft-mgmt-client-filter','label' => 'Client','property' => 'clientFilter','type' => 'clients','context' => 'dashboard','action' => 'setDashboardFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilter),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dashboardClientFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'dashboard-client-filter-'.e($clientFilter ?: 'all').'']); ?>
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
        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-mgmt-remote-filter ft-mgmt-team-filter','label' => 'Team','property' => 'teamFilter','type' => 'departments','context' => 'dashboard','action' => 'setDashboardFilter','value' => $teamFilter,'placeholder' => 'All teams','initialOptions' => $dashboardTeamFilterOptions,'menuWidth' => 300,'fixedMenu' => true,'wire:key' => 'dashboard-team-filter-'.e($teamFilter ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-mgmt-remote-filter ft-mgmt-team-filter','label' => 'Team','property' => 'teamFilter','type' => 'departments','context' => 'dashboard','action' => 'setDashboardFilter','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($teamFilter),'placeholder' => 'All teams','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dashboardTeamFilterOptions),'menu-width' => 300,'fixed-menu' => true,'wire:key' => 'dashboard-team-filter-'.e($teamFilter ?: 'all').'']); ?>
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
        <input class="ft-mgmt-search" wire:model.live.debounce.300ms="search" type="search" placeholder="Search orders, inquiries or tasks" aria-label="Search dashboard">
    </section>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.overview-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'defer' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-progressive-overview';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.priority-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-priority';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <section class="ft-mgmt-grid ft-mgmt-dashboard-pair-grid">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.attention-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-attention';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-2', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.tagged-comments', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-tagged-comments';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-3', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
    </section>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.distribution-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-distribution';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-4', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('reports.view')): ?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.team-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-team';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-5', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-mgmt-grid ft-mgmt-dashboard-pair-grid">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.portfolio-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-portfolio';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-6', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.catalogue-panel', ['lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-catalogue';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-7', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
    </section>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.activity-panel', ['range-days' => $rangeDays,'client-filter' => $clientFilter,'team-filter' => $teamFilter,'search' => $search,'filter-generation' => $filterGeneration,'lazy' => true]);

$__keyOuter = $__key ?? null;

$__key = 'dashboard-activity';
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1633890669-8', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcfea599f97a0d6266449c21c198d875e)): ?>
<?php $attributes = $__attributesOriginalcfea599f97a0d6266449c21c198d875e; ?>
<?php unset($__attributesOriginalcfea599f97a0d6266449c21c198d875e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcfea599f97a0d6266449c21c198d875e)): ?>
<?php $component = $__componentOriginalcfea599f97a0d6266449c21c198d875e; ?>
<?php unset($__componentOriginalcfea599f97a0d6266449c21c198d875e); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/progressive-shell.blade.php ENDPATH**/ ?>