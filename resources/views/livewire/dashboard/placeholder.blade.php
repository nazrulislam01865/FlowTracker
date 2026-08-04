<div class="ft-lazy-dashboard" aria-busy="true" aria-label="Loading dashboard">
    <div class="ft-lazy-head">
        <div>
            <h1>Dashboard</h1>
            <p>Overview of your work and team activity</p>
        </div>
    </div>

    <div class="ft-lazy-metrics">
        @for($i = 0; $i < 4; $i++)
            <div class="ft-lazy-card ft-lazy-metric-card">
                <span class="ft-lazy-circle"></span>
                <span class="ft-lazy-copy">
                    <i class="ft-lazy-line short"></i>
                    <i class="ft-lazy-line medium"></i>
                </span>
            </div>
        @endfor
    </div>

    <div class="ft-lazy-dashboard-grid">
        <div class="ft-lazy-card ft-lazy-chart-card">
            <div class="ft-lazy-section-head"><span class="ft-lazy-line medium"></span><span class="ft-lazy-line tiny"></span></div>
            <div class="ft-lazy-chart">
                @foreach([45,72,51,60,36,69,40] as $height)
                    <span style="height:{{ $height }}%"></span>
                @endforeach
            </div>
        </div>
        <div class="ft-lazy-card ft-lazy-attention-card">
            <div class="ft-lazy-section-head"><span class="ft-lazy-line medium"></span><span class="ft-lazy-line tiny"></span></div>
            @for($i = 0; $i < 3; $i++)
                <div class="ft-lazy-list-row"><span class="ft-lazy-circle small"></span><span class="ft-lazy-copy"><i class="ft-lazy-line medium"></i><i class="ft-lazy-line long"></i></span><i class="ft-lazy-line tiny"></i></div>
            @endfor
        </div>
    </div>

    <div class="ft-lazy-card ft-lazy-table-card">
        <div class="ft-lazy-section-head"><span class="ft-lazy-line medium"></span></div>
        @for($i = 0; $i < 4; $i++)
            <div class="ft-lazy-table-row">
                <span class="ft-lazy-circle small"></span>
                <span class="ft-lazy-line medium"></span>
                <span class="ft-lazy-line short"></span>
                <span class="ft-lazy-line short"></span>
                <span class="ft-lazy-line medium"></span>
            </div>
        @endfor
    </div>

    <div class="ft-workspace-loader" role="status" aria-live="polite">
        <div class="ft-workspace-loader-title"><span class="ft-lazy-spinner"></span><strong>Preparing your workspace</strong></div>
        <div class="ft-workspace-step done"><span>✓</span><b>Account and permissions ready</b></div>
        <div class="ft-workspace-step active"><span class="ft-lazy-spinner small"></span><b>Loading Jobs and tasks</b></div>
        <div class="ft-workspace-step"><span class="dot"></span><b>Preparing documents and activity</b></div>
        <div class="ft-workspace-progress"><span></span></div>
        <small>This usually takes only a few seconds.</small>
        <div class="ft-workspace-tip">💡 <b>Tip:</b> Open My Work to see everything assigned to you.</div>
    </div>
</div>
