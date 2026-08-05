<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — FlowTrack</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/login.css']); ?>
</head>
<body>
<div class="login screen">
    <section class="login-visual">
        <div>
            <div class="brand">
                <div class="brand-mark">FT</div>
                <span>FlowTrack</span>
            </div>

            <h1>One Job. Every phase. Clear ownership.</h1>
            <p>A practical workspace for promotional-product sales, artwork, samples, manufacturing, shipping, invoicing and collection.</p>

            <div class="flow-preview">
                <div>01 · Request &amp; quotation</div>
                <div>02 · Artwork &amp; sample</div>
                <div>03 · Production &amp; quality</div>
                <div>04 · Shipment &amp; payment</div>
            </div>
        </div>

        <div class="small" style="color:#a9bdd2">Secure operations workspace</div>
    </section>

    <section class="login-form-wrap">
        <form class="login-form" method="POST" action="<?php echo e(route('login.store')); ?>">
            <?php echo csrf_field(); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->query('reason') === 'other-device'): ?>
                <div class="validation-error ft-login-session-message" role="alert">
                    Another device logged in with the same user ID and password. Your previous session was logged out.
                </div>
            <?php elseif(request()->query('reason') === 'timeout'): ?>
                <div class="validation-error ft-login-session-message" role="alert">
                    Your <?php echo e(config('session.lifetime', 30)); ?>-minute session has expired. Please sign in again.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="brand" style="margin-bottom:30px">
                <div class="brand-mark" style="background:var(--blue);color:#fff">FT</div>
                <span>FlowTrack</span>
            </div>

            <h2>Welcome back</h2>
            <p>Sign in to manage Jobs, assignments and client delivery.</p>

            <div class="field">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    value="<?php echo e(old('email')); ?>"
                    type="email"
                    autocomplete="email"
                    required
                    autofocus
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="validation-error" role="alert"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <label class="check-row" style="border:0;margin-bottom:12px">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>

            <button class="primary" type="submit">Sign in</button>

            <div class="demo-note">
                Super-admin credentials are configured from <b>SUPER_ADMIN_EMAIL</b> and
                <b>SUPER_ADMIN_PASSWORD</b> in the environment file.
            </div>
        </form>
    </section>
</div>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/auth/login.blade.php ENDPATH**/ ?>