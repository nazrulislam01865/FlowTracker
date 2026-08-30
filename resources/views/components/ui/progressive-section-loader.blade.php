@props([
    'section',
    'rows' => 3,
    'message' => 'Loading this section when needed…',
    'rootMargin' => '240px 0px',
    'method' => 'loadCreateSection',
    'keyPrefix' => 'progressive-section',
    'contextType' => '',
    'contextId' => null,
])

@php
    $contextKey = $contextType !== '' && $contextId !== null
        ? '-'.$contextType.'-'.$contextId
        : '';
@endphp

<div
    {{ $attributes->class(['ft-progressive-section-placeholder']) }}
    wire:key="{{ $keyPrefix }}{{ $contextKey }}-{{ $method }}-{{ $section }}"
    role="status"
    aria-live="polite"
    aria-busy="true"
    x-data="{ requested: false, observer: null }"
    x-init="
        const invokeSectionLoad = () => {
            @if($contextType !== '' && $contextId !== null)
                return $wire[@js($method)](@js($section), @js($contextType), @js((int) $contextId));
            @else
                return $wire[@js($method)](@js($section));
            @endif
        };

        const loadSection = () => {
            if (requested || !$el.isConnected) return;
            requested = true;

            Promise.resolve(invokeSectionLoad()).catch(() => {
                // Allow a later viewport event to retry after a transient
                // Livewire/network failure instead of leaving the skeleton
                // permanently stuck.
                requested = false;
            });
        };

        // Disconnect observers before Livewire SPA navigation. We deliberately
        // avoid Alpine-only cleanup helpers here because this shared component
        // must also work during Livewire DOM morphs/navigation.
        document.addEventListener('livewire:navigating', () => {
            observer?.disconnect();
        }, { once: true });

        if (!window.IntersectionObserver) {
            loadSection();
            return;
        }

        observer = new IntersectionObserver((entries) => {
            if (!entries[0]?.isIntersecting) return;
            loadSection();
        }, { rootMargin: @js($rootMargin) });

        observer.observe($el);
    "
>
    <div class="ft-progressive-skeleton" aria-hidden="true">
        @for($row = 0; $row < $rows; $row++)
            <span style="--ft-skeleton-width: {{ 100 - (($row % 3) * 12) }}%"></span>
        @endfor
    </div>
    @if($message)
        <small class="muted">{{ $message }}</small>
    @endif
</div>
