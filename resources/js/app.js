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

const markRealtimeUnread = () => {
    const bell = document.getElementById('flowtrackNotificationBell');
    if (bell && !document.getElementById('flowtrackNotificationDot')) {
        const dot = document.createElement('span');
        dot.id = 'flowtrackNotificationDot';
        dot.className = 'dot';
        bell.appendChild(dot);
    }

    const notificationLink = [...document.querySelectorAll('#sidebar .nav-btn')]
        .find((link) => link.getAttribute('href')?.includes('/notifications'));
    if (!notificationLink) return;
    let badge = notificationLink.querySelector('.nav-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'nav-badge';
        badge.textContent = '1';
        notificationLink.appendChild(badge);
    } else {
        badge.textContent = String((Number.parseInt(badge.textContent || '0', 10) || 0) + 1);
    }
};


const clearRealtimeUnread = () => {
    document.getElementById('flowtrackNotificationDot')?.remove();
    const notificationLink = [...document.querySelectorAll('#sidebar .nav-btn')]
        .find((link) => link.getAttribute('href')?.includes('/notifications'));
    notificationLink?.querySelector('.nav-badge')?.remove();
};

let livewireNotificationListenerBound = false;
const bootLivewireNotificationEvents = () => {
    if (livewireNotificationListenerBound || !window.Livewire?.on) return;
    livewireNotificationListenerBound = true;
    window.Livewire.on('flowtrack-unread-cleared', clearRealtimeUnread);
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
        markRealtimeUnread();
        showRealtimeToast(payload);
        window.Livewire?.dispatch?.('flowtrack-notification');
    });

    window.FlowTrackPusher = pusher;
};

const boot = () => {
    bootShell();
    bootRealtimeNotifications();
    bootLivewireNotificationEvents();
};

document.addEventListener('DOMContentLoaded', boot);
document.addEventListener('livewire:navigated', bootShell);
window.addEventListener('load', () => { bootRealtimeNotifications(); bootLivewireNotificationEvents(); });
