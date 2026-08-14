<div id="inquiry-intelligence-app" class="ii">
<?php
    $report = $this->report;
    $portfolio = $report['portfolio'];
    $pk = $portfolio['kpis'];
    $people = $report['people'];
    $peopleKpis = $people['kpis'];
    $products = $report['products'];
    $productKpis = $products['kpis'];
    $badgeClass = fn (string $tone) => in_array($tone, ['green','amber','red','blue'], true) ? $tone : 'blue';
?>

    <div class="ii-crumb"><b>Analytics</b> &nbsp;/&nbsp; Inquiry Intelligence</div>
    <header class="ii-head">
        <div>
            <h1>Inquiry Intelligence</h1>
            <p>Portfolio-wide visibility into inquiry volume, execution, assignee performance and improvement opportunities.</p>
        </div>
        <div class="ii-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('reports','export')): ?>
                <button type="button" class="ii-btn" wire:click="exportVisible" wire:loading.attr="disabled" wire:target="exportVisible">↓ Export visible inquiries</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" class="ii-btn ii-primary" onclick="window.print()">⤓ Download / Print PDF</button>
        </div>
    </header>

    <section class="ii-filters" aria-label="Dashboard filters">
        <div class="ii-field ii-search">
            <label>Search</label>
            <input type="search" wire:model.live.debounce.400ms="search" placeholder="Reference, title, product or assignee">
        </div>
        <div class="ii-field">
            <label>Period</label>
            <select wire:model.live="period">
                <option value="month"><?php echo e(app(\App\Services\WorkspaceSettingsService::class)->localNow()->format('F Y')); ?></option>
                <option value="30d">Last 30 days</option>
                <option value="qtd">Quarter to date</option>
                <option value="ytd">Year to date</option>
            </select>
        </div>
        <div class="ii-field">
            <label>Status</label>
            <select wire:model.live="status">
                <option value="">All statuses</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $report['filters']['statuses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($option); ?>"><?php echo e($option); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>
        <div class="ii-field">
            <label>Priority</label>
            <select wire:model.live="priority">
                <option value="">All priorities</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $report['filters']['priorities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($option); ?>"><?php echo e($option); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>
        <div class="ii-field">
            <label>Assignee</label>
            <select wire:model.live="assigneeId">
                <option value="0">All assignees</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $report['filters']['assignees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($option['id']); ?>"><?php echo e($option['name']); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>
        <button type="button" class="ii-btn ii-filter-reset" wire:click="resetFilters">Reset</button>
    </section>

    <nav class="ii-tabs" aria-label="Inquiry intelligence sections">
        <button type="button" class="ii-tab <?php echo e($activeTab === 'portfolio' ? 'active' : ''); ?>" wire:click="setTab('portfolio')">Portfolio overview</button>
        <button type="button" class="ii-tab <?php echo e($activeTab === 'people' ? 'active' : ''); ?>" wire:click="setTab('people')">Assignee performance</button>
        <button type="button" class="ii-tab <?php echo e($activeTab === 'products' ? 'active' : ''); ?>" wire:click="setTab('products')">Product performance</button>
    </nav>

    <section class="ii-panel <?php echo e($activeTab === 'portfolio' ? 'active' : ''); ?>" <?php if($activeTab !== 'portfolio'): ?> hidden <?php endif; ?>>
        <div class="ii-sect"><div><h2>Inquiry performance at a glance</h2><p>Management indicators recalculated from the active filters</p></div><small><?php echo e($report['period']['label']); ?></small></div>
        <div class="ii-grid6">
            <article class="ii-card ii-kpi"><div class="ii-label">Total inquiries</div><div class="ii-big"><?php echo e(number_format($pk['total'])); ?></div><div class="ii-trend">Visible in the selected period</div><div class="ii-track"><i style="width:100%"></i></div></article>
            <article class="ii-card ii-kpi ii-warn"><div class="ii-label">Open inquiries</div><div class="ii-big"><?php echo e(number_format($pk['open'])); ?></div><div class="ii-trend">Still moving through inquiry workflow</div><div class="ii-track"><i style="width:<?php echo e($pk['total'] ? round($pk['open']/$pk['total']*100) : 0); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-good"><div class="ii-label">Completed inquiries</div><div class="ii-big"><?php echo e(number_format($pk['completed'])); ?></div><div class="ii-trend">Workflow completed or final result recorded</div><div class="ii-track"><i style="width:<?php echo e($pk['total'] ? round($pk['completed']/$pk['total']*100) : 0); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-good"><div class="ii-label">Task completion</div><div class="ii-big"><?php echo e(number_format($pk['task_completion'],1)); ?><small>%</small></div><div class="ii-trend"><?php echo e(number_format($pk['task_done'])); ?> of <?php echo e(number_format($pk['task_total'])); ?> workflow tasks</div><div class="ii-track"><i style="width:<?php echo e(min(100,$pk['task_completion'])); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-good"><div class="ii-label">File compliance</div><div class="ii-big"><?php echo e($pk['file_compliance']); ?><small>%</small></div><div class="ii-trend"><?php echo e($pk['evidenced']); ?> of <?php echo e($pk['evidence_total']); ?> completed required-file tasks evidenced</div><div class="ii-track"><i style="width:<?php echo e($pk['file_compliance']); ?>%"></i></div></article>
            <article class="ii-card ii-kpi ii-warn"><div class="ii-label">Structured products</div><div class="ii-big"><?php echo e($pk['structured_products']); ?><small>%</small></div><div class="ii-trend"><?php echo e($pk['structured_count']); ?> inquiries contain structured product lines</div><div class="ii-track"><i style="width:<?php echo e($pk['structured_products']); ?>%"></i></div></article>
        </div>

        <div class="ii-sect"><div><h2>Volume and outcome</h2><p>Inquiry creation and workflow result across the selected period</p></div></div>
        <div class="ii-layout">
            <article class="ii-card">
                <div class="ii-cardhead"><h3>Inquiry activity</h3><div class="ii-legend"><span><i></i>Created</span><span><i class="green"></i>Completed</span></div></div>
                <div class="ii-chartbody ii-trendchart">
                    <div class="ii-plot">
                        <svg viewBox="0 0 700 180" preserveAspectRatio="none" aria-label="Inquiry trend chart">
                            <polygon points="<?php echo e($portfolio['trend']['created_fill_points']); ?>" fill="rgba(18,104,245,.11)"></polygon>
                            <polyline points="<?php echo e($portfolio['trend']['created_points']); ?>" fill="none" stroke="#1268f5" stroke-width="3"></polyline>
                            <polyline points="<?php echo e($portfolio['trend']['completed_points']); ?>" fill="none" stroke="#168451" stroke-width="3" stroke-dasharray="6 5"></polyline>
                        </svg>
                    </div>
                    <div class="ii-xaxis"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $portfolio['trend']['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($label); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div>
                </div>
            </article>
            <div class="ii-stack">
                <article class="ii-card">
                    <div class="ii-cardhead"><h3>Status mix</h3><span><?php echo e(number_format($pk['total'])); ?> inquiries</span></div>
                    <div class="ii-donuts">
                        <?php $openPct = $pk['total'] ? round($pk['open']/$pk['total']*100) : 0; $completedPct = $pk['total'] ? round($pk['completed']/$pk['total']*100) : 0; ?>
                        <div class="ii-donutbox"><div class="ii-donut" style="--p:<?php echo e($openPct); ?>"><div><b><?php echo e($openPct); ?>%</b><small>Open</small></div></div><small><?php echo e($pk['open']); ?> inquiries</small></div>
                        <div class="ii-donutbox"><div class="ii-donut" style="--p:<?php echo e($completedPct); ?>;--c:var(--ii-green)"><div><b><?php echo e($completedPct); ?>%</b><small>Completed</small></div></div><small><?php echo e($pk['completed']); ?> inquiries</small></div>
                    </div>
                </article>
            </div>
        </div>

        <div class="ii-sect"><div><h2>Management attention</h2><p>Exceptions and decision signals across the inquiry portfolio</p></div></div>
        <div class="ii-layout">
            <article class="ii-card"><div class="ii-attention">
                <div class="ii-alert ii-amber"><b><?php echo e(number_format($portfolio['attention']['attention_count'])); ?> inquiries require attention</b><p>Includes inquiry-level attention and open task statuses configured to require attention in Master Data.</p></div>
                <div class="ii-alert ii-red"><b><?php echo e(number_format($portfolio['attention']['unstructured_count'])); ?> inquiries lack structured product lines</b><p>Product analysis is limited where requests remain only in descriptions or attachments.</p></div>
                <div class="ii-alert"><b><?php echo e(number_format($portfolio['attention']['overdue_tasks'])); ?> open inquiry tasks are overdue</b><p>Calculated from the current task due date and completion state.</p></div>
            </div></article>
            <article class="ii-card"><div class="ii-cardhead"><h3>Priority mix</h3><span>Current filters</span></div><div class="ii-bars">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $portfolio['priority_mix']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priorityRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ii-barrow"><span><?php echo e($priorityRow['name']); ?></span><div class="ii-bar"><i style="width:<?php echo e($priorityRow['width']); ?>%"></i></div><b><?php echo e($priorityRow['count']); ?></b></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ii-empty-inline">No priority data.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></article>
        </div>

        <div class="ii-sect"><div><h2>All inquiries</h2><p>Filterable register of every inquiry available to this report</p></div><small><?php echo e(number_format($portfolio['row_count'])); ?> results</small></div>
        <article class="ii-card ii-tablewrap">
            <table>
                <thead><tr><th>Reference / inquiry</th><th>Product signal</th><th>Created</th><th>Priority</th><th>Lead assignee</th><th>Progress</th><th>Status</th><th>Attention</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $portfolio['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><b><?php echo e($row['reference']); ?></b><small><?php echo e($row['subject']); ?></small></td>
                        <td><?php echo e($row['product']); ?></td>
                        <td><?php echo e($row['created']); ?></td>
                        <td><span class="ii-badge ii-blue"><?php echo e($row['priority']); ?></span></td>
                        <td><?php echo e($row['assignee']); ?></td>
                        <td><b><?php echo e($row['progress']); ?>%</b><small><?php echo e($row['progress_text']); ?></small></td>
                        <td><span class="ii-status"><i class="ii-dot <?php echo e(in_array($row['status'], ['Completed','Converted'], true) ? 'green' : ''); ?>"></i><?php echo e($row['status']); ?></span></td>
                        <td><span class="ii-badge ii-<?php echo e($badgeClass($row['attention_tone'])); ?>"><?php echo e($row['attention']); ?></span></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="8"><div class="ii-empty-inline">No inquiries match the selected filters.</div></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </article>
    </section>

    <section class="ii-panel <?php echo e($activeTab === 'people' ? 'active' : ''); ?>" <?php if($activeTab !== 'people'): ?> hidden <?php endif; ?>>
        <div class="ii-sect"><div><h2>Assignee performance center</h2><p>Speed, productivity, efficiency, quality and workload across the inquiry team</p></div><small><?php echo e($report['period']['label']); ?> · live data</small></div>
        <div class="ii-demo-banner"><div><b>FlowTrack operational scoring</b>Metrics use actual inquiry-task timestamps, due dates, reopen events and workflow output. Employees with fewer than 10 completed tasks remain visible but are marked as insufficient data for formal ranking.</div><span class="ii-badge ii-blue">Live data</span></div>

        <div class="ii-sect"><div><h2>Team summary</h2><p>Headline capacity and execution indicators</p></div></div>
        <div class="ii-people-kpis">
            <article class="ii-card ii-smallkpi"><div class="ii-label">Employee roster</div><div class="ii-big"><?php echo e(number_format($peopleKpis['roster'])); ?></div><div class="ii-trend">Active users in FlowTrack</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Active this period</div><div class="ii-big"><?php echo e(number_format($peopleKpis['active'])); ?></div><div class="ii-trend">Assignees with inquiry tasks</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Tasks assigned</div><div class="ii-big"><?php echo e(number_format($peopleKpis['assigned'])); ?></div><div class="ii-trend">Across filtered inquiries</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Tasks completed</div><div class="ii-big"><?php echo e(number_format($peopleKpis['completed'])); ?></div><div class="ii-trend"><?php echo e($peopleKpis['completion_rate']); ?>% completion rate</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Avg completion time</div><div class="ii-big"><?php echo e(number_format($peopleKpis['avg_hours'],1)); ?><small>h</small></div><div class="ii-trend">Start/assignment to completion</div></article>
            <article class="ii-card ii-smallkpi ii-warn"><div class="ii-label">On-time completion</div><div class="ii-big"><?php echo e(number_format($peopleKpis['on_time'],1)); ?><small>%</small></div><div class="ii-trend">Target ≥ 95%</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">First-pass quality</div><div class="ii-big"><?php echo e(number_format($peopleKpis['quality'],1)); ?><small>%</small></div><div class="ii-trend">No recorded task reopen</div></article>
        </div>

        <div class="ii-sect"><div><h2>Performance highlights</h2><p>Ranked on cycle time, SLA, completion and quality — not speed alone</p></div></div>
        <div class="ii-rankgrid">
            <?php
                $highlightCards = [
                    ['key'=>'best','class'=>'','eyebrow'=>'Best overall performer'],
                    ['key'=>'throughput','class'=>'ii-watch','eyebrow'=>'Highest throughput'],
                    ['key'=>'coaching','class'=>'ii-attn','eyebrow'=>'Coaching priority'],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $highlightCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php $person = $people['highlights'][$card['key']] ?? null; ?>
                <article class="ii-card ii-rankcard <?php echo e($card['class']); ?>">
                    <div class="ii-rankeyebrow"><?php echo e($card['eyebrow']); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($person): ?>
                        <div class="ii-rankperson"><span class="ii-face"><?php echo e($person['initials']); ?></span><div><h3><?php echo e($person['name']); ?></h3><p><?php echo e($person['completed']); ?> completed tasks · <?php echo e($person['signal']['label']); ?></p></div></div>
                        <div class="ii-rankstats"><div><b><?php echo e(number_format($person['avg_hours'],1)); ?>h</b><span>Avg cycle</span></div><div><b><?php echo e(number_format($person['on_time'],0)); ?>%</b><span>On time</span></div><div><b><?php echo e($person['efficiency']); ?></b><span>Efficiency</span></div></div>
                    <?php else: ?>
                        <div class="ii-empty-inline">No assignee data in this period.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="ii-sect"><div><h2>Employee performance ranking</h2><p>Minimum 10 completed tasks; lower cycle time is better, while quality and SLA protect against speed-only ranking</p></div><small>Sorted by efficiency score</small></div>
        <article class="ii-card ii-tablewrap"><table><thead><tr><th>Rank</th><th>Assignee</th><th>Assigned</th><th>Completed</th><th>Completion</th><th>Avg hours</th><th>On time</th><th>Reopen</th><th>Efficiency</th><th>Management signal</th></tr></thead><tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $people['ranking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td class="ii-rankno"><?php echo e(str_pad((string)$row['rank'],2,'0',STR_PAD_LEFT)); ?></td>
                    <td><div class="ii-emp-name"><span class="ii-face"><?php echo e($row['initials']); ?></span><b><?php echo e($row['name']); ?></b></div></td>
                    <td><?php echo e($row['assigned']); ?></td><td><?php echo e($row['completed']); ?></td><td><?php echo e(number_format($row['completion'],1)); ?>%</td>
                    <td class="ii-hours <?php echo e($row['avg_hours'] > 8 ? 'slow' : ($row['avg_hours'] > 0 && $row['avg_hours'] <= 2 ? 'fast' : '')); ?>"><?php echo e(number_format($row['avg_hours'],1)); ?>h</td>
                    <td><?php echo e(number_format($row['on_time'],1)); ?>%</td><td><?php echo e(number_format($row['reopen'],1)); ?>%</td>
                    <td><span class="ii-scorepill <?php echo e($row['efficiency'] < 60 ? 'low' : ($row['efficiency'] < 80 ? 'mid' : '')); ?>"><?php echo e($row['efficiency']); ?></span></td>
                    <td><span class="ii-badge ii-<?php echo e($badgeClass($row['signal']['tone'])); ?>"><?php echo e($row['signal']['label']); ?></span></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="10"><div class="ii-empty-inline">No assignee activity matches the selected filters.</div></td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody></table></article>

        <div class="ii-sect"><div><h2>Assignee inquiry-to-order conversion</h2><p>Completed inquiry handoffs converted into linked FlowTrack orders, attributed to the inquiry owner or latest task assignee</p></div><small>Current filtered period</small></div>
        <article class="ii-card ii-tablewrap"><table><thead><tr><th>Assignee</th><th>Completed inquiries</th><th>Orders converted</th><th>Conversion rate</th><th>Vs team average</th><th>Interpretation</th></tr></thead><tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $people['conversion']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr><td><b><?php echo e($row['name']); ?></b></td><td><?php echo e($row['completed_inquiries']); ?></td><td><b><?php echo e($row['orders']); ?></b></td><td class="ii-conversion"><?php echo e(number_format($row['conversion'],1)); ?>%</td><td><span class="ii-badge ii-<?php echo e($badgeClass($row['tone'])); ?>"><?php echo e($row['delta'] >= 0 ? '+' : ''); ?><?php echo e(number_format($row['delta'],1)); ?> pts</span></td><td><?php echo e($row['interpretation']); ?></td></tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="6"><div class="ii-empty-inline">No completed inquiry conversion data in this period.</div></td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody></table></article>

        <div class="ii-sect"><div><h2>Task start-to-completion detail</h2><p>Event-level audit trail for cycle-time verification and coaching</p></div></div>
        <div class="ii-focusbar">
            <div class="ii-subtabs">
                <button type="button" class="ii-subtab <?php echo e($taskTab === 'recent' ? 'active' : ''); ?>" wire:click="setTaskTab('recent')">Recent completions</button>
                <button type="button" class="ii-subtab <?php echo e($taskTab === 'longest' ? 'active' : ''); ?>" wire:click="setTaskTab('longest')">Longest tasks</button>
                <button type="button" class="ii-subtab <?php echo e($taskTab === 'reopened' ? 'active' : ''); ?>" wire:click="setTaskTab('reopened')">Reopened tasks</button>
            </div>
            <div class="ii-field"><label>Focus employee</label><select wire:model.live="employeeFocus"><option value="all">All active employees</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $people['ranking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($row['name']); ?>"><?php echo e($row['name']); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
        </div>
        <article class="ii-card ii-tablewrap"><table><thead><tr><th>Assignee</th><th>Inquiry</th><th>Task</th><th>Started / assigned</th><th>Completed</th><th>Hours taken</th><th>SLA</th><th>Quality</th></tr></thead><tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->taskRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr><td><b><?php echo e($row['assignee']); ?></b></td><td><?php echo e($row['inquiry']); ?></td><td><?php echo e($row['task']); ?></td><td class="ii-timecell"><?php echo e($row['started']); ?></td><td class="ii-timecell"><?php echo e($row['completed']); ?></td><td class="ii-hours <?php echo e($row['hours_value'] !== null && $row['hours_value'] > 8 ? 'slow' : ($row['hours_value'] !== null && $row['hours_value'] <= 2 ? 'fast' : '')); ?>"><?php echo e($row['hours']); ?></td><td><span class="ii-badge ii-<?php echo e($badgeClass($row['sla_tone'])); ?>"><?php echo e($row['sla']); ?></span></td><td><?php echo e($row['quality']); ?></td></tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="8"><div class="ii-empty-inline">No task events match this focus.</div></td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody></table></article>

        <div class="ii-sect"><div><h2>KPI definitions and guardrails</h2><p>A fair score separates throughput, cycle time, due-date reliability and recorded rework</p></div></div>
        <div class="ii-metric-defs">
            <article class="ii-card ii-metricdef"><b>Efficiency score · 30%</b><p>Relative cycle speed compared with the team median for completed inquiry tasks.</p></article>
            <article class="ii-card ii-metricdef"><b>On-time completion · 25%</b><p>Completed tasks finished on or before their configured due date.</p></article>
            <article class="ii-card ii-metricdef"><b>Productivity · 20%</b><p>Completed-task output compared with the team median for the selected period.</p></article>
            <article class="ii-card ii-metricdef"><b>First-pass quality · 15%</b><p>Tasks completed without a recorded <span class="ii-codekey">inquiry.task_reopened</span> event.</p></article>
            <article class="ii-card ii-metricdef"><b>Workload reliability · 10%</b><p>Open backlog adjusted for tasks that are already overdue.</p></article>
            <article class="ii-card ii-metricdef"><b>Minimum sample rule</b><p>Do not formally rank employees with fewer than 10 completed tasks in the selected period.</p></article>
            <article class="ii-card ii-metricdef"><b>Data scope</b><p>Only inquiries visible under the signed-in user’s Inquiry access scope are included.</p></article>
            <article class="ii-card ii-metricdef"><b>Coaching, not punishment</b><p>Use low scores to diagnose training, workload, supplier or process issues before judging performance.</p></article>
        </div>

    </section>

    <section class="ii-panel <?php echo e($activeTab === 'products' ? 'active' : ''); ?>" <?php if($activeTab !== 'products'): ?> hidden <?php endif; ?>>
        <div class="ii-sect"><div><h2>Product performance center</h2><p>Demand, inquiry workload, turnaround, conversion and recurring client questions</p></div><small><?php echo e($report['period']['label']); ?> · live data</small></div>
        <div class="ii-demo-banner"><div><b>Product analytics uses Inquiry Product &amp; Quantity lines</b>Category and quantity metrics are based only on structured inquiry items. Requests kept exclusively in descriptions or attachments remain visible in coverage gaps instead of being guessed.</div><span class="ii-badge ii-blue">Live data</span></div>

        <div class="ii-sect"><div><h2>Product portfolio summary</h2><p>Commercial and operational indicators for inquiry-led demand</p></div></div>
        <div class="ii-product-kpis">
            <article class="ii-card ii-smallkpi"><div class="ii-label">Product inquiries</div><div class="ii-big"><?php echo e(number_format($productKpis['product_inquiries'])); ?></div><div class="ii-trend">Inquiries with structured product lines</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Completed workflows</div><div class="ii-big"><?php echo e(number_format($productKpis['completed'])); ?></div><div class="ii-trend">Product inquiries completed</div></article>
            <article class="ii-card ii-smallkpi ii-good"><div class="ii-label">Converted to order</div><div class="ii-big"><?php echo e(number_format($productKpis['converted'])); ?></div><div class="ii-trend"><?php echo e(number_format($productKpis['conversion'],1)); ?>% of completed product inquiries</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Avg quote workflow time</div><div class="ii-big"><?php echo e(number_format($productKpis['avg_quote_hours'],1)); ?><small>h</small></div><div class="ii-trend">Inquiry start to workflow completion</div></article>
            <article class="ii-card ii-smallkpi ii-warn"><div class="ii-label">Product data coverage</div><div class="ii-big"><?php echo e(number_format($productKpis['data_coverage'],0)); ?><small>%</small></div><div class="ii-trend"><?php echo e(number_format(100-$productKpis['data_coverage'],0)); ?>% needs structured product lines</div></article>
            <article class="ii-card ii-smallkpi"><div class="ii-label">Top demand category</div><div class="ii-big ii-category-big"><?php echo e($productKpis['top_category']); ?></div><div class="ii-trend"><?php echo e($productKpis['top_category_share']); ?>% of structured product inquiries</div></article>
        </div>

        <div class="ii-sect"><div><h2>Category performance</h2><p>Compare demand with workflow speed and conversion, not inquiry count alone</p></div></div>
        <div class="ii-product-layout">
            <article class="ii-card ii-tablewrap"><table><thead><tr><th>Product category</th><th>Inquiries</th><th>Demand share</th><th>Avg workflow time</th><th>Completed</th><th>Conversion</th><th>Avg quantity</th><th>Signal</th></tr></thead><tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><b><?php echo e($row['category']); ?></b><small><?php echo e($row['sample_product'] ?: 'Structured inquiry products'); ?></small></td><td><?php echo e($row['inquiries']); ?></td><td><?php echo e($row['share']); ?>%</td><td class="ii-hours <?php echo e($row['avg_hours'] > 24 ? 'slow' : ($row['avg_hours'] > 0 && $row['avg_hours'] <= 4 ? 'fast' : '')); ?>"><?php echo e(number_format($row['avg_hours'],1)); ?>h</td><td><?php echo e($row['completed']); ?></td><td><b><?php echo e(number_format($row['conversion'],1)); ?>%</b></td><td><?php echo e($row['avg_quantity'] !== null ? number_format($row['avg_quantity']).' pcs' : '—'); ?></td><td><span class="ii-badge ii-<?php echo e($badgeClass($row['signal']['tone'])); ?>"><?php echo e($row['signal']['label']); ?></span></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="8"><div class="ii-empty-inline">No structured product data matches the selected filters.</div></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody></table></article>
            <div class="ii-stack">
                <article class="ii-card"><div class="ii-cardhead"><h3>Inquiry demand share</h3><span><?php echo e(number_format($productKpis['product_inquiries'])); ?> total</span></div><div class="ii-bars">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products['demand_bars']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ii-barrow"><span><?php echo e($row['category']); ?></span><div class="ii-bar"><i style="width:<?php echo e($row['width']); ?>%"></i></div><b><?php echo e($row['inquiries']); ?></b></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ii-empty-inline">No demand data.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div></article>
                <article class="ii-card ii-product-insight"><h3>Management reading</h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products['insights']['top']): ?><p><?php echo e($products['insights']['top']['category']); ?> currently creates the most structured inquiry demand. Compare its workflow time with conversion before prioritizing templates or supplier changes.</p><?php else: ?><p>Structured product data is not yet available for the selected period.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products['insights']['slowest']): ?><div class="ii-alert ii-amber"><b><?php echo e($products['insights']['slowest']['category']); ?> has the longest average workflow time</b><p>Review sourcing, task wait states and product complexity before attributing delay to an employee.</p></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </article>
            </div>
        </div>

        <div class="ii-sect"><div><h2>Recurring product-related queries</h2><p>Common themes detected from inquiry subject and requirement text</p></div></div>
        <div class="ii-product-layout">
            <article class="ii-card"><div class="ii-cardhead"><h3>Top query themes</h3><span>Mentions can overlap</span></div><div class="ii-querycloud">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products['query_themes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span class="ii-querytag"><?php echo e($row['label']); ?> <b><?php echo e($row['count']); ?></b></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><span class="ii-querytag">No recurring themes detected</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></article>
            <article class="ii-card ii-product-insight"><h3>Suggested knowledge content</h3><p>Use recurring question themes to build reusable product briefs, quantity guidance, decoration rules, freight assumptions and lead-time ranges.</p><div class="ii-alert"><b>Recommended product KPI</b><p>Track clarification messages per inquiry once message/event classification is available.</p></div></article>
        </div>

        <div class="ii-sect"><div><h2>Product KPI framework</h2><p>Measures management can use from the records already linked in FlowTrack</p></div></div>
        <article class="ii-card ii-tablewrap"><table><thead><tr><th>KPI</th><th>Definition</th><th>Suggested target</th><th>Decision supported</th></tr></thead><tbody>
            <tr><td><b>Inquiry demand</b></td><td>Unique inquiries by structured product category and period</td><td>Trend, no fixed target</td><td>Catalog and sales focus</td></tr>
            <tr><td><b>Workflow turnaround</b></td><td>Inquiry start/create → completed inquiry workflow, by category</td><td>Company policy</td><td>Templates, staffing and supplier panels</td></tr>
            <tr><td><b>Workflow completion rate</b></td><td>Completed product inquiries ÷ structured product inquiries</td><td>Upward trend</td><td>Identify abandoned or blocked demand</td></tr>
            <tr><td><b>Order conversion</b></td><td>Linked orders ÷ completed product inquiries</td><td>By category</td><td>Pricing and assortment quality</td></tr>
            <tr><td><b>Average quantity</b></td><td>Mean quantity across structured inquiry items</td><td>Trend, no fixed target</td><td>Supplier MOQs and price tiers</td></tr>
            <tr><td><b>Data completeness</b></td><td>Inquiries with at least one structured product line</td><td>100%</td><td>Trustworthy analytics</td></tr>
            <tr><td><b>Attention load</b></td><td>Open product inquiries with configured attention states</td><td>Downward trend</td><td>Operational bottleneck review</td></tr>
            <tr><td><b>Query theme frequency</b></td><td>Keyword themes detected from request text</td><td>Downward after content fixes</td><td>Product content quality</td></tr>
        </tbody></table></article>

        <div class="ii-sect"><div><h2>Product improvement actions</h2><p>Recommended response to the current report signals</p></div></div>
        <div class="ii-recommend">
            <article class="ii-card ii-rec"><div class="ii-num">01 · Top demand</div><h3><?php echo e($products['insights']['top']['category'] ?? 'Product categories'); ?></h3><p>Build repeatable quote and workflow templates around the highest-demand structured category.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">02 · Product master</div><h3>Require structured product selection</h3><p><?php echo e(number_format($products['insights']['coverage_gap'],0)); ?>% of filtered inquiries currently lack structured product lines and cannot be categorized reliably.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">03 · Best conversion</div><h3><?php echo e($products['insights']['best_conversion']['category'] ?? 'Conversion playbook'); ?></h3><p>Document the workflow, product and handoff practices behind the strongest observed conversion rate.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">04 · Slow categories</div><h3><?php echo e($products['insights']['slowest']['category'] ?? 'Separate complexity from delay'); ?></h3><p>Compare task wait states, sourcing complexity and employee handling time before setting category expectations.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">05 · Commercial linkage</div><h3>Keep inquiries linked to orders</h3><p>Conversion depends on valid Inquiry → Order linkage; maintain that relationship during imports and manual creation.</p></article>
            <article class="ii-card ii-rec"><div class="ii-num">06 · Query reduction</div><h3>Answer common questions upfront</h3><p>Use the recurring themes above to improve product request forms and reduce repeated clarification work.</p></article>
        </div>
    </section>

    <p class="ii-method">Method note: all headline metrics on this page are calculated from FlowTrack records visible to the signed-in user. Task cycle time uses <b>started_at</b> when available and falls back to task creation/assignment time for older records. Product analytics does not infer missing product categories from attachments. Assignee scores with fewer than 10 completed tasks are shown for context but marked as insufficient data.</p>
    <footer class="ii-pagefoot"><span>StepPromo · Inquiry Intelligence</span><span>Management analytics · <?php echo e($report['period']['label']); ?></span></footer>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/reports/index.blade.php ENDPATH**/ ?>