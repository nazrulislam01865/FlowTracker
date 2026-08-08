<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="flowtrack-session-timeout" content="{{ (int) config('session.lifetime', 30) * 60 }}">
    @auth
        <meta name="flowtrack-session-status-url" content="{{ route('session.status') }}">
        <meta name="flowtrack-logout-url" content="{{ route('logout') }}">
        <meta name="flowtrack-timezone-sync-url" content="{{ route('session.timezone') }}">
        <meta name="flowtrack-display-timezone" content="{{ app(\App\Services\WorkspaceSettingsService::class)->displayTimezone() }}">
    @endauth
    <title>{{ $title ?? 'FlowTrack' }} — {{ $branding['name'] ?? config('app.name','FlowTrack') }}</title>
    <link rel="icon" href="{{ $branding['favicon_url'] ?? asset('favicon.ico') }}">
    <link rel="stylesheet" href="/css/flowtrack-inline-editing.css?v=20260808-10">
    <script src="/js/flowtrack-inline-editing.js?v=20260807-3"></script>
    <script src="/js/flowtrack-list-filters.js?v=20260808-5"></script>
    @auth
        <meta name="flowtrack-notification-count-url" content="{{ route('notifications.unread-count') }}">
    @endauth
    @if(auth()->check() && app(\App\Services\PusherChannelService::class)->enabled())
        <meta name="flowtrack-pusher-key" content="{{ config('services.pusher.key') }}">
        <meta name="flowtrack-pusher-cluster" content="{{ config('services.pusher.cluster','mt1') }}">
        <meta name="flowtrack-pusher-channel" content="private-flowtrack.user.{{ auth()->id() }}">
        <meta name="flowtrack-pusher-auth" content="{{ route('pusher.auth') }}">
        <script src="https://js.pusher.com/8.4.0/pusher.min.js" defer></script>
    @endif
    @vite([
        'resources/css/generated/flowtrack-01.css',
        'resources/css/generated/flowtrack-02.css',
        'resources/css/generated/flowtrack-03.css',
        'resources/css/generated/flowtrack-04.css',
        'resources/js/app.js',
    ])
    <link rel="stylesheet" href="/css/flowtrack-list-filters.css?v=20260808-4">
    <link rel="stylesheet" href="/css/flowtrack-user-editor.css?v=20260807-2">
    @if(request()->routeIs('dashboard'))<link rel="stylesheet" href="/css/flowtrack-dashboard-prototype.css?v=20260809-3">@endif
    @if(request()->routeIs('inquiries.*'))<link rel="stylesheet" href="/css/flowtrack-inquiries.css?v=20260809-07">@endif
    @livewireStyles
</head>
<body>
<div class="app">
    @include('layouts.partials.sidebar')
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        @include('layouts.partials.topbar')
        <div class="content {{ request()->routeIs('dashboard') ? 'ft-dashboard-content-shell' : '' }}">
            @if(session('success') && !request()->routeIs('task-pack.setup','workflow.setup','master-data','profile','inquiries.*'))<div class="flash">{{ session('success') }}</div>@endif
            @yield('content')
        </div>
    </main>
    @include('layouts.partials.mobile-bottom')
</div>
@livewireScripts
</body>
</html>
