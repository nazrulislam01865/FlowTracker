@props([
    'step' => 4,
    'title' => 'What happens next',
    'workflowOptions' => collect(),
    'selectedWorkflowId' => null,
    'selectedWorkflowName' => 'Select workflow',
    'phaseCount' => 0,
    'taskCount' => 0,
    'selectionAction' => 'setCreateSelector',
    'selectionProperty' => 'workflowId',
    'optionFallback' => 'Workflow',
    'footnote' => 'Tasks are created when you create this record.',
    'previewAllowed' => false,
    'emptyMessage' => null,
    'errorField' => null,
    'startPhases' => collect(),
    'startPhaseId' => null,
    'startPhaseProperty' => null,
    'startPhaseErrorField' => null,
])

@php
    $workflowOptions = collect($workflowOptions);
    $startPhases = collect($startPhases);
    $workflowOptionCount = $workflowOptions->count();
    $phaseCount = (int) $phaseCount;
    $taskCount = (int) $taskCount;
    $selectedWorkflowId = $selectedWorkflowId !== null ? (int) $selectedWorkflowId : null;
    $showStartPhasePicker = filled($startPhaseProperty) && $startPhases->count() > 1;
@endphp

<section {{ $attributes->class('ft-create-workflow-next') }} x-data="{ workflowOpen: false }">
    <div class="ft-create-workflow-heading">
        <span>{{ $step }}</span>
        <h2>{{ $title }}</h2>
        @if($workflowOptionCount > 0)
            <em>{{ $workflowOptionCount }} {{ \Illuminate\Support\Str::plural('workflow', $workflowOptionCount) }} available</em>
        @endif
    </div>

    <div class="ft-create-workflow-card" :class="{ 'is-open': workflowOpen }">
        <button
            class="ft-create-workflow-selected"
            type="button"
            x-on:click="workflowOpen = !workflowOpen"
            :aria-expanded="workflowOpen.toString()"
            aria-haspopup="listbox"
        >
            <span class="ft-create-workflow-icon" aria-hidden="true">✓</span>
            <span class="ft-create-workflow-copy">
                <small>Default workflow</small>
                <strong>{{ $selectedWorkflowName ?: 'Select workflow' }}</strong>
                <span>{{ $phaseCount }} {{ \Illuminate\Support\Str::plural('phase', $phaseCount) }} · {{ $taskCount }} {{ \Illuminate\Support\Str::plural('task', $taskCount) }} will be created</span>
            </span>
            @if($previewAllowed)
                <span class="ft-workflow-preview-muted" title="Workflow Setup is temporarily disabled">Preview workflow unavailable</span>
            @endif
            <span class="ft-create-workflow-chevron" aria-hidden="true">⌄</span>
        </button>

        <div class="ft-create-workflow-options" x-cloak x-show="workflowOpen" role="listbox" aria-label="Available workflows">
            @forelse($workflowOptions as $workflowOption)
                @php
                    $optionId = (int) ($workflowOption['id'] ?? 0);
                    $wireClick = $selectionAction."('".$selectionProperty."', '".$optionId."')";
                @endphp
                <button
                    type="button"
                    class="ft-create-workflow-option {{ $optionId === $selectedWorkflowId ? 'is-selected' : '' }}"
                    wire:click="{{ $wireClick }}"
                    x-on:click="workflowOpen = false"
                    role="option"
                    aria-selected="{{ $optionId === $selectedWorkflowId ? 'true' : 'false' }}"
                >
                    <span class="ft-create-workflow-radio" aria-hidden="true"></span>
                    <span>
                        <strong>{{ $workflowOption['label'] ?? 'Workflow' }}</strong>
                        <small>{{ filled($workflowOption['meta'] ?? null) ? $workflowOption['meta'] : $optionFallback }}</small>
                    </span>
                </button>
            @empty
                <div class="ft-create-workflow-empty">No workflow is available for the selected client.</div>
            @endforelse

            @if($showStartPhasePicker)
                <label class="ft-create-workflow-start-phase">
                    <span>Starting phase</span>
                    <select wire:model.live="{{ $startPhaseProperty }}">
                        @foreach($startPhases as $phase)
                            <option value="{{ $phase->id }}">{{ $phase->sequence }}. {{ $phase->name }}</option>
                        @endforeach
                    </select>
                    <small>Choose where this Order should enter the selected workflow.</small>
                </label>
            @endif
        </div>
    </div>

    @if($emptyMessage)
        <small class="field-error validation-error">{{ $emptyMessage }}</small>
    @elseif($errorField && $errors->has($errorField))
        <small class="field-error validation-error">{{ $errors->first($errorField) }}</small>
    @endif

    @if($startPhaseErrorField && $errors->has($startPhaseErrorField))
        <small class="field-error validation-error">{{ $errors->first($startPhaseErrorField) }}</small>
    @endif

    @if($footnote)
        <p class="ft-create-workflow-footnote">{{ $footnote }}</p>
    @endif
</section>
