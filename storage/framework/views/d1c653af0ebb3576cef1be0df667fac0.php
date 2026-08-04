<div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCreate): ?>
    <?php if (isset($component)) { $__componentOriginalfa2d9a437a932329040def908f7b2e55 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa2d9a437a932329040def908f7b2e55 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clients.create','data' => ['users' => $users,'clientCode' => $clientCode]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clients.create'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'client-code' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCode)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa2d9a437a932329040def908f7b2e55)): ?>
<?php $attributes = $__attributesOriginalfa2d9a437a932329040def908f7b2e55; ?>
<?php unset($__attributesOriginalfa2d9a437a932329040def908f7b2e55); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa2d9a437a932329040def908f7b2e55)): ?>
<?php $component = $__componentOriginalfa2d9a437a932329040def908f7b2e55; ?>
<?php unset($__componentOriginalfa2d9a437a932329040def908f7b2e55); ?>
<?php endif; ?>
<?php elseif($showDetail && $detail): ?>
    <?php if (isset($component)) { $__componentOriginal55bb30efc6f1e06bfb4e867263eeb751 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55bb30efc6f1e06bfb4e867263eeb751 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clients.detail','data' => ['detail' => $detail,'users' => $users,'editing' => $showEdit]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clients.detail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['detail' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detail),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'editing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showEdit)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55bb30efc6f1e06bfb4e867263eeb751)): ?>
<?php $attributes = $__attributesOriginal55bb30efc6f1e06bfb4e867263eeb751; ?>
<?php unset($__attributesOriginal55bb30efc6f1e06bfb4e867263eeb751); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55bb30efc6f1e06bfb4e867263eeb751)): ?>
<?php $component = $__componentOriginal55bb30efc6f1e06bfb4e867263eeb751; ?>
<?php unset($__componentOriginal55bb30efc6f1e06bfb4e867263eeb751); ?>
<?php endif; ?>
<?php else: ?>
<?php
    $selected = $detail['client'] ?? null;
    $selectedJobs = $detail['jobs'] ?? collect();
    $activeJobs = $detail['active'] ?? collect();
    $attentionTasks = $detail['tasks'] ?? collect();
    $selectedHealth = $detail['health'] ?? 'On Track';
?>
<div class="ft-clients-reference">
    <div class="ft-clients-page-head">
        <div>
            <h1>Clients</h1>
            <p>Monitor client Jobs, task delivery, account health and outstanding balances.</p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('clients','create')): ?>
            <button class="ft-clients-new" type="button" wire:click="openCreate">＋ New Client</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-clients-layout">
        <section class="ft-clients-main">
            <div class="ft-clients-metrics">
                <button type="button" wire:click="setQuick('all')" class="ft-client-metric <?php echo e($quick==='all'?'is-active':''); ?>">
                    <span class="ft-client-metric-icon ft-client-metric-blue">♙</span><span><small>Total clients</small><b><?php echo e(number_format($summary['clients'])); ?></b></span>
                </button>
                <button type="button" wire:click="setQuick('active_jobs')" class="ft-client-metric <?php echo e($quick==='active_jobs'?'is-active':''); ?>">
                    <span class="ft-client-metric-icon ft-client-metric-green">▣</span><span><small>Active Jobs</small><b><?php echo e(number_format($summary['active_jobs'])); ?></b></span>
                </button>
                <button type="button" wire:click="setQuick('attention')" class="ft-client-metric <?php echo e($quick==='attention'?'is-active':''); ?>">
                    <span class="ft-client-metric-icon ft-client-metric-amber">△</span><span><small>Needs attention</small><b><?php echo e(number_format($summary['attention'])); ?></b></span>
                </button>
                <button type="button" wire:click="setQuick('outstanding')" class="ft-client-metric <?php echo e($quick==='outstanding'?'is-active':''); ?>">
                    <span class="ft-client-metric-icon ft-client-metric-purple">$</span><span><small>Outstanding</small><b>$<?php echo e(number_format($summary['outstanding'],0)); ?></b></span>
                </button>
            </div>

            <div class="ft-client-filter-card">
                <div class="ft-client-filter-search"><span>⌕</span><input wire:model.live.debounce.300ms="search" placeholder="Search client, Job ID, country or manager"></div>
                <select wire:model.live="manager"><option value="">Account manager</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $managerOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($managerOption->id); ?>"><?php echo e($managerOption->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="country"><option value="">Country</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $countryOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($countryOption); ?>"><?php echo e($countryOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="jobHealth"><option value="">Job health</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $healthOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $healthOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($healthOption); ?>"><?php echo e($healthOption); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                <select wire:model.live="outstanding"><option value="">Outstanding</option><option value="positive">Has balance</option><option value="high">$10,000+</option><option value="zero">No balance</option></select>
                <button type="button" wire:click="clearFilters">Clear</button>
            </div>

            <div class="ft-client-quick-row">
                <button type="button" wire:click="setQuick('all')" class="<?php echo e($quick==='all'?'active':''); ?>">All clients <span><?php echo e($summary['clients']); ?></span></button>
                <button type="button" wire:click="setQuick('active_jobs')" class="<?php echo e($quick==='active_jobs'?'active':''); ?>">Active Jobs <span><?php echo e($summary['clients_active']); ?></span></button>
                <button type="button" wire:click="setQuick('attention')" class="<?php echo e($quick==='attention'?'active':''); ?>">Needs attention <span><?php echo e($summary['clients_attention']); ?></span></button>
                <button type="button" wire:click="setQuick('outstanding')" class="<?php echo e($quick==='outstanding'?'active':''); ?>">Outstanding balance <span><?php echo e($summary['clients_outstanding']); ?></span></button>
            </div>

            <div class="ft-client-list-card">
                <div class="ft-client-table-scroll">
                    <table class="ft-client-table">
                        <thead><tr><th>Client</th><th>Account manager</th><th>Jobs</th><th>Tasks</th><th>Health</th><th>Next delivery</th><th>Outstanding</th><th>Updated</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $rowHealth = $clientRow->attention_jobs_count > 0 ? 'Needs Attention' : ($clientRow->overdue_tasks_count > 0 ? 'At Risk' : 'On Track');
                                $healthClass = $rowHealth === 'On Track' ? 'green' : ($rowHealth === 'At Risk' ? 'amber' : 'red');
                                $rowInitials = collect(preg_split('/\s+/', trim($clientRow->name)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part,0,1)))->implode('');
                            ?>
                            <tr <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'client-row-'.e($clientRow->id).''; ?>wire:key="client-row-<?php echo e($clientRow->id); ?>" class="<?php echo e((int)$selectedClientId === (int)$clientRow->id ? 'selected' : ''); ?>" wire:click="openClient(<?php echo e($clientRow->id); ?>)">
                                <td><div class="ft-client-identity"><span class="ft-client-logo"><?php echo e($rowInitials ?: 'CL'); ?></span><span><b><?php echo e($clientRow->name); ?></b><small><?php echo e($clientRow->country ?: '—'); ?></small></span></div></td>
                                <td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clientRow->accountManager): ?><div class="ft-client-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $clientRow->accountManager->name,'size' => 26]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientRow->accountManager->name),'size' => 26]); ?>
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
<?php endif; ?><span><?php echo e($clientRow->accountManager->name); ?></span></div><?php else: ?><span class="muted">Unassigned</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                <td><b><?php echo e($clientRow->active_jobs_count); ?> / <?php echo e($clientRow->total_jobs_count); ?></b> active<div class="ft-mini-progress"><span style="width:<?php echo e($clientRow->total_jobs_count ? min(100,round(($clientRow->active_jobs_count/$clientRow->total_jobs_count)*100)) : 0); ?>%"></span></div></td>
                                <td>
                                    <b><?php echo e($clientRow->open_tasks_count); ?></b> open
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $clientRow->overdue_tasks_count > 0): ?>
                                        <small class="ft-text-red"><?php echo e($clientRow->overdue_tasks_count); ?> overdue</small>
                                    <?php elseif((int) $clientRow->blocked_tasks_count > 0): ?>
                                        <small class="ft-text-purple"><?php echo e($clientRow->blocked_tasks_count); ?> blocked</small>
                                    <?php else: ?>
                                        <small class="ft-text-green">0 overdue</small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><span class="ft-client-health <?php echo e($healthClass); ?>"><?php echo e($rowHealth); ?></span></td>
                                <td><?php echo e($clientRow->next_delivery_at ? \Carbon\Carbon::parse($clientRow->next_delivery_at)->format('M j') : '—'); ?></td>
                                <td><b>$<?php echo e(number_format($clientRow->outstanding_balance,0)); ?></b></td>
                                <td><?php echo e($clientRow->updated_at?->diffForHumans(short:true)); ?></td>
                                <td class="ft-client-action-cell">
                                    <button type="button" class="ft-client-more" wire:click.stop="toggleClientMenu(<?php echo e($clientRow->id); ?>)" aria-label="Client actions">⋮</button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($actionMenuClientId === (int)$clientRow->id): ?>
                                        <div class="ft-client-action-menu" x-on:click.stop>
                                            <button type="button" wire:click="viewClient(<?php echo e($clientRow->id); ?>)">View client</button>
                                            <?php
                                                $access = app(\App\Services\AccessControlService::class);
                                                $rowCanEdit = $access->isAdministrator(auth()->user()) || $access->canEditAll(auth()->user(),'clients') || ($access->canEditOwn(auth()->user(),'clients') && (int)$clientRow->account_manager_id === (int)auth()->id());
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rowCanEdit): ?><button type="button" wire:click="editClient(<?php echo e($clientRow->id); ?>)">Edit client</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('clients','delete')): ?><button type="button" class="danger" wire:click="deleteClient(<?php echo e($clientRow->id); ?>)" wire:confirm="Delete this client? Clients with Job history will be archived.">Delete client</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="9" class="ft-client-empty">No clients match the selected filters.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="ft-client-pagination">
                    <span>Showing <?php echo e($clients->firstItem() ?? 0); ?>–<?php echo e($clients->lastItem() ?? 0); ?> of <?php echo e($clients->total()); ?> clients</span>
                    <div><label>Rows per page:</label><select wire:model.live="perPage"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option></select><button type="button" wire:click="previousPage" <?php if($clients->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button><span>Page <?php echo e($clients->currentPage()); ?> of <?php echo e(max(1,$clients->lastPage())); ?></span><button type="button" wire:click="nextPage" <?php if(!$clients->hasMorePages()): echo 'disabled'; endif; ?>>Next →</button></div>
                </div>
            </div>
        </section>

        <aside class="ft-client-detail-card">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
            <?php
                $detailHealthClass = $selectedHealth === 'On Track' ? 'green' : ($selectedHealth === 'At Risk' ? 'amber' : 'red');
                $selectedInitials = collect(preg_split('/\s+/', trim($selected->name)))->filter()->take(2)->map(fn($part) => strtoupper(substr($part,0,1)))->implode('');
            ?>
            <div class="ft-client-detail-head">
                <span class="ft-client-detail-logo"><?php echo e($selectedInitials ?: 'CL'); ?></span>
                <div><h2><?php echo e($selected->name); ?></h2><p><?php echo e($selected->country ?: '—'); ?> <span class="ft-client-health <?php echo e($detailHealthClass); ?>"><?php echo e($selectedHealth); ?></span></p></div>
                <button class="ft-open-client" type="button" wire:click="viewClient(<?php echo e($selected->id); ?>)">Open client</button>
            </div>
            <div class="ft-client-detail-contact">
                <div><small>Account manager</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->accountManager): ?><div class="ft-client-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $selected->accountManager->name,'size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selected->accountManager->name),'size' => 28]); ?>
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
<?php endif; ?><b><?php echo e($selected->accountManager->name); ?></b></div><?php else: ?><b>Unassigned</b><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <div><small>Contact</small><a href="mailto:<?php echo e($selected->email); ?>"><?php echo e($selected->email ?: ($selected->contact_name ?: 'No contact recorded')); ?></a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->phone): ?><span><?php echo e($selected->phone); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            </div>
            <div class="ft-client-detail-stats">
                <div><b><?php echo e($activeJobs->count()); ?></b><small>Active Jobs</small></div><div><b><?php echo e($detail['openTasks']); ?></b><small>Open Tasks</small></div><div><b class="ft-text-red"><?php echo e($detail['overdue']); ?></b><small>Overdue</small></div><div><b>$<?php echo e(number_format($selected->outstanding_balance,0)); ?></b><small>Outstanding</small></div>
            </div>

            <div class="ft-client-detail-section">
                <div class="ft-client-detail-section-head"><h3>Active Jobs</h3><a href="<?php echo e(route('jobs.index',['search'=>$selected->name])); ?>" wire:navigate>View all jobs</a></div>
                <table class="ft-client-mini-table"><thead><tr><th>Job ID</th><th>Job name</th><th>Phase</th><th>Progress</th><th>Next delivery</th><th>Health</th></tr></thead><tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activeJobs->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><a href="<?php echo e(route('jobs.index',['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->job_number); ?></a></td><td><?php echo e($job->title); ?></td><td><?php echo e($job->phase?->name ?? '—'); ?></td><td><b><?php echo e($job->progress); ?>%</b><div class="ft-mini-progress"><span style="width:<?php echo e($job->progress); ?>%"></span></div></td><td><?php echo e($job->delivery_date?->format('M j') ?? '—'); ?></td><td><span class="ft-health-dot <?php echo e(in_array($job->health,['Needs Attention','Blocked','Delayed'])?'red':($job->health==='At Risk'?'amber':'green')); ?>"></span></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="6" class="ft-client-empty">No active Jobs.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody></table>
            </div>

            <div class="ft-client-detail-section ft-client-attention-section">
                <div class="ft-client-detail-section-head"><h3>Tasks needing attention</h3><a href="<?php echo e(route('my-work')); ?>" wire:navigate>View all tasks</a></div>
                <table class="ft-client-mini-table"><thead><tr><th>Task</th><th>Due</th><th>Status</th><th>Assignee</th></tr></thead><tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attentionTasks->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><a href="<?php echo e(route('jobs.index',['open'=>$task->flow_job_id,'task'=>$task->id])); ?>" wire:navigate><?php echo e($task->title); ?></a></td><td class="<?php echo e($task->due_date?->isPast()?'ft-text-red':''); ?>"><?php echo e($task->due_date?->isPast() ? 'Overdue '.$task->due_date->diffInDays(today()).'d' : ($task->due_date?->format('M j') ?? '—')); ?></td><td><span class="ft-client-health <?php echo e($task->needs_attention||$task->status==='Blocked'?'red':'amber'); ?>"><?php echo e($task->needs_attention?'Needs Attention':$task->status); ?></span></td><td><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->assignee): ?><div class="ft-client-person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['name' => $task->assignee->name,'size' => 25]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($task->assignee->name),'size' => 25]); ?>
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
<?php endif; ?><span><?php echo e($task->assignee->name); ?></span></div><?php else: ?><span class="muted">Unassigned</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><tr><td colspan="4" class="ft-client-empty">No tasks need attention.</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody></table>
            </div>
            <a class="ft-view-client-work" href="<?php echo e(route('jobs.index',['search'=>$selected->name])); ?>" wire:navigate>View all client work&nbsp; →</a>
        <?php else: ?>
            <div class="ft-client-empty-detail">Select a client to see its Jobs and Tasks.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </aside>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/clients/index.blade.php ENDPATH**/ ?>