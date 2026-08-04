<div class="ft-access-admin">
    <div class="ft-access-head">
        <div><h1>Access Roles & Permissions</h1><p>Control who can view, create, edit, assign, delete, link, export or manage every FlowTrack module.</p></div>
        <div class="ft-access-actions"><button class="ghost" wire:click="setTab('audit')">Audit Log</button><button class="primary" wire:click="openRole">＋ New Role</button></div>
    </div>

    <div class="ft-access-tabs">
        @foreach(['dashboard'=>'Access Dashboard','roles'=>'Roles & Policies','matrix'=>'Permission Matrix','users'=>'Users & Assignments','audit'=>'Audit Log','security'=>'Security Settings'] as $key=>$label)
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
                <div class="ft-control-note"><b>3. Record ownership</b><span>Edit own allows task assignees or Job owners/coordinators to update only their records.</span></div>
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
                        @php($access=$selectedRole->moduleAccess->firstWhere('module_code',$code))
                        <tr>
                            <td><b>{{ $meta['name'] }}</b><small>{{ $meta['group'] }}</small></td>
                            @foreach($actions as $action)
                                <td><input type="checkbox" class="ft-perm-check" wire:click="toggleMatrixAction({{ $selectedRole->id }},'{{ $code }}','{{ $action }}')" @checked(in_array($selectedRole->slug,['super-admin','admin','administrator'],true) || in_array($action,$access?->actions??[],true)) @disabled(in_array($selectedRole->slug,['super-admin','admin','administrator'],true))></td>
                            @endforeach
                            <td><select class="ft-scope-select" wire:change="setModuleScope({{ $selectedRole->id }},'{{ $code }}',$event.target.value)" @disabled(in_array($selectedRole->slug,['super-admin','admin','administrator'],true))>
                                @foreach(['none'=>'None','own_records'=>'Own records','assigned_jobs'=>'Assigned Jobs','selected_clients'=>'Selected clients','department'=>'Department','all_records'=>'All records'] as $value=>$label)<option value="{{ $value }}" @selected((in_array($selectedRole->slug,['super-admin','admin','administrator'],true)?'all_records':($access?->record_scope??'none'))===$value)>{{ $label }}</option>@endforeach
                            </select></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ft-access-info">For normal team roles, <b>Assigned Jobs</b> means the Job must belong to the user as owner/coordinator/member or contain a task assigned to that user. Task records remain restricted to the actual task assignee unless the role has <b>All records</b>.</div>
        @endif
    @elseif($tab==='users')
        <div class="section-head"><div><h3>Users & role assignments</h3><div class="small muted">Assign the effective role used throughout FlowTrack.</div></div><button class="primary" wire:click="openUser">＋ Add User</button></div>
        <div class="card table-wrap"><table class="data-table ft-user-access-table"><thead><tr><th>User</th><th>Department</th><th>Role</th><th>Effective scope</th><th>Open tasks</th><th>Status</th></tr></thead><tbody>
        @foreach($users as $u)<tr><td><div class="person"><x-ui.avatar :name="$u->name"/><div><b>{{ $u->name }}</b><div class="small muted">{{ $u->email }}</div></div></div></td><td>{{ $u->department?->name ?? '—' }}</td><td><select wire:change="assignRole({{ $u->id }},$event.target.value)" @disabled($u->isSuperAdmin())>@foreach($roles->where('is_active',true) as $role)<option value="{{ $role->id }}" @selected($u->role_id===$role->id)>{{ $role->name }}</option>@endforeach</select></td><td><span class="tag">{{ str_replace('_',' ',$u->role?->default_scope ?? 'none') }}</span></td><td>{{ $u->open_tasks_count }}</td><td><button class="mini-btn" wire:click="toggleUserActive({{ $u->id }})" @disabled($u->isSuperAdmin())><span class="badge {{ $u->is_active?'b-green':'b-gray' }}">{{ $u->is_active?'Active':'Inactive' }}</span></button></td></tr>@endforeach
        </tbody></table></div>
    @elseif($tab==='audit')
        <section class="card ft-access-panel"><div class="section-head"><div><h3>Access audit log</h3><div class="small muted">Role, permission, scope, security and assignment changes.</div></div></div>
            <div class="ft-access-audit">@forelse($auditLog as $event)<div class="ft-audit-row"><div class="ft-audit-dot">{{ strtoupper(substr($event->user?->name ?? 'S',0,1)) }}</div><div><b>{{ $event->description }}</b><span>{{ $event->user?->name ?? 'System' }} · {{ $event->created_at?->format('M j, Y g:i A') }}</span></div><code>{{ $event->event }}</code></div>@empty<div class="empty">No access changes recorded yet.</div>@endforelse</div>
        </section>
    @elseif($tab==='security')
        <div class="ft-access-grid-2">
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Security controls</h3><div class="small muted">Workspace access-control policy flags stored in Master Data.</div></div></div>
                @foreach($securitySettings as $setting)<div class="ft-security-row"><div><b>{{ $setting['label'] }}</b><span>Administrative access policy</span></div><label class="ft-switch"><input type="checkbox" wire:click="toggleSecurity('{{ $setting['code'] }}')" @checked($setting['enabled'])><i></i></label></div>@endforeach
            </section>
            <section class="card ft-access-panel"><div class="section-head"><div><h3>Access policy</h3></div></div><div class="ft-control-note"><b>Administrator/Super Admin</b><span>Unrestricted application access. These roles can configure all permissions.</span></div><div class="ft-control-note"><b>All other roles</b><span>Must pass both the action permission and record-scope check on every page and update.</span></div><div class="ft-control-note"><b>Assignments</b><span>Task assignees see their assigned tasks; associated Job visibility follows from those assignments.</span></div></section>
        </div>
    @endif

    @if($showUserModal)<div class="overlay livewire-overlay" wire:click.self="closeUser"></div><div class="modal livewire-modal"><div class="modal-head"><h2>Add User</h2><button class="close-btn" wire:click="closeUser">×</button></div><div class="modal-body"><div class="form-grid"><div class="field"><label>Full name *</label><input wire:model="name"></div><div class="field"><label>Email *</label><input wire:model="email" type="email">@error('email')<div class="validation-error">{{ $message }}</div>@enderror</div><div class="field"><label>Role *</label><select wire:model="roleId"><option value="">Select role</option>@foreach($roles->where('is_active',true) as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div><div class="field"><label>Department</label><select wire:model="departmentId"><option value="">No department</option>@foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div><div class="field full"><label>Temporary password *</label><input wire:model="password" type="password">@error('password')<div class="validation-error">{{ $message }}</div>@enderror</div></div></div><div class="modal-foot"><button class="ghost" wire:click="closeUser">Cancel</button><button class="primary" wire:click="createUser">Create User</button></div></div>@endif

    @if($showRoleModal)<div class="overlay livewire-overlay" wire:click.self="closeRole"></div><div class="modal livewire-modal"><div class="modal-head"><h2>{{ $editingRoleId?'Edit Role':'Create Role' }}</h2><button class="close-btn" wire:click="closeRole">×</button></div><div class="modal-body"><div class="form-grid"><div class="field"><label>Role name *</label><input wire:model="roleName"></div><div class="field"><label>Role code</label><input wire:model="roleCode" placeholder="JOB_MANAGER"></div><div class="field full"><label>Description</label><textarea wire:model="roleDescription" rows="3"></textarea></div><div class="field"><label>Default record scope</label><select wire:model="roleDefaultScope"><option value="none">None</option><option value="own_records">Own records</option><option value="assigned_jobs">Assigned Jobs</option><option value="selected_clients">Selected clients</option><option value="department">Department</option><option value="all_records">All records</option></select></div><div class="field"><label>Status</label><select wire:model="roleActive"><option value="1">Active</option><option value="0">Inactive</option></select></div></div></div><div class="modal-foot"><button class="ghost" wire:click="closeRole">Cancel</button><button class="primary" wire:click="saveRole">Save Role</button></div></div>@endif
</div>
