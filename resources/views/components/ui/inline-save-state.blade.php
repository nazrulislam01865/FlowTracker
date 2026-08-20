@props(['compact' => false])
<span
    {{ $attributes->class(['ft-inline-save-state', 'compact' => $compact]) }}
    data-ft-ui-component="inline-save-state"
    :class="{
        'is-saving': status === 'saving',
        'is-saved': status === 'saved',
        'is-error': status === 'error'
    }"
    aria-live="polite"
>
    <span x-cloak x-show="status === 'saving'">Saving…</span>
    <span x-cloak x-show="status === 'saved'">Saved</span>
    <button x-cloak x-show="status === 'error'" type="button" x-on:click.stop="retry()" title="Retry this save">Not saved · Retry</button>
</span>
