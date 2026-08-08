<div class="ft-documents-page">
    <div class="ft-doc-page-head"><div><h1>Documents</h1><p>Find, upload and manage files across every Order, phase and task.</p></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','create')): ?><button class="primary" wire:click="openUpload">⇧ Upload document</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-doc-layout">
        <div class="ft-doc-main">
            <div class="ft-doc-metrics">
                <button class="card" wire:click="$set('status','')"><span>All files</span><b><?php echo e($metrics['all']); ?></b><i>▤</i></button>
                <button class="card danger" wire:click="$set('status','needs_action')"><span>Needs attention</span><b><?php echo e($metrics['attention']); ?></b><i>△</i></button>
                <button class="card purple" wire:click="$set('status','awaiting_approval')"><span>Awaiting approval</span><b><?php echo e($metrics['approval']); ?></b><i>◷</i></button>
                <button class="card" wire:click="$set('status','recent')"><span>Recently updated</span><b><?php echo e($metrics['recent']); ?></b><i>◴</i></button>
            </div>

            <div class="ft-list-filter-shell">
                <div class="ft-list-filter-grid">
                    <?php if (isset($component)) { $__componentOriginal5dde89dca5800d8916043e79c051883f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5dde89dca5800d8916043e79c051883f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.list-search','data' => ['property' => 'search','value' => $search,'placeholder' => 'File name, document number, Order or task…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.list-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['property' => 'search','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search),'placeholder' => 'File name, document number, Order or task…']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5dde89dca5800d8916043e79c051883f)): ?>
<?php $attributes = $__attributesOriginal5dde89dca5800d8916043e79c051883f; ?>
<?php unset($__attributesOriginal5dde89dca5800d8916043e79c051883f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5dde89dca5800d8916043e79c051883f)): ?>
<?php $component = $__componentOriginal5dde89dca5800d8916043e79c051883f; ?>
<?php unset($__componentOriginal5dde89dca5800d8916043e79c051883f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8724aaa5ef3af2fc9ca32a1db6cf7e11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Order','property' => 'job','type' => 'jobs','context' => 'documents','value' => $job,'placeholder' => 'All orders','initialOptions' => $jobFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Order','property' => 'job','type' => 'jobs','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($job),'placeholder' => 'All orders','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobFilterOptions)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Client','property' => 'client','type' => 'clients','context' => 'documents','value' => $client,'placeholder' => 'All clients','initialOptions' => $clientFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Client','property' => 'client','type' => 'clients','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientFilterOptions)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Phase','property' => 'phase','type' => 'phases','context' => 'documents','value' => $phase,'placeholder' => 'All phases','initialOptions' => $phaseFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Phase','property' => 'phase','type' => 'phases','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phase),'placeholder' => 'All phases','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($phaseFilterOptions)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.remote-filter','data' => ['label' => 'Document type','property' => 'category','type' => 'document-categories','context' => 'documents','value' => $category,'placeholder' => 'All types','initialOptions' => $categoryFilterOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.remote-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Document type','property' => 'category','type' => 'document-categories','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category),'placeholder' => 'All types','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categoryFilterOptions)]); ?>
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
                    <?php if (isset($component)) { $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select-filter','data' => ['label' => 'Status','property' => 'status','value' => $status,'placeholder' => 'All statuses','options' => collect([['id'=>'current','label'=>'Current'],['id'=>'approved','label'=>'Approved'],['id'=>'needs_action','label'=>'Needs attention'],['id'=>'awaiting_approval','label'=>'Awaiting approval'],['id'=>'recent','label'=>'Recently updated']])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status','property' => 'status','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status),'placeholder' => 'All statuses','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect([['id'=>'current','label'=>'Current'],['id'=>'approved','label'=>'Approved'],['id'=>'needs_action','label'=>'Needs attention'],['id'=>'awaiting_approval','label'=>'Awaiting approval'],['id'=>'recent','label'=>'Recently updated']]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $attributes = $__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__attributesOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2)): ?>
<?php $component = $__componentOriginal4c441a1c27191c086ffa43032f3a6cc2; ?>
<?php unset($__componentOriginal4c441a1c27191c086ffa43032f3a6cc2); ?>
<?php endif; ?>
                </div>
                <?php
                    $chips = collect();
                    if($search) $chips->push(['key'=>'search','label'=>'Search: '.$search]);
                    if($job) $chips->push(['key'=>'job','label'=>'Job: '.(collect($jobFilterOptions)->firstWhere('id',(int)$job)['label'] ?? 'Selected')]);
                    if($client) $chips->push(['key'=>'client','label'=>'Client: '.(collect($clientFilterOptions)->firstWhere('id',(int)$client)['label'] ?? 'Selected')]);
                    if($phase) $chips->push(['key'=>'phase','label'=>'Phase: '.(collect($phaseFilterOptions)->firstWhere('id',(int)$phase)['label'] ?? 'Selected')]);
                    if($category) $chips->push(['key'=>'category','label'=>'Type: '.$category]);
                    if($status) $chips->push(['key'=>'status','label'=>'Status: '.(['current'=>'Current','approved'=>'Approved','needs_action'=>'Needs attention','awaiting_approval'=>'Awaiting approval','recent'=>'Recently updated'][$status] ?? $status)]);
                ?>
                <div class="ft-list-active-row">
                    <div class="ft-list-filter-chips"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $chips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span class="ft-list-filter-chip"><?php echo e($chip['label']); ?><button type="button" wire:click="clearFilter('<?php echo e($chip['key']); ?>')">×</button></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><span>No filters applied</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($chips->isNotEmpty()): ?><button type="button" class="ft-list-clear-all" wire:click="clearFilters">Clear all filters</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="ft-doc-expand-row">
                <div></div>
                <div class="ft-group-toggle-actions" aria-label="Document group controls">
                    <button type="button" class="ft-double-chevron-btn" wire:click="expandAll" title="Expand all" aria-label="Expand all">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg>
                    </button>
                    <button type="button" class="ft-double-chevron-btn" wire:click="collapseAll" title="Collapse all" aria-label="Collapse all">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg>
                    </button>
                </div>
            </div>

            <div class="card ft-doc-table-card ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,job,client,phase,category,status">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobId=>$docs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $first=$docs->first();
                    ?>
                    <?php
                        $expanded=$jobId==0 || in_array((int)$jobId,$expandedJobs,true);
                    ?>
                    <div class="ft-doc-job-group">
                        <button class="ft-doc-job-head" <?php if($jobId): ?> wire:click="toggleJob(<?php echo e($jobId); ?>)" <?php endif; ?>>
                            <span class="ft-doc-chevron"><?php echo e($expanded?'⌄':'›'); ?></span>
                            <b><?php echo e($first->job?->displayOrderNumber() ?? 'General documents'); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($first->job): ?> · <?php echo e($first->job->title); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></b>
                            <span class="ft-doc-job-meta"><?php echo e($first->job?->client?->name ?? 'No client'); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($first->job): ?> <em>|</em> Phase: <?php echo e($first->job?->phase?->sequence ?? '—'); ?> <em>·</em> Tasks: <?php echo e($first->job?->tasks?->count() ?? 0); ?> <em>·</em> Documents: <?php echo e($docs->count()); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expanded): ?>
                            <div class="ft-doc-table-scroll"><table class="ft-doc-table"><thead><tr><th>Document</th><th>Linked to</th><th>Type</th><th>Version</th><th>Owner</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead><tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $docStatus=$doc->is_final?'Approved':($doc->task?->needs_attention?'Needs attention':'Current');
                                ?>
                                <tr class="<?php echo e($selected?->id===$doc->id?'selected':''); ?>" wire:click="selectDocument(<?php echo e($doc->id); ?>)">
                                    <td data-label="Document"><div class="ft-doc-file"><span class="ft-file-badge <?php echo e(strtolower(pathinfo($doc->name,PATHINFO_EXTENSION))); ?>"><?php echo e(strtoupper(pathinfo($doc->name,PATHINFO_EXTENSION) ?: 'FILE')); ?></span><b><?php echo e($doc->name); ?></b></div></td>
                                    <td data-label="Linked to"><b><?php echo e($doc->task?->phase?->name ?? $doc->job?->phase?->name ?? 'Order'); ?></b><span><?php echo e($doc->task?->title ?? $doc->job?->title ?? 'General'); ?></span></td>
                                    <td data-label="Type"><?php echo e($doc->category ?: 'Other'); ?></td><td data-label="Version">v<?php echo e($doc->version); ?></td>
                                    <td data-label="Owner"><div class="person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $doc->uploader,'name' => $doc->uploader?->name ?? 'System']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($doc->uploader),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($doc->uploader?->name ?? 'System')]); ?>
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
<?php endif; ?><span><?php echo e($doc->uploader?->name ?? 'System'); ?></span></div></td>
                                    <td data-label="Status"><span class="ft-doc-status <?php echo e($docStatus==='Approved'?'green':($docStatus==='Needs attention'?'amber':'blue')); ?>"><?php echo e($docStatus); ?></span></td>
                                    <td data-label="Updated"><?php echo e(\App\Support\UserLocalTime::format($doc->updated_at, 'M j')); ?></td>
                                    <td data-label="Actions"><div class="ft-doc-row-actions"><a href="<?php echo e(route('documents.open',$doc)); ?>" target="_blank" rel="noopener" wire:click.stop>Open</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','delete')): ?><button wire:click.stop="deleteDocument(<?php echo e($doc->id); ?>)" wire:confirm="Delete this document?">⋮</button><?php else: ?><span>⋮</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody></table></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="empty">No documents found.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documents->hasPages() || $documents->total()): ?><div class="ft-doc-pagination"><span>Showing <?php echo e($documents->firstItem() ?? 0); ?>–<?php echo e($documents->lastItem() ?? 0); ?> of <?php echo e($documents->total()); ?> documents</span><div><select wire:model.live="perPage"><option value="10">10 per page</option><option value="25">25 per page</option><option value="50">50 per page</option></select><button wire:click="previousPage" <?php if($documents->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button><span>Page <?php echo e($documents->currentPage()); ?> of <?php echo e($documents->lastPage()); ?></span><button wire:click="nextPage" <?php if(!$documents->hasMorePages()): echo 'disabled'; endif; ?>>Next</button></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <aside class="card ft-doc-detail-panel">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
                <div class="ft-doc-detail-title"><span class="ft-file-large"><?php echo e(strtoupper(pathinfo($selected->name,PATHINFO_EXTENSION) ?: 'FILE')); ?></span><div><h3><?php echo e($selected->name); ?></h3><div><span class="ft-doc-status blue"><?php echo e($selected->is_final?'Approved':'Current'); ?></span> <span class="tag">v<?php echo e($selected->version); ?></span> · <?php echo e(number_format($selected->size/1024)); ?> KB</div></div></div>
                <div class="ft-doc-side-list">
                    <div><span>Order</span><a href="<?php echo e($selected->job ? route('jobs.index',['open'=>$selected->flow_job_id]) : '#'); ?>" wire:navigate><?php echo e($selected->job?->displayOrderNumber() ?? '—'); ?></a></div>
                    <div><span>Client</span><b><?php echo e($selected->job?->client?->name ?? $selected->client?->name ?? '—'); ?></b></div>
                    <div><span>Phase</span><b><?php echo e($selected->task?->phase?->name ?? $selected->job?->phase?->name ?? '—'); ?></b></div>
                    <div><span>Task</span><b><?php echo e($selected->task?->title ?? '—'); ?></b></div>
                    <div><span>Type</span><b><?php echo e($selected->category ?: 'Other'); ?></b></div>
                    <div><span>Owner</span><b><?php echo e($selected->uploader?->name ?? 'System'); ?></b></div>
                    <div><span>Uploaded</span><b><?php echo e(\App\Support\UserLocalTime::format($selected->created_at, 'M j, Y g:i A')); ?></b></div>
                </div>
                <div class="ft-doc-side-actions"><a class="primary" href="<?php echo e(route('documents.open',$selected)); ?>" target="_blank" rel="noopener">Open ↗</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('documents','export')): ?><a class="ghost" href="<?php echo e(route('documents.download',$selected)); ?>">⇩ Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <div class="ft-doc-versions"><h4>Versions</h4><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><button wire:click="selectDocument(<?php echo e($version->id); ?>)"><b>v<?php echo e($version->version); ?></b><span><?php echo e($version->uploader?->name ?? 'System'); ?> · <?php echo e(\App\Support\UserLocalTime::format($version->created_at, 'M j, Y g:i A')); ?> · <?php echo e(number_format($version->size/1024)); ?> KB</span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($version->id===$selected->id): ?><em>Current</em><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></button><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="small muted">No previous versions.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
            <?php else: ?><div class="empty">Select a document to view details.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </aside>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUpload): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeUpload"></div>
        <div class="modal livewire-modal ft-doc-upload-modal" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-upload-modal'; ?>wire:key="document-upload-modal">
            <div class="modal-head">
                <div>
                    <h2>Upload document</h2>
                    <div class="small muted">Link the file to a visible Order and optionally to one of your assigned tasks.</div>
                </div>
                <button type="button" class="close-btn" wire:click="closeUpload">×</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Order *</label>
                        <select wire:model.live="uploadJobId">
                            <option value="">Select Order</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($j->id); ?>"><?php echo e($j->displayOrderNumber()); ?> · <?php echo e($j->title); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['uploadJobId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Task</label>
                        <select wire:model="uploadTaskId">
                            <option value="">Order-level document</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $uploadTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($task->id); ?>"><?php echo e($task->phase?->short_name); ?> · <?php echo e($task->title); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>

                    <div class="field full">
                        <label>Document type *</label>
                        <select wire:model="uploadCategory">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option><?php echo e($cat->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['uploadCategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field full">
                        <label
                            class="upload-zone ft-livewire-upload-zone"
                            data-file-dropzone
                            for="document-page-upload-input"
                        >
                            <input
                                id="document-page-upload-input"
                                type="file"
                                wire:model="documentUploads"
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv"
                            >
                            <b>Drop files here or browse</b>
                            <div class="small muted" data-drop-status>PDF, DOCX, XLSX, JPG, PNG, ZIP, TXT or CSV · Max 20 MB each</div>
                        </label>

                        <div class="ft-file-upload-progress" wire:loading wire:target="documentUploads">
                            Preparing selected files…
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($documentUploads)): ?>
                            <div class="ft-upload-ready-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentUploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span><?php echo e($file->getClientOriginalName()); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['documentUploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['documentUploads.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="modal-foot">
                <button type="button" class="ghost" wire:click="closeUpload">Cancel</button>
                <button
                    type="button"
                    class="primary"
                    wire:click="storeDocuments"
                    wire:loading.attr="disabled"
                    wire:target="documentUploads,storeDocuments"
                    <?php if(count($documentUploads) === 0): echo 'disabled'; endif; ?>
                >
                    <span wire:loading.remove wire:target="storeDocuments">Upload document</span>
                    <span wire:loading wire:target="storeDocuments">Uploading…</span>
                </button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/documents/index.blade.php ENDPATH**/ ?>