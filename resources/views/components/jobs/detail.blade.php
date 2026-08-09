@props([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
])
@php
    $team = \App\Support\JobDetailPresenter::team($job);
    $tabs = ['overview'=>'Overview','workflow'=>'Workflow','documents'=>'Documents'];
@endphp
<div {{ $attributes->class('ft-job-detail-page ft-exact-job-detail') }}>
    <div class="ft-detail-toolbar ft-exact-job-header">
        <div class="ft-job-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <span>Orders</span><span>/</span>
                <a class="ft-copyable-id-link" href="{{ route('jobs.index', ['open'=>$job->id]) }}" wire:navigate>{{ $job->displayOrderNumber() }}</a>
                <button type="button" class="ft-copy-id-btn" title="Copy Order ID" aria-label="Copy {{ $job->displayOrderNumber() }}" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(@js($job->displayOrderNumber())); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
            </div>
            <h1
                class="ft-editable-job-title ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-title'), label: 'Order name', value: @js($job->title), display: @js($job->title) })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span x-show="!editing" x-text="display">{{ $job->title }}</span>
                @if(app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job))
                    <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit order title" title="Edit order name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.jobTitle.focus())">✎</button>
                    <input x-ref="jobTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                        x-on:keydown.escape.prevent="cancelEdit()"
                        x-on:keydown.enter.prevent="$event.target.blur()"
                        x-on:blur="if (editing) { draftValue.trim() === value ? cancelEdit() : commit(draftValue.trim(), draftValue.trim(), () => $wire.updateJobTextField({{ $job->id }}, 'title', draftValue.trim())) }">
                    <x-ui.inline-save-state />
                @endif
            </h1>
            <div class="ft-exact-job-meta">
                <span>{{ $job->client?->name }}</span>
                <span class="ft-soft-pill {{ \App\Support\JobDetailPresenter::healthClass($job->health) }}">{{ $job->health }}</span>
                <span class="ft-soft-pill red">{{ $job->priority }}</span>
                <span class="ft-soft-pill purple">{{ $job->phase?->name ?? $job->status }}</span>
            </div>
        </div>
        <div class="ft-detail-actions ft-exact-job-team" aria-label="Order team">
            <div class="ft-team-stack">
                @foreach($team->take(4) as $member)<x-ui.avatar :user="$member" :name="$member->name" :size="28"/>@endforeach
                @if($team->count()>4)<span class="ft-avatar-more small">+{{ $team->count()-4 }}</span>@endif
            </div>
        </div>
    </div>

    <nav class="ft-detail-tabs ft-exact-tabs">
        @foreach($tabs as $key=>$label)
            <button class="{{ $detailTab===$key ? 'active' : '' }}" wire:click="setDetailTab('{{ $key }}')">
                {{ $label }} @if($key==='documents')<span>{{ $job->relationLoaded('documents') ? $job->documents->count() : (int) ($job->documents_count ?? 0) }}</span>@endif
            </button>
        @endforeach
    </nav>

    @if($detailTab==='overview')
        <x-jobs.detail-overview
            :job="$job"
            :expanded-phase-ids="$expandedPhaseIds"
            :task-statuses="$taskStatuses"
            :users="$users"
            :mention-users="$mentionUsers"
            :priorities="$priorities"
            :products="$products"
            :categories="$categories"
            :job-task-search="$jobTaskSearch"
            :activity-tab="$activityTab"
            :activity-page="$activityPage"
            :focus-comment="$focusComment"
            :job-document-uploads="$jobDocumentUploads"
        />
    @elseif($detailTab==='workflow')
        <x-jobs.detail-workflow :job="$job" :users="$users" :health-options="$healthOptions" />
    @elseif($detailTab==='documents')
        <x-jobs.detail-documents
            :job="$job"
            :available-documents="$availableDocuments"
            :job-document-uploads="$jobDocumentUploads"
            :show-document-picker="$showDocumentPicker"
        />
    @endif
</div>
