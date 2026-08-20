@props(['property' => 'search', 'value' => '', 'placeholder' => 'Search…', 'label' => 'Search'])
<div class="ft-jl-control ft-jl-control-search" x-data x-on:keydown.window="if ($event.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement?.tagName)) { $event.preventDefault(); $refs.search.focus(); }">
    <label>{{ $label }}</label>
    <div class="ft-jl-search-wrap">
        <span class="ft-jl-search-icon" aria-hidden="true">⌕</span>
        {{-- type=text avoids the browser's native search cancel button. FlowTrack
             owns the single clear action so every page behaves consistently. --}}
        <input x-ref="search" class="ft-jl-search" type="text" role="searchbox" inputmode="search" wire:model.live.debounce.300ms="{{ $property }}" placeholder="{{ $placeholder }}" autocomplete="off" x-on:keydown.escape="if ($el.value) { $wire.set(@js($property), ''); }">
        @if(filled($value))<button type="button" class="ft-jl-clear-search" wire:click="$set('{{ $property }}', '')" aria-label="Clear search">×</button>@endif
    </div>
</div>
