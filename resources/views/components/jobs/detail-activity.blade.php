@props(['job','compact'=>false,'activityTab'=>'all'])
@php
    $canComment = app(\App\Services\AccessControlService::class)->canEditJob(auth()->user(), $job);
    $activities = $job->activities->sortByDesc('created_at')->values();
    if ($activityTab === 'comments') {
        $activities = $activities->where('event','job.comment')->values();
    } elseif ($activityTab === 'history') {
        $activities = $activities->reject(fn($activity) => $activity->event === 'job.comment')->values();
    }
@endphp
<section class="ft-detail-card ft-activity-card ft-friendly-activity {{ $compact ? 'compact' : '' }}">
    <div class="ft-activity-head">
        <div>
            <h2>Activity</h2>
            <p>Comments and Job changes, with who changed what and when.</p>
        </div>
        <div class="ft-activity-tabs">
            <button type="button" class="{{ $activityTab==='all'?'active':'' }}" wire:click="setJobActivityTab('all')">All</button>
            <button type="button" class="{{ $activityTab==='comments'?'active':'' }}" wire:click="setJobActivityTab('comments')">Comments</button>
            <button type="button" class="{{ $activityTab==='history'?'active':'' }}" wire:click="setJobActivityTab('history')">History</button>
        </div>
    </div>
    @if($canComment)
        <div class="ft-comment-composer ft-friendly-composer">
            <x-ui.avatar :name="auth()->user()->name" :size="32"/>
            <input wire:model="jobComment" wire:keydown.enter="addJobComment" placeholder="Write a comment about this Job...">
            <button class="ft-new-job-btn" type="button" wire:click="addJobComment" wire:loading.attr="disabled" wire:target="addJobComment">Comment</button>
        </div>
    @endif
    <div class="ft-activity-feed">
        @forelse($activities as $activity)
            @php
                $isComment = $activity->event === 'job.comment';
                $actorName = $activity->user?->name ?? 'System';
                $eventLabel = $isComment ? 'Comment' : \Illuminate\Support\Str::headline(str_replace(['job.','task.'], '', (string) $activity->event));
            @endphp
            <article class="ft-activity-entry {{ $isComment ? 'is-comment' : 'is-history' }}">
                <div class="ft-activity-entry-avatar">
                    <x-ui.avatar :name="$actorName" :size="32"/>
                    <span>{{ $isComment ? '💬' : '↻' }}</span>
                </div>
                <div class="ft-activity-entry-content">
                    <div class="ft-activity-entry-head">
                        <div><b>{{ $actorName }}</b><span class="ft-activity-kind {{ $isComment ? 'comment' : 'history' }}">{{ $isComment ? 'Comment' : 'Change' }}</span></div>
                        <time title="{{ $activity->created_at?->format('M j, Y g:i A') }}">{{ $activity->created_at?->diffForHumans() }}</time>
                    </div>
                    <p>{{ $activity->description }}</p>
                    <div class="ft-activity-entry-meta"><span>{{ $eventLabel }}</span><span>•</span><span>{{ $activity->created_at?->format('M j, Y · g:i A') }}</span></div>
                </div>
            </article>
        @empty
            <div class="empty-state">No {{ $activityTab==='comments' ? 'comments' : ($activityTab==='history' ? 'changes' : 'activity') }} yet.</div>
        @endforelse
    </div>
</section>
