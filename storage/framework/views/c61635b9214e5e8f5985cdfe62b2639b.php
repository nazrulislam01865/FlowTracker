<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
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
    'job',
    'detailTab',
    'expandedPhaseIds'=>[],
    'taskStatuses'=>collect(),
    'users'=>collect(),
    'mentionUsers'=>collect(),
    'priorities'=>collect(),
    'products'=>collect(),
    'categories'=>collect(),
    'availableDocuments'=>collect(),
    'healthOptions'=>collect(),
    'jobTaskSearch'=>'',
    'activityTab'=>'all',
    'activityPage'=>1,
    'focusComment'=>null,
    'jobDocumentUploads'=>[],
    'showDocumentPicker'=>false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $team = \App\Support\JobDetailPresenter::team($job);
    $tabs = ['overview'=>'Overview','workflow'=>'Workflow','documents'=>'Documents'];
?>
<div <?php echo e($attributes->class('ft-job-detail-page ft-exact-job-detail')); ?>>
    <div class="ft-detail-toolbar ft-exact-job-header">
        <div class="ft-job-heading-copy">
            <div class="ft-detail-breadcrumb ft-id-breadcrumb">
                <span>Orders</span><span>/</span>
                <a class="ft-copyable-id-link" href="<?php echo e(route('jobs.index', ['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                <button type="button" class="ft-copy-id-btn" title="Copy Order ID" aria-label="Copy <?php echo e($job->displayOrderNumber()); ?>" onclick="event.preventDefault(); event.stopPropagation(); navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($job->displayOrderNumber())->toHtml() ?>); this.classList.add('copied'); setTimeout(()=>this.classList.remove('copied'),900)">⧉</button>
            </div>
            <h1
                class="ft-editable-job-title ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('job-'.$job->id.'-title')->toHtml() ?>, label: 'Order name', value: <?php echo \Illuminate\Support\Js::from($job->title)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($job->title)->toHtml() ?> })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span x-show="!editing" x-text="display"><?php echo e($job->title); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app(\App\Services\AccessControlService::class)->canEditVisibleJob(auth()->user(), $job)): ?>
                    <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-pencil" aria-label="Edit order title" title="Edit order name" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.jobTitle.focus())">✎</button>
                    <input x-ref="jobTitle" x-cloak x-show="editing" x-model="draftValue" type="text" maxlength="255"
                        x-on:keydown.escape.prevent="cancelEdit()"
                        x-on:keydown.enter.prevent="$event.target.blur()"
                        x-on:blur="if (editing) { draftValue.trim() === value ? cancelEdit() : commit(draftValue.trim(), draftValue.trim(), () => $wire.updateJobTextField(<?php echo e($job->id); ?>, 'title', draftValue.trim())) }">
                    <?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $attributes = $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c)): ?>
<?php $component = $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c; ?>
<?php unset($__componentOriginal610752b6d86af46dc7d5e0c5ff95106c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h1>
            <div class="ft-exact-job-meta">
                <span><?php echo e($job->client?->name); ?></span>
                <span class="ft-soft-pill <?php echo e(\App\Support\JobDetailPresenter::healthClass($job->health)); ?>"><?php echo e($job->health); ?></span>
                <span class="ft-soft-pill red"><?php echo e($job->priority); ?></span>
                <span class="ft-soft-pill purple"><?php echo e($job->phase?->name ?? $job->status); ?></span>
            </div>
        </div>
        <div class="ft-detail-actions ft-exact-job-team" aria-label="Order team">
            <div class="ft-team-stack">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $team->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $member,'name' => $member->name,'size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($member),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($member->name),'size' => 28]); ?>
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
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->count()>4): ?><span class="ft-avatar-more small">+<?php echo e($team->count()-4); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="ft-detail-tabs ft-exact-tabs">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button class="<?php echo e($detailTab===$key ? 'active' : ''); ?>" wire:click="setDetailTab('<?php echo e($key); ?>')">
                <?php echo e($label); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key==='documents'): ?><span><?php echo e($job->relationLoaded('documents') ? $job->documents->count() : (int) ($job->documents_count ?? 0)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </nav>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailTab==='overview'): ?>
        <?php if (isset($component)) { $__componentOriginaldad3229fa826ba1f935ba3112a62f4a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-overview','data' => ['job' => $job,'expandedPhaseIds' => $expandedPhaseIds,'taskStatuses' => $taskStatuses,'users' => $users,'mentionUsers' => $mentionUsers,'priorities' => $priorities,'products' => $products,'categories' => $categories,'jobTaskSearch' => $jobTaskSearch,'activityTab' => $activityTab,'activityPage' => $activityPage,'focusComment' => $focusComment,'jobDocumentUploads' => $jobDocumentUploads]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-overview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'expanded-phase-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedPhaseIds),'task-statuses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($taskStatuses),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'mention-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mentionUsers),'priorities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priorities),'products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($products),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'job-task-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobTaskSearch),'activity-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityTab),'activity-page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activityPage),'focus-comment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusComment),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3)): ?>
<?php $attributes = $__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3; ?>
<?php unset($__attributesOriginaldad3229fa826ba1f935ba3112a62f4a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldad3229fa826ba1f935ba3112a62f4a3)): ?>
<?php $component = $__componentOriginaldad3229fa826ba1f935ba3112a62f4a3; ?>
<?php unset($__componentOriginaldad3229fa826ba1f935ba3112a62f4a3); ?>
<?php endif; ?>
    <?php elseif($detailTab==='workflow'): ?>
        <?php if (isset($component)) { $__componentOriginal1a8f8e8240d4ca7d127e32a48c32dddd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1a8f8e8240d4ca7d127e32a48c32dddd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-workflow','data' => ['job' => $job,'users' => $users,'healthOptions' => $healthOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-workflow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users),'health-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($healthOptions)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1a8f8e8240d4ca7d127e32a48c32dddd)): ?>
<?php $attributes = $__attributesOriginal1a8f8e8240d4ca7d127e32a48c32dddd; ?>
<?php unset($__attributesOriginal1a8f8e8240d4ca7d127e32a48c32dddd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1a8f8e8240d4ca7d127e32a48c32dddd)): ?>
<?php $component = $__componentOriginal1a8f8e8240d4ca7d127e32a48c32dddd; ?>
<?php unset($__componentOriginal1a8f8e8240d4ca7d127e32a48c32dddd); ?>
<?php endif; ?>
    <?php elseif($detailTab==='documents'): ?>
        <?php if (isset($component)) { $__componentOriginal23d359266473be775f700b1603db506e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23d359266473be775f700b1603db506e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.jobs.detail-documents','data' => ['job' => $job,'availableDocuments' => $availableDocuments,'jobDocumentUploads' => $jobDocumentUploads,'showDocumentPicker' => $showDocumentPicker]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('jobs.detail-documents'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['job' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'available-documents' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableDocuments),'job-document-uploads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobDocumentUploads),'show-document-picker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDocumentPicker)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23d359266473be775f700b1603db506e)): ?>
<?php $attributes = $__attributesOriginal23d359266473be775f700b1603db506e; ?>
<?php unset($__attributesOriginal23d359266473be775f700b1603db506e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23d359266473be775f700b1603db506e)): ?>
<?php $component = $__componentOriginal23d359266473be775f700b1603db506e; ?>
<?php unset($__componentOriginal23d359266473be775f700b1603db506e); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/jobs/detail.blade.php ENDPATH**/ ?>