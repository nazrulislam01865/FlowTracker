<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'preview' => [],
    'defaultSubject' => '',
    'emptyRecipientText' => 'No active team member with an email address was found.',
]));

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

foreach (array_filter(([
    'preview' => [],
    'defaultSubject' => '',
    'emptyRecipientText' => 'No active team member with an email address was found.',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $recipients = collect($preview['recipients'] ?? []);
    $fromName = trim((string) ($preview['from_name'] ?? ''));
    $fromAddress = trim((string) ($preview['from_address'] ?? ''));
    $fromText = $fromName !== '' && $fromAddress !== ''
        ? $fromName.' <'.$fromAddress.'>'
        : ($fromAddress !== '' ? $fromAddress : $fromName);
    $replyTo = trim((string) ($preview['reply_to'] ?? ''));
    $subject = trim((string) ($preview['subject'] ?? '')) ?: $defaultSubject;
    $attachments = collect($preview['documents'] ?? [])->pluck('name')->filter()->values();
    if ($attachments->isEmpty() && filled($preview['document_name'] ?? null)) {
        $attachments = collect([(string) $preview['document_name']]);
    }
    $delivery = trim((string) ($preview['delivery'] ?? '')) ?: 'Configured email service';
    $emailServiceEnabled = (bool) ($preview['email_service_enabled'] ?? true);
    $recipientSource = trim((string) ($preview['recipient_source'] ?? ''));
    $previewEmptyRecipientText = trim((string) ($preview['empty_recipient_message'] ?? ''));
    if ($previewEmptyRecipientText !== '') {
        $emptyRecipientText = $previewEmptyRecipientText;
    }
    $html = (string) ($preview['html'] ?? '');
?>

<section class="ft-order-email-preview-shell" aria-label="Email preview">
    <header class="ft-order-email-preview-head">
        <div>
            <small>EMAIL PREVIEW</small>
            <strong><?php echo e($emailServiceEnabled ? 'Exact email that will be sent' : 'Email delivery is currently disabled'); ?></strong>
            <span><?php echo e($emailServiceEnabled ? 'Recipients, subject, attachment and message body are generated from this Order.' : 'The preview remains available, but confirming this task will continue the workflow without sending email.'); ?></span>
        </div>
        <em><?php echo e($delivery); ?></em>
    </header>

    <div class="ft-order-email-preview-meta">
        <div class="ft-order-email-preview-meta-row ft-order-email-preview-meta-row--recipients">
            <span>To</span>
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recipients->isNotEmpty()): ?>
                    <div class="ft-order-email-recipient-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <span class="ft-order-email-recipient-chip">
                                <b><?php echo e($recipient['name'] ?? 'Team member'); ?></b>
                                <small><?php echo e($recipient['email'] ?? ''); ?></small>
                            </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="ft-order-email-recipient-empty"><?php echo e($emptyRecipientText); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recipientSource !== ''): ?>
            <div class="ft-order-email-preview-meta-row"><span>Recipient rule</span><strong><?php echo e($recipientSource); ?></strong></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-order-email-preview-meta-row"><span>From</span><strong><?php echo e($fromText !== '' ? $fromText : 'Configured sender'); ?></strong></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($replyTo !== ''): ?>
            <div class="ft-order-email-preview-meta-row"><span>Reply to</span><strong><?php echo e($replyTo); ?></strong></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="ft-order-email-preview-meta-row"><span>Subject</span><strong><?php echo e($subject); ?></strong></div>
        <div class="ft-order-email-preview-meta-row"><span>Attachment<?php echo e($attachments->count() === 1 ? '' : 's'); ?></span><strong><?php echo e($attachments->isNotEmpty() ? $attachments->implode(', ') : 'No attachment available'); ?></strong></div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($html !== ''): ?>
        <div class="ft-order-email-preview-browser">
            <div class="ft-order-email-preview-browser-bar">
                <span></span><span></span><span></span>
                <b>Message body preview</b>
            </div>
            <iframe
                class="ft-order-email-preview-frame"
                title="Outgoing email message preview"
                sandbox=""
                referrerpolicy="no-referrer"
                srcdoc="<?php echo e($html); ?>"
            ></iframe>
        </div>
    <?php else: ?>
        <div class="ft-order-email-preview-unavailable">
            The email body preview will appear after the required attachment is available.
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/email/handoff-preview.blade.php ENDPATH**/ ?>