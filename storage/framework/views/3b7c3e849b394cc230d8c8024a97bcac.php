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
    <link rel="stylesheet" href="/css/flowtrack-inline-editing.css?v=20260815-order-urgency-dropdown-1">
    <script src="/js/flowtrack-inline-editing.js?v=20260817-assignee-immediate-1"></script>
    <script src="/js/flowtrack-list-filters.js?v=20260817-assignee-immediate-1"></script>
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
    <link rel="stylesheet" href="/css/flowtrack-list-filters.css?v=20260818-taskpack-task-prototype-1">
    <link rel="stylesheet" href="/css/flowtrack-user-editor.css?v=20260815-user-assignment-refine-1">
    <link rel="stylesheet" href="/css/flowtrack-order-document-upload.css?v=20260810-1">
    <link rel="stylesheet" href="/css/flowtrack-attachment-auto-upload.css?v=20260811-2">
    <link rel="stylesheet" href="/css/flowtrack-client-logo.css?v=20260811-1">
    <link rel="stylesheet" href="/css/flowtrack-client-validation-focus.css?v=20260817-client-search-select-1">
    <link rel="stylesheet" href="/css/flowtrack-sidebar-template.css?v=20260811-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('dashboard', 'team-performance.report')): ?><link rel="stylesheet" href="/css/flowtrack-dashboard-prototype.css?v=20260812-1"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <link rel="stylesheet" href="/css/flowtrack-inquiry-intelligence.css?v=20260818-searchable-report-filters-1">
    
    <link rel="stylesheet" href="/css/flowtrack-inquiries.css?v=20260818-inquiry-filter-align-1">
    
    <link rel="stylesheet" href="/css/flowtrack-my-work.css?v=20260817-inline-assignee-1">
    <link rel="stylesheet" href="/css/flowtrack-master-colors.css?v=20260818-dashboard-portfolio-statuses-1">
    <link rel="stylesheet" href="/css/flowtrack-master-data.css?v=20260818-taskpack-work-calendar-1">
    <link rel="stylesheet" href="/css/flowtrack-product-categories.css?v=20260815-category-column-width-1">
    <link rel="stylesheet" href="/css/flowtrack-order-create-products.css?v=20260815-inquiry-section-numbering-1">
    <link rel="stylesheet" href="/css/flowtrack-create-order.css?v=20260817-shipping-address-3-optional">
    <link rel="stylesheet" href="/css/flowtrack-order-detail-header.css?v=20260815-order-attention-1">
    <link rel="stylesheet" href="/css/flowtrack-task-detail-attachments.css?v=20260815-compact-doc-rows-1">
    <link rel="stylesheet" href="/css/flowtrack-order-products-detail.css?v=20260817-shared-detail-products-1">
    <link rel="stylesheet" href="/css/flowtrack-order-finance.css?v=20260814-invoice-pdf-1">
    <link rel="stylesheet" href="/css/flowtrack-documents-archive.css?v=20260817-readable-table-1">
    
    <link rel="stylesheet" href="/css/flowtrack-bulk-order-import.css?v=20260815-review-compact-1">
    
    <link rel="stylesheet" href="/css/flowtrack-management-theme.css?v=20260819-team-department-colors-1">
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="<?php echo e(request()->routeIs('dashboard', 'team-performance.report') ? 'ft-management-dashboard-page' : ''); ?>">
<div class="app">
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        <?php echo $__env->make('layouts.partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="content <?php echo e(request()->routeIs('dashboard', 'team-performance.report') ? 'ft-dashboard-content-shell' : ''); ?> <?php echo e(request()->routeIs('reports') ? 'ft-inquiry-intelligence-content-shell' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success') && !request()->routeIs('task-pack.setup','master-data','financial-master-data','profile','inquiries.*','company.setup')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
    <?php echo $__env->make('layouts.partials.mobile-bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script src="/js/flowtrack-reverb-client.js?v=20260817-systemwide-1"></script>
    <script src="/js/flowtrack-workspace-refresh.js?v=20260812-reverb-1"></script>
    <script src="/js/flowtrack-master-colors.js?v=20260815-priority-persist-1"></script>
<script src="/js/flowtrack-attachment-auto-upload.js?v=20260811-2"></script>
<script src="/js/flowtrack-client-validation-focus.js?v=20260817-client-search-select-1"></script>
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