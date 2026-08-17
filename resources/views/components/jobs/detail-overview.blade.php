@props(['job','expandedPhaseIds'=>[],'taskStatuses'=>collect(),'users'=>collect(),'mentionUsers'=>collect(),'priorities'=>collect(),'products'=>collect(),'categories'=>collect(),'showAddJobProductForm'=>false,'jobProductSearch'=>'','jobProductSearchResults'=>collect(),'jobProductResultTotal'=>0,'jobProductSelectedProduct'=>null,'jobProductCategory'=>'','jobTaskSearch'=>'','activityTab'=>'all','activityPage'=>1,'focusComment'=>null,'jobDocumentUploads'=>[],'overviewTaskDocumentModalTask'=>null,'overviewTaskAvailableDocuments'=>collect(),'showOverviewTaskDocumentModal'=>false,'overviewTaskDocumentSource'=>'upload','overviewTaskDocumentUpload'=>null,'overviewTaskExistingDocumentId'=>null,'overviewTaskLinkFormTaskId'=>null,'showAddOrderTaskForm'=>false,'newOrderTaskAssigneeId'=>null])
@php
    $productRows = \App\Support\JobDetailPresenter::products($job);
    $completedProductRows = $productRows->filter(fn ($item) => filled($item->product_name ?? null));
    $nextTask = \App\Support\JobDetailPresenter::nextTask($job);
    $currentTasks = \App\Support\JobDetailPresenter::phaseTasks($job);
    $done = \App\Support\JobDetailPresenter::completedCount($currentTasks);
    $accessControl = app(\App\Services\AccessControlService::class);
    $canEditJob = $accessControl->canEditVisibleJob(auth()->user(), $job);
    $canViewOrderProducts = $accessControl->can(auth()->user(), 'catalog_products', 'view');
    $canEditOrderProducts = $canEditJob && $canViewOrderProducts && $accessControl->can(auth()->user(), 'catalog_products', 'edit');
    $canCreateOrderProducts = $canEditJob && $canViewOrderProducts && $accessControl->can(auth()->user(), 'catalog_products', 'create');
    $canDeleteOrderProducts = $canEditJob && $canViewOrderProducts && $accessControl->can(auth()->user(), 'catalog_products', 'delete');
    $canChangeJobOwner = $accessControl->isAdministrator(auth()->user());
    $canAddOrderTask = $accessControl->canCreateJobTask(auth()->user(), $job)
        && !$job->completed_at
        && $job->status !== 'Completed'
        && !in_array($job->status, \App\Services\JobService::INACTIVE_STATUSES, true);
    $canDeleteDocument = $accessControl->can(auth()->user(), 'documents', 'delete');
    $canUploadDocument = $accessControl->can(auth()->user(), 'documents', 'create');
    $canLinkDocument = $accessControl->can(auth()->user(), 'documents', 'link');
    $requiredDocuments = \App\Support\JobDetailPresenter::requiredDocuments($job);
    $configuredTasks = $job->workflow->phases->flatMap(fn($phase) => \App\Support\JobDetailPresenter::phaseTasks($job,$phase))->values();
    $masterData = app(\App\Services\MasterDataService::class);
    // Urgency is a single-choice field. Legacy rows may still contain more than
    // one id from the old checkbox UI, so the overview intentionally uses the
    // first stored value and every edit writes back at most one id.
    $productionUrgencyId = collect($job->production_urgency_ids ?? [])->map(fn ($id) => (int) $id)->first(fn ($id) => $id > 0);
    $shipmentUrgencyId = collect($job->shipment_urgency_ids ?? [])->map(fn ($id) => (int) $id)->first(fn ($id) => $id > 0);
    $productionUrgencyName = $productionUrgencyId
        ? (string) ($masterData->query('production_urgency')->whereKey($productionUrgencyId)->value('name') ?? '')
        : '';
    $shipmentUrgencyName = $shipmentUrgencyId
        ? (string) ($masterData->query('shipment_urgency')->whereKey($shipmentUrgencyId)->value('name') ?? '')
        : '';
    $productionUrgencyOptions = $masterData->active('production_urgency')
        ->map(fn ($urgency) => ['id' => (int) $urgency->id, 'name' => (string) $urgency->name])
        ->values();
    $shipmentUrgencyOptions = $masterData->active('shipment_urgency')
        ->map(fn ($urgency) => ['id' => (int) $urgency->id, 'name' => (string) $urgency->name])
        ->values();
    $orderProductNames = $completedProductRows->pluck('product_name')->filter()->unique()->values();
    $orderProductMasters = $orderProductNames->isEmpty()
        ? collect()
        : \App\Models\MasterRecord::query()
            ->where('workspace_id', max(1, (int) config('flowtrack.workspace_id', 1)))
            ->where('type', 'product')
            ->whereIn('name', $orderProductNames)
            ->with('parent')
            ->get()
            ->keyBy(fn ($record) => mb_strtolower(trim((string) $record->name)));
    $shippingAddressValue = trim((string) ($job->shipping_address ?? ''));
    $shippingPhoneCodeValue = trim((string) ($job->shipping_phone_country_code ?? ''));
    $shippingPhoneValue = trim((string) ($job->shipping_phone ?? ''));
    $shippingPhoneDisplay = collect([$shippingPhoneCodeValue, $shippingPhoneValue])->filter()->implode(' ');
    $shippingPostalValue = trim((string) ($job->shipping_postal_code ?? ''));
    $orderCurrency = strtoupper((string) ($job->currency ?: 'USD'));
    $orderCurrencySymbol = match ($orderCurrency) {
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'CNY', 'RMB' => '¥',
        default => $orderCurrency.' ',
    };
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
                    <div class="ft-rich-text-content ft-editable-rich-display">
                        <div x-show="!hasRichTextOverride">@if($job->description)<x-ui.mention-text :text="$job->description" />@else No order description recorded. @endif</div>
                        <div x-cloak x-show="hasRichTextOverride" x-html="richTextOverrideHtml"></div>
                    </div>
                    @if($canEditJob)
                        <button type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit order description" title="Edit" x-on:click.stop="beginRichTextEdit($refs.descriptionEditor)">✎</button>
                    @endif
                </div>
                @if($canEditJob)
                    <div x-cloak x-show="editing" class="ft-inline-description-editor">
                        <textarea x-ref="descriptionEditor" rows="3" class="ft-mention-input" data-rich-text autocomplete="off" data-mention-users="{{ $mentionUsers->toJson() }}">{{ $job->description ?? '' }}</textarea>
                        <div class="ft-inline-description-actions">
                            <button type="button" class="ft-outline-btn" x-on:click="cancelRichTextEdit($refs.descriptionEditor)">Cancel</button>
                            <button type="button" class="ft-new-job-btn" data-rich-text-submit :disabled="status === 'saving'" x-on:click="saveRichText($refs.descriptionEditor, 'No order description recorded.', (clean) => $wire.updateJobTextField({{ $job->id }}, 'description', clean))">Save</button>
                        </div>
                    </div>
                    <x-ui.inline-save-state />
                @endif
            </div>
            @if(filled($job->notes))
                <div class="ft-order-overview-notes">
                    <small>Notes</small>
                    <div><x-ui.mention-text :text="$job->notes" /></div>
                </div>
            @endif
        </section>

        <div class="ft-overview-side-stack">
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
            <div class="ft-side-row"><span>Reference number</span><b>{{ $job->order_number ?: 'Not set' }}</b></div>
            @if($job->is_repeat_order)
                <div class="ft-side-row"><span>Previous reference</span><b>{{ $job->repeat_order_number ?: 'Not set' }}</b></div>
            @endif
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-urgency-row ft-inline-edit-shell"
                x-data="{
                    ...window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-production-urgency'), label: 'production urgency', value: @js($productionUrgencyId ? (string) $productionUrgencyId : ''), display: @js($productionUrgencyName ?: 'None') }),
                    options: @js($productionUrgencyOptions),
                    selectedId: @js($productionUrgencyId ? (string) $productionUrgencyId : ''),
                    savedId: @js($productionUrgencyId ? (string) $productionUrgencyId : ''),
                    openUrgency() {
                        if (!this.beginEdit()) return;
                        this.selectedId = this.savedId;
                        this.$nextTick(() => this.$refs.urgencySelect?.focus());
                    },
                    cancelUrgency() { this.selectedId = this.savedId; this.cancelEdit(); },
                    async saveUrgency() {
                        const id = Number(this.selectedId || 0);
                        const ids = id > 0 ? [id] : [];
                        const label = this.options.find(option => Number(option.id) === id)?.name || 'None';
                        const ok = await this.commit(id > 0 ? String(id) : '', label, () => $wire.updateJobUrgencies({{ $job->id }}, 'production', ids));
                        if (ok) this.savedId = id > 0 ? String(id) : '';
                        else this.selectedId = this.savedId;
                    }
                }"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                x-on:click.outside="if (editing) cancelUrgency()"
            >
                <span>Production urgency</span>
                <b class="ft-planning-value ft-urgency-value">
                    <span x-show="!editing" class="ft-planning-urgency-display">
                        <span x-show="display === 'None'" class="ft-planning-empty">None</span>
                        <span x-show="display !== 'None'" class="ft-soft-pill amber" x-text="display">{{ $productionUrgencyName ?: 'None' }}</span>
                    </span>
                    @if($canEditJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit production urgency" title="Edit" x-on:click.stop="openUrgency()">✎</button>
                        <div x-cloak x-show="editing" class="ft-inline-urgency-editor">
                            <select
                                x-ref="urgencySelect"
                                x-model="selectedId"
                                class="ft-inline-urgency-select"
                                aria-label="Select production urgency"
                                x-on:keydown.escape.prevent.stop="cancelUrgency()"
                            >
                                <option value="">None</option>
                                <template x-for="option in options" :key="option.id">
                                    <option :value="String(option.id)" x-text="option.name"></option>
                                </template>
                            </select>
                            <span x-show="options.length === 0" class="ft-planning-empty ft-inline-urgency-empty">No active urgency options</span>
                            <div class="ft-inline-urgency-actions">
                                <button type="button" class="ft-inline-urgency-cancel" x-on:click.stop="cancelUrgency()">Cancel</button>
                                <button type="button" class="ft-inline-urgency-save" :disabled="status === 'saving'" x-on:click.stop="saveUrgency()">Save</button>
                            </div>
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-inline-urgency-row ft-inline-edit-shell"
                x-data="{
                    ...window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-shipment-urgency'), label: 'shipment urgency', value: @js($shipmentUrgencyId ? (string) $shipmentUrgencyId : ''), display: @js($shipmentUrgencyName ?: 'None') }),
                    options: @js($shipmentUrgencyOptions),
                    selectedId: @js($shipmentUrgencyId ? (string) $shipmentUrgencyId : ''),
                    savedId: @js($shipmentUrgencyId ? (string) $shipmentUrgencyId : ''),
                    openUrgency() {
                        if (!this.beginEdit()) return;
                        this.selectedId = this.savedId;
                        this.$nextTick(() => this.$refs.urgencySelect?.focus());
                    },
                    cancelUrgency() { this.selectedId = this.savedId; this.cancelEdit(); },
                    async saveUrgency() {
                        const id = Number(this.selectedId || 0);
                        const ids = id > 0 ? [id] : [];
                        const label = this.options.find(option => Number(option.id) === id)?.name || 'None';
                        const ok = await this.commit(id > 0 ? String(id) : '', label, () => $wire.updateJobUrgencies({{ $job->id }}, 'shipment', ids));
                        if (ok) this.savedId = id > 0 ? String(id) : '';
                        else this.selectedId = this.savedId;
                    }
                }"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                x-on:click.outside="if (editing) cancelUrgency()"
            >
                <span>Shipment urgency</span>
                <b class="ft-planning-value ft-urgency-value">
                    <span x-show="!editing" class="ft-planning-urgency-display">
                        <span x-show="display === 'None'" class="ft-planning-empty">None</span>
                        <span x-show="display !== 'None'" class="ft-soft-pill blue" x-text="display">{{ $shipmentUrgencyName ?: 'None' }}</span>
                    </span>
                    @if($canEditJob)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit shipment urgency" title="Edit" x-on:click.stop="openUrgency()">✎</button>
                        <div x-cloak x-show="editing" class="ft-inline-urgency-editor">
                            <select
                                x-ref="urgencySelect"
                                x-model="selectedId"
                                class="ft-inline-urgency-select"
                                aria-label="Select shipment urgency"
                                x-on:keydown.escape.prevent.stop="cancelUrgency()"
                            >
                                <option value="">None</option>
                                <template x-for="option in options" :key="option.id">
                                    <option :value="String(option.id)" x-text="option.name"></option>
                                </template>
                            </select>
                            <span x-show="options.length === 0" class="ft-planning-empty ft-inline-urgency-empty">No active urgency options</span>
                            <div class="ft-inline-urgency-actions">
                                <button type="button" class="ft-inline-urgency-cancel" x-on:click.stop="cancelUrgency()">Cancel</button>
                                <button type="button" class="ft-inline-urgency-save" :disabled="status === 'saving'" x-on:click.stop="saveUrgency()">Save</button>
                            </div>
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div
                class="ft-side-row ft-inline-planning-row ft-planning-owner-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-owner'), label: 'Order owner', value: @js($job->owner_id ?? ''), display: @js($job->owner?->name ?? 'Unassigned'), avatarUrl: @js($job->owner?->profileImageUrl() ?? '') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                x-on:click.outside="if (editing) cancelEdit()"
                x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateJobOwner({{ $job->id }}, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
            >
                <span>Order owner</span>
                <b class="ft-planning-value">
                    <span x-show="!editing" class="ft-inline-person-live ft-planning-person-value">
                        <x-ui.inline-live-avatar :size="24" />
                        <span x-text="display">{{ $job->owner?->name ?? 'Unassigned' }}</span>
                    </span>
                    @if($canChangeJobOwner)
                        <button x-show="!editing" :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" aria-label="Edit job owner" title="Edit" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                        <div x-cloak x-show="editing" class="ft-task-inline-assignee-picker">
                            <x-ui.inline-remote-user
                                :value="$job->owner_id ?? ''"
                                :selected-label="$job->owner?->name ?? 'Unassigned'"
                                context="job-owner"
                                parent-type="job"
                                :parent-id="$job->id"
                                search-placeholder="Search owner…"
                                trigger-class="ft-planning-inline-select"
                                variant="compact"
                                :menu-width="300"
                            />
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
            <div class="ft-side-row"><span>Workflow</span><b>▣ {{ $job->workflow?->name }}</b></div>
            <div class="ft-side-row"><span>Created</span><b>{{ \App\Support\UserLocalTime::format($job->created_at, 'M j, Y, g:i A') }}</b></div>
        </aside>

        <aside class="ft-detail-card ft-side-panel ft-planning-panel ft-order-shipping-side-panel" aria-labelledby="ft-order-shipping-detail-title">
            <h2 id="ft-order-shipping-detail-title">Shipping address</h2>

            <div
                class="ft-side-row ft-inline-planning-row ft-order-shipping-side-row ft-order-shipping-address-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-shipping-address'), label: 'shipping address', value: @js($shippingAddressValue), display: @js($shippingAddressValue ?: 'Not set') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Delivery address</span>
                <b class="ft-planning-value ft-order-shipping-side-value">
                    <span x-show="!editing" class="ft-order-shipping-side-copy" :class="{ 'is-empty': !value }" x-text="display">{{ $shippingAddressValue ?: 'Not set' }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit shipping address" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.shippingAddress.focus(); $refs.shippingAddress.setSelectionRange($refs.shippingAddress.value.length, $refs.shippingAddress.value.length); })">✎</button>
                        <div x-cloak x-show="editing" class="ft-order-shipping-inline-editor ft-order-shipping-address-editor">
                            <textarea x-ref="shippingAddress" x-model="draftValue" rows="4" maxlength="2000" placeholder="Recipient name&#10;Street address&#10;City, State, Country" x-on:keydown.escape.prevent="cancelEdit()"></textarea>
                            <div class="ft-order-shipping-inline-actions">
                                <button type="button" class="ft-order-shipping-inline-cancel" x-on:click.stop="cancelEdit()">Cancel</button>
                                <button type="button" class="ft-order-shipping-inline-save" :disabled="status === 'saving'" x-on:click.stop="const next = String(draftValue || '').trim(); commit(next, next || 'Not set', () => $wire.updateJobShippingField({{ $job->id }}, 'shipping_address', next)).then((ok) => { if (!ok) editing = true; })">Save</button>
                            </div>
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>

            <div
                class="ft-side-row ft-inline-planning-row ft-order-shipping-side-row ft-order-shipping-phone-row ft-inline-edit-shell"
                x-data="{
                    ...window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-shipping-phone'), label: 'shipping phone number', value: @js($shippingPhoneCodeValue.'|'.$shippingPhoneValue), display: @js($shippingPhoneDisplay ?: 'Not set') }),
                    codeDraft: @js($shippingPhoneCodeValue),
                    phoneDraft: @js($shippingPhoneValue),
                    savedCode: @js($shippingPhoneCodeValue),
                    savedPhone: @js($shippingPhoneValue),
                    syncPhonePicker() {
                        const picker = this.$el.querySelector('[data-ft-inline-remote-picker]');
                        picker?.dispatchEvent(new CustomEvent('ft-inline-remote-sync', { detail: { value: this.codeDraft, label: this.codeDraft || 'Code' } }));
                    },
                    openPhoneEditor() {
                        if (!this.beginEdit()) return;
                        this.codeDraft = this.savedCode;
                        this.phoneDraft = this.savedPhone;
                        this.$nextTick(() => { this.syncPhonePicker(); this.$refs.shippingPhone?.focus(); });
                    },
                    cancelPhoneEditor() {
                        this.codeDraft = this.savedCode;
                        this.phoneDraft = this.savedPhone;
                        this.syncPhonePicker();
                        this.cancelEdit();
                    },
                    async savePhoneEditor() {
                        const code = String(this.codeDraft || '').trim();
                        const phone = String(this.phoneDraft || '').trim();
                        const composite = code + '|' + phone;
                        const label = [code, phone].filter(Boolean).join(' ') || 'Not set';
                        const ok = await this.commit(composite, label, () => $wire.updateJobShippingPhone({{ $job->id }}, code, phone));
                        if (ok) {
                            this.savedCode = code;
                            this.savedPhone = phone;
                        } else {
                            this.codeDraft = this.savedCode;
                            this.phoneDraft = this.savedPhone;
                            this.editing = true;
                            this.$nextTick(() => this.syncPhonePicker());
                        }
                    }
                }"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error', 'is-editing': editing }"
                x-on:ft-inline-remote-selected.stop="codeDraft = String($event.detail?.value ?? '')"
            >
                <span>Phone number</span>
                <b class="ft-planning-value ft-order-shipping-side-value">
                    <span x-show="!editing" class="ft-order-shipping-side-copy" :class="{ 'is-empty': !savedCode && !savedPhone }" x-text="display">{{ $shippingPhoneDisplay ?: 'Not set' }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit shipping phone number" title="Edit" x-on:click.stop="openPhoneEditor()">✎</button>
                        <div x-cloak x-show="editing" class="ft-order-shipping-inline-editor ft-order-shipping-phone-editor">
                            <div class="ft-order-shipping-phone-editor-row">
                                <x-ui.inline-remote-catalog
                                    type="phone-country-codes"
                                    :value="$shippingPhoneCodeValue"
                                    :selected-label="$shippingPhoneCodeValue ?: 'Code'"
                                    placeholder="Code"
                                    search-label="phone country code"
                                    trigger-class="ft-order-shipping-phone-code-trigger"
                                    :menu-width="300"
                                    :fixed-menu="true"
                                    :clearable="true"
                                />
                                <input x-ref="shippingPhone" x-model="phoneDraft" type="tel" inputmode="tel" maxlength="60" autocomplete="tel" placeholder="Enter phone number" x-on:keydown.escape.prevent="cancelPhoneEditor()" x-on:keydown.enter.prevent="savePhoneEditor()">
                            </div>
                            <div class="ft-order-shipping-inline-actions">
                                <button type="button" class="ft-order-shipping-inline-cancel" x-on:click.stop="cancelPhoneEditor()">Cancel</button>
                                <button type="button" class="ft-order-shipping-inline-save" :disabled="status === 'saving'" x-on:click.stop="savePhoneEditor()">Save</button>
                            </div>
                        </div>
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>

            <div
                class="ft-side-row ft-inline-planning-row ft-order-shipping-side-row ft-inline-edit-shell"
                x-data="window.FlowTrackInlineEdit({ key: @js('job-'.$job->id.'-shipping-postal'), label: 'shipping postal code', value: @js($shippingPostalValue), display: @js($shippingPostalValue ?: 'Not set') })"
                :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
            >
                <span>Postal code</span>
                <b class="ft-planning-value ft-order-shipping-side-value">
                    <span x-show="!editing" class="ft-order-shipping-side-copy" :class="{ 'is-empty': !value }" x-text="display">{{ $shippingPostalValue ?: 'Not set' }}</span>
                    @if($canEditJob)
                        <button x-show="!editing" type="button" :disabled="status === 'saving'" class="ft-inline-edit-button" aria-label="Edit shipping postal code" title="Edit" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.shippingPostal.focus(); $refs.shippingPostal.select(); })">✎</button>
                        <input
                            x-ref="shippingPostal"
                            x-cloak
                            x-show="editing"
                            x-model="draftValue"
                            class="ft-order-shipping-inline-input"
                            type="text"
                            maxlength="30"
                            autocomplete="postal-code"
                            placeholder="Enter postal code"
                            x-on:keydown.escape.prevent="cancelEdit()"
                            x-on:keydown.enter.prevent="$event.target.blur()"
                            x-on:blur="if (editing) { const next = String(draftValue || '').trim(); commit(next, next || 'Not set', () => $wire.updateJobShippingField({{ $job->id }}, 'shipping_postal_code', next)).then((ok) => { if (!ok) editing = true; }) }"
                        >
                        <x-ui.inline-save-state compact />
                    @endif
                </b>
            </div>
        </aside>
        </div>
    </div>

    @if($canViewOrderProducts)
        <x-catalog.detail-products-card
            id="order-products-card"
            variant="order"
            :count="$completedProductRows->count()"
            :total-units="$completedProductRows->sum('quantity')"
        >
                    @if($productRows->isEmpty())
                        <tr class="ft-order-product-empty-row"><td colspan="7">No products have been added to this Order yet.</td></tr>
                    @else
                    @foreach($productRows as $item)
                        @php
                            $isDraftItem = filled($item->id) && blank($item->product_name);
                            $categoryNeedsSelection = filled($item->id) && blank($item->category_name);
                            $productNeedsSelection = filled($item->id) && filled($item->category_name) && blank($item->product_name);
                            $categoryLabel = $item->category_name ?: 'Select category';
                            $productLabel = $item->product_name ?: (blank($item->category_name) ? 'Select category first' : 'Select product');
                            $productPickerKey = 'job-item-'.$item->id.'-product-'.md5((string) ($item->category_name ?? '').'|'.(string) ($item->product_name ?? ''));
                            $productMaster = $orderProductMasters->get(mb_strtolower(trim((string) ($item->product_name ?? ''))));
                            $productImageUrl = $productMaster?->productImageUrl();
                            $productCode = $productMaster?->productDisplayCode();
                            $productReference = $productMaster?->productReferenceCode();
                            $classificationParts = collect([
                                $productMaster?->productMainCategory(),
                                ...array_filter(array_map('trim', preg_split('/\s*>\s*/', (string) ($productMaster?->productClassificationPath() ?? '')) ?: [])),
                            ])->filter()->unique()->values();
                            if ($classificationParts->isEmpty() && filled($item->category_name)) $classificationParts = collect([$item->category_name]);
                            $categoryDisplay = $classificationParts->implode(' › ') ?: $categoryLabel;
                            $updatedByName = $item->updatedBy?->name ?: $job->creator?->name ?: 'FlowTrack';
                            $updatedWhen = $item->updated_at?->diffForHumans() ?: 'just now';
                            $unitPrice = (float) ($item->unit_price ?? 0);
                            $unitPriceDisplay = $orderCurrencySymbol.number_format($unitPrice, 2);
                        @endphp
                        <tr wire:key="job-product-detail-{{ $item->id ?? $loop->index }}"
                            x-data="{ categorySaving: false, productSaving: false, quantitySaving: false, priceSaving: false, notesSaving: false, actionOpen: false, draftProductReady: @js(filled($item->product_name)) }"
                            @class(['ft-order-product-draft-row' => $isDraftItem])>
                            <td data-label="Product">
                                <x-catalog.detail-product-identity
                                    :image-url="$productImageUrl"
                                    :alt="$item->product_name ?? ''"
                                    :code="$productCode"
                                    :reference="$productReference"
                                    fallback-meta="Order product"
                                >
                                    @if($item->id)
                                        <div
                                            class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor ft-order-product-name-editor"
                                            wire:key="{{ $productPickerKey }}"
                                            x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-product'), label: 'product', value: @js($item->product_name ?? ''), display: @js($productLabel) })"
                                            x-init="if (@js($canEditOrderProducts && $productNeedsSelection)) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                            :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                            x-on:click.outside="if (editing && !@js($productNeedsSelection)) cancelEdit()"
                                            x-on:ft-inline-remote-cancel.stop="if (!@js($productNeedsSelection)) cancelEdit()"
                                            x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select product'); productSaving = true; commit(nextValue, nextLabel, () => $wire.updateJobItem({{ $item->id }}, 'product_name', nextValue)).then(async (ok) => { productSaving = false; if (ok) { draftProductReady = true; await $wire.$refresh(); } })"
                                        >
                                            <span class="ft-order-product-name" x-show="!editing" x-text="display">{{ $productLabel }}</span>
                                            @if($canEditOrderProducts)
                                                <button x-show="!editing" :disabled="status === 'saving' || categorySaving || quantitySaving || priceSaving || notesSaving || @js(blank($item->category_name))" type="button" class="ft-inline-edit-button" aria-label="Edit product" title="{{ blank($item->category_name) ? 'Select a category first' : 'Edit product' }}" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                                <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                                    <x-ui.inline-remote-catalog
                                                        type="products"
                                                        :value="$item->product_name ?? ''"
                                                        :selected-label="$productLabel"
                                                        :placeholder="blank($item->category_name) ? 'Select category first' : 'Select product'"
                                                        search-label="product"
                                                        :params="['category' => (string) ($item->category_name ?? '')]"
                                                        :disabled="blank($item->category_name)"
                                                        :menu-width="360"
                                                        :fixed-menu="true"
                                                    />
                                                </div>
                                                <x-ui.inline-save-state compact />
                                            @endif
                                        </div>
                                    @else
                                        <strong class="ft-order-product-name">{{ $item->product_name }}</strong>
                                    @endif
                                </x-catalog.detail-product-identity>
                            </td>
                            <td data-label="Category">
                                @if($item->id)
                                    <div
                                        class="ft-inline-field-editor ft-inline-edit-shell ft-inline-catalog-editor ft-order-product-category-editor"
                                        wire:key="job-item-{{ $item->id }}-category-{{ md5((string) ($item->category_name ?? '')) }}"
                                        x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-category'), label: 'product category', value: @js($item->category_name ?? ''), display: @js($categoryDisplay) })"
                                        x-init="if (@js($canEditOrderProducts && $categoryNeedsSelection)) { editing = true; $nextTick(() => setTimeout(() => { const picker = $el.querySelector('[data-ft-inline-remote-picker]'); picker?.dispatchEvent(new CustomEvent('ft-inline-remote-open', { detail: { value: value, label: display } })) }, 0)) }"
                                        :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                        x-on:click.outside="if (editing && !@js($categoryNeedsSelection)) cancelEdit()"
                                        x-on:ft-inline-remote-cancel.stop="if (!@js($categoryNeedsSelection)) cancelEdit()"
                                        x-on:ft-inline-remote-selected.stop="const nextValue = String($event.detail?.value ?? ''); const nextLabel = String($event.detail?.label ?? 'Select category'); const changed = nextValue !== savedValue; categorySaving = true; commit(nextValue, nextLabel, () => $wire.updateJobItem({{ $item->id }}, 'category_name', nextValue)).then(async (ok) => { if (ok && changed) await $wire.$refresh(); categorySaving = false })"
                                    >
                                        <span class="ft-order-product-category-path" x-show="!editing" x-text="display">{{ $categoryDisplay }}</span>
                                        @if($canEditOrderProducts)
                                            <button x-show="!editing" :disabled="status === 'saving' || productSaving || quantitySaving || priceSaving || notesSaving" type="button" class="ft-inline-edit-button" aria-label="Edit product category" title="Edit category" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                            <div x-cloak x-show="editing" class="ft-inline-catalog-picker">
                                                <x-ui.inline-remote-catalog type="product-categories" :value="$item->category_name ?? ''" :selected-label="$categoryLabel" placeholder="Select category" search-label="product category" :menu-width="340" :fixed-menu="true" />
                                            </div>
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>
                                @else
                                    {{ $categoryDisplay }}
                                @endif
                            </td>
                            <td class="ft-order-product-quantity" data-label="Quantity">
                                @if($item->id)
                                    <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-quantity'), label: 'quantity', value: @js((string) $item->quantity), display: @js(number_format((int) $item->quantity).' units') })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                        <span x-show="!editing" class="ft-order-product-edit-value" x-text="display">{{ number_format((int) $item->quantity) }} units</span>
                                        @if($canEditOrderProducts)
                                            <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || priceSaving || notesSaving" type="button" class="ft-inline-edit-button" title="Edit quantity" aria-label="Edit product quantity" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.quantityInput.focus(); $refs.quantityInput.select(); })">✎</button>
                                            <input x-ref="quantityInput" x-cloak x-show="editing" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-number-input" type="number" min="1"
                                                x-on:keydown.escape.prevent="cancelEdit()"
                                                x-on:keydown.enter.prevent="$event.target.blur()"
                                                x-on:blur="if (editing && !quantitySaving) { const next = positiveInteger(draftValue); quantitySaving = true; commit(next, Number(next).toLocaleString() + ' units', () => $wire.updateJobItem({{ $item->id }}, 'quantity', next)).then((ok) => { quantitySaving = false; if (!ok) editing = true; }) }">
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>
                                @else
                                    {{ number_format((int) $item->quantity) }} units
                                @endif
                            </td>
                            <td class="ft-order-product-price" data-label="Unit price">
                                @if($item->id)
                                    <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-unit-price'), label: 'unit price', value: @js(number_format($unitPrice, 2, '.', '')), display: @js($unitPriceDisplay) })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                        <span x-show="!editing" class="ft-order-product-edit-value" x-text="display">{{ $unitPriceDisplay }}</span>
                                        @if($canEditOrderProducts)
                                            <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || quantitySaving || notesSaving" type="button" class="ft-inline-edit-button" title="Edit unit price" aria-label="Edit unit price" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.priceInput.focus(); $refs.priceInput.select(); })">✎</button>
                                            <div x-cloak x-show="editing" class="ft-order-product-price-input-wrap">
                                                <span>{{ $orderCurrencySymbol }}</span>
                                                <input x-ref="priceInput" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-number-input" type="number" min="0" step="0.01"
                                                    x-on:keydown.escape.prevent="cancelEdit()"
                                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                                    x-on:blur="if (editing && !priceSaving) { const raw = Number(draftValue || 0); const next = Number.isFinite(raw) ? Math.max(0, raw).toFixed(2) : '0.00'; priceSaving = true; commit(next, @js($orderCurrencySymbol) + Number(next).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}), () => $wire.updateJobItem({{ $item->id }}, 'unit_price', next)).then((ok) => { priceSaving = false; if (!ok) editing = true; }) }">
                                            </div>
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>
                                @else
                                    {{ $unitPriceDisplay }}
                                @endif
                            </td>
                            <td class="ft-order-product-notes" data-label="Notes">
                                @if($item->id)
                                    <div class="ft-inline-field-editor ft-inline-edit-shell" x-data="window.FlowTrackInlineEdit({ key: @js('job-item-'.$item->id.'-notes'), label: 'product notes', value: @js($item->notes ?? ''), display: @js($item->notes ?: 'Add notes') })" :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }">
                                        <span x-show="!editing" class="ft-order-product-note-value" :class="{ 'is-empty': !value }" x-text="display">{{ $item->notes ?: 'Add notes' }}</span>
                                        @if($canEditOrderProducts)
                                            <button x-show="!editing" :disabled="status === 'saving' || categorySaving || productSaving || quantitySaving || priceSaving" type="button" class="ft-inline-edit-button" title="Edit notes" aria-label="Edit product notes" x-on:click.stop="if (beginEdit()) $nextTick(() => { $refs.notesInput.focus(); $refs.notesInput.select(); })">✎</button>
                                            <input x-ref="notesInput" x-cloak x-show="editing" x-model="draftValue" class="ft-order-product-inline-input ft-order-product-notes-input" type="text" maxlength="2000" placeholder="Product notes"
                                                x-on:keydown.escape.prevent="cancelEdit()"
                                                x-on:keydown.enter.prevent="$event.target.blur()"
                                                x-on:blur="if (editing && !notesSaving) { const next = String(draftValue || '').trim(); notesSaving = true; commit(next, next || 'Add notes', () => $wire.updateJobItem({{ $item->id }}, 'notes', next)).then((ok) => { notesSaving = false; if (!ok) editing = true; }) }">
                                            <x-ui.inline-save-state compact />
                                        @endif
                                    </div>
                                @else
                                    {{ $item->notes ?: '—' }}
                                @endif
                            </td>
                            <x-catalog.detail-product-updated
                                :primary="$updatedByName"
                                :secondary="$updatedWhen"
                            />
                            <td class="ft-order-product-actions-cell" data-label="Actions">
                                <x-catalog.detail-product-actions
                                    :item-id="$item->id"
                                    :can-delete="$canDeleteOrderProducts"
                                    remove-method="removeJobItem"
                                    confirm-text="Remove this product from the Order?"
                                />
                            </td>
                        </tr>
                    @endforeach
                    @endif

            <x-slot:afterTable>
                @if($showAddJobProductForm && $canCreateOrderProducts)
                    <x-catalog.detail-add-product
                        :wire-key="'job-detail-add-product-'.$job->id"
                        search-model="jobProductSearch"
                        :search-value="$jobProductSearch"
                        :search-results="$jobProductSearchResults"
                        :result-total="$jobProductResultTotal"
                        show-all-method="showAllJobProductResults"
                        select-method="selectJobProduct"
                        :selected-product="$jobProductSelectedProduct"
                        :category-value="$jobProductCategory"
                        quantity-model="jobProductQuantity"
                        unit-price-model="jobProductUnitPrice"
                        :currency-symbol="$orderCurrencySymbol"
                        close-method="closeAddJobProductForm"
                        :save-method="'saveJobProduct('.$job->id.')'"
                        selected-error-key="jobProductSelectedId"
                        quantity-error-key="jobProductQuantity"
                        unit-price-error-key="jobProductUnitPrice"
                    />
                @endif
            </x-slot:afterTable>

            <x-slot:footer>
                <span>Product and quantity changes are recorded in order activity.</span>
                @if($canCreateOrderProducts && !$showAddJobProductForm)
                    <button type="button" class="ft-outline-btn ft-order-product-add-another" wire:click="openAddJobProductForm({{ $job->id }})" wire:loading.attr="disabled" wire:target="openAddJobProductForm({{ $job->id }})">＋ Add another product</button>
                @endif
            </x-slot:footer>
        </x-catalog.detail-products-card>
    @endif

    <section class="ft-workflow-mini-line ft-overview-workflow-line">
        @foreach($job->workflow->phases as $phase)
            <button type="button" class="{{ $phase->sequence < $job->phase->sequence ? 'done' : ($phase->id === $job->phase->id ? 'current' : '') }}" disabled aria-disabled="true" title="Workflow page is temporarily disabled">
                <span>{{ $phase->sequence < $job->phase->sequence ? '✓' : $phase->sequence }}</span><small>{{ $phase->short_name }}</small>
            </button>
        @endforeach
    </section>

    <section class="ft-detail-card ft-phase-table-card ft-overview-task-card" id="order-taskflow">
        <div class="ft-card-row-head ft-task-card-heading">
            <div><h2>All phase tasks</h2><p>{{ $configuredTasks->count() }} tasks across {{ $job->workflow->phases->count() }} phases</p></div>
            <div class="ft-row-actions ft-order-taskflow-controls" aria-label="Order taskflow controls">
                <span class="ft-order-task-count-pill">{{ $configuredTasks->count() }} Tasks</span>
                <span class="ft-order-taskflow-badge">Taskflow</span>
                @if($canAddOrderTask)
                    <button type="button" class="ft-order-add-task-button" wire:click="openAddOrderTaskForm">＋ Add Task</button>
                @endif
                <div class="ft-phase-toolbar-icons" aria-label="Phase task controls">
                    <button type="button" class="ft-phase-toolbar-icon" wire:click="expandAllJobPhases" title="Expand all phases" aria-label="Expand all phases">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 7 6 6 6-6"/><path d="m6 12 6 6 6-6"/></svg>
                    </button>
                    <button type="button" class="ft-phase-toolbar-icon" wire:click="collapseAllJobPhases" title="Collapse all phases" aria-label="Collapse all phases">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 12 6-6 6 6"/><path d="m6 17 6-6 6 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="ft-phase-load-note"><span>◉ All {{ $configuredTasks->count() }} configured and added tasks are loaded</span><span>Task status changes save automatically</span></div>
        @if($showAddOrderTaskForm && $canAddOrderTask)
            <div class="ft-order-add-task" wire:key="order-add-task-form" x-data>
                <div class="ft-order-add-task-head">
                    <div>
                        <strong>Add taskflow task</strong>
                        <span>Select the workflow phase where this task should be added.</span>
                    </div>
                    <button class="ft-order-add-task-close" type="button" wire:click="cancelAddOrderTask" aria-label="Close add task form">×</button>
                </div>
                <div class="ft-order-add-task-grid">
                    <label class="ft-order-add-task-field ft-order-add-task-field-wide">
                        <span>Task name *</span>
                        <input type="text" wire:model="newOrderTaskName" placeholder="Task name" maxlength="255">
                    </label>
                    <label class="ft-order-add-task-field ft-order-add-task-phase">
                        <span>Phase *</span>
                        <select wire:model="newOrderTaskPhaseId">
                            <option value="">Select phase</option>
                            @foreach($job->workflow->phases->sortBy('sequence') as $taskPhaseOption)
                                <option value="{{ $taskPhaseOption->id }}">{{ $taskPhaseOption->sequence }}. {{ $taskPhaseOption->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    @php
                        $newOrderTaskAssignee = $users->firstWhere('id', $newOrderTaskAssigneeId);
                    @endphp
                    <div
                        class="ft-order-add-task-field ft-order-add-task-assignee"
                        x-data
                        x-on:ft-inline-remote-selected.stop="const raw = String($event.detail?.value ?? ''); $wire.$set('newOrderTaskAssigneeId', raw === '' ? null : Number(raw));"
                    >
                        <span>Assignee</span>
                        <x-ui.inline-remote-user
                            :value="$newOrderTaskAssigneeId ?? ''"
                            :selected-label="$newOrderTaskAssignee?->name ?? 'Unassigned'"
                            context="task-assignee"
                            parent-type="job"
                            :parent-id="$job->id"
                            search-placeholder="Search assignee…"
                            trigger-class="ft-order-add-task-assignee-trigger"
                            variant="compact"
                            :menu-width="320"
                            wire:key="order-add-task-assignee-{{ $job->id }}-{{ $newOrderTaskAssigneeId ?? 'none' }}"
                        />
                    </div>
                    <label class="ft-order-add-task-field">
                        <span>Due date</span>
                        <input type="date" wire:model="newOrderTaskDueDate" onclick="this.showPicker && this.showPicker()">
                    </label>
                    <div class="ft-order-add-task-field ft-order-add-task-field-description ft-mention-host" wire:ignore>
                        <span>Instructions</span>
                        <textarea
                            x-ref="newOrderTaskDescription"
                            class="ft-mention-input"
                            data-rich-text
                            data-mention-users="{{ $mentionUsers->toJson() }}"
                            autocomplete="off"
                            placeholder="Describe what must be completed for this task or paste screenshots here."
                        ></textarea>
                    </div>
                </div>
                @error('newOrderTaskName')<div class="ft-order-add-task-error">{{ $message }}</div>@enderror
                @error('newOrderTaskDescription')<div class="ft-order-add-task-error">{{ $message }}</div>@enderror
                @error('newOrderTaskPhaseId')<div class="ft-order-add-task-error">{{ $message }}</div>@enderror
                @error('newOrderTaskAssigneeId')<div class="ft-order-add-task-error">{{ $message }}</div>@enderror
                @error('newOrderTaskDueDate')<div class="ft-order-add-task-error">{{ $message }}</div>@enderror
                <div class="ft-order-add-task-actions">
                    <button class="ft-outline-btn" type="button" wire:click="cancelAddOrderTask">Cancel</button>
                    <button
                        class="ft-order-add-task-submit"
                        type="button"
                        data-rich-text-submit
                        wire:loading.attr="disabled"
                        wire:target="addOrderTask"
                        x-on:click="const source = $refs.newOrderTaskDescription; const read = source?.__flowtrackRichTextValueAsync ? source.__flowtrackRichTextValueAsync() : Promise.resolve(String(source?.value || '')); read.then((description) => $wire.addOrderTask(description))"
                    >
                        <span wire:loading.remove wire:target="addOrderTask">Add Task</span>
                        <span wire:loading wire:target="addOrderTask">Adding…</span>
                    </button>
                </div>
            </div>
        @endif
        <div class="ft-phase-task-table ft-order-overview-taskflow">
            @foreach($job->workflow->phases as $phase)
                @php
                    $allPhaseTasks = \App\Support\JobDetailPresenter::phaseTasks($job,$phase);
                    $completed = \App\Support\JobDetailPresenter::completedCount($allPhaseTasks);
                    $phaseProgress = $allPhaseTasks->count() ? round($completed/max(1,$allPhaseTasks->count())*100) : 0;
                    $phaseTasks = $allPhaseTasks;
                    $expanded = in_array((int) $phase->id, array_map('intval', $expandedPhaseIds), true);
                    $phaseTone = ((max(1, (int) $phase->sequence) - 1) % 6) + 1;
                @endphp
                <div class="ft-phase-group ft-phase-tone-{{ $phaseTone }} {{ $expanded ? 'open' : '' }}" wire:key="job-phase-{{ $phase->id }}">
                    <div class="ft-phase-group-head ft-order-phase-head">
                        <b class="{{ $phase->id === $job->phase->id ? 'current-number' : '' }}">{{ $phase->sequence }}</b>
                        <strong>{{ $phase->name }}</strong>
                        <small>{{ $completed }} of {{ $allPhaseTasks->count() }} complete</small>
                        <em style="--phase-progress:{{ $phaseProgress }}%"></em>
                    </div>
                    @if($expanded)
                        <div class="ft-phase-task-columns"><span>Task</span><span>Assignee</span><span>Due date</span><span>Status</span><span>Files</span><span>Action</span></div>
                        @forelse($phaseTasks as $task)
                            @php
                                $taskAccess = app(\App\Services\AccessControlService::class);
                                $canEditTask = $taskAccess->canEditVisibleTask(auth()->user(), $task);
                                $canAssignTask = $taskAccess->canAssignTask(auth()->user(), $task);
                                $canDeleteTask = $taskAccess->can(auth()->user(), 'tasks', 'delete');
                                $taskDocuments = $job->documents->where('task_id', $task->id)->sortByDesc('created_at')->values();
                                $taskLinks = $task->relationLoaded('links') ? $task->links : collect();
                                $taskRequirement = $requiredDocuments->first(fn ($requirement) => (int) ($requirement->task?->id ?? 0) === (int) $task->id);
                                $effectiveTaskDescription = $task->description ?: $task->setupTemplate?->description;
                                $taskStripeClass = $loop->odd ? 'is-green' : 'is-white';
                            @endphp
                            <div class="ft-phase-task-line ft-editable-task-line ft-order-taskflow-row {{ $taskStripeClass }}" wire:key="job-task-{{ $task->id }}">
                                <span class="ft-order-task-number">{{ $phase->sequence }}.{{ $loop->iteration }}</span>
                                <div class="ft-order-task-copy">
                                    <button class="ft-inline-task-link" type="button" wire:click="openTask({{ $task->id }})">{{ $task->title }}</button>
                                    <span class="ft-order-task-description">{{ $effectiveTaskDescription ? \Illuminate\Support\Str::limit(strip_tags((string) $effectiveTaskDescription), 110) : 'No instructions added.' }}</span>
                                    @if($taskRequirement)
                                        <span class="ft-order-task-required-file {{ $taskRequirement->complete ? 'is-complete' : '' }}">{{ $taskRequirement->complete ? '✓ File submitted' : '□ Required file: '.$taskRequirement->name }}</span>
                                    @endif
                                </div>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell ft-order-task-assignee-inline"
                                    data-field-label="Assignee"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-assignee'), label: 'task assignee', value: @js($task->assignee_id ?? ''), display: @js($task->assignee?->name ?? 'Unassigned'), avatarUrl: @js($task->assignee?->profileImageUrl() ?? '') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                    x-on:click.outside="if (editing) cancelEdit()"
                                    x-on:ft-inline-remote-cancel.stop="cancelEdit()"
                                    x-on:ft-inline-remote-selected.stop="commit(String($event.detail?.value ?? ''), String($event.detail?.label ?? 'Unassigned'), () => $wire.updateTaskAssigneeFromJob({{ $task->id }}, draftValue), { avatarUrl: String($event.detail?.avatarUrl ?? '') })"
                                >
                                    <div class="ft-order-inline-display-row" x-show="!editing">
                                        <div class="ft-order-assignee-display">
                                            <span class="ft-inline-avatar-slot"><x-ui.inline-live-avatar :size="28" /></span>
                                            <span class="ft-order-assignee-name" x-text="display">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                        </div>
                                        @if($canAssignTask)
                                            <button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit assignee" aria-label="Edit task assignee" x-on:click.stop="openRemotePicker($event.currentTarget)">✎</button>
                                        @endif
                                    </div>
                                    @if($canAssignTask)
                                        <div x-cloak x-show="editing" class="ft-order-assignee-picker">
                                            <x-ui.inline-remote-user
                                                :value="$task->assignee_id ?? ''"
                                                parent-type="job"
                                                :parent-id="$job->id"
                                                :selected-label="$task->assignee?->name ?? 'Unassigned'"
                                                trigger-class="ft-order-task-inline-input"
                                                variant="compact"
                                                :menu-width="260"
                                            />
                                        </div>
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </span>
                                <span
                                    class="ft-task-inline-editor ft-inline-edit-shell ft-order-task-date-inline"
                                    data-field-label="Due date"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-due-date'), label: 'task due date', value: @js($task->due_date?->format('Y-m-d') ?? ''), display: @js($task->due_date?->format('M j, Y') ?? 'Set due date') })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    <div class="ft-order-inline-display-row" x-show="!editing">
                                        <span x-text="display" class="ft-order-inline-value {{ ($task->due_date && \App\Support\UserLocalTime::isDatePast($task->due_date)) && !$task->completed_at ? 'danger-text' : '' }}">{{ $task->due_date?->format('M j, Y') ?? 'Set due date' }}</span>
                                        @if($canEditTask)
                                            <button :disabled="status === 'saving'" type="button" class="ft-inline-edit-button" title="Edit due date" aria-label="Edit task due date" x-on:click.stop="if (beginEdit()) $nextTick(() => $refs.taskDue.showPicker ? $refs.taskDue.showPicker() : $refs.taskDue.focus())">✎</button>
                                        @endif
                                    </div>
                                    @if($canEditTask)
                                        <input x-ref="taskDue" x-cloak x-show="editing" x-model="draftValue" class="ft-order-task-inline-input" type="date"
                                            x-on:keydown.escape.prevent="cancelEdit()"
                                            x-on:blur="if (editing) cancelEdit()"
                                            x-on:change="commit($event.target.value, formatDate($event.target.value), () => $wire.updateTaskDueDateFromJob({{ $task->id }}, draftValue))">
                                        <x-ui.inline-save-state compact />
                                    @endif
                                </span>
                                <span
                                    class="ft-task-inline-status-shell ft-inline-edit-shell"
                                    data-field-label="Status"
                                    x-data="window.FlowTrackInlineEdit({ key: @js('task-'.$task->id.'-status'), label: 'task status', value: @js($task->status), display: @js($task->status) })"
                                    :class="{ 'is-inline-saving': status === 'saving', 'is-inline-error': status === 'error' }"
                                >
                                    @php
                                        $taskStatusColor = app(\App\Services\MasterDataService::class)->colorFor('order_task_status', (string) $task->status);
                                    @endphp
                                    <select data-master-color-select class="ft-inline-task-status {{ $taskStatusColor ? 'ft-master-color' : \App\Support\JobDetailPresenter::taskStatusClass($task->status) }}" style="{{ \App\Support\MasterColor::style($taskStatusColor) }}" x-model="draftValue"
                                        x-on:change="window.FlowTrackMasterColor?.applySelect($event.target); commit($event.target.value, selectedLabel($event), () => $wire.updateTaskStatusFromJob({{ $task->id }}, draftValue))"
                                        :disabled="status === 'saving'" @disabled(!$canEditTask)>
                                        @foreach($taskStatuses as $status)<option value="{{ $status }}" data-color="{{ app(\App\Services\MasterDataService::class)->colorFor('order_task_status', $status) }}">{{ $status }}</option>@endforeach
                                    </select>
                                    @if($canEditTask)<x-ui.inline-save-state compact />@endif
                                </span>
                                <div class="ft-order-task-files" data-field-label="Files">
                                    @if($canEditTask)
                                        <div class="ft-order-task-resource-add-actions" aria-label="Add task resource">
                                            @if($canUploadDocument || $canLinkDocument)
                                                <button class="ft-order-task-resource-add-icon" type="button" wire:click="openOverviewTaskDocumentModal({{ $task->id }})" title="Add file" aria-label="Add file to {{ $task->title }}">
                                                    <span class="ft-order-task-resource-plus">+</span>
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h6"/></svg>
                                                </button>
                                            @endif
                                            <button class="ft-order-task-resource-add-icon {{ (int) $overviewTaskLinkFormTaskId === (int) $task->id ? 'is-active' : '' }}" type="button" wire:click="openOverviewTaskLinkForm({{ $task->id }})" title="Add link" aria-label="Add external link to {{ $task->title }}">
                                                <span class="ft-order-task-resource-plus">+</span>
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                    <span class="ft-order-task-file-count"><b>{{ $taskDocuments->count() }}</b> file{{ $taskDocuments->count() === 1 ? '' : 's' }}@if($taskLinks->isNotEmpty()) · <b>{{ $taskLinks->count() }}</b> link{{ $taskLinks->count() === 1 ? '' : 's' }}@endif</span>
                                </div>
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

                            @if((int) $overviewTaskLinkFormTaskId === (int) $task->id || $taskDocuments->isNotEmpty() || $taskLinks->isNotEmpty())
                                <div class="ft-order-task-resource-list {{ $taskStripeClass }}" wire:key="job-task-resources-{{ $task->id }}">
                                    @if((int) $overviewTaskLinkFormTaskId === (int) $task->id && $canEditTask)
                                        <form class="ft-order-task-link-form" wire:submit.prevent="saveOverviewTaskLink({{ $task->id }})" wire:key="job-task-link-form-{{ $task->id }}">
                                            <div class="ft-order-task-link-input-wrap">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                <input type="text" inputmode="url" wire:model="overviewTaskLinkUrl" placeholder="Paste link, e.g. https://drive.google.com/..." autocomplete="url" autofocus aria-label="External link">
                                            </div>
                                            <div class="ft-order-task-link-form-actions">
                                                <button class="secondary" type="button" wire:click="cancelOverviewTaskLinkForm">Cancel</button>
                                                <button class="primary" type="submit" wire:loading.attr="disabled" wire:target="saveOverviewTaskLink({{ $task->id }})">Add</button>
                                            </div>
                                            @error('overviewTaskLinkUrl')<div class="ft-order-task-link-error">{{ $message }}</div>@enderror
                                        </form>
                                    @endif

                                    @foreach($taskDocuments as $taskDocument)
                                        <div class="ft-order-task-document-row" wire:key="job-task-document-{{ $taskDocument->id }}">
                                            <span class="ft-order-task-file-type">{{ strtoupper(pathinfo($taskDocument->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                                            <div class="ft-order-task-file-copy">
                                                <b title="{{ $taskDocument->name }}">{{ $taskDocument->name }}</b>
                                                @if($taskDocument->note)<span class="ft-order-task-file-note">{{ $taskDocument->note }}</span>@endif
                                                <small>{{ $taskDocument->category ?: 'Task attachment' }} · {{ $taskDocument->uploader?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::format($taskDocument->created_at, 'M j, Y, g:i A') }}</small>
                                            </div>
                                            <div class="ft-order-task-file-actions">
                                                <a href="{{ route('documents.open', $taskDocument) }}" target="_blank" rel="noopener">Open</a>
                                                @if(auth()->user()->canModule('documents','export'))<a href="{{ route('documents.download', $taskDocument) }}">Download</a>@endif
                                                @if($canDeleteDocument)
                                                    <button type="button" wire:click="deleteJobDocument({{ $taskDocument->id }})" wire:loading.attr="disabled" wire:target="deleteJobDocument({{ $taskDocument->id }})" wire:confirm="Delete this document link?" title="Remove attachment" aria-label="Remove {{ $taskDocument->name }}">×</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach($taskLinks as $taskLink)
                                        <div class="ft-order-task-link-row" wire:key="job-task-link-{{ $taskLink->id }}">
                                            <span class="ft-order-task-link-type" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></span>
                                            <div class="ft-order-task-link-copy">
                                                <a href="{{ $taskLink->url }}" target="_blank" rel="noopener noreferrer" title="{{ $taskLink->url }}">{{ \Illuminate\Support\Str::limit($taskLink->url, 110) }}</a>
                                                <small>{{ $taskLink->created_at ? \App\Support\UserLocalTime::format($taskLink->created_at, 'M j, Y, g:i A') : '—' }}</small>
                                            </div>
                                            <div class="ft-order-task-link-actions">
                                                <a href="{{ $taskLink->url }}" target="_blank" rel="noopener noreferrer">Open ↗</a>
                                                @if($canEditTask)<button type="button" wire:click="deleteOverviewTaskLink({{ $task->id }}, {{ $taskLink->id }})" wire:confirm="Remove this link from the task?" title="Remove link" aria-label="Remove link">×</button>@endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
                    <label class="ft-task-upload-drop ft-livewire-upload-zone {{ $errors->has('jobDocumentUploads') || $errors->has('jobDocumentUploads.*') ? 'has-upload-error' : '' }}" data-file-dropzone data-auto-upload-method="uploadJobOverviewDocuments" for="jobOverviewDocumentUpload-{{ $job->id }}">
                        <input id="jobOverviewDocumentUpload-{{ $job->id }}" type="file" wire:model="jobDocumentUploads" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv">
                        <span class="ft-paperclip">⌕</span>
                        <div>Drop files here or <strong>browse</strong><small data-drop-status>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small></div>
                    </label>
                @else
                    <div class="ft-task-upload-drop ft-task-upload-readonly"><span class="ft-paperclip">⌕</span><div>Attachments<small>You have read-only access to Job attachments.</small></div></div>
                @endif
            </div>
            @if($canUploadDocument)
                @error('jobDocumentUploads')
                    <div class="ft-upload-field-error validation-error" role="alert">
                        <span>{{ $message }}</span>
                        <button type="button" wire:click="clearJobDocumentUploads">Remove failed file</button>
                    </div>
                @enderror
            @endif
            @if($canUploadDocument && count($jobDocumentUploads ?? []))
                <div class="ft-pending-upload-list" aria-label="Files selected for upload">
                    @foreach($jobDocumentUploads as $uploadIndex => $upload)
                        @php
                            $uploadName = method_exists($upload, 'getClientOriginalName') ? $upload->getClientOriginalName() : ('File '.($uploadIndex + 1));
                        @endphp
                        <div class="ft-pending-upload-item {{ $errors->has('jobDocumentUploads.'.$uploadIndex) ? 'has-error' : '' }}" wire:key="job-overview-doc-pending-{{ $job->id }}-{{ $uploadIndex }}-{{ md5($uploadName) }}">
                            <div class="ft-pending-upload-copy">
                                <b>{{ $uploadName }}</b>
                                @error('jobDocumentUploads.'.$uploadIndex)<small class="validation-error" role="alert">{{ $message }}</small>@enderror
                            </div>
                            <button type="button" class="ft-pending-upload-remove" wire:click="removeJobDocumentUpload({{ $uploadIndex }})">Remove</button>
                        </div>
                    @endforeach
                </div>
                <div class="ft-upload-ready-row ft-auto-upload-state" aria-live="polite">
                    <span>Uploading and linking {{ count($jobDocumentUploads ?? []) }} file{{ count($jobDocumentUploads ?? [])===1?'':'s' }} automatically…</span>
                </div>
            @endif
        @else
            <div class="ft-empty-taskpack-docs">No Task Pack document requirement is configured for this Job. Open Documents to review the document setup.</div>
        @endif
        @foreach($job->documents as $doc)
            <div class="ft-job-file-row" wire:key="job-overview-document-{{ $doc->id }}">
                <span class="ft-file-type">{{ strtoupper(pathinfo($doc->name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                <div class="ft-job-file-main">
                    <b title="{{ $doc->name }}">{{ $doc->name }}</b>
                    <small>{{ $doc->task?->title ?: 'Job document' }} · {{ $doc->uploader?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::format($doc->created_at, 'M j, Y, g:i A') }}</small>
                </div>
                <div class="ft-job-file-actions">
                    <a class="ft-link-blue" href="{{ route('documents.open',$doc) }}" target="_blank" rel="noopener">Open</a>
                    @if(auth()->user()->canModule('documents','export'))
                        <a class="ft-link-blue" href="{{ route('documents.download',$doc) }}">Download</a>
                    @endif
                    @if($canDeleteDocument)
                        <button type="button" class="ft-job-file-delete" wire:click="deleteJobDocument({{ $doc->id }})" wire:confirm="Delete this document link?" title="Remove attachment" aria-label="Remove {{ $doc->name }}">×</button>
                    @endif
                </div>
            </div>
        @endforeach
    </section>

    <div id="order-product-history"></div>
    <x-jobs.detail-activity :job="$job" :mention-users="$mentionUsers" compact="true" :activity-tab="$activityTab" :activity-page="$activityPage" :focus-comment="$focusComment" />

    @if($showOverviewTaskDocumentModal && $overviewTaskDocumentModalTask)
        <div class="ft-order-task-document-modal-backdrop" wire:key="order-task-document-modal" wire:click.self="closeOverviewTaskDocumentModal">
            <section class="ft-order-task-document-modal" role="dialog" aria-modal="true" aria-labelledby="order-task-document-modal-title">
                <header class="ft-order-task-document-modal-head">
                    <div>
                        <h2 id="order-task-document-modal-title">Add new document to task</h2>
                        <p>Upload a new file or choose a document that already exists.</p>
                    </div>
                    <button type="button" class="ft-order-task-document-modal-close" wire:click="closeOverviewTaskDocumentModal" aria-label="Close">×</button>
                </header>

                <div class="ft-order-task-document-modal-body">
                    <div class="ft-order-task-document-target">
                        <span class="ft-order-task-document-target-icon">▣</span>
                        <div>
                            <small>ATTACHING TO</small>
                            <strong>{{ $overviewTaskDocumentModalTask->title }}</strong>
                            <span>{{ $overviewTaskDocumentModalTask->task_number ?: 'TASK-'.str_pad((string) $overviewTaskDocumentModalTask->id, 5, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp; {{ $overviewTaskDocumentModalTask->phase?->name ?? 'Order Taskflow' }}</span>
                            <span class="ft-order-task-document-reference"><b>Order Reference:</b> {{ $job->order_number ?: '—' }}</span>
                        </div>
                        <span class="ft-order-task-document-target-lock">▣&nbsp; Task selected</span>
                    </div>

                    <div class="ft-order-task-document-source-label">Document source</div>
                    <div class="ft-order-task-document-source-tabs">
                        <button type="button" class="{{ $overviewTaskDocumentSource === 'upload' ? 'active' : '' }}" wire:click="setOverviewTaskDocumentSource('upload')" @disabled(!$canUploadDocument)><span>↥</span> Upload new</button>
                        <button type="button" class="{{ $overviewTaskDocumentSource === 'existing' ? 'active' : '' }}" wire:click="setOverviewTaskDocumentSource('existing')" @disabled(!$canLinkDocument)><span>▤</span> Choose existing</button>
                    </div>

                    @if($overviewTaskDocumentSource === 'upload' && $canUploadDocument)
                        <label class="ft-order-task-document-dropzone">
                            <input type="file" wire:model="overviewTaskDocumentUpload" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.txt,.csv,.ai">
                            <span class="ft-order-task-document-upload-icon">⇧</span>
                            @if($overviewTaskDocumentUpload)
                                <strong>{{ $overviewTaskDocumentUpload->getClientOriginalName() }}</strong>
                                <b>File selected — choose another file</b>
                                <small>{{ number_format(max(1, (int) ceil($overviewTaskDocumentUpload->getSize() / 1024))) }} KB · ready to add</small>
                            @else
                                <strong>Drop a file here</strong>
                                <b>or browse files</b>
                                <small>PDF, DOCX, XLSX, JPG, PNG or ZIP · Max 20 MB</small>
                            @endif
                        </label>
                        @error('overviewTaskDocumentUpload')<p class="ft-order-task-document-error">{{ $message }}</p>@enderror
                    @else
                        <div class="ft-order-task-document-existing">
                            @if($overviewTaskAvailableDocuments->isEmpty())
                                <div class="ft-order-task-document-existing-empty">No existing client documents are available.</div>
                            @else
                                <label>
                                    <span>Choose an existing document</span>
                                    <select wire:model="overviewTaskExistingDocumentId">
                                        <option value="">Select a document...</option>
                                        @foreach($overviewTaskAvailableDocuments as $sourceDocument)
                                            <option value="{{ $sourceDocument->id }}">{{ $sourceDocument->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif
                        </div>
                        @error('overviewTaskExistingDocumentId')<p class="ft-order-task-document-error">{{ $message }}</p>@enderror
                    @endif

                    <label class="ft-order-task-document-note">
                        <span>Document note (optional)</span>
                        <input type="text" wire:model="overviewTaskDocumentNote" placeholder="Add a short note about this document...">
                    </label>
                    @error('overviewTaskDocumentNote')<p class="ft-order-task-document-error">{{ $message }}</p>@enderror

                    <div class="ft-order-task-document-info">
                        <span>ⓘ</span>
                        <p>This document will appear directly under <strong>{{ $overviewTaskDocumentModalTask->title }}</strong> and in Order Documents.</p>
                    </div>
                </div>

                <footer class="ft-order-task-document-modal-actions">
                    <button type="button" class="secondary" wire:click="closeOverviewTaskDocumentModal">Cancel</button>
                    <button type="button" class="primary" wire:click="saveOverviewTaskDocument" wire:loading.attr="disabled" wire:target="saveOverviewTaskDocument,overviewTaskDocumentUpload"
                        @disabled($overviewTaskDocumentSource === 'upload' ? !$overviewTaskDocumentUpload : !$overviewTaskExistingDocumentId)>
                        <span wire:loading.remove wire:target="saveOverviewTaskDocument">Add document</span>
                        <span wire:loading wire:target="saveOverviewTaskDocument">Adding...</span>
                    </button>
                </footer>
            </section>
        </div>
    @endif
</div>
