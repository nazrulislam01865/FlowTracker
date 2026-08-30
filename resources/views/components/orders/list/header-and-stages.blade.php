@if(session('success'))
    <div class="ft-order-list-flash" role="status">{{ session('success') }}</div>
@endif

<header class="list-head">
    <div>
        <div class="breadcrumbs">{{ $pageBreadcrumbs }}</div>
        <h1>{{ $pageTitle }}</h1>
        <p class="sub">{{ $pageDescription }}</p>
    </div>
    @if($showPageActions)
        <div class="top-actions">
            @if(auth()->user()->canAccess('jobs.create'))
                <a class="btn" href="{{ route('orders.bulk-import') }}">⇧ Bulk order</a>
            @endif
            @if(auth()->user()->canModule('jobs', 'create'))
                <a class="btn primary" href="{{ route('jobs.index', ['create' => 1]) }}" wire:navigate>＋ Create order</a>
            @endif
        </div>
    @endif
</header>

<x-orders.workflow-stage-overview
    :stages="$stages"
    :selected-stage-id="$phaseFilter"
    mode="filter"
    :title="$workflowTitle"
    :description="$workflowDescription"
/>
