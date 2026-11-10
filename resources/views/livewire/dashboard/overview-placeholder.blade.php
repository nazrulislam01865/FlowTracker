<section class="ft-order-workflow-stage-overview" aria-busy="true" aria-label="Orders by workflow stage">
    <div class="ft-order-workflow-stage-strip" style="min-height:110px;" role="status">
        @for($i = 0; $i < 7; $i++)
            <div aria-hidden="true" style="flex:1;min-width:0;min-height:96px;border-radius:10px;background:var(--ft-surface,#f3f6f8);border:1px solid var(--ft-border,#e6ebf0)"></div>
        @endfor
        <span class="sr-only">Loading workflow stages</span>
    </div>
</section>
