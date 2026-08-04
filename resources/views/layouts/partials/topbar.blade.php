<header class="topbar">
    <button id="mobileMenu" class="mobile-menu">☰</button>
    <form action="{{ route('jobs.index') }}" method="GET" class="search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
        <input name="search" value="{{ request('search') }}" placeholder="Search jobs, clients, tasks, documents…">
    </form>
    <div class="top-actions">
        @unless(request()->routeIs('clients.index'))
            @if(auth()->user()->canModule('jobs','create'))<a class="primary" href="{{ route('jobs.index', ['create'=>1]) }}" wire:navigate>＋ <span>New Job</span></a>@endif
        @endunless
        <span class="lang-btn" aria-label="Language">中文</span>
        @if(auth()->user()->canModule('notifications','view'))
            <a id="flowtrackNotificationBell" class="icon-btn" href="{{ route('notifications') }}" wire:navigate aria-label="Notifications"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>@if(\App\Models\FlowNotification::where('user_id',auth()->id())->whereNull('read_at')->exists())<span id="flowtrackNotificationDot" class="dot"></span>@endif</a>
        @endif
        <a class="icon-btn" href="{{ route('profile') }}" wire:navigate aria-label="Profile"><x-ui.avatar :name="auth()->user()->name" :size="28" /></a>
    </div>
</header>
