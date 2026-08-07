<header class="topbar ft-shell-topbar">
    <button id="mobileMenu" class="mobile-menu">☰</button>
    <div class="top-actions">
        <span class="lang-btn" aria-label="Language">中文</span>
        <a id="flowtrackNotificationBell" class="icon-btn" href="{{ route('notifications') }}" wire:navigate aria-label="Notifications"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>@if((int) ($shellData['unread_notifications'] ?? 0) > 0)<span id="flowtrackNotificationDot" class="dot"></span>@endif</a>
        <a class="icon-btn" href="{{ route('profile') }}" wire:navigate aria-label="Profile"><x-ui.avatar :user="auth()->user()" :name="auth()->user()->name" :size="28" /></a>
    </div>
</header>
