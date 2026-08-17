@php
    $user = auth()->user();
    $unread = (int) ($shellData['unread_notifications'] ?? 0);
    $myWork = (int) ($shellData['open_my_work'] ?? 0);

    $inquiryCreate = $user->canAccess('inquiries.create');
    $inquiryView = $user->canAccess('inquiries.view');
    $inquiryGroupActive = request()->routeIs('inquiries.*');

    $orderView = $user->canAccess('jobs.view');
    $orderCreate = $user->canAccess('jobs.create');
    $taskView = $user->canAccess('tasks.view');
    $orderGroupActive = request()->routeIs('jobs.*', 'orders.*', 'all-tasks', 'my-work');

    $clientView = $user->canAccess('clients.view');
    $clientCreate = $user->canAccess('clients.create');
    $clientGroupActive = request()->routeIs('clients.*');

    $masterView = $user->canAccess('master.view');
    $masterGroup = (string) request()->query('group', 'product');
    $masterLabels = \App\Services\MasterDataService::LABELS;
    if (!array_key_exists($masterGroup, $masterLabels)) $masterGroup = 'product';

    $catalogueGroups = ['product', 'product_category', 'supplier'];
    $financialGroups = \App\Services\MasterDataService::FINANCIAL_TYPES;
    $catalogProductView = $user->canModule('catalog_products', 'view');
    $catalogProductCreate = $user->canModule('catalog_products', 'create');
    $productCategoryView = $user->canModule('product_categories', 'view');
    $productCategoryCreate = $user->canModule('product_categories', 'create');
    $supplierView = $user->canModule('suppliers', 'view');
    $financeMasterView = $user->canModule('finance', 'view');
    $catalogueGroupActive = request()->routeIs('master-data') && in_array($masterGroup, $catalogueGroups, true);
    $productMenuActive = request()->routeIs('master-data') && in_array($masterGroup, ['product', 'product_category'], true);
    $financialGroupActive = request()->routeIs('financial-master-data')
        || (request()->routeIs('master-data') && in_array($masterGroup, $financialGroups, true));

    $masterGroupActive = request()->routeIs('master-data')
        && !in_array($masterGroup, [...$catalogueGroups, ...$financialGroups], true);
    $masterLinks = collect($masterLabels)->except([...$catalogueGroups, ...$financialGroups, 'task_status', 'task_flag'])->all();
    $financialLinks = collect($financialGroups)
        ->mapWithKeys(fn ($type) => [$type => $masterLabels[$type]])
        ->all();
    $administrator = app(\App\Services\AccessControlService::class)->isAdministrator($user);
    $settingsGroupActive = request()->routeIs('company.setup', 'administration');
@endphp
<aside id="sidebar" class="sidebar ft-sidebar-template">
    <a class="brand ft-system-brand" href="{{ route('dashboard') }}" wire:navigate aria-label="Open Dashboard">
        @if($branding['logo_url'] ?? null)
            <img class="ft-system-logo" src="{{ $branding['logo_url'] }}" alt="{{ $branding['name'] ?? 'FlowTrack' }}">
        @else
            <div class="brand-mark">FT</div><span>{{ $branding['name'] ?? 'FlowTrack' }}</span>
        @endif
    </a>

    <nav class="ft-sidebar-nav" aria-label="Primary navigation">
        @if($user->canAccess('dashboard.view'))
            <x-ui.nav-link route="dashboard" label="Dashboard" icon="dashboard" />
        @endif

        {{-- TEMPORARILY DISABLED: Inquiry Intelligence navigation. Restore together with the page.
        @if($user->canAccess('reports.view'))
            <x-ui.nav-link route="reports" label="Inquiry Intelligence" icon="reports" />
        @endif
        --}}

        @if($inquiryView || $inquiryCreate)
            <details class="ft-sidebar-group" @if($inquiryGroupActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $inquiryGroupActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    </span>
                    <span>Inquiry</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    @if($inquiryView)
                        <x-ui.nav-link route="inquiries.index" label="Inquiries" icon="inquiries" child :active="request()->routeIs('inquiries.index') && !request()->boolean('create')" />
                    @endif
                    @if($inquiryCreate)
                        <x-ui.nav-link route="inquiries.index" label="Create Inquiry" icon="plus" child :params="['create' => 1]" :active="request()->routeIs('inquiries.index') && request()->boolean('create')" />
                    @endif
                </div>
            </details>
        @endif

        @if($orderView || $orderCreate || $taskView)
            <details class="ft-sidebar-group" @if($orderGroupActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $orderGroupActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16v13H4z"/><path d="M8 7V4h8v3"/><path d="M4 12h16"/></svg>
                    </span>
                    <span>Order</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    @if($orderView)
                        <x-ui.nav-link route="jobs.index" label="Orders" icon="jobs" child :active="request()->routeIs('jobs.index') && !request()->boolean('create')" />
                    @endif
                    @if($taskView)
                        <x-ui.nav-link route="my-work" label="My Tasks" icon="work" :badge="$myWork" child />
                    @endif
                    @if($orderCreate)
                        <x-ui.nav-link route="jobs.index" label="Create Order" icon="plus" child :params="['create' => 1]" :active="request()->routeIs('jobs.index') && request()->boolean('create')" />
                        <x-ui.nav-link route="orders.bulk-import" label="Create Bulk Order" icon="upload" child />
                    @endif
                </div>
            </details>
        @endif

        @if($clientView || $clientCreate)
            <details class="ft-sidebar-group" @if($clientGroupActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $clientGroupActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2"/></svg>
                    </span>
                    <span>Client</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    @if($clientView)
                        <x-ui.nav-link route="clients.index" label="Clients" icon="clients" child :active="request()->routeIs('clients.index') && !request()->boolean('create')" />
                    @endif
                    @if($clientCreate)
                        <x-ui.nav-link route="clients.index" label="Add Client" icon="plus" child :params="['create' => 1]" :active="request()->routeIs('clients.index') && request()->boolean('create')" />
                    @endif
                </div>
            </details>
        @endif

        @if($catalogProductView || $catalogProductCreate || $productCategoryView || $productCategoryCreate)
            <details class="ft-sidebar-group" @if($productMenuActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $productMenuActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12 12 20 4 12V4h8l8 8Z"/><circle cx="8.5" cy="8.5" r="1.2"/></svg>
                    </span>
                    <span>Product</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    @if($catalogProductView)
                        <x-ui.nav-link route="master-data" label="Products" icon="products" child :params="['group' => 'product']" :active="$catalogueGroupActive && $masterGroup === 'product' && !request()->boolean('create')" />
                    @endif
                    @if($catalogProductCreate)
                        <x-ui.nav-link route="master-data" label="Create Product" icon="plus" child :params="['group' => 'product', 'create' => 1]" :active="$catalogueGroupActive && $masterGroup === 'product' && request()->boolean('create')" />
                    @endif
                    @if($productCategoryView)
                        <x-ui.nav-link route="master-data" label="Product Categories" icon="categories" child :params="['group' => 'product_category']" :active="$catalogueGroupActive && $masterGroup === 'product_category' && !request()->boolean('create')" />
                    @endif
                    @if($productCategoryCreate)
                        <x-ui.nav-link route="master-data" label="Create Product Category" icon="plus" child :params="['group' => 'product_category', 'create' => 1]" :active="$catalogueGroupActive && $masterGroup === 'product_category' && request()->boolean('create')" />
                    @endif
                </div>
            </details>
        @endif
        @if($supplierView)
            <x-ui.nav-link route="master-data" label="Suppliers" icon="suppliers" :params="['group' => 'supplier']" :active="$catalogueGroupActive && $masterGroup === 'supplier'" />
        @endif

        @if($user->canAccess('documents.view'))
            <x-ui.nav-link route="documents.index" label="Documents" icon="documents" />
        @endif

        <div class="sidebar-section ft-sidebar-section-line"><span>Administration</span></div>
        @if($user->canAccess('notifications.view'))<x-ui.nav-link route="notifications" label="Notifications" :badge="$unread" icon="notifications" />@endif
        @if($user->canAccess('workflow.view'))<x-ui.nav-link route="workflow.setup" label="Workflow Setup" icon="settings" />@endif
        @if($user->canAccess('taskpacks.view'))<x-ui.nav-link route="task-pack.setup" label="Task Pack Setup" icon="settings" />@endif
        @if($financeMasterView)
            <details class="ft-sidebar-group" @if($financialGroupActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $financialGroupActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18M7 14h4"/></svg>
                    </span>
                    <span>Financial Master Data</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children ft-master-sidebar-children">
                    @foreach($financialLinks as $financialKey => $financialLabel)
                        <x-ui.nav-link
                            route="financial-master-data"
                            :label="$financialLabel"
                            icon="dot"
                            child
                            :params="['group' => $financialKey]"
                            :active="$financialGroupActive && $masterGroup === $financialKey"
                        />
                    @endforeach
                </div>
            </details>
        @endif
        @if($masterView)
            <details class="ft-sidebar-group" @if($masterGroupActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $masterGroupActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v4H4zM4 11h16v4H4zM4 17h16v3H4z"/></svg>
                    </span>
                    <span>Master Data</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children ft-master-sidebar-children">
                    @foreach($masterLinks as $masterKey => $masterLabel)
                        <x-ui.nav-link
                            route="master-data"
                            :label="$masterLabel"
                            icon="dot"
                            child
                            :params="['group' => $masterKey]"
                            :active="$masterGroupActive && $masterGroup === $masterKey"
                        />
                    @endforeach
                </div>
            </details>
        @endif
        @if($administrator)
            <details class="ft-sidebar-group" @if($settingsGroupActive) open @endif>
                <summary class="ft-sidebar-group-toggle {{ $settingsGroupActive ? 'is-active' : '' }}">
                    <span class="ft-sidebar-group-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21H9.6v-.09A1.7 1.7 0 0 0 8.5 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.1 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H2.3V9.6h.09A1.7 1.7 0 0 0 4.1 8.5a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 8.5 4.1a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V2.3h4v.09A1.7 1.7 0 0 0 15 4.1a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 8.5c.2.38.51.7.9.9.3.14.63.21.97.2h.09v4h-.09a1.7 1.7 0 0 0-1.87 1.4Z"/></svg>
                    </span>
                    <span>Settings</span>
                    <svg class="ft-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                </summary>
                <div class="ft-sidebar-children">
                    <x-ui.nav-link route="company.setup" label="Company Setup" icon="dot" child />
                    <x-ui.nav-link route="administration" label="Roles & Access" icon="dot" child :active="request()->routeIs('administration') && !in_array((string) request()->query('tab'), ['settings','branding'], true)" />
                    <x-ui.nav-link route="administration" label="System Settings" icon="dot" child :params="['tab' => 'settings']" :active="request()->routeIs('administration') && request()->query('tab') === 'settings'" />
                    <x-ui.nav-link route="administration" label="Branding" icon="dot" child :params="['tab' => 'branding']" :active="request()->routeIs('administration') && request()->query('tab') === 'branding'" />
                </div>
            </details>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-mini">
            <x-ui.avatar :user="$user" :name="$user->name" dark />
            <div class="ft-sidebar-user-copy">
                <div class="ft-sidebar-user-name">{{ $user->name }}</div>
                @php($sidebarRoles = $user->assignedRoles()->pluck('name'))
                <div class="ft-sidebar-user-role">{{ $sidebarRoles->count() > 1 ? $sidebarRoles->first().' +'.($sidebarRoles->count()-1) : ($sidebarRoles->first() ?: 'User') }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="ft-sidebar-logout-form">
            @csrf
            <button type="submit" class="ft-sidebar-logout" aria-label="Log out of FlowTrack">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/></svg>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>
