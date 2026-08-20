@props([
    'action',
    'filters' => [],
    'buttonClass' => '',
    'entityLabel' => 'records',
])

@php
    $exportModalId = 'ft-export-period-'.substr(md5((string) $action.'|'.(string) $entityLabel), 0, 10);
    $currentMonth = app(\App\Services\WorkspaceSettingsService::class)->localNow()->format('Y-m');
    $safeFilters = collect($filters)
        ->except(['date_from', 'date_to', 'export_period', 'export_month'])
        ->filter(static fn ($value) => $value !== null && $value !== '')
        ->all();
@endphp

@once
    <style>
        [x-cloak]{display:none!important}
        .ft-export-period{display:inline-flex}
        .ft-export-period-trigger{cursor:pointer}
        .ft-export-period-layer{position:fixed;inset:0;z-index:1200;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(15,23,42,.38);backdrop-filter:blur(2px)}
        .ft-export-period-dialog{width:min(560px,100%);max-height:calc(100vh - 40px);overflow:auto;border:1px solid #d7e1ee;border-radius:16px;background:#fff;box-shadow:0 24px 70px rgba(15,23,42,.22)}
        .ft-export-period-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:20px 22px 15px;border-bottom:1px solid #e2e8f0}
        .ft-export-period-head h2{margin:0;color:#17243a;font-size:18px;font-weight:800;letter-spacing:-.02em}
        .ft-export-period-head p{margin:5px 0 0;color:#66758d;font-size:11px;line-height:1.45}
        .ft-export-period-close{display:inline-flex;width:32px;height:32px;flex:0 0 32px;align-items:center;justify-content:center;border:1px solid #d7e1ee;border-radius:9px;background:#fff;color:#5f6f86;font-size:18px;line-height:1;cursor:pointer}
        .ft-export-period-close:hover{background:#f7f9fc;color:#1f2c43}
        .ft-export-period-body{padding:17px 22px 20px}
        .ft-export-period-label{display:block;margin-bottom:9px;color:#34445d;font-size:10px;font-weight:800;letter-spacing:.035em;text-transform:uppercase}
        .ft-export-period-options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
        .ft-export-period-option{position:relative;display:flex;min-height:62px;align-items:center;gap:10px;padding:10px 12px;border:1px solid #d8e2ef;border-radius:12px;background:#fff;cursor:pointer;transition:border-color .15s ease,background .15s ease,box-shadow .15s ease}
        .ft-export-period-option:hover{border-color:#a8bde3;background:#f8fbff}
        .ft-export-period-option.is-active{border-color:#4e7de5;background:#f3f7ff;box-shadow:0 0 0 2px rgba(36,99,235,.08)}
        .ft-export-period-option input{width:15px;height:15px;flex:0 0 15px;margin:0;accent-color:#2463eb}
        .ft-export-period-copy{display:block;min-width:0}
        .ft-export-period-copy strong{display:block;color:#233047;font-size:11.5px;font-weight:800}
        .ft-export-period-copy small{display:block;margin-top:2px;color:#76869c;font-size:9.5px;line-height:1.25}
        .ft-export-period-month{margin-top:12px;padding:12px;border:1px solid #d8e2ef;border-radius:12px;background:#f8fafc}
        .ft-export-period-month label{display:block;margin-bottom:6px;color:#34445d;font-size:10px;font-weight:750}
        .ft-export-period-month-control{display:flex;width:100%;min-width:0;border:1px solid #ccd8e8;border-radius:9px;background:#fff;padding:8px 10px;cursor:pointer}
        .ft-export-period-month input{display:block;width:100%;min-width:0;border:0;background:transparent;padding:0;color:#26364d;font:inherit;font-size:11px;outline:none;cursor:pointer}
        .ft-export-period-note{margin:12px 0 0;color:#738198;font-size:9.5px;line-height:1.45}
        .ft-export-period-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;margin-top:17px;padding-top:15px;border-top:1px solid #e5ebf3}
        .ft-export-period-cancel,.ft-export-period-submit{display:inline-flex;min-height:36px;align-items:center;justify-content:center;padding:0 13px;border-radius:9px;font-size:10px;font-weight:800;cursor:pointer}
        .ft-export-period-cancel{border:1px solid #d4deec;background:#fff;color:#34445b}
        .ft-export-period-submit{border:1px solid #2463eb;background:#2463eb;color:#fff;box-shadow:0 5px 13px rgba(36,99,235,.16)}
        .ft-export-period-submit:hover{background:#1f57ce}
        @media(max-width:560px){
            .ft-export-period-layer{align-items:flex-end;padding:12px}
            .ft-export-period-dialog{max-height:calc(100vh - 24px);border-radius:15px}
            .ft-export-period-head{padding:17px 16px 13px}
            .ft-export-period-body{padding:14px 16px 17px}
            .ft-export-period-options{grid-template-columns:1fr}
            .ft-export-period-actions{display:grid;grid-template-columns:1fr 1fr}
        }
    </style>
@endonce

<div
    class="ft-export-period"
    x-data="{ open: false, period: 'this_month', selectedMonth: @js($currentMonth) }"
    x-on:keydown.escape.window="open = false"
>
    <button
        type="button"
        class="{{ $buttonClass }} ft-export-period-trigger"
        x-on:click="open = true"
        aria-haspopup="dialog"
        :aria-expanded="open ? 'true' : 'false'"
        aria-controls="{{ $exportModalId }}"
        title="Choose a report period and export {{ $entityLabel }}"
    >⇩ Export</button>

    <div
        x-cloak
        x-show="open"
        x-transition.opacity.duration.150ms
        class="ft-export-period-layer"
        x-on:click.self="open = false"
    >
        <section
            id="{{ $exportModalId }}"
            class="ft-export-period-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $exportModalId }}-title"
            x-on:click.stop
        >
            <div class="ft-export-period-head">
                <div>
                    <h2 id="{{ $exportModalId }}-title">Export {{ ucfirst($entityLabel) }}</h2>
                    <p>Choose the created-date period for the report. Other active list filters and your access scope will still apply.</p>
                </div>
                <button type="button" class="ft-export-period-close" x-on:click="open = false" aria-label="Close export options">×</button>
            </div>

            <form class="ft-export-period-body" method="GET" action="{{ $action }}">
                @foreach($safeFilters as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach

                <span class="ft-export-period-label">Report period</span>
                <div class="ft-export-period-options">
                    @foreach([
                        'today' => ['Today', 'Created today'],
                        'last_7_days' => ['Last 7 days', 'Today + previous 6 days'],
                        'last_30_days' => ['Last 30 days', 'Today + previous 29 days'],
                        'this_month' => ['This month', 'Current calendar month'],
                        'selected_month' => ['Selected month', 'Choose any calendar month'],
                        'all_time' => ['All time', 'No created-date restriction'],
                    ] as $value => [$label, $help])
                        <label class="ft-export-period-option" :class="period === '{{ $value }}' ? 'is-active' : ''">
                            <input type="radio" name="export_period" value="{{ $value }}" x-model="period">
                            <span class="ft-export-period-copy">
                                <strong>{{ $label }}</strong>
                                <small>{{ $help }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="ft-export-period-month" x-cloak x-show="period === 'selected_month'">
                    <label for="{{ $exportModalId }}-month">Select month</label>
                    <div
                        class="ft-export-period-month-control"
                        role="button"
                        tabindex="0"
                        aria-label="Open month picker"
                        x-on:click="const picker = $refs.monthPicker; if (!picker) return; picker.focus({ preventScroll: true }); if (typeof picker.showPicker === 'function') { try { picker.showPicker(); } catch (e) {} }"
                        x-on:keydown.enter.prevent="const picker = $refs.monthPicker; if (!picker) return; picker.focus({ preventScroll: true }); if (typeof picker.showPicker === 'function') { try { picker.showPicker(); } catch (e) {} }"
                        x-on:keydown.space.prevent="const picker = $refs.monthPicker; if (!picker) return; picker.focus({ preventScroll: true }); if (typeof picker.showPicker === 'function') { try { picker.showPicker(); } catch (e) {} }"
                    >
                        <input
                            id="{{ $exportModalId }}-month"
                            x-ref="monthPicker"
                            type="month"
                            name="export_month"
                            x-model="selectedMonth"
                            :required="period === 'selected_month'"
                        >
                    </div>
                </div>

                <p class="ft-export-period-note">The export includes all matching records in the chosen period, not only the rows currently visible on the page.</p>

                <div class="ft-export-period-actions">
                    <button type="button" class="ft-export-period-cancel" x-on:click="open = false">Cancel</button>
                    <button type="submit" class="ft-export-period-submit">Generate &amp; Export</button>
                </div>
            </form>
        </section>
    </div>
</div>
