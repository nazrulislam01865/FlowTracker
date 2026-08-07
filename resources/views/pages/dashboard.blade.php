@extends('layouts.app')

@section('content')
    {{--
        Dashboard is intentionally disabled for now.

        Do not mount <livewire:dashboard.index /> here until the dashboard is
        re-enabled. Keeping the Livewire component unmounted prevents all
        DashboardService queries, secondary wire:init requests, workload data,
        delivery data, activity data, metrics, and phase-count loading.
    --}}
    <div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p class="muted">Dashboard data is temporarily disabled.</p>
        </div>
    </div>

    <div class="card section-card">
        <div class="empty-state">
            Dashboard is temporarily unavailable. No dashboard data is being loaded.
        </div>
    </div>
@endsection
