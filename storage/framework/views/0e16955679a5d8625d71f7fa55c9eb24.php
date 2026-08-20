<div
    id="my-work-app"
    x-data="{ metrics: <?php echo \Illuminate\Support\Js::from($taskPackMetrics)->toHtml() ?>, groupsExpanded: false }"
    x-on:board-task-metrics.window="metrics = $event.detail"
>
<style>
/* FlowTrack My Work content follows the supplied prototype; the application shell/sidebar inherits the shared FlowTrack layout. */
body{background:#f3f6fb;color:#172033}
.topbar{height:55px;display:flex;align-items:center;justify-content:flex-end;gap:7px;padding:0 22px;border-bottom:1px solid #dbe3ed;background:#fff;position:sticky;top:0;z-index:20}
.top-actions{margin-left:auto;display:flex;align-items:center;gap:7px}
.lang-btn{height:32px;min-width:32px;padding:0 10px;border:1px solid #dce4ef;border-radius:9px;background:#fff;color:#374257;font-size:11px;display:grid;place-items:center}
.icon-btn{width:32px;height:32px;min-width:32px;padding:0;border:1px solid #dce4ef;border-radius:9px;background:#fff;color:#374257}
.icon-btn svg{width:16px}
.content{width:100%;max-width:1680px;margin:0 auto;padding:21px clamp(12px,2vw,28px) 35px}
#my-work-app .page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:0}
#my-work-app .page-head h1{margin:0;font-size:23px;letter-spacing:-.025em}
#my-work-app .page-head p{margin:4px 0 0;color:#60708b;font-size:10px}
#my-work-app .page-tabs{display:flex;gap:4px;margin-top:12px;border-bottom:1px solid #dbe3ed}
#my-work-app .page-tab{position:relative;min-height:36px;padding:0 11px;border:0;background:transparent;color:#69778e;font-size:9px;font-weight:750}
#my-work-app .page-tab.active{color:#155ce9}
#my-work-app .page-tab.active::after{content:"";position:absolute;right:9px;bottom:-1px;left:9px;height:2px;background:#2463eb}
#my-work-app .metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:9px;margin-top:12px;margin-bottom:0}
#my-work-app .metric{display:flex;min-width:0;align-items:center;justify-content:space-between;gap:8px;min-height:66px;padding:10px 11px;border:1px solid #dbe3ed;border-radius:10px;background:#fff;text-align:left;position:static;overflow:visible;transform:none;box-shadow:none}
#my-work-app .metric:hover,#my-work-app .metric.active{border-color:#91afea;background:#fbfdff;transform:none;box-shadow:none}
#my-work-app .metric span{min-width:0}
#my-work-app .metric small{display:block;overflow:hidden;color:#718097;font-size:8px;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .metric strong{display:block;margin-top:3px;font-size:18px;line-height:1}
#my-work-app .metric i{display:grid;width:27px;height:27px;flex:0 0 27px;place-items:center;border-radius:8px;background:#edf3ff;color:#2463eb;font-size:10px;font-style:normal}
#my-work-app .metric.red i{background:#fff0f0;color:#c43f3f}
#my-work-app .metric.amber i{background:#fff6e5;color:#9a6208}
#my-work-app .toolbar{display:grid;grid-template-columns:1fr;gap:9px;align-items:stretch;margin-top:10px;margin-bottom:0;padding:10px;border:1px solid #dbe3ed;border-radius:11px;background:#fff}
#my-work-app .search-wrap{position:relative;min-width:240px;flex:1}
#my-work-app .search-icon{position:absolute;left:11px;top:50%;color:#718198;transform:translateY(-50%)}
#my-work-app .search{position:static;display:block;max-width:none;width:100%;height:38px;padding:0 62px 0 34px;border:1px solid #ccd8e8;border-radius:9px;background:#fff;outline:0;font-size:9px;flex:none}
#my-work-app .search:focus{border-color:#78a0f2;box-shadow:0 0 0 3px rgba(36,99,235,.08)}
#my-work-app .clear{position:absolute;right:7px;top:50%;padding:4px 6px;border:0;background:transparent;color:#65748a;font-size:7.5px;transform:translateY(-50%)}
#my-work-app .quick-filters{display:flex;gap:5px}
#my-work-app .chip{min-height:33px;padding:0 9px;border:1px solid #d2dce9;border-radius:8px;background:#fff;color:#526078;font-size:8px;font-weight:720}
#my-work-app .chip.active{border-color:#91afea;background:#edf3ff;color:#245fcf}
#my-work-app .sort{height:35px;min-width:150px;padding:0 28px 0 9px;border:1px solid #d2dce9;border-radius:8px;background:#fff;color:#37445a;font-size:8px}
#my-work-app .load-state{display:flex;min-height:28px;align-items:center;justify-content:space-between;gap:8px;padding:0 3px;color:#758298;font-size:7.5px}
#my-work-app .spinner{display:inline-block;width:12px;height:12px;border:2px solid #dce5f2;border-top-color:#2463eb;border-radius:50%;animation:spin .6s linear infinite}
#my-work-app .loading-copy{display:flex;align-items:center;gap:6px}
#my-work-app .list-shell{overflow-x:auto;overflow-y:hidden;overscroll-behavior-x:contain;-webkit-overflow-scrolling:touch;scrollbar-gutter:stable;border:1px solid #d8e1ec;border-radius:11px;background:#fff}
#my-work-app .task-head,#my-work-app .task-row{display:grid;grid-template-columns:minmax(230px,1.7fr) minmax(105px,.68fr) minmax(130px,.82fr) 92px 124px minmax(95px,.65fr) minmax(92px,.62fr) 48px;align-items:center;gap:8px}
#my-work-app .task-head{min-height:32px;padding:0 11px;border-bottom:1px solid #dce3ec;background:#f8fafc;color:#69778e;font-size:7px;font-weight:780;letter-spacing:.04em;text-transform:uppercase}
#my-work-app .order-group{border-bottom:1px solid #dce3ec;content-visibility:auto;contain:layout paint style;contain-intrinsic-size:205px}
#my-work-app .order-group:last-child{border-bottom:0}
#my-work-app .order-head{display:grid;grid-template-columns:22px minmax(180px,1.3fr) minmax(130px,.8fr) minmax(110px,.7fr) minmax(100px,.7fr) minmax(120px,.8fr) auto;align-items:center;gap:8px;min-height:49px;padding:7px 11px;background:#fbfcfe}
#my-work-app .order-head:hover{background:#f8faff}
#my-work-app .collapse{display:grid;width:22px;height:22px;place-items:center;border:0;background:transparent;color:#506078;font-size:11px;padding:0}
#my-work-app .order-id{color:#155ce9;font-size:9px;font-weight:800;text-decoration:none}
#my-work-app .order-title{display:block;margin-top:2px;overflow:hidden;color:#253248;font-size:9px;font-weight:730;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .order-client,#my-work-app .order-stage{color:#64738a;font-size:8px}
#my-work-app .health,#my-work-app .flag{display:inline-flex;max-width:100%;align-items:center;padding:3px 6px;border-radius:8px;font-size:7px;font-weight:750;white-space:nowrap}
#my-work-app .health:before{content:none}
#my-work-app .health.green,#my-work-app .flag.green{background:#edf9f4;color:#147e5b}
#my-work-app .health.amber,#my-work-app .flag.amber{background:#fff6e5;color:#9a6208}
#my-work-app .health.red,#my-work-app .flag.red{background:#fff0f0;color:#c43f3f}
#my-work-app .flag.blue{background:#edf3ff;color:#245fcf}
#my-work-app .order-progress{display:flex;align-items:center;gap:6px;color:#67768c;font-size:7.5px}
#my-work-app .progress-track{width:54px;height:5px;overflow:hidden;border-radius:5px;background:#e7ecf3}
#my-work-app .progress-track i{display:block;height:100%;border-radius:5px;background:#2463eb}
#my-work-app .task-count{color:#607087;font-size:7.5px;text-align:right}
#my-work-app .task-row{position:relative;min-height:54px;padding:6px 11px;border-top:1px solid #e5eaf1;background:#fff}
#my-work-app .task-row:hover{background:#fbfdff}
#my-work-app .task-row.saving{opacity:.55}
#my-work-app .task-row.saving::after{content:"";position:absolute;right:8px;top:8px;width:10px;height:10px;border:2px solid #dce5f2;border-top-color:#2463eb;border-radius:50%;animation:spin .6s linear infinite}
#my-work-app .task-main{min-width:0}
#my-work-app .task-link{display:block;overflow:hidden;color:#263248;font-size:9px;font-weight:750;text-decoration:none;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .task-link:hover{color:#155ce9;text-decoration:underline}
#my-work-app .task-ref{display:block;margin-top:3px;color:#7c899c;font-size:7px}
#my-work-app .phase{overflow:hidden;color:#5a687f;font-size:8px;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .assignee{display:flex;min-width:0;align-items:center;gap:7px;color:#42526b;font-size:8px}
#my-work-app .assignee .avatar{flex:0 0 auto}
#my-work-app .assignee-name{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#my-work-app .due{font-size:8px}
#my-work-app .due.overdue{color:#c43f3f;font-weight:750}
#my-work-app .due.today{color:#9a6208;font-weight:750}
#my-work-app .status-select{width:100%;height:30px;padding:0 22px 0 7px;border:1px solid #cfdbea;border-radius:7px;background:#fff;color:#344057;font-size:7.5px}
#my-work-app .updated{color:#718097;font-size:7.5px}
#my-work-app .row-action{display:grid;width:42px;height:28px;place-items:center;border:1px solid #cbd8e8;border-radius:7px;background:#fff;color:#155ce9;font-size:7.5px;font-weight:750;text-decoration:none}
#my-work-app .empty{padding:40px 15px;color:#718097;font-size:9px;text-align:center}
#my-work-app .empty strong{display:block;margin-bottom:5px;color:#2d3950;font-size:11px}
#my-work-app .footer{display:flex;align-items:center;justify-content:space-between;gap:10px;min-height:50px;padding:8px 11px;border-top:1px solid #dce3ec;background:#fbfcfe;color:#69778e;font-size:8px}
#my-work-app .pages{display:flex;gap:5px}
#my-work-app .page-button{min-width:31px;height:31px;padding:0 8px;border:1px solid #cfdbea;border-radius:7px;background:#fff;color:#40506a;font-size:8px;font-weight:750}
#my-work-app .page-button.active{border-color:#2463eb;background:#2463eb;color:#fff}
#my-work-app .page-button:disabled{opacity:.45;cursor:default}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes reveal{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
#my-work-app .metric,#my-work-app .list-shell{animation:reveal .25s ease both}
#my-work-app .metric:nth-child(2){animation-delay:.03s}
#my-work-app .metric:nth-child(3){animation-delay:.06s}
#my-work-app .metric:nth-child(4){animation-delay:.09s}
#my-work-app .metric:nth-child(5){animation-delay:.12s}
@media(max-width:1180px){
    #my-work-app .metrics{grid-template-columns:repeat(3,minmax(0,1fr))}
    #my-work-app .task-head{display:none}
    #my-work-app .task-row{grid-template-columns:minmax(175px,1.3fr) minmax(90px,.62fr) minmax(115px,.78fr) 82px 112px 85px 78px 48px}
    #my-work-app .order-head{grid-template-columns:22px minmax(170px,1fr) minmax(110px,.7fr) minmax(100px,.7fr) minmax(85px,.6fr) auto}
    #my-work-app .order-progress{display:none}
}
@media(max-width:820px){
    .content{padding-inline:11px}
    #my-work-app .metrics{grid-template-columns:repeat(2,minmax(0,1fr))}
    #my-work-app .toolbar{align-items:stretch;flex-wrap:wrap}
    #my-work-app .search-wrap{width:100%;flex-basis:100%}
    #my-work-app .sort{flex:1}
    #my-work-app .task-row{grid-template-columns:minmax(0,1.4fr) 86px 108px 44px;grid-template-areas:"task assignee status action" "phase due flag action" "updated updated flag action";row-gap:6px;min-height:78px}
    #my-work-app .task-main{grid-area:task}
    #my-work-app .phase{grid-area:phase}
    #my-work-app .assignee{grid-area:assignee}
    #my-work-app .due{grid-area:due}
    #my-work-app .status-select{grid-area:status}
    #my-work-app .updated{grid-area:updated}
    #my-work-app .task-row>.flag{grid-area:flag;justify-self:start}
    #my-work-app .row-action{grid-area:action}
    #my-work-app .order-head{grid-template-columns:22px minmax(0,1fr) auto;grid-template-areas:"toggle order count" "toggle client health";row-gap:4px}
    #my-work-app .collapse{grid-area:toggle}
    #my-work-app .order-identity{grid-area:order}
    #my-work-app .order-client{grid-area:client}
    #my-work-app .order-stage{display:none}
    #my-work-app .order-head>.health{grid-area:health;justify-self:end}
    #my-work-app .task-count{grid-area:count}
}
@media(max-width:520px){
    .topbar{padding-inline:10px}
    .content{padding:14px 8px 28px}
    #my-work-app .page-head p{display:none}
    #my-work-app .metrics{grid-template-columns:repeat(2,minmax(0,1fr))}
    #my-work-app .metric{min-height:59px;padding:8px}
    #my-work-app .metric:nth-child(5){grid-column:1/-1}
    #my-work-app .toolbar{padding:8px}
    #my-work-app .quick-filters{width:100%;overflow:auto}
    #my-work-app .chip{flex:0 0 auto}
    #my-work-app .task-row{grid-template-columns:minmax(0,1fr) 95px 42px;grid-template-areas:"task action" "assignee action" "phase action" "status action" "due action" "updated action" "flag action";padding:9px;min-height:105px}
    #my-work-app .footer{align-items:flex-start;flex-direction:column}
    #my-work-app .pages{width:100%}
    #my-work-app .page-button{flex:1}
    #my-work-app .order-title{white-space:normal}
}

/* 2026-08-09: Mobile/tablet task-row alignment. Keep each task readable without
   squeezing status, due date, flag and action into mismatched columns. */
@media(max-width:820px){
    #my-work-app .load-state{flex-wrap:wrap;row-gap:3px;padding-block:5px}
    #my-work-app .loading-copy{margin-left:auto}
    #my-work-app .order-head{padding:9px 10px}
    #my-work-app .order-identity{min-width:0}
    #my-work-app .order-id{display:block;overflow-wrap:anywhere}
    #my-work-app .order-client{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    #my-work-app .task-row{
        grid-template-columns:minmax(0,1fr) minmax(145px,180px) 60px;
        grid-template-areas:
            "task assignee action"
            "phase status action"
            "updated due action"
            "flag flag action";
        column-gap:12px;
        row-gap:7px;
        min-height:112px;
        padding:11px 12px;
        align-items:center;
    }
    #my-work-app .task-main{grid-area:task;align-self:start;min-width:0}
    #my-work-app .assignee{grid-area:assignee;justify-self:start;max-width:100%}
    #my-work-app .task-link{white-space:normal;overflow:visible;text-overflow:clip;line-height:1.25;overflow-wrap:anywhere}
    #my-work-app .phase{grid-area:phase;justify-self:start;max-width:100%;white-space:normal;overflow:visible;text-overflow:clip}
    #my-work-app .updated{grid-area:updated;justify-self:start}
    #my-work-app .status-select{grid-area:status;width:100%;min-width:0;height:36px;font-size:10px}
    #my-work-app .due{grid-area:due;justify-self:start;white-space:nowrap}
    #my-work-app .task-row>.flag{grid-area:flag;justify-self:start;max-width:100%;white-space:normal;line-height:1.2;text-align:left}
    #my-work-app .row-action{grid-area:action;width:58px;height:36px;align-self:center;justify-self:end;font-size:10px}
}
@media(max-width:600px){
    #my-work-app .load-state{align-items:flex-start;flex-direction:column}
    #my-work-app .loading-copy{margin-left:0}
    #my-work-app .task-row{
        grid-template-columns:minmax(0,1fr) 58px;
        grid-template-areas:
            "task action"
            "assignee action"
            "phase action"
            "status action"
            "due action"
            "flag action"
            "updated action";
        min-height:0;
        row-gap:7px;
        padding:12px 10px;
    }
    #my-work-app .status-select{width:min(100%,220px);justify-self:start}
    #my-work-app .row-action{align-self:start}
    #my-work-app .order-head{grid-template-columns:20px minmax(0,1fr) auto;column-gap:8px}
    #my-work-app .task-count{white-space:nowrap}
}

@media(prefers-reduced-motion:reduce){#my-work-app *,#my-work-app *::before,#my-work-app *::after{animation:none!important;transition:none!important}}

/* 2026-08-08: Slightly larger All Tasks typography for readability without changing the layout. */
.lang-btn{font-size:13px}
#my-work-app .page-head h1{font-size:25px}
#my-work-app .page-head p{font-size:12px}
#my-work-app .ft-board-tabs button{font-size:12px}
#my-work-app .page-tab{font-size:11px}
#my-work-app .metric small{font-size:10px}
#my-work-app .metric strong{font-size:20px}
#my-work-app .metric i{font-size:12px}
#my-work-app .search{font-size:11px}
#my-work-app .clear{font-size:9.5px}
#my-work-app .chip{font-size:10px}
#my-work-app .sort{font-size:10px}
#my-work-app .load-state{font-size:9.5px}
#my-work-app .task-head{font-size:9px}
#my-work-app .collapse{font-size:13px}
#my-work-app .order-id,#my-work-app .order-title{font-size:11px}
#my-work-app .order-client,#my-work-app .order-stage{font-size:10px}
#my-work-app .health,#my-work-app .flag{font-size:9px}
#my-work-app .order-progress,#my-work-app .task-count{font-size:9.5px}
#my-work-app .task-link{font-size:11px}
#my-work-app .task-ref{font-size:9px}
#my-work-app .phase,#my-work-app .assignee,#my-work-app .due{font-size:10px}
#my-work-app .status-select,#my-work-app .updated,#my-work-app .row-action{font-size:9.5px}
#my-work-app .empty{font-size:11px}
#my-work-app .empty strong{font-size:13px}
#my-work-app .footer,#my-work-app .page-button{font-size:10px}


/* 2026-08-10: All Tasks responsive alignment refinement.
   Keep the read-only assignee visible with the other task metadata on tablet
   and phone layouts without adding an inline assignee editor. */
@media(max-width:820px){
    #my-work-app .task-row{
        grid-template-columns:minmax(0,1.2fr) minmax(130px,.78fr) 60px;
        grid-template-areas:
            "task assignee action"
            "phase status action"
            "updated due action"
            "flag flag action";
        column-gap:12px;
        row-gap:8px;
        min-height:112px;
        padding:11px 12px;
        align-items:center;
    }
    #my-work-app .task-main{grid-area:task;align-self:center;min-width:0}
    #my-work-app .task-link{white-space:normal;overflow:visible;text-overflow:clip;line-height:1.25;overflow-wrap:anywhere}
    #my-work-app .phase{grid-area:phase;justify-self:start;min-width:0;max-width:100%;white-space:normal;overflow:visible;text-overflow:clip;overflow-wrap:anywhere}
    #my-work-app .assignee{grid-area:assignee;justify-self:start;max-width:100%}
    #my-work-app .updated{grid-area:updated;justify-self:start;align-self:center;white-space:nowrap}
    #my-work-app .status-select{grid-area:status;width:100%;min-width:0;max-width:none;height:36px;padding-right:28px;font-size:10px}
    #my-work-app .due{grid-area:due;justify-self:start;align-self:center;white-space:normal;line-height:1.25}
    #my-work-app .task-row>.flag{grid-area:flag;justify-self:start;align-self:center;max-width:100%;white-space:normal;line-height:1.2;text-align:left}
    #my-work-app .row-action{grid-area:action;width:58px;height:36px;align-self:center;justify-self:end;font-size:10px}
}
@media(max-width:600px){
    #my-work-app .task-row{
        grid-template-columns:minmax(0,1fr) 58px;
        grid-template-areas:
            "task action"
            "assignee action"
            "phase action"
            "status action"
            "due action"
            "flag action"
            "updated action";
        min-height:0;
        column-gap:12px;
        row-gap:8px;
        padding:12px 10px;
        align-items:start;
    }
    #my-work-app .task-main{align-self:start}
    #my-work-app .status-select{width:100%;max-width:none;justify-self:stretch}
    #my-work-app .row-action{align-self:start}
    #my-work-app .phase,#my-work-app .assignee,#my-work-app .due,#my-work-app .updated,#my-work-app .task-row>.flag{align-self:start}
}
@media(max-width:430px){
    #my-work-app .task-row{column-gap:9px;padding-inline:9px}
    #my-work-app .row-action{width:54px}
    #my-work-app .status-select{height:38px}
    #my-work-app .footer{gap:8px}
    #my-work-app .pages{gap:6px}
}
</style>

    <div class="page-head">
        <div>
            <h1>All Tasks</h1>
            <p><?php echo e($taskPackAdministratorView
                ? 'All active Job tasks, grouped by Order and ranked by what needs action first.'
                : 'Tasks from Jobs associated with your assigned work, grouped by Order and ranked by what needs action first.'); ?></p>
        </div>
    </div>

    <section class="work-view" aria-busy="false">
        <div class="metrics ft-summary-card-grid" aria-label="All Tasks summary filters">
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Created Today','value' => $taskPackMetrics['createdToday'] ?? 0,'valueExpression' => 'metrics.createdToday ?? \'—\'','icon' => 'created','tone' => 'blue','caption' => 'Tasks created today','active' => $taskQuick === 'createdToday','wire:click' => 'setTaskMetricFilter(\'createdToday\')','ariaPressed' => ''.e($taskQuick === 'createdToday' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Created Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['createdToday'] ?? 0),'value-expression' => 'metrics.createdToday ?? \'—\'','icon' => 'created','tone' => 'blue','caption' => 'Tasks created today','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'createdToday'),'wire:click' => 'setTaskMetricFilter(\'createdToday\')','aria-pressed' => ''.e($taskQuick === 'createdToday' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Not Started','value' => $taskPackMetrics['notStarted'] ?? 0,'valueExpression' => 'metrics.notStarted ?? \'—\'','icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => $taskQuick === 'notStarted','wire:click' => 'setTaskMetricFilter(\'notStarted\')','ariaPressed' => ''.e($taskQuick === 'notStarted' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Not Started','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['notStarted'] ?? 0),'value-expression' => 'metrics.notStarted ?? \'—\'','icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'notStarted'),'wire:click' => 'setTaskMetricFilter(\'notStarted\')','aria-pressed' => ''.e($taskQuick === 'notStarted' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'In Progress','value' => $taskPackMetrics['inProgress'] ?? 0,'valueExpression' => 'metrics.inProgress ?? \'—\'','icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => $taskQuick === 'inProgress','wire:click' => 'setTaskMetricFilter(\'inProgress\')','ariaPressed' => ''.e($taskQuick === 'inProgress' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['inProgress'] ?? 0),'value-expression' => 'metrics.inProgress ?? \'—\'','icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'inProgress'),'wire:click' => 'setTaskMetricFilter(\'inProgress\')','aria-pressed' => ''.e($taskQuick === 'inProgress' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Due This Week','value' => $taskPackMetrics['dueThisWeek'] ?? 0,'valueExpression' => 'metrics.dueThisWeek ?? \'—\'','icon' => 'due-week','tone' => 'amber','caption' => 'Tasks due this week','active' => $taskQuick === 'dueThisWeek','wire:click' => 'setTaskMetricFilter(\'dueThisWeek\')','ariaPressed' => ''.e($taskQuick === 'dueThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['dueThisWeek'] ?? 0),'value-expression' => 'metrics.dueThisWeek ?? \'—\'','icon' => 'due-week','tone' => 'amber','caption' => 'Tasks due this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'dueThisWeek'),'wire:click' => 'setTaskMetricFilter(\'dueThisWeek\')','aria-pressed' => ''.e($taskQuick === 'dueThisWeek' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Completed This Week','value' => $taskPackMetrics['completedThisWeek'] ?? 0,'valueExpression' => 'metrics.completedThisWeek ?? \'—\'','icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => $taskQuick === 'completedThisWeek','wire:click' => 'setTaskMetricFilter(\'completedThisWeek\')','ariaPressed' => ''.e($taskQuick === 'completedThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Completed This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['completedThisWeek'] ?? 0),'value-expression' => 'metrics.completedThisWeek ?? \'—\'','icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'completedThisWeek'),'wire:click' => 'setTaskMetricFilter(\'completedThisWeek\')','aria-pressed' => ''.e($taskQuick === 'completedThisWeek' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Needs Attention','value' => $taskPackMetrics['attention'] ?? 0,'valueExpression' => 'metrics.attention ?? \'—\'','icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => $taskQuick === 'attention','wire:click' => 'setTaskMetricFilter(\'attention\')','ariaPressed' => ''.e($taskQuick === 'attention' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskPackMetrics['attention'] ?? 0),'value-expression' => 'metrics.attention ?? \'—\'','icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskQuick === 'attention'),'wire:click' => 'setTaskMetricFilter(\'attention\')','aria-pressed' => ''.e($taskQuick === 'attention' ? 'true' : 'false').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $attributes = $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542)): ?>
<?php $component = $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542; ?>
<?php unset($__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542); ?>
<?php endif; ?>
        </div>

        <div class="toolbar ft-list-filter-bar">
            <div class="toolbar-primary">
                <label class="search-wrap">
                    <span class="search-icon">⌕</span>
                    <input class="search" type="search" wire:model.live.debounce.650ms="search" autocomplete="off" placeholder="Search tasks, Orders, clients, assignees or flags" aria-label="Search All Tasks">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search !== ''): ?><button class="clear" type="button" wire:click="clearTaskSearch">Clear</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="phase-filters" aria-label="Filter by Order workflow phase">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPackPhaseOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phaseOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button
                            type="button"
                            class="phase-toggle <?php echo e($taskPhaseFilter === $phaseOption ? 'active' : ''); ?>"
                            wire:click="setTaskPhaseFilter(<?php echo e(\Illuminate\Support\Js::from($phaseOption)); ?>)"
                            aria-pressed="<?php echo e($taskPhaseFilter === $phaseOption ? 'true' : 'false'); ?>"
                            title="<?php echo e($phaseOption); ?>"
                        >
                            <span class="phase-check" aria-hidden="true">✓</span>
                            <span><?php echo e($phaseOption); ?></span>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
            <div class="toolbar-secondary">
                <div class="quick-filters">
                    <button type="button" class="chip <?php echo e($taskQuick === 'mentions' ? 'active' : ''); ?>" wire:click="setTaskQuick('<?php echo e($taskQuick === 'mentions' ? 'all' : 'mentions'); ?>')">Mentions (<span x-text="metrics.mentions ?? '—'"><?php echo e($taskPackMetrics['mentions'] ?? '—'); ?></span>)</button>
                </div>
                <label class="completed-toggle <?php echo e($hideCompleted ? 'active' : ''); ?>">
                    <input type="checkbox" wire:model.live="hideCompleted" aria-label="Hide completed tasks">
                    <span class="completed-check" aria-hidden="true">✓</span>
                    <span>Hide completed</span>
                </label>
                <select class="sort" wire:model.live="taskSort" aria-label="Sort All Tasks">
                    <option value="action">Sort: Action priority</option>
                    <option value="due">Sort: Due soon</option>
                    <option value="job">Sort: Order number</option>
                </select>
                <button type="button" class="chip clear-filters" wire:click="clearFilters" <?php if($search === '' && $taskPhaseFilter === '' && $taskQuick === 'all' && $hideCompleted && $assignee === '' && $job === '' && $client === '' && $status === '' && $due === ''): echo 'disabled'; endif; ?>>Clear filters</button>
            </div>
        </div>

        <div class="load-state">
            <span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskPackPaginator && $taskPackPaginator->total()): ?>
                    Showing <?php echo e($taskPackGroups->count()); ?> of <?php echo e($taskPackPaginator->total()); ?> matching Orders · <?php echo e($taskPackTaskCount); ?> visible tasks
                <?php elseif($taskPackAdministratorView): ?>
                    Showing all active Job Task Packs
                <?php else: ?>
                    Showing associated Job Task Packs only
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <span class="load-actions">
                <span class="loading-copy">
                    <span wire:loading.remove wire:target="search,taskPhaseFilter,taskQuick,taskSort,hideCompleted,setTaskMetricFilter,setTaskPhaseFilter,setTaskQuick,clearFilters,clearTaskSearch,gotoPage,previousPage,nextPage">Results update after 650 ms</span>
                    <span wire:loading.delay.long wire:target="search,taskPhaseFilter,taskQuick,taskSort,hideCompleted,setTaskMetricFilter,setTaskPhaseFilter,setTaskQuick,clearFilters,clearTaskSearch,gotoPage,previousPage,nextPage"><i class="spinner"></i> Searching all visible work…</span>
                </span>
                <span class="group-controls" aria-label="Order group controls">
                    <button type="button" class="group-control" x-on:click="groupsExpanded = true" title="Expand all Orders" aria-label="Expand all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 6 5 5 5-5M5 11l5 5 5-5"/></svg>
                    </button>
                    <button type="button" class="group-control" x-on:click="groupsExpanded = false" title="Collapse all Orders" aria-label="Collapse all Orders">
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 14 5-5 5 5M5 9l5-5 5 5"/></svg>
                    </button>
                </span>
            </span>
        </div>

        <section class="list-shell" aria-label="All Tasks grouped by Order">
            <div class="task-head"><span>Task</span><span>Phase</span><span>Assignee</span><span>Due</span><span>Status</span><span>Flag</span><span>Updated</span><span>View</span></div>

            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $taskPackGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <article class="order-group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-order-'.e($group['id']).''; ?>wire:key="board-task-order-<?php echo e($group['id']); ?>" x-data="{ open: false }" x-effect="open = groupsExpanded">
                        <header class="order-head">
                            <button type="button" class="collapse" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" x-bind:aria-label="open ? 'Collapse Order' : 'Expand Order'"><span x-text="open ? '⌄' : '›'">›</span></button>
                            <span class="order-identity">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['route']): ?><a class="order-id" href="<?php echo e($group['route']); ?>" wire:navigate><?php echo e($group['number']); ?></a><?php else: ?><span class="order-id"><?php echo e($group['number']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="order-title"><?php echo e($group['title']); ?></span>
                            </span>
                            <span class="order-client"><?php echo e($group['client']); ?></span>
                            <span class="order-stage"><?php echo e($group['stage']); ?></span>
                            <span class="health <?php echo e($group['healthTone']); ?>"><?php echo e($group['health']); ?></span>
                            <span class="order-progress"><i class="progress-track"><i style="width:<?php echo e($group['progress']); ?>%"></i></i><?php echo e($group['progress']); ?>%</span>
                            <span class="task-count"><?php echo e($group['taskCount']); ?> <?php echo e($group['taskCount'] === 1 ? 'task' : 'tasks'); ?></span>
                        </header>

                        <div class="task-rows" x-cloak x-show="open">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div
                                    class="task-row"
                                    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'board-task-'.e($task['id']).''; ?>wire:key="board-task-<?php echo e($task['id']); ?>"
                                    x-data="{
                                        saving:false,
                                        version:<?php echo \Illuminate\Support\Js::from($task['version'])->toHtml() ?>,
                                        currentStatus:<?php echo \Illuminate\Support\Js::from($task['status'])->toHtml() ?>,
                                        async saveStatus(event){
                                            const select=event.currentTarget;
                                            const previous=this.currentStatus;
                                            const next=select.value;
                                            if(next===previous||this.saving)return;
                                            this.saving=true;
                                            select.disabled=true;
                                            try{
                                                const result=await $wire.updateTaskStatus(<?php echo e($task['id']); ?>,next,this.version);
                                                if(!result?.ok){select.value=previous;window.FlowTrackMasterColor?.applySelect(select);return;}
                                                this.currentStatus=result.status||next;
                                                this.version=result.version||this.version;
                                                if(result.metrics)window.dispatchEvent(new CustomEvent('board-task-metrics',{detail:result.metrics}));
                                                // Status saves are normally renderless for speed. When a task is
                                                // completed while Hide completed is active, refresh the grouped
                                                // list once so the completed row disappears immediately and the
                                                // Order disappears too when it no longer has any visible tasks.
                                                if(result.completed && <?php echo \Illuminate\Support\Js::from($hideCompleted)->toHtml() ?>)await $wire.$refresh();
                                            }catch(error){select.value=previous;window.FlowTrackMasterColor?.applySelect(select);}
                                            finally{this.saving=false;select.disabled=false;}
                                        }
                                    }"
                                    x-bind:class="{ 'saving': saving }"
                                >
                                    <div class="task-main">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?><a class="task-link" href="<?php echo e($task['route']); ?>" wire:navigate><?php echo e($task['title']); ?></a><?php else: ?><span class="task-link"><?php echo e($task['title']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="task-ref"><?php echo e($task['number']); ?></span>
                                    </div>
                                    <span class="phase ft-phase-color-label" style="<?php echo e(\App\Support\MasterColor::style($task['phaseColor'] ?? null)); ?>"><?php echo e($task['phase']); ?></span>
                                    <span class="assignee" title="<?php echo e($task['assignee']); ?>">
                                        <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task['assignee'],'src' => $task['assigneeImage'],'size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assignee']),'src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task['assigneeImage']),'size' => 22]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
                                        <span class="assignee-name"><?php echo e($task['assignee']); ?></span>
                                    </span>
                                    <time class="due <?php echo e($task['dueTone']); ?>"><?php echo e($task['due']); ?></time>
                                    <select data-master-color-select class="status-select <?php echo e($task['statusColor'] ? 'ft-master-color' : ''); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['statusColor'])); ?>" <?php if($task['canEdit']): ?> x-on:change="saveStatus($event); window.FlowTrackMasterColor?.applySelect($event.currentTarget)" <?php else: ?> disabled <?php endif; ?> aria-label="Status for <?php echo e($task['title']); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($task['status'], $taskPackStatusOptions, true)): ?><option value="<?php echo e($task['status']); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $task['status'])); ?>" selected><?php echo e($task['status']); ?></option><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $taskPackStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($statusOption); ?>" data-color="<?php echo e(app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $statusOption)); ?>" <?php if($statusOption === $task['status']): echo 'selected'; endif; ?>><?php echo e($statusOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <span class="flag <?php echo e($task['flagColor'] ? 'ft-master-color' : $task['flagTone']); ?>" style="<?php echo e(\App\Support\MasterColor::style($task['flagColor'])); ?>"><?php echo e($task['flag']); ?></span>
                                    <span class="updated"><?php echo e($task['updated']); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task['route']): ?><a class="row-action" href="<?php echo e($task['route']); ?>" wire:navigate>Open</a><?php else: ?><span class="row-action" aria-disabled="true">—</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="empty"><strong>No matching work</strong>Try another task, Order, client, assignee, or flag.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <footer class="footer">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taskPackPaginator && $taskPackPaginator->total()): ?>
                        Orders <?php echo e($taskPackPaginator->firstItem()); ?>–<?php echo e($taskPackPaginator->lastItem()); ?> of <?php echo e($taskPackPaginator->total()); ?> · <?php echo e($taskPackTaskCount); ?> tasks on this page
                    <?php elseif($taskPackAdministratorView): ?>
                        All active Job tasks
                    <?php else: ?>
                        Associated Job tasks
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php
                    $currentPage = $taskPackPaginator?->currentPage() ?? 1;
                    $lastPage = max(1, $taskPackPaginator?->lastPage() ?? 1);
                    $pageStart = max(1, $currentPage - 2);
                    $pageEnd = min($lastPage, $currentPage + 2);
                ?>
                <nav class="pages" aria-label="Pagination">
                    <button type="button" class="page-button" wire:click="previousPage('taskPackPage')" <?php if(!$taskPackPaginator || $taskPackPaginator->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button type="button" class="page-button <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>, 'taskPackPage')" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" class="page-button" wire:click="nextPage('taskPackPage')" <?php if(!$taskPackPaginator || !$taskPackPaginator->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                </nav>
            </footer>
        </section>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/board/index.blade.php ENDPATH**/ ?>