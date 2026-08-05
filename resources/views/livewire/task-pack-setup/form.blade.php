<div class="ft-admin-reference ft-taskpack-form-page">
    <div class="ft-admin-form-top">
        <div>
            <div class="ft-admin-breadcrumb">{{ $taskPackId ? 'Edit Task Pack' : 'Add Task Pack' }}</div>
            <h1>{{ $taskPackId ? 'Edit Task Pack' : 'Add Task Pack' }}</h1>
            <p>Build the task sequence on a full page for easier editing.</p>
        </div>
        <a href="{{ route('task-pack.setup') }}" wire:navigate class="ft-admin-back">← Back to Task Pack Setup</a>
    </div>

    <form wire:submit="save" class="ft-admin-form-card">
        <div class="ft-admin-form-card-head">
            <h2>{{ $taskPackId ? 'Edit Task Pack' : 'Create Task Pack' }}</h2>
            <p>Build the complete reusable task sequence activated by workflow phases.</p>
        </div>

        <div class="ft-admin-form-body">
            <div class="ft-admin-field">
                <label>Task Pack code</label>
                <div class="ft-admin-locked">{{ $packCode }}</div>
                <small>Automatically generated and permanently locked.</small>
            </div>

            <div class="ft-admin-field">
                <label>Status</label>
                <select wire:model="packStatus">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                @error('packStatus')<div class="validation-error">{{ $message }}</div>@enderror
            </div>

            <div class="ft-admin-field">
                <label>Task Pack name *</label>
                <input type="text" wire:model="packName" placeholder="e.g. Quality Inspection">
                @error('packName')<div class="validation-error">{{ $message }}</div>@enderror
            </div>

            <div class="ft-admin-field">
                <label>Description</label>
                <textarea wire:model="packDescription" rows="3" placeholder="Explain when this Task Pack is used..."></textarea>
                @error('packDescription')<div class="validation-error">{{ $message }}</div>@enderror
            </div>

            <div class="ft-sequence-title-row">
                <div><h2>Task sequence</h2><p>Tasks are created in this order when the phase becomes active.</p></div>
                <button type="button" class="ft-add-soft" wire:click="addTask">＋ Add Task</button>
            </div>

            @error('tasks')<div class="validation-error">{{ $message }}</div>@enderror

            <div class="ft-task-editor-list">
                @foreach($tasks as $index => $task)
                    <section class="ft-task-editor-card" wire:key="task-pack-form-task-{{ $task['id'] ?? 'new-'.$index }}-{{ $index }}">
                        <div class="ft-task-editor-head">
                            <div><h3>Task {{ $index + 1 }}</h3><p>Sequence {{ $index + 1 }} in this Task Pack</p></div>
                            <div class="ft-task-editor-actions">
                                <button type="button" wire:click="moveTask({{ $index }}, -1)" @disabled($index === 0)>↑</button>
                                <button type="button" wire:click="moveTask({{ $index }}, 1)" @disabled($index === count($tasks)-1)>↓</button>
                                <button type="button" wire:click="removeTask({{ $index }})">Remove</button>
                            </div>
                        </div>

                        <div class="ft-admin-field">
                            <label>Task title *</label>
                            <input type="text" wire:model="tasks.{{ $index }}.title" placeholder="Task title">
                            @error("tasks.$index.title")<div class="validation-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="ft-admin-field">
                            <label>Description</label>
                            <textarea wire:model="tasks.{{ $index }}.description" rows="2"></textarea>
                        </div>

                        <div class="ft-admin-field">
                            <label>Default assignee</label>
                            <select wire:model="tasks.{{ $index }}.default_assignee_id">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                            </select>
                        </div>

                        <div class="ft-admin-field">
                            <label>Default department</label>
                            <select wire:model="tasks.{{ $index }}.default_department_id">
                                <option value="">No department default</option>
                                @foreach($departments as $department)<option value="{{ $department->id }}">{{ $department->name }}</option>@endforeach
                            </select>
                        </div>

                        <div class="ft-admin-field">
                            <label>Priority</label>
                            <select wire:model="tasks.{{ $index }}.priority_id">
                                <option value="">Use Job priority</option>
                                @foreach($priorities as $priority)<option value="{{ $priority->id }}">{{ $priority->name }}</option>@endforeach
                            </select>
                        </div>

                        <div class="ft-admin-field">
                            <label>Required document</label>
                            <select wire:model="tasks.{{ $index }}.document_category_id">
                                <option value="">No task-specific file</option>
                                @foreach($documentCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                            </select>
                            <small>The file must be attached to the Job before this task can be completed.</small>
                        </div>

                        <label class="ft-required-task-check">
                            <input type="checkbox" wire:model="tasks.{{ $index }}.is_required">
                            <span>Required task</span>
                        </label>
                    </section>
                @endforeach
            </div>
        </div>

        <div class="ft-admin-form-footer">
            <button type="button" class="ft-admin-cancel" wire:click="cancel">Cancel</button>
            <button type="submit" class="ft-admin-primary">{{ $taskPackId ? 'Save Task Pack' : 'Create Task Pack' }}</button>
        </div>
    </form>
</div>
