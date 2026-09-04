<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => '',
    'selectedLabel' => null,
    'placeholder' => 'Unassigned',
    'context' => 'task-assignee',
    'triggerClass' => '',
    'variant' => 'compact',
    'menuWidth' => 300,
    'fixedMenu' => true,
    'searchPlaceholder' => 'Search assignee…',
    'parentType' => '',
    'parentId' => null,
    'externalTrigger' => false,
    'instanceKey' => '',
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
    'value' => '',
    'selectedLabel' => null,
    'placeholder' => 'Unassigned',
    'context' => 'task-assignee',
    'triggerClass' => '',
    'variant' => 'compact',
    'menuWidth' => 300,
    'fixedMenu' => true,
    'searchPlaceholder' => 'Search assignee…',
    'parentType' => '',
    'parentId' => null,
    'externalTrigger' => false,
    'instanceKey' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $resolvedLabel = $selectedLabel ?: $placeholder;
    $pickerKey = $instanceKey !== ''
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $instanceKey)
        : 'inline';
    // Each rendered picker needs its own physical origin. The menu is teleported
    // to <body>, so events fired from menu options cannot rely on normal DOM
    // bubbling to reach the inline editor that owns this picker.
    $pickerId = 'ft-inline-user-picker-'.$pickerKey.'-'.\Illuminate\Support\Str::uuid();
    $listboxId = $pickerId.'-listbox';
?>
<div
    id="<?php echo e($pickerId); ?>"
    <?php echo e($attributes->class(['ft-inline-remote-user', 'ft-inline-remote-user-'.$variant])); ?>

    data-ft-inline-remote-picker
    x-data="(() => {
        const picker = window.FlowTrack.ui.searchSelect({
        property: '',
        type: 'users',
        context: <?php echo \Illuminate\Support\Js::from($context)->toHtml() ?>,
        value: <?php echo \Illuminate\Support\Js::from((string) $value)->toHtml() ?>,
        placeholder: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>,
        selectedLabel: <?php echo \Illuminate\Support\Js::from($resolvedLabel)->toHtml() ?>,
        endpoint: <?php echo \Illuminate\Support\Js::from(route('filter-options.index', ['type' => 'users']))->toHtml() ?>,
        initialItems: [],
        params: { parent_type: <?php echo \Illuminate\Support\Js::from((string) $parentType)->toHtml() ?>, parent_id: <?php echo \Illuminate\Support\Js::from($parentId)->toHtml() ?> },
        disabled: false,
        menuWidth: <?php echo \Illuminate\Support\Js::from((int) $menuWidth)->toHtml() ?>,
        fixedMenu: <?php echo \Illuminate\Support\Js::from((bool) $fixedMenu)->toHtml() ?>,
        });

        picker.emitSelection = function (value, label, avatarUrl) {
            // The option menu lives under <body> because of x-teleport. Dispatch
            // from this component's ORIGINAL root instead of the teleported menu
            // so the owning inline editor receives ft-inline-remote-selected.
            // This is the persistence bridge for Order owner, Inquiry assignee,
            // task assignee, and every other shared inline user picker.
            this.pendingValue = '';
            this.pendingLabel = '';
            this.pendingPreviousValue = '';
            this.pendingPreviousLabel = '';
            this.pendingAt = 0;

            const origin = document.getElementById(<?php echo \Illuminate\Support\Js::from($pickerId)->toHtml() ?>);
            if (!origin) return;

            origin.dispatchEvent(new CustomEvent('ft-inline-remote-selected', {
                bubbles: true,
                composed: true,
                detail: {
                    value: String(value ?? ''),
                    label: String(label ?? <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>),
                    avatarUrl: String(avatarUrl ?? ''),
                },
            }));
        };

        return picker;
    })()"
    x-on:ft-inline-remote-open.stop="externalAnchorEl = $event.detail?.anchor || null; externalAnchorRect = $event.detail?.rect || null; sync(String($event.detail?.value ?? ''), String($event.detail?.label ?? <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>)); openMenu()"
    x-on:keydown.escape.window="if (open) { close(); $dispatch('ft-inline-remote-cancel') }"
    x-on:resize.window="open && reposition()"
    x-on:scroll.window="open && reposition()"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($externalTrigger)): ?>
        <button
            x-ref="trigger"
            type="button"
            class="<?php echo e(trim($triggerClass.' ft-inline-remote-user-trigger')); ?>"
            x-on:click.stop="toggle()"
            :aria-expanded="open.toString()"
            aria-haspopup="listbox"
            aria-controls="<?php echo e($listboxId); ?>"
        >
            <span x-text="selectedLabel"><?php echo e($resolvedLabel); ?></span>
            <span class="ft-filter-chevron" aria-hidden="true">⌄</span>
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fixedMenu): ?>
        
        <template x-teleport="body">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div
        x-ref="menu"
        class="ft-remote-filter-menu ft-inline-remote-user-menu"
        data-ft-inline-remote-menu
        x-cloak
        x-bind:style="open ? menuStyle + ';display:flex!important;' : 'display:none!important;'"
        x-on:click.outside="close()"
        x-on:keydown.arrow-down.prevent="moveOption(1)"
        x-on:keydown.arrow-up.prevent="moveOption(-1)"
        x-on:keydown.home.prevent="focusBoundary('first')"
        x-on:keydown.end.prevent="focusBoundary('last')"
    >
        <input
            x-ref="search"
            class="ft-remote-filter-search"
            type="text"
            role="searchbox"
            inputmode="search"
            x-model="query"
            x-on:input.debounce.300ms="searchOptions()"
            x-on:keydown.arrow-down.prevent="focusFirst()"
            placeholder="<?php echo e($searchPlaceholder); ?>"
            autocomplete="off"
        >

        <button
            type="button"
            class="ft-remote-filter-option ft-remote-filter-clear"
            x-show="selectedValue"
            x-on:click.stop="clearSelection(); emitSelection('', <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>, '')"
        >
            <span><?php echo e($placeholder); ?></span><small>Clear</small>
        </button>

        <div id="<?php echo e($listboxId); ?>" class="ft-remote-filter-list" role="listbox">
            <template x-if="loading">
                <div><div class="ft-filter-skeleton"></div><div class="ft-filter-skeleton"></div></div>
            </template>
            <template x-if="!loading && visibleItems.length === 0">
                <div class="ft-remote-filter-message">No matching options</div>
            </template>
            <template x-for="item in visibleItems" :key="item.id">
                <button
                    type="button"
                    class="ft-remote-filter-option"
                    :aria-selected="String(item.id) === String(selectedValue)"
                    x-on:click.stop="select(item); emitSelection(String(item.id), item.label, String(item.avatarUrl || ''))"
                >
                    <span x-text="item.label"></span>
                    <small x-text="item.meta || (String(item.id) === String(selectedValue) ? 'Selected' : '')"></small>
                </button>
            </template>
        </div>
        <button type="button" class="ft-search-select__load-more" x-show="hasMore && !loading" x-on:click="loadMore()">Load more</button>
        <div class="ft-remote-filter-message" x-text="message"></div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fixedMenu): ?>
        </template>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/ui/inline-remote-user.blade.php ENDPATH**/ ?>