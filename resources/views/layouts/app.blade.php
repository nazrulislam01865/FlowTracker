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
    @vite('resources/css/legacy/prelude.css')
    <script src="/js/flowtrack-inline-editing.js?v=20260817-assignee-immediate-1"></script>
    <script src="/js/flowtrack-list-filters.js?v=20260820-select-autoclose-3"></script>
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
    {{-- Phase 3: former /public/css assets are now Vite-managed source bundles. --}}
    @vite('resources/css/legacy/shell-a.css')
    @if(request()->routeIs('dashboard', 'team-performance.report'))
        @vite('resources/css/modules/dashboard/legacy-prototype.css')
    @endif
    @vite('resources/css/legacy/shell-b.css')

    {{-- CSS extracted from Blade style blocks; loaded after compatibility CSS to preserve cascade. --}}
    @vite('resources/css/migration/components.css')

    {{-- Incremental feature batches. Keep each route family independently reversible. --}}
    @if(request()->routeIs('jobs.index'))
        @vite('resources/css/modules/orders/index.css')
    @endif
    @if(request()->routeIs('all-tasks'))
        @vite('resources/css/modules/work/index.css')
    @endif
    @if(request()->routeIs('workflow.*', 'task-pack.*'))
        @vite('resources/css/modules/setup/index.css')
    @endif
    @if(request()->routeIs('dashboard', 'team-performance.report'))
        @vite('resources/css/modules/dashboard/migration.css')
    @endif
    @if(request()->routeIs('documents.index'))
        @vite('resources/css/modules/documents/filters.css')
    @endif
    @if(request()->routeIs('inquiries.*'))
        @vite('resources/css/modules/inquiries/filters.css')
    @endif
    @if(request()->routeIs('clients.index'))
        @vite('resources/css/modules/clients/filters.css')
    @endif
    @livewireStyles
</head>
<body class="{{ request()->routeIs('dashboard', 'team-performance.report') ? 'ft-management-dashboard-page' : '' }}">
<div class="app">
    @include('layouts.partials.sidebar')
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        @include('layouts.partials.topbar')
        <div class="content {{ request()->routeIs('dashboard', 'team-performance.report') ? 'ft-dashboard-content-shell' : '' }} {{ request()->routeIs('reports') ? 'ft-inquiry-intelligence-content-shell' : '' }}">
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
