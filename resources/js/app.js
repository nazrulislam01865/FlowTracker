const bootShell = () => {
    const sidebar = document.getElementById('sidebar');
    const shade = document.getElementById('sidebarShade');
    const menu = document.getElementById('mobileMenu');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        shade?.classList.remove('open');
    };

    if (menu && !menu.dataset.flowtrackBound) {
        menu.dataset.flowtrackBound = '1';
        menu.addEventListener('click', () => {
            sidebar?.classList.add('open');
            shade?.classList.add('open');
        });
    }
    if (shade && !shade.dataset.flowtrackBound) {
        shade.dataset.flowtrackBound = '1';
        shade.addEventListener('click', closeSidebar);
    }
};

const showRealtimeToast = (payload) => {
    let host = document.getElementById('flowtrackRealtimeToasts');
    if (!host) {
        host = document.createElement('div');
        host.id = 'flowtrackRealtimeToasts';
        host.className = 'ft-realtime-toasts';
        document.body.appendChild(host);
    }

    const item = document.createElement(payload?.url ? 'a' : 'div');
    item.className = `ft-realtime-toast ft-realtime-toast-${payload?.type || 'info'}`;
    if (payload?.url) item.href = payload.url;
    item.innerHTML = `
        <span class="ft-realtime-toast-dot"></span>
        <span class="ft-realtime-toast-copy">
            <strong></strong>
            <small></small>
        </span>
        <span class="ft-realtime-toast-time">Now</span>`;
    item.querySelector('strong').textContent = payload?.title || 'FlowTrack update';
    item.querySelector('small').textContent = payload?.message || '';
    host.prepend(item);
    window.setTimeout(() => item.remove(), 6500);
};

const setRealtimeUnreadCount = (count) => {
    const unread = Math.max(0, Number.parseInt(String(count ?? 0), 10) || 0);
    const bell = document.getElementById('flowtrackNotificationBell');
    const existingDot = document.getElementById('flowtrackNotificationDot');

    if (unread > 0 && bell && !existingDot) {
        const dot = document.createElement('span');
        dot.id = 'flowtrackNotificationDot';
        dot.className = 'dot';
        bell.appendChild(dot);
    } else if (unread === 0) {
        existingDot?.remove();
    }

    const notificationLink = [...document.querySelectorAll('#sidebar .nav-btn')]
        .find((link) => link.getAttribute('href')?.includes('/notifications'));
    if (!notificationLink) return;

    let badge = notificationLink.querySelector('.nav-badge');
    if (unread === 0) {
        badge?.remove();
        return;
    }
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'nav-badge';
        notificationLink.appendChild(badge);
    }
    badge.textContent = String(unread);
};

const markRealtimeUnread = (payload = {}) => {
    const current = Number.parseInt(document.querySelector('#sidebar .nav-btn[href*="/notifications"] .nav-badge')?.textContent || '0', 10) || 0;
    setRealtimeUnreadCount(payload?.unread_count ?? (current + 1));
};

const clearRealtimeUnread = () => setRealtimeUnreadCount(0);

let unreadSyncTimer = null;
const syncUnreadCount = async () => {
    const url = document.querySelector('meta[name="flowtrack-notification-count-url"]')?.content;
    if (!url) return;
    try {
        const response = await fetch(url, {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin',
            cache: 'no-store',
        });
        if (!response.ok) return;
        const data = await response.json();
        setRealtimeUnreadCount(data?.count ?? 0);
    } catch (_) {
        // Pusher remains the primary delivery path; this lightweight request is
        // only a fallback when a browser/network temporarily misses an event.
    }
};

const bootUnreadFallback = () => {
    syncUnreadCount();
    if (!unreadSyncTimer) unreadSyncTimer = window.setInterval(syncUnreadCount, 5000);
};

let livewireNotificationListenerBound = false;
const bootLivewireNotificationEvents = () => {
    if (livewireNotificationListenerBound || !window.Livewire?.on) return;
    livewireNotificationListenerBound = true;
    window.Livewire.on('flowtrack-unread-cleared', clearRealtimeUnread);
    window.Livewire.on('flowtrack-unread-count', (event) => setRealtimeUnreadCount(event?.count ?? event?.[0]?.count ?? 0));
};

let pusherBooted = false;
const bootRealtimeNotifications = () => {
    if (pusherBooted || !window.Pusher) return;
    const key = document.querySelector('meta[name="flowtrack-pusher-key"]')?.content;
    const cluster = document.querySelector('meta[name="flowtrack-pusher-cluster"]')?.content;
    const channelName = document.querySelector('meta[name="flowtrack-pusher-channel"]')?.content;
    const authEndpoint = document.querySelector('meta[name="flowtrack-pusher-auth"]')?.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!key || !cluster || !channelName || !authEndpoint || !csrf) return;

    pusherBooted = true;
    const pusher = new window.Pusher(key, {
        cluster,
        forceTLS: true,
        channelAuthorization: {
            endpoint: authEndpoint,
            transport: 'ajax',
            headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'},
        },
    });

    const channel = pusher.subscribe(channelName);
    channel.bind('flowtrack.notification', (payload) => {
        markRealtimeUnread(payload);
        showRealtimeToast(payload);
        window.Livewire?.dispatch?.('flowtrack-notification');
    });

    window.FlowTrackPusher = pusher;
};


const flowtrackSessionState = { lastHumanActivity: Date.now(), statusTimer: null, idleTimer: null, bound: false };
const flowtrackSessionTimeoutMs = () => (Number.parseInt(document.querySelector('meta[name="flowtrack-session-timeout"]')?.content || '1800', 10) || 1800) * 1000;
const flowtrackRedirectToLogin = (reason = 'timeout') => { if (window.location.pathname !== '/login') window.location.assign(`/login?reason=${encodeURIComponent(reason)}`); };
const flowtrackLogoutForTimeout = async () => {
    const url = document.querySelector('meta[name="flowtrack-logout-url"]')?.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (url && csrf) { try { await fetch(url, {method:'POST',headers:{'X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},credentials:'same-origin'}); } catch (_) {} }
    flowtrackRedirectToLogin('timeout');
};
const checkFlowtrackSessionOwner = async () => {
    const url = document.querySelector('meta[name="flowtrack-session-status-url"]')?.content;
    if (!url || document.hidden) return;
    try {
        const response = await fetch(url,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-FlowTrack-Background':'1'},credentials:'same-origin',cache:'no-store',redirect:'manual'});
        if (response.status === 409 || response.status === 401 || response.type === 'opaqueredirect') flowtrackRedirectToLogin('other-device');
    } catch (_) {}
};
const checkFlowtrackIdle = () => { if (Date.now() - flowtrackSessionState.lastHumanActivity >= flowtrackSessionTimeoutMs()) flowtrackLogoutForTimeout(); };
const bootSessionSafety = () => {
    if (!document.querySelector('meta[name="flowtrack-session-status-url"]')) return;
    if (!flowtrackSessionState.bound) {
        flowtrackSessionState.bound = true;
        const mark = () => { flowtrackSessionState.lastHumanActivity = Date.now(); };
        ['pointerdown','keydown','touchstart','wheel'].forEach((name) => window.addEventListener(name,mark,{passive:true}));
        document.addEventListener('visibilitychange',()=>{ if(!document.hidden){ checkFlowtrackIdle(); checkFlowtrackSessionOwner(); } });
    }
    if (!flowtrackSessionState.statusTimer) flowtrackSessionState.statusTimer = window.setInterval(checkFlowtrackSessionOwner,10000);
    if (!flowtrackSessionState.idleTimer) flowtrackSessionState.idleTimer = window.setInterval(checkFlowtrackIdle,30000);
    checkFlowtrackSessionOwner();
};

const boot = () => {
    bootShell();
    bootRealtimeNotifications();
    bootLivewireNotificationEvents();
    bootUnreadFallback();
    bootSessionSafety();
};

document.addEventListener('DOMContentLoaded', boot);
document.addEventListener('livewire:navigated', () => { bootShell(); bootRealtimeNotifications(); bootLivewireNotificationEvents(); bootSessionSafety(); syncUnreadCount(); });
window.addEventListener('load', () => { bootRealtimeNotifications(); bootLivewireNotificationEvents(); bootUnreadFallback(); });
window.addEventListener('focus', syncUnreadCount);
document.addEventListener('visibilitychange', () => { if (!document.hidden) syncUnreadCount(); });
