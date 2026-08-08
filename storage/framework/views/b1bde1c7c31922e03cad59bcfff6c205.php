<?php
    $user = auth()->user();
    $unread = (int) ($shellData['unread_notifications'] ?? 0);
    $myWork = (int) ($shellData['open_my_work'] ?? 0);
?>
<aside id="sidebar" class="sidebar">
    <a class="brand ft-system-brand" href="<?php echo e(route('dashboard')); ?>" wire:navigate aria-label="Open Dashboard">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($branding['logo_url'] ?? null): ?>
            <img class="ft-system-logo" src="<?php echo e($branding['logo_url']); ?>" alt="<?php echo e($branding['name'] ?? 'FlowTrack'); ?>">
        <?php else: ?>
            <div class="brand-mark">FT</div><span><?php echo e($branding['name'] ?? 'FlowTrack'); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>
    <div class="sidebar-section">Workspace</div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('dashboard.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
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
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('tasks.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'my-work','label' => 'My Task','badge' => $myWork,'icon' => 'work']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'my-work','label' => 'My Task','badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($myWork),'icon' => 'work']); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('jobs.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'jobs.index','label' => 'Order','icon' => 'jobs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'jobs.index','label' => 'Order','icon' => 'jobs']); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('tasks.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'all-tasks','label' => 'All Task','icon' => 'board']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'all-tasks','label' => 'All Task','icon' => 'board']); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('inquiries.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'inquiries.index','label' => 'Inquiries','icon' => 'inquiries']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'inquiries.index','label' => 'Inquiries','icon' => 'inquiries']); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('clients.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'clients.index','label' => 'Clients','icon' => 'clients']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'clients.index','label' => 'Clients','icon' => 'clients']); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('documents.view')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
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
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    
    <div class="sidebar-section">Administration</div>
    <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
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
<?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('workflow.manage')): ?>
        <?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'workflow.setup','label' => 'Workflow Setup','icon' => 'workflow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'workflow.setup','label' => 'Workflow Setup','icon' => 'workflow']); ?>
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
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->canAccess('master.manage')): ?><?php if (isset($component)) { $__componentOriginal230d78629742508075cd03dd9439398e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal230d78629742508075cd03dd9439398e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.nav-link','data' => ['route' => 'master-data','label' => 'Master Data','icon' => 'master']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'master-data','label' => 'Master Data','icon' => 'master']); ?>
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
    <div class="sidebar-footer">
        <div class="user-mini"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
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
<?php endif; ?><div><div style="color:#fff;font-size:12px;font-weight:650"><?php echo e($user->name); ?></div><div style="font-size:10px;color:#8397ae"><?php echo e($user->role?->name ?? 'User'); ?></div></div></div>
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