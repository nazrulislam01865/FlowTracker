<section class="ft-panel" id="tagged">
    <div class="ft-panel-head">
        <div><h2 class="ft-panel-title">Tagged comments <span id="unread-count">{{ $unreadMentionCount }} unread</span></h2><div class="ft-panel-note">{{ $administratorView ? 'All mentions across orders, tasks and inquiries' : 'Your mentions from comments and descriptions across orders, tasks and inquiries' }}</div></div>
        <button class="ft-link" type="button" wire:click="markAllRead" @disabled($unreadMentionCount === 0)>Mark all read</button>
    </div>
    <div class="ft-mention-tabs">
        @foreach(['all' => 'All', 'unread' => 'Unread', 'job' => 'Orders', 'task' => 'Tasks', 'inquiry' => 'Inquiries'] as $key => $label)
            <button type="button" class="ft-tab {{ $filter === $key ? 'active' : '' }}" wire:click="setFilter('{{ $key }}')">{{ $label }}</button>
        @endforeach
    </div>
    <div class="ft-mentions">
        @forelse($mentions as $mention)
            @php
                $route = app(\App\Services\NotificationService::class)->urlFor($mention);
                $actor = $mention->actor;
                $actorName = $actor?->name;

                // Legacy mention rows created before actor_id existed still keep
                // the actor's name in the title. Use it as a safe initials fallback
                // if the migration could not resolve a unique user record.
                if (!$actorName && preg_match('/^(.*?) mentioned (?:you|a user) in /u', (string) $mention->title, $actorMatch)) {
                    $actorName = trim((string) ($actorMatch[1] ?? ''));
                }

                $actorName = $actorName ?: 'FlowTrack';
                $messagePreview = str(app(\App\Services\MentionService::class)->displayText($mention->message))->limit(90);
                $contextLabel = $mention->inquiry_task_id
                    ? (($mention->inquiry?->inquiry_number ?: 'Inquiry').' · '.($mention->inquiryTask?->title ?: 'Task'))
                    : ($mention->inquiry_id
                        ? ($mention->inquiry?->inquiry_number ?: 'Inquiry')
                        : ($mention->task?->task_number ?: ($mention->job?->displayOrderNumber() ?: 'Notification')));
            @endphp
            <a class="ft-mention {{ $mention->read_at ? '' : 'unread' }}" href="{{ $route }}" wire:key="dashboard-mention-{{ $mention->id }}">
                <x-ui.avatar class="ft-avatar" :user="$actor" :name="$actorName" :size="29" />
                <span><strong class="ft-mention-copy">{{ $mention->title }}: <strong>“{{ $messagePreview }}”</strong></strong><span class="ft-mention-meta">{{ $contextLabel }}</span></span>
                <time class="ft-mention-time">{{ $mention->created_at?->diffForHumans() }}</time>
            </a>
        @empty
            <div class="ft-panel-empty">No tagged comments in this view.</div>
        @endforelse
    </div>
</section>
