@props(['detail','users','editing'=>false])
@php
    $client = $detail['client'];
    $jobs = $detail['jobs'];
    $active = $detail['active'];
    $health = $detail['health'];
    $initials = collect(preg_split('/\s+/', trim($client->name)))->filter()->take(2)->map(fn($part)=>strtoupper(substr($part,0,1)))->implode('') ?: 'CL';
    $access = app(\App\Services\AccessControlService::class);
    $canEdit = $access->isAdministrator(auth()->user()) || $access->canEditAll(auth()->user(),'clients') || ($access->canEditOwn(auth()->user(),'clients') && (int)$client->account_manager_id === (int)auth()->id());
    $canDelete = auth()->user()->canModule('clients','delete');
@endphp
<div class="ft-client-view-page">
    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
    <div class="ft-client-view-top">
        <div><small>Client Details</small><h1>{{ $client->name }}</h1><small>{{ $client->country ?: '—' }}</small></div>
        <button class="ft-client-view-back" type="button" wire:click="backToClients">← Back to Clients</button>
    </div>

    <section class="ft-client-view-card">
        <div class="ft-client-view-hero">
            <span class="ft-client-detail-logo">{{ $initials }}</span>
            <div><small>Client</small><h2>{{ $client->name }}</h2><small>{{ $client->country ?: '—' }}</small></div>
        </div>

        <div class="ft-client-view-summary">
            <div><small>Primary Contact</small><b>{{ $client->contact_name ?: 'Not set' }}</b></div>
            <div><small>Account Manager</small><b>{{ $client->accountManager?->name ?? 'Unassigned' }}</b></div>
            <div><small>Outstanding</small><b>${{ number_format($client->outstanding_balance,0) }}</b></div>
        </div>

        <div class="ft-client-view-section">
            <div class="ft-client-view-section-head">
                <h3>Contact Information</h3>
                <div class="ft-client-view-actions">
                    @if($canDelete)<button type="button" class="ft-client-delete-btn" wire:click="deleteClient({{ $client->id }})" wire:confirm="Delete this client? Clients with Order history will be archived so existing records remain intact.">Delete Client</button>@endif
                    @if($canEdit && !$editing)<button type="button" class="ft-client-edit-btn" wire:click="editClient({{ $client->id }})">Edit Client</button>@endif
                </div>
            </div>

            @if($editing)
                <div class="ft-client-edit-form">
                    <label><span>Client name *</span><input wire:model="clientName">@error('clientName')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label><span>Country</span><input wire:model="clientCountry"></label>
                    <label><span>Primary contact</span><input wire:model="contactName"></label>
                    <label><span>Email</span><input type="email" wire:model="email">@error('email')<small class="validation-error">{{ $message }}</small>@enderror</label>
                    <label><span>Phone</span><input wire:model="phone"></label>
                    <label><span>Account manager</span><select wire:model="accountManagerId"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></label>
                    <label><span>Preferred language</span><select wire:model="preferredLanguage"><option>English</option><option>Chinese</option><option>Spanish</option><option>French</option><option>German</option><option>Arabic</option><option>Bengali</option></select></label>
                    <label><span>Outstanding balance</span><input type="number" min="0" step="0.01" wire:model="outstandingBalance"></label>
                    <label class="span-2"><span>Notes</span><textarea wire:model="notes"></textarea></label>
                    <div class="ft-client-edit-form-actions"><button type="button" class="ft-outline-btn" wire:click="cancelEditClient">Cancel</button><button type="button" class="ft-new-job-btn" wire:click="updateClient">Save Client</button></div>
                </div>
            @else
                <div class="ft-client-contact-box">
                    <b>{{ $client->contact_name ?: 'No primary contact recorded' }}</b>
                    <div>{{ $client->email ?: 'No email recorded' }}</div>
                    <div>{{ $client->phone ?: 'No phone recorded' }}</div>
                    <div>Preferred language: {{ $client->preferred_language ?: 'English' }}</div>
                    @if($client->notes)<div style="margin-top:8px;color:#60738d">{{ $client->notes }}</div>@endif
                </div>
            @endif

            <div class="ft-client-jobs-head">
                <h3>Active Orders</h3>
                @if(auth()->user()->canModule('jobs','create'))<a class="ft-new-job-btn" href="{{ route('jobs.index',['create'=>1,'client'=>$client->id]) }}" wire:navigate>＋ New Order</a>@endif
            </div>
            <div class="ft-client-jobs-list">
                @forelse($active as $job)
                    <div class="ft-client-job-row">
                        <a href="{{ route('jobs.index',['open'=>$job->id]) }}" wire:navigate>{{ $job->displayOrderNumber() }}</a>
                        <div><b>{{ $job->title }}</b><small>{{ $job->phase?->name ?? '—' }}</small></div>
                        <div><b>{{ $job->progress }}%</b><div class="ft-mini-progress"><span style="width:{{ $job->progress }}%"></span></div></div>
                        <div><small>{{ $job->delivery_date?->format('M j, Y') ?? 'No delivery date' }}</small></div>
                    </div>
                @empty
                    <div class="ft-client-empty" style="padding:34px;text-align:center">No Orders have been created for this client.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
