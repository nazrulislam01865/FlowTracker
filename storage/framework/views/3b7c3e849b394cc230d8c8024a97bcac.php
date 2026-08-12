<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_','-',app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="flowtrack-session-timeout" content="<?php echo e((int) config('session.lifetime', 30) * 60); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <meta name="flowtrack-session-status-url" content="<?php echo e(route('session.status')); ?>">
        <meta name="flowtrack-session-recover-url" content="<?php echo e(route('session.recover')); ?>">
        <meta name="flowtrack-logout-url" content="<?php echo e(route('logout')); ?>">
        <meta name="flowtrack-timezone-sync-url" content="<?php echo e(route('session.timezone')); ?>">
        <meta name="flowtrack-display-timezone" content="<?php echo e(app(\App\Services\WorkspaceSettingsService::class)->displayTimezone()); ?>">
        <meta name="flowtrack-rich-text-upload-url" content="<?php echo e(route('rich-text-images.store')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <title><?php echo e(($title ?? null) ? $title.' — ' : ''); ?>STEP PROMO</title>
    <link rel="icon" href="<?php echo e($branding['favicon_url'] ?? asset('images/step-promo/step-promo-icon.webp')); ?>">
    <link rel="stylesheet" href="/css/flowtrack-inline-editing.css?v=20260811-role-matrix-save-ux-1">
    <script src="/js/flowtrack-inline-editing.js?v=20260810-inquiry-start-datetime-1"></script>
    <script src="/js/flowtrack-list-filters.js?v=20260810-client-selection-atomic-3"></script>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <meta name="flowtrack-notification-count-url" content="<?php echo e(route('notifications.unread-count')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->check() && app(\App\Services\ReverbChannelService::class)->enabled()): ?>
        <meta name="flowtrack-reverb-key" content="<?php echo e(data_get(config('reverb'), 'apps.apps.0.key')); ?>">
        <meta name="flowtrack-reverb-host" content="<?php echo e(data_get(config('reverb'), 'apps.apps.0.options.host')); ?>">
        <meta name="flowtrack-reverb-port" content="<?php echo e(data_get(config('reverb'), 'apps.apps.0.options.port', 443)); ?>">
        <meta name="flowtrack-reverb-scheme" content="<?php echo e(data_get(config('reverb'), 'apps.apps.0.options.scheme', 'https')); ?>">
        <meta name="flowtrack-reverb-channel" content="private-flowtrack.user.<?php echo e(auth()->id()); ?>">
        <meta name="flowtrack-reverb-workspace-channel" content="private-flowtrack.workspace.<?php echo e(max(1, (int) config('flowtrack.workspace_id', 1))); ?>">
        <meta name="flowtrack-reverb-auth" content="<?php echo e(route('realtime.auth')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/generated/flowtrack-01.css',
        'resources/css/generated/flowtrack-02.css',
        'resources/css/generated/flowtrack-03.css',
        'resources/css/generated/flowtrack-04.css',
        'resources/js/app.js',
    ]); ?>
    <link rel="stylesheet" href="/css/flowtrack-list-filters.css?v=20260812-taskpack-assignee-1">
    <link rel="stylesheet" href="/css/flowtrack-user-editor.css?v=20260807-2">
    <link rel="stylesheet" href="/css/flowtrack-order-document-upload.css?v=20260810-1">
    <link rel="stylesheet" href="/css/flowtrack-attachment-auto-upload.css?v=20260811-2">
    <link rel="stylesheet" href="/css/flowtrack-client-logo.css?v=20260811-1">
    <link rel="stylesheet" href="/css/flowtrack-client-validation-focus.css?v=20260811-1">
    <link rel="stylesheet" href="/css/flowtrack-sidebar-template.css?v=20260811-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('dashboard')): ?><link rel="stylesheet" href="/css/flowtrack-dashboard-prototype.css?v=20260812-1"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <link rel="stylesheet" href="/css/flowtrack-inquiries.css?v=20260812-20">
    
    <link rel="stylesheet" href="/css/flowtrack-my-work.css?v=20260810-3">
    <link rel="stylesheet" href="/css/flowtrack-master-colors.css?v=20260811-2">
    <link rel="stylesheet" href="/css/flowtrack-master-data.css?v=20260812-4">
    <link rel="stylesheet" href="/css/flowtrack-order-create-products.css?v=20260812-19">
    <link rel="stylesheet" href="/css/flowtrack-order-detail-header.css?v=20260812-1">
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body>
<div class="app">
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        <?php echo $__env->make('layouts.partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="content <?php echo e(request()->routeIs('dashboard') ? 'ft-dashboard-content-shell' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success') && !request()->routeIs('task-pack.setup','master-data','profile','inquiries.*')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
    <?php echo $__env->make('layouts.partials.mobile-bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script src="/js/flowtrack-reverb-client.js?v=20260812-1"></script>
    <script src="/js/flowtrack-workspace-refresh.js?v=20260812-reverb-1"></script>
    <script src="/js/flowtrack-master-colors.js?v=20260811-1"></script>
<script src="/js/flowtrack-attachment-auto-upload.js?v=20260811-2"></script>
<script src="/js/flowtrack-client-validation-focus.js?v=20260811-1"></script>
<script>
    (() => {
        const bindFlowtrackSessionRecovery = () => {
            if (window.__flowtrackSessionRecoveryBound || !window.Livewire?.interceptRequest) return;
            window.__flowtrackSessionRecoveryBound = true;

            Livewire.interceptRequest(({ onError }) => {
                onError(({ response, preventDefault }) => {
                    if (response.status !== 419) return;

                    // Livewire normally displays a Page Expired dialog for 419.
                    // FlowTrack instead performs one deterministic session recovery
                    // navigation, which gives the browser a new CSRF/session pair.
                    preventDefault();
                    const recover = document.querySelector('meta[name="flowtrack-session-recover-url"]')?.content || '/session/recover';
                    window.location.replace(recover);
                });
            });
        };

        if (window.Livewire) bindFlowtrackSessionRecovery();
        else document.addEventListener('livewire:init', bindFlowtrackSessionRecovery, { once: true });
    })();
</script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/layouts/app.blade.php ENDPATH**/ ?>