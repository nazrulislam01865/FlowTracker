<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type',
    'value' => '',
    'selectedId' => '',
    'options' => collect(),
    'placeholder' => 'Enter contact name',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'type',
    'value' => '',
    'selectedId' => '',
    'options' => collect(),
    'placeholder' => 'Enter contact name',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $items = collect($options)
        ->map(function ($item): array {
            if (is_array($item)) {
                return [
                    'id' => (string) ($item['id'] ?? ''),
                    'label' => (string) ($item['label'] ?? $item['name'] ?? ''),
                    'meta' => (string) ($item['meta'] ?? ''),
                ];
            }

            return [
                'id' => (string) ($item->id ?? ''),
                'label' => (string) ($item->label ?? $item->name ?? ''),
                'meta' => (string) ($item->meta ?? ''),
            ];
        })
        ->filter(fn (array $item): bool => $item['id'] !== '' && $item['label'] !== '')
        ->unique('id')
        ->values();

    $componentId = 'create-order-contact-person-'.str_replace('_', '-', (string) $type);
?>

<div
    class="ft-order-contact-combobox"
    data-ft-ui-component="order-contact-person-combobox"
    x-data="{
        open: false,
        query: '',
        dynamicType: <?php echo \Illuminate\Support\Js::from((string) $type)->toHtml() ?>,
        dynamicPlaceholder: <?php echo \Illuminate\Support\Js::from((string) $placeholder)->toHtml() ?>,
        currentValue: <?php echo \Illuminate\Support\Js::from((string) $value)->toHtml() ?>,
        selectedId: <?php echo \Illuminate\Support\Js::from((string) $selectedId)->toHtml() ?>,
        items: <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>,
        sync(type, value, selectedId, items, placeholder = null) {
            this.dynamicType = String(type || this.dynamicType || '');
            this.currentValue = String(value || '');
            this.selectedId = String(selectedId || '');
            this.items = Array.isArray(items) ? items : [];
            if (placeholder !== null) this.dynamicPlaceholder = String(placeholder || '');
            if (!this.open && this.$refs.input) this.$refs.input.value = this.currentValue;
        },
        openMenu() {
            this.open = true;
            this.query = '';
        },
        closeMenu() {
            this.open = false;
            this.query = '';
        },
        normalise(value) {
            return String(value || '').trim().toLocaleLowerCase();
        },
        get filteredItems() {
            const needle = this.normalise(this.query);
            if (!needle) return this.items;
            return this.items.filter((item) => {
                const haystack = `${item.label || ''} ${item.meta || ''}`.toLocaleLowerCase();
                return haystack.includes(needle);
            });
        },
        get hasExactMatch() {
            const typed = this.normalise(this.query || this.$refs.input?.value || '');
            return typed !== '' && this.items.some((item) => this.normalise(item.label) === typed);
        },
        async choose(item) {
            if (!item) return;
            this.open = false;
            this.query = '';
            if (this.$refs.input) this.$refs.input.value = item.label || '';
            const payload = await $wire.call('selectShippingContactOption', this.dynamicType, String(item.id));
            window.dispatchEvent(new CustomEvent('flowtrack-shipping-contact-payload', { detail: payload }));
        },
        async useTypedContact() {
            const name = String(this.$refs.input?.value || '').trim();
            if (!name) return;
            this.open = false;
            this.query = '';
            const payload = await $wire.call('useNewShippingContactPerson', this.dynamicType, name);
            window.dispatchEvent(new CustomEvent('flowtrack-shipping-contact-payload', { detail: payload }));
        },
    }"
    x-init="sync(<?php echo \Illuminate\Support\Js::from((string) $type)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from((string) $value)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from((string) $selectedId)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($items->all())->toHtml() ?>, <?php echo \Illuminate\Support\Js::from((string) $placeholder)->toHtml() ?>)"
    x-on:flowtrack-shipping-contact-switched.window="sync($event.detail?.type, $event.detail?.name, $event.detail?.selection, $event.detail?.items, $event.detail?.placeholder)"
    x-on:click.outside="closeMenu()"
>
    <div class="ft-order-contact-combobox__input-wrap">
        <input
            id="<?php echo e($componentId); ?>"
            x-ref="input"
            class="ft-order-contact-combobox__input"
            wire:model="shippingContactName"
            value="<?php echo e($value); ?>"
            autocomplete="off"
            role="combobox"
            aria-autocomplete="list"
            aria-haspopup="listbox"
            aria-controls="<?php echo e($componentId); ?>-listbox"
            x-bind:aria-expanded="open.toString()"
            x-bind:placeholder="dynamicPlaceholder"
            x-on:focus="openMenu()"
            x-on:click="openMenu()"
            x-on:input="query = $event.target.value; open = true; $dispatch('flowtrack-shipping-contact-name-input', { name: $event.target.value })"
            x-on:keydown.escape.stop="closeMenu()"
            x-on:keydown.enter.prevent="filteredItems.length === 1 ? choose(filteredItems[0]) : useTypedContact()"
        >
        <button
            type="button"
            class="ft-order-contact-combobox__toggle"
            aria-label="Show saved contacts"
            x-on:mousedown.prevent
            x-on:click.stop="open ? closeMenu() : openMenu()"
        >
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
        </button>
    </div>

    <div
        id="<?php echo e($componentId); ?>-listbox"
        class="ft-search-select__menu ft-order-contact-combobox__menu"
        role="listbox"
        x-cloak
        x-show="open"
        x-on:keydown.escape.stop="closeMenu()"
    >
        <div class="ft-search-select__list ft-order-contact-combobox__list">
            <template x-for="item in filteredItems" :key="item.id">
                <button
                    type="button"
                    role="option"
                    class="ft-search-select__option ft-order-contact-combobox__option"
                    x-bind:aria-selected="String(item.id) === String(selectedId)"
                    x-on:mousedown.prevent
                    x-on:click.stop="choose(item)"
                >
                    <span x-text="item.label"></span>
                    <small x-text="item.meta || (String(item.id) === String(selectedId) ? 'Selected' : '')"></small>
                </button>
            </template>

            <button
                type="button"
                class="ft-search-select__option ft-order-contact-combobox__option ft-order-contact-combobox__new"
                x-show="String(query || '').trim() !== '' && !hasExactMatch"
                x-on:mousedown.prevent
                x-on:click.stop="useTypedContact()"
            >
                <span>Use <strong x-text="`&quot;${String(query || '').trim()}&quot;`"></strong></span>
                <small>New contact</small>
            </button>

            <div
                class="ft-order-contact-combobox__empty"
                x-show="filteredItems.length === 0 && String(query || '').trim() === ''"
            >No saved contacts yet. Type a name to add one.</div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/create/contact-person-combobox.blade.php ENDPATH**/ ?>