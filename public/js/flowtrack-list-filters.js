(() => {
    const normaliseOptionValue = (value) => String(value ?? '')
        .trim()
        .toLowerCase()
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ');

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
        const requestedWidth = Number(component.menuWidth || 420);
        const preferredWidth = Number.isFinite(requestedWidth) ? Math.max(rect.width, requestedWidth) : 420;
        const width = Math.min(preferredWidth, availableWidth);
        const alignRight = rect.left + width > viewportWidth - edge;

        const roomBelow = Math.max(0, viewportHeight - rect.bottom - edge - gap);
        const roomAbove = Math.max(0, rect.top - edge - gap);
        const heightCap = component.searchable === false ? 300 : 360;
        const naturalHeight = Math.min(heightCap, Math.max(120, menu.scrollHeight || heightCap));
        const openAbove = roomBelow < Math.min(190, naturalHeight) && roomAbove > roomBelow;
        const availableHeight = Math.max(120, Math.min(naturalHeight, openAbove ? roomAbove : roomBelow || naturalHeight));

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

    window.FlowTrackRemoteFilter = (config) => {
        const initialItems = Array.isArray(config.initialItems) ? config.initialItems : [];
        const initialCache = new Map();
        if (initialItems.length) initialCache.set('', initialItems);

        return {
            ...positioningMethods,
            searchable: true,
            menuWidth: Number(config.menuWidth || 420),
            open: false,
            query: '',
            loading: false,
            items: initialItems,
            selectedValue: String(config.value || ''),
            selectedLabel: config.selectedLabel || config.placeholder,
            message: 'Recent options shown instantly. Type 2 characters to search.',
            controller: null,
            cache: initialCache,
            recentLoaded: initialItems.length > 0,
            requestSequence: 0,
            toggle() {
                if (config.disabled) return;
                this.open ? this.close() : this.openMenu();
            },
            openMenu() {
                if (config.disabled) return;
                this.openPositionedMenu();
                if (!this.recentLoaded) this.searchOptions();
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
            async searchOptions() {
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
                if (this.cache.has(key)) {
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
                    Object.entries(config.params || {}).forEach(([name, value]) => {
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
            sync(value, label) {
                const next = String(value || '');
                if (!next) {
                    this.selectedValue = '';
                    this.selectedLabel = config.placeholder;
                    return;
                }

                const item = this.items.find((candidate) => String(candidate.id) === next);
                const resolved = label && label !== config.placeholder
                    ? label
                    : item?.label || (this.selectedValue === next && this.selectedLabel !== config.placeholder ? this.selectedLabel : next);

                this.selectedValue = next;
                this.selectedLabel = resolved;
            },
            clearSelection() {
                this.selectedValue = '';
                this.selectedLabel = config.placeholder;
                this.open = false;
                this.query = '';
            },
            select(item) {
                this.selectedValue = String(item.id);
                this.selectedLabel = item.label;
                this.open = false;
                this.query = '';
            },
        };
    };

    // Small fixed lists use the exact same panel/option visuals as searchable
    // filters, just without the search input. This removes native OS dropdown
    // differences while preserving the prototype's simple-list behavior.
    window.FlowTrackLocalFilter = (config) => ({
        ...positioningMethods,
        searchable: false,
        open: false,
        items: Array.isArray(config.items) ? config.items : [],
        selectedValue: String(config.value || ''),
        selectedLabel: config.selectedLabel || config.placeholder,
        toggle() {
            if (config.disabled) return;
            if (this.open) {
                this.close();
                return;
            }

            this.openPositionedMenu();
            this.$nextTick(() => {
                this.reposition();
                const selected = this.$refs.menu?.querySelector('[aria-selected="true"]');
                (selected || this.$refs.menu?.querySelector('.ft-remote-filter-option'))?.focus();
            });
        },
        close() {
            this.open = false;
        },
        moveOption(direction) {
            const buttons = [...(this.$refs.menu?.querySelectorAll('.ft-remote-filter-option') || [])];
            if (!buttons.length) return;
            const index = buttons.indexOf(document.activeElement);
            const next = index < 0 ? 0 : Math.max(0, Math.min(buttons.length - 1, index + direction));
            buttons[next]?.focus();
        },
        choose(value, label) {
            this.selectedValue = String(value || '');
            this.selectedLabel = label || config.placeholder;
            this.open = false;
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
