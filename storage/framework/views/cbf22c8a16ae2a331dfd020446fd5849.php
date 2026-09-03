            <section class="ft-detail-card ft-management-card">
                <h2>Management attention</h2>
                <div class="ft-attention-row"><span>Required evidence</span><b><span class="<?php echo e($taskDocumentName ? 'ft-red-doc-icon' : ''); ?>">▯</span> <?php echo e($taskDocumentName ?: 'No required evidence'); ?></b></div>
                <div class="ft-attention-row ft-task-flag-row">
                    <span>Automatic flag</span>
                    <b class="ft-runtime-flag-pill <?php echo e($currentTaskFlagColor ? 'ft-master-color' : ($currentTaskFlag ? 'danger-text' : '')); ?>" style="<?php echo e(\App\Support\MasterColor::style($currentTaskFlagColor)); ?>"><span class="<?php echo e($currentTaskFlag ? 'ft-red-flag' : ''); ?>">⚑</span> <?php echo e($currentTaskFlag ?: 'No flag'); ?></b>
                </div>
                <small class="ft-task-flag-help">Driven automatically by Order Task Status Master Data. Overdue overrides the status mapping after the due date passes.</small>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentTaskFlag && filled($task->attention_reason)): ?>
                    <div class="ft-attention-row"><span>Flag reason</span><b><?php echo e($task->attention_reason); ?></b></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="ft-detail-card ft-job-context-card"><h2>Order context</h2><b><?php echo e($job?->title); ?></b><div><span>Client</span><b><?php echo e($job?->client?->name); ?></b></div><div><span>Order flag</span><b class="ft-runtime-flag-pill <?php echo e($currentOrderFlagColor ? 'ft-master-color' : ($currentOrderFlag ? 'danger-text' : '')); ?>" style="<?php echo e(\App\Support\MasterColor::style($currentOrderFlagColor)); ?>"><span class="<?php echo e($currentOrderFlag ? 'ft-red-flag' : ''); ?>">⚑</span> <?php echo e($currentOrderFlag ?: 'No flag'); ?></b></div><div><span>Delivery</span><b><?php echo e($job?->delivery_date?->format('M j, Y') ?? '—'); ?></b></div><div class="ft-context-progress"><span>Order progress</span><b><?php echo e($job?->progress); ?>%</b><div class="ft-line-progress"><span style="width:<?php echo e($job?->progress ?? 0); ?>%"></span></div></div><button class="ft-link-blue ft-open-job" wire:click="closeTask">Open order details ↗</button></section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/task-detail/sidebar.blade.php ENDPATH**/ ?>