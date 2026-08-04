@props([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
])
@php
    $team = \App\Support\JobDetailPresenter::team($job);
    $tabs = ['overview'=>'Overview','workflow'=>'Workflow','documents'=>'Documents'];
@endphp
<div class="ft-job-detail-page ft-exact-job-detail">
    <div class="ft-detail-toolbar ft-exact-job-header">
        <div class="ft-job-heading-copy">
            <div class="ft-detail-breadcrumb">Jobs / {{ $job->job_number }}</div>
            <h1 class="ft-editable-job-title" x-data="{ editing:false }">
                <span x-show="!editing">{{ $job->title }}</span>
                @if(app(\App\Services\AccessControlService::class)->canEditJob(auth()->user(), $job))
                    <button x-show="!editing" type="button" class="ft-pencil" aria-label="Edit job title" title="Edit job name" x-on:click.stop="editing=true; $nextTick(() => $refs.jobTitle.focus())">✎</button>
                    <input x-ref="jobTitle" x-show="editing" type="text" value="{{ $job->title }}" maxlength="255"
                        x-on:keydown.escape="editing=false"
                        x-on:keydown.enter="$event.target.blur()"
                        x-on:blur="editing=false"
                        wire:change="updateJobTextField({{ $job->id }}, 'title', $event.target.value)">
                @endif
            </h1>
            <div class="ft-exact-job-meta">
                <span>{{ $job->client?->name }}</span>
                <span class="ft-soft-pill {{ \App\Support\JobDetailPresenter::healthClass($job->health) }}">{{ $job->health }}</span>
                <span class="ft-soft-pill red">{{ $job->priority }}</span>
                <span class="ft-soft-pill purple">{{ $job->phase?->name ?? $job->status }}</span>
                <span class="ft-job-number-inline">{{ $job->job_number }} <span aria-hidden="true">▧</span></span>
            </div>
        </div>
        <div class="ft-detail-actions ft-exact-job-team" aria-label="Job team">
            <div class="ft-team-stack">
                @foreach($team->take(4) as $member)<x-ui.avatar :name="$member->name" :size="28"/>@endforeach
                @if($team->count()>4)<span class="ft-avatar-more small">+{{ $team->count()-4 }}</span>@endif
            </div>
        </div>
    </div>

    <nav class="ft-detail-tabs ft-exact-tabs">
        @foreach($tabs as $key=>$label)
            <button class="{{ $detailTab===$key ? 'active' : '' }}" wire:click="setDetailTab('{{ $key }}')">
                {{ $label }} @if($key==='documents')<span>{{ $job->documents->count() }}</span>@endif
            </button>
        @endforeach
    </nav>

    @if($detailTab==='overview')
        <x-jobs.detail-overview
            :job="$job"
            :expanded-phase-ids="$expandedPhaseIds"
            :task-statuses="$taskStatuses"
            :users="$users"
            :priorities="$priorities"
            :products="$products"
            :categories="$categories"
            :job-task-search="$jobTaskSearch"
            :activity-tab="$activityTab"
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
