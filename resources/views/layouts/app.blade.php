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
    @endauth
    <title>{{ $title ?? 'FlowTrack' }} — {{ config('app.name','FlowTrack') }}</title>
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
    @livewireStyles
</head>
<body>
<div class="app">
    @include('layouts.partials.sidebar')
    <div id="sidebarShade" class="mobile-sidebar-shade"></div>
    <main class="main">
        @include('layouts.partials.topbar')
        <div class="content">
            @if(session('success') && !request()->routeIs('task-pack.setup','workflow.setup','master-data'))<div class="flash">{{ session('success') }}</div>@endif
            @yield('content')
        </div>
    </main>
    @include('layouts.partials.mobile-bottom')
</div>
@livewireScripts
</body>
</html>
