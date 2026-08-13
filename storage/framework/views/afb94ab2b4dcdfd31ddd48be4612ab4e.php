<div class="ft-access-admin">
    <div class="ft-access-head">
        <div>
            <h1><?php echo e($tab === 'branding' ? 'System Branding' : ($tab === 'settings' ? 'System Settings' : 'Access Roles & Permissions')); ?></h1>
            <p><?php echo e($tab === 'branding' ? 'Manage the logo and browser favicon used across FlowTrack.' : ($tab === 'settings' ? 'Configure workspace-wide display settings used throughout FlowTrack.' : 'Control who can view, create, edit, assign, delete, link, export or manage every FlowTrack module.')); ?></p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($tab, ['branding','settings'], true)): ?>
            <div class="ft-access-actions"><button class="ghost" wire:click="setTab('audit')">Audit Log</button><button class="primary" wire:click="openRole">＋ New Role</button></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="ft-access-tabs">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['dashboard'=>'Access Dashboard','roles'=>'Roles & Policies','matrix'=>'Permission Matrix','users'=>'Users & Assignments','audit'=>'Audit Log','security'=>'Security Settings','settings'=>'Settings','branding'=>'Branding']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button class="<?php echo e($tab===$key?'active':''); ?>" wire:click="setTab('<?php echo e($key); ?>')"><?php echo e($label); ?></button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab==='dashboard'): ?>
        <div class="ft-access-metrics">
            <div class="card"><span>Active users</span><b><?php echo e($summary['users']); ?></b><small>Current workspace members</small></div>
            <div class="card"><span>Active roles</span><b><?php echo e($summary['roles']); ?></b><small>Reusable permission profiles</small></div>
            <div class="card"><span>Access changes</span><b><?php echo e($summary['access_changes']); ?></b><small>Last 30 days</small></div>
            <div class="card"><span>Notification rules</span><b><?php echo e($summary['rules']); ?></b><small>Active system rules</small></div>
        </div>
        <div class="ft-access-grid-2">
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Role coverage</h3><div class="small muted">Module permissions always control actions. Operational records also use record scope; shared workspace reference data does not.</div></div><button class="link-btn" wire:click="setTab('roles')">Manage roles</button></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-role-line"><div><b><?php echo e($role->name); ?></b><small><?php echo e((int) ($role->users_count ?? 0)); ?> users · <?php echo e(str_replace('_',' ',$role->default_scope)); ?></small></div><span class="badge <?php echo e($role->is_active?'b-green':'b-gray'); ?>"><?php echo e($role->is_active?'Active':'Inactive'); ?></span></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </section>
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Enforcement model</h3><div class="small muted">The same rules are applied by routes, queries and update actions.</div></div></div>
                <div class="ft-control-note"><b>1. Module permission</b><span>The role must allow the requested action.</span></div>
                <div class="ft-control-note"><b>2. Record scope</b><span>Assigned users see scoped operational Jobs, Tasks, Inquiries and Documents. Clients and setup/reference data are shared workspace-wide once the relevant action is granted.</span></div>
                <div class="ft-control-note"><b>3. Record ownership</b><span>Edit own allows task assignees or Job owners/coordinators to update only their records. Job workflow/status changes are stricter: only the assigned Job owner (or Admin/Super Admin) can transition a Job.</span></div>
                <div class="ft-control-note"><b>4. Audit trail</b><span>Role, scope and user assignment changes are recorded with actor and time.</span></div>
            </section>
        </div>
    <?php elseif($tab==='roles'): ?>
        <div class="ft-role-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <article class="card ft-role-card <?php echo e($selectedRoleId===$role->id?'selected':''); ?>">
                    <div class="ft-role-card-top"><div class="ft-role-symbol"><?php echo e(collect(explode(' ',$role->name))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('')); ?></div><span class="badge <?php echo e($role->is_active?'b-green':'b-gray'); ?>"><?php echo e($role->is_active?'Active':'Inactive'); ?></span></div>
                    <h3><?php echo e($role->name); ?></h3><div class="small muted"><?php echo e($role->code ?: strtoupper($role->slug)); ?></div>
                    <p><?php echo e($role->description ?: 'Reusable FlowTrack role with module and action permissions.'); ?></p>
                    <div class="ft-role-meta"><span><?php echo e((int) ($role->users_count ?? 0)); ?> users</span><span><?php echo e(str_replace('_',' ',$role->default_scope)); ?></span></div>
                    <div class="ft-role-buttons"><button class="ghost" wire:click="openRole(<?php echo e($role->id); ?>)" <?php if(in_array($role->slug,['super-admin','admin','administrator'],true)): echo 'disabled'; endif; ?>>Details</button><button class="secondary" wire:click="selectRole(<?php echo e($role->id); ?>)">Permissions</button></div>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php elseif($tab==='matrix'): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedRole): ?>
            <div
                class="ft-matrix-editor"
                x-data="{ pendingSaves: 0, saveState: '', saveTimer: null }"
                x-on:matrix-save-start.window="
                    pendingSaves++;
                    saveState = 'saving';
                    if (saveTimer) { clearTimeout(saveTimer); saveTimer = null; }
                "
                x-on:matrix-save-finish.window="
                    pendingSaves = Math.max(0, pendingSaves - 1);
                    if (!$event.detail?.ok) {
                        saveState = 'error';
                        if (saveTimer) clearTimeout(saveTimer);
                        saveTimer = setTimeout(() => { if (saveState === 'error') saveState = ''; }, 4200);
                    } else if (pendingSaves === 0) {
                        saveState = 'saved';
                        if (saveTimer) clearTimeout(saveTimer);
                        saveTimer = setTimeout(() => { if (saveState === 'saved') saveState = ''; }, 1400);
                    }
                "
            >
            <div class="ft-matrix-toolbar card">
                <div>
                    <label>Role</label>
                    <select
                        wire:change="selectMatrixRole($event.target.value)"
                        wire:loading.attr="disabled"
                        wire:target="selectMatrixRole"
                        x-bind:disabled="pendingSaves > 0"
                        aria-label="Select role permissions"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($role->id); ?>" <?php if((int) $selectedRole->id === (int) $role->id): echo 'selected'; endif; ?>><?php echo e($role->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="ft-matrix-role-info" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'matrix-role-info-'.e($selectedRole->id).''; ?>wire:key="matrix-role-info-<?php echo e($selectedRole->id); ?>">
                    <div>
                        <b><?php echo e($selectedRole->name); ?></b>
                        <span><?php echo e($selectedRole->description ?: 'Configure the module actions and effective record scope.'); ?></span>
                    </div>
                    <div class="ft-matrix-save-summary" aria-live="polite" aria-atomic="true">
                        <span x-cloak x-show="saveState === 'saving'" class="is-saving">
                            <i class="ft-matrix-save-spinner" aria-hidden="true"></i>
                            <span x-text="pendingSaves > 1 ? `Saving ${pendingSaves} changes…` : 'Saving change…'"></span>
                        </span>
                        <span x-cloak x-show="saveState === 'saved'" class="is-saved"><b aria-hidden="true">✓</b> All changes saved</span>
                        <span x-cloak x-show="saveState === 'error'" class="is-error"><b aria-hidden="true">!</b> Change not saved — use Retry</span>
                    </div>
                </div>
            </div>
            <div class="ft-role-matrix-stage">
                <div class="ft-matrix-loading" wire:loading.flex wire:target="selectMatrixRole">
                    <span class="ft-matrix-spinner" aria-hidden="true"></span>
                    <span>Loading role permissions…</span>
                </div>
                <div class="ft-role-matrix-wrap card" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'permission-matrix-'.e($selectedRole->id).''; ?>wire:key="permission-matrix-<?php echo e($selectedRole->id); ?>" wire:loading.class="is-switching-role" wire:target="selectMatrixRole">
                <table class="ft-role-matrix">
                    <thead><tr><th>Module</th><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><th><?php echo e(ucwords(str_replace('_',' ',$action))); ?></th><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><th>Record scope</th></tr></thead>
                    <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code=>$meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $access = $selectedRole->moduleAccess->firstWhere('module_code', $code);
                        ?>
                        <tr>
                            <td><b><?php echo e($meta['name']); ?></b><small><?php echo e($meta['group']); ?></small></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $permissionLocked = in_array($selectedRole->slug, ['super-admin', 'admin', 'administrator'], true);
                                    $permissionSupported = \App\Services\AccessControlService::supportsAction($code, $action);
                                    $permissionEnabled = $permissionSupported && ($permissionLocked || in_array($action, $access?->actions ?? [], true) || in_array('manage', $access?->actions ?? [], true));
                                ?>
                                <td
                                    data-label="<?php echo e(ucwords(str_replace('_',' ',$action))); ?>"
                                    class="ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('role-'.$selectedRole->id.'-'.$code.'-'.$action)->toHtml() ?>, label: <?php echo \Illuminate\Support\Js::from(str_replace('_',' ',$action).' permission')->toHtml() ?>, value: <?php echo \Illuminate\Support\Js::from($permissionEnabled ? '1' : '0')->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($permissionEnabled ? 'Enabled' : 'Disabled')->toHtml() ?> })"
                                    x-on:matrix-permission-synced.window="
                                        if (Number($event.detail.roleId) === <?php echo e((int) $selectedRole->id); ?> && $event.detail.module === '<?php echo e($code); ?>') {
                                            const enabled = Array.isArray($event.detail.actions) && $event.detail.actions.includes('<?php echo e($action); ?>');
                                            value = enabled ? '1' : '0'; savedValue = value; draftValue = value;
                                            display = enabled ? 'Enabled' : 'Disabled'; savedDisplay = display;
                                        }
                                    "
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <input type="checkbox" class="ft-perm-check" :checked="value === '1'" :disabled="status === 'saving'"
                                        x-on:change="
                                            window.dispatchEvent(new CustomEvent('matrix-save-start'));
                                            commit($event.target.checked ? '1' : '0', $event.target.checked ? 'Enabled' : 'Disabled', () => $wire.setMatrixAction(<?php echo e($selectedRole->id); ?>, '<?php echo e($code); ?>', '<?php echo e($action); ?>', draftValue === '1')).then(ok => {
                                                if (ok && lastResponse) window.dispatchEvent(new CustomEvent('matrix-permission-synced', { detail: lastResponse }));
                                                window.dispatchEvent(new CustomEvent('matrix-save-finish', { detail: { ok } }));
                                            });
                                        "
                                        <?php if($permissionLocked): echo 'disabled'; endif; ?>>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($permissionLocked)): ?>
                                        <span class="ft-matrix-cell-feedback" aria-hidden="true">
                                            <i x-cloak x-show="status === 'saving'" class="ft-matrix-cell-spinner"></i>
                                            <b x-cloak x-show="status === 'error'" class="ft-matrix-cell-error">!</b>
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php
                                $universalScope = \App\Services\AccessControlService::isUniversalRecordModule($code);
                                $parentRecordScope = \App\Services\AccessControlService::isParentRecordModule($code);
                                $scopeSupported = \App\Services\AccessControlService::supportsScope($code);
                                $scopeLocked = !$scopeSupported || $universalScope || $parentRecordScope || in_array($selectedRole->slug, ['super-admin', 'admin', 'administrator'], true);
                            ?>
                            <?php
                                $effectiveScope = !$scopeSupported ? 'all_records' : ($scopeLocked ? 'all_records' : ($access?->record_scope ?? 'none'));
                            ?>
                            <td
                                data-label="Record scope"
                                class="ft-inline-edit-shell"
                                x-data="window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('role-'.$selectedRole->id.'-'.$code.'-scope')->toHtml() ?>, label: 'record scope', value: <?php echo \Illuminate\Support\Js::from($effectiveScope)->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from(str_replace('_',' ',$effectiveScope))->toHtml() ?> })"
                                x-on:matrix-permission-synced.window="
                                    if (Number($event.detail.roleId) === <?php echo e((int) $selectedRole->id); ?> && $event.detail.module === '<?php echo e($code); ?>' && $event.detail.recordScope) {
                                        value = String($event.detail.recordScope); savedValue = value; draftValue = value; display = value.replaceAll('_', ' '); savedDisplay = display;
                                    }
                                "
                                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            >
                                <select class="ft-scope-select" x-model="draftValue" :disabled="status === 'saving'"
                                    x-on:change="
                                        window.dispatchEvent(new CustomEvent('matrix-save-start'));
                                        commit($event.target.value, selectedLabel($event), () => $wire.setModuleScope(<?php echo e($selectedRole->id); ?>, '<?php echo e($code); ?>', draftValue)).then(ok => {
                                            if (ok && lastResponse) window.dispatchEvent(new CustomEvent('matrix-permission-synced', { detail: lastResponse }));
                                            window.dispatchEvent(new CustomEvent('matrix-save-finish', { detail: { ok } }));
                                        });
                                    "
                                    <?php if($scopeLocked): echo 'disabled'; endif; ?>>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$scopeSupported || $universalScope): ?>
                                        <option value="all_records"><?php echo e($parentRecordScope ? 'Parent record access' : ('All records'.($universalScope ? ' (shared)' : ''))); ?></option>
                                    <?php else: ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['none'=>'None','own_records'=>'Own records','assigned_jobs'=>'Assigned / related','department'=>'Department','all_records'=>'All records']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($value); ?>"><?php echo e($label); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($scopeLocked)): ?>
                                    <span class="ft-matrix-scope-feedback" aria-hidden="true">
                                        <i x-cloak x-show="status === 'saving'" class="ft-matrix-cell-spinner"></i>
                                        <b x-cloak x-show="status === 'error'" class="ft-matrix-cell-error">!</b>
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            <div class="ft-access-info">Every permission cell is selectable by an administrator. Changes save automatically; the status beside the selected role confirms when the matrix is saved. The saved matrix is the authoritative role capability set; FlowTrack enforces the relevant module/action wherever that operation is available. <b>View</b> controls visibility; enabling another record action automatically enables View. <b>Edit Own</b> means the record owner/coordinator for Orders, owner for Inquiries, and assignee for Tasks. <b>Edit All</b> applies to every record inside the selected scope. <b>Workflow Setup, Task Pack Setup, Products, Product Categories, Suppliers and the remaining Master Data</b> support separate View/Create/Edit/Delete permissions plus Manage for full control. <b>Clients are shared workspace reference data</b>, so users with Client View can see all active clients while Orders, Inquiries, Tasks and Documents keep their own selected scopes. <b>Products</b> controls the shared Product catalogue and the Product search/select/create options used on Create Inquiry and Create Order. <b>Product Categories</b> independently controls category visibility and category creation in those Product controls. <b>Products</b> is also the authority for Product rows on existing Inquiry/Order records; there is no separate Product Lines permission. The user must still have access to and edit rights for the parent Inquiry/Order before changing its Product rows. <b>Finance permissions inherit the parent record scope</b>.</div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php elseif($tab==='users'): ?>
        <div class="section-head"><div><h3>Users & role assignments</h3><div class="small muted">Create, edit, assign roles, change passwords or remove users from FlowTrack.</div></div><button class="primary" wire:click="openUser">＋ Add User</button></div>
        <div class="card table-wrap"><table class="data-table ft-user-access-table"><thead><tr><th>User</th><th>Department</th><th>Role</th><th>Effective scope</th><th>Open tasks</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $adminUserStatus = in_array((string) ($u->account_status ?? ''), ['active','inactive','suspended'], true)
                    ? (string) $u->account_status
                    : ($u->is_active ? 'active' : 'inactive');
                $adminUserStatusClass = $adminUserStatus === 'active' ? 'b-green' : ($adminUserStatus === 'suspended' ? 'b-amber' : 'b-gray');
            ?>
            <tr
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'admin-user-'.e($u->id).''; ?>wire:key="admin-user-<?php echo e($u->id); ?>"
                x-data="{ ...window.FlowTrackInlineEdit({ key: <?php echo \Illuminate\Support\Js::from('admin-user-'.$u->id.'-role')->toHtml() ?>, label: 'user role', value: <?php echo \Illuminate\Support\Js::from((string)($u->role_id ?? ''))->toHtml() ?>, display: <?php echo \Illuminate\Support\Js::from($u->role?->name ?? 'No role')->toHtml() ?> }), roleScopes: <?php echo \Illuminate\Support\Js::from($roles->mapWithKeys(fn($role) => [(string)$role->id => ($role->default_scope ?: 'none')])->all())->toHtml() ?> }"
            >
                <td><div class="person"><?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $u,'name' => $u->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($u),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($u->name)]); ?>
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
<?php endif; ?><div><b><?php echo e($u->name); ?></b><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->workspaceMemberships->first()?->job_title): ?><div class="small muted"><?php echo e($u->workspaceMemberships->first()->job_title); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><div class="small muted"><?php echo e($u->email); ?></div></div></div></td>
                <td><?php echo e($u->department?->name ?? '—'); ?></td>
                <td class="ft-inline-edit-shell" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                    <select x-model="draftValue" :disabled="status === 'saving'" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.assignRole(<?php echo e($u->id); ?>, Number(draftValue)))" <?php if($u->isSuperAdmin()): echo 'disabled'; endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles->where('is_active',true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($u->isSuperAdmin())): ?><?php if (isset($component)) { $__componentOriginal610752b6d86af46dc7d5e0c5ff95106c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal610752b6d86af46dc7d5e0c5ff95106c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.inline-save-state','data' => ['compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.inline-save-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['compact' => true]); ?>
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
<?php endif; ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td><span class="tag" x-text="String(roleScopes[value] || 'none').replaceAll('_',' ')"><?php echo e(str_replace('_',' ',$u->role?->default_scope ?? 'none')); ?></span></td>
                <td><?php echo e($u->open_tasks_count); ?></td>
                <td><button class="mini-btn" wire:click="toggleUserActive(<?php echo e($u->id); ?>)" <?php if($u->isSuperAdmin()): echo 'disabled'; endif; ?>><span class="badge <?php echo e($adminUserStatusClass); ?>"><?php echo e(ucfirst($adminUserStatus)); ?></span></button></td>
                <td data-label="Actions"><div class="ft-user-row-actions"><a class="ghost ft-user-edit-link" href="<?php echo e(route('users.edit', ['user' => $u->id, 'from' => 'administration'])); ?>" wire:navigate>Edit</a><button type="button" class="ft-user-delete-btn" wire:click="deleteUser(<?php echo e($u->id); ?>)" wire:confirm="Delete <?php echo e(addslashes($u->name)); ?>? Existing Job/Task history will be preserved, but this user account will be removed." <?php if($u->isSuperAdmin() || $u->id === auth()->id()): echo 'disabled'; endif; ?>>Delete</button></div></td>
            </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody></table></div>
    <?php elseif($tab==='audit'): ?>
        <section class="card ft-access-panel"><div class="section-head"><div><h3>Access audit log</h3><div class="small muted">Role, permission, scope, security and assignment changes.</div></div></div>
            <div class="ft-access-audit"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $auditLog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ft-audit-row"><div class="ft-audit-dot"><?php echo e(strtoupper(substr($event->user?->name ?? 'S',0,1))); ?></div><div><b><?php echo e($event->description); ?></b><span><?php echo e($event->user?->name ?? 'System'); ?> · <?php echo e(\App\Support\UserLocalTime::format($event->created_at, 'M j, Y g:i A')); ?></span></div><code><?php echo e($event->event); ?></code></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><div class="empty">No access changes recorded yet.</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        </section>
    <?php elseif($tab==='security'): ?>
        <div class="ft-access-grid-2">
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Security controls</h3><div class="small muted">Workspace access-control policy flags stored in Master Data.</div></div></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $securitySettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div class="ft-security-row"><div><b><?php echo e($setting['label']); ?></b><span>Administrative access policy</span></div><label class="ft-switch"><input type="checkbox" wire:click="toggleSecurity('<?php echo e($setting['code']); ?>')" <?php if($setting['enabled']): echo 'checked'; endif; ?>><i></i></label></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </section>
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Access policy</h3></div></div><div class="ft-control-note"><b>Administrator/Super Admin</b><span>Unrestricted application access. These roles can configure all permissions.</span></div><div class="ft-control-note"><b>All other roles</b><span>Must pass both the action permission and record-scope check on every page and update.</span></div><div class="ft-control-note"><b>Assignments</b><span>Task assignees see their assigned tasks; associated Job visibility follows from those assignments.</span></div></section>
        </div>
    <?php elseif($tab==='settings'): ?>
        <section class="card ft-access-panel ft-workspace-settings-card">
            <div class="section-head">
                <div><h3>Local time</h3><div class="small muted">FlowTrack automatically uses each signed-in user's current device/browser time zone.</div></div>
            </div>
            <div class="ft-workspace-setting-row ft-auto-timezone-row">
                <div><b>Automatic local time</b><span>No manual selection is required. If the user's device time zone changes, FlowTrack updates it for that session automatically. Database timestamps remain unchanged.</span></div>
                <div class="ft-access-info"><b><?php echo e(app(\App\Services\WorkspaceSettingsService::class)->displayTimezone()); ?></b><span><?php echo e(app(\App\Services\WorkspaceSettingsService::class)->localNow()->format('M j, Y · g:i A')); ?></span></div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'branding'): ?>
        <?php echo $__env->make('livewire.administration.partials.branding', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUserModal): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeUser"></div>
        <div class="modal livewire-modal ft-user-modal">
            <div class="modal-head">
                <h2><?php echo e($editingUserId ? 'Edit User' : 'Add User'); ?></h2>
                <button class="close-btn" wire:click="closeUser">×</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Full name *</label>
                        <input wire:model="name">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Position / job title</label>
                        <input wire:model="position" placeholder="e.g. Production Manager" maxlength="120">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['position'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Email *</label>
                        <input wire:model="email" type="email">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Role *</label>
                        <select wire:model="roleId" <?php if($editingUserId && optional($users->firstWhere('id', $editingUserId))->isSuperAdmin()): echo 'disabled'; endif; ?>>
                            <option value="">Select role</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['roleId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Department</label>
                        <select wire:model="departmentId">
                            <option value="">No department</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['departmentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Password <?php echo e($editingUserId ? '' : '*'); ?></label>
                        <input wire:model="password" type="password" autocomplete="new-password" placeholder="<?php echo e($editingUserId ? 'Leave blank to keep current password' : 'Enter password'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label>Confirm password <?php echo e($editingUserId ? '' : '*'); ?></label>
                        <input wire:model="passwordConfirmation" type="password" autocomplete="new-password" placeholder="Confirm password">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['passwordConfirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="validation-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingUserId): ?>
                        <div class="field full">
                            <label>Status</label>
                            <select wire:model="userActive" <?php if(optional($users->firstWhere('id', $editingUserId))->isSuperAdmin()): echo 'disabled'; endif; ?>>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingUserId): ?>
                    <div class="ft-access-info">Enter a new password only when you want to change this user's password. Leaving both password fields blank keeps the current password.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="modal-foot">
                <button class="ghost" wire:click="closeUser">Cancel</button>
                <button class="primary" wire:click="saveUser"><?php echo e($editingUserId ? 'Save Changes' : 'Create User'); ?></button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRoleModal): ?>
        <div class="overlay livewire-overlay" wire:click.self="closeRole"></div>
        <div class="modal livewire-modal">
            <div class="modal-head">
                <h2><?php echo e($editingRoleId ? 'Edit Role' : 'Create Role'); ?></h2>
                <button class="close-btn" wire:click="closeRole">×</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Role name *</label>
                        <input wire:model="roleName">
                    </div>
                    <div class="field">
                        <label>Role code</label>
                        <input wire:model="roleCode" placeholder="JOB_MANAGER">
                    </div>
                    <div class="field full">
                        <label>Description</label>
                        <textarea wire:model="roleDescription" rows="3"></textarea>
                    </div>
                    <div class="field">
                        <label>Default record scope</label>
                        <select wire:model="roleDefaultScope">
                            <option value="none">None</option>
                            <option value="own_records">Own records</option>
                            <option value="assigned_jobs">Assigned Jobs</option>
                            <option value="department">Department</option>
                            <option value="all_records">All records</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select wire:model="roleActive">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-foot">
                <button class="ghost" wire:click="closeRole">Cancel</button>
                <button class="primary" wire:click="saveRole">Save Role</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/administration/index.blade.php ENDPATH**/ ?>