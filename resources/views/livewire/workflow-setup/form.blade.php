<div class="ft-admin-reference ft-workflow-form-page">
    <div class="ft-admin-form-top ft-workflow-create-top">
        <div>
            <div class="ft-admin-breadcrumb">{{ $workflowId ? 'Edit Workflow' : 'New Workflow' }}</div>
            <h1>{{ $workflowId ? 'Edit Workflow' : 'Create New Workflow' }}</h1>
            <p>Configure workflow identity on a dedicated page.</p>
        </div>
        <a href="{{ route('workflow.setup') }}" wire:navigate class="ft-admin-back">← Back to Workflow Setup</a>
    </div>

    <form wire:submit="save" class="ft-admin-form-card ft-workflow-create-card">
        <section class="ft-workflow-form-section ft-workflow-details-section">
            <div class="ft-workflow-section-heading">
                <h2>1. Workflow details</h2>
                <p>Name and describe this workflow.</p>
            </div>

            <div class="ft-workflow-details-grid">
                <div class="ft-admin-field">
                    <label for="workflow-name">Workflow name *</label>
                    <input id="workflow-name" type="text" wire:model="workflowName" placeholder="e.g. Fast Track Order" autocomplete="off">
                    @error('workflowName')<div class="validation-error">{{ $message }}</div>@enderror
                </div>
                <div class="ft-admin-field">
                    <label for="workflow-code">Workflow code *</label>
                    <input id="workflow-code" type="text" wire:model="workflowCode" placeholder="e.g. FAST_TRACK" autocomplete="off">
                    @error('workflowCode')<div class="validation-error">{{ $message }}</div>@enderror
                </div>
                <div class="ft-admin-field ft-workflow-description-field">
                    <label for="workflow-description">Description</label>
                    <textarea id="workflow-description" wire:model="workflowDescription" rows="3" placeholder="Describe when this workflow should be used..."></textarea>
                    @error('workflowDescription')<div class="validation-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </section>

        <section class="ft-workflow-form-section ft-workflow-scope-section">
            <div class="ft-workflow-section-heading">
                <h2>2. Workflow scope</h2>
                <p>Choose where this workflow is available.</p>
            </div>

            <fieldset class="ft-workflow-choice-group">
                <legend>Workflow applies to *</legend>
                <div class="ft-workflow-choice-grid">
                    <label class="ft-workflow-choice-card {{ $workflowAppliesTo === 'inquiries' ? 'is-selected' : '' }}">
                        <input type="radio" value="inquiries" wire:model.live="workflowAppliesTo">
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M9.8 9.2a2.45 2.45 0 0 1 4.75.8c0 1.85-2.55 2.08-2.55 3.65"></path>
                                <path d="M12 17.2h.01"></path>
                            </svg>
                        </span>
                        <span class="ft-workflow-choice-copy">
                            <strong>Inquiries</strong>
                            <small>Use this workflow when managing new client inquiries.</small>
                        </span>
                    </label>

                    <label class="ft-workflow-choice-card {{ $workflowAppliesTo === 'orders' ? 'is-selected' : '' }}">
                        <input type="radio" value="orders" wire:model.live="workflowAppliesTo">
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 8.5h16v10.5H4z"></path>
                                <path d="M8.2 8.5V6.8A2.8 2.8 0 0 1 11 4h2a2.8 2.8 0 0 1 2.8 2.8v1.7"></path>
                                <path d="M4 12.3h16"></path>
                            </svg>
                        </span>
                        <span class="ft-workflow-choice-copy">
                            <strong>Orders</strong>
                            <small>Use this workflow after an inquiry becomes an order.</small>
                        </span>
                    </label>
                </div>
                @error('workflowAppliesTo')<div class="validation-error">{{ $message }}</div>@enderror
            </fieldset>

            <fieldset class="ft-workflow-choice-group ft-workflow-client-availability">
                <legend>Client availability *</legend>
                <div class="ft-workflow-choice-grid">
                    <label class="ft-workflow-choice-card ft-workflow-client-choice {{ $clientAvailability === 'all' ? 'is-selected' : '' }}">
                        <input type="radio" value="all" wire:model.live="clientAvailability">
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-copy">
                            <strong>All clients</strong>
                            <small>Available to every current and future client.</small>
                        </span>
                    </label>

                    <label class="ft-workflow-choice-card ft-workflow-client-choice {{ $clientAvailability === 'specific' ? 'is-selected' : '' }}">
                        <input type="radio" value="specific" wire:model.live="clientAvailability">
                        <span class="ft-workflow-choice-radio" aria-hidden="true"></span>
                        <span class="ft-workflow-choice-copy">
                            <strong>Specific clients</strong>
                            <small>Available only to clients you select.</small>
                        </span>
                    </label>
                </div>
                @error('clientAvailability')<div class="validation-error">{{ $message }}</div>@enderror
            </fieldset>

            @if($clientAvailability === 'specific')
                <div class="ft-admin-field ft-workflow-client-field" x-data x-on:click.outside="$wire.set('clientPickerOpen', false)">
                    <label for="workflow-client-search">Select clients *</label>
                    <div class="ft-workflow-client-picker {{ $clientPickerOpen ? 'is-open' : '' }}">
                        <div class="ft-workflow-client-picker-control" wire:click="openClientPicker">
                            @foreach($selectedClients as $client)
                                <span class="ft-workflow-client-chip" wire:key="workflow-client-chip-{{ $client->id }}">
                                    {{ $client->name }}
                                    <button type="button" aria-label="Remove {{ $client->name }}" wire:click.stop="removeClient({{ $client->id }})">×</button>
                                </span>
                            @endforeach
                            <input id="workflow-client-search" type="search" wire:model.live.debounce.250ms="clientSearch" wire:focus="openClientPicker" placeholder="Search clients..." autocomplete="off">
                            <button type="button" class="ft-workflow-client-chevron" wire:click.stop="toggleClientPicker" aria-label="Toggle client list" aria-expanded="{{ $clientPickerOpen ? 'true' : 'false' }}">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5.5 7.5 4.5 4.5 4.5-4.5"></path></svg>
                            </button>
                        </div>

                        @if($clientPickerOpen)
                            <div class="ft-workflow-client-menu">
                                @forelse($clientOptions as $client)
                                    <button type="button" wire:click="selectClient({{ $client->id }})" wire:key="workflow-client-option-{{ $client->id }}">
                                        <span>{{ $client->name }}</span>
                                        @if($client->code)<small>{{ $client->code }}</small>@endif
                                    </button>
                                @empty
                                    <div class="ft-workflow-client-empty">{{ trim($clientSearch) !== '' ? 'No matching clients.' : 'No more active clients to select.' }}</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                    <small>{{ count($selectedClientIds) }} {{ \Illuminate\Support\Str::plural('client', count($selectedClientIds)) }} selected. You can update this later.</small>
                    @error('selectedClientIds')<div class="validation-error">{{ $message }}</div>@enderror
                    @error('selectedClientIds.*')<div class="validation-error">{{ $message }}</div>@enderror
                </div>
            @endif
        </section>

        @unless($workflowId)
            <section class="ft-workflow-form-section ft-workflow-start-section">
                <div class="ft-workflow-section-heading">
                    <h2>3. Start from</h2>
                </div>
                <div class="ft-admin-field">
                    <label for="workflow-source">Start from</label>
                    <select id="workflow-source" wire:model="sourceWorkflowId">
                        <option value="">Blank workflow</option>
                        @foreach($workflows as $workflow)
                            <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                        @endforeach
                    </select>
                    <small>Duplicating copies the phase sequence and configuration, but not Job history.</small>
                    @error('sourceWorkflowId')<div class="validation-error">{{ $message }}</div>@enderror
                </div>
            </section>
        @endunless

        <div class="ft-workflow-scope-summary">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="10" cy="10" r="7"></circle><path d="M10 9v4"></path><path d="M10 6.7h.01"></path>
            </svg>
            <span>
                This workflow will be available for {{ $workflowAppliesTo === 'inquiries' ? 'Inquiries' : 'Orders' }}
                @if($clientAvailability === 'specific')
                    from {{ count($selectedClientIds) }} selected {{ \Illuminate\Support\Str::plural('client', count($selectedClientIds)) }}.
                @else
                    for all clients.
                @endif
            </span>
        </div>

        <div class="ft-admin-form-footer ft-workflow-create-footer">
            <button type="button" class="ft-admin-cancel" wire:click="cancel">Cancel</button>
            <button type="submit" class="ft-admin-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ $workflowId ? 'Save Workflow' : 'Create Workflow' }}</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
