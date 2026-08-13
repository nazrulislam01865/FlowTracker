<div class="ft-access-admin">
    <div class="ft-access-head">
        <div>
            <h1>{{ $tab === 'branding' ? 'System Branding' : ($tab === 'settings' ? 'System Settings' : 'Access Roles & Permissions') }}</h1>
            <p>{{ $tab === 'branding' ? 'Manage the logo and browser favicon used across FlowTrack.' : ($tab === 'settings' ? 'Configure workspace-wide display settings used throughout FlowTrack.' : 'Control who can view, create, edit, assign, delete, link, export or manage every FlowTrack module.') }}</p>
        </div>
        @if(!in_array($tab, ['branding','settings'], true))
            <div class="ft-access-actions"><button class="ghost" wire:click="setTab('audit')">Audit Log</button><button class="primary" wire:click="openRole">＋ New Role</button></div>
        @endif
    </div>

    <div class="ft-access-tabs">
        @foreach(['dashboard'=>'Access Dashboard','roles'=>'Roles & Policies','matrix'=>'Permission Matrix','users'=>'Users & Assignments','audit'=>'Audit Log','security'=>'Security Settings','settings'=>'Settings','branding'=>'Branding'] as $key=>$label)
            <button class="{{ $tab===$key?'active':'' }}" wire:click="setTab('{{ $key }}')">{{ $label }}</button>
        @endforeach
    </div>

    @if($tab==='dashboard')
        <div class="ft-access-metrics">
            <div class="card"><span>Active users</span><b>{{ $summary['users'] }}</b><small>Current workspace members</small></div>
            <div class="card"><span>Active roles</span><b>{{ $summary['roles'] }}</b><small>Reusable permission profiles</small></div>
            <div class="card"><span>Access changes</span><b>{{ $summary['access_changes'] }}</b><small>Last 30 days</small></div>
            <div class="card"><span>Notification rules</span><b>{{ $summary['rules'] }}</b><small>Active system rules</small></div>
        </div>
        <div class="ft-access-grid-2">
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Role coverage</h3><div class="small muted">Module permissions always control actions. Operational records also use record scope; shared workspace reference data does not.</div></div><button class="link-btn" wire:click="setTab('roles')">Manage roles</button></div>
                @foreach($roles as $role)
                    <div class="ft-role-line"><div><b>{{ $role->name }}</b><small>{{ (int) ($role->users_count ?? 0) }} users · {{ str_replace('_',' ',$role->default_scope) }}</small></div><span class="badge {{ $role->is_active?'b-green':'b-gray' }}">{{ $role->is_active?'Active':'Inactive' }}</span></div>
                @endforeach
            </section>
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Enforcement model</h3><div class="small muted">The same rules are applied by routes, queries and update actions.</div></div></div>
                <div class="ft-control-note"><b>1. Module permission</b><span>The role must allow the requested action.</span></div>
                <div class="ft-control-note"><b>2. Record scope</b><span>Assigned users see scoped operational Jobs, Tasks, Inquiries and Documents. Clients and setup/reference data are shared workspace-wide once the relevant action is granted.</span></div>
                <div class="ft-control-note"><b>3. Record ownership</b><span>Edit own allows task assignees or Job owners/coordinators to update only their records. Job workflow/status changes are stricter: only the assigned Job owner (or Admin/Super Admin) can transition a Job.</span></div>
                <div class="ft-control-note"><b>4. Audit trail</b><span>Role, scope and user assignment changes are recorded with actor and time.</span></div>
            </section>
        </div>
    @elseif($tab==='roles')
        <div class="ft-role-grid">
            @foreach($roles as $role)
                <article class="card ft-role-card {{ $selectedRoleId===$role->id?'selected':'' }}">
                    <div class="ft-role-card-top"><div class="ft-role-symbol">{{ collect(explode(' ',$role->name))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('') }}</div><span class="badge {{ $role->is_active?'b-green':'b-gray' }}">{{ $role->is_active?'Active':'Inactive' }}</span></div>
                    <h3>{{ $role->name }}</h3><div class="small muted">{{ $role->code ?: strtoupper($role->slug) }}</div>
                    <p>{{ $role->description ?: 'Reusable FlowTrack role with module and action permissions.' }}</p>
                    <div class="ft-role-meta"><span>{{ (int) ($role->users_count ?? 0) }} users</span><span>{{ str_replace('_',' ',$role->default_scope) }}</span></div>
                    <div class="ft-role-buttons"><button class="ghost" wire:click="openRole({{ $role->id }})" @disabled(in_array($role->slug,['super-admin','admin','administrator'],true))>Details</button><button class="secondary" wire:click="selectRole({{ $role->id }})">Permissions</button></div>
                </article>
            @endforeach
        </div>
    @elseif($tab==='matrix')
        @if($selectedRole)
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
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected((int) $selectedRole->id === (int) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ft-matrix-role-info" wire:key="matrix-role-info-{{ $selectedRole->id }}">
                    <div>
                        <b>{{ $selectedRole->name }}</b>
                        <span>{{ $selectedRole->description ?: 'Configure the module actions and effective record scope.' }}</span>
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
                <div class="ft-role-matrix-wrap card" wire:key="permission-matrix-{{ $selectedRole->id }}" wire:loading.class="is-switching-role" wire:target="selectMatrixRole">
                <table class="ft-role-matrix">
                    <thead><tr><th>Module</th>@foreach($actions as $action)<th>{{ ucwords(str_replace('_',' ',$action)) }}</th>@endforeach<th>Record scope</th></tr></thead>
                    <tbody>
                    @foreach($modules as $code=>$meta)
                        @php
                            $access = $selectedRole->moduleAccess->firstWhere('module_code', $code);
                        @endphp
                        <tr>
                            <td><b>{{ $meta['name'] }}</b><small>{{ $meta['group'] }}</small></td>
                            @foreach($actions as $action)
                                @php
                                    $permissionLocked = in_array($selectedRole->slug, ['super-admin', 'admin', 'administrator'], true);
                                    $permissionSupported = \App\Services\AccessControlService::supportsAction($code, $action);
                                    $permissionEnabled = $permissionSupported && ($permissionLocked || in_array($action, $access?->actions ?? [], true) || in_array('manage', $access?->actions ?? [], true));
                                @endphp
                                <td
                                    data-label="{{ ucwords(str_replace('_',' ',$action)) }}"
                                    class="ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('role-'.$selectedRole->id.'-'.$code.'-'.$action), label: @js(str_replace('_',' ',$action).' permission'), value: @js($permissionEnabled ? '1' : '0'), display: @js($permissionEnabled ? 'Enabled' : 'Disabled') })"
                                    x-on:matrix-permission-synced.window="
                                        if (Number($event.detail.roleId) === {{ (int) $selectedRole->id }} && $event.detail.module === '{{ $code }}') {
                                            const enabled = Array.isArray($event.detail.actions) && $event.detail.actions.includes('{{ $action }}');
                                            value = enabled ? '1' : '0'; savedValue = value; draftValue = value;
                                            display = enabled ? 'Enabled' : 'Disabled'; savedDisplay = display;
                                        }
                                    "
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <input type="checkbox" class="ft-perm-check" :checked="value === '1'" :disabled="status === 'saving'"
                                        x-on:change="
                                            window.dispatchEvent(new CustomEvent('matrix-save-start'));
                                            commit($event.target.checked ? '1' : '0', $event.target.checked ? 'Enabled' : 'Disabled', () => $wire.setMatrixAction({{ $selectedRole->id }}, '{{ $code }}', '{{ $action }}', draftValue === '1')).then(ok => {
                                                if (ok && lastResponse) window.dispatchEvent(new CustomEvent('matrix-permission-synced', { detail: lastResponse }));
                                                window.dispatchEvent(new CustomEvent('matrix-save-finish', { detail: { ok } }));
                                            });
                                        "
                                        @disabled($permissionLocked)>
                                    @unless($permissionLocked)
                                        <span class="ft-matrix-cell-feedback" aria-hidden="true">
                                            <i x-cloak x-show="status === 'saving'" class="ft-matrix-cell-spinner"></i>
                                            <b x-cloak x-show="status === 'error'" class="ft-matrix-cell-error">!</b>
                                        </span>
                                    @endunless
                                </td>
                            @endforeach
                            @php
                                $universalScope = \App\Services\AccessControlService::isUniversalRecordModule($code);
                                $parentRecordScope = \App\Services\AccessControlService::isParentRecordModule($code);
                                $scopeSupported = \App\Services\AccessControlService::supportsScope($code);
                                $scopeLocked = !$scopeSupported || $universalScope || $parentRecordScope || in_array($selectedRole->slug, ['super-admin', 'admin', 'administrator'], true);
                            @endphp
                            @php
                                $effectiveScope = !$scopeSupported ? 'all_records' : ($scopeLocked ? 'all_records' : ($access?->record_scope ?? 'none'));
                            @endphp
                            <td
                                data-label="Record scope"
                                class="ft-inline-edit-shell"
                                x-data="window.FlowTrackInlineEdit({ key: @js('role-'.$selectedRole->id.'-'.$code.'-scope'), label: 'record scope', value: @js($effectiveScope), display: @js(str_replace('_',' ',$effectiveScope)) })"
                                x-on:matrix-permission-synced.window="
                                    if (Number($event.detail.roleId) === {{ (int) $selectedRole->id }} && $event.detail.module === '{{ $code }}' && $event.detail.recordScope) {
                                        value = String($event.detail.recordScope); savedValue = value; draftValue = value; display = value.replaceAll('_', ' '); savedDisplay = display;
                                    }
                                "
                                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            >
                                <select class="ft-scope-select" x-model="draftValue" :disabled="status === 'saving'"
                                    x-on:change="
                                        window.dispatchEvent(new CustomEvent('matrix-save-start'));
                                        commit($event.target.value, selectedLabel($event), () => $wire.setModuleScope({{ $selectedRole->id }}, '{{ $code }}', draftValue)).then(ok => {
                                            if (ok && lastResponse) window.dispatchEvent(new CustomEvent('matrix-permission-synced', { detail: lastResponse }));
                                            window.dispatchEvent(new CustomEvent('matrix-save-finish', { detail: { ok } }));
                                        });
                                    "
                                    @disabled($scopeLocked)>
                                    @if(!$scopeSupported || $universalScope)
                                        <option value="all_records">{{ $parentRecordScope ? 'Parent record access' : ('All records'.($universalScope ? ' (shared)' : '')) }}</option>
                                    @else
                                        @foreach(['none'=>'None','own_records'=>'Own records','assigned_jobs'=>'Assigned / related','department'=>'Department','all_records'=>'All records'] as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                                    @endif
                                </select>
                                @unless($scopeLocked)
                                    <span class="ft-matrix-scope-feedback" aria-hidden="true">
                                        <i x-cloak x-show="status === 'saving'" class="ft-matrix-cell-spinner"></i>
                                        <b x-cloak x-show="status === 'error'" class="ft-matrix-cell-error">!</b>
                                    </span>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            <div class="ft-access-info">Every permission cell is selectable by an administrator. Changes save automatically; the status beside the selected role confirms when the matrix is saved. The saved matrix is the authoritative role capability set; FlowTrack enforces the relevant module/action wherever that operation is available. <b>View</b> controls visibility; enabling another record action automatically enables View. <b>Edit Own</b> means the record owner/coordinator for Orders, owner for Inquiries, and assignee for Tasks. <b>Edit All</b> applies to every record inside the selected scope. <b>Workflow Setup, Task Pack Setup, Products, Product Categories, Suppliers and the remaining Master Data</b> support separate View/Create/Edit/Delete permissions plus Manage for full control. <b>Clients are shared workspace reference data</b>, so users with Client View can see all active clients while Orders, Inquiries, Tasks and Documents keep their own selected scopes. <b>Products</b> controls the shared Product catalogue and the Product search/select/create options used on Create Inquiry and Create Order. <b>Product Categories</b> independently controls category visibility and category creation in those Product controls. <b>Products</b> is also the authority for Product rows on existing Inquiry/Order records; there is no separate Product Lines permission. The user must still have access to and edit rights for the parent Inquiry/Order before changing its Product rows. <b>Finance permissions inherit the parent record scope</b>.</div>
            </div>
        @endif
    @elseif($tab==='users')
        <div class="section-head"><div><h3>Users & role assignments</h3><div class="small muted">Create, edit, assign roles, change passwords or remove users from FlowTrack.</div></div><button class="primary" wire:click="openUser">＋ Add User</button></div>
        <div class="card table-wrap"><table class="data-table ft-user-access-table"><thead><tr><th>User</th><th>Department</th><th>Role</th><th>Effective scope</th><th>Open tasks</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @foreach($users as $u)
            @php
                $adminUserStatus = in_array((string) ($u->account_status ?? ''), ['active','inactive','suspended'], true)
                    ? (string) $u->account_status
                    : ($u->is_active ? 'active' : 'inactive');
                $adminUserStatusClass = $adminUserStatus === 'active' ? 'b-green' : ($adminUserStatus === 'suspended' ? 'b-amber' : 'b-gray');
            @endphp
            <tr
                wire:key="admin-user-{{ $u->id }}"
                x-data="{ ...window.FlowTrackInlineEdit({ key: @js('admin-user-'.$u->id.'-role'), label: 'user role', value: @js((string)($u->role_id ?? '')), display: @js($u->role?->name ?? 'No role') }), roleScopes: @js($roles->mapWithKeys(fn($role) => [(string)$role->id => ($role->default_scope ?: 'none')])->all()) }"
            >
                <td><div class="person"><x-ui.avatar :user="$u" :name="$u->name"/><div><b>{{ $u->name }}</b>@if($u->workspaceMemberships->first()?->job_title)<div class="small muted">{{ $u->workspaceMemberships->first()->job_title }}</div>@endif<div class="small muted">{{ $u->email }}</div></div></div></td>
                <td>{{ $u->department?->name ?? '—' }}</td>
                <td class="ft-inline-edit-shell" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                    <select x-model="draftValue" :disabled="status === 'saving'" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.assignRole({{ $u->id }}, Number(draftValue)))" @disabled($u->isSuperAdmin())>@foreach($roles->where('is_active',true) as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
                    @unless($u->isSuperAdmin())<x-ui.inline-save-state compact />@endunless
                </td>
                <td><span class="tag" x-text="String(roleScopes[value] || 'none').replaceAll('_',' ')">{{ str_replace('_',' ',$u->role?->default_scope ?? 'none') }}</span></td>
                <td>{{ $u->open_tasks_count }}</td>
                <td><button class="mini-btn" wire:click="toggleUserActive({{ $u->id }})" @disabled($u->isSuperAdmin())><span class="badge {{ $adminUserStatusClass }}">{{ ucfirst($adminUserStatus) }}</span></button></td>
                <td data-label="Actions"><div class="ft-user-row-actions"><a class="ghost ft-user-edit-link" href="{{ route('users.edit', ['user' => $u->id, 'from' => 'administration']) }}" wire:navigate>Edit</a><button type="button" class="ft-user-delete-btn" wire:click="deleteUser({{ $u->id }})" wire:confirm="Delete {{ addslashes($u->name) }}? Existing Job/Task history will be preserved, but this user account will be removed." @disabled($u->isSuperAdmin() || $u->id === auth()->id())>Delete</button></div></td>
            </tr>
        @endforeach
        </tbody></table></div>
    @elseif($tab==='audit')
        <section class="card ft-access-panel"><div class="section-head"><div><h3>Access audit log</h3><div class="small muted">Role, permission, scope, security and assignment changes.</div></div></div>
            <div class="ft-access-audit">@forelse($auditLog as $event)<div class="ft-audit-row"><div class="ft-audit-dot">{{ strtoupper(substr($event->user?->name ?? 'S',0,1)) }}</div><div><b>{{ $event->description }}</b><span>{{ $event->user?->name ?? 'System' }} · {{ \App\Support\UserLocalTime::format($event->created_at, 'M j, Y g:i A') }}</span></div><code>{{ $event->event }}</code></div>@empty<div class="empty">No access changes recorded yet.</div>@endforelse</div>
        </section>
    @elseif($tab==='security')
        <div class="ft-access-grid-2">
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Security controls</h3><div class="small muted">Workspace access-control policy flags stored in Master Data.</div></div></div>
                @foreach($securitySettings as $setting)<div class="ft-security-row"><div><b>{{ $setting['label'] }}</b><span>Administrative access policy</span></div><label class="ft-switch"><input type="checkbox" wire:click="toggleSecurity('{{ $setting['code'] }}')" @checked($setting['enabled'])><i></i></label></div>@endforeach
            </section>
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Access policy</h3></div></div><div class="ft-control-note"><b>Administrator/Super Admin</b><span>Unrestricted application access. These roles can configure all permissions.</span></div><div class="ft-control-note"><b>All other roles</b><span>Must pass both the action permission and record-scope check on every page and update.</span></div><div class="ft-control-note"><b>Assignments</b><span>Task assignees see their assigned tasks; associated Job visibility follows from those assignments.</span></div></section>
        </div>
    @elseif($tab==='settings')
        <section class="card ft-access-panel ft-workspace-settings-card">
            <div class="section-head">
                <div><h3>Local time</h3><div class="small muted">FlowTrack automatically uses each signed-in user's current device/browser time zone.</div></div>
            </div>
            <div class="ft-workspace-setting-row ft-auto-timezone-row">
                <div><b>Automatic local time</b><span>No manual selection is required. If the user's device time zone changes, FlowTrack updates it for that session automatically. Database timestamps remain unchanged.</span></div>
                <div class="ft-access-info"><b>{{ app(\App\Services\WorkspaceSettingsService::class)->displayTimezone() }}</b><span>{{ app(\App\Services\WorkspaceSettingsService::class)->localNow()->format('M j, Y · g:i A') }}</span></div>
            </div>
        </section>
    @endif

    @if($tab === 'branding')
        @include('livewire.administration.partials.branding')
    @endif

    @if($showUserModal)
        <div class="overlay livewire-overlay" wire:click.self="closeUser"></div>
        <div class="modal livewire-modal ft-user-modal">
            <div class="modal-head">
                <h2>{{ $editingUserId ? 'Edit User' : 'Add User' }}</h2>
                <button class="close-btn" wire:click="closeUser">×</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Full name *</label>
                        <input wire:model="name">
                        @error('name')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Position / job title</label>
                        <input wire:model="position" placeholder="e.g. Production Manager" maxlength="120">
                        @error('position')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Email *</label>
                        <input wire:model="email" type="email">
                        @error('email')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Role *</label>
                        <select wire:model="roleId" @disabled($editingUserId && optional($users->firstWhere('id', $editingUserId))->isSuperAdmin())>
                            <option value="">Select role</option>
                            @foreach($roles->where('is_active', true) as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('roleId')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Department</label>
                        <select wire:model="departmentId">
                            <option value="">No department</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        @error('departmentId')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Password {{ $editingUserId ? '' : '*' }}</label>
                        <input wire:model="password" type="password" autocomplete="new-password" placeholder="{{ $editingUserId ? 'Leave blank to keep current password' : 'Enter password' }}">
                        @error('password')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Confirm password {{ $editingUserId ? '' : '*' }}</label>
                        <input wire:model="passwordConfirmation" type="password" autocomplete="new-password" placeholder="Confirm password">
                        @error('passwordConfirmation')
                            <div class="validation-error">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($editingUserId)
                        <div class="field full">
                            <label>Status</label>
                            <select wire:model="userActive" @disabled(optional($users->firstWhere('id', $editingUserId))->isSuperAdmin())>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    @endif
                </div>

                @if($editingUserId)
                    <div class="ft-access-info">Enter a new password only when you want to change this user's password. Leaving both password fields blank keeps the current password.</div>
                @endif
            </div>

            <div class="modal-foot">
                <button class="ghost" wire:click="closeUser">Cancel</button>
                <button class="primary" wire:click="saveUser">{{ $editingUserId ? 'Save Changes' : 'Create User' }}</button>
            </div>
        </div>
    @endif

    @if($showRoleModal)
        <div class="overlay livewire-overlay" wire:click.self="closeRole"></div>
        <div class="modal livewire-modal">
            <div class="modal-head">
                <h2>{{ $editingRoleId ? 'Edit Role' : 'Create Role' }}</h2>
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
    @endif
</div>
