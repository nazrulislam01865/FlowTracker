(() => {
    'use strict';

    if (window.__flowtrackSidebarNavigationBound) return;
    window.__flowtrackSidebarNavigationBound = true;

    const truthy = (value) => ['1', 'true', 'yes', 'on'].includes(String(value || '').toLowerCase());

    const matches = (link, currentUrl) => {
        let targetUrl;
        try {
            targetUrl = new URL(link.href, window.location.origin);
        } catch (_) {
            return false;
        }

        const route = link.dataset.ftNavRoute || '';
        const currentPath = currentUrl.pathname.replace(/\/$/, '') || '/';
        const targetPath = targetUrl.pathname.replace(/\/$/, '') || '/';

        if (route === 'workflow.setup' || route === 'task-pack.setup') {
            return currentPath === targetPath || currentPath.startsWith(`${targetPath}/`);
        }

        if (currentPath !== targetPath) return false;

        const currentCreate = truthy(currentUrl.searchParams.get('create'));
        const targetCreate = truthy(targetUrl.searchParams.get('create'));

        if (['jobs.index', 'inquiries.index', 'clients.index'].includes(route)) {
            return targetCreate ? currentCreate : !currentCreate;
        }

        if (route === 'master-data') {
            const targetGroup = targetUrl.searchParams.get('group') || 'product';
            const currentGroup = currentUrl.searchParams.get('group') || 'product';
            if (targetGroup !== currentGroup) return false;

            return targetCreate ? currentCreate : !currentCreate;
        }

        if (route === 'financial-master-data') {
            const targetGroup = targetUrl.searchParams.get('group');
            const currentGroup = currentUrl.searchParams.get('group');
            return targetGroup ? targetGroup === currentGroup : true;
        }

        if (route === 'administration') {
            const targetTab = targetUrl.searchParams.get('tab');
            const currentTab = currentUrl.searchParams.get('tab');

            if (targetTab) return targetTab === currentTab;
            return !['settings', 'branding'].includes(currentTab || '');
        }

        return true;
    };

    const setCancelledOrderCount = (count) => {
        const value = Math.max(0, Number.parseInt(String(count ?? 0), 10) || 0);
        const link = [...document.querySelectorAll('#sidebar .nav-btn')]
            .find((item) => item.getAttribute('href')?.includes('/orders/cancelled'));
        if (!link) return;

        let badge = link.querySelector('.nav-badge');
        if (value === 0) {
            badge?.remove();
            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'nav-badge';
            link.appendChild(badge);
        }
        badge.textContent = String(value);
    };

    let lastCounterSyncAt = 0;
    const syncPersistedCounters = async () => {
        const now = Date.now();
        if (now - lastCounterSyncAt < 5000) return;
        lastCounterSyncAt = now;

        const endpoint = document.querySelector('meta[name="flowtrack-notification-count-url"]')?.content;
        if (!endpoint || document.hidden) return;

        try {
            const response = await fetch(endpoint, {
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
            setCancelledOrderCount(data?.cancelled_order_count ?? 0);
        } catch (_) {
            // The existing workspace poll/realtime cycle will retry later.
        }
    };

    const sync = () => {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        const currentUrl = new URL(window.location.href);

        sidebar.querySelectorAll('a.nav-btn[data-ft-nav-route]').forEach((link) => {
            const active = matches(link, currentUrl);
            link.classList.toggle('active', active);

            if (active) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });

        sidebar.querySelectorAll('details.ft-sidebar-group').forEach((group) => {
            const active = Boolean(group.querySelector('a.nav-btn.active'));
            const summary = group.querySelector(':scope > .ft-sidebar-group-toggle');

            summary?.classList.toggle('is-active', active);
            group.open = active;
        });

        syncPersistedCounters();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', sync, { once: true });
    } else {
        sync();
    }

    document.addEventListener('livewire:navigated', sync);
    window.addEventListener('popstate', () => requestAnimationFrame(sync));
})();
