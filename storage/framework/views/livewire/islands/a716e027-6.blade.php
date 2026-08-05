<?php
// Extract directive's "with" parameter (overrides component properties)
$__islandScope = (function($name = null, $token = null, $lazy = false, $defer = false, $always = false, $skip = false, $with = []) {
    return $with;
})(name: 'dashboard-activity', lazy: true, always: true);
if (!empty($__islandScope)) {
    extract($__islandScope, EXTR_OVERWRITE);
}

// Extract runtime "with" parameter if provided (overrides everything)
if (isset($__runtimeWith) && is_array($__runtimeWith) && !empty($__runtimeWith)) {
    extract($__runtimeWith, EXTR_OVERWRITE);
}
?>
<?php if (isset($__placeholder)) { echo $__placeholder; return; } ?>


<div class="card section-card">
                    <div class="section-head"><h3>Recent Activity</h3><a class="link-btn" href="{{ route('notifications') }}" wire:navigate>All activity</a></div>
                    @forelse($this->activity as $notification)
                        <div class="activity"><x-ui.avatar :name="auth()->user()->name" :size="30"/><div><div class="activity-text"><b>{{ $notification->title }}</b><br>{{ $notification->message }}</div><div class="activity-time">{{ $notification->created_at->diffForHumans() }}</div></div></div>
                    @empty
                        <div class="empty-state">No recent activity.</div>
                    @endforelse
                </div>
            