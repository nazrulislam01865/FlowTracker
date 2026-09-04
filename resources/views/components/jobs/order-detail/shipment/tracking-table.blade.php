@props(['row', 'presentation'])

@php
    $task = $row['task'];
    $canEdit = (bool) ($row['can_edit'] ?? false);
    $actionable = $row['mode'] === 'active' || $row['is_done'];
    $couriers = $presentation['couriers'] ?? [];
@endphp

<div class="ft-ms-table-wrap ft-ms-table-wrap--tracking">
    <table class="ft-ms-table ft-ms-table--tracking">
        <thead>
            <tr>
                <th>Shipment</th>
                <th>Courier</th>
                <th>Tracking Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($presentation['shipments'] ?? []) as $shipment)
                <tr
                    wire:key="shipment-tracking-row-{{ $shipment['id'] }}"
                    x-data="{
                        editing: false,
                        courierId: @js($shipment['courier_id'] ? (string) $shipment['courier_id'] : ''),
                        originalCourierId: @js($shipment['courier_id'] ? (string) $shipment['courier_id'] : ''),
                        tracking: @js($shipment['tracking_number']),
                        originalTracking: @js($shipment['tracking_number']),
                        cancelEdit() {
                            this.courierId = this.originalCourierId;
                            this.tracking = this.originalTracking;
                            this.editing = false;
                        }
                    }"
                >
                    <td data-label="Shipment">
                        <div class="ft-ms-shipment-number"><b>{{ $shipment['sequence'] }}</b>@if($shipment['is_primary'])<span class="ft-ms-primary">Primary</span>@endif</div>
                    </td>
                    <td data-label="Courier">
                        <div class="ft-ms-courier-value" x-show="!editing">
                            <strong>{{ $shipment['courier_name'] ?: 'Not selected' }}</strong>
                        </div>
                        <x-jobs.order-detail.shipment.courier-select
                            x-cloak
                            x-show="editing"
                            x-model="courierId"
                            :couriers="$couriers"
                            aria-label="Courier for Shipment {{ $shipment['sequence'] }}"
                        />
                    </td>
                    <td data-label="Tracking Number">
                        <div class="ft-ms-tracking-value" x-show="!editing">
                            <span x-text="tracking || 'Not added'" :class="tracking ? '' : 'is-empty'">{{ $shipment['tracking_number'] ?: 'Not added' }}</span>
                        </div>
                        <input
                            x-cloak
                            x-show="editing"
                            x-model.trim="tracking"
                            class="ft-ms-tracking-input"
                            type="text"
                            maxlength="255"
                            placeholder="Enter tracking number"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:keydown.enter.prevent="if (String(courierId || '').trim() && String(tracking || '').trim()) $wire.saveOrderShipmentTracking({{ $task->id }}, {{ $shipment['id'] }}, Number(courierId), tracking).then(() => { originalCourierId = courierId; originalTracking = tracking; editing = false; })"
                        >
                    </td>
                    <td data-label="Actions">
                        @if($canEdit && $actionable)
                            <div class="ft-ms-actions" x-show="!editing">
                                <button type="button" class="ft-ms-outline-btn" x-on:click="editing = true; $nextTick(() => $el.closest('tr').querySelector('.ft-ms-courier-select')?.focus())">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m4 14.5-.5 2 2-.5L14 7.5 12.5 6 4 14.5Z"/><path d="m11.5 7 1.5-1.5a1.1 1.1 0 0 1 1.6 0l.4.4a1.1 1.1 0 0 1 0 1.6L13.5 9"/></svg>
                                    <span>{{ ($shipment['courier_id'] && $shipment['tracking_number']) ? 'Edit courier & tracking' : 'Add courier & tracking' }}</span>
                                </button>
                            </div>
                            <div class="ft-ms-actions ft-ms-actions--editing" x-cloak x-show="editing">
                                <button type="button" class="ft-ms-ghost-btn" x-on:click="cancelEdit()">Cancel</button>
                                <button
                                    type="button"
                                    class="ft-ms-primary-btn"
                                    x-bind:disabled="!String(courierId || '').trim() || !String(tracking || '').trim()"
                                    x-on:click="$wire.saveOrderShipmentTracking({{ $task->id }}, {{ $shipment['id'] }}, Number(courierId), tracking).then(() => { originalCourierId = courierId; originalTracking = tracking; editing = false; })"
                                >Save</button>
                            </div>
                        @else
                            <span class="ft-ms-readonly-dash">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="ft-ms-empty">No shipments are available.</td></tr>
            @endforelse
        </tbody>
    </table>
    @error('shipmentTracking')<p class="validation-error ft-ms-error">{{ $message }}</p>@enderror
</div>
