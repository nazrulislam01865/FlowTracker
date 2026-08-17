<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'jobs',
    'searchFilter' => '',
    'clientFilter' => '',
    'phaseFilter' => '',
    'assigneeFilter' => '',
    'ownerFilter' => null,
    'metrics' => [],
    'metricFilter' => '',
    'dateFrom' => '',
    'dateTo' => '',
    'importFilterId' => 0,
    'importFilterLabel' => '',
    'dateRangeEnabled' => false,
    'clientFilterOptions' => collect(),
    'phaseFilterOptions' => collect(),
    'assigneeFilterOptions' => collect(),
    'ownerFilterOptions' => collect(),
    'clearAction' => 'clearSearch',
    'clearFiltersAction' => null,
    'selectedOrderIds' => [],
    'showBulkDeleteConfirm' => false,
]));

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

foreach (array_filter(([
    'jobs',
    'searchFilter' => '',
    'clientFilter' => '',
    'phaseFilter' => '',
    'assigneeFilter' => '',
    'ownerFilter' => null,
    'metrics' => [],
    'metricFilter' => '',
    'dateFrom' => '',
    'dateTo' => '',
    'importFilterId' => 0,
    'importFilterLabel' => '',
    'dateRangeEnabled' => false,
    'clientFilterOptions' => collect(),
    'phaseFilterOptions' => collect(),
    'assigneeFilterOptions' => collect(),
    'ownerFilterOptions' => collect(),
    'clearAction' => 'clearSearch',
    'clearFiltersAction' => null,
    'selectedOrderIds' => [],
    'showBulkDeleteConfirm' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $masterData = app(\App\Services\MasterDataService::class);
    $tone = static function (?string $value): string {
        $value = (string) $value;
        if (preg_match('/delayed|issue|overdue|blocked|attention|critical/i', $value)) return 'red';
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
    $usesOwnerFilter = $ownerFilter !== null;
    $peopleFilter = $usesOwnerFilter ? (string) $ownerFilter : (string) $assigneeFilter;
    $peopleFilterOptions = $usesOwnerFilter ? $ownerFilterOptions : $assigneeFilterOptions;
    $peopleProperty = $usesOwnerFilter ? 'owner' : 'assignee';
    $peopleContext = $usesOwnerFilter ? 'order-list-owner' : 'order-list';
    $peopleLabel = $usesOwnerFilter ? 'Owner' : 'Assignee';
    $peoplePlaceholder = $usesOwnerFilter ? 'All owner' : 'All assignees';
    $accessControl = app(\App\Services\AccessControlService::class);
    $canViewFinance = $accessControl->can(auth()->user(), 'finance', 'view');
    $canDeleteOrders = auth()->user()->canModule('jobs', 'delete');
    $selectedOrderIdSet = collect($selectedOrderIds)->map(fn ($id) => (int) $id)->filter()->unique();
    $visibleOrderIds = collect($jobs->items())->pluck('id')->map(fn ($id) => (int) $id)->values();
    $selectedOrderCount = $selectedOrderIdSet->count();
    $selectedVisibleCount = $visibleOrderIds->filter(fn ($id) => $selectedOrderIdSet->contains($id))->count();
    $allVisibleOrdersSelected = $visibleOrderIds->isNotEmpty() && $selectedVisibleCount === $visibleOrderIds->count();
    $someVisibleOrdersSelected = $selectedVisibleCount > 0 && ! $allVisibleOrdersSelected;
?>

<div id="ft-orders-page" class="ft-orders-prototype">
    <?php if (! $__env->hasRenderedOnce('3bb41ca0-41cd-44ea-8d57-3050f088358d')): $__env->markAsRenderedOnce('3bb41ca0-41cd-44ea-8d57-3050f088358d'); ?>
        <style>
            .ft-orders-prototype{color-scheme:light;--navy:#0d1b2b;--navy-active:#22466f;--blue:#2463eb;--blue-soft:#edf3ff;--canvas:#f3f6fb;--surface:#fff;--line:#dbe3ed;--text:#172033;--muted:#62728a;--green:#147e5b;--green-soft:#edf9f4;--amber:#a56708;--amber-soft:#fff6e5;--red:#c43f3f;--red-soft:#fff0f0;--purple:#6f54cf;--purple-soft:#f1edff;width:100%;min-width:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
            .ft-orders-prototype button,.ft-orders-prototype input,.ft-orders-prototype a{font:inherit}.ft-orders-prototype button,.ft-orders-prototype a{-webkit-tap-highlight-color:transparent}.ft-orders-prototype button{cursor:pointer}.ft-orders-prototype a{color:inherit}
            .ft-orders-prototype .ft-page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:15px}.ft-orders-prototype .ft-page-head h1{margin:0 0 3px;font-size:23px;line-height:1.1;letter-spacing:-.025em}.ft-orders-prototype .ft-page-head p{margin:0;color:#60708b;font-size:11px}.ft-orders-prototype .ft-actions{display:flex;flex:0 0 auto;align-items:center;gap:8px}.ft-orders-prototype .ft-button{min-height:36px;padding:0 13px;border:1px solid #d4deec;border-radius:9px;background:#fff;color:#233047;font-size:10px;font-weight:750}.ft-orders-prototype .ft-button.primary{border-color:var(--blue);background:var(--blue);color:#fff;box-shadow:0 5px 13px rgba(36,99,235,.18)}.ft-orders-prototype .ft-button:hover{border-color:#9fb8e7}.ft-orders-prototype .ft-button.primary:hover{background:#1e57cf}.ft-orders-prototype .ft-bulk-import-button{display:inline-flex;height:34px;min-height:34px;align-items:center;justify-content:center;gap:6px;padding:0 11px;border-radius:9px;font-size:9px;font-weight:650;line-height:1;text-decoration:none;white-space:nowrap;box-sizing:border-box}.ft-orders-prototype .ft-import-batch-filter{display:flex;min-height:35px;align-items:center;gap:8px;padding:6px 10px;border-bottom:1px solid #d8e3f2;background:#f3f7ff;color:#40516b;font-size:9.5px}.ft-orders-prototype .ft-import-batch-filter-dot{width:7px;height:7px;flex:0 0 7px;border-radius:999px;background:#2463eb;box-shadow:0 0 0 3px rgba(36,99,235,.1)}.ft-orders-prototype .ft-import-batch-filter strong{color:#1d4ed8;font-weight:800}.ft-orders-prototype .ft-import-batch-filter button{margin-left:auto;padding:4px 7px;border:1px solid #c9d7ed;border-radius:7px;background:#fff;color:#31517f;font-size:8.5px;font-weight:750}.ft-orders-prototype .ft-import-batch-filter button:hover{border-color:#8fb0e3;background:#f8fbff}
            .ft-orders-prototype .ft-list-shell{min-width:0;overflow:hidden;border:1px solid var(--line);border-radius:13px;background:var(--surface);box-shadow:0 1px 2px rgba(25,45,75,.02)}.ft-orders-prototype .ft-order-table-scroll{width:100%;min-width:0;overflow-x:auto;overflow-y:visible;overscroll-behavior-inline:contain;scrollbar-gutter:stable}.ft-orders-prototype .ft-order-table-scroll::-webkit-scrollbar{height:10px}.ft-orders-prototype .ft-order-table-scroll::-webkit-scrollbar-track{background:#f3f6fa}.ft-orders-prototype .ft-order-table-scroll::-webkit-scrollbar-thumb{border:2px solid #f3f6fa;border-radius:999px;background:#c6d2e2}.ft-orders-prototype .ft-search-bar{display:grid;grid-template-columns:minmax(320px,1.55fr) repeat(3,minmax(150px,.72fr)) auto;min-width:0;align-items:center;gap:7px;padding:8px 10px;border-bottom:1px solid var(--line)}.ft-orders-prototype .ft-search{position:relative;width:100%;min-width:0;align-self:center}.ft-orders-prototype .ft-search-icon{position:absolute;left:11px;top:50%;color:#72829a;font-size:13px;transform:translateY(-50%);pointer-events:none}.ft-orders-prototype .ft-search input{width:100%;height:34px;padding:0 56px 0 33px;border:1px solid #cfdae9;border-radius:8px;outline:0;background:#fff;color:var(--text);font-size:10.5px;line-height:34px}.ft-orders-prototype .ft-search input:focus{border-color:#7ba2f3;box-shadow:0 0 0 3px rgba(36,99,235,.1)}.ft-orders-prototype .ft-search-clear{position:absolute;right:7px;top:50%;display:none;padding:3px 5px;border:0;background:transparent;color:#5e6e85;font-size:8.5px;transform:translateY(-50%)}.ft-orders-prototype .ft-search-clear.show{display:block}.ft-orders-prototype .ft-order-list-filter{min-width:0;margin:0!important;gap:0!important;align-self:center}.ft-orders-prototype .ft-order-list-filter>label{position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important}.ft-orders-prototype .ft-order-list-filter .ft-remote-filter-button{width:100%;height:34px;min-height:34px;padding:0 27px 0 10px;border:1px solid #cfdae9;border-radius:8px;background:#fff;color:#42526a;font-size:10px;font-weight:650;line-height:1}.ft-orders-prototype .ft-order-list-filter .ft-remote-filter-button:hover,.ft-orders-prototype .ft-order-list-filter .ft-remote-filter-button[aria-expanded=true]{border-color:#7ba2f3;box-shadow:0 0 0 3px rgba(36,99,235,.1)}.ft-orders-prototype .ft-order-list-filter .ft-remote-filter-menu{font-size:10px}.ft-orders-prototype .ft-order-clear-filters{height:34px;min-height:34px;align-self:center;padding:0 12px;border:1px solid #cfdae9;border-radius:8px;background:#fff;color:#42526a;font-size:10px;font-weight:700;line-height:1;white-space:nowrap}.ft-orders-prototype .ft-order-clear-filters:hover{border-color:#7ba2f3;background:#f7faff;color:#1d57c7}.ft-orders-prototype .ft-order-clear-filters:disabled{cursor:default;opacity:.45}.ft-orders-prototype .ft-search-state{grid-column:1/-1;display:flex;min-width:0;align-items:center;gap:7px;color:#64748b;font-size:9px;white-space:nowrap}.ft-orders-prototype .ft-live-dot{width:6px;height:6px;border-radius:50%;background:#20a36f}.ft-orders-prototype .ft-spinner{width:13px;height:13px;border:2px solid #dce5f2;border-top-color:var(--blue);border-radius:50%;animation:ft-orders-spin .6s linear infinite}
            .ft-orders-prototype .ft-job-head,.ft-orders-prototype .ft-job-row{display:grid;grid-template-columns:150px 185px 155px 300px 185px 120px 125px 195px 110px 58px 38px;column-gap:10px;align-items:center;width:100%;min-width:1750px}.ft-orders-prototype .ft-job-head{min-height:34px;padding:0 12px;border-bottom:1px solid var(--line);background:#f8fafc;color:#6c7a90;font-size:7.5px;font-weight:800;letter-spacing:.035em;text-transform:uppercase}.ft-orders-prototype .ft-job-list{min-width:0}.ft-orders-prototype .ft-job-row{position:relative;min-height:74px;padding:9px 12px;border-bottom:1px solid #e5eaf1;background:#fff;content-visibility:auto;contain-intrinsic-size:74px}.ft-orders-prototype .ft-job-row:nth-child(odd){background:#F3F4F6}.ft-orders-prototype .ft-job-row:nth-child(even){background:#fff}.ft-orders-prototype .ft-orders-prototype .ft-job-row:nth-child(even):hover{background:#f8fcfa}.ft-orders-prototype .ft-cell{min-width:0}.ft-orders-prototype .ft-cell::before{display:none}.ft-orders-prototype .ft-id{display:inline-block;max-width:100%;overflow:visible;color:#125be6;font-size:10px;font-weight:800;line-height:1.3;text-decoration:none;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-id:hover{text-decoration:underline}.ft-orders-prototype .ft-sub{display:block;max-width:100%;margin-top:3px;overflow:visible;color:#77869b;font-size:8px;line-height:1.35;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-client{display:block;max-width:100%;overflow:visible;color:#263248;font-size:9px;font-weight:750;line-height:1.3;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-product{display:block;max-width:100%;margin-top:3px;overflow:visible;color:#526078;font-size:8px;line-height:1.35;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-product-detail{display:block;max-width:100%;margin-top:2px;overflow:visible;color:#77869b;font-size:7.5px;line-height:1.35;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-created-name{display:block;overflow:visible;color:#2d3950;font-size:9px;font-weight:750;line-height:1.3;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-created-on{display:block;margin-top:3px;color:#758399;font-size:8px}.ft-orders-prototype .ft-inquiry-link{display:inline-block;max-width:100%;overflow:visible;color:#155ce9;font-size:8.5px;font-weight:750;line-height:1.3;text-decoration:none;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-inquiry-link:hover{text-decoration:underline}.ft-orders-prototype .ft-standard-empty{display:inline-block;color:#8995a6;font-size:8px;font-style:italic}.ft-orders-prototype .ft-chips{display:flex;max-width:100%;flex-wrap:wrap;gap:4px}.ft-orders-prototype .ft-pill{display:inline-flex;max-width:100%;align-items:center;padding:3px 6px;overflow:visible;border-radius:9px;font-size:7.5px;font-weight:750;line-height:1.25;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-pill.blue{background:var(--blue-soft);color:#225fd4}.ft-orders-prototype .ft-pill.green{background:var(--green-soft);color:var(--green)}.ft-orders-prototype .ft-pill.amber{background:var(--amber-soft);color:var(--amber)}.ft-orders-prototype .ft-pill.red{background:var(--red-soft);color:var(--red)}.ft-orders-prototype .ft-pill.purple{background:var(--purple-soft);color:var(--purple)}.ft-orders-prototype .ft-owner{display:flex;min-width:0;align-items:center;gap:7px}.ft-orders-prototype .ft-owner-copy{min-width:0}.ft-orders-prototype .ft-owner-name{display:block;overflow:visible;font-size:9px;font-weight:750;line-height:1.3;text-overflow:clip;white-space:normal;overflow-wrap:anywhere}.ft-orders-prototype .ft-due{display:block;margin-top:2px;color:#67768c;font-size:8px}.ft-orders-prototype .ft-due.overdue{color:var(--red);font-weight:750}.ft-orders-prototype .ft-order-avatar{display:grid;place-items:center;width:28px;height:28px;flex:0 0 28px;overflow:hidden;border-radius:50%;background:#dce8ff;color:#315da8;font-size:8px;font-weight:800}.ft-orders-prototype .ft-order-avatar img{width:100%;height:100%;object-fit:cover}.ft-orders-prototype .ft-progress{display:flex;align-items:center;gap:7px;color:#59677d;font-size:8px}.ft-orders-prototype .ft-progress-track{width:min(70px,70%);height:6px;overflow:hidden;border-radius:5px;background:#e8edf3}.ft-orders-prototype .ft-progress-fill{display:block;height:100%;border-radius:5px;background:var(--blue)}.ft-orders-prototype .ft-view{display:grid;place-items:center;width:34px;height:30px;border:1px solid #d0dbeb;border-radius:8px;background:#fff;color:#155ce9;font-size:8px;font-weight:800;text-decoration:none}.ft-orders-prototype .ft-view:hover{background:var(--blue-soft)}.ft-orders-prototype .ft-row-actions{display:flex;align-items:center;justify-content:flex-end}.ft-orders-prototype .ft-row-action-trigger{display:grid;place-items:center;width:30px;height:30px;padding:0;border:0;border-radius:7px;background:transparent;color:#5f6f85;font-size:18px;font-weight:800;line-height:1;transition:background .15s ease,color .15s ease}.ft-orders-prototype .ft-row-action-trigger:hover,.ft-orders-prototype .ft-row-action-trigger[aria-expanded="true"]{background:#f1f5fa;color:#1e2c43;box-shadow:none}.ft-orders-prototype .ft-row-action-menu[popover]{position:fixed;inset:auto;width:190px;margin:0;padding:5px;border:1px solid #d9e2ed;border-radius:10px;background:#fff;box-shadow:0 14px 34px rgba(30,48,73,.18);z-index:2147483000}.ft-orders-prototype .ft-row-action-menu[popover]::backdrop{background:transparent}.ft-orders-prototype .ft-row-action-menu a,.ft-orders-prototype .ft-row-action-menu button{display:flex;width:100%;box-sizing:border-box;align-items:center;gap:8px;padding:9px 10px;border:0;border-radius:7px;background:transparent;color:#26364f;font-size:10px;font-weight:750;line-height:1.25;text-align:left;text-decoration:none}.ft-orders-prototype .ft-row-action-menu a:hover,.ft-orders-prototype .ft-row-action-menu a:focus-visible,.ft-orders-prototype .ft-row-action-menu button:hover,.ft-orders-prototype .ft-row-action-menu button:focus-visible{background:#f2f6fc;color:#155ce9;outline:0}.ft-orders-prototype .ft-row-action-menu .ft-row-action-danger{margin-top:4px;border-top:1px solid #e5eaf1;border-radius:0 0 7px 7px;color:#bf3131}.ft-orders-prototype .ft-row-action-menu .ft-row-action-danger:hover,.ft-orders-prototype .ft-row-action-menu .ft-row-action-danger:focus-visible{background:#fff0f0;color:#bf3131}.ft-orders-prototype .ft-row-action-menu svg{width:14px;height:14px;flex:0 0 14px;fill:none;stroke:currentColor;stroke-width:1.8}.ft-orders-prototype .ft-list-flash{margin-bottom:10px;padding:9px 12px;border:1px solid #bfe7d5;border-radius:9px;background:#effaf5;color:#147e5b;font-size:10px;font-weight:700}.ft-orders-prototype .ft-empty{padding:42px 16px;color:#65748b;text-align:center;font-size:11px}.ft-orders-prototype .ft-empty strong{display:block;margin-bottom:5px;color:#273349;font-size:13px}

            .ft-orders-prototype .ft-order-select-wrap{display:inline-flex;align-items:center;gap:8px;min-width:0}.ft-orders-prototype .ft-order-select-wrap>span{min-width:0}.ft-orders-prototype .ft-order-select{width:15px;height:15px;flex:0 0 15px;margin:0;border:1px solid #b9c7da;border-radius:4px;accent-color:var(--blue);cursor:pointer}.ft-orders-prototype .ft-order-select:focus-visible{outline:2px solid rgba(36,99,235,.3);outline-offset:2px}.ft-orders-prototype .ft-job-row.is-bulk-selected{box-shadow:inset 3px 0 0 var(--blue)}
            .ft-orders-prototype .ft-order-bulk-bar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:9px 11px;border-bottom:1px solid #cfe0fa;background:#f5f9ff}.ft-orders-prototype .ft-order-bulk-summary{display:flex;min-width:0;align-items:baseline;gap:8px}.ft-orders-prototype .ft-order-bulk-summary strong{color:#173761;font-size:10px;font-weight:800}.ft-orders-prototype .ft-order-bulk-summary span{color:#718096;font-size:8.7px}.ft-orders-prototype .ft-order-bulk-actions{display:flex;flex:0 0 auto;align-items:center;gap:7px}.ft-orders-prototype .ft-order-bulk-clear,.ft-orders-prototype .ft-order-bulk-delete{display:inline-flex;min-height:31px;align-items:center;justify-content:center;gap:6px;padding:0 10px;border-radius:8px;font-size:9px;font-weight:800;line-height:1}.ft-orders-prototype .ft-order-bulk-clear{border:1px solid #ccd8e8;background:#fff;color:#45566e}.ft-orders-prototype .ft-order-bulk-clear:hover{border-color:#99afd0;background:#f8fbff}.ft-orders-prototype .ft-order-bulk-delete{border:1px solid #d83d3d;background:#d83d3d;color:#fff}.ft-orders-prototype .ft-order-bulk-delete:hover{background:#bd3030;border-color:#bd3030}.ft-orders-prototype .ft-order-bulk-delete svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:1.8}.ft-orders-prototype .ft-order-bulk-clear:disabled,.ft-orders-prototype .ft-order-bulk-delete:disabled{cursor:wait;opacity:.65}
            .ft-orders-prototype .ft-order-delete-modal{position:fixed;inset:0;z-index:260;display:flex;align-items:center;justify-content:center;padding:20px}.ft-orders-prototype .ft-order-delete-backdrop{position:absolute;inset:0;border:0;background:rgba(15,23,42,.46);backdrop-filter:blur(2px);cursor:default}.ft-orders-prototype .ft-order-delete-card{position:relative;z-index:1;width:min(430px,100%);overflow:hidden;border:1px solid #e2e8f0;border-radius:16px;background:#fff;box-shadow:0 24px 70px rgba(15,23,42,.24);animation:ft-order-delete-pop .16s ease-out}.ft-orders-prototype .ft-order-delete-body{padding:22px 22px 17px}.ft-orders-prototype .ft-order-delete-icon{display:flex;width:46px;height:46px;align-items:center;justify-content:center;margin-bottom:14px;border-radius:13px;background:#fff0f0;color:#d92d20}.ft-orders-prototype .ft-order-delete-icon svg{width:23px;height:23px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.ft-orders-prototype .ft-order-delete-title{margin:0;color:#172033;font-size:16px;font-weight:800;letter-spacing:-.015em}.ft-orders-prototype .ft-order-delete-copy{margin:7px 0 0;color:#64748b;font-size:10.5px;line-height:1.55}.ft-orders-prototype .ft-order-delete-count{display:flex;align-items:center;gap:9px;margin-top:15px;padding:11px 12px;border:1px solid #f1c7c7;border-radius:10px;background:#fff8f8;color:#7f1d1d}.ft-orders-prototype .ft-order-delete-count strong{font-size:11px;font-weight:800}.ft-orders-prototype .ft-order-delete-count span{font-size:9.5px;color:#9f3f3f}.ft-orders-prototype .ft-order-delete-warning{margin:12px 0 0;color:#8a5260;font-size:9.5px;line-height:1.45}.ft-orders-prototype .ft-order-delete-actions{display:flex;justify-content:flex-end;gap:8px;padding:14px 22px 20px;border-top:1px solid #edf1f6;background:#fbfcfe}.ft-orders-prototype .ft-order-delete-cancel,.ft-orders-prototype .ft-order-delete-confirm{display:inline-flex;min-height:35px;align-items:center;justify-content:center;padding:0 13px;border-radius:9px;font-size:10px;font-weight:800}.ft-orders-prototype .ft-order-delete-cancel{border:1px solid #d4deeb;background:#fff;color:#334155}.ft-orders-prototype .ft-order-delete-cancel:hover{border-color:#a9b8cc;background:#f8fafc}.ft-orders-prototype .ft-order-delete-confirm{gap:6px;border:1px solid #d92d20;background:#d92d20;color:#fff;box-shadow:0 5px 12px rgba(217,45,32,.16)}.ft-orders-prototype .ft-order-delete-confirm:hover{border-color:#b42318;background:#b42318}.ft-orders-prototype .ft-order-delete-confirm:disabled,.ft-orders-prototype .ft-order-delete-cancel:disabled{cursor:wait;opacity:.65}.ft-orders-prototype .ft-order-delete-confirm svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:1.9}.ft-orders-prototype .ft-order-delete-close{position:absolute;top:13px;right:13px;display:flex;width:30px;height:30px;align-items:center;justify-content:center;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#64748b;font-size:18px;line-height:1}.ft-orders-prototype .ft-order-delete-close:hover{background:#f8fafc;color:#334155}@keyframes ft-order-delete-pop{from{opacity:0;transform:translateY(5px) scale(.985)}to{opacity:1;transform:translateY(0) scale(1)}}
            .ft-orders-prototype .ft-list-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;min-height:55px;padding:9px 12px;border-top:1px solid var(--line);background:#fbfcfe}.ft-orders-prototype .ft-result-count{color:#66758c;font-size:9px}.ft-orders-prototype .ft-page-buttons{display:flex;align-items:center;gap:5px}.ft-orders-prototype .ft-page-button{min-width:76px;min-height:34px;border:1px solid #cfdbea;border-radius:9px;background:#fff;color:#23436e;font-size:9px;font-weight:750}.ft-orders-prototype .ft-page-button:hover{border-color:#8facdf;background:var(--blue-soft)}.ft-orders-prototype .ft-page-button[disabled]{cursor:default;opacity:.45}.ft-orders-prototype .ft-page-number{width:34px;min-width:34px;height:34px;border:1px solid #cfdbea;border-radius:8px;background:#fff;color:#40506a;font-size:9px;font-weight:750}.ft-orders-prototype .ft-page-number.active{border-color:var(--blue);background:var(--blue);color:#fff}.ft-orders-prototype .ft-page-ellipsis{color:#74839a;font-size:9px}.ft-orders-prototype .ft-load-skeleton{display:none;gap:7px;padding:8px 12px;border-top:1px solid var(--line)}.ft-orders-prototype .ft-skeleton-line{height:11px;border-radius:5px;background:linear-gradient(90deg,#edf1f6 25%,#f8fafc 45%,#edf1f6 65%);background-size:240% 100%;animation:ft-orders-shimmer 1s linear infinite}.ft-orders-prototype .ft-skeleton-line:nth-child(2){width:82%}

            /* Typography alignment with Dashboard / My Task. */
            .ft-orders-prototype .ft-page-head h1{font-size:25px}.ft-orders-prototype .ft-page-head p{font-size:12px}.ft-orders-prototype .ft-button{font-size:11.5px}.ft-orders-prototype .ft-search input{font-size:11px}.ft-orders-prototype .ft-search-clear,.ft-orders-prototype .ft-search-state{font-size:10px}
            .ft-orders-prototype .ft-job-head{font-size:9px}.ft-orders-prototype .ft-id{font-size:11px}.ft-orders-prototype .ft-client,.ft-orders-prototype .ft-created-name,.ft-orders-prototype .ft-owner-name{font-size:10.5px}.ft-orders-prototype .ft-sub,.ft-orders-prototype .ft-product,.ft-orders-prototype .ft-created-on{font-size:9.5px}.ft-orders-prototype .ft-product-detail{font-size:9px}.ft-orders-prototype .ft-inquiry-link{font-size:10px}.ft-orders-prototype .ft-standard-empty{font-size:9.5px}.ft-orders-prototype .ft-pill{font-size:9px}.ft-orders-prototype .ft-due,.ft-orders-prototype .ft-progress{font-size:9.5px}.ft-orders-prototype .ft-order-avatar{font-size:9.5px}.ft-orders-prototype .ft-view{font-size:9.5px}.ft-orders-prototype .ft-result-count,.ft-orders-prototype .ft-page-button,.ft-orders-prototype .ft-page-number,.ft-orders-prototype .ft-page-ellipsis{font-size:10px}
            @keyframes ft-orders-spin{to{transform:rotate(360deg)}}@keyframes ft-orders-shimmer{to{background-position:-240% 0}}@keyframes ft-orders-reveal{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}.ft-orders-prototype .ft-job-row{animation:ft-orders-reveal .22s ease both}.ft-orders-prototype .ft-job-row:nth-child(2){animation-delay:.025s}.ft-orders-prototype .ft-job-row:nth-child(3){animation-delay:.05s}.ft-orders-prototype .ft-job-row:nth-child(4){animation-delay:.075s}.ft-orders-prototype .ft-job-row:nth-child(5){animation-delay:.1s}
            @media(max-width:1380px){.ft-orders-prototype .ft-order-table-scroll{overflow-x:visible}.ft-orders-prototype .ft-job-head{display:none}.ft-orders-prototype .ft-job-row{width:100%;min-width:0;grid-template-columns:minmax(140px,1fr) minmax(125px,.8fr) minmax(130px,.9fr) 42px 32px;grid-template-areas:"identity created inquiry view actions" "brief brief owner progress progress" "stage health flag progress progress";row-gap:8px;min-height:128px}.ft-orders-prototype .ft-job-row .ft-cell::before{content:attr(data-label);display:block;margin-bottom:3px;color:#8793a5;font-size:7px;font-weight:750;letter-spacing:.04em;text-transform:uppercase}.ft-orders-prototype .ft-identity{grid-area:identity}.ft-orders-prototype .ft-created-cell{grid-area:created}.ft-orders-prototype .ft-inquiry-cell{grid-area:inquiry}.ft-orders-prototype .ft-brief{grid-area:brief}.ft-orders-prototype .ft-stage-cell{grid-area:stage}.ft-orders-prototype .ft-health-cell{grid-area:health}.ft-orders-prototype .ft-flag-cell{grid-area:flag}.ft-orders-prototype .ft-owner-cell{grid-area:owner}.ft-orders-prototype .ft-progress-cell{grid-area:progress}.ft-orders-prototype .ft-view{grid-area:view}.ft-orders-prototype .ft-row-actions{grid-area:actions}.ft-orders-prototype .ft-progress-cell{justify-self:end;width:100%}.ft-orders-prototype .ft-progress-track{flex:1}}
            @media(max-width:980px){.ft-orders-prototype .ft-search-bar{grid-template-columns:minmax(240px,1.4fr) repeat(2,minmax(150px,.8fr))}.ft-orders-prototype .ft-order-list-filter-assignee,.ft-orders-prototype .ft-order-list-filter-owner{grid-column:auto}.ft-orders-prototype .ft-search-state{grid-column:1/-1}}@media(max-width:820px){.ft-orders-prototype .ft-page-head{align-items:flex-start}.ft-orders-prototype .ft-page-head p{max-width:420px}.ft-orders-prototype .ft-search-bar{grid-template-columns:1fr 1fr;align-items:stretch}.ft-orders-prototype .ft-search{width:100%;grid-column:1/-1}.ft-orders-prototype .ft-search-state{width:100%;grid-column:1/-1}}
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
                .ft-orders-prototype .ft-dashboard-action-match{min-height:36px!important;padding:0 12px!important;font-size:10px!important}.ft-orders-prototype .ft-bulk-import-button{height:36px;min-height:36px;padding:0 12px;font-size:10px}
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
            @media(max-width:760px){
                .ft-orders-prototype .ft-order-bulk-bar{align-items:stretch;flex-direction:column;gap:8px}
                .ft-orders-prototype .ft-order-bulk-summary{align-items:flex-start;flex-direction:column;gap:2px}
                .ft-orders-prototype .ft-order-bulk-actions{width:100%}
                .ft-orders-prototype .ft-order-bulk-clear,.ft-orders-prototype .ft-order-bulk-delete{flex:1;min-height:34px}
                .ft-orders-prototype .ft-order-delete-card{border-radius:14px}.ft-orders-prototype .ft-order-delete-body{padding:20px 18px 15px}.ft-orders-prototype .ft-order-delete-actions{padding:12px 18px 17px}.ft-orders-prototype .ft-order-delete-cancel,.ft-orders-prototype .ft-order-delete-confirm{flex:1}
            }
            @media(max-width:600px){
                .ft-orders-prototype .ft-search-bar{grid-template-columns:1fr}
                .ft-orders-prototype .ft-search,.ft-orders-prototype .ft-order-list-filter,.ft-orders-prototype .ft-search-state{grid-column:1/-1}
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
                .ft-orders-prototype .ft-dashboard-action-match{min-height:34px!important;padding:0 10px!important;font-size:9.5px!important}.ft-orders-prototype .ft-bulk-import-button{height:34px;min-height:34px;padding:0 10px;font-size:9.5px}
                .ft-orders-prototype .ft-search input{height:38px;padding-left:34px;padding-right:50px;font-size:11px;line-height:38px}.ft-orders-prototype .ft-order-list-filter .ft-remote-filter-button,.ft-orders-prototype .ft-order-clear-filters{height:38px;min-height:38px}
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


            /* 2026-08-15: final phone card refinement. Keep the mobile Order list
               compact, predictable and consistent with the Inquiry card hierarchy. */
            .ft-orders-prototype .ft-view-label-mobile,.ft-orders-prototype .ft-view-arrow{display:none}
            @media(max-width:680px){
                .ft-orders-prototype .ft-order-table-scroll{overflow:visible}
                .ft-orders-prototype .ft-job-list{
                    display:grid;
                    gap:8px;
                    padding:8px;
                    background:#f8fafc;
                }
                .ft-orders-prototype .ft-job-row,
                .ft-orders-prototype .ft-job-row.ft-client-row-iid,
                .ft-orders-prototype .ft-job-row.ft-client-row-nep{
                    position:relative;
                    display:grid;
                    grid-template-columns:repeat(6,minmax(0,1fr));
                    grid-template-areas:
                        "identity identity identity identity identity identity"
                        "created created created inquiry inquiry inquiry"
                        "brief brief brief brief brief brief"
                        "stage stage health health flag flag"
                        "owner owner owner owner owner owner"
                        "progress progress progress progress progress progress"
                        "view view view view view view";
                    width:100%;
                    min-width:0;
                    min-height:0;
                    margin:0;
                    padding:12px;
                    gap:10px 8px;
                    overflow:visible;
                    border:1px solid #dce5ef;
                    border-radius:14px;
                    box-shadow:none;
                    content-visibility:visible;
                    contain-intrinsic-size:auto;
                }
                .ft-orders-prototype .ft-job-row:last-child{border-bottom:1px solid #dce5ef}
                .ft-orders-prototype .ft-job-row .ft-cell{min-width:0}
                .ft-orders-prototype .ft-job-row .ft-cell::before{
                    margin:0 0 4px;
                    color:#7d8ca2;
                    font-size:7.4px;
                    font-weight:800;
                    letter-spacing:.075em;
                    line-height:1;
                }
                .ft-orders-prototype .ft-identity{grid-area:identity;padding-right:42px}
                .ft-orders-prototype .ft-created-cell{grid-area:created}
                .ft-orders-prototype .ft-inquiry-cell{grid-area:inquiry}
                .ft-orders-prototype .ft-brief{
                    grid-area:brief;
                    padding:9px 10px;
                    border:1px solid rgba(207,218,233,.82);
                    border-radius:10px;
                    background:rgba(255,255,255,.66);
                }
                .ft-orders-prototype .ft-stage-cell{grid-area:stage}
                .ft-orders-prototype .ft-health-cell{grid-area:health}
                .ft-orders-prototype .ft-flag-cell{grid-area:flag}
                .ft-orders-prototype .ft-owner-cell{
                    grid-area:owner;
                    padding-top:9px;
                    border-top:1px solid rgba(219,227,237,.92);
                }
                .ft-orders-prototype .ft-progress-cell{
                    grid-area:progress;
                    width:100%;
                    max-width:none;
                    justify-self:stretch;
                    gap:8px;
                    padding-top:1px;
                }
                .ft-orders-prototype .ft-progress-track{width:auto;max-width:none;flex:1;height:5px}
                .ft-orders-prototype .ft-row-actions{
                    position:absolute;
                    grid-area:auto;
                    top:10px;
                    right:10px;
                    z-index:4;
                    display:flex;
                    width:32px;
                    height:32px;
                    align-items:center;
                    justify-content:center;
                }
                .ft-orders-prototype .ft-row-actions::before{display:none!important}
                .ft-orders-prototype .ft-row-action-trigger{
                    width:32px;
                    height:32px;
                    border:1px solid #dbe3ed;
                    border-radius:9px;
                    background:rgba(255,255,255,.9);
                    color:#31445f;
                    font-size:16px;
                    box-shadow:none;
                }
                .ft-orders-prototype .ft-view{
                    position:static;
                    grid-area:view;
                    display:inline-flex;
                    width:auto;
                    height:auto;
                    min-height:25px;
                    justify-self:end;
                    align-items:center;
                    justify-content:center;
                    gap:3px;
                    padding:5px 8px;
                    border:1px solid #ccdcfb;
                    border-radius:7px;
                    background:#f4f7ff;
                    color:#155ce9;
                    font-size:8.5px;
                    font-weight:650;
                    line-height:1;
                    text-decoration:none;
                    box-shadow:none;
                }
                .ft-orders-prototype .ft-view:hover{background:#eaf1ff;border-color:#bdd2fa}
                .ft-orders-prototype .ft-view:focus-visible{outline:2px solid rgba(21,92,233,.17);outline-offset:2px}
                .ft-orders-prototype .ft-view-label-desktop{display:none}
                .ft-orders-prototype .ft-view-label-mobile,.ft-orders-prototype .ft-view-arrow{display:inline}
                .ft-orders-prototype .ft-view-arrow{font-size:10px;font-weight:600;line-height:1}
                .ft-orders-prototype .ft-id{font-size:12.5px;font-weight:800;line-height:1.18}
                .ft-orders-prototype .ft-sub{margin-top:2px;font-size:9.3px;line-height:1.25}
                .ft-orders-prototype .ft-created-name,.ft-orders-prototype .ft-client,.ft-orders-prototype .ft-owner-name{font-size:10.5px;line-height:1.25}
                .ft-orders-prototype .ft-created-on,.ft-orders-prototype .ft-product,.ft-orders-prototype .ft-product-detail,.ft-orders-prototype .ft-due,.ft-orders-prototype .ft-progress,.ft-orders-prototype .ft-standard-empty{font-size:9px;line-height:1.3}
                .ft-orders-prototype .ft-product-detail{margin-top:2px}
                .ft-orders-prototype .ft-job-client-logo-line{gap:6px}
                .ft-orders-prototype .ft-pill{max-width:100%;padding:4px 6px;border-radius:8px;font-size:8.2px;font-weight:750;line-height:1.15}
                .ft-orders-prototype .ft-stage-cell,.ft-orders-prototype .ft-health-cell,.ft-orders-prototype .ft-flag-cell{min-width:0;align-self:start}
                .ft-orders-prototype .ft-stage-cell .ft-pill,.ft-orders-prototype .ft-health-cell .ft-pill,.ft-orders-prototype .ft-flag-cell .ft-pill{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
                .ft-orders-prototype .ft-owner{gap:7px}
                .ft-orders-prototype .ft-order-avatar{width:28px;height:28px;flex-basis:28px;font-size:9px}
            }
            @media(max-width:430px){
                .ft-orders-prototype .ft-job-list{gap:7px;padding:6px 4px}
                .ft-orders-prototype .ft-job-row,
                .ft-orders-prototype .ft-job-row.ft-client-row-iid,
                .ft-orders-prototype .ft-job-row.ft-client-row-nep{padding:11px 10px;gap:9px 7px;border-radius:13px}
                .ft-orders-prototype .ft-identity{padding-right:39px}
                .ft-orders-prototype .ft-row-actions{top:9px;right:9px;width:30px;height:30px}
                .ft-orders-prototype .ft-row-action-trigger{width:30px;height:30px;border-radius:8px;font-size:15px}
                .ft-orders-prototype .ft-brief{padding:8px 9px}
                .ft-orders-prototype .ft-view{min-height:23px;padding:4px 7px;font-size:8px}
                .ft-orders-prototype .ft-view-arrow{font-size:9.5px}
                .ft-orders-prototype .ft-id{font-size:12px}
                .ft-orders-prototype .ft-created-name,.ft-orders-prototype .ft-client,.ft-orders-prototype .ft-owner-name{font-size:10px}
                .ft-orders-prototype .ft-created-on,.ft-orders-prototype .ft-product,.ft-orders-prototype .ft-product-detail,.ft-orders-prototype .ft-due,.ft-orders-prototype .ft-progress,.ft-orders-prototype .ft-standard-empty{font-size:8.7px}
                .ft-orders-prototype .ft-pill{font-size:7.9px;padding:3.5px 5.5px}
            }

                        @media(prefers-reduced-motion:reduce){.ft-orders-prototype *,.ft-orders-prototype *::before,.ft-orders-prototype *::after{scroll-behavior:auto!important;animation:none!important;transition:none!important}}

            /* Client-specific row tones: IID = light green, NEP = light blue. */
            .ft-orders-prototype .ft-job-row.ft-client-row-iid,
            .ft-orders-prototype .ft-job-row.ft-client-row-iid:nth-child(odd),
            .ft-orders-prototype .ft-job-row.ft-client-row-iid:nth-child(even){background:#eef9f1}
            .ft-orders-prototype .ft-job-row.ft-client-row-nep,
            .ft-orders-prototype .ft-job-row.ft-client-row-nep:nth-child(odd),
            .ft-orders-prototype .ft-job-row.ft-client-row-nep:nth-child(even){background:#eef6ff}
            .ft-orders-prototype .ft-job-row.ft-client-row-iid:hover{background:#e5f5ea}
            .ft-orders-prototype .ft-job-row.ft-client-row-nep:hover{background:#e4f0ff}
        </style>
    <?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="ft-list-flash" role="status"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-page-head">
        <div><h1>Orders</h1><p>Fast access to every active and completed order</p></div>
        <div class="ft-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canAccess('jobs.create')): ?>
                <a class="ft-button ft-bulk-import-button" href="<?php echo e(route('orders.bulk-import')); ?>">⇧ Bulk Import</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs', 'create')): ?>
                <a class="ft-new-job-btn ft-dashboard-action-match" href="<?php echo e(route('jobs.index', ['create' => 1])); ?>" wire:navigate><span class="ft-dashboard-action-match-icon">+</span>New Order</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($metrics)): ?>
        <div class="metrics ft-summary-card-grid ft-order-summary" aria-label="Order summary filters">
            <?php if (isset($component)) { $__componentOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3d9f2b72d2e6c6ddaf41740549ad542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Created Today','value' => $metrics['createdToday'] ?? 0,'icon' => 'created','tone' => 'blue','caption' => 'New orders received','active' => $metricFilter === 'createdToday','wire:click' => 'setMetricFilter(\'createdToday\')','ariaPressed' => ''.e($metricFilter === 'createdToday' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Created Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['createdToday'] ?? 0),'icon' => 'created','tone' => 'blue','caption' => 'New orders received','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'createdToday'),'wire:click' => 'setMetricFilter(\'createdToday\')','aria-pressed' => ''.e($metricFilter === 'createdToday' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Not Started','value' => $metrics['notStarted'] ?? 0,'icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => $metricFilter === 'notStarted','wire:click' => 'setMetricFilter(\'notStarted\')','ariaPressed' => ''.e($metricFilter === 'notStarted' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Not Started','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['notStarted'] ?? 0),'icon' => 'not-started','tone' => 'slate','caption' => 'Waiting for first action','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'notStarted'),'wire:click' => 'setMetricFilter(\'notStarted\')','aria-pressed' => ''.e($metricFilter === 'notStarted' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'In Progress','value' => $metrics['inProgress'] ?? 0,'icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => $metricFilter === 'inProgress','wire:click' => 'setMetricFilter(\'inProgress\')','ariaPressed' => ''.e($metricFilter === 'inProgress' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['inProgress'] ?? 0),'icon' => 'in-progress','tone' => 'blue','caption' => 'Work currently underway','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'inProgress'),'wire:click' => 'setMetricFilter(\'inProgress\')','aria-pressed' => ''.e($metricFilter === 'inProgress' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Due This Week','value' => $metrics['dueThisWeek'] ?? 0,'icon' => 'due-week','tone' => 'amber','caption' => 'Required date this week','active' => $metricFilter === 'dueThisWeek','wire:click' => 'setMetricFilter(\'dueThisWeek\')','ariaPressed' => ''.e($metricFilter === 'dueThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['dueThisWeek'] ?? 0),'icon' => 'due-week','tone' => 'amber','caption' => 'Required date this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'dueThisWeek'),'wire:click' => 'setMetricFilter(\'dueThisWeek\')','aria-pressed' => ''.e($metricFilter === 'dueThisWeek' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Completed This Week','value' => $metrics['completedThisWeek'] ?? 0,'icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => $metricFilter === 'completedThisWeek','wire:click' => 'setMetricFilter(\'completedThisWeek\')','ariaPressed' => ''.e($metricFilter === 'completedThisWeek' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Completed This Week','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['completedThisWeek'] ?? 0),'icon' => 'completed','tone' => 'green','caption' => 'Finished this week','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'completedThisWeek'),'wire:click' => 'setMetricFilter(\'completedThisWeek\')','aria-pressed' => ''.e($metricFilter === 'completedThisWeek' ? 'true' : 'false').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.summary-card','data' => ['label' => 'Needs Attention','value' => $metrics['attention'] ?? 0,'icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => $metricFilter === 'attention','wire:click' => 'setMetricFilter(\'attention\')','ariaPressed' => ''.e($metricFilter === 'attention' ? 'true' : 'false').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.summary-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics['attention'] ?? 0),'icon' => 'attention','tone' => 'red','caption' => 'Blocked, overdue or unassigned','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metricFilter === 'attention'),'wire:click' => 'setMetricFilter(\'attention\')','aria-pressed' => ''.e($metricFilter === 'attention' ? 'true' : 'false').'']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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

            <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-order-list-filter ft-order-list-filter-client','label' => 'Client','property' => 'client','type' => 'clients','context' => 'jobs','value' => $clientFilter,'placeholder' => 'All clients','initialOptions' => $clientFilterOptions,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'order-list-client-filter-'.e(filled($clientFilter) ? $clientFilter : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-list-filter ft-order-list-filter-client','label' => 'Client','property' => 'client','type' => 'clients','context' => 'jobs','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilter),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions),'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'order-list-client-filter-'.e(filled($clientFilter) ? $clientFilter : 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-order-list-filter ft-order-list-filter-phase','label' => 'Phase','property' => 'phase','type' => 'phases','context' => 'order-list','value' => $phaseFilter,'placeholder' => 'All phases','initialOptions' => $phaseFilterOptions,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'order-list-phase-filter-'.e(filled($phaseFilter) ? $phaseFilter : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-list-filter ft-order-list-filter-phase','label' => 'Phase','property' => 'phase','type' => 'phases','context' => 'order-list','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilter),'placeholder' => 'All phases','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilterOptions),'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'order-list-phase-filter-'.e(filled($phaseFilter) ? $phaseFilter : 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['class' => 'ft-order-list-filter ft-order-list-filter-owner','label' => $peopleLabel,'property' => $peopleProperty,'type' => 'users','context' => $peopleContext,'value' => $peopleFilter,'placeholder' => $peoplePlaceholder,'initialOptions' => $peopleFilterOptions,'fixedMenu' => true,'menuWidth' => 260,'wire:key' => 'order-list-people-filter-'.e($peopleProperty).'-'.e(filled($peopleFilter) ? $peopleFilter : 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-list-filter ft-order-list-filter-owner','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleLabel),'property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleProperty),'type' => 'users','context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleContext),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleFilter),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peoplePlaceholder),'initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($peopleFilterOptions),'fixed-menu' => true,'menu-width' => 260,'wire:key' => 'order-list-people-filter-'.e($peopleProperty).'-'.e(filled($peopleFilter) ? $peopleFilter : 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $attributes = $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11)): ?>
<?php $component = $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11; ?>
<?php unset($__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearFiltersAction): ?>
                <button
                    type="button"
                    class="ft-order-clear-filters"
                    wire:click="<?php echo e($clearFiltersAction); ?>"
                    <?php if(blank($searchFilter) && blank($clientFilter) && blank($phaseFilter) && blank($peopleFilter) && blank($metricFilter) && blank($dateFrom) && blank($dateTo) && ! $importFilterId): echo 'disabled'; endif; ?>
                >Clear filters</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($importFilterId): ?>
            <div class="ft-import-batch-filter" role="status" aria-live="polite">
                <span class="ft-import-batch-filter-dot" aria-hidden="true"></span>
                <span>Showing <strong><?php echo e(number_format($jobs->total())); ?></strong> <?php echo e(\Illuminate\Support\Str::plural('order', $jobs->total())); ?> imported in <strong><?php echo e($importFilterLabel ?: 'this import'); ?></strong></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearFiltersAction): ?>
                    <button type="button" wire:click="<?php echo e($clearFiltersAction); ?>">Show all orders</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateRangeEnabled): ?>
            <?php if (isset($component)) { $__componentOriginalfddc3e752d626ff4464d9025a0e0b874 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfddc3e752d626ff4464d9025a0e0b874 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.date-range-filter','data' => ['class' => 'ft-order-date-range','fromProperty' => 'dateFrom','toProperty' => 'dateTo','fromValue' => $dateFrom,'toValue' => $dateTo,'label' => 'Created date']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.date-range-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-order-date-range','from-property' => 'dateFrom','to-property' => 'dateTo','from-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateFrom),'to-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateTo),'label' => 'Created date']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfddc3e752d626ff4464d9025a0e0b874)): ?>
<?php $attributes = $__attributesOriginalfddc3e752d626ff4464d9025a0e0b874; ?>
<?php unset($__attributesOriginalfddc3e752d626ff4464d9025a0e0b874); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfddc3e752d626ff4464d9025a0e0b874)): ?>
<?php $component = $__componentOriginalfddc3e752d626ff4464d9025a0e0b874; ?>
<?php unset($__componentOriginalfddc3e752d626ff4464d9025a0e0b874); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders && $selectedOrderCount > 0): ?>
            <?php if (isset($component)) { $__componentOriginal497829c258fdc4691f65e9caf04f7e61 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal497829c258fdc4691f65e9caf04f7e61 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.bulk-delete-bar','data' => ['count' => $selectedOrderCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.bulk-delete-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOrderCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal497829c258fdc4691f65e9caf04f7e61)): ?>
<?php $attributes = $__attributesOriginal497829c258fdc4691f65e9caf04f7e61; ?>
<?php unset($__attributesOriginal497829c258fdc4691f65e9caf04f7e61); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal497829c258fdc4691f65e9caf04f7e61)): ?>
<?php $component = $__componentOriginal497829c258fdc4691f65e9caf04f7e61; ?>
<?php unset($__componentOriginal497829c258fdc4691f65e9caf04f7e61); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders && $showBulkDeleteConfirm && $selectedOrderCount > 0): ?>
            <?php if (isset($component)) { $__componentOriginald776f0916bb417998a994ba873f50ec0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald776f0916bb417998a994ba873f50ec0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.bulk-delete-confirmation','data' => ['count' => $selectedOrderCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.bulk-delete-confirmation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOrderCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald776f0916bb417998a994ba873f50ec0)): ?>
<?php $attributes = $__attributesOriginald776f0916bb417998a994ba873f50ec0; ?>
<?php unset($__attributesOriginald776f0916bb417998a994ba873f50ec0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald776f0916bb417998a994ba873f50ec0)): ?>
<?php $component = $__componentOriginald776f0916bb417998a994ba873f50ec0; ?>
<?php unset($__componentOriginald776f0916bb417998a994ba873f50ec0); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-order-table-scroll" tabindex="0" aria-label="Orders table. Scroll horizontally to view all columns when needed.">
            <div class="ft-job-head">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders): ?>
                        <span class="ft-order-select-wrap">
                            <input
                                class="ft-order-select"
                                type="checkbox"
                                aria-label="Select all orders on this page"
                                <?php if($allVisibleOrdersSelected): echo 'checked'; endif; ?>
                                x-on:change="$wire.toggleOrderPageSelection(<?php echo \Illuminate\Support\Js::from($visibleOrderIds->all())->toHtml() ?>, $event.target.checked)"
                            >
                            <span>Created by / on</span>
                        </span>
                    <?php else: ?>
                        <span>Created by / on</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span><span>Order</span><span>Inquiry</span><span>Client / Products</span><span>Phase</span><span>Health</span><span>Flag</span><span>Owner / Delivery</span><span>Progress</span><span>View</span><span aria-label="Actions"></span>
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
                    $health = $job->completed_at ? 'Completed' : ($job->health ?: 'On Track');
                    $phaseName = $job->phase?->name ?? $job->status ?? '—';
                    // The automatic Order Flag remains stored independently in
                    // flow_jobs.order_flag_id. A manual attention request is also stored
                    // independently and only takes display precedence in the list.
                    $automaticFlag = app(\App\Services\OrderTaskFlagService::class)->labelForOrder($job);
                    $manualAttention = (bool) ($job->attention_requested ?? false);
                    $flag = $manualAttention ? 'Requires attention' : $automaticFlag;
                    $flagColor = !$manualAttention && $automaticFlag ? $masterData->displayColorFor('order_flag', $automaticFlag) : null;
                    $flagReason = $manualAttention ? trim((string) ($job->attention_reason ?? '')) : '';
                    $deliveryOverdue = $job->delivery_date && !$job->completed_at && \App\Support\UserLocalTime::isDatePast($job->delivery_date);
                    $clientCode = strtoupper(trim((string) ($job->client?->code ?? '')));
                    $clientName = strtoupper(trim((string) ($job->client?->name ?? '')));
                    $clientRowTone = ($clientCode === 'IID' || preg_match('/\bIID\b/i', $clientName))
                        ? 'iid'
                        : (($clientCode === 'NEP' || preg_match('/\bNEP\b/i', $clientName)) ? 'nep' : '');
                ?>
                <article class="<?php echo \Illuminate\Support\Arr::toCssClasses(['ft-job-row', 'ft-client-row-'.$clientRowTone => $clientRowTone, 'is-bulk-selected' => $selectedOrderIdSet->contains((int) $job->id)]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-row-'.e($job->id).''; ?>wire:key="order-row-<?php echo e($job->id); ?>">
                    <div class="ft-cell ft-created-cell" data-label="Created by / on">
                        <span class="ft-order-select-wrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders): ?>
                                <input
                                    class="ft-order-select"
                                    type="checkbox"
                                    aria-label="Select <?php echo e($job->displayOrderNumber()); ?>"
                                    <?php if($selectedOrderIdSet->contains((int) $job->id)): echo 'checked'; endif; ?>
                                    wire:change="toggleOrderSelection(<?php echo e($job->id); ?>)"
                                >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><span class="ft-created-name"><?php echo e($creatorName); ?></span><time class="ft-created-on"><?php echo e($job->created_at?->format('M j, Y') ?? '—'); ?></time></span>
                        </span>
                    </div>
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
                        <span class="ft-job-client-logo-line"><?php if (isset($component)) { $__componentOriginalb7fdbb44e2f28c5f803966058155c072 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7fdbb44e2f28c5f803966058155c072 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.client-logo','data' => ['client' => $job->client,'name' => $job->client?->name ?: 'Client','size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.client-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['client' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->client?->name ?: 'Client'),'size' => 22]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $attributes = $__attributesOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__attributesOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7fdbb44e2f28c5f803966058155c072)): ?>
<?php $component = $__componentOriginalb7fdbb44e2f28c5f803966058155c072; ?>
<?php unset($__componentOriginalb7fdbb44e2f28c5f803966058155c072); ?>
<?php endif; ?><span class="ft-client"><?php echo e($job->client?->name ?? '—'); ?></span></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productRows->count() === 1): ?>
                            <span class="ft-product"><?php echo e($productNames->first() ?: 'Product'); ?></span>
                            <span class="ft-product-detail"><?php echo e(number_format($totalUnits)); ?> <?php echo e(\Illuminate\Support\Str::plural('pc', $totalUnits)); ?></span>
                        <?php else: ?>
                            <span class="ft-product"><?php echo e($productRows->count()); ?> ordered products · <?php echo e(number_format($totalUnits)); ?> pcs</span>
                            <span class="ft-product-detail" title="<?php echo e($productNames->implode(' · ')); ?>"><?php echo e($productNames->implode(' · ')); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="ft-cell ft-stage-cell" data-label="Phase"><span class="ft-pill <?php echo e($tone($phaseName)); ?>" title="<?php echo e($phaseName); ?>"><?php echo e($phaseName); ?></span></div>
                    <div class="ft-cell ft-health-cell" data-label="Health"><span class="ft-pill <?php echo e($tone($health)); ?>"><?php echo e($health); ?></span></div>
                    <div class="ft-cell ft-flag-cell" data-label="Flag"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flag): ?><span class="ft-pill <?php echo e($flagColor ? 'ft-master-color' : $tone($flag)); ?>" style="<?php echo e(\App\Support\MasterColor::style($flagColor)); ?>" <?php if($flagReason): ?> title="<?php echo e($flagReason); ?>" <?php endif; ?>><?php echo e($flag); ?></span><?php else: ?><span class="ft-standard-empty">No flag</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <div class="ft-cell ft-owner-cell" data-label="Owner / delivery">
                        <div class="ft-owner">
                            <span class="ft-order-avatar"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ownerImage): ?><img src="<?php echo e($ownerImage); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><?php echo e($ownerInitials ?: 'FT'); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                            <span class="ft-owner-copy"><span class="ft-owner-name"><?php echo e($ownerName); ?></span><time class="ft-due <?php echo e($deliveryOverdue ? 'overdue' : ''); ?>"><?php echo e($job->delivery_date ? 'Due '.$job->delivery_date->format('M j') : 'No delivery date'); ?></time></span>
                        </div>
                    </div>
                    <div class="ft-cell ft-progress ft-progress-cell" data-label="Progress"><span class="ft-progress-track"><span class="ft-progress-fill" style="width:<?php echo e(max(0,min(100,(int)$job->progress))); ?>%"></span></span><span><?php echo e((int)$job->progress); ?>%</span></div>
                    <a class="ft-view" href="<?php echo e(route('jobs.index',['open'=>$job->id])); ?>" wire:navigate aria-label="View details for <?php echo e($job->displayOrderNumber()); ?>"><span class="ft-view-label-desktop">View</span><span class="ft-view-label-mobile">Details</span><span class="ft-view-arrow" aria-hidden="true">→</span></a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewFinance || $canDeleteOrders): ?>
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
                                    const menuWidth = 190;
                                    const menuHeight = <?php echo e($canViewFinance && $canDeleteOrders ? 88 : 46); ?>;
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewFinance): ?>
                                    <button
                                        type="button"
                                        role="menuitem"
                                        x-on:click="$refs.menu.hidePopover()"
                                        wire:click="openInvoiceAndPayment(<?php echo e($job->id); ?>)"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3.5h12v17H6z"/><path d="M9 8h6M9 12h6M9 16h3"/></svg>
                                        <span>Invoice and payment</span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteOrders): ?>
                                    <button class="ft-row-action-danger" type="button" role="menuitem" x-on:click="$refs.menu.hidePopover()" wire:click="deleteOrder(<?php echo e($job->id); ?>)" wire:confirm="Delete <?php echo e($job->displayOrderNumber()); ?>? This removes the order from active lists.">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                                        <span>Delete order</span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="ft-empty"><strong>No matching orders</strong>Try another order, inquiry, client, product, creator or owner.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="ft-load-skeleton" wire:loading.delay.grid wire:target="search,gotoPage,previousPage,nextPage" aria-hidden="true"><span class="ft-skeleton-line"></span><span class="ft-skeleton-line"></span></div>
        </div>

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