@php
    $user = auth()->user();
    $unread = \App\Models\FlowNotification::where('user_id',$user->id)->whereNull('read_at')->count();
    $myWork = app(\App\Services\TaskService::class)->visibleQuery($user)->whereNull('completed_at')->count();
@endphp
<aside id="sidebar" class="sidebar">
    <div class="brand"><div class="brand-mark">FT</div><span>FlowTrack</span></div>
    <div class="sidebar-section">Workspace</div>
    @if($user->canAccess('dashboard.view'))<x-ui.nav-link route="dashboard" label="Dashboard" icon="dashboard" />@endif
    @if($user->canAccess('tasks.view'))<x-ui.nav-link route="my-work" label="My Work" :badge="$myWork" icon="work" />@endif
    @if($user->canAccess('jobs.view'))<x-ui.nav-link route="jobs.index" label="Jobs" icon="jobs" />@endif
    @if($user->canAccess('clients.view'))<x-ui.nav-link route="clients.index" label="Clients" icon="clients" />@endif
    @if($user->canAccess('tasks.view'))<x-ui.nav-link route="board" label="Board" icon="board" />@endif
    @if($user->canAccess('documents.view'))<x-ui.nav-link route="documents.index" label="Documents" icon="documents" />@endif
    @if($user->canAccess('reports.view'))<x-ui.nav-link route="reports" label="Reports" icon="reports" />@endif
    <div class="sidebar-section">Administration</div>
    @if($user->canAccess('notifications.view'))<x-ui.nav-link route="notifications" label="Notifications" :badge="$unread" icon="notifications" />@endif
    @if($user->canAccess('workflow.manage'))
        <x-ui.nav-link route="workflow.setup" label="Workflow Setup" icon="workflow" />
        <x-ui.nav-link route="task-pack.setup" label="Task Pack Setup" icon="settings" />
    @endif
    @if($user->canAccess('master.manage'))<x-ui.nav-link route="master-data" label="Master Data" icon="master" />@endif
    @if(app(\App\Services\AccessControlService::class)->isAdministrator($user))<x-ui.nav-link route="administration" label="Roles & Access" icon="settings" />@endif
    <div class="sidebar-footer"><div class="user-mini"><x-ui.avatar :name="$user->name" dark /><div><div style="color:#fff;font-size:12px;font-weight:650">{{ $user->name }}</div><div style="font-size:10px;color:#8397ae">{{ $user->role?->name ?? 'User' }}</div></div></div></div>
</aside>
