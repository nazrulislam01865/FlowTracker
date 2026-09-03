<?php
    $formatBytes = static function (int $bytes): string {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);
        $precision = $power >= 3 ? 1 : ($value >= 10 ? 0 : 1);
        return number_format($value, $precision).' '.$units[$power];
    };
    $formatUpdated = static function ($value): string {
        $local = \App\Support\UserLocalTime::localize($value);
        if (!$local) return '—';
        $today = app(\App\Services\WorkspaceSettingsService::class)->localToday();
        if ($local->toDateString() === $today->toDateString()) {
            $minutes = max(0, (int) $local->diffInMinutes(now(), false));
            if ($minutes >= 0 && $minutes < 60) return $minutes <= 1 ? 'Just now' : $minutes.' minutes ago';
            $hours = max(1, (int) floor($minutes / 60));
            if ($hours <= 3) return $hours.' '.\Illuminate\Support\Str::plural('hour', $hours).' ago';
            return 'Today, '.$local->format('g:i A');
        }
        if ($local->toDateString() === $today->copy()->subDay()->toDateString()) return 'Yesterday';
        return $local->format('M j, Y');
    };
    $fileType = static function (string $extension): string {
        return match (strtolower($extension)) {
            'pdf' => 'PDF',
            'doc', 'docx' => 'DOCX',
            'xls', 'xlsx', 'csv' => 'XLSX',
            'jpg', 'jpeg' => 'JPG',
            'png' => 'PNG',
            'zip' => 'ZIP',
            'txt' => 'TXT',
            default => strtoupper($extension ?: 'FILE'),
        };
    };
    $lastPage = max(1, (int) $documents->lastPage());
    $currentPage = max(1, (int) $documents->currentPage());
    $pageNumbers = collect([1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
        ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
        ->unique()->sort()->values();
?>

<div id="document-archive-app" class="ft-da-page">
    <header class="ft-da-page-header">
        <div class="ft-da-title-block">
            <div class="ft-da-eyebrow">Documents</div>
            <h1>Document archive</h1>
            <p>Find every file linked to clients, inquiries, orders and tasks.</p>
            <div class="ft-da-summary-line">
                <strong><?php echo e(number_format($documentCount)); ?> documents</strong>
                <span aria-hidden="true">•</span>
                <strong><?php echo e($formatBytes((int) $storageBytes)); ?> used</strong>
            </div>
        </div>

    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="ft-da-flash"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="ft-da-archive-card" aria-label="Document archive">
        <div class="ft-da-search-area" x-data x-on:keydown.window.meta.k.prevent="$refs.archiveSearch.focus()" x-on:keydown.window.ctrl.k.prevent="$refs.archiveSearch.focus()">
            <div class="ft-da-search-box">
                <svg class="ft-da-search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input
                    x-ref="archiveSearch"
                    type="search"
                    wire:model.live.debounce.350ms="search"
                    placeholder="Search filename, client, order, inquiry, task or document text..."
                    autocomplete="off"
                    aria-label="Search documents"
                >
                <span class="ft-da-shortcut"><kbd>⌘</kbd><kbd>K</kbd></span>
            </div>
            <p>Search includes file names, linked records and extracted text from supported documents.</p>
        </div>

        <?php if (isset($component)) { $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.filter-bar','data' => ['class' => 'ft-da-filter-row','label' => 'Document filters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-da-filter-row','label' => 'Document filters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="ft-da-filter-group">
                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-da-select-wrap ft-da-search-select','label' => 'Client','property' => 'client','type' => 'clients','context' => 'documents','value' => $client,'placeholder' => 'All clients','initialOptions' => $clientOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'documents-client-filter-'.e($client ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-da-select-wrap ft-da-search-select','label' => 'Client','property' => 'client','type' => 'clients','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client),'placeholder' => 'All clients','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'documents-client-filter-'.e($client ?: 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <label class="ft-da-select-wrap">
                    <span class="sr-only">Linked record</span>
                    <select wire:model.live="linkType">
                        <option value="">Order or inquiry</option>
                        <option value="order">Orders</option>
                        <option value="inquiry">Inquiries</option>
                        <option value="task">Tasks</option>
                        <option value="client">Client only</option>
                        <option value="unlinked">Not linked</option>
                    </select>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 10 4 4 4-4"/></svg>
                </label>

                <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['class' => 'ft-da-select-wrap ft-da-search-select','label' => 'Uploaded by','property' => 'uploader','type' => 'users','context' => 'documents','value' => $uploader,'placeholder' => 'Uploaded by','initialOptions' => $uploaderOptions,'hideLabel' => true,'fixedMenu' => true,'menuWidth' => 280,'wire:key' => 'documents-uploader-filter-'.e($uploader ?: 'all').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ft-da-select-wrap ft-da-search-select','label' => 'Uploaded by','property' => 'uploader','type' => 'users','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uploader),'placeholder' => 'Uploaded by','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uploaderOptions),'hide-label' => true,'fixed-menu' => true,'menu-width' => 280,'wire:key' => 'documents-uploader-filter-'.e($uploader ?: 'all').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>

                <label class="ft-da-select-wrap ft-da-date-select">
                    <svg class="ft-da-calendar-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                    <span class="sr-only">Updated date</span>
                    <select wire:model.live="dateRange">
                        <option value="">Any time</option>
                        <option value="today">Today</option>
                        <option value="7_days">Last 7 days</option>
                        <option value="30_days">Last 30 days</option>
                    </select>
                    <svg class="ft-da-select-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 10 4 4 4-4"/></svg>
                </label>

                <button type="button" class="ft-da-clear-filters" wire:click="clearFilters">Clear filters</button>
            </div>

            <label class="ft-da-select-wrap ft-da-sort-select">
                <span class="sr-only">Sort documents</span>
                <select wire:model.live="sort">
                    <option value="updated_desc">Updated: newest</option>
                    <option value="updated_asc">Updated: oldest</option>
                    <option value="name_asc">Name: A–Z</option>
                    <option value="name_desc">Name: Z–A</option>
                    <option value="size_desc">Size: largest</option>
                </select>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 10 4 4 4-4"/></svg>
            </label>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed)): ?>
<?php $attributes = $__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed; ?>
<?php unset($__attributesOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed)): ?>
<?php $component = $__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed; ?>
<?php unset($__componentOriginal14f469cfc51ebc3cb5d7fb0ffa2702ed); ?>
<?php endif; ?>

        <div class="ft-da-result-count"><?php echo e(number_format($documents->total())); ?> documents</div>

        <div class="ft-da-table-scroll ft-results-refreshable" wire:loading.class="is-refreshing" wire:target="search,client,linkType,uploader,dateRange,sort,perPage,gotoPage,previousPage,nextPage">
            <table class="ft-da-table">
                <colgroup>
                    <col class="ft-da-col-name">
                    <col class="ft-da-col-record">
                    <col class="ft-da-col-task">
                    <col class="ft-da-col-client">
                    <col class="ft-da-col-uploader">
                    <col class="ft-da-col-updated">
                    <col class="ft-da-col-size">
                    <col class="ft-da-col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Order / inquiry</th>
                        <th>Task</th>
                        <th>Client</th>
                        <th>Uploaded by</th>
                        <th>Updated</th>
                        <th>Size</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $extension = $row['extension'];
                            $type = $fileType($extension);
                            $fileClass = in_array($extension, ['jpg', 'jpeg', 'png'], true) ? 'image' : ($extension ?: 'file');
                        ?>
                        <tr class="<?php echo e($row['is_unlinked'] ? 'ft-da-unlinked-row' : ''); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-archive-row-'.e($row['source']).'-'.e($row['id']).''; ?>wire:key="document-archive-row-<?php echo e($row['source']); ?>-<?php echo e($row['id']); ?>">
                            <td>
                                <div class="ft-da-file-cell">
                                    <span class="ft-da-file-icon ft-da-file-<?php echo e($fileClass); ?>" aria-hidden="true">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($extension, ['jpg', 'jpeg', 'png'], true)): ?>
                                            <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4z"/><circle cx="9" cy="10" r="2"/><path d="m5 18 5-5 3 3 2-2 4 4"/></svg>
                                        <?php else: ?>
                                            <span><?php echo e(mb_substr($type, 0, 4)); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                    <span class="ft-da-file-copy">
                                        <button type="button" class="ft-da-file-name" wire:click="openDetails('<?php echo e($row['source']); ?>', <?php echo e($row['id']); ?>)"><?php echo e($row['name']); ?></button>
                                        <small><?php echo e($type); ?></small>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_unlinked']): ?>
                                    <span class="ft-da-record-badge ft-da-record-unlinked">Not linked</span>
                                <?php elseif($row['is_client_only']): ?>
                                    <span class="ft-da-client-only">Client only</span>
                                <?php else: ?>
                                    <div class="ft-da-record-cell">
                                        <span class="ft-da-record-badge"><?php echo e($row['record_kind']); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['record_url']): ?>
                                            <a href="<?php echo e($row['record_url']); ?>" wire:navigate><?php echo e($row['record_number']); ?></a>
                                        <?php else: ?>
                                            <span><?php echo e($row['record_number'] ?: '—'); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['task_title']): ?>
                                    <a class="ft-da-task-link" href="<?php echo e($row['task_url']); ?>" wire:navigate>
                                        <?php echo e($row['task_title']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['task_number']): ?> · <?php echo e($row['task_number']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="ft-da-dash">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><span class="ft-da-client-name"><?php echo e($row['client']?->name ?? '—'); ?></span></td>
                            <td>
                                <div class="ft-da-uploader">
                                    <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $row['uploader'],'name' => $row['uploader']?->name ?? 'FlowTrack','size' => 27]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['uploader']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['uploader']?->name ?? 'FlowTrack'),'size' => 27]); ?>
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
                                    <span><?php echo e($row['uploader']?->name ?? 'FlowTrack Super Admin'); ?></span>
                                </div>
                            </td>
                            <td><time><?php echo e($formatUpdated($row['updated_at'])); ?></time></td>
                            <td><span class="ft-da-size"><?php echo e($formatBytes((int) $row['size'])); ?></span></td>
                            <td>
                                <div class="ft-da-actions" x-data="{ open: false }">
                                    <a class="ft-da-icon-button" href="<?php echo e($row['open_url']); ?>" target="_blank" rel="noopener" aria-label="Preview <?php echo e($row['name']); ?>" title="Preview document">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('document_archive', 'export')): ?>
                                        <a class="ft-da-download-button" href="<?php echo e($row['download_url']); ?>">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>
                                            <span>Download</span>
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <button
                                        type="button"
                                        class="ft-da-more-button"
                                        :aria-expanded="open ? 'true' : 'false'"
                                        aria-haspopup="menu"
                                        aria-controls="document-actions-<?php echo e($row['source']); ?>-<?php echo e($row['id']); ?>"
                                        aria-label="More actions for <?php echo e($row['name']); ?>"
                                        x-on:click.stop="
                                            const menu = $refs.menu;
                                            if (menu.matches(':popover-open')) { menu.hidePopover(); return; }
                                            const rect = $el.getBoundingClientRect();
                                            const menuWidth = 220;
                                            const menuHeight = 286;
                                            const edge = 12;
                                            const gap = 6;
                                            const left = Math.min(window.innerWidth - menuWidth - edge, Math.max(edge, rect.right - menuWidth));
                                            const openAbove = (window.innerHeight - rect.bottom) < (menuHeight + gap + edge) && rect.top > (menuHeight + gap + edge);
                                            const top = openAbove ? rect.top - menuHeight - gap : rect.bottom + gap;
                                            menu.style.left = `${left}px`;
                                            menu.style.top = `${Math.max(edge, top)}px`;
                                            menu.showPopover();
                                        "
                                    >⋮</button>
                                    <div
                                        id="document-actions-<?php echo e($row['source']); ?>-<?php echo e($row['id']); ?>"
                                        class="ft-da-action-menu"
                                        x-ref="menu"
                                        popover="auto"
                                        role="menu"
                                        x-on:toggle="open = $event.newState === 'open'"
                                        x-on:click.capture="
                                            const item = $event.target.closest('[role=menuitem]');
                                            if (item && $el.matches(':popover-open')) {
                                                $el.hidePopover();
                                            }
                                        "
                                    >
                                        <button type="button" role="menuitem" wire:click="openDetails('<?php echo e($row['source']); ?>', <?php echo e($row['id']); ?>)">
                                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/></svg>
                                            <span>View document details</span>
                                        </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['task_url']): ?>
                                            <a role="menuitem" href="<?php echo e($row['task_url']); ?>" wire:navigate>
                                                <svg viewBox="0 0 24 24"><path d="M14 4h6v6M20 4l-9 9"/><path d="M19 13v6H5V5h6"/></svg>
                                                <span>Open linked task</span>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="ft-da-menu-divider"></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['can_edit']): ?>
                                            <button type="button" role="menuitem" wire:click="openRename('<?php echo e($row['source']); ?>', <?php echo e($row['id']); ?>)">
                                                <svg viewBox="0 0 24 24"><path d="m4 16-.5 4.5L8 20l10.5-10.5-4-4L4 16Z"/><path d="m13 7 4 4"/></svg>
                                                <span>Rename document</span>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['supports_versions'] && auth()->user()->canModule('document_archive', 'create')): ?>
                                            <button type="button" role="menuitem" wire:click="openVersionUpload(<?php echo e($row['id']); ?>)">
                                                <svg viewBox="0 0 24 24"><path d="M12 16V4m0 0-4 4m4-4 4 4"/><path d="M5 15v5h14v-5"/></svg>
                                                <span>Upload new version</span>
                                            </button>
                                            <button type="button" role="menuitem" wire:click="openVersions(<?php echo e($row['id']); ?>)">
                                                <svg viewBox="0 0 24 24"><path d="M4 12a8 8 0 1 0 2.3-5.7L4 8"/><path d="M4 4v4h4M12 8v5l3 2"/></svg>
                                                <span>Version history</span>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <button type="button" role="menuitem" x-on:click="navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($row['open_url'])->toHtml() ?>); $refs.menu.hidePopover()">
                                            <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"/></svg>
                                            <span>Copy link</span>
                                        </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['can_delete']): ?>
                                            <div class="ft-da-menu-divider"></div>
                                            <button type="button" class="ft-da-delete-menu-item" role="menuitem" wire:click="deleteArchiveDocument('<?php echo e($row['source']); ?>', <?php echo e($row['id']); ?>)" wire:confirm="Delete <?php echo e(addslashes($row['name'])); ?>? This action cannot be undone.">
                                                <svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/></svg>
                                                <span>Delete document</span>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="8"><div class="ft-da-empty">No documents match the current filters.</div></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="ft-da-table-footer">
            <div class="ft-da-footer-left">
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documents->total()): ?>
                        Showing <?php echo e($documents->firstItem()); ?>–<?php echo e($documents->lastItem()); ?> of <?php echo e(number_format($documents->total())); ?> documents
                    <?php else: ?>
                        Showing 0 documents
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <label class="ft-da-select-wrap ft-da-per-page">
                    <span class="sr-only">Documents per page</span>
                    <select wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 10 4 4 4-4"/></svg>
                </label>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastPage > 1): ?>
                <nav class="ft-da-pagination" aria-label="Documents pagination">
                    <button type="button" wire:click="previousPage" <?php if($documents->onFirstPage()): echo 'disabled'; endif; ?>>Previous</button>
                    <?php $previousRenderedPage = null; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pageNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previousRenderedPage !== null && $pageNumber - $previousRenderedPage > 1): ?>
                            <span class="ft-da-page-ellipsis">…</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button type="button" class="<?php echo e($pageNumber === $currentPage ? 'active' : ''); ?>" wire:click="gotoPage(<?php echo e($pageNumber); ?>)" <?php if($pageNumber === $currentPage): ?> aria-current="page" <?php endif; ?>><?php echo e($pageNumber); ?></button>
                        <?php $previousRenderedPage = $pageNumber; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" wire:click="nextPage" <?php if(!$documents->hasMorePages()): echo 'disabled'; endif; ?>>Next</button>
                </nav>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </footer>
    </section>

    <div class="ft-da-info-bar">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/></svg>
        <span>Documents stay connected to the task, inquiry, order or client where they were uploaded in FlowTrack.</span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUpload): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeUpload"></div>
        <div class="modal livewire-modal ft-doc-upload-modal" data-ft-feedback-scope="form" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-upload-modal'; ?>wire:key="document-upload-modal">
            <div class="modal-head">
                <div><h2>Upload document</h2><div class="small muted">Link the file to a visible Order and optionally to one of your assigned tasks.</div></div>
                <button type="button" class="close-btn" wire:click="closeUpload">×</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <?php if (isset($component)) { $__componentOriginal655167214ff7da69eb027810b956fa88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal655167214ff7da69eb027810b956fa88 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-select','data' => ['label' => 'Order *','property' => 'uploadJobId','type' => 'jobs','context' => 'documents','value' => $uploadJobId,'placeholder' => 'Select Order','initialOptions' => $jobs,'clearable' => false,'menuWidth' => 420,'fixedMenu' => true,'wire:key' => 'document-upload-order-'.e($uploadJobId ?: 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Order *','property' => 'uploadJobId','type' => 'jobs','context' => 'documents','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($uploadJobId),'placeholder' => 'Select Order','initial-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jobs),'clearable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'menu-width' => 420,'fixed-menu' => true,'wire:key' => 'document-upload-order-'.e($uploadJobId ?: 'none').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $attributes = $__attributesOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__attributesOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal655167214ff7da69eb027810b956fa88)): ?>
<?php $component = $__componentOriginal655167214ff7da69eb027810b956fa88; ?>
<?php unset($__componentOriginal655167214ff7da69eb027810b956fa88); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['uploadJobId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php if (isset($component)) { $__componentOriginalce11a07acd8b47e338d25689bef957cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce11a07acd8b47e338d25689bef957cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.validation-message','data' => ['message' => $message]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.validation-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $attributes = $__attributesOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__attributesOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce11a07acd8b47e338d25689bef957cf)): ?>
<?php $component = $__componentOriginalce11a07acd8b47e338d25689bef957cf; ?>
<?php unset($__componentOriginalce11a07acd8b47e338d25689bef957cf); ?>
<?php endif; ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="field">
                        <label>Task</label>
                        <select wire:model="uploadTaskId"><option value="">Order-level document</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $uploadTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($task->id); ?>"><?php echo e($task->phase?->short_name); ?> · <?php echo e($task->title); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                    </div>
                    <div class="field full">
                        <label>Document type *</label>
                        <select wire:model="uploadCategory"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option><?php echo e($cat->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
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
                        <label class="upload-zone ft-livewire-upload-zone" data-file-dropzone for="document-page-upload-input">
                            <input id="document-page-upload-input" type="file" wire:model="documentUploads" multiple accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>">
                            <b>Drop files here or browse</b>
                            <div class="small muted" data-drop-status><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></div>
                        </label>
                        <div class="ft-file-upload-progress" wire:loading wire:target="documentUploads">Preparing selected files…</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($documentUploads)): ?><div class="ft-upload-ready-list"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documentUploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><span><?php echo e($file->getClientOriginalName()); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <button type="button" class="primary" wire:click="storeDocuments" wire:loading.attr="disabled" wire:target="documentUploads,storeDocuments" <?php if(count($documentUploads) === 0): echo 'disabled'; endif; ?>>
                    <span wire:loading.remove wire:target="storeDocuments">Upload document</span><span wire:loading wire:target="storeDocuments">Uploading…</span>
                </button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDetails && $selected): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeDetails"></div>
        <div class="modal livewire-modal ft-da-details-modal" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-details-modal'; ?>wire:key="document-details-modal">
            <div class="modal-head"><div><h2>Document details</h2><div class="small muted">File information and linked record.</div></div><button type="button" class="close-btn" wire:click="closeDetails">×</button></div>
            <div class="modal-body">
                <div class="ft-da-detail-file">
                    <span class="ft-da-file-icon ft-da-file-<?php echo e(strtolower(pathinfo($selected['name'], PATHINFO_EXTENSION) ?: 'file')); ?>"><span><?php echo e(mb_substr(strtoupper(pathinfo($selected['name'], PATHINFO_EXTENSION) ?: 'FILE'), 0, 4)); ?></span></span>
                    <div><h3><?php echo e($selected['name']); ?></h3><p><?php echo e($selected['mime_type'] ?: 'File'); ?> · <?php echo e($formatBytes((int) $selected['size'])); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected['version']): ?> · v<?php echo e($selected['version']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p></div>
                </div>
                <dl class="ft-da-detail-grid">
                    <div><dt>Linked record</dt><dd><?php echo e($selected['record_label'] ?: '—'); ?></dd></div>
                    <div><dt>Task</dt><dd><?php echo e($selected['task_label'] ?: '—'); ?></dd></div>
                    <div><dt>Client</dt><dd><?php echo e($selected['client_name'] ?: '—'); ?></dd></div>
                    <div><dt>Uploaded by</dt><dd><?php echo e($selected['uploader']?->name ?? 'FlowTrack'); ?></dd></div>
                    <div><dt>Uploaded</dt><dd><?php echo e(\App\Support\UserLocalTime::format($selected['created_at'], 'M j, Y g:i A')); ?></dd></div>
                    <div><dt>Updated</dt><dd><?php echo e(\App\Support\UserLocalTime::format($selected['updated_at'], 'M j, Y g:i A')); ?></dd></div>
                </dl>
            </div>
            <div class="modal-foot"><button type="button" class="ghost" wire:click="closeDetails">Close</button><a class="primary" href="<?php echo e($selected['open_url']); ?>" target="_blank" rel="noopener">Open document</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('document_archive', 'export')): ?><a class="ghost" href="<?php echo e($selected['download_url']); ?>">Download</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRename): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeRename"></div>
        <div class="modal livewire-modal ft-da-small-modal" data-ft-feedback-scope="form" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-rename-modal'; ?>wire:key="document-rename-modal">
            <div class="modal-head"><div><h2>Rename document</h2><div class="small muted">Change the display name without moving the stored file.</div></div><button type="button" class="close-btn" wire:click="closeRename">×</button></div>
            <div class="modal-body"><div class="field"><label>File name *</label><input type="text" wire:model="renameName" maxlength="255"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['renameName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
            <div class="modal-foot"><button type="button" class="ghost" wire:click="closeRename">Cancel</button><button type="button" class="primary" wire:click="renameDocument">Rename</button></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showVersionUpload): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeVersionUpload"></div>
        <div class="modal livewire-modal ft-da-small-modal" data-ft-feedback-scope="form" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-version-upload-modal'; ?>wire:key="document-version-upload-modal">
            <div class="modal-head"><div><h2>Upload new version</h2><div class="small muted">The new file is added to the same document history.</div></div><button type="button" class="close-btn" wire:click="closeVersionUpload">×</button></div>
            <div class="modal-body">
                <label class="upload-zone ft-livewire-upload-zone" data-file-dropzone for="document-version-upload-input">
                    <input id="document-version-upload-input" type="file" wire:model="versionUpload" accept="<?php echo e(\App\Support\AttachmentUpload::accept()); ?>">
                    <b>Drop a file here or browse</b><div class="small muted"><?php echo e(\App\Support\AttachmentUpload::helperText(20)); ?></div>
                </label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['versionUpload'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="modal-foot"><button type="button" class="ghost" wire:click="closeVersionUpload">Cancel</button><button type="button" class="primary" wire:click="storeNewVersion" wire:loading.attr="disabled" wire:target="versionUpload,storeNewVersion" <?php if(!$versionUpload): echo 'disabled'; endif; ?>>Upload version</button></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showVersions): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeVersions"></div>
        <div class="modal livewire-modal ft-da-versions-modal" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'document-version-history-modal'; ?>wire:key="document-version-history-modal">
            <div class="modal-head"><div><h2>Version history</h2><div class="small muted">Previous versions of this document.</div></div><button type="button" class="close-btn" wire:click="closeVersions">×</button></div>
            <div class="modal-body">
                <div class="ft-da-version-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-da-version-row">
                            <strong><?php echo e($version->name); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! (\App\Support\ArtworkDocumentName::hasVersion((string) $version->name))): ?> · Version <?php echo e(max(1, (int) $version->version)); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></strong>
                            <em class="ft-da-version-state <?php echo e($loop->first ? 'is-latest' : 'is-archived'); ?>"><?php echo e($loop->first ? 'Latest' : 'Archived'); ?></em>
                            <span><?php echo e($version->uploader?->name ?? 'FlowTrack'); ?></span>
                            <time><?php echo e(\App\Support\UserLocalTime::format($version->created_at, 'M j, Y g:i A')); ?></time>
                            <span><?php echo e($formatBytes((int) $version->size)); ?></span>
                            <a href="<?php echo e(route('documents.open', $version)); ?>" target="_blank" rel="noopener">Open</a>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="ft-da-empty">No versions found.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="modal-foot"><button type="button" class="ghost" wire:click="closeVersions">Close</button></div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/documents/index.blade.php ENDPATH**/ ?>