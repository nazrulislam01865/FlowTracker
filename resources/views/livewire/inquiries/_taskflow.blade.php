<section class="panel ft-inquiry-taskflow-panel">
    <header class="panelhead"><div><h2>Inquiry Taskflow</h2><p>Task status can be changed at any time, including reopening a completed task.</p></div><div class="task-control-row"><span class="task-count-pill">{{ $totalTasks }} Tasks</span><span class="manage-badge">Taskflow</span>@if($canAddInquiryTask)<button class="primary" type="button" wire:click="openAddTaskForm" style="min-height:34px">＋ Add Task</button>@endif</div></header>
    <div class="ft-inquiry-task-grid-head" aria-hidden="true">
        <span>#</span><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Files</span><span>Action</span>
    </div>
    <div class="ft-inquiry-task-list">
        @forelse($inquiry->tasks as $i => $task)
            @php
                $state = $task->completed_at ? 'done' : (strcasecmp(trim((string) $task->status), 'In Progress') === 0 ? 'active' : 'wait');
                $fileOk = !$task->requires_submission || (int)$task->documents_count > 0;
                $canChangeStatusThisTask = !$inquiry->result && ($canEditInquiry || ((int)$task->assignee_id === (int)auth()->id() && auth()->user()->canModule('inquiries', 'view')));
                // Assignee and due date stay editable even after completion.
                // Attachments/action controls keep their completed-task lock.
                $canEditTaskFields = $canChangeStatusThisTask;
                $canEditThisTask = $state !== 'done' && $canChangeStatusThisTask;
                $taskDeepLinked = (int)($selectedTaskId ?? 0) === (int)$task->id;
                $canCompleteThisTask = !$task->completed_at && strcasecmp(trim((string) $task->status), 'In Progress') === 0;
            @endphp
            <div class="ft-inquiry-task-row {{ $state }} {{ $taskDeepLinked ? 'is-highlighted' : '' }}" wire:key="inquiry-task-row-{{ $task->id }}">
                <div class="ft-inquiry-task-step"><span>{{ $state === 'done' ? '✓' : $i + 1 }}</span></div>
                <div class="ft-inquiry-task-copy">
                    <strong>{{ $task->title }}</strong>
                    <div class="ft-rich-text-content ft-inquiry-task-description">@if($task->description)<x-ui.mention-text :text="$task->description" />@else No instructions added. @endif</div>
                    @if($task->requires_submission)<span class="reqfile {{ $fileOk ? 'ok' : '' }}">{{ $fileOk ? '✓ File submitted' : '□ Required file' }}</span>@endif
                </div>

                <div class="ft-inquiry-assignee-inline ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-task-'.$task->id.'-assignee'), label: 'task assignee', value: @js($task->assignee_id ?? ''), display: @js($task->assignee?->name ?? 'Unassigned'), avatarUrl: @js($task->assignee?->profileImageUrl() ?? '') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    x-on:click.outside="if (editing) cancelEdit()"
                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeInline({{ $task->id }}, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })">
                    <div class="ft-inquiry-inline-display-row">
                        <div x-show="!editing" class="ft-inquiry-assignee-display">
                            <span class="ft-inline-avatar-slot"><x-ui.inline-live-avatar :size="28" /></span>
                            <span class="ft-inquiry-assignee-name" x-text="display">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                        </div>
                        @if($canEditTaskFields)<button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>@endif
                    </div>
                    @if($canEditTaskFields)
                        <div x-cloak x-show="editing" class="ft-inquiry-assignee-picker">
                            <x-ui.inline-remote-user :value="$task->assignee_id ?? ''" :selected-label="$task->assignee?->name ?? 'Unassigned'" trigger-class="ft-task-inline-input" variant="compact" :menu-width="260" />
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </div>

                <div class="ft-inquiry-task-date ft-inline-edit-shell"
                    x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-task-'.$task->id.'-due-date'), label: 'task due date', value: @js($task->due_date?->format('Y-m-d') ?? ''), display: @js($task->due_date?->format('M j, Y') ?? 'Set due date') })"
                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                    <div class="ft-inquiry-inline-display-row" x-show="!editing">
                        <span class="ft-inquiry-inline-value" x-text="display">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
                        @if($canEditTaskFields)<button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.inquiryDue.showPicker ? $refs.inquiryDue.showPicker() : $refs.inquiryDue.focus())">✎</button>@endif
                    </div>
                    @if($canEditTaskFields)
                        <input x-ref="inquiryDue" x-cloak x-show="editing" x-model="draftValue" class="ft-inquiry-inline-input" type="date"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:blur="if (editing) cancelEdit()"
                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueInline({{ $task->id }}, draftValue))">
                        <x-ui.inline-save-state compact />
                    @endif
                </div>
                <div class="task-status-cell">
                    <span
                        class="ft-task-inline-status-shell ft-inline-edit-shell"
                        x-data="window.FlowTrackInlineEdit({ key: @js('inquiry-task-'.$task->id.'-status'), label: 'task status', value: @js($task->status), display: @js($task->status) })"
                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                    >
                        <select
                            class="ft-inline-task-status {{ \App\Support\JobDetailPresenter::taskStatusClass((string) $task->status) }}"
                            x-model="draftValue"
                            x-on:change="const next=$event.target.value; commit(next, selectedLabel($event), async () => { const result=await $wire.updateTaskStatusInline({{ $task->id }}, draftValue); if(result?.inquiryStatus) inquiryStatus=result.inquiryStatus; if(result && Object.prototype.hasOwnProperty.call(result,'inquiryStartValue')){ inquiryStartValue=result.inquiryStartValue || ''; inquiryStartDisplay=result.inquiryStartDisplay || '—'; window.dispatchEvent(new CustomEvent('flowtrack-inquiry-started',{detail:{value:inquiryStartValue,display:inquiryStartDisplay}})); } return result; })"
                            :disabled="status === 'saving'"
                            @disabled(!$canChangeStatusThisTask)
                            aria-label="Change {{ $task->title }} status"
                        >
                            @foreach(\App\Services\InquiryService::TASK_STATUSES as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                        @if($canChangeStatusThisTask)<x-ui.inline-save-state compact />@endif
                    </span>
                </div>
                <div class="ft-inquiry-task-files">
                    @if($canEditThisTask)
                        <button class="ft-inquiry-attach-button" type="button" wire:click="openTaskDocumentModal({{ $task->id }})">＋ Attach</button>
                    @endif
                    <span><b>{{ $task->documents_count }}</b> file{{ $task->documents_count === 1 ? '' : 's' }}</span>
                </div>
                <div class="ft-inquiry-task-action">
                    @if($state === 'done')
                        <span class="ft-inquiry-complete-state">✓ Completed</span>
                    @elseif($canCompleteThisTask)
                        <button class="ft-inquiry-action-button primary-action" type="button" wire:click="completeTaskInline({{ $task->id }})" wire:loading.attr="disabled" wire:target="completeTaskInline({{ $task->id }})" @disabled(!$canEditThisTask || !$fileOk)>{{ !$fileOk ? 'File required' : 'Complete' }}</button>
                    @else
                        <button class="ft-inquiry-action-button" type="button" disabled>Waiting</button>
                    @endif
                </div>
            </div>
            @if($task->documents->isNotEmpty())
                <div class="ft-inquiry-task-document-list" wire:key="inquiry-task-documents-{{ $task->id }}">
                    @foreach($task->documents as $taskDocument)
                        <div class="ft-inquiry-task-document-row" wire:key="inquiry-task-document-{{ $taskDocument->id }}">
                            <span class="ft-inquiry-task-file-type">{{ strtoupper(pathinfo($taskDocument->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                            <div class="ft-inquiry-task-file-copy">
                                <b title="{{ $taskDocument->name }}">{{ $taskDocument->name }}</b>
                                @if($taskDocument->note)<span class="ft-inquiry-task-file-note">{{ $taskDocument->note }}</span>@endif
                                <small>{{ $taskDocument->created_at ? \App\Support\UserLocalTime::format($taskDocument->created_at, 'M j, Y, g:i A') : '—' }}</small>
                            </div>
                            <div class="ft-inquiry-task-file-actions">
                                <a href="{{ route('inquiries.documents.open', $taskDocument) }}" target="_blank" rel="noopener">Open</a>
                                @if($canEditThisTask)
                                    <button type="button" class="ft-inquiry-task-file-remove" wire:click="deleteTaskDocument({{ $task->id }}, {{ $taskDocument->id }})" wire:loading.attr="disabled" wire:target="deleteTaskDocument({{ $task->id }}, {{ $taskDocument->id }})" title="Remove attachment" aria-label="Remove {{ $taskDocument->name }}">×</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @empty
            <div class="ft-inquiry-empty-workflow">No taskflow tasks configured.</div>
        @endforelse
    </div>

    @if($showAddTaskForm && $canAddInquiryTask)
        <div class="ft-inquiry-add-task" wire:key="inquiry-add-task-form">
            <div class="ft-inquiry-add-task-head">
                <div><strong>Add taskflow task</strong><span>The task is appended after the existing taskflow. If the taskflow was already complete, this new task becomes active.</span></div>
                <button class="ft-inquiry-add-task-close" type="button" wire:click="cancelAddTask" aria-label="Close add task form">×</button>
            </div>
            <div class="ft-inquiry-add-task-grid">
                <label class="ft-inquiry-add-task-field ft-inquiry-add-task-field-wide"><span>Task name *</span><input type="text" wire:model="newTaskName" placeholder="Task name"></label>
                <label class="ft-inquiry-add-task-field"><span>Assignee</span><select wire:model="newTaskAssigneeId"><option value="">Unassigned</option>@foreach($userOptions as $userOption)<option value="{{ $userOption['id'] }}">{{ $userOption['name'] }}</option>@endforeach</select></label>
                <label class="ft-inquiry-add-task-field"><span>Due date</span><input type="date" wire:model="newTaskDueDate" onclick="this.showPicker && this.showPicker()"></label>
                <label class="ft-inquiry-add-task-field ft-inquiry-add-task-field-wide"><span>Instructions</span><textarea data-rich-text wire:model="newTaskDescription" placeholder="Describe what must be completed for this task or paste screenshots here."></textarea></label>
                <label class="ft-inquiry-add-task-field"><span>Submission</span><select wire:model.live.boolean="newTaskRequiresSubmission"><option value="0">No required file</option><option value="1">Required file</option></select></label>
                @if($newTaskRequiresSubmission)<label class="ft-inquiry-add-task-field"><span>Required file</span><input type="text" wire:model="newTaskSubmissionLabel" placeholder="Submission name"></label>@endif
            </div>
            @error('newTaskName')<div class="ft-inquiry-add-task-error">{{ $message }}</div>@enderror
            <div class="ft-inquiry-add-task-actions"><button class="secondary" type="button" wire:click="cancelAddTask">Cancel</button><button class="primary" type="button" wire:click="addInquiryTask" wire:loading.attr="disabled" wire:target="addInquiryTask">Add Task</button></div>
        </div>
    @endif
</section>
