<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_','-',app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="flowtrack-session-timeout" content="<?php echo e((int) config('session.lifetime', 30) * 60); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <meta name="flowtrack-session-status-url" content="<?php echo e(route('session.status')); ?>">
        <meta name="flowtrack-logout-url" content="<?php echo e(route('logout')); ?>">
        <meta name="flowtrack-timezone-sync-url" content="<?php echo e(route('session.timezone')); ?>">
        <meta name="flowtrack-display-timezone" content="<?php echo e(app(\App\Services\WorkspaceSettingsService::class)->displayTimezone()); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <title><?php echo e($title ?? 'FlowTrack'); ?> — <?php echo e($branding['name'] ?? config('app.name','FlowTrack')); ?></title>
    <link rel="icon" href="<?php echo e($branding['favicon_url'] ?? asset('favicon.ico')); ?>">
    <link rel="stylesheet" href="/css/flowtrack-inline-editing.css?v=20260807-9">
    <script src="/js/flowtrack-inline-editing.js?v=20260807-3"></script>
    <script src="/js/flowtrack-list-filters.js?v=20260808-2"></script>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <meta name="flowtrack-notification-count-url" content="<?php echo e(route('notifications.unread-count')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->check() && app(\App\Services\PusherChannelService::class)->enabled()): ?>
        <meta name="flowtrack-pusher-key" content="<?php echo e(config('services.pusher.key')); ?>">
        <meta name="flowtrack-pusher-cluster" content="<?php echo e(config('services.pusher.cluster','mt1')); ?>">
        <meta name="flowtrack-pusher-channel" content="private-flowtrack.user.<?php echo e(auth()->id()); ?>">
        <meta name="flowtrack-pusher-auth" content="<?php echo e(route('pusher.auth')); ?>">
        <script src="https://js.pusher.com/8.4.0/pusher.min.js" defer></script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/generated/flowtrack-01.css',
        'resources/css/generated/flowtrack-02.css',
        'resources/css/generated/flowtrack-03.css',
        'resources/css/generated/flowtrack-04.css',
        'resources/js/app.js',
    ]); ?>
    <link rel="stylesheet" href="/css/flowtrack-list-filters.css?v=20260808-3">
    <link rel="stylesheet" href="/css/flowtrack-user-editor.css?v=20260807-2">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('dashboard')): ?><link rel="stylesheet" href="/css/flowtrack-dashboard-prototype.css?v=20260808-1"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body>
<div class="app">
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        <?php echo $__env->make('layouts.partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="content <?php echo e(request()->routeIs('dashboard') ? 'ft-dashboard-content-shell' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success') && !request()->routeIs('task-pack.setup','workflow.setup','master-data','profile')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
    <?php echo $__env->make('layouts.partials.mobile-bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/layouts/app.blade.php ENDPATH**/ ?>