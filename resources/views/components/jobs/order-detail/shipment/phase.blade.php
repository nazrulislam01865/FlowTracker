@props([
    'job',
    'phase',
    'presentation' => [],
])

<section class="ft-shipment-phase" aria-label="Shipment tasks" wire:key="shipment-phase-{{ $job->id }}-{{ $phase->id }}">
    <div class="ft-shipment-phase__status">
        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M2.5 5.5h9v8h-9zM11.5 8h3l3 3v2.5h-6z"/><circle cx="6" cy="15" r="1.5"/><circle cx="14.5" cy="15" r="1.5"/></svg>
        Order status: Ready for shipment
    </div>

    <header class="ft-shipment-phase__head">
        <div>
            <h3>Shipment tasks</h3>
            <p>Complete these steps in order to dispatch the package.</p>
        </div>
        <div class="ft-shipment-progress" aria-label="{{ $presentation['completed_count'] ?? 0 }} of {{ $presentation['total_count'] ?? 0 }} complete">
            <strong class="{{ ($presentation['completed_count'] ?? 0) === ($presentation['total_count'] ?? 0) && ($presentation['total_count'] ?? 0) > 0 ? 'is-complete' : '' }}">
                {{ $presentation['completed_count'] ?? 0 }} of {{ $presentation['total_count'] ?? 0 }} complete
            </strong>
        </div>
    </header>

    <div class="ft-shipment-phase__tasks">
        @foreach(($presentation['tasks'] ?? []) as $row)
            <article
                class="ft-shipment-task ft-shipment-task--{{ $row['mode'] }}"
                wire:key="shipment-task-{{ $row['task']->id }}-{{ $row['mode'] }}-{{ (int) $row['is_done'] }}"
            >
                <div class="ft-shipment-task__marker-wrap" aria-hidden="true">
                    <span class="ft-shipment-task__marker">
                        {{ $row['is_done'] ? '✓' : ($row['mode'] === 'active' ? '●' : '⌁') }}
                    </span>
                </div>

                <div class="ft-shipment-task__content">
                    <div class="ft-shipment-task__top">
                        <div class="ft-shipment-task__copy">
                            <div class="ft-shipment-task__eyebrow">
                                <span>TASK {{ $row['display_code'] }}</span>
                                @if($row['mode'] === 'active')<em>Current task</em>@endif
                            </div>
                            <h4>{{ $row['title'] }}</h4>
                            <p>{{ $row['description'] }}</p>
                        </div>

                        <x-jobs.order-detail.shipment.task-meta :job="$job" :row="$row" />

                        <div class="ft-shipment-task__state">
                            @if($row['mode'] === 'active')
                                <span class="ft-shipment-state ft-shipment-state--action"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 6.5v4M10 13.5h.01"/></svg>Action required</span>
                            @elseif($row['is_done'])
                                <span class="ft-shipment-state ft-shipment-state--done">Completed</span>
                            @else
                                <span class="ft-shipment-state ft-shipment-state--locked"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Locked</span>
                            @endif
                        </div>
                    </div>

                    @if($row['key'] === 'SHIP_CONFIRM_INFO')
                        <div class="ft-shipment-task__expanded">
                            <section class="ft-shipment-current-details">
                                <header>
                                    <strong>Current shipment details</strong>
                                    @if($row['can_edit'] && ($row['mode'] === 'active' || $row['is_done']))
                                        <button type="button" wire:click="openOrderWorkflowAction({{ $row['task']->id }})">
                                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                            Edit
                                        </button>
                                    @endif
                                </header>
                                <dl>
                                    <div><dt>Recipient:</dt><dd>{{ $presentation['recipient'] ?: '—' }}</dd></div>
                                    <div><dt>Address:</dt><dd>{{ $presentation['address'] ?: '—' }}</dd></div>
                                    <div><dt>Client:</dt><dd>{{ $presentation['client_name'] ?: '—' }}</dd></div>
                                    <div><dt>Postal code:</dt><dd>{{ $presentation['postal_code'] ?: '—' }}</dd></div>
                                    <div><dt>Phone:</dt><dd>{{ $presentation['phone'] ?: '—' }}</dd></div>
                                </dl>
                            </section>

                            <section class="ft-shipment-review-panel">
                                @if($row['is_done'])
                                    <h5>Shipment details remain editable</h5>
                                    <p>Update the recipient, contact or delivery address whenever a shipment detail changes.</p>
                                    @if($row['can_edit'])
                                        <div class="ft-shipment-review-panel__actions">
                                            <button type="button" class="ft-shipment-btn ft-shipment-btn--primary" wire:click="openOrderWorkflowAction({{ $row['task']->id }})">
                                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>Edit shipment details
                                            </button>
                                        </div>
                                    @endif
                                    <small><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 9v4M10 6.5h.01"/></svg>Updating these details does not reopen the completed task.</small>
                                @else
                                    <h5>Do you want to update the shipment details?</h5>
                                    <p>Choose Update details to edit the existing information, or continue without changes.</p>
                                    <div class="ft-shipment-review-panel__actions">
                                        <button type="button" class="ft-shipment-btn ft-shipment-btn--outline" @disabled(!$row['can_edit'] || $row['mode'] !== 'active') wire:click="confirmShipmentDetailsWithoutChanges({{ $row['task']->id }})" wire:loading.attr="disabled" wire:target="confirmShipmentDetailsWithoutChanges">
                                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4.5 10.5 3.2 3.2 7.8-8"/></svg>No changes — continue
                                        </button>
                                        <button type="button" class="ft-shipment-btn ft-shipment-btn--primary" @disabled(!$row['can_edit'] || $row['mode'] !== 'active') wire:click="openOrderWorkflowAction({{ $row['task']->id }})">
                                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>Update details
                                        </button>
                                    </div>
                                    <small><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 9v4M10 6.5h.01"/></svg>Either action completes this task and unlocks tracking setup.</small>
                                @endif
                            </section>
                        </div>
                    @elseif($row['key'] === 'SHIP_LABEL')
                        <div
                            class="ft-shipment-inline-work"
                            wire:key="shipment-tracking-work-{{ $row['task']->id }}-{{ $row['mode'] }}-{{ (int) $row['can_edit'] }}"
                            x-data="{
                                carrier: @js($presentation['carrier'] ?? ''),
                                tracking: @js($presentation['tracking'] ?? ''),
                                originalCarrier: @js($presentation['carrier'] ?? ''),
                                originalTracking: @js($presentation['tracking'] ?? ''),
                                editable: @js($row['can_edit'] && ($row['mode'] === 'active' || $row['is_done'])),
                                editing: @js($row['mode'] === 'active')
                            }"
                            x-on:shipment-tracking-updated.window="
                                if (Number($event.detail?.taskId) === Number({{ $row['task']->id }})) {
                                    carrier = String($event.detail?.carrier ?? carrier);
                                    tracking = String($event.detail?.tracking ?? tracking);
                                    originalCarrier = carrier;
                                    originalTracking = tracking;
                                    editing = false;
                                }
                            "
                        >
                            <label>
                                <span>COURIER</span>
                                <select x-model="carrier" x-bind:disabled="!editable || !editing">
                                    <option value="">Select courier</option>
                                    @foreach(($presentation['couriers'] ?? []) as $courier)
                                        <option value="{{ $courier['value'] }}">{{ $courier['label'] }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>TRACKING NUMBER</span>
                                <input type="text" x-model.trim="tracking" placeholder="Enter tracking number" x-bind:disabled="!editable || !editing">
                            </label>
                            <div class="ft-shipment-inline-work__buttons">
                                @if($row['mode'] === 'active')
                                    <button
                                        type="button"
                                        class="ft-shipment-btn ft-shipment-btn--primary ft-shipment-btn--continue"
                                        @disabled(!$row['can_edit'])
                                        x-bind:disabled="!editable || String(carrier || '').trim() === '' || String(tracking || '').trim() === ''"
                                        x-on:click="$wire.completeShipmentTrackingTask({{ $row['task']->id }}, carrier, tracking)"
                                        wire:loading.attr="disabled"
                                        wire:target="completeShipmentTrackingTask"
                                    >
                                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h11M11 6l4 4-4 4"/></svg>Continue to next task
                                    </button>
                                @elseif($row['is_done'] && $row['can_edit'])
                                    <button
                                        x-cloak
                                        x-show="!editing"
                                        type="button"
                                        class="ft-shipment-btn ft-shipment-btn--outline"
                                        x-on:click="editing = true"
                                    >
                                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>Edit tracking number
                                    </button>
                                    <div x-cloak x-show="editing" class="ft-shipment-inline-work__edit-actions">
                                        <button type="button" class="ft-shipment-btn ft-shipment-btn--soft" x-on:click="carrier = originalCarrier; tracking = originalTracking; editing = false">Cancel</button>
                                        <button
                                            type="button"
                                            class="ft-shipment-btn ft-shipment-btn--primary"
                                            x-bind:disabled="String(carrier || '').trim() === '' || String(tracking || '').trim() === ''"
                                            x-on:click="$wire.updateShipmentTrackingDetails({{ $row['task']->id }}, carrier, tracking)"
                                            wire:loading.attr="disabled"
                                            wire:target="updateShipmentTrackingDetails"
                                        >Save changes</button>
                                    </div>
                                @endif
                            </div>
                            @if($row['mode'] !== 'active' && !$row['is_done'])
                                <small class="ft-shipment-unlock-note"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Available after Review or update shipment details</small>
                            @elseif($row['is_done'])
                                <small class="ft-shipment-unlock-note ft-shipment-unlock-note--editable"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="M10 9v4M10 6.5h.01"/></svg>Courier and tracking can be edited without reopening this completed task.</small>
                            @endif
                            @error('shipmentLabel')<p class="validation-error">{{ $message }}</p>@enderror
                        </div>
                    @elseif($row['key'] === 'SHIP_PACKAGE')
                        <div class="ft-shipment-dispatch-work">
                            <button type="button" class="ft-shipment-btn ft-shipment-btn--primary" @disabled($row['mode'] !== 'active' || !$row['can_edit']) wire:click="dispatchShipment({{ $row['task']->id }})" wire:loading.attr="disabled" wire:target="dispatchShipment">
                                <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m6.5 10 2.2 2.2 4.8-4.8"/></svg>Mark as dispatched
                            </button>
                            @if($row['mode'] !== 'active' && !$row['is_done'])
                                <small class="ft-shipment-unlock-note"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="9" width="10" height="8" rx="1.5"/><path d="M7.5 9V6.5a2.5 2.5 0 0 1 5 0V9"/></svg>Available after Add tracking number &amp; print courier label</small>
                            @endif
                            @error('shipmentDispatch')<p class="validation-error">{{ $message }}</p>@enderror
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>
