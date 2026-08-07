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
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Role coverage</h3><div class="small muted">Every non-admin user is restricted by module permissions and record scope.</div></div><button class="link-btn" wire:click="setTab('roles')">Manage roles</button></div>
                @foreach($roles as $role)
                    <div class="ft-role-line"><div><b>{{ $role->name }}</b><small>{{ $role->users->count() }} users · {{ str_replace('_',' ',$role->default_scope) }}</small></div><span class="badge {{ $role->is_active?'b-green':'b-gray' }}">{{ $role->is_active?'Active':'Inactive' }}</span></div>
                @endforeach
            </section>
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Enforcement model</h3><div class="small muted">The same rules are applied by routes, queries and update actions.</div></div></div>
                <div class="ft-control-note"><b>1. Module permission</b><span>The role must allow the requested action.</span></div>
                <div class="ft-control-note"><b>2. Record scope</b><span>Assigned users see their own assigned Jobs and Tasks unless the role grants all records.</span></div>
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
                    <div class="ft-role-meta"><span>{{ $role->users->count() }} users</span><span>{{ str_replace('_',' ',$role->default_scope) }}</span></div>
                    <div class="ft-role-buttons"><button class="ghost" wire:click="openRole({{ $role->id }})" @disabled(in_array($role->slug,['super-admin','admin','administrator'],true))>Details</button><button class="secondary" wire:click="selectRole({{ $role->id }})">Permissions</button></div>
                </article>
            @endforeach
        </div>
    @elseif($tab==='matrix')
        @if($selectedRole)
            <div class="ft-matrix-toolbar card">
                <div><label>Role</label><select wire:model.live="selectedRoleId">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></div>
                <div class="ft-matrix-role-info"><b>{{ $selectedRole->name }}</b><span>{{ $selectedRole->description ?: 'Configure the module actions and effective record scope.' }}</span></div>
            </div>
            <div class="ft-role-matrix-wrap card">
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
                                @endphp
                                @php
                                    $permissionEnabled = $permissionLocked || in_array($action, $access?->actions ?? [], true);
                                @endphp
                                <td
                                    data-label="{{ ucwords(str_replace('_',' ',$action)) }}"
                                    class="ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('role-'.$selectedRole->id.'-'.$code.'-'.$action), label: @js(str_replace('_',' ',$action).' permission'), value: @js($permissionEnabled ? '1' : '0'), display: @js($permissionEnabled ? 'Enabled' : 'Disabled') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <input type="checkbox" class="ft-perm-check" :checked="value === '1'" :disabled="status === 'saving'"
                                        x-on:change="commit($event.target.checked ? '1' : '0', $event.target.checked ? 'Enabled' : 'Disabled', () => $wire.setMatrixAction({{ $selectedRole->id }}, '{{ $code }}', '{{ $action }}', draftValue === '1'))"
                                        @disabled($permissionLocked)>
                                    @unless($permissionLocked)<x-ui.inline-save-state compact />@endunless
                                </td>
                            @endforeach
                            @php
                                $scopeLocked = in_array($selectedRole->slug, ['super-admin', 'admin', 'administrator'], true);
                            @endphp
                            @php
                                $effectiveScope = $scopeLocked ? 'all_records' : ($access?->record_scope ?? 'none');
                            @endphp
                            <td
                                data-label="Record scope"
                                class="ft-inline-edit-shell"
                                x-data="window.FlowTrackInlineEdit({ key: @js('role-'.$selectedRole->id.'-'.$code.'-scope'), label: 'record scope', value: @js($effectiveScope), display: @js(str_replace('_',' ',$effectiveScope)) })"
                                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                            >
                                <select class="ft-scope-select" x-model="draftValue" :disabled="status === 'saving'"
                                    x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.setModuleScope({{ $selectedRole->id }}, '{{ $code }}', draftValue))"
                                    @disabled($scopeLocked)>
                                    @foreach(['none'=>'None','own_records'=>'Own records','assigned_jobs'=>'Assigned Jobs','selected_clients'=>'Selected clients','department'=>'Department','all_records'=>'All records'] as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                                </select>
                                @unless($scopeLocked)<x-ui.inline-save-state compact />@endunless
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ft-access-info">For normal team roles, <b>Assigned Jobs</b> means the Job must belong to the user as owner/coordinator/member or contain a task assigned to that user. Task records remain restricted to the actual task assignee unless the role has <b>All records</b>.</div>
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
                            <option value="selected_clients">Selected clients</option>
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
