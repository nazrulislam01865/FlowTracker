import { bootRichTextEditors, observeRichTextEditors, bootLivewireRichTextHooks, bootRichTextImageViewer, scheduleRichTextRefresh } from './rich-text';

const flowtrackTimezoneState = { syncing: false, attemptedTimezone: null };

const syncBrowserTimezone = async () => {
    if (flowtrackTimezoneState.syncing || !window.Intl?.DateTimeFormat) return false;

    const url = document.querySelector('meta[name="flowtrack-timezone-sync-url"]')?.content;
    const timezoneMeta = document.querySelector('meta[name="flowtrack-display-timezone"]');
    const current = timezoneMeta?.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (!url || !csrf || !timezone || timezone === current) return false;

    // Never reload the page just to persist a display time zone. On a cloud
    // deployment a dropped/non-sticky session could otherwise cause an
    // endless reload loop that repeatedly re-mounts Livewire and flickers the
    // whole dashboard. One successful sync is enough for this browser tab.
    if (flowtrackTimezoneState.attemptedTimezone === timezone) return false;

    flowtrackTimezoneState.syncing = true;
    flowtrackTimezoneState.attemptedTimezone = timezone;
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
            body: JSON.stringify({ timezone }),
        });
        if (!response.ok) return false;

        // Keep the current page stable. Server-rendered dates will use the new
        // time zone on the next normal request/navigation.
        if (timezoneMeta) timezoneMeta.content = timezone;
        return true;
    } catch (_) {
        return false;
    } finally {
        flowtrackTimezoneState.syncing = false;
    }
};

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

    // On phones/tablets, close the off-canvas navigation immediately after a
    // destination is chosen. This also works with Livewire wire:navigate.
    if (sidebar && !sidebar.dataset.flowtrackNavCloseBound) {
        sidebar.dataset.flowtrackNavCloseBound = '1';
        sidebar.addEventListener('click', (event) => {
            const link = event.target.closest('a.nav-btn, a.ft-system-brand');
            if (!link || window.matchMedia('(min-width: 901px)').matches) return;
            closeSidebar();
        });
    }
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

const realtimeState = { booted: false, connected: false, unreadTimer: null, listenerBound: false, latestNotificationId: null, initialNotificationSynced: false };
const unreadFallbackIntervalMs = 60000;

const syncUnreadCount = async () => {
    const url = document.querySelector('meta[name="flowtrack-notification-count-url"]')?.content;
    if (!url || document.hidden) return;

    try {
        const response = await fetch(url, {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin',
            cache: 'no-store',
        });
        if (!response.ok) return;
        const data = await response.json();
        setRealtimeUnreadCount(data?.count ?? 0);

        const latest = data?.latest;
        const latestId = Number.parseInt(String(latest?.id ?? 0), 10) || 0;
        if (!realtimeState.initialNotificationSynced) {
            realtimeState.initialNotificationSynced = true;
            realtimeState.latestNotificationId = latestId || null;
            return;
        }

        if (latestId > (realtimeState.latestNotificationId || 0)) {
            realtimeState.latestNotificationId = latestId;
            // Keep the Notifications page/live components in sync without
            // rendering any floating notification popup.
            window.Livewire?.dispatch?.('flowtrack-notification');
        }
    } catch (_) {
        // The next focus event or disconnected fallback interval retries.
    }
};

const stopUnreadFallback = () => {
    if (realtimeState.unreadTimer) window.clearInterval(realtimeState.unreadTimer);
    realtimeState.unreadTimer = null;
};

const startUnreadFallback = () => {
    if (realtimeState.unreadTimer) return;
    syncUnreadCount();
    realtimeState.unreadTimer = window.setInterval(syncUnreadCount, unreadFallbackIntervalMs);
};

const bootLivewireNotificationEvents = () => {
    if (realtimeState.listenerBound || !window.Livewire?.on) return;
    realtimeState.listenerBound = true;
    window.Livewire.on('flowtrack-unread-cleared', clearRealtimeUnread);
    window.Livewire.on('flowtrack-unread-count', (event) => setRealtimeUnreadCount(event?.count ?? event?.[0]?.count ?? 0));
};

const bootRealtimeNotifications = () => {
    if (realtimeState.booted) return;
    realtimeState.booted = true;

    const bindRealtimeState = () => {
        const realtime = window.FlowTrackRealtime;
        if (!realtime?.connection?.bind) {
            startUnreadFallback();
            return false;
        }

        realtime.connection.bind('connected', () => {
            realtimeState.connected = true;
            stopUnreadFallback();
            syncUnreadCount();
        });
        ['disconnected', 'unavailable', 'failed'].forEach((state) => {
            realtime.connection.bind(state, () => {
                realtimeState.connected = false;
                startUnreadFallback();
            });
        });
        realtime.connection.bind('error', () => {
            if (!realtimeState.connected) startUnreadFallback();
        });

        if (realtime.connection.state === 'connected') {
            realtimeState.connected = true;
            stopUnreadFallback();
            syncUnreadCount();
        } else {
            startUnreadFallback();
        }
        return true;
    };

    if (!bindRealtimeState()) {
        // The standalone Reverb client is loaded near the end of the layout.
        // Retry briefly, then polling remains the safe fallback if Reverb is
        // intentionally disabled or unavailable.
        let attempts = 0;
        const timer = window.setInterval(() => {
            attempts += 1;
            if (bindRealtimeState() || attempts >= 20) window.clearInterval(timer);
        }, 250);
    }
};

const flowtrackSessionState = { lastHumanActivity: Date.now(), statusTimer: null, idleTimer: null, bound: false, ownerChecked: false };
const flowtrackSessionTimeoutMs = () => (Number.parseInt(document.querySelector('meta[name="flowtrack-session-timeout"]')?.content || '1800', 10) || 1800) * 1000;
const flowtrackRedirectToLogin = (reason = 'timeout') => { if (window.location.pathname !== '/login') window.location.assign(`/login?reason=${encodeURIComponent(reason)}`); };
const flowtrackLogoutForTimeout = async () => {
    const url = document.querySelector('meta[name="flowtrack-logout-url"]')?.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (url && csrf) {
        try {
            await fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}, credentials: 'same-origin'});
        } catch (_) {}
    }
    flowtrackRedirectToLogin('timeout');
};
const checkFlowtrackSessionOwner = async () => {
    const url = document.querySelector('meta[name="flowtrack-session-status-url"]')?.content;
    if (!url || document.hidden) return;
    try {
        const response = await fetch(url, {headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-FlowTrack-Background': '1'}, credentials: 'same-origin', cache: 'no-store', redirect: 'manual'});
        if (response.status === 409 || response.status === 401 || response.type === 'opaqueredirect') flowtrackRedirectToLogin('other-device');
    } catch (_) {}
};
const checkFlowtrackIdle = () => { if (Date.now() - flowtrackSessionState.lastHumanActivity >= flowtrackSessionTimeoutMs()) flowtrackLogoutForTimeout(); };
const bootSessionSafety = () => {
    if (!document.querySelector('meta[name="flowtrack-session-status-url"]')) return;
    if (!flowtrackSessionState.bound) {
        flowtrackSessionState.bound = true;
        const mark = () => { flowtrackSessionState.lastHumanActivity = Date.now(); };
        ['pointerdown', 'keydown', 'touchstart', 'wheel'].forEach((name) => window.addEventListener(name, mark, {passive: true}));
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                checkFlowtrackIdle();
                checkFlowtrackSessionOwner();
                syncUnreadCount();
            }
        });
        window.addEventListener('focus', () => {
            checkFlowtrackSessionOwner();
            syncUnreadCount();
        });
    }
    if (!flowtrackSessionState.ownerChecked) {
        flowtrackSessionState.ownerChecked = true;
        checkFlowtrackSessionOwner();
    }
    if (!flowtrackSessionState.statusTimer) {
        flowtrackSessionState.statusTimer = window.setInterval(checkFlowtrackSessionOwner, 300000);
    }
    if (!flowtrackSessionState.idleTimer) flowtrackSessionState.idleTimer = window.setInterval(checkFlowtrackIdle, 30000);
};

const mentionState = { observer: null, livewireHookBound: false };

const parseMentionUsers = (input) => {
    try {
        const users = JSON.parse(input.dataset.mentionUsers || '[]');
        return Array.isArray(users) ? users : [];
    } catch (_) {
        return [];
    }
};

const mentionInputsWithin = (root) => {
    const inputs = [];
    if (root instanceof Element && root.matches('[data-mention-users]:not([data-rich-text])')) inputs.push(root);
    root.querySelectorAll?.('[data-mention-users]:not([data-rich-text])').forEach((input) => inputs.push(input));
    return inputs;
};

const bootMentionInputs = (root = document) => {
    mentionInputsWithin(root).forEach((input) => {
        if (input.__flowtrackMentionBound) return;

        input.__flowtrackMentionBound = true;
        input.dataset.flowtrackMentionBound = '1';

        const host = input.closest('.ft-mention-host, .ft-inline-description-editor, .ft-editable-description, .ft-comment-composer') || input.parentElement;
        host?.classList.add('ft-mention-host');

        // Keep the popup outside Livewire-managed markup. Otherwise a morph can
        // remove the popup while leaving the input's event handlers attached.
        const menu = document.createElement('div');
        menu.className = 'ft-mention-menu';
        menu.hidden = true;
        menu.setAttribute('role', 'listbox');
        menu.style.position = 'fixed';
        // The attention dialog sits above the normal application shell. Keep
        // mention results above every modal even when an older stylesheet is
        // still cached by the browser.
        menu.style.zIndex = '2005';
        document.body.appendChild(menu);

        let matches = [];
        let selectedIndex = 0;
        let mentionStart = -1;
        let mentionEnd = -1;

        const close = () => {
            menu.hidden = true;
            menu.replaceChildren();
            matches = [];
            selectedIndex = 0;
            mentionStart = -1;
            mentionEnd = -1;
        };

        const positionMenu = () => {
            if (!input.isConnected || menu.hidden) return;

            const inputRect = input.getBoundingClientRect();
            const menuWidth = Math.min(Math.max(260, Math.min(inputRect.width, 380)), Math.max(260, window.innerWidth - 24));
            const left = Math.min(Math.max(12, inputRect.left), Math.max(12, window.innerWidth - menuWidth - 12));
            const estimatedHeight = Math.min(240, Math.max(48, matches.length * 38 + 10));
            const spaceBelow = window.innerHeight - inputRect.bottom;
            const top = spaceBelow >= estimatedHeight + 12
                ? inputRect.bottom + 5
                : Math.max(12, inputRect.top - estimatedHeight - 5);

            menu.style.left = `${left}px`;
            menu.style.top = `${top}px`;
            menu.style.width = `${menuWidth}px`;
        };

        const activeToken = () => {
            const caret = input.selectionStart ?? input.value.length;
            const before = input.value.slice(0, caret);
            const at = before.lastIndexOf('@');
            if (at < 0) return null;

            // Do not treat an @ inside an email/word as a mention. The user may
            // otherwise type @ at the start of a sentence or after whitespace/
            // punctuation, then continue searching with a multi-word name.
            const boundary = at > 0 ? before.charAt(at - 1) : '';
            if (boundary && /[\p{L}\p{N}._-]/u.test(boundary)) return null;

            const query = before.slice(at + 1);
            if (query.length > 90) return null;
            if (/[\r\n,;:()[\]{}]/u.test(query)) return null;
            if (!/^[\p{L}\p{N} ._'’\-]*$/u.test(query)) return null;

            return { query, start: at, end: caret };
        };

        const selectUser = (user) => {
            if (!user || mentionStart < 0) return;

            const before = input.value.slice(0, mentionStart);
            const after = input.value.slice(mentionEnd).replace(/^\s*/, '');
            const insertion = `@${user.handle} `;
            input.value = before + insertion + after;

            const caret = before.length + insertion.length;
            input.setSelectionRange(caret, caret);
            input.dispatchEvent(new Event('input', { bubbles: true }));
            close();
            input.focus();
        };

        const render = () => {
            menu.replaceChildren();

            matches.forEach((user, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `ft-mention-option${index === selectedIndex ? ' active' : ''}`;
                button.setAttribute('role', 'option');
                button.setAttribute('aria-selected', index === selectedIndex ? 'true' : 'false');

                const avatar = document.createElement('span');
                avatar.className = 'ft-mention-avatar';
                avatar.textContent = String(user.name || '?')
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2)
                    .map((part) => part[0])
                    .join('')
                    .toUpperCase();

                const copy = document.createElement('span');
                copy.className = 'ft-mention-copy';
                const name = document.createElement('strong');
                name.textContent = user.name || 'User';
                const handle = document.createElement('small');
                handle.textContent = `@${user.handle}`;
                copy.append(name, handle);
                button.append(avatar, copy);

                button.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    selectUser(user);
                });
                menu.appendChild(button);
            });

            menu.hidden = matches.length === 0;
            positionMenu();
        };

        const update = () => {
            const token = activeToken();
            if (!token) {
                close();
                return;
            }

            const users = parseMentionUsers(input);
            mentionStart = token.start;
            mentionEnd = token.end;
            const query = token.query.toLowerCase().replace(/[._-]+/g, ' ').replace(/\s+/g, ' ').trim();
            matches = users.filter((user) => {
                const haystack = `${user.name || ''} ${user.handle || ''}`.toLowerCase().replace(/[._-]+/g, ' ').replace(/\s+/g, ' ');
                return query === '' || haystack.includes(query);
            }).slice(0, 8);
            selectedIndex = Math.min(selectedIndex, Math.max(0, matches.length - 1));
            render();
        };

        const onKeydown = (event) => {
            if (menu.hidden) return;

            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                const direction = event.key === 'ArrowDown' ? 1 : -1;
                selectedIndex = (selectedIndex + direction + matches.length) % matches.length;
                render();
                return;
            }

            if ((event.key === 'Enter' || event.key === 'Tab') && matches[selectedIndex]) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                selectUser(matches[selectedIndex]);
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                close();
            }
        };

        const onInput = () => update();
        const onClick = () => update();
        const onFocus = () => update();
        const onKeyup = (event) => {
            if (!['ArrowUp', 'ArrowDown', 'Enter', 'Escape', 'Tab'].includes(event.key)) update();
        };
        const onBlur = () => window.setTimeout(close, 120);
        const onViewportChange = () => positionMenu();

        input.addEventListener('input', onInput);
        input.addEventListener('click', onClick);
        input.addEventListener('focus', onFocus);
        input.addEventListener('keyup', onKeyup);
        input.addEventListener('keydown', onKeydown, true);
        input.addEventListener('blur', onBlur);
        window.addEventListener('resize', onViewportChange);
        window.addEventListener('scroll', onViewportChange, true);

        input.__flowtrackMentionCleanup = () => {
            input.removeEventListener('input', onInput);
            input.removeEventListener('click', onClick);
            input.removeEventListener('focus', onFocus);
            input.removeEventListener('keyup', onKeyup);
            input.removeEventListener('keydown', onKeydown, true);
            input.removeEventListener('blur', onBlur);
            window.removeEventListener('resize', onViewportChange);
            window.removeEventListener('scroll', onViewportChange, true);
            menu.remove();
            delete input.__flowtrackMentionBound;
            delete input.__flowtrackMentionCleanup;
            delete input.dataset.flowtrackMentionBound;
        };
    });
};

const cleanupMentionInputs = (root) => {
    mentionInputsWithin(root).forEach((input) => input.__flowtrackMentionCleanup?.());
};

const observeMentionInputs = () => {
    if (mentionState.observer || !document.body) return;

    mentionState.observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.removedNodes.forEach((node) => {
                if (node instanceof Element && !node.isConnected) cleanupMentionInputs(node);
            });
            mutation.addedNodes.forEach((node) => {
                if (node instanceof Element) bootMentionInputs(node);
            });
            if (mutation.type === 'attributes' && mutation.target instanceof Element) {
                bootMentionInputs(mutation.target);
            }
        });
    });

    mentionState.observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['data-mention-users'],
    });
};

const bootLivewireMentionHooks = () => {
    if (mentionState.livewireHookBound || !window.Livewire?.hook) return;
    mentionState.livewireHookBound = true;

    try {
        window.Livewire.hook('morph.updated', (payload = {}) => bootMentionInputs(payload.el || document));
    } catch (_) {
        // MutationObserver remains the compatibility fallback.
    }
};

const dropzoneState = { bound: false };

const fileDropzoneFrom = (target) => target instanceof Element ? target.closest('[data-file-dropzone]') : null;
const fileInputForDropzone = (zone) => zone?.querySelector('input[type="file"]') || null;

const updateDropzoneStatus = (zone, message = null) => {
    if (!zone) return;
    const status = zone.querySelector('[data-drop-status]');
    if (!status) return;
    if (!status.dataset.defaultText) status.dataset.defaultText = status.textContent.trim();
    status.textContent = message || status.dataset.defaultText;
};

const selectedFileSummary = (files) => {
    const list = Array.from(files || []);
    if (list.length === 0) return null;
    if (list.length === 1) return `${list[0].name} selected`;
    return `${list.length} files selected`;
};

const mergeDroppedFiles = (input, droppedFiles, replaceExisting = false) => {
    const transfer = new DataTransfer();
    const seen = new Set();
    const candidates = input.multiple
        ? (replaceExisting
            ? Array.from(droppedFiles || [])
            : [...Array.from(input.files || []), ...Array.from(droppedFiles || [])])
        : Array.from(droppedFiles || []).slice(0, 1);

    candidates.forEach((file) => {
        const key = `${file.name}:${file.size}:${file.lastModified}`;
        if (seen.has(key)) return;
        seen.add(key);
        transfer.items.add(file);
    });

    input.files = transfer.files;
    input.dispatchEvent(new Event('change', { bubbles: true }));
};

const clearDropzoneDragState = (zone) => {
    zone?.classList.remove('is-dragging');
};

const bootFileDropzones = () => {
    if (dropzoneState.bound) return;
    dropzoneState.bound = true;

    document.addEventListener('dragenter', (event) => {
        const zone = fileDropzoneFrom(event.target);
        if (!zone) return;
        event.preventDefault();
        zone.classList.add('is-dragging');
    });

    document.addEventListener('dragover', (event) => {
        const zone = fileDropzoneFrom(event.target);
        if (!zone) return;
        event.preventDefault();
        event.dataTransfer.dropEffect = 'copy';
        zone.classList.add('is-dragging');
    });

    document.addEventListener('dragleave', (event) => {
        const zone = fileDropzoneFrom(event.target);
        if (!zone || (event.relatedTarget instanceof Node && zone.contains(event.relatedTarget))) return;
        clearDropzoneDragState(zone);
    });

    document.addEventListener('drop', (event) => {
        const zone = fileDropzoneFrom(event.target);
        if (!zone) return;
        event.preventDefault();
        event.stopPropagation();
        clearDropzoneDragState(zone);

        const input = fileInputForDropzone(zone);
        const files = event.dataTransfer?.files;
        if (!input || !files?.length || input.disabled) return;

        // A failed temporary upload must never be carried into the next attempt.
        // When the zone is in an error state, the next dropped selection replaces it.
        mergeDroppedFiles(input, files, zone.classList.contains('has-upload-error'));
        zone.classList.remove('has-upload-error');
        updateDropzoneStatus(zone, selectedFileSummary(input.files));
    });

    document.addEventListener('change', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || input.type !== 'file') return;
        const zone = input.closest('[data-file-dropzone]');
        if (!zone) return;
        // Choosing a new file is a fresh attempt, so clear the previous visual error.
        zone.classList.remove('has-upload-error');
        updateDropzoneStatus(zone, selectedFileSummary(input.files));
    });

    document.addEventListener('livewire-upload-start', (event) => {
        const zone = event.target?.closest?.('[data-file-dropzone]');
        if (!zone) return;
        zone.classList.add('is-uploading');
        updateDropzoneStatus(zone, 'Preparing files…');
    });

    document.addEventListener('livewire-upload-progress', (event) => {
        const zone = event.target?.closest?.('[data-file-dropzone]');
        if (!zone) return;
        const progress = Math.max(0, Math.min(100, Number(event.detail?.progress) || 0));
        updateDropzoneStatus(zone, `Preparing files… ${progress}%`);
    });

    const finishUpload = (event, failed = false) => {
        const input = event.target;
        const zone = input?.closest?.('[data-file-dropzone]');
        if (!zone) return;
        zone.classList.remove('is-uploading');
        zone.classList.toggle('has-upload-error', failed);
        updateDropzoneStatus(zone, failed ? 'Upload preparation failed. Please try again.' : selectedFileSummary(input.files));
    };

    document.addEventListener('livewire-upload-finish', (event) => finishUpload(event, false));
    document.addEventListener('livewire-upload-error', (event) => finishUpload(event, true));
    document.addEventListener('livewire-upload-cancel', (event) => finishUpload(event, true));
};

const boot = () => {
    syncBrowserTimezone();
    bootShell();
    bootRealtimeNotifications();
    bootLivewireNotificationEvents();
    bootSessionSafety();
    bootRichTextEditors();
    observeRichTextEditors();
    bootLivewireRichTextHooks();
    bootRichTextImageViewer();
    bootMentionInputs();
    observeMentionInputs();
    bootLivewireMentionHooks();
    bootFileDropzones();
};

document.addEventListener('DOMContentLoaded', boot);
document.addEventListener('livewire:init', () => {
    bootRichTextEditors();
    observeRichTextEditors();
    bootLivewireRichTextHooks();
    bootRichTextImageViewer();
    bootMentionInputs();
    observeMentionInputs();
    bootLivewireMentionHooks();
});
document.addEventListener('livewire:navigating', (event) => {
    // Livewire 4 replaces the page body for wire:navigate. Enhance rich-text
    // fields in the onSwap phase so the browser never paints the temporary raw
    // textarea, and recreate the image viewer if the old body removed it.
    event.detail?.onSwap?.(() => {
        scheduleRichTextRefresh();
        observeRichTextEditors();
        bootRichTextImageViewer();
    });
});

document.addEventListener('livewire:navigated', () => {
    // Rich text is deliberately first: a notification/session integration
    // should never be able to block editor or image-viewer initialization.
    scheduleRichTextRefresh();
    observeRichTextEditors();
    bootLivewireRichTextHooks();
    bootRichTextImageViewer();
    syncBrowserTimezone();
    bootShell();
    bootRealtimeNotifications();
    bootLivewireNotificationEvents();
    bootSessionSafety();
    bootMentionInputs();
    observeMentionInputs();
    bootLivewireMentionHooks();
});
window.addEventListener('load', () => {
    bootRealtimeNotifications();
    bootLivewireNotificationEvents();
});
