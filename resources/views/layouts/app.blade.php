<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="flowtrack-session-timeout" content="{{ (int) config('session.lifetime', 30) * 60 }}">
    @auth
        <meta name="flowtrack-session-status-url" content="{{ route('session.status') }}">
        <meta name="flowtrack-session-recover-url" content="{{ route('session.recover') }}">
        <meta name="flowtrack-logout-url" content="{{ route('logout') }}">
        <meta name="flowtrack-timezone-sync-url" content="{{ route('session.timezone') }}">
        <meta name="flowtrack-display-timezone" content="{{ app(\App\Services\WorkspaceSettingsService::class)->displayTimezone() }}">
        <meta name="flowtrack-rich-text-upload-url" content="{{ route('rich-text-images.store') }}">
    @endauth
    <title>{{ ($title ?? null) ? $title.' — ' : '' }}STEP PROMO</title>
    <link rel="icon" href="{{ $branding['favicon_url'] ?? asset('images/step-promo/step-promo-icon.webp') }}">
    <link rel="stylesheet" href="/css/flowtrack-inline-editing.css?v=20260815-order-urgency-dropdown-1">
    <script src="/js/flowtrack-inline-editing.js?v=20260817-assignee-immediate-1"></script>
    <script src="/js/flowtrack-list-filters.js?v=20260817-assignee-immediate-1"></script>
    @auth
        <meta name="flowtrack-notification-count-url" content="{{ route('notifications.unread-count') }}">
    @endauth
    @if(auth()->check() && app(\App\Services\ReverbChannelService::class)->enabled())
        <meta name="flowtrack-reverb-key" content="{{ data_get(config('reverb'), 'apps.apps.0.key') }}">
        <meta name="flowtrack-reverb-host" content="{{ data_get(config('reverb'), 'apps.apps.0.options.host') }}">
        <meta name="flowtrack-reverb-port" content="{{ data_get(config('reverb'), 'apps.apps.0.options.port', 443) }}">
        <meta name="flowtrack-reverb-scheme" content="{{ data_get(config('reverb'), 'apps.apps.0.options.scheme', 'https') }}">
        <meta name="flowtrack-reverb-channel" content="private-flowtrack.user.{{ auth()->id() }}">
        <meta name="flowtrack-reverb-workspace-channel" content="private-flowtrack.workspace.{{ max(1, (int) config('flowtrack.workspace_id', 1)) }}">
        <meta name="flowtrack-reverb-auth" content="{{ route('realtime.auth') }}">
    @endif
    @vite([
        'resources/css/generated/flowtrack-01.css',
        'resources/css/generated/flowtrack-02.css',
        'resources/css/generated/flowtrack-03.css',
        'resources/css/generated/flowtrack-04.css',
        'resources/js/app.js',
    ])
    <link rel="stylesheet" href="/css/flowtrack-list-filters.css?v=20260815-client-single-filter-1">
    <link rel="stylesheet" href="/css/flowtrack-user-editor.css?v=20260815-user-assignment-refine-1">
    <link rel="stylesheet" href="/css/flowtrack-order-document-upload.css?v=20260810-1">
    <link rel="stylesheet" href="/css/flowtrack-attachment-auto-upload.css?v=20260811-2">
    <link rel="stylesheet" href="/css/flowtrack-client-logo.css?v=20260811-1">
    <link rel="stylesheet" href="/css/flowtrack-client-validation-focus.css?v=20260817-client-search-select-1">
    <link rel="stylesheet" href="/css/flowtrack-sidebar-template.css?v=20260811-3">
    @if(request()->routeIs('dashboard'))<link rel="stylesheet" href="/css/flowtrack-dashboard-prototype.css?v=20260812-1">@endif
    {{-- Inquiry Intelligence CSS is deliberately loaded with the authenticated shell.
         The sidebar uses Livewire wire:navigate, so loading this stylesheet only on the
         reports route can briefly paint the new page before its route-specific CSS
         finishes loading. The stylesheet is scoped to #inquiry-intelligence-app, so
         keeping it warm globally prevents that FOUC without affecting other pages. --}}
    <link rel="stylesheet" href="/css/flowtrack-inquiry-intelligence.css?v=20260817-portfolio-preview-2">
    {{-- Inquiry CSS is deliberately loaded for the authenticated shell, not only
         after entering /inquiries. Livewire wire:navigate swaps pages SPA-style;
         keeping this scoped stylesheet warm prevents the first Inquiry visit from
         rendering unstyled and then flashing into place a moment later. --}}
    <link rel="stylesheet" href="/css/flowtrack-inquiries.css?v=20260817-inquiry-date-toolbar-1">
    {{-- My Work CSS is preloaded with the authenticated shell. It is scoped to #my-work-app,
         which avoids resending a large inline stylesheet on every Livewire render/navigation. --}}
    <link rel="stylesheet" href="/css/flowtrack-my-work.css?v=20260817-inline-assignee-1">
    <link rel="stylesheet" href="/css/flowtrack-master-colors.css?v=20260811-2">
    <link rel="stylesheet" href="/css/flowtrack-master-data.css?v=20260817-product-shipment-urgencies-2">
    <link rel="stylesheet" href="/css/flowtrack-product-categories.css?v=20260815-category-column-width-1">
    <link rel="stylesheet" href="/css/flowtrack-order-create-products.css?v=20260815-inquiry-section-numbering-1">
    <link rel="stylesheet" href="/css/flowtrack-create-order.css?v=20260817-shipping-address-3-optional">
    <link rel="stylesheet" href="/css/flowtrack-order-detail-header.css?v=20260815-order-attention-1">
    <link rel="stylesheet" href="/css/flowtrack-task-detail-attachments.css?v=20260815-compact-doc-rows-1">
    <link rel="stylesheet" href="/css/flowtrack-order-products-detail.css?v=20260817-shared-detail-products-1">
    <link rel="stylesheet" href="/css/flowtrack-order-finance.css?v=20260814-invoice-pdf-1">
    <link rel="stylesheet" href="/css/flowtrack-documents-archive.css?v=20260817-readable-table-1">
    {{-- Bulk Order Import CSS is loaded with the shell because wire:navigate can otherwise
         paint the page before a body-level stylesheet finishes loading, causing a visible FOUC. --}}
    <link rel="stylesheet" href="/css/flowtrack-bulk-order-import.css?v=20260815-review-compact-1">
    {{-- Reusable management theme is loaded last for dashboard content only; the shared sidebar remains system-wide. --}}
    <link rel="stylesheet" href="/css/flowtrack-management-theme.css?v=20260817-dashboard-remote-filters-1">
    @livewireStyles
</head>
<body class="{{ request()->routeIs('dashboard') ? 'ft-management-dashboard-page' : '' }}">
<div class="app">
    @include('layouts.partials.sidebar')
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        @include('layouts.partials.topbar')
        <div class="content {{ request()->routeIs('dashboard') ? 'ft-dashboard-content-shell' : '' }} {{ request()->routeIs('reports') ? 'ft-inquiry-intelligence-content-shell' : '' }}">
            @if(session('success') && !request()->routeIs('task-pack.setup','master-data','financial-master-data','profile','inquiries.*','company.setup'))<div class="flash">{{ session('success') }}</div>@endif
            @yield('content')
        </div>
    </main>
    @include('layouts.partials.mobile-bottom')
</div>
@livewireScripts
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
