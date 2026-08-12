<?php
    $user = auth()->user();
    $unread = (int) ($shellData['unread_notifications'] ?? 0);
    $myWork = (int) ($shellData['open_my_work'] ?? 0);

    $inquiryCreate = $user->canAccess('inquiries.create');
    $inquiryView = $user->canAccess('inquiries.view');
    $inquiryGroupActive = request()->routeIs('inquiries.*');

    $orderView = $user->canAccess('jobs.view');
    $orderCreate = $user->canAccess('jobs.create');
    $taskView = $user->canAccess('tasks.view');
    $orderGroupActive = request()->routeIs('jobs.*', 'orders.*', 'all-tasks', 'my-work');

    $clientView = $user->canAccess('clients.view');
    $clientCreate = $user->canAccess('clients.create');
    $clientGroupActive = request()->routeIs('clients.*');

    $masterView = $user->canAccess('master.view');
    $masterGroupActive = request()->routeIs('master-data');
    $masterGroup = (string) request()->query('group', 'product');
    $masterLabels = \App\Services\MasterDataService::LABELS;
    if (!array_key_exists($masterGroup, $masterLabels)) $masterGroup = 'product';
    $masterLinks = collect(['product' => $masterLabels['product'], 'product_category' => $masterLabels['product_category']])
        ->merge(collect($masterLabels)->except(['product', 'product_category']))
        ->all();
?>
<aside id="sidebar" class="sidebar ft-sidebar-template">
    <a class="brand ft-system-brand" href="<?php echo e(route('dashboard')); ?>" wire:navigate aria-label="Open Dashboard">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($branding['logo_url'] ?? null): ?>
            <img class="ft-system-logo" src="<?php echo e($branding['logo_url']); ?>" alt="<?php echo e($branding['name'] ?? 'FlowTrack'); ?>">
        <?php else: ?>
            <div class="brand-mark">FT</div><span><?php echo e($branding['name'] ?? 'FlowTrack'); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>

    <nav class="ft-sidebar-nav" aria-label="Primary navigation">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('dashboard.view')): ?>
            <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'dashboard','label' => 'Dashboard','icon' => 'dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'dashboard','label' => 'Dashboard','icon' => 'dashboard']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryView || $inquiryCreate): ?>
            <details class="ft-sidebar-group" <?php if($inquiryGroupActive): ?> open <?php endif; ?>>
                <summary class="ft-sidebar-group-toggle <?php echo e($inquiryGroupActive ? 'is-active' : ''); ?>">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    </span>
                    <span>Inquiry</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryView): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'inquiries.index','label' => 'Inquiries','icon' => 'inquiries','child' => true,'active' => request()->routeIs('inquiries.index') && !request()->boolean('create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'inquiries.index','label' => 'Inquiries','icon' => 'inquiries','child' => true,'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('inquiries.index') && !request()->boolean('create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inquiryCreate): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'inquiries.index','label' => 'Create Inquiry','icon' => 'plus','child' => true,'params' => ['create' => 1],'active' => request()->routeIs('inquiries.index') && request()->boolean('create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'inquiries.index','label' => 'Create Inquiry','icon' => 'plus','child' => true,'params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['create' => 1]),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('inquiries.index') && request()->boolean('create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </details>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderView || $orderCreate || $taskView): ?>
            <details class="ft-sidebar-group" <?php if($orderGroupActive): ?> open <?php endif; ?>>
                <summary class="ft-sidebar-group-toggle <?php echo e($orderGroupActive ? 'is-active' : ''); ?>">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16v13H4z"/><path d="M8 7V4h8v3"/><path d="M4 12h16"/></svg>
                    </span>
                    <span>Order</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderView): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'jobs.index','label' => 'Orders','icon' => 'jobs','child' => true,'active' => request()->routeIs('jobs.index') && !request()->boolean('create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'jobs.index','label' => 'Orders','icon' => 'jobs','child' => true,'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('jobs.index') && !request()->boolean('create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskView): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'all-tasks','label' => 'All Tasks','icon' => 'board','child' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'all-tasks','label' => 'All Tasks','icon' => 'board','child' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'my-work','label' => 'My Tasks','icon' => 'work','badge' => $myWork,'child' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'my-work','label' => 'My Tasks','icon' => 'work','badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($myWork),'child' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderCreate): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'jobs.index','label' => 'Create Order','icon' => 'plus','child' => true,'params' => ['create' => 1],'active' => request()->routeIs('jobs.index') && request()->boolean('create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'jobs.index','label' => 'Create Order','icon' => 'plus','child' => true,'params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['create' => 1]),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('jobs.index') && request()->boolean('create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'orders.bulk-import','label' => 'Create Bulk Order','icon' => 'upload','child' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'orders.bulk-import','label' => 'Create Bulk Order','icon' => 'upload','child' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </details>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientView || $clientCreate): ?>
            <details class="ft-sidebar-group" <?php if($clientGroupActive): ?> open <?php endif; ?>>
                <summary class="ft-sidebar-group-toggle <?php echo e($clientGroupActive ? 'is-active' : ''); ?>">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2"/></svg>
                    </span>
                    <span>Client</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientView): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'clients.index','label' => 'Clients','icon' => 'clients','child' => true,'active' => request()->routeIs('clients.index') && !request()->boolean('create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'clients.index','label' => 'Clients','icon' => 'clients','child' => true,'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('clients.index') && !request()->boolean('create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientCreate): ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'clients.index','label' => 'Add Client','icon' => 'plus','child' => true,'params' => ['create' => 1],'active' => request()->routeIs('clients.index') && request()->boolean('create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'clients.index','label' => 'Add Client','icon' => 'plus','child' => true,'params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['create' => 1]),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('clients.index') && request()->boolean('create'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </details>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('documents.view')): ?>
            <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'documents.index','label' => 'Documents','icon' => 'documents']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'documents.index','label' => 'Documents','icon' => 'documents']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="sidebar-section ft-sidebar-section-line"><span>Administration</span></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('notifications.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'notifications','label' => 'Notifications','badge' => $unread,'icon' => 'notifications']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'notifications','label' => 'Notifications','badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($unread),'icon' => 'notifications']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('taskpacks.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'task-pack.setup','label' => 'Task Pack Setup','icon' => 'settings']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'task-pack.setup','label' => 'Task Pack Setup','icon' => 'settings']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($masterView): ?>
            <details class="ft-sidebar-group" <?php if($masterGroupActive): ?> open <?php endif; ?>>
                <summary class="ft-sidebar-group-toggle <?php echo e($masterGroupActive ? 'is-active' : ''); ?>">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v4H4zM4 11h16v4H4zM4 17h16v3H4z"/></svg>
                    </span>
                    <span>Master Data</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children ft-master-sidebar-children">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $masterLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterKey => $masterLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'master-data','label' => $masterLabel,'icon' => 'dot','child' => true,'params' => ['group' => $masterKey],'active' => $masterGroupActive && $masterGroup === $masterKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'master-data','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($masterLabel),'icon' => 'dot','child' => true,'params' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['group' => $masterKey]),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($masterGroupActive && $masterGroup === $masterKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </details>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app(\App\Services\AccessControlService::class)->isAdministrator($user)): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'administration','label' => 'Roles & Access','icon' => 'settings']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'administration','label' => 'Roles & Access','icon' => 'settings']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $attributes = $__attributesOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__attributesOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal230d78629742508075cd03dd9439398e)): ?>
<?php $component = $__componentOriginal230d78629742508075cd03dd9439398e; ?>
<?php unset($__componentOriginal230d78629742508075cd03dd9439398e); ?>
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-mini">
            <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $user,'name' => $user->name,'dark' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->name),'dark' => true]); ?>
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
            <div class="ft-sidebar-user-copy">
                <div class="ft-sidebar-user-name"><?php echo e($user->name); ?></div>
                <div class="ft-sidebar-user-role"><?php echo e($user->role?->name ?? 'User'); ?></div>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="ft-sidebar-logout-form">
            <?php echo csrf_field(); ?>
            <button type="submit" class="ft-sidebar-logout" aria-label="Log out of FlowTrack">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/></svg>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>