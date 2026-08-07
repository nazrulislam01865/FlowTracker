@php
    $user = auth()->user();
    $unread = (int) ($shellData['unread_notifications'] ?? 0);
    $myWork = (int) ($shellData['open_my_work'] ?? 0);
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
    <x-ui.nav-link route="notifications" label="Notifications" :badge="$unread" icon="notifications" />
    @if($user->canAccess('workflow.manage'))
        <x-ui.nav-link route="workflow.setup" label="Workflow Setup" icon="workflow" />
        <x-ui.nav-link route="task-pack.setup" label="Task Pack Setup" icon="settings" />
    @endif
    @if($user->canAccess('master.manage'))<x-ui.nav-link route="master-data" label="Master Data" icon="master" />@endif
    @if(app(\App\Services\AccessControlService::class)->isAdministrator($user))<x-ui.nav-link route="administration" label="Roles & Access" icon="settings" />@endif
    <div class="sidebar-footer">
        <div class="user-mini"><x-ui.avatar :user="$user" :name="$user->name" dark /><div><div style="color:#fff;font-size:12px;font-weight:650">{{ $user->name }}</div><div style="font-size:10px;color:#8397ae">{{ $user->role?->name ?? 'User' }}</div></div></div>
        <form method="POST" action="{{ route('logout') }}" class="ft-sidebar-logout-form">
            @csrf
            <button type="submit" class="ft-sidebar-logout" aria-label="Log out of FlowTrack">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/></svg>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>
