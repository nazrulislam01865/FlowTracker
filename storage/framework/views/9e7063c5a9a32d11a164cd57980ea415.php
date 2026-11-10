<?php
    $orderTerminology = static function (?string $value): string {
        return preg_replace_callback('/\bjobs?\b/i', static function (array $match): string {
            return match ($match[0]) {
                'Jobs' => 'Orders', 'jobs' => 'orders', 'JOB' => 'ORDER', 'JOBS' => 'ORDERS',
                default => ctype_upper($match[0][0] ?? '') ? 'Order' : 'order',
            };
        }, (string) $value) ?: (string) $value;
    };

?>
    <article class="ft-mgmt-panel ft-mgmt-recent-prototype" data-dashboard-panel="activity">
    <div class="ft-mgmt-dashboard-panel-swap" style="display:contents" wire:replace <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-activity-content'; ?>wire:key="dashboard-activity-content">
            <div class="ft-mgmt-panel-head">
                <div><h2>Recent activity</h2><p>Latest changes from Orders, Inquiries and Tasks</p></div>
                <div class="ft-mgmt-tabs">
                    <button type="button" wire:click="setActivityTab('all')" class="ft-mgmt-tab <?php echo e($activityTab === 'all' ? 'active' : ''); ?>">All</button>
                    <button type="button" wire:click="setActivityTab('orders')" class="ft-mgmt-tab <?php echo e($activityTab === 'orders' ? 'active' : ''); ?>">Orders</button>
                    <button type="button" wire:click="setActivityTab('inquiries')" class="ft-mgmt-tab <?php echo e($activityTab === 'inquiries' ? 'active' : ''); ?>">Inquiries</button>
                    <button type="button" wire:click="setActivityTab('tasks')" class="ft-mgmt-tab <?php echo e($activityTab === 'tasks' ? 'active' : ''); ?>">Tasks</button>
                </div>
            </div>
            <div class="ft-mgmt-activity-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $activityDetail = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode((string) $orderTerminology($activity->dashboard_detail), ENT_QUOTES | ENT_HTML5, 'UTF-8'))) ?? '');
                        $activityKind = (string) ($activity->dashboard_kind ?? 'orders');
                        $activityIsInquiry = $activity->subject_type === \App\Models\Inquiry::class;
                        $activityRoute = ($activityKind === 'inquiries' || $activityIsInquiry)
                            ? route('inquiries.index', ['open' => (int) ($activity->dashboard_parent_id ?? 0)])
                            : route('jobs.index', array_filter([
                                'open' => (int) ($activity->dashboard_parent_id ?? 0),
                                'task' => $activityKind === 'tasks' && (int) ($activity->dashboard_task_id ?? 0) > 0 ? (int) $activity->dashboard_task_id : null,
                            ]));
                    ?>
                    <a class="ft-mgmt-activity" href="<?php echo e($activityRoute); ?>" wire:navigate <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mgmt-activity-'.e($activity->id).''; ?>wire:key="mgmt-activity-<?php echo e($activity->id); ?>">
                        <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $activity->user,'name' => $activity->user?->name ?? 'FlowTrack','size' => 38]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->user),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->user?->name ?? 'FlowTrack'),'size' => 38]); ?>
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
                        <span class="ft-mgmt-activity-copy">
                            <strong><?php echo e($orderTerminology($activity->dashboard_title)); ?></strong>
                            <p><?php echo e($activityDetail !== '' ? $activityDetail : 'Record updated'); ?></p>
                        </span>
                        <time><?php echo e($activity->created_at?->diffForHumans(short: true)); ?></time>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="ft-mgmt-empty">No Order, Inquiry or Task changes match the selected period or filters.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/livewire/dashboard/activity-panel.blade.php ENDPATH**/ ?>