@props([
    'preview' => [],
    'defaultSubject' => '',
    'emptyRecipientText' => 'No active team member with an email address was found.',
])
@php
    $recipients = collect($preview['recipients'] ?? []);
    $fromName = trim((string) ($preview['from_name'] ?? ''));
    $fromAddress = trim((string) ($preview['from_address'] ?? ''));
    $fromText = $fromName !== '' && $fromAddress !== ''
        ? $fromName.' <'.$fromAddress.'>'
        : ($fromAddress !== '' ? $fromAddress : $fromName);
    $replyTo = trim((string) ($preview['reply_to'] ?? ''));
    $subject = trim((string) ($preview['subject'] ?? '')) ?: $defaultSubject;
    $attachment = trim((string) ($preview['document_name'] ?? ''));
    $delivery = trim((string) ($preview['delivery'] ?? '')) ?: 'Configured email service';
    $emailServiceEnabled = (bool) ($preview['email_service_enabled'] ?? true);
    $recipientSource = trim((string) ($preview['recipient_source'] ?? ''));
    $previewEmptyRecipientText = trim((string) ($preview['empty_recipient_message'] ?? ''));
    if ($previewEmptyRecipientText !== '') {
        $emptyRecipientText = $previewEmptyRecipientText;
    }
    $html = (string) ($preview['html'] ?? '');
@endphp

<section class="ft-order-email-preview-shell" aria-label="Email preview">
    <header class="ft-order-email-preview-head">
        <div>
            <small>EMAIL PREVIEW</small>
            <strong>{{ $emailServiceEnabled ? 'Exact email that will be sent' : 'Email delivery is currently disabled' }}</strong>
            <span>{{ $emailServiceEnabled ? 'Recipients, subject, attachment and message body are generated from this Order.' : 'The preview remains available, but confirming this task will continue the workflow without sending email.' }}</span>
        </div>
        <em>{{ $delivery }}</em>
    </header>

    <div class="ft-order-email-preview-meta">
        <div class="ft-order-email-preview-meta-row ft-order-email-preview-meta-row--recipients">
            <span>To</span>
            <div>
                @if($recipients->isNotEmpty())
                    <div class="ft-order-email-recipient-list">
                        @foreach($recipients as $recipient)
                            <span class="ft-order-email-recipient-chip">
                                <b>{{ $recipient['name'] ?? 'Team member' }}</b>
                                <small>{{ $recipient['email'] ?? '' }}</small>
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="ft-order-email-recipient-empty">{{ $emptyRecipientText }}</p>
                @endif
            </div>
        </div>
        @if($recipientSource !== '')
            <div class="ft-order-email-preview-meta-row"><span>Recipient rule</span><strong>{{ $recipientSource }}</strong></div>
        @endif
        <div class="ft-order-email-preview-meta-row"><span>From</span><strong>{{ $fromText !== '' ? $fromText : 'Configured sender' }}</strong></div>
        @if($replyTo !== '')
            <div class="ft-order-email-preview-meta-row"><span>Reply to</span><strong>{{ $replyTo }}</strong></div>
        @endif
        <div class="ft-order-email-preview-meta-row"><span>Subject</span><strong>{{ $subject }}</strong></div>
        <div class="ft-order-email-preview-meta-row"><span>Attachment</span><strong>{{ $attachment !== '' ? $attachment : 'No attachment available' }}</strong></div>
    </div>

    @if($html !== '')
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
                srcdoc="{{ $html }}"
            ></iframe>
        </div>
    @else
        <div class="ft-order-email-preview-unavailable">
            The email body preview will appear after the required attachment is available.
        </div>
    @endif
</section>
