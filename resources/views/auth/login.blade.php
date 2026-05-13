@extends('layouts.app')

@section('title', 'Sign In — Paaila')

@section('content')
<div class="auth-page">

    <div class="auth-left">
        <div class="auth-left-inner">
            <a href="{{ route('home') }}" class="auth-logo">
                <div class="navbar-logo"><img src="{{ asset('images/paailaLogo.png')}}" alt="Logo" class="navbar-logo"></div>
                <span>Paaila</span>
            </a>

            <div class="auth-tagline">
                <h1>Every trek<br>tells a story.</h1>
                <p>GPS-tracked adventures with live safety monitoring. Your family watches every step.</p>
            </div>

            <div class="auth-trust-badges">
                <div class="trust-badge">
                    <i class="fas fa-satellite-dish"></i>
                    <span>Live GPS tracking</span>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>PIN-based family safety</span>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-star"></i>
                    <span>Verified trekker reviews</span>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrapper">

            <div class="auth-form-header">
                <h2>Welcome back</h2>
                <p>Sign in to your account</p>
            </div>

            @if($errors->any())
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(session('status'))
                <div class="auth-alert auth-alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="form-field">
                    <label for="email">Email address</label>
                    <div class="field-wrap">
                        <i class="fas fa-envelope field-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="form-field">
                    <label for="password">
                        Password
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </label>
                    <div class="field-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-remember">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="remember-box"></span>
                        <span>Keep me signed in</span>
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="auth-divider">
                    <span>New to Paaila?</span>
                    <a href="{{ route('register') }}">Create an account</a>
                </div>
            </form>
        </div>
    </div>

</div>

@push('styles')
<style>
    body { overflow: hidden; }

    .auth-page {
        display: flex;
        height: 100vh;
        overflow: hidden;
    }

    .auth-left {
        width: 44%;
        flex-shrink: 0;
        background: var(--color-primary);
        background-image:
            radial-gradient(ellipse at 20% 80%, rgba(255,255,255,0.07) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(0,0,0,0.15) 0%, transparent 50%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding: 48px;
    }

    .auth-left::before {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 55%;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 400' preserveAspectRatio='none'%3E%3Cpath d='M0 400 L0 280 L120 180 L240 240 L360 100 L480 200 L600 80 L720 160 L840 50 L960 140 L1080 90 L1200 170 L1320 120 L1440 200 L1440 400Z' fill='rgba(0,0,0,0.12)'/%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom;
        pointer-events: none;
    }

    .auth-left-inner {
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .auth-logo {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        margin-bottom: 64px;
    }

    .auth-logo-icon {
        width: 44px; height: 44px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: white;
    }

    .auth-logo span {
        font-size: 22px;
        font-weight: 800;
        color: white;
        letter-spacing: -0.3px;
    }

    .auth-tagline h1 {
        font-size: clamp(32px, 3.5vw, 48px);
        font-weight: 800;
        color: white;
        line-height: 1.15;
        margin-bottom: 20px;
        letter-spacing: -0.5px;
    }

    .auth-tagline p {
        font-size: 16px;
        color: rgba(255,255,255,0.82);
        line-height: 1.6;
        max-width: 340px;
        margin-bottom: 48px;
    }

    .auth-trust-badges {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: rgba(255,255,255,0.9);
        font-weight: 500;
    }

    .trust-badge i {
        width: 32px; height: 32px;
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; color: white;
        flex-shrink: 0;
    }

    .auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        overflow-y: auto;
        background: white;
    }

    .auth-form-wrapper {
        width: 100%;
        max-width: 420px;
    }

    .auth-form-header {
        margin-bottom: 36px;
    }

    .auth-form-header h2 {
        font-size: 28px;
        font-weight: 800;
        color: var(--color-text);
        margin-bottom: 6px;
        letter-spacing: -0.3px;
    }

    .auth-form-header p {
        font-size: 15px;
        color: var(--color-text-light);
        margin: 0;
    }

    .auth-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 24px;
    }

    .auth-alert i { margin-top: 1px; flex-shrink: 0; font-size: 15px; }

    .auth-alert-error  { background: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2; }
    .auth-alert-success { background: #E8F5E9; color: #2E7D32; border: 1px solid #C8E6C9; }

    .auth-form { display: flex; flex-direction: column; gap: 20px; }

    .form-field { display: flex; flex-direction: column; gap: 6px; }

    .form-field label {
        font-size: 14px;
        font-weight: 600;
        color: var(--color-text);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .forgot-link {
        font-size: 13px;
        font-weight: 500;
        color: var(--color-primary);
        text-decoration: none;
    }

    .forgot-link:hover { text-decoration: underline; }

    .field-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .field-icon {
        position: absolute;
        left: 14px;
        color: #B0BEC5;
        font-size: 14px;
        pointer-events: none;
        z-index: 1;
    }

    .field-wrap input {
        width: 100%;
        padding: 12px 44px 12px 42px;
        border: 2px solid #E8ECEF;
        border-radius: 10px;
        font-size: 15px;
        color: var(--color-text);
        background: #FAFBFC;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none;
        box-sizing: border-box;
    }

    .field-wrap input:focus {
        border-color: var(--color-primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(27,94,32,0.08);
    }

    .field-wrap input::placeholder { color: #B0BEC5; }

    .toggle-password {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        color: #B0BEC5;
        font-size: 14px;
        padding: 4px;
        transition: color 0.15s;
    }

    .toggle-password:hover { color: var(--color-text); }

    .form-remember { margin: -4px 0; }

    .remember-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        color: var(--color-text-light);
        user-select: none;
    }

    .remember-label input[type="checkbox"] { display: none; }

    .remember-box {
        width: 18px; height: 18px;
        border: 2px solid #D0D7DE;
        border-radius: 5px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s;
        background: white;
    }

    .remember-label input:checked + .remember-box {
        background: var(--color-primary);
        border-color: var(--color-primary);
    }

    .remember-label input:checked + .remember-box::after {
        content: '';
        width: 5px; height: 9px;
        border: 2px solid white;
        border-top: none; border-left: none;
        transform: rotate(45deg) translate(-1px, -1px);
        display: block;
    }

    .auth-submit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 14px 24px;
        background: var(--color-primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
        margin-top: 4px;
    }

    .auth-submit-btn:hover {
        background: #1B5E20;
        box-shadow: 0 4px 16px rgba(27,94,32,0.3);
        transform: translateY(-1px);
    }

    .auth-submit-btn:active { transform: translateY(0); }

    .auth-divider {
        text-align: center;
        font-size: 14px;
        color: var(--color-text-light);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding-top: 4px;
    }

    .auth-divider a {
        color: var(--color-primary);
        font-weight: 700;
        text-decoration: none;
    }

    .auth-divider a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        body { overflow: auto; }
        .auth-page { flex-direction: column; height: auto; min-height: 100vh; }
        .auth-left { width: 100%; padding: 36px 24px; min-height: auto; }
        .auth-left::before { display: none; }
        .auth-logo { margin-bottom: 32px; }
        .auth-tagline h1 { font-size: 28px; margin-bottom: 12px; }
        .auth-tagline p { font-size: 14px; margin-bottom: 28px; }
        .auth-right { padding: 32px 24px; }
    }
</style>
@endpush

@push('scripts')
<script>
function togglePassword(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
@endpush
@endsection