(() => {
    const state = window.__flowtrackWorkspaceRefresh ??= {
        version: null,
        channel: null,
        pusher: null,
        retryTimer: null,
        retryCount: 0,
        pollTimer: null,
        pollInterval: null,
        bound: false,
        syncing: false,
    };

    const endpoint = () => document.querySelector('meta[name="flowtrack-notification-count-url"]')?.content || null;
    const workspaceChannelName = () => document.querySelector('meta[name="flowtrack-pusher-workspace-channel"]')?.content || null;

    const setUnreadCount = (count) => {
        const unread = Math.max(0, Number.parseInt(String(count ?? 0), 10) || 0);
        const bell = document.getElementById('flowtrackNotificationBell');
        const dot = document.getElementById('flowtrackNotificationDot');

        if (unread > 0 && bell && !dot) {
            const marker = document.createElement('span');
            marker.id = 'flowtrackNotificationDot';
            marker.className = 'dot';
            bell.appendChild(marker);
        } else if (unread === 0) {
            dot?.remove();
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

    const setMyWorkCount = (count) => {
        const value = Math.max(0, Number.parseInt(String(count ?? 0), 10) || 0);
        const myWorkLink = [...document.querySelectorAll('#sidebar .nav-btn')]
            .find((link) => link.getAttribute('href')?.includes('/my-work'));
        if (!myWorkLink) return;

        let badge = myWorkLink.querySelector('.nav-badge');
        if (value === 0) {
            badge?.remove();
            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'nav-badge';
            myWorkLink.appendChild(badge);
        }
        badge.textContent = String(value);
    };

    const dispatchRefresh = () => {
        window.Livewire?.dispatch?.('flowtrack-refresh');
    };

    const syncState = async ({ dispatchOnVersionChange = true } = {}) => {
        const url = endpoint();
        if (!url || document.hidden || state.syncing) return;

        state.syncing = true;
        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-FlowTrack-Background': '1',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!response.ok) return;

            const data = await response.json();
            setUnreadCount(data?.count ?? 0);
            setMyWorkCount(data?.my_work_count ?? 0);

            const nextVersion = String(data?.data_version ?? '1');
            if (state.version === null) {
                state.version = nextVersion;
                return;
            }

            if (nextVersion !== state.version) {
                state.version = nextVersion;
                if (dispatchOnVersionChange) dispatchRefresh();
            }
        } catch (_) {
            // Focus, Pusher reconnect, or the next polling interval will retry.
        } finally {
            state.syncing = false;
        }
    };

    const stopPolling = () => {
        if (state.pollTimer) window.clearInterval(state.pollTimer);
        state.pollTimer = null;
        state.pollInterval = null;
    };

    const startPolling = (intervalMs = 30000) => {
        if (state.pollTimer && state.pollInterval === intervalMs) return;
        stopPolling();
        state.pollInterval = intervalMs;
        syncState();
        state.pollTimer = window.setInterval(() => syncState(), intervalMs);
    };

    const clearRetry = () => {
        if (state.retryTimer) window.clearTimeout(state.retryTimer);
        state.retryTimer = null;
    };

    const scheduleRetry = () => {
        if (state.retryTimer || state.retryCount >= 15) return;
        state.retryCount += 1;
        const delay = Math.min(5000, 400 * state.retryCount);
        state.retryTimer = window.setTimeout(() => {
            state.retryTimer = null;
            subscribeWorkspace();
        }, delay);
    };

    const subscribeWorkspace = () => {
        const channelName = workspaceChannelName();
        if (!channelName) {
            startPolling();
            return;
        }

        const pusher = window.FlowTrackPusher;
        if (!pusher) {
            // app.js creates the authenticated Pusher connection on DOM ready.
            scheduleRetry();
            startPolling();
            return;
        }

        if (state.pusher === pusher && state.channel) {
            startPolling(pusher.connection?.state === 'connected' ? 60000 : 30000);
            return;
        }

        clearRetry();
        state.retryCount = 0;
        state.pusher = pusher;
        state.channel = pusher.subscribe(channelName);
        state.channel.bind('flowtrack.refresh', (payload = {}) => {
            const incomingVersion = payload?.version == null ? null : String(payload.version);
            if (incomingVersion) state.version = incomingVersion;

            // The workspace event is only an invalidation signal. Livewire pulls
            // the latest authorized database state, so no stale record payload is
            // pushed into the page and bulk changes stay cheap.
            dispatchRefresh();
            syncState({ dispatchOnVersionChange: false });
        });

        pusher.connection?.bind('connected', () => {
            startPolling(60000);
            syncState();
        });
        ['disconnected', 'unavailable', 'failed'].forEach((connectionState) => {
            pusher.connection?.bind(connectionState, () => startPolling(30000));
        });
        pusher.connection?.bind('error', () => {
            if (pusher.connection?.state !== 'connected') startPolling(30000);
        });

        if (pusher.connection?.state === 'connected') {
            startPolling(60000);
            syncState();
        } else {
            startPolling(30000);
        }
    };

    const boot = () => {
        syncState();
        subscribeWorkspace();

        if (!state.bound) {
            state.bound = true;
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) syncState();
            });
            window.addEventListener('focus', () => syncState());
            window.addEventListener('load', subscribeWorkspace);
        }
    };

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('livewire:navigated', boot);
    document.addEventListener('livewire:init', subscribeWorkspace);
    boot();
})();
