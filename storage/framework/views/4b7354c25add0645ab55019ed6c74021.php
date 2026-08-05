<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'jobs','jobSummary','clients','phases','users','priorities','healthOptions','jobStatuses',
    'phaseFilter'=>'','healthFilter'=>'','quickFilter'=>'all','showMoreFilters'=>false,'selectedJobIds'=>[],
    'allFilteredJobsSelected'=>false,
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
    'jobs','jobSummary','clients','phases','users','priorities','healthOptions','jobStatuses',
    'phaseFilter'=>'','healthFilter'=>'','quickFilter'=>'all','showMoreFilters'=>false,'selectedJobIds'=>[],
    'allFilteredJobsSelected'=>false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="ft-list-page ft-jobs-list-page ft-exact-jobs-list">
    <div class="ft-list-head">
        <div><h1>Jobs</h1><p>Manage active jobs from request to collection</p></div>
        <div class="ft-list-actions"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','export')): ?><button class="ft-outline-btn" type="button">Export</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><button class="ft-outline-btn" type="button">Columns</button><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','create')): ?><button class="ft-new-job-btn" wire:click="openCreate">＋ New Job</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
    </div>

    <div class="ft-list-view-tabs">
        <button class="ft-list-view-chip red <?php echo e($quickFilter==='attention' ? 'active' : ''); ?>" wire:click="setQuickFilter('attention')">Needs attention <b><?php echo e($jobSummary['attention'] ?? 0); ?></b></button>
        <button class="ft-list-view-chip <?php echo e($quickFilter==='due_week' ? 'active' : ''); ?>" wire:click="setQuickFilter('due_week')">Due this week <b><?php echo e($jobSummary['week'] ?? 0); ?></b></button>
        <button class="ft-list-view-chip <?php echo e($quickFilter==='waiting' ? 'active' : ''); ?>" wire:click="setQuickFilter('waiting')">Waiting for client <b><?php echo e($jobSummary['waiting'] ?? 0); ?></b></button>
        <button class="ft-list-view-chip amber <?php echo e($quickFilter==='invoice' ? 'active' : ''); ?>" wire:click="setQuickFilter('invoice')">Unpaid invoices <b><?php echo e($jobSummary['invoice'] ?? 0); ?></b></button>
        <button class="ft-list-view-chip <?php echo e($quickFilter==='completed' ? 'active' : ''); ?>" wire:click="setQuickFilter('completed')">Completed <b><?php echo e($jobSummary['completed'] ?? 0); ?></b></button>
    </div>

    <section class="ft-job-table-card">
        <div class="ft-job-filter-grid ft-job-filter-grid-direct">
            <label class="ft-filter-search ft-job-list-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input wire:model.live.debounce.300ms="search" placeholder="Search Job ID, order, client or product"></label>
            <select wire:model.live="phase"><option value="">Phase</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <select wire:model.live="health"><option value="">Health</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $healthOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($h); ?>"><?php echo e($h); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <select wire:model.live="owner"><option value="">Owner</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <select wire:model.live="client"><option value="">Client</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <select wire:model.live="delivery"><option value="">Delivery</option><option value="week">Due this week</option><option value="overdue">Overdue</option><option value="none">No delivery date</option></select>
            <select wire:model.live="invoice"><option value="">Invoice</option><option value="pending">Quotation pending</option><option value="draft">Draft / value recorded</option></select>
            <select wire:model.live="priorityFilter"><option value="">Priority</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($p->name); ?>"><?php echo e($p->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <select wire:model.live="jobStatusFilter"><option value="">Job status</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($status); ?>"><?php echo e($status); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <button class="ft-clear-link" wire:click="clearFilters" type="button">× Clear filters</button>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedJobIds)): ?>
            <div class="ft-job-bulk-bar">
                <div><b><?php echo e(count($selectedJobIds)); ?></b> Job<?php echo e(count($selectedJobIds) === 1 ? '' : 's'); ?> selected@if($allFilteredJobsSelected) · all filtered Jobs@endif</div>
                <div class="ft-job-bulk-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','edit')): ?>
                        <button type="button" class="ft-outline-btn" wire:click="bulkUpdateJobs('deactivate')">Deactivate</button>
                        <button type="button" class="ft-outline-btn" wire:click="bulkUpdateJobs('cancel')" wire:confirm="Cancel the selected Jobs?">Cancel Jobs</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','delete')): ?>
                        <button type="button" class="ft-danger-outline-btn" wire:click="bulkUpdateJobs('delete')" wire:confirm="Delete the selected Jobs? This removes them from active FlowTrack views.">Delete</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ft-job-table-wrap">
            <table class="ft-job-table">
                <thead><tr><th><label class="ft-checkbox-head"><input type="checkbox" wire:click="toggleSelectAllJobs" <?php if($allFilteredJobsSelected): echo 'checked'; endif; ?> <?php if($jobs->total() === 0): echo 'disabled'; endif; ?> aria-label="Select all <?php echo e($jobs->total()); ?> filtered Jobs"><span>Select all</span></label></th><th>Job / Order</th><th>Client / Brief</th><th>Product / Qty</th><th>Phase</th><th>Next Action</th><th>Health</th><th>Owner</th><th>Delivery ↓</th><th>Progress</th><th>Invoice</th><th>•••</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php ($next = \App\Support\BoardPresenter::nextTask($job)); ?>
                    <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'job-row-'.e($job->id).''; ?>wire:key="job-row-<?php echo e($job->id); ?>">
                        <td data-label="Select"><input type="checkbox" wire:model.live="selectedJobIds" value="<?php echo e($job->id); ?>" aria-label="Select <?php echo e($job->job_number); ?>"></td>
                        <td data-label="Job / Order"><button class="ft-table-job-link" wire:click="openJob(<?php echo e($job->id); ?>)"><?php echo e($job->job_number); ?></button><div class="ft-table-sub"><?php echo e($job->order_number ?: 'RFQ-'.str_pad((string)$job->id,5,'0',STR_PAD_LEFT)); ?></div></td>
                        <td data-label="Client / Brief"><b><?php echo e($job->client?->name); ?></b><div class="ft-table-sub"><?php echo e(\Illuminate\Support\Str::limit($job->title, 36)); ?></div></td>
                        <td data-label="Product / Qty"><b><?php echo e($job->product ?: 'Product'); ?></b><div class="ft-table-sub"><?php echo e(max(1,(int) $job->items_count)); ?> product · <?php echo e(number_format($job->quantity)); ?> pcs</div></td>
                        <td data-label="Phase"><span class="ft-soft-pill blue"><?php echo e($job->phase?->short_name ?? '—'); ?></span></td>
                        <td data-label="Next Action"><b><?php echo e($next?->title ?? ($job->next_action ?: 'Review client requirement')); ?></b><div class="ft-table-due <?php echo e($next?->due_date?->isPast() ? 'overdue' : ''); ?>"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($next?->due_date): ?><?php echo e($next->due_date->isPast() ? 'Overdue '.$next->due_date->format('M j') : 'Due '.$next->due_date->format('M j')); ?><?php else: ?> — <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></td>
                        <td data-label="Health"><span class="ft-soft-pill <?php echo e(\App\Support\JobDetailPresenter::healthClass($job->needs_attention ? 'Needs Attention' : $job->health)); ?>"><?php echo e($job->needs_attention ? 'Needs Attention' : $job->health); ?></span></td>
                        <td data-label="Owner">
                            <div class="ft-owner-chip ft-inline-owner-editor" x-data="{ editing:false }">
                                <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $job->owner?->name ?? 'Unassigned','size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job->owner?->name ?? 'Unassigned'),'size' => 28]); ?>
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
                                <span x-show="!editing" class="ft-inline-owner-name"><?php echo e($job->owner?->name ?? 'Unassigned'); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app(\App\Services\AccessControlService::class)->canAssignJob(auth()->user(), $job)): ?>
                                    <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit Job owner" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.ownerSelect.focus())">✎</button>
                                    <select x-ref="ownerSelect" x-show="editing" aria-label="Edit Job owner"
                                        x-on:keydown.escape="editing=false"
                                        x-on:blur="editing=false"
                                        wire:change="updateJobOwner(<?php echo e($job->id); ?>, $event.target.value)" x-on:change="editing=false">
                                        <option value="">Unassigned</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($u->id); ?>" <?php if((int)$job->owner_id===(int)$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td data-label="Delivery">
                            <div class="ft-date-chip ft-inline-date-editor <?php echo e($job->delivery_date?->isPast() && !$job->completed_at ? 'overdue' : ''); ?>" x-data="{ editing:false }">
                                <span x-show="!editing" class="ft-inline-date-text"><?php echo e($job->delivery_date?->format('M j') ?? 'Set date'); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app(\App\Services\AccessControlService::class)->canEditJob(auth()->user(), $job)): ?>
                                    <button x-show="!editing" type="button" class="ft-inline-edit-button" aria-label="Edit delivery date" title="Edit" x-on:click.stop="editing=true; $nextTick(() => $refs.deliveryDate.showPicker ? $refs.deliveryDate.showPicker() : $refs.deliveryDate.focus())">✎</button>
                                    <input x-ref="deliveryDate" x-show="editing" type="date" value="<?php echo e($job->delivery_date?->format('Y-m-d')); ?>" aria-label="Edit delivery date"
                                        x-on:keydown.escape="editing=false"
                                        x-on:blur="editing=false"
                                        wire:change="updateJobDeliveryDate(<?php echo e($job->id); ?>, $event.target.value)">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td data-label="Progress"><div class="ft-table-progress"><span style="width:<?php echo e($job->progress); ?>%"></span></div><small><?php echo e($job->progress); ?>%</small></td>
                        <td data-label="Invoice"><span class="ft-soft-pill <?php echo e($job->commercial_value > 0 ? 'blue' : 'amber'); ?>"><?php echo e($job->commercial_value > 0 ? 'Draft $'.number_format($job->commercial_value,0) : 'Quotation pending'); ?></span></td>
                        <td data-label="Actions"><button class="ft-table-kebab" wire:click="openJob(<?php echo e($job->id); ?>)">•••</button></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="12"><div class="empty-state">No Jobs match the selected filters.</div></td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="ft-list-pagination"><span>Showing <b><?php echo e($jobs->firstItem() ?? 0); ?>–<?php echo e($jobs->lastItem() ?? 0); ?></b> of <?php echo e($jobs->total()); ?> jobs</span><div><span>Show</span><select wire:model.live="perPage"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option></select><span>per page</span></div><div class="ft-page-actions"><button wire:click="previousPage" <?php if($jobs->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button><span>Page <?php echo e($jobs->currentPage()); ?> of <?php echo e($jobs->lastPage()); ?></span><button wire:click="nextPage" <?php if(!$jobs->hasMorePages()): echo 'disabled'; endif; ?>>Next</button></div></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/table.blade.php ENDPATH**/ ?>