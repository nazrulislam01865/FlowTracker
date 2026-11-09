<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Order tracking — STEP PROMO'); ?></title>
    <link rel="icon" href="<?php echo e(asset('images/step-promo/step-promo-icon.webp')); ?>">
    <script
        src="<?php echo e(asset('js/flowtrack-image-fallback.js')); ?>?v=<?php echo e(\App\Support\FrontendBuildVersion::current()); ?>"
        data-fallback-src="<?php echo e(asset('images/flowtrack-image-fallback.svg')); ?>"
    ></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/tracking.css', 'resources/js/order-tracking.js']); ?>
</head>
<body class="ft-tracking-page">
    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/tracking/layout.blade.php ENDPATH**/ ?>