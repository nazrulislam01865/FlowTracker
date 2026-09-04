<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['brand' => []]));

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

foreach (array_filter((['brand' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $brandName = trim((string) ($brand['name'] ?? 'Company')) ?: 'Company';
    $legalName = trim((string) ($brand['legal_name'] ?? ''));
    $addressLines = collect($brand['address_lines'] ?? [])->filter(fn ($value) => filled($value))->values();
    $contactParts = collect([
        $brand['billing_email'] ?? null,
        $brand['phone'] ?? null,
        $brand['website'] ?? null,
    ])->filter(fn ($value) => filled($value))->map(fn ($value) => trim((string) $value))->values();
    $registrationParts = collect([
        filled($brand['registration_number'] ?? null) ? 'Registration: '.trim((string) $brand['registration_number']) : null,
        filled($brand['tax_number'] ?? null) ? 'Tax/VAT: '.trim((string) $brand['tax_number']) : null,
    ])->filter()->values();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="margin:0;padding:0;background:#f4f7fa;color:#152238;font-family:Arial,Helvetica,sans-serif;line-height:1.5;-webkit-text-size-adjust:100%">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent">Supplier quotation update from <?php echo e($brandName); ?></div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#f4f7fa">
<tr><td align="center" style="padding:32px 16px">
    <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:640px;border-collapse:separate;background:#ffffff;border:1px solid #d9e2ec;border-radius:14px;overflow:hidden;box-shadow:0 8px 28px rgba(15,35,65,.07)">
        <tr>
            <td style="padding:20px 24px;background:#13263d;color:#ffffff">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
                    <td style="vertical-align:middle">
                        <div style="font-size:20px;font-weight:800;letter-spacing:-.35px;color:#35c0b1"><?php echo e($brandName); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($legalName !== '' && strcasecmp($legalName, $brandName) !== 0): ?>
                            <div style="margin-top:3px;font-size:10px;color:#c5d3df"><?php echo e($legalName); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td align="right" style="vertical-align:middle;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#b9c8d8">Supplier RFQ</td>
                </tr></table>
            </td>
        </tr>
        <tr><td style="padding:30px 28px 30px"><?php echo e($slot); ?></td></tr>
        <tr>
            <td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e7edf2;color:#6b7e96;font-size:11px;line-height:1.5">
                <?php echo e($footer); ?>

                <div style="margin-top:7px;color:#4f6279;font-weight:700"><?php echo e($legalName !== '' ? $legalName : $brandName); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($addressLines->isNotEmpty()): ?>
                    <div style="margin-top:2px"><?php echo e($addressLines->implode(' · ')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contactParts->isNotEmpty()): ?>
                    <div style="margin-top:2px"><?php echo e($contactParts->implode(' · ')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($registrationParts->isNotEmpty()): ?>
                    <div style="margin-top:2px"><?php echo e($registrationParts->implode(' · ')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div style="margin-top:7px;color:#8a98aa">This is an automated supplier communication from <?php echo e($brandName); ?>.</div>
            </td>
        </tr>
    </table>
    <div style="padding:14px 8px 0;color:#8a98aa;font-size:10px">Please do not forward secure quotation links to another company.</div>
</td></tr>
</table>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/components/email/rfq-frame.blade.php ENDPATH**/ ?>