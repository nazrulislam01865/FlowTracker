<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['jobs', 'searchFilter' => '', 'clearAction' => 'clearSearch']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['jobs', 'searchFilter' => '', 'clearAction' => 'clearSearch']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $tone = static function (?string $value): string {
        $value = (string) $value;
        if (preg_match('/delayed|issue|overdue|blocked|attention/i', $value)) return 'red';
        if (preg_match('/risk|wait|reply|hold|revision|delay|due|urgent|high/i', $value)) return 'amber';
        if (preg_match('/track|ready|invoice|warehouse|shipping|completed/i', $value)) return 'green';
        if (preg_match('/artwork|sample|client/i', $value)) return 'purple';
        return 'blue';
    };
    $currentPage = $jobs->currentPage();
    $lastPage = $jobs->lastPage();
    $pageNumbers = collect(range(1, max(1, $lastPage)))
        ->filter(fn ($page) => $lastPage <= 7 || $page === 1 || $page === $lastPage || abs($page - $currentPage) <= 1)
        ->values();
?>

<div id="ft-orders-page" class="ft-orders-prototype">
    <?php if (! $__env->hasRenderedOnce('39b6e8be-9c6b-48fa-ba25-7d7c2631c4ad')): $__env->markAsRenderedOnce('39b6e8be-9c6b-48fa-ba25-7d7c2631c4ad'); ?>
        <style>
            .ft-orders-prototype{color-scheme:light;--navy:#0d1b2b;--navy-active:#22466f;--blue:#2463eb;--blue-soft:#edf3ff;--canvas:#f3f6fb;--surface:#fff;--line:#dbe3ed;--text:#172033;--muted:#62728a;--green:#147e5b;--green-soft:#edf9f4;--amber:#a56708;--amber-soft:#fff6e5;--red:#c43f3f;--red-soft:#fff0f0;--purple:#6f54cf;--purple-soft:#f1edff;width:100%;min-width:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
            .ft-orders-prototype button,.ft-orders-prototype input,.ft-orders-prototype a{font:inherit}.ft-orders-prototype button,.ft-orders-prototype a{-webkit-tap-highlight-color:transparent}.ft-orders-prototype button{cursor:pointer}.ft-orders-prototype a{color:inherit}
            .ft-orders-prototype .ft-page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:15px}.ft-orders-prototype .ft-page-head h1{margin:0 0 3px;font-size:23px;line-height:1.1;letter-spacing:-.025em}.ft-orders-prototype .ft-page-head p{margin:0;color:#60708b;font-size:11px}.ft-orders-prototype .ft-actions{display:flex;flex:0 0 auto;gap:8px}.ft-orders-prototype .ft-button{min-height:36px;padding:0 13px;border:1px solid #d4deec;border-radius:9px;background:#fff;color:#233047;font-size:10px;font-weight:750}.ft-orders-prototype .ft-button.primary{border-color:var(--blue);background:var(--blue);color:#fff;box-shadow:0 5px 13px rgba(36,99,235,.18)}.ft-orders-prototype .ft-button:hover{border-color:#9fb8e7}.ft-orders-prototype .ft-button.primary:hover{background:#1e57cf}
            .ft-orders-prototype .ft-list-shell{min-width:0;overflow:hidden;border:1px solid var(--line);border-radius:13px;background:var(--surface);box-shadow:0 1px 2px rgba(25,45,75,.02)}.ft-orders-prototype .ft-search-bar{display:flex;min-width:0;align-items:center;gap:10px;padding:11px 12px;border-bottom:1px solid var(--line)}.ft-orders-prototype .ft-search{position:relative;width:min(610px,100%);min-width:0}.ft-orders-prototype .ft-search-icon{position:absolute;left:12px;top:50%;color:#72829a;font-size:14px;transform:translateY(-50%);pointer-events:none}.ft-orders-prototype .ft-search input{width:100%;height:40px;padding:0 68px 0 36px;border:1px solid #cfdae9;border-radius:10px;outline:0;background:#fff;color:var(--text);font-size:11px}.ft-orders-prototype .ft-search input:focus{border-color:#7ba2f3;box-shadow:0 0 0 3px rgba(36,99,235,.1)}.ft-orders-prototype .ft-search-clear{position:absolute;right:9px;top:50%;display:none;padding:4px 6px;border:0;background:transparent;color:#5e6e85;font-size:9px;transform:translateY(-50%)}.ft-orders-prototype .ft-search-clear.show{display:block}.ft-orders-prototype .ft-search-state{display:flex;min-width:0;align-items:center;gap:7px;margin-left:auto;color:#64748b;font-size:9px;white-space:nowrap}.ft-orders-prototype .ft-live-dot{width:6px;height:6px;border-radius:50%;background:#20a36f}.ft-orders-prototype .ft-spinner{width:13px;height:13px;border:2px solid #dce5f2;border-top-color:var(--blue);border-radius:50%;animation:ft-orders-spin .6s linear infinite}
            .ft-orders-prototype .ft-job-head,.ft-orders-prototype .ft-job-row{display:grid;grid-template-columns:minmax(108px,.78fr) minmax(135px,.95fr) minmax(92px,.7fr) minmax(185px,1.45fr) minmax(92px,.72fr) 72px minmax(82px,.68fr) minmax(120px,.9fr) 78px 42px 32px;column-gap:8px;align-items:center;min-width:0}.ft-orders-prototype .ft-job-head{min-height:34px;padding:0 12px;border-bottom:1px solid var(--line);background:#f8fafc;color:#6c7a90;font-size:7.5px;font-weight:800;letter-spacing:.035em;text-transform:uppercase}.ft-orders-prototype .ft-job-list{min-width:0}.ft-orders-prototype .ft-job-row{position:relative;min-height:74px;padding:9px 12px;border-bottom:1px solid #e5eaf1;background:#fff;content-visibility:auto;contain-intrinsic-size:74px}.ft-orders-prototype .ft-job-row:last-child{border-bottom:0}.ft-orders-prototype .ft-job-row:hover{background:#fbfdff}.ft-orders-prototype .ft-cell{min-width:0}.ft-orders-prototype .ft-cell::before{display:none}.ft-orders-prototype .ft-id{display:inline-block;max-width:100%;overflow:hidden;color:#125be6;font-size:10px;font-weight:800;text-decoration:none;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-id:hover{text-decoration:underline}.ft-orders-prototype .ft-sub{display:block;max-width:100%;margin-top:3px;overflow:hidden;color:#77869b;font-size:8px;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-client{display:block;max-width:100%;overflow:hidden;color:#263248;font-size:9px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-product{display:block;max-width:100%;margin-top:3px;overflow:hidden;color:#526078;font-size:8px;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-product-detail{display:block;max-width:100%;margin-top:2px;overflow:hidden;color:#77869b;font-size:7.5px;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-created-name{display:block;overflow:hidden;color:#2d3950;font-size:9px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-created-on{display:block;margin-top:3px;color:#758399;font-size:8px}.ft-orders-prototype .ft-inquiry-link{display:inline-block;max-width:100%;overflow:hidden;color:#155ce9;font-size:8.5px;font-weight:750;text-decoration:none;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-inquiry-link:hover{text-decoration:underline}.ft-orders-prototype .ft-standard-empty{display:inline-block;color:#8995a6;font-size:8px;font-style:italic}.ft-orders-prototype .ft-chips{display:flex;max-width:100%;flex-wrap:wrap;gap:4px}.ft-orders-prototype .ft-pill{display:inline-flex;max-width:100%;align-items:center;padding:3px 6px;overflow:hidden;border-radius:9px;font-size:7.5px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-pill.blue{background:var(--blue-soft);color:#225fd4}.ft-orders-prototype .ft-pill.green{background:var(--green-soft);color:var(--green)}.ft-orders-prototype .ft-pill.amber{background:var(--amber-soft);color:var(--amber)}.ft-orders-prototype .ft-pill.red{background:var(--red-soft);color:var(--red)}.ft-orders-prototype .ft-pill.purple{background:var(--purple-soft);color:var(--purple)}.ft-orders-prototype .ft-owner{display:flex;min-width:0;align-items:center;gap:7px}.ft-orders-prototype .ft-owner-copy{min-width:0}.ft-orders-prototype .ft-owner-name{display:block;overflow:hidden;font-size:9px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.ft-orders-prototype .ft-due{display:block;margin-top:2px;color:#67768c;font-size:8px}.ft-orders-prototype .ft-due.overdue{color:var(--red);font-weight:750}.ft-orders-prototype .ft-order-avatar{display:grid;place-items:center;width:28px;height:28px;flex:0 0 28px;overflow:hidden;border-radius:50%;background:#dce8ff;color:#315da8;font-size:8px;font-weight:800}.ft-orders-prototype .ft-order-avatar img{width:100%;height:100%;object-fit:cover}.ft-orders-prototype .ft-progress{display:flex;align-items:center;gap:7px;color:#59677d;font-size:8px}.ft-orders-prototype .ft-progress-track{width:min(70px,70%);height:6px;overflow:hidden;border-radius:5px;background:#e8edf3}.ft-orders-prototype .ft-progress-fill{display:block;height:100%;border-radius:5px;background:var(--blue)}.ft-orders-prototype .ft-view{display:grid;place-items:center;width:34px;height:30px;border:1px solid #d0dbeb;border-radius:8px;background:#fff;color:#155ce9;font-size:8px;font-weight:800;text-decoration:none}.ft-orders-prototype .ft-view:hover{background:var(--blue-soft)}.ft-orders-prototype .ft-row-actions{display:flex;align-items:center;justify-content:flex-end}.ft-orders-prototype .ft-row-action-trigger{display:grid;place-items:center;width:30px;height:30px;padding:0;border:0;border-radius:7px;background:transparent;color:#5f6f85;font-size:18px;font-weight:800;line-height:1;transition:background .15s ease,color .15s ease}.ft-orders-prototype .ft-row-action-trigger:hover,.ft-orders-prototype .ft-row-action-trigger[aria-expanded="true"]{background:#f1f5fa;color:#1e2c43;box-shadow:none}.ft-orders-prototype .ft-row-action-menu[popover]{position:fixed;inset:auto;width:164px;margin:0;padding:5px;border:1px solid #d9e2ed;border-radius:10px;background:#fff;box-shadow:0 14px 34px rgba(30,48,73,.18);z-index:2147483000}.ft-orders-prototype .ft-row-action-menu[popover]::backdrop{background:transparent}.ft-orders-prototype .ft-row-action-menu button{display:flex;width:100%;align-items:center;gap:8px;padding:9px 10px;border:0;border-radius:7px;background:transparent;color:#bf3131;font-size:10px;font-weight:750;text-align:left}.ft-orders-prototype .ft-row-action-menu button:hover,.ft-orders-prototype .ft-row-action-menu button:focus-visible{background:#fff0f0;outline:0}.ft-orders-prototype .ft-row-action-menu svg{width:14px;height:14px;flex:0 0 14px;fill:none;stroke:currentColor;stroke-width:1.8}.ft-orders-prototype .ft-list-flash{margin-bottom:10px;padding:9px 12px;border:1px solid #bfe7d5;border-radius:9px;background:#effaf5;color:#147e5b;font-size:10px;font-weight:700}.ft-orders-prototype .ft-empty{padding:42px 16px;color:#65748b;text-align:center;font-size:11px}.ft-orders-prototype .ft-empty strong{display:block;margin-bottom:5px;color:#273349;font-size:13px}
            .ft-orders-prototype .ft-list-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;min-height:55px;padding:9px 12px;border-top:1px solid var(--line);background:#fbfcfe}.ft-orders-prototype .ft-result-count{color:#66758c;font-size:9px}.ft-orders-prototype .ft-page-buttons{display:flex;align-items:center;gap:5px}.ft-orders-prototype .ft-page-button{min-width:76px;min-height:34px;border:1px solid #cfdbea;border-radius:9px;background:#fff;color:#23436e;font-size:9px;font-weight:750}.ft-orders-prototype .ft-page-button:hover{border-color:#8facdf;background:var(--blue-soft)}.ft-orders-prototype .ft-page-button[disabled]{cursor:default;opacity:.45}.ft-orders-prototype .ft-page-number{width:34px;min-width:34px;height:34px;border:1px solid #cfdbea;border-radius:8px;background:#fff;color:#40506a;font-size:9px;font-weight:750}.ft-orders-prototype .ft-page-number.active{border-color:var(--blue);background:var(--blue);color:#fff}.ft-orders-prototype .ft-page-ellipsis{color:#74839a;font-size:9px}.ft-orders-prototype .ft-load-skeleton{display:none;gap:7px;padding:8px 12px;border-top:1px solid var(--line)}.ft-orders-prototype .ft-skeleton-line{height:11px;border-radius:5px;background:linear-gradient(90deg,#edf1f6 25%,#f8fafc 45%,#edf1f6 65%);background-size:240% 100%;animation:ft-orders-shimmer 1s linear infinite}.ft-orders-prototype .ft-skeleton-line:nth-child(2){width:82%}
            
            /* Typography alignment with Dashboard / My Task. */
            .ft-orders-prototype .ft-page-head h1{font-size:25px}.ft-orders-prototype .ft-page-head p{font-size:12px}.ft-orders-prototype .ft-button{font-size:11.5px}.ft-orders-prototype .ft-search input{font-size:11px}.ft-orders-prototype .ft-search-clear,.ft-orders-prototype .ft-search-state{font-size:10px}
            .ft-orders-prototype .ft-job-head{font-size:9px}.ft-orders-prototype .ft-id{font-size:11px}.ft-orders-prototype .ft-client,.ft-orders-prototype .ft-created-name,.ft-orders-prototype .ft-owner-name{font-size:10.5px}.ft-orders-prototype .ft-sub,.ft-orders-prototype .ft-product,.ft-orders-prototype .ft-created-on{font-size:9.5px}.ft-orders-prototype .ft-product-detail{font-size:9px}.ft-orders-prototype .ft-inquiry-link{font-size:10px}.ft-orders-prototype .ft-standard-empty{font-size:9.5px}.ft-orders-prototype .ft-pill{font-size:9px}.ft-orders-prototype .ft-due,.ft-orders-prototype .ft-progress{font-size:9.5px}.ft-orders-prototype .ft-order-avatar{font-size:9.5px}.ft-orders-prototype .ft-view{font-size:9.5px}.ft-orders-prototype .ft-result-count,.ft-orders-prototype .ft-page-button,.ft-orders-prototype .ft-page-number,.ft-orders-prototype .ft-page-ellipsis{font-size:10px}
            @keyframes ft-orders-spin{to{transform:rotate(360deg)}}@keyframes ft-orders-shimmer{to{background-position:-240% 0}}@keyframes ft-orders-reveal{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}.ft-orders-prototype .ft-job-row{animation:ft-orders-reveal .22s ease both}.ft-orders-prototype .ft-job-row:nth-child(2){animation-delay:.025s}.ft-orders-prototype .ft-job-row:nth-child(3){animation-delay:.05s}.ft-orders-prototype .ft-job-row:nth-child(4){animation-delay:.075s}.ft-orders-prototype .ft-job-row:nth-child(5){animation-delay:.1s}
            @media(max-width:1380px){.ft-orders-prototype .ft-job-head{display:none}.ft-orders-prototype .ft-job-row{grid-template-columns:minmax(140px,1fr) minmax(125px,.8fr) minmax(130px,.9fr) 42px 32px;grid-template-areas:"identity created inquiry view actions" "brief brief owner progress progress" "stage health flag progress progress";row-gap:8px;min-height:128px}.ft-orders-prototype .ft-job-row .ft-cell::before{content:attr(data-label);display:block;margin-bottom:3px;color:#8793a5;font-size:7px;font-weight:750;letter-spacing:.04em;text-transform:uppercase}.ft-orders-prototype .ft-identity{grid-area:identity}.ft-orders-prototype .ft-created-cell{grid-area:created}.ft-orders-prototype .ft-inquiry-cell{grid-area:inquiry}.ft-orders-prototype .ft-brief{grid-area:brief}.ft-orders-prototype .ft-stage-cell{grid-area:stage}.ft-orders-prototype .ft-health-cell{grid-area:health}.ft-orders-prototype .ft-flag-cell{grid-area:flag}.ft-orders-prototype .ft-owner-cell{grid-area:owner}.ft-orders-prototype .ft-progress-cell{grid-area:progress}.ft-orders-prototype .ft-view{grid-area:view}.ft-orders-prototype .ft-row-actions{grid-area:actions}.ft-orders-prototype .ft-progress-cell{justify-self:end;width:100%}.ft-orders-prototype .ft-progress-track{flex:1}}
            @media(max-width:820px){.ft-orders-prototype .ft-page-head{align-items:flex-start}.ft-orders-prototype .ft-page-head p{max-width:420px}.ft-orders-prototype .ft-search-bar{align-items:stretch;flex-wrap:wrap}.ft-orders-prototype .ft-search{width:100%}.ft-orders-prototype .ft-search-state{width:100%;margin-left:0}}
            @media(max-width:680px){.ft-orders-prototype .ft-page-head{align-items:flex-start}.ft-orders-prototype .ft-page-head h1{font-size:20px}.ft-orders-prototype .ft-page-head p{font-size:10px}.ft-orders-prototype .ft-button{min-height:34px;padding-inline:11px}.ft-orders-prototype .ft-job-row{grid-template-columns:minmax(0,1fr) minmax(105px,.7fr) 38px 32px;grid-template-areas:"identity identity view actions" "created inquiry inquiry inquiry" "brief brief brief brief" "stage health health health" "flag flag flag flag" "owner progress progress progress";padding:11px;row-gap:9px;min-height:228px}.ft-orders-prototype .ft-view{justify-self:end}.ft-orders-prototype .ft-progress-cell{max-width:145px}.ft-orders-prototype .ft-list-footer{align-items:flex-start;flex-direction:column}.ft-orders-prototype .ft-page-buttons{width:100%}.ft-orders-prototype .ft-page-button{flex:1}}
            @media(max-width:390px){.ft-orders-prototype .ft-page-head p{display:none}.ft-orders-prototype .ft-search input{padding-right:54px}}
            /* 2026-08-09 mobile order-list correction: compact cards with predictable columns. */
            @media(max-width:820px){
                .ft-orders-prototype .ft-page-head{margin-bottom:13px}
                .ft-orders-prototype .ft-page-head h1{font-size:22px}
                .ft-orders-prototype .ft-page-head p{font-size:11px;line-height:1.35}
                .ft-orders-prototype .ft-list-shell{border-radius:12px}
                .ft-orders-prototype .ft-search-bar{gap:8px;padding:11px}
                .ft-orders-prototype .ft-search input{height:42px;font-size:11.5px}
                .ft-orders-prototype .ft-search-state{gap:6px;font-size:9.5px;line-height:1.3;white-space:normal}
            }
            @media(max-width:680px){
                .ft-orders-prototype .ft-page-head{align-items:flex-start;gap:10px}
                .ft-orders-prototype .ft-page-head h1{font-size:21px}
                .ft-orders-prototype .ft-page-head p{display:block;max-width:245px;font-size:10.5px}
                .ft-orders-prototype .ft-actions{padding-top:1px}
                .ft-orders-prototype .ft-dashboard-action-match{min-height:36px!important;padding:0 12px!important;font-size:10px!important}
                .ft-orders-prototype .ft-job-row{
                    position:relative;
                    grid-template-columns:repeat(3,minmax(0,1fr));
                    grid-template-areas:
                        "identity identity identity"
                        "created created inquiry"
                        "brief brief brief"
                        "stage health flag"
                        "owner owner owner"
                        "progress progress progress";
                    gap:10px 9px;
                    min-height:0;
                    padding:12px;
                    padding-right:12px;
                }
                .ft-orders-prototype .ft-job-row .ft-cell::before{margin-bottom:4px;font-size:7.8px;line-height:1.15}
                .ft-orders-prototype .ft-identity{padding-right:54px}
                .ft-orders-prototype .ft-id{font-size:12.5px;line-height:1.2}
                .ft-orders-prototype .ft-sub{font-size:10px;line-height:1.3}
                .ft-orders-prototype .ft-created-name,.ft-orders-prototype .ft-client,.ft-orders-prototype .ft-owner-name{font-size:11px;line-height:1.3}
                .ft-orders-prototype .ft-created-on,.ft-orders-prototype .ft-product,.ft-orders-prototype .ft-product-detail,.ft-orders-prototype .ft-due,.ft-orders-prototype .ft-progress,.ft-orders-prototype .ft-standard-empty{font-size:9.8px;line-height:1.35}
                .ft-orders-prototype .ft-product-detail{white-space:normal;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;line-clamp:2}
                .ft-orders-prototype .ft-pill{max-width:100%;padding:4px 7px;font-size:9px}
                .ft-orders-prototype .ft-stage-cell,.ft-orders-prototype .ft-health-cell,.ft-orders-prototype .ft-flag-cell{align-self:start}
                .ft-orders-prototype .ft-owner{gap:8px}
                .ft-orders-prototype .ft-order-avatar{width:30px;height:30px;flex-basis:30px;font-size:9.5px}
                .ft-orders-prototype .ft-progress-cell{justify-self:stretch;width:100%;max-width:none;gap:8px}
                .ft-orders-prototype .ft-progress-track{width:auto;max-width:none;flex:1;height:6px}
                .ft-orders-prototype .ft-view{position:absolute;top:11px;right:11px;width:40px;height:32px;font-size:9.5px}
                .ft-orders-prototype .ft-list-footer{gap:10px;padding:10px 11px}
                .ft-orders-prototype .ft-result-count{font-size:9.5px}
                .ft-orders-prototype .ft-page-buttons{gap:5px}
                .ft-orders-prototype .ft-page-button{min-width:0;min-height:34px;font-size:9.5px}
                .ft-orders-prototype .ft-page-number{width:34px;min-width:34px;height:34px;font-size:9.5px}
            }
            @media(max-width:600px){
                .ft-orders-prototype .ft-list-footer{flex-direction:column;align-items:stretch;justify-content:flex-start;gap:9px}
                .ft-orders-prototype .ft-result-count{width:100%;line-height:1.35}
                .ft-orders-prototype .ft-list-footer>nav.ft-page-buttons{display:grid;grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);width:100%;gap:7px}
                .ft-orders-prototype .ft-list-footer>nav.ft-page-buttons>.ft-page-button{width:100%;min-width:0;padding:0 8px}
                .ft-orders-prototype .ft-list-footer>nav.ft-page-buttons>span.ft-page-buttons{min-width:0;max-width:100%;justify-content:center;overflow-x:auto;overscroll-behavior-inline:contain;scrollbar-width:none}
                .ft-orders-prototype .ft-list-footer>nav.ft-page-buttons>span.ft-page-buttons::-webkit-scrollbar{display:none}
            }
            @media(max-width:430px){
                .ft-orders-prototype .ft-page-head{gap:8px}
                .ft-orders-prototype .ft-page-head h1{font-size:20px}
                .ft-orders-prototype .ft-page-head p{max-width:210px;font-size:10px}
                .ft-orders-prototype .ft-dashboard-action-match{min-height:34px!important;padding:0 10px!important;font-size:9.5px!important}
                .ft-orders-prototype .ft-search input{height:40px;padding-left:34px;padding-right:50px;font-size:11px}
                .ft-orders-prototype .ft-search-icon{left:11px}
                .ft-orders-prototype .ft-search-state{font-size:9px}
                .ft-orders-prototype .ft-job-row{gap:9px 7px;padding:11px}
                .ft-orders-prototype .ft-job-row .ft-cell::before{font-size:7.4px}
                .ft-orders-prototype .ft-id{font-size:12px}
                .ft-orders-prototype .ft-created-name,.ft-orders-prototype .ft-client,.ft-orders-prototype .ft-owner-name{font-size:10.5px}
                .ft-orders-prototype .ft-sub,.ft-orders-prototype .ft-created-on,.ft-orders-prototype .ft-product,.ft-orders-prototype .ft-product-detail,.ft-orders-prototype .ft-due,.ft-orders-prototype .ft-progress,.ft-orders-prototype .ft-standard-empty{font-size:9.3px}
                .ft-orders-prototype .ft-pill{font-size:8.5px;padding:4px 6px}
                .ft-orders-prototype .ft-view{top:10px;right:10px;width:38px;height:31px}
                .ft-orders-prototype .ft-list-footer{align-items:stretch}
                .ft-orders-prototype .ft-result-count{text-align:left}
                .ft-orders-prototype .ft-list-footer>nav.ft-page-buttons{grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);gap:5px}
                .ft-orders-prototype .ft-list-footer>nav.ft-page-buttons>.ft-page-button{min-height:36px;font-size:9px}
                .ft-orders-prototype .ft-page-number{width:32px;min-width:32px;height:36px}
            }
            @media(prefers-reduced-motion:reduce){.ft-orders-prototype *,.ft-orders-prototype *::before,.ft-orders-prototype *::after{scroll-behavior:auto!important;animation:none!important;transition:none!important}}
        </style>
    <?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="ft-list-flash" role="status"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-page-head">
        <div><h1>Orders</h1><p>Fast access to every active and completed order</p></div>
        <div class="ft-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('jobs.create')): ?>
                <a class="ft-new-job-btn ft-dashboard-action-match" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate><span class="ft-dashboard-action-match-icon">+</span>New Order</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <section class="ft-list-shell" aria-label="Orders list">
        <div class="ft-search-bar">
            <label class="ft-search">
                <span class="ft-search-icon">⌕</span>
                <input
                    type="search"
                    autocomplete="off"
                    placeholder="Search order, inquiry, client, product, creator or owner"
                    aria-label="Search orders"
                    wire:model.live.debounce.700ms="search"
                >
                <button class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-search-clear','show'=>filled($searchFilter)]); ?>" wire:click="<?php echo e($clearAction); ?>" type="button">Clear</button>
            </label>
            <div class="ft-search-state">
                <i class="ft-spinner" wire:loading wire:target="search,gotoPage,previousPage,nextPage" aria-hidden="true"></i>
                <span wire:loading.remove wire:target="search,gotoPage,previousPage,nextPage">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($searchFilter) && mb_strlen(trim((string) $searchFilter)) < 3): ?>
                        Type at least 3 characters to search · showing all <?php echo e(number_format($jobs->total())); ?> orders
                    <?php elseif(filled($searchFilter)): ?>
                        <?php echo e(number_format($jobs->total())); ?> <?php echo e(\Illuminate\Support\Str::plural('order', $jobs->total())); ?> found for “<?php echo e($searchFilter); ?>”
                    <?php else: ?>
                        Type to search all <?php echo e(number_format($jobs->total())); ?> orders · results update after 700 ms
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span wire:loading wire:target="search,gotoPage,previousPage,nextPage">Searching orders…</span>
                <i class="ft-live-dot" aria-hidden="true"></i>
            </div>
        </div>

        <div class="ft-job-head" aria-hidden="true">
            <span>Created by / on</span><span>Order</span><span>Inquiry</span><span>Client / Products</span><span>Order stage</span><span>Health</span><span>Flag</span><span>Owner / Delivery</span><span>Progress</span><span>View</span><span aria-label="Actions"></span>
        </div>

        <div class="ft-job-list">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $creator = $job->createdActivity?->user ?? $job->owner;
                    $creatorName = $creator?->name ?? 'System';
                    $ownerName = $job->owner?->name ?? 'Unassigned';
                    $ownerInitials = collect(preg_split('/\\s+/', trim($ownerName)))->filter()->map(fn($part)=>mb_substr($part,0,1))->take(2)->implode('');
                    $ownerImage = $job->owner?->profile_image_path && $job->owner?->id
                        ? route('profile-images.show', ['user'=>$job->owner->id,'filename'=>basename($job->owner->profile_image_path)], false)
                        : null;
                    $items = $job->items;
                    $productRows = $items->isNotEmpty()
                        ? $items
                        : collect([(object)['product_name'=>$job->product ?: 'Product','quantity'=>(int)$job->quantity]]);
                    $totalUnits = (int) $productRows->sum(fn($item)=>(int)($item->quantity ?? 0));
                    $productNames = $productRows->pluck('product_name')->filter()->values();
                    $health = $job->completed_at ? 'Completed' : ($job->needs_attention ? 'Needs Attention' : ($job->health ?: 'On Track'));
                    $stage = $job->phase?->short_name ?? $job->status ?? '—';
                    $flag = $job->needs_attention
                        ? (app(\App\Services\TaskFlagService::class)->labelForOrder($job) ?: 'Management attention')
                        : (in_array($job->priority, ['Urgent','High'], true) ? $job->priority : null);
                    $deliveryOverdue = $job->delivery_date && !$job->completed_at && \App\Support\UserLocalTime::isDatePast($job->delivery_date);
                ?>
                <article class="ft-job-row" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-row-'.e($job->id).''; ?>wire:key="order-row-<?php echo e($job->id); ?>">
                    <div class="ft-cell ft-created-cell" data-label="Created by / on"><span class="ft-created-name"><?php echo e($creatorName); ?></span><time class="ft-created-on"><?php echo e($job->created_at?->format('M j, Y') ?? '—'); ?></time></div>
                    <div class="ft-cell ft-identity" data-label="Order"><a class="ft-id" href="<?php echo e(route('jobs.index',['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a><span class="ft-sub"><?php echo e($job->order_number ?: 'REF-'.str_pad((string)$job->id,5,'0',STR_PAD_LEFT)); ?></span></div>
                    <div class="ft-cell ft-inquiry-cell" data-label="Inquiry">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->sourceInquiry): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('inquiries.view')): ?>
                                <a class="ft-id" href="<?php echo e(route('inquiries.index', ['open' => $job->sourceInquiry->id])); ?>" wire:navigate><?php echo e($job->sourceInquiry->inquiry_number); ?></a>
                            <?php else: ?>
                                <span class="ft-client"><?php echo e($job->sourceInquiry->inquiry_number); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="ft-sub"><?php echo e($job->sourceInquiry->reference_number ?: 'Source inquiry'); ?></span>
                        <?php else: ?>
                            <span class="ft-standard-empty">Not linked</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-cell ft-brief" data-label="Client / products">
                        <span class="ft-client"><?php echo e($job->client?->name ?? '—'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productRows->count() === 1): ?>
                            <span class="ft-product"><?php echo e($productNames->first() ?: 'Product'); ?></span>
                            <span class="ft-product-detail"><?php echo e(number_format($totalUnits)); ?> <?php echo e(\Illuminate\Support\Str::plural('pc', $totalUnits)); ?></span>
                        <?php else: ?>
                            <span class="ft-product"><?php echo e($productRows->count()); ?> ordered products · <?php echo e(number_format($totalUnits)); ?> pcs</span>
                            <span class="ft-product-detail" title="<?php echo e($productNames->implode(' · ')); ?>"><?php echo e($productNames->implode(' · ')); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-cell ft-stage-cell" data-label="Order stage"><span class="ft-pill <?php echo e($tone($stage)); ?>"><?php echo e($stage); ?></span></div>
                    <div class="ft-cell ft-health-cell" data-label="Health"><span class="ft-pill <?php echo e($tone($health)); ?>"><?php echo e($health); ?></span></div>
                    <div class="ft-cell ft-flag-cell" data-label="Flag"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flag): ?><span class="ft-pill <?php echo e($tone($flag)); ?>"><?php echo e($flag); ?></span><?php else: ?><span class="ft-standard-empty">No flag</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="ft-cell ft-owner-cell" data-label="Owner / delivery">
                        <div class="ft-owner">
                            <span class="ft-order-avatar"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ownerImage): ?><img src="<?php echo e($ownerImage); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><?php echo e($ownerInitials ?: 'FT'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                            <span class="ft-owner-copy"><span class="ft-owner-name"><?php echo e($ownerName); ?></span><time class="ft-due <?php echo e($deliveryOverdue ? 'overdue' : ''); ?>"><?php echo e($job->delivery_date ? 'Due '.$job->delivery_date->format('M j') : 'No delivery date'); ?></time></span>
                        </div>
                    </div>
                    <div class="ft-cell ft-progress ft-progress-cell" data-label="Progress"><span class="ft-progress-track"><span class="ft-progress-fill" style="width:<?php echo e(max(0,min(100,(int)$job->progress))); ?>%"></span></span><span><?php echo e((int)$job->progress); ?>%</span></div>
                    <a class="ft-view" href="<?php echo e(route('jobs.index',['open'=>$job->id])); ?>" wire:navigate aria-label="View <?php echo e($job->displayOrderNumber()); ?>">View</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs', 'delete')): ?>
                        <div class="ft-row-actions" data-label="Actions" x-data="{ open: false }">
                            <button
                                class="ft-row-action-trigger"
                                type="button"
                                :aria-expanded="open ? 'true' : 'false'"
                                aria-haspopup="menu"
                                aria-controls="order-actions-<?php echo e($job->id); ?>"
                                aria-label="Actions for <?php echo e($job->displayOrderNumber()); ?>"
                                x-on:click.stop="
                                    const menu = $refs.menu;
                                    if (menu.matches(':popover-open')) { menu.hidePopover(); return; }
                                    const rect = $el.getBoundingClientRect();
                                    const menuWidth = 164;
                                    const menuHeight = 46;
                                    const edge = 10;
                                    const gap = 6;
                                    const left = Math.min(window.innerWidth - menuWidth - edge, Math.max(edge, rect.right - menuWidth));
                                    const openAbove = (window.innerHeight - rect.bottom) < (menuHeight + gap + edge) && rect.top > (menuHeight + gap + edge);
                                    const top = openAbove ? rect.top - menuHeight - gap : rect.bottom + gap;
                                    menu.style.left = `${left}px`;
                                    menu.style.top = `${Math.max(edge, top)}px`;
                                    menu.showPopover();
                                "
                            >⋮</button>
                            <div
                                id="order-actions-<?php echo e($job->id); ?>"
                                class="ft-row-action-menu"
                                x-ref="menu"
                                popover="auto"
                                role="menu"
                                x-on:toggle="open = $event.newState === 'open'"
                            >
                                <button type="button" role="menuitem" wire:click="deleteOrder(<?php echo e($job->id); ?>)" wire:confirm="Delete <?php echo e($job->displayOrderNumber()); ?>? This removes the order from active lists.">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                                    <span>Delete order</span>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <span aria-hidden="true"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-empty"><strong>No matching orders</strong>Try another order, inquiry, client, product, creator or owner.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ft-load-skeleton" wire:loading.delay.grid wire:target="search,gotoPage,previousPage,nextPage" aria-hidden="true"><span class="ft-skeleton-line"></span><span class="ft-skeleton-line"></span></div>

        <div class="ft-list-footer">
            <span class="ft-result-count"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jobs->total()): ?> Showing <?php echo e($jobs->firstItem()); ?>–<?php echo e($jobs->lastItem()); ?> of <?php echo e(number_format($jobs->total())); ?> orders <?php else: ?> No orders found <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
            <nav class="ft-page-buttons" aria-label="Orders pagination">
                <button class="ft-page-button" type="button" wire:click="previousPage" <?php if($jobs->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                <span class="ft-page-buttons">
                    <?php $previousRenderedPage = null; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pageNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previousRenderedPage !== null && $pageNumber - $previousRenderedPage > 1): ?><span class="ft-page-ellipsis">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button type="button" class="ft-page-number <?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>)" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                        <?php $previousRenderedPage = $pageNumber; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </span>
                <button class="ft-page-button" type="button" wire:click="nextPage" <?php if(!$jobs->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
            </nav>
        </div>
    </section>

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/table.blade.php ENDPATH**/ ?>