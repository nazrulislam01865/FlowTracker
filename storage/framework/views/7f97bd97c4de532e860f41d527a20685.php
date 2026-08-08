<section class="ft-panel" id="tagged">
    <div class="ft-panel-head">
        <div><h2 class="ft-panel-title">Tagged comments <span id="unread-count"><?php echo e($unreadMentionCount); ?> unread</span></h2><div class="ft-panel-note">Mentions from jobs and tasks that require your response</div></div>
        <button class="ft-link" type="button" wire:click="markAllRead" <?php if($unreadMentionCount === 0): echo 'disabled'; endif; ?>>Mark all read</button>
    </div>
    <div class="ft-mention-tabs">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['all' => 'All', 'unread' => 'Unread', 'job' => 'Jobs', 'task' => 'Tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button type="button" class="ft-tab <?php echo e($filter === $key ? 'active' : ''); ?>" wire:click="setFilter('<?php echo e($key); ?>')"><?php echo e($label); ?></button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <div class="ft-mentions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $mentions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mention): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $route = app(\App\Services\NotificationService::class)->urlFor($mention);
                $initials = collect(preg_split('/\s+/', trim($mention->title)))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
            ?>
            <a class="ft-mention <?php echo e($mention->read_at ? '' : 'unread'); ?>" href="<?php echo e($route); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'dashboard-mention-'.e($mention->id).''; ?>wire:key="dashboard-mention-<?php echo e($mention->id); ?>">
                <span class="ft-avatar"><?php echo e($initials ?: '@'); ?></span>
                <span><strong class="ft-mention-copy"><?php echo e($mention->title); ?>: <strong>“<?php echo e(str($mention->message)->limit(90)); ?>”</strong></strong><span class="ft-mention-meta"><?php echo e($mention->task?->task_number ?: ($mention->job?->displayOrderNumber() ?: 'Notification')); ?></span></span>
                <time class="ft-mention-time"><?php echo e($mention->created_at?->diffForHumans()); ?></time>
            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="ft-panel-empty">No tagged comments in this view.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/dashboard/tagged-comments.blade.php ENDPATH**/ ?>