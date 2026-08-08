@props(['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'mentionUsers'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'jobTaskSearch'=>'','activityTab'=>'all','activityPage'=>1,'jobDocumentUploads'=>[]])
@php
    $productRows = \App\Support\JobDetailPresenter::products($job);
    $completedProductRows = $productRows->filter(fn ($item) => filled($item->product_name ?? null));
    $persistedProductRowCount = $productRows->filter(fn ($item) => filled($item->id ?? null))->count();
    $nextTask = \App\Support\JobDetailPresenter::nextTask($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditJob = $accessControl->canEditVisibleJob(auth()->user(), $job);
    $canAssignJob = $accessControl->canAssignVisibleJob(auth()->user());
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $canUploadDocument = $accessControl->can(auth()->user(), 'documents', 'create');
    $requiredDocuments = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $configuredTasks = $job->workflow->phases->flatMap(fn($phase) => \App\Support\JobDetailPresenter::phaseTasks($job,$phase))->values();
@endphp
<div class="ft-job-overview-section ft-exact-overview">
    <div class="ft-overview-metrics">
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">▣</span><div><small>Current phase</small><b>{{ $job->phase?->name }} · Phase {{ $job->phase?->sequence }} of {{ $job->workflow->phases->count() }}</b><p>{{ $currentTasks->count() }} tasks · {{ $done }} of {{ $currentTasks->count() }} complete</p></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">↗</span><div><small>Overall progress</small><b>{{ $job->progress }}%</b><div class="ft-line-progress"><span style="width:{{ $job->progress }}%"></span></div></div></div>
        <div class="ft-overview-metric"><span class="ft-metric-icon blue">⌘</span><div><small>Next required action</small><b>{{ $nextTask?->title ?? ($job->next_action ?: 'Review client requirement') }}</b><p>{{ $nextTask?->assignee?->name ?? $job->coordinator?->name ?? 'Unassigned' }}</p></div></div>
    </div>

    <div class="ft-overview-top-grid">
        <section class="ft-detail-card ft-overview-card">
            <h2>Order overview</h2>
            <div
                class="ft-editable-copy ft-editable-description ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-description'), label: 'Order description', value: @js($job->description ?? ''), display: @js($job->description ?: 'No order description recorded.') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <div class="ft-edit-display-row" x-show="!editing">
                    <span>
                        <span x-show="String(value) === String(serverValue)">@if($job->description)<x-ui.mention-text :text="$job->description" />@else No order description recorded. @endif</span>
                        <span x-cloak x-show="String(value) !== String(serverValue)" x-text="display"></span>
                    </span>
                    @if($canEditJob)
                        <button type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit order description" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.descriptionEditor.focus())">✎</button>
                    @endif
                </div>
                @if($canEditJob)
                    <div x-cloak x-show="editing" class="ft-inline-description-editor">
                        <textarea x-ref="descriptionEditor" x-model="draftValue" rows="3" class="ft-mention-input" autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}" x-on:keydown.escape.prevent="cancelEdit()"></textarea>
                        <div>
                            <button type="button" class="ft-outline-btn" x-on:click="cancelEdit()">Cancel</button>
                            <button type="button" class="ft-new-job-btn" x-on:click="commit(draftValue.trim(), draftValue.trim() || 'No order description recorded.', () => $wire.updateJobTextField({{ $job->id }}, 'description', draftValue))">Save</button>
                        </div>
                    </div>
                    <x-ui.inline-save-state />
                @endif
            </div>
            <div class="ft-card-section-head"><b>Products &amp; quantities</b><span>{{ $completedProductRows->count() }} product · {{ number_format($completedProductRows->sum('quantity')) }} total units</span></div>
            <table class="ft-mini-grid-table ft-inline-product-table">
                <thead><tr><th>Category</th><th>Product</th><th>Quantity</th><th class="ft-product-delete-column"><span class="sr-only">Action</span></th></tr></thead>
                <tbody>
                @foreach($productRows as $item)
                    @php
                        $isDraftItem = filled($item->id) && blank($item->product_name);
                        $categoryNeedsSelection = filled($item->id) && blank($item->category_name);
                        $productNeedsSelection = filled($item->id) && filled($item->category_name) && blank($item->product_name);
                        $categoryLabel = $item->category_name ?: 'Select category';
                        $productLabel = $item->product_name ?: (blank($item->category_name) ? 'Select category first' : 'Select product');
                        $productPickerKey = 'job-item-'.$item->id.'-product-'.md5((string) ($item->category_name ?? '').'|'.(string) ($item->product_name ?? ''));
                    @endphp
                    <tr wire:key="job-item-{{ $item->id ?? $loop->index }}" x-data="{ categorySaving: false, productSaving: false, quantitySaving: false, draftProductReady: @js(filled($item->product_name)) }" @class(['ft-inline-product-draft-row' => $isDraftItem])>
                        <td>
                            @if($item->id && $isDraftItem)
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor"
                                    wire:key="job-item-{{ $item->id }}-category-{{ md5((string) ($item->category_name ?? '')) }}"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-category'), label: 'product category', value: @js($item->category_name ?? ''), display: @js($categoryLabel) })"
                                    x-init="if (@js($categoryNeedsSelection)) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing && !@js($categoryNeedsSelection)) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="if (!@js($categoryNeedsSelection)) cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select category'); const changed = nextValue !== savedValue; categorySaving = true; commit(nextValue, nextLabel, () => $wire.updateJobItem({{ $item->id }}, 'category_name', nextValue)).then(async (ok) => { if (ok && changed) await $wire.$refresh(); categorySaving = false })"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display">{{ $categoryLabel }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" :disabled="status === 'saving' || productSaving || quantitySaving" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                            <x-ui.inline-remote-catalog
                                                type="product-categories"
                                                :value="$item->category_name ?? ''"
                                                :selected-label="$categoryLabel"
                                                placeholder="Select category"
                                                search-label="product category"
                                                :menu-width="320"
                                            />
                                        </div>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </div>
                            @else
                                {{ $item->category_name }}
                            @endif
                        </td>
                        <td>
                            @if($item->id && $isDraftItem)
                                <div
                                    class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor"
                                    wire:key="{{ $productPickerKey }}"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-product'), label: 'product', value: @js($item->product_name ?? ''), display: @js($productLabel) })"
                                    x-init="if (@js($productNeedsSelection)) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing && !@js($productNeedsSelection)) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="if (!@js($productNeedsSelection)) cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select product'); productSaving = true; commit(nextValue, nextLabel, () => $wire.updateJobItem({{ $item->id }}, 'product_name', nextValue)).then((ok) => { productSaving = false; if (ok) { draftProductReady = true; $nextTick(() => setTimeout(() => { const input = $el.closest('tr')?.querySelector('[data-job-item-quantity]'); input?.focus(); input?.select(); }, 0)) } })"
                                >
                                    <span class="ft-inline-field-value" x-show="!editing" x-text="display">{{ $productLabel }}</span>
                                    @if($canEditJob)
                                        <button x-show="!editing" :disabled="status === 'saving' || categorySaving || quantitySaving || @js(blank($item->category_name))" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="{{ blank($item->category_name) ? 'Select a category first' : 'Edit product' }}" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                            <x-ui.inline-remote-catalog
                                                type="products"
                                                :value="$item->product_name ?? ''"
                                                :selected-label="$productLabel"
                                                :placeholder="blank($item->category_name) ? 'Select category first' : 'Select product'"
                                                search-label="product"
                                                :params="['category' => (string) ($item->category_name ?? '')]"
                                                :disabled="blank($item->category_name)"
                                                :menu-width="340"
                                            />
                                        </div>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </div>
                            @else
                                {{ $item->product_name }}
                            @endif
                        </td>
                        <td class="ft-product-quantity-cell">
                            @if($item->id)
                                @if($isDraftItem)
                                    <div
                                        class="ft-inline-field-editor ft-inline-edit-shell ft-inline-product-quantity-editor"
                                        x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-quantity'), label: 'quantity', value: @js((string) $item->quantity), display: @js(number_format((int)$item->quantity)) })"
                                        x-init="editing = true"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    >
                                        <input x-ref="quantityInput" data-job-item-quantity x-cloak x-show="editing" x-model="draftValue" class="ft-inline-cell-input quantity" type="number" min="1" :disabled="categorySaving || productSaving"
                                            x-on:keydown.escape.prevent="draftValue = value"
                                            x-on:keydown.enter.prevent="$event.target.blur()"
                                            x-on:blur="if (editing && !categorySaving && !productSaving && !quantitySaving) { const next = positiveInteger(draftValue); if (next !== value) { quantitySaving = true; commit(next, numberLabel(next), () => $wire.updateJobItem({{ $item->id }}, 'quantity', next)).then(async (ok) => { quantitySaving = false; if (ok) await $wire.$refresh(); else editing = true }) } else if (draftProductReady) { editing = false; $wire.$refresh() } }">
                                        <x-ui.inline-save-state compact />
                                    </div>
                                @else
                                    <span class="ft-readonly-product-value">{{ number_format((int)$item->quantity) }}</span>
                                @endif
                            @else
                                {{ number_format((int)$item->quantity) }}
                            @endif
                        </td>
                        <td class="ft-product-delete-cell">
                            @if($item->id && $canEditJob)
                                <button
                                    type="button"
                                    class="ft-inline-product-delete"
                                    title="{{ $persistedProductRowCount <= 1 ? 'An Order must keep at least one product' : 'Remove product' }}"
                                    aria-label="Remove product"
                                    wire:click.stop="removeJobItem({{ $item->id }})"
                                    wire:confirm="Remove this product from the Order?"
                                    wire:loading.attr="disabled"
                                    wire:target="removeJobItem({{ $item->id }})"
                                    :disabled="categorySaving || productSaving || quantitySaving || @js($persistedProductRowCount <= 1)"
                                >×</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($canEditJob)
                <div class="ft-product-actions"><button class="ft-link-blue ft-add-product-inline" type="button" wire:click="addJobItem({{ $job->id }})" wire:loading.attr="disabled" wire:target="addJobItem({{ $job->id }})">＋ Add product</button></div>
            @endif
        </section>

        <aside class="ft-detail-card ft-side-panel ft-planning-panel">
            <h2>Planning &amp; ownership</h2>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-delivery-date'), label: 'delivery date', value: @js($job->delivery_date?->format('Y-m-d') ?? ''), display: @js($job->delivery_date?->format('M j, Y') ?? 'Not set') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Required delivery</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" x-text="display">{{ $job->delivery_date?->format('M j, Y') ?? 'Not set' }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit required delivery" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.deliveryInput.showPicker ? $refs.deliveryInput.showPicker() : $refs.deliveryInput.focus())">✎</button>
                        <input x-ref="deliveryInput" x-cloak x-show="editing" x-model="draftValue" type="date"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:blur="if (editing) cancelEdit()"
                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateJobDeliveryDate({{ $job->id }}, draftValue))">
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-priority'), label: 'priority', value: @js($job->priority), display: @js($job->priority) })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Priority</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" x-text="display">{{ $job->priority }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit priority" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.prioritySelect.focus())">✎</button>
                        <select x-ref="prioritySelect" x-cloak x-show="editing" x-model="draftValue" x-on:keydown.escape.prevent="cancelEdit()" x-on:blur="if (editing) cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateJobPriority({{ $job->id }}, draftValue))">
                            @foreach($priorities as $priority)<option value="{{ $priority->name }}">{{ $priority->name }}</option>@endforeach
                        </select>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-owner'), label: 'Order owner', value: @js($job->owner_id ?? ''), display: @js($job->owner?->name ?? 'Unassigned') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Order owner</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-inline-person-live ft-planning-person-value">
                        <span x-show="String(value) === String(serverValue)"><x-ui.avatar :user="$job->owner" :name="$job->owner?->name ?? 'Unassigned'" :size="24"/></span>
                        <span x-cloak x-show="String(value) !== String(serverValue)" class="ft-inline-generated-avatar" x-text="initials(display)"></span>
                        <span x-text="display">{{ $job->owner?->name ?? 'Unassigned' }}</span>
                    </span>
                    @if($canAssignJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit job owner" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.ownerSelect.focus())">✎</button>
                        <select x-ref="ownerSelect" x-cloak x-show="editing" x-model="draftValue" class="ft-planning-inline-select" x-on:keydown.escape.prevent="cancelEdit()" x-on:blur="if (editing) cancelEdit()" x-on:change="commit($event.target.value, selectedLabel($event, 'Unassigned'), () => $wire.updateJobOwner({{ $job->id }}, draftValue))">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                        </select>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div class="ft-side-row"><span>Workflow</span><b>▣ {{ $job->workflow?->name }}</b></div>
            <div class="ft-side-row"><span>Created</span><b>{{ \App\Support\UserLocalTime::format($job->created_at, 'M j, Y, g:i A') }}</b></div>
        </aside>
    </div>

    <section class="ft-workflow-mini-line ft-overview-workflow-line">
        @foreach($job->workflow->phases as $phase)
            <button type="button" class="{{ $phase->sequence < $job->phase->sequence ? 'done' : ($phase->id === $job->phase->id ? 'current' : '') }}" wire:click="setDetailTab('workflow')">
                <span>{{ $phase->sequence < $job->phase->sequence ? '✓' : $phase->sequence }}</span><small>{{ $phase->short_name }}</small>
            </button>
        @endforeach
    </section>

    <section class="ft-detail-card ft-phase-table-card ft-overview-task-card">
        <div class="ft-card-row-head ft-task-card-heading">
            <div><h2>All phase tasks</h2><p>{{ $configuredTasks->count() }} tasks across {{ $job->workflow->phases->count() }} phases</p></div>
            <div class="ft-row-actions ft-phase-toolbar-icons" aria-label="Phase task controls">
                <button type="button" class="ft-phase-toolbar-icon" wire:click="expandAllJobPhases" title="Expand all phases" aria-label="Expand all phases">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg>
                </button>
                <button type="button" class="ft-phase-toolbar-icon" wire:click="collapseAllJobPhases" title="Collapse all phases" aria-label="Collapse all phases">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg>
                </button>
            </div>
        </div>
        <div class="ft-phase-load-note"><span>◉ All {{ $configuredTasks->count() }} configured Task Pack tasks are loaded</span><span>Task status changes save automatically</span></div>
        <div class="ft-phase-task-table">
            @foreach($job->workflow->phases as $phase)
                @php
                    $allPhaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                    $completed = \App\Support\JobDetailPresenter::completedCount($allPhaseTasks);
                    $phaseProgress = $allPhaseTasks->count() ? round($completed/max(1,$allPhaseTasks->count())*100) : 0;
                    $phaseTasks = $allPhaseTasks;
                    $expanded = in_array((int) $phase->id, array_map('intval', $expandedPhaseIds), true);
                @endphp
                <div class="ft-phase-group {{ $expanded ? 'open' : '' }}" wire:key="job-phase-{{ $phase->id }}">
                    <div class="ft-phase-group-head">
                        <b class="{{ $phase->id === $job->phase->id ? 'current-number' : '' }}">{{ $phase->sequence }}</b>
                        <strong>{{ $phase->name }}</strong>
                        <small>{{ $completed }} of {{ $allPhaseTasks->count() }} complete</small>
                        <em style="--phase-progress:{{ $phaseProgress }}%"></em>
                    </div>
                    @if($expanded)
                        <div class="ft-phase-task-columns"><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Action</span></div>
                        @forelse($phaseTasks as $task)
                            @php
                                $taskAccess = app(\App\Services\AccessControlService::class);
                                $canEditTask = $taskAccess->canEditVisibleTask(auth()->user(), $task);
                                $canAssignTask = $taskAccess->canAssignTask(auth()->user(), $task);
                                $canDeleteTask = $taskAccess->can(auth()->user(), 'tasks', 'delete');
                            @endphp
                            <div class="ft-phase-task-line ft-editable-task-line" wire:key="job-task-{{ $task->id }}">
                                <span>{{ $phase->sequence }}.{{ $loop->iteration }}</span>
                                <button class="ft-inline-task-link" type="button" wire:click="openTask({{ $task->id }})">{{ $task->title }}</button>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-assignee'), label: 'task assignee', value: @js($task->assignee_id ?? ''), display: @js($task->assignee?->name ?? 'Unassigned') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeFromJob({{ $task->id }}, draftValue))"
                                >
                                    <span x-show="!editing" class="ft-task-inline-display ft-inline-person-live">
                                        <span x-show="String(value) === String(serverValue)"><x-ui.avatar :user="$task->assignee" :name="$task->assignee?->name ?? 'Unassigned'" :size="24"/></span>
                                        <span x-cloak x-show="String(value) !== String(serverValue)" class="ft-inline-generated-avatar" x-text="initials(display)"></span>
                                        <span x-text="display">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                    </span>
                                    @if($canAssignTask)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        <div x-cloak x-show="editing" class="ft-task-inline-assignee-picker">
                                            <x-ui.inline-remote-user
                                                :value="$task->assignee_id ?? ''"
                                                :selected-label="$task->assignee?->name ?? 'Unassigned'"
                                                trigger-class="ft-task-inline-input"
                                                variant="compact"
                                                :menu-width="300"
                                            />
                                        </div>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </span>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-due-date'), label: 'task due date', value: @js($task->due_date?->format('Y-m-d') ?? ''), display: @js($task->due_date?->format('M j, Y') ?? 'Set due date') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <span x-show="!editing" x-text="display" class="ft-task-inline-display {{ ($task->due_date && \App\Support\UserLocalTime::isDatePast($task->due_date)) && !$task->completed_at ? 'danger-text' : '' }}">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
                                    @if($canEditTask)
                                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskDue.showPicker ? $refs.taskDue.showPicker() : $refs.taskDue.focus())">✎</button>
                                        <input x-ref="taskDue" x-cloak x-show="editing" x-model="draftValue" class="ft-task-inline-input" type="date"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDateFromJob({{ $task->id }}, draftValue))">
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </span>
                                <span
                                    class="ft-task-inline-status-shell ft-inline-edit-shell"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-status'), label: 'task status', value: @js($task->status), display: @js($task->status) })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <select class="ft-inline-task-status {{ \App\Support\JobDetailPresenter::taskStatusClass($task->status) }}" x-model="draftValue"
                                        x-on:change="commit($event.target.value, selectedLabel($event), () => $wire.updateTaskStatusFromJob({{ $task->id }}, draftValue))"
                                        :disabled="status === 'saving'" @disabled(!$canEditTask)>
                                        @foreach($taskStatuses as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach
                                    </select>
                                    @if($canEditTask)<x-ui.inline-save-state compact />@endif
                                </span>
                                <div class="ft-task-action-wrap" x-data="{ open: false }" x-on:click.stop>
                                    <button class="ft-table-kebab" type="button" x-on:click="open = !open" aria-label="Task actions" :aria-expanded="open ? 'true' : 'false'">•••</button>
                                    <div class="ft-task-action-menu" x-cloak x-show="open" x-on:click.outside="open = false">
                                        <button type="button" x-on:click="open = false" wire:click.stop="viewTask({{ $task->id }})">View</button>
                                        @if($canEditTask)
                                            <button type="button" x-on:click="open = false" wire:click.stop="editTask({{ $task->id }})">Edit</button>
                                        @endif
                                        @if($canDeleteTask)
                                            <button type="button" class="danger" x-on:click="open = false" wire:click.stop="deleteTaskFromJob({{ $task->id }})" wire:confirm="Delete this task? The task will be removed from this Job and its phase progress will be recalculated.">Delete</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ft-phase-empty-row">No configured tasks in this phase.</div>
                        @endforelse
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="ft-detail-card ft-attachment-card ft-job-overview-attachments">
        <h2>Attachments <span>{{ $job->documents->count() }}</span></h2>
        @if($requiredDocuments->isNotEmpty())
            <div class="ft-upload-zone compact ft-task-upload-zone ft-job-overview-dropzone">
                @if($canUploadDocument)
                    <label class="ft-task-upload-drop ft-livewire-upload-zone" data-file-dropzone for="jobOverviewDocumentUpload-{{ $job->id }}">
                        <input id="jobOverviewDocumentUpload-{{ $job->id }}" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                        <span class="ft-paperclip">⌕</span>
                        <div>Drop files here or <strong>browse</strong><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    </label>
                @else
                    <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to Job attachments.</small></div></div>
                @endif
                <button class="ft-outline-btn ft-task-choose-document" type="button" wire:click="setDetailTab('documents')">Choose from Documents</button>
            </div>
            @if($canUploadDocument && count($jobDocumentUploads ?? []))
                <div class="ft-upload-ready-row">
                    <span>{{ count($jobDocumentUploads ?? []) }} file{{ count($jobDocumentUploads ?? [])===1?'':'s' }} ready</span>
                    <button class="ft-new-job-btn" type="button" wire:click="uploadJobDocuments">Upload &amp; link</button>
                </div>
            @endif
            @error('jobDocumentUploads')<div class="validation-error">{{ $message }}</div>@enderror
            @error('jobDocumentUploads.*')<div class="validation-error">{{ $message }}</div>@enderror
        @else
            <div class="ft-empty-taskpack-docs">No Task Pack document requirement is configured for this Job. Open Documents to review the document setup.</div>
        @endif
        @foreach($job->documents as $doc)<div class="ft-job-file-row"><span class="ft-file-type">{{ strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span><div><b>{{ $doc->name }}</b><small>{{ $doc->task?->title ?: 'Job document' }} · {{ $doc->uploader?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A') }}</small></div><a href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>@if($canDeleteDocument)<button type="button" wire:click="deleteJobDocument({{ $doc->id }})" wire:confirm="Delete this document link?">Delete</button>@endif</div>@endforeach
    </section>

    <x-jobs.detail-activity :job="$job" :mention-users="$mentionUsers" compact="true" :activity-tab="$activityTab" :activity-page="$activityPage" />
</div>
