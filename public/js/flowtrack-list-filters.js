(() => {
    const normaliseOptionValue = (value) => String(value ?? '')
        .trim()
        .toLowerCase()
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ');

    const normalisePastedSearchText = (value) => String(value ?? '')
        .replace(/[\u200B-\u200D\uFEFF]/g, '')
        .replace(/\u00A0/g, ' ')
        .replace(/[\r\n\t]+/g, ' ')
        .replace(/\s{2,}/g, ' ')
        .trim();

    const isSearchInput = (target) => {
        if (!(target instanceof HTMLInputElement)) return false;
        const placeholder = String(target.getAttribute('placeholder') || '').trim().toLowerCase();
        return target.type === 'search'
            || target.getAttribute('role') === 'searchbox'
            || target.classList.contains('ft-remote-filter-search')
            || placeholder.startsWith('search');
    };

    // Search inputs are used throughout FlowTrack by both Livewire and Alpine.
    // Normal browser paste normally fires an input event, but text copied from
    // email, Excel, chat apps and PDFs can contain NBSP/zero-width/newline
    // characters that make a visually identical search fail. Handle paste in
    // one place, preserve the user's current selection, and emit a real input
    // event so every search implementation reacts exactly like typed text.
    document.addEventListener('paste', (event) => {
        const input = event.target;
        if (!isSearchInput(input)) return;

        const clipboardText = event.clipboardData?.getData('text/plain');
        if (typeof clipboardText !== 'string') return;

        const pasted = normalisePastedSearchText(clipboardText);
        event.preventDefault();

        const start = Number.isInteger(input.selectionStart) ? input.selectionStart : input.value.length;
        const end = Number.isInteger(input.selectionEnd) ? input.selectionEnd : start;
        input.setRangeText(pasted, start, end, 'end');

        let inputEvent;
        try {
            inputEvent = new InputEvent('input', {
                bubbles: true,
                composed: true,
                inputType: 'insertFromPaste',
                data: pasted,
            });
        } catch (_) {
            inputEvent = new Event('input', {bubbles: true, composed: true});
        }
        input.dispatchEvent(inputEvent);
    }, true);

    const measureNaturalMenuHeight = (menu, heightCap) => {
        // positionDropdown() runs while the menu may already have an inline
        // max-height from an earlier scroll position. Measuring scrollHeight
        // while that flex container is constrained can return the constrained
        // height itself (for example 120px). Reusing that value on the next
        // reposition made the dropdown permanently shrink after scrolling.
        // Temporarily remove only that constraint, measure the real content,
        // then restore it until Alpine applies the newly calculated style.
        const previousMaxHeight = menu.style.getPropertyValue('max-height');
        const previousPriority = menu.style.getPropertyPriority('max-height');

        menu.style.setProperty('max-height', 'none', 'important');
        const measuredHeight = menu.scrollHeight;

        if (previousMaxHeight) {
            menu.style.setProperty('max-height', previousMaxHeight, previousPriority);
        } else {
            menu.style.removeProperty('max-height');
        }

        return Math.min(heightCap, Math.max(120, measuredHeight || heightCap));
    };

    const positionDropdown = (component) => {
        const trigger = component.$refs?.trigger;
        const menu = component.$refs?.menu;
        if (!trigger || !menu || !component.open) return;

        const rect = trigger.getBoundingClientRect();
        const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
        const viewportHeight = document.documentElement.clientHeight || window.innerHeight;
        const edge = 12;
        const gap = 5;

        // Follow the supplied prototype: the menu stays anchored to the
        // control that opened it. It may be wider than the trigger, but it
        // never participates in page layout or horizontal board scrolling.
        const availableWidth = Math.max(0, viewportWidth - (edge * 2));
        const requestedWidth = Number(component.menuWidth || 320);
        const preferredWidth = Number.isFinite(requestedWidth) ? Math.max(rect.width, requestedWidth) : 320;
        const width = Math.min(preferredWidth, availableWidth);
        const alignRight = rect.left + width > viewportWidth - edge;

        const roomBelow = Math.max(0, viewportHeight - rect.bottom - edge - gap);
        const roomAbove = Math.max(0, rect.top - edge - gap);
        const heightCap = component.searchable === false ? 300 : 360;
        const naturalHeight = measureNaturalMenuHeight(menu, heightCap);
        const openAbove = roomBelow < Math.min(190, naturalHeight) && roomAbove > roomBelow;
        const availableHeight = Math.max(120, Math.min(naturalHeight, openAbove ? roomAbove : roomBelow || naturalHeight));

        if (component.fixedMenu) {
            // Inline editors can live inside rounded cards/panels that intentionally
            // use overflow:hidden. A fixed menu is anchored to the viewport rather
            // than that card, so the final-row assignee picker can never be clipped
            // by the following Attachments/Activity section.
            const left = alignRight
                ? Math.max(edge, Math.min(rect.right - width, viewportWidth - width - edge))
                : Math.max(edge, Math.min(rect.left, viewportWidth - width - edge));
            const preferredTop = openAbove
                ? rect.top - availableHeight - gap
                : rect.bottom + gap;
            const top = Math.max(edge, Math.min(preferredTop, viewportHeight - availableHeight - edge));

            component.menuStyle = [
                'position:fixed!important',
                `left:${Math.round(left)}px!important`,
                `top:${Math.round(top)}px!important`,
                'right:auto!important',
                'bottom:auto!important',
                `width:${Math.round(width)}px!important`,
                `max-width:${Math.round(availableWidth)}px!important`,
                `max-height:${Math.round(availableHeight)}px!important`,
                'z-index:2450!important',
            ].join(';');
            return;
        }

        component.menuStyle = [
            'position:absolute!important',
            `width:${Math.round(width)}px!important`,
            `max-width:${Math.round(availableWidth)}px!important`,
            `max-height:${Math.round(availableHeight)}px!important`,
            alignRight ? 'right:0!important' : 'left:0!important',
            alignRight ? 'left:auto!important' : 'right:auto!important',
            openAbove ? `bottom:calc(100% + ${gap}px)!important` : `top:calc(100% + ${gap}px)!important`,
            openAbove ? 'top:auto!important' : 'bottom:auto!important',
        ].join(';');
    };

    const positioningMethods = {
        menuStyle: '',
        reposition() {
            this.$nextTick(() => positionDropdown(this));
        },
        openPositionedMenu() {
            this.open = true;
            this.reposition();
        },
    };

    const positionFloatingActionMenu = (component) => {
        const trigger = component.$refs?.trigger;
        const menu = component.$refs?.menu;
        if (!trigger || !menu) return;

        const triggerRect = trigger.getBoundingClientRect();
        const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
        const viewportHeight = document.documentElement.clientHeight || window.innerHeight;
        const edge = 10;
        const gap = 6;
        const width = menu.offsetWidth || 168;
        const height = menu.offsetHeight || 120;
        const roomBelow = viewportHeight - triggerRect.bottom - edge - gap;
        const roomAbove = triggerRect.top - edge - gap;
        const openAbove = roomBelow < height && roomAbove > roomBelow;
        const maxLeft = Math.max(edge, viewportWidth - width - edge);
        const left = Math.min(Math.max(edge, triggerRect.right - width), maxLeft);
        const preferredTop = openAbove
            ? triggerRect.top - height - gap
            : triggerRect.bottom + gap;
        const maxTop = Math.max(edge, viewportHeight - height - edge);
        const top = Math.min(Math.max(edge, preferredTop), maxTop);

        component.menuStyle = [
            'position:fixed!important',
            `left:${Math.round(left)}px!important`,
            `top:${Math.round(top)}px!important`,
            'right:auto!important',
            'bottom:auto!important',
            'z-index:1200!important',
        ].join(';');
    };

    window.FlowTrackFloatingActionMenu = () => ({
        menuStyle: '',
        positionMenu() {
            this.$nextTick(() => positionFloatingActionMenu(this));
        },
    });

    window.FlowTrackRemoteFilter = (config) => {
        const initialItems = Array.isArray(config.initialItems) ? config.initialItems : [];
        const initialCache = new Map();
        if (initialItems.length) initialCache.set('', initialItems);
        const initialLabels = new Map(
            initialItems.map((item) => [String(item?.id ?? ''), String(item?.label ?? '')])
        );

        return {
            ...positioningMethods,
            searchable: true,
            menuWidth: Number(config.menuWidth || 320),
            fixedMenu: config.fixedMenu === true,
            open: false,
            query: '',
            loading: false,
            items: initialItems,
            params: config.params && typeof config.params === 'object' ? {...config.params} : {},
            selectedValue: String(config.value || ''),
            selectedLabel: config.selectedLabel || config.placeholder,
            message: 'Recent options shown instantly. Type 2 characters to search.',
            controller: null,
            cache: initialCache,
            knownLabels: initialLabels,
            recentLoaded: initialItems.length > 0,
            requestSequence: 0,
            pendingValue: '',
            pendingLabel: '',
            pendingPreviousValue: '',
            pendingPreviousLabel: '',
            pendingAt: 0,
            rememberItems(items = []) {
                if (!Array.isArray(items)) return;
                items.forEach((item) => {
                    const id = String(item?.id ?? '');
                    const label = String(item?.label ?? '');
                    if (id && label) this.knownLabels.set(id, label);
                });
            },
            toggle() {
                if (config.disabled) return;
                this.open ? this.close() : this.openMenu();
            },
            openMenu() {
                if (config.disabled) return;
                this.openPositionedMenu();
                // Show the render-provided recent options immediately, then refresh
                // them in the background so each open reflects current source data.
                this.searchOptions(true);
                this.$nextTick(() => {
                    this.reposition();
                    this.$refs.search?.focus();
                });
            },
            close() {
                this.open = false;
                this.query = '';
                this.controller?.abort();
            },
            focusFirst() {
                this.$refs.menu?.querySelector('.ft-remote-filter-list .ft-remote-filter-option')?.focus();
            },
            moveOption(direction) {
                const buttons = [...(this.$refs.menu?.querySelectorAll('.ft-remote-filter-list .ft-remote-filter-option') || [])];
                if (!buttons.length) return;
                const index = buttons.indexOf(document.activeElement);
                const next = index < 0 ? 0 : Math.max(0, Math.min(buttons.length - 1, index + direction));
                buttons[next]?.focus();
            },
            async searchOptions(force = false) {
                const q = this.query.trim();
                if (q.length > 0 && q.length < 2) {
                    this.controller?.abort();
                    this.loading = false;
                    this.items = this.cache.get('') || [];
                    this.message = 'Type at least 2 characters to search.';
                    this.reposition();
                    return;
                }

                const key = q.toLowerCase();
                if (!force && this.cache.has(key)) {
                    this.items = this.cache.get(key);
                    this.message = q
                        ? `${this.items.length} result${this.items.length === 1 ? '' : 's'}`
                        : 'Recent options shown instantly. Type 2 characters to search.';
                    this.reposition();
                    return;
                }

                this.controller?.abort();
                this.controller = new AbortController();
                const sequence = ++this.requestSequence;
                this.loading = true;
                this.message = q ? 'Searching…' : 'Loading recent options…';

                try {
                    const url = new URL(config.endpoint, window.location.origin);
                    if (q) url.searchParams.set('q', q);
                    if (config.context) url.searchParams.set('context', config.context);
                    if (this.selectedValue) url.searchParams.set('selected', this.selectedValue);
                    Object.entries(this.params || {}).forEach(([name, value]) => {
                        if (value !== null && value !== undefined && String(value) !== '') {
                            url.searchParams.set(name, String(value));
                        }
                    });

                    const response = await fetch(url, {
                        headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                        credentials: 'same-origin',
                        signal: this.controller.signal,
                    });
                    if (!response.ok) throw new Error('option-search-failed');

                    const payload = await response.json();
                    if (sequence !== this.requestSequence) return;

                    this.items = Array.isArray(payload.items) ? payload.items : [];
                    this.rememberItems(this.items);
                    this.cache.set(key, this.items);
                    if (!q) this.recentLoaded = true;
                    this.message = q
                        ? `${this.items.length} result${this.items.length === 1 ? '' : 's'} · max 20`
                        : 'Recent options shown instantly. Type 2 characters to search.';
                    this.reposition();
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        this.message = 'Could not load options. Try again.';
                    }
                } finally {
                    if (sequence === this.requestSequence) this.loading = false;
                }
            },
            syncSelection(selection, params = {}, serverItems = []) {
                const nextParams = params && typeof params === 'object' ? {...params} : {};
                const currentParamsKey = JSON.stringify(this.params || {});
                const nextParamsKey = JSON.stringify(nextParams);
                const freshItems = Array.isArray(serverItems) ? serverItems : [];
                this.rememberItems(freshItems);

                // Dependent selectors (Workflow by Client, Product by
                // Category, etc.) must never keep requests/cache from the
                // previous parent selection. Livewire can preserve Alpine
                // state during a morph, so refresh the selector context here
                // as well as relying on wire:key.
                if (currentParamsKey !== nextParamsKey) {
                    this.controller?.abort();
                    this.requestSequence++;
                    this.params = nextParams;
                    this.items = freshItems;
                    this.cache = new Map();
                    if (freshItems.length) this.cache.set('', freshItems);
                    this.recentLoaded = freshItems.length > 0;
                    this.query = '';
                    this.message = 'Recent options shown instantly. Type 2 characters to search.';
                }

                // Value and label arrive as one server-rendered object. This is
                // important during a Livewire morph: accepting them as separate
                // reactive arguments allowed a new Client id to be paired with
                // the previous Client label for one or more renders.
                const next = String(selection?.value || '');
                const suppliedLabel = selection?.label && selection.label !== config.placeholder
                    ? String(selection.label)
                    : '';

                // A click is optimistic and must stay visible immediately.
                // Other Livewire requests (progressive Create Order sections,
                // a previous selector request, etc.) can finish while the Client
                // change is still in flight. Those responses contain an older
                // server value. Ignore them until the server confirms the exact
                // value the user most recently chose. This prevents the selector
                // from jumping back to the previous Client and then changing a
                // few moments later.
                if (this.pendingAt) {
                    if (next === this.pendingValue) {
                        const confirmedLabel = freshItems.find((candidate) => String(candidate?.id) === next)?.label
                            || this.knownLabels.get(next)
                            || this.pendingLabel
                            || suppliedLabel
                            || next;
                        this.selectedValue = next;
                        this.selectedLabel = String(confirmedLabel);
                        this.knownLabels.set(next, String(confirmedLabel));
                        this.pendingValue = '';
                        this.pendingLabel = '';
                        this.pendingPreviousValue = '';
                        this.pendingPreviousLabel = '';
                        this.pendingAt = 0;
                        return;
                    }

                    if ((Date.now() - this.pendingAt) < 15000) return;

                    // A very old pending selection should not lock the control
                    // forever if a request was interrupted outside Livewire.
                    this.pendingValue = '';
                    this.pendingLabel = '';
                    this.pendingPreviousValue = '';
                    this.pendingPreviousLabel = '';
                    this.pendingAt = 0;
                }

                if (!next) {
                    this.selectedValue = '';
                    this.selectedLabel = config.placeholder;
                    return;
                }

                const item = freshItems.find((candidate) => String(candidate?.id) === next)
                    || this.items.find((candidate) => String(candidate?.id) === next);
                const knownLabel = this.knownLabels.get(next);

                // Never reuse the currently displayed label as a fallback for a
                // server value. Alpine state may have survived a DOM morph while
                // the value changed, which is exactly how "anything" could be
                // selected while "hh" or another previous Client was shown.
                const resolved = item?.label || knownLabel || suppliedLabel || next;

                this.selectedValue = next;
                this.selectedLabel = String(resolved);
                if (resolved && resolved !== next) this.knownLabels.set(next, String(resolved));
            },
            beginPendingSelection(next, label) {
                if (!this.pendingValue) {
                    this.pendingPreviousValue = this.selectedValue;
                    this.pendingPreviousLabel = this.selectedLabel;
                }
                this.pendingValue = String(next || '');
                this.pendingLabel = String(label || config.placeholder);
                this.pendingAt = Date.now();
            },
            selectionFailed() {
                if (!this.pendingValue && !this.pendingAt) return;
                this.selectedValue = this.pendingPreviousValue;
                this.selectedLabel = this.pendingPreviousLabel || config.placeholder;
                this.pendingValue = '';
                this.pendingLabel = '';
                this.pendingPreviousValue = '';
                this.pendingPreviousLabel = '';
                this.pendingAt = 0;
            },
            clearSelection() {
                this.beginPendingSelection('', config.placeholder);
                this.selectedValue = '';
                this.selectedLabel = config.placeholder;
                this.open = false;
                this.query = '';
            },
            select(item) {
                const next = String(item.id);
                const nextLabel = String(item.label || next);
                this.beginPendingSelection(next, nextLabel);
                this.knownLabels.set(next, nextLabel);
                this.selectedValue = next;
                this.selectedLabel = nextLabel;
                this.open = false;
                this.query = '';
            },
        };
    };

    // Fixed list filters now use the same searchable, lightweight interaction
    // as the Job Details assignee picker. Search stays client-side because the
    // option sets are already small and fully loaded.
    window.FlowTrackLocalFilter = (config) => ({
        ...positioningMethods,
        searchable: true,
        menuWidth: Number(config.menuWidth || 320),
        open: false,
        query: '',
        items: Array.isArray(config.items) ? config.items : [],
        selectedValue: String(config.value || ''),
        selectedLabel: config.selectedLabel || config.placeholder,
        get filteredItems() {
            const q = normaliseOptionValue(this.query);
            if (!q) return this.items;
            return this.items.filter((item) =>
                normaliseOptionValue(item.label).includes(q) || normaliseOptionValue(item.meta).includes(q)
            );
        },
        toggle() {
            if (config.disabled) return;
            if (this.open) {
                this.close();
                return;
            }

            this.openPositionedMenu();
            this.$nextTick(() => {
                this.reposition();
                this.$refs.search?.focus();
            });
        },
        close() {
            this.open = false;
            this.query = '';
        },
        focusFirst() {
            this.$refs.menu?.querySelector('.ft-remote-filter-list .ft-remote-filter-option')?.focus();
        },
        moveOption(direction) {
            const buttons = [...(this.$refs.menu?.querySelectorAll('.ft-remote-filter-list .ft-remote-filter-option') || [])];
            if (!buttons.length) return;
            const index = buttons.indexOf(document.activeElement);
            const next = index < 0 ? 0 : Math.max(0, Math.min(buttons.length - 1, index + direction));
            buttons[next]?.focus();
        },
        choose(value, label) {
            this.selectedValue = String(value || '');
            this.selectedLabel = label || config.placeholder;
            this.open = false;
            this.query = '';
        },
        sync(value, label) {
            const next = String(value || '');
            if (!next) {
                this.selectedValue = '';
                this.selectedLabel = config.placeholder;
                return;
            }

            const normalised = normaliseOptionValue(next);
            const item = this.items.find((candidate) =>
                String(candidate.id) === next || normaliseOptionValue(candidate.id) === normalised
            );
            const resolved = label && label !== config.placeholder
                ? label
                : item?.label || (this.selectedValue === next && this.selectedLabel !== config.placeholder ? this.selectedLabel : next);

            this.selectedValue = next;
            this.selectedLabel = resolved;
        },
    });
})();
