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
                $initials = collect(preg_split('/\s+/', trim($mention->title)))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                $contextLabel = $mention->inquiry_task_id
                    ? (($mention->inquiry?->inquiry_number ?: 'Inquiry').' · '.($mention->inquiryTask?->title ?: 'Task'))
                    : ($mention->inquiry_id
                        ? ($mention->inquiry?->inquiry_number ?: 'Inquiry')
                        : ($mention->task?->task_number ?: ($mention->job?->displayOrderNumber() ?: 'Notification')));
            @endphp
            <a class="ft-mention {{ $mention->read_at ? '' : 'unread' }}" href="{{ $route }}" wire:key="dashboard-mention-{{ $mention->id }}">
                <span class="ft-avatar">{{ $initials ?: '@' }}</span>
                <span><strong class="ft-mention-copy">{{ $mention->title }}: <strong>“{{ str(app(\App\Services\RichTextService::class)->plainText($mention->message))->limit(90) }}”</strong></strong><span class="ft-mention-meta">{{ $contextLabel }}</span></span>
                <time class="ft-mention-time">{{ $mention->created_at?->diffForHumans() }}</time>
            </a>
        @empty
            <div class="ft-panel-empty">No tagged comments in this view.</div>
        @endforelse
    </div>
</section>
