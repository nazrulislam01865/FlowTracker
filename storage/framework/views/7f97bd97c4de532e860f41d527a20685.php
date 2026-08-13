<section class="ft-panel" id="tagged">
    <div class="ft-panel-head">
        <div><h2 class="ft-panel-title">Tagged comments <span id="unread-count"><?php echo e($unreadMentionCount); ?> unread</span></h2><div class="ft-panel-note"><?php echo e($administratorView ? 'All mentions across orders, tasks and inquiries' : 'Your mentions from comments and descriptions across orders, tasks and inquiries'); ?></div></div>
        <button class="ft-link" type="button" wire:click="markAllRead" <?php if($unreadMentionCount === 0): echo 'disabled'; endif; ?>>Mark all read</button>
    </div>
    <div class="ft-mention-tabs">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['all' => 'All', 'unread' => 'Unread', 'job' => 'Orders', 'task' => 'Tasks', 'inquiry' => 'Inquiries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button type="button" class="ft-tab <?php echo e($filter === $key ? 'active' : ''); ?>" wire:click="setFilter('<?php echo e($key); ?>')"><?php echo e($label); ?></button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <div class="ft-mentions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $mentions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mention): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $route = app(\App\Services\NotificationService::class)->urlFor($mention);
                $actor = $mention->actor;
                $actorName = $actor?->name;

                // Legacy mention rows created before actor_id existed still keep
                // the actor's name in the title. Use it as a safe initials fallback
                // if the migration could not resolve a unique user record.
                if (!$actorName && preg_match('/^(.*?) mentioned (?:you|a user) in /u', (string) $mention->title, $actorMatch)) {
                    $actorName = trim((string) ($actorMatch[1] ?? ''));
                }

                $actorName = $actorName ?: 'FlowTrack';
                $messagePreview = str(app(\App\Services\MentionService::class)->displayText($mention->message))->limit(90);
                $contextLabel = $mention->inquiry_task_id
                    ? (($mention->inquiry?->inquiry_number ?: 'Inquiry').' · '.($mention->inquiryTask?->title ?: 'Task'))
                    : ($mention->inquiry_id
                        ? ($mention->inquiry?->inquiry_number ?: 'Inquiry')
                        : ($mention->task?->task_number ?: ($mention->job?->displayOrderNumber() ?: 'Notification')));
            ?>
            <a class="ft-mention <?php echo e($mention->read_at ? '' : 'unread'); ?>" href="<?php echo e($route); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-mention-'.e($mention->id).''; ?>wire:key="dashboard-mention-<?php echo e($mention->id); ?>">
                <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['class' => 'ft-avatar','user' => $actor,'name' => $actorName,'size' => 29]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-avatar','user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actor),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actorName),'size' => 29]); ?>
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
                <span><strong class="ft-mention-copy"><?php echo e($mention->title); ?>: <strong>“<?php echo e($messagePreview); ?>”</strong></strong><span class="ft-mention-meta"><?php echo e($contextLabel); ?></span></span>
                <time class="ft-mention-time"><?php echo e($mention->created_at?->diffForHumans()); ?></time>
            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-panel-empty">No tagged comments in this view.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/dashboard/tagged-comments.blade.php ENDPATH**/ ?>