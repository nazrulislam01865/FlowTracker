<div>
<x-ui.page-head title="Notifications" subtitle="Assignments, changes and attention alerts delivered according to your role and record access">
    <x-slot:actions><button class="ghost" wire:click="markAllRead">Mark all read</button></x-slot:actions>
</x-ui.page-head>
<div class="card section-card"><div class="attention-list">
@forelse($notifications as $n)
    @php($url = app(\App\Services\NotificationService::class)->urlFor($n))
    <div class="attention-item" style="{{ $n->read_at?'opacity:.62':'' }}">
        <span class="signal {{ $n->type==='risk'?'red':($n->type==='assignment'?'purple':($n->type==='approval'?'amber':'purple')) }}"></span>
        <div><div class="item-title">{{ $n->title }} @if(!$n->read_at)<span class="badge b-blue">New</span>@endif</div><div class="item-meta">{{ $n->message }} · {{ $n->created_at->diffForHumans() }}</div></div>
        <a class="mini-btn" href="{{ $url }}" wire:navigate wire:click="markRead({{ $n->id }})">Open</a>
    </div>
@empty
    <div class="empty-state">No notifications.</div>
@endforelse
</div></div>
</div>
