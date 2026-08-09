<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['detail','users','editing'=>false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['detail','users','editing'=>false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $client = $detail['client'];
    $jobs = $detail['jobs'];
    $active = $detail['active'];
    $health = $detail['health'];
    $initials = collect(preg_split('/\s+/', trim($client->name)))->filter()->take(2)->map(fn($part)=>strtoupper(substr($part,0,1)))->implode('') ?: 'CL';
    $access = app(\App\Services\AccessControlService::class);
    $canEdit = $access->isAdministrator(auth()->user()) || $access->canEditAll(auth()->user(),'clients') || ($access->canEditOwn(auth()->user(),'clients') && (int)$client->account_manager_id === (int)auth()->id());
    $canDelete = auth()->user()->canModule('clients','delete');
?>
<div class="ft-client-view-page">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="flash"><?php echo e(session('success')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ft-client-view-top">
        <div><small>Client Details</small><h1><?php echo e($client->name); ?></h1><small><?php echo e($client->country ?: '—'); ?></small></div>
        <button class="ft-client-view-back" type="button" wire:click="backToClients">← Back to Clients</button>
    </div>

    <section class="ft-client-view-card">
        <div class="ft-client-view-hero">
            <span class="ft-client-detail-logo"><?php echo e($initials); ?></span>
            <div><small>Client</small><h2><?php echo e($client->name); ?></h2><small><?php echo e($client->country ?: '—'); ?></small></div>
        </div>

        <div class="ft-client-view-summary">
            <div><small>Primary Contact</small><b><?php echo e($client->contact_name ?: 'Not set'); ?></b></div>
            <div><small>Account Manager</small><b><?php echo e($client->accountManager?->name ?? 'Unassigned'); ?></b></div>
            <div><small>Outstanding</small><b>$<?php echo e(number_format($client->outstanding_balance,0)); ?></b></div>
        </div>

        <div class="ft-client-view-section">
            <div class="ft-client-view-section-head">
                <h3>Contact Information</h3>
                <div class="ft-client-view-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?><button type="button" class="ft-client-delete-btn" wire:click="deleteClient(<?php echo e($client->id); ?>)" wire:confirm="Delete this client? Clients with Order history will be archived so existing records remain intact.">Delete Client</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit && !$editing): ?><button type="button" class="ft-client-edit-btn" wire:click="editClient(<?php echo e($client->id); ?>)">Edit Client</button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editing): ?>
                <div class="ft-client-edit-form">
                    <label><span>Client name *</span><input wire:model="clientName"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['clientName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label><span>Country</span><input wire:model="clientCountry"></label>
                    <label><span>Office address</span><input wire:model="officeAddress"></label>
                    <label><span>Primary contact</span><input wire:model="contactName"></label>
                    <label><span>Email</span><input type="email" wire:model="email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="validation-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <label><span>Phone</span><input wire:model="phone"></label>
                    <label><span>Account manager</span><select wire:model="accountManagerId"><option value="">Unassigned</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label>
                    <label><span>Preferred language</span><select wire:model="preferredLanguage"><option>English</option><option>Chinese</option><option>Spanish</option><option>French</option><option>German</option><option>Arabic</option><option>Bengali</option></select></label>
                    <label><span>Outstanding balance</span><input type="number" min="0" step="0.01" wire:model="outstandingBalance"></label>
                    <label class="span-2"><span>Notes</span><textarea wire:model="notes"></textarea></label>
                    <div class="ft-client-edit-form-actions"><button type="button" class="ft-outline-btn" wire:click="cancelEditClient">Cancel</button><button type="button" class="ft-new-job-btn" wire:click="updateClient">Save Client</button></div>
                </div>
            <?php else: ?>
                <div class="ft-client-contact-box">
                    <b><?php echo e($client->contact_name ?: 'No primary contact recorded'); ?></b>
                    <div><?php echo e($client->email ?: 'No email recorded'); ?></div>
                    <div><?php echo e($client->phone ?: 'No phone recorded'); ?></div>
                    <div>Office address: <?php echo e($client->office_address ?: 'Not set'); ?></div>
                    <div>Preferred language: <?php echo e($client->preferred_language ?: 'English'); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($client->notes): ?><div style="margin-top:8px;color:#60738d"><?php echo e($client->notes); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ft-client-jobs-head">
                <h3>Active Orders</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canModule('jobs','create')): ?><a class="ft-new-job-btn" href="<?php echo e(route('jobs.index',['create'=>1,'client'=>$client->id])); ?>" wire:navigate>＋ New Order</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="ft-client-jobs-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $active; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="ft-client-job-row">
                        <a href="<?php echo e(route('jobs.index',['open'=>$job->id])); ?>" wire:navigate><?php echo e($job->displayOrderNumber()); ?></a>
                        <div><b><?php echo e($job->title); ?></b><small><?php echo e($job->phase?->name ?? '—'); ?></small></div>
                        <div><b><?php echo e($job->progress); ?>%</b><div class="ft-mini-progress"><span style="width:<?php echo e($job->progress); ?>%"></span></div></div>
                        <div><small><?php echo e($job->delivery_date?->format('M j, Y') ?? 'No delivery date'); ?></small></div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="ft-client-empty" style="padding:34px;text-align:center">No Orders have been created for this client.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/clients/detail.blade.php ENDPATH**/ ?>