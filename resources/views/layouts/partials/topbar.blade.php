<header class="topbar ft-shell-topbar {{ request()->routeIs('dashboard') ? 'ft-dashboard-topbar' : '' }}">
    <button id="mobileMenu" class="mobile-menu">☰</button>
    @if(request()->routeIs('dashboard'))
        <div class="ft-dashboard-welcome"><strong>Welcome back, {{ auth()->user()->name }}</strong><span> — here is what needs your attention today.</span></div>
    @endif
    <div class="top-actions">
        <a id="flowtrackNotificationBell" class="icon-btn" href="{{ route('notifications') }}" wire:navigate aria-label="Notifications"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>@if((int) ($shellData['unread_notifications'] ?? 0) > 0)<span id="flowtrackNotificationDot" class="dot"></span>@endif</a>
        <a class="icon-btn" href="{{ route('profile') }}" wire:navigate aria-label="Profile"><x-ui.avatar :user="auth()->user()" :name="auth()->user()->name" :size="28" /></a>
    </div>
</header>
