<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — {{ $branding['name'] ?? 'FlowTrack' }}</title>
    <link rel="icon" href="{{ $branding['favicon_url'] ?? asset('favicon.ico') }}">
    @vite(['resources/css/login.css'])
</head>
<body>
<div class="login screen">
    <section class="login-visual">
        <div>
            <div class="brand ft-login-brand ft-login-brand-dark">
                @if($branding['logo_url'] ?? null)
                    <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['name'] ?? 'FlowTrack' }}">
                @else
                    <div class="brand-mark">FT</div>
                    <span>{{ $branding['name'] ?? 'FlowTrack' }}</span>
                @endif
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
        <form class="login-form" method="POST" action="{{ route('login.store') }}">
            @csrf

            @if (request()->query('reason') === 'other-device')
                <div class="validation-error ft-login-session-message" role="alert">
                    Another device logged in with the same user ID and password. Your previous session was logged out.
                </div>
            @elseif (request()->query('reason') === 'timeout')
                <div class="validation-error ft-login-session-message" role="alert">
                    Your {{ config('session.lifetime', 30) }}-minute session has expired. Please sign in again.
                </div>
            @endif

            <div class="brand ft-login-brand ft-login-brand-form" style="margin-bottom:30px">
                @if($branding['logo_url'] ?? null)
                    <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['name'] ?? 'FlowTrack' }}">
                @else
                    <div class="brand-mark" style="background:var(--blue);color:#fff">FT</div>
                    <span>{{ $branding['name'] ?? 'FlowTrack' }}</span>
                @endif
            </div>

            <h2>Welcome back</h2>
            <p>Sign in to manage Jobs, assignments and client delivery.</p>

            <div class="field">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    type="email"
                    autocomplete="email"
                    required
                    autofocus
                >
                @error('email')
                    <div class="validation-error" role="alert">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        style="padding-right: 40px; width: 100%; box-sizing: border-box;"
                    >
                    <button type="button" id="togglePassword" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; display: flex;" tabindex="-1" title="Toggle password visibility">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <label class="check-row" style="border:0;margin-bottom:12px">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>

            <button class="primary" type="submit">Sign in</button>

        </form>
    </section>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        if (type === 'text') {
            this.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            this.style.color = '#3b82f6';
        } else {
            this.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            this.style.color = '#94a3b8';
        }
    });
</script>
</body>
</html>
