<div class="card section-card ft-dashboard-skeleton" role="status" aria-live="polite" aria-busy="true">
    <div class="section-head">
        <h3>{{ $title }}</h3>
        <span class="ft-dashboard-skeleton-label">Loading…</span>
    </div>
    <div class="ft-dashboard-skeleton-lines" aria-hidden="true">
        @for($row = 0; $row < $rows; $row++)
            <span style="--ft-skeleton-width: {{ 100 - (($row % 3) * 12) }}%"></span>
        @endfor
    </div>
</div>
