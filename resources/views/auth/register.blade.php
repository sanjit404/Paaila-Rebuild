@extends('layouts.app')

@section('title', 'Create Account — Paaila')

@section('content')
<div class="auth-page">

    <div class="auth-left">
        <div class="auth-left-inner">
            <a href="{{ route('home') }}" class="auth-logo">                
                    <div class="navbar-logo"><img src="{{ asset('images/paailaLogo.png')}}" alt="Logo" style="height:100px;" class="navbar-logo"></div>
                <span>Paaila</span>
            </a>

            <div class="auth-tagline">
                <h1>Your next<br>adventure<br>starts here.</h1>
                <p>Join thousands of trekkers exploring Nepal with real-time GPS safety and personalised recommendations.</p>
            </div>

            <div class="reg-steps">
                <div class="reg-step active">
                    <div class="reg-step-num">1</div>
                    <div class="reg-step-content">
                        <div class="reg-step-title">Create your account</div>
                        <div class="reg-step-sub">Name, email & password</div>
                    </div>
                </div>
                <div class="reg-step-line"></div>
                <div class="reg-step">
                    <div class="reg-step-num">2</div>
                    <div class="reg-step-content">
                        <div class="reg-step-title">Set your preferences</div>
                        <div class="reg-step-sub">Trek types, budget, difficulty</div>
                    </div>
                </div>
                <div class="reg-step-line"></div>
                <div class="reg-step">
                    <div class="reg-step-num">3</div>
                    <div class="reg-step-content">
                        <div class="reg-step-title">Get recommendations</div>
                        <div class="reg-step-sub">Personalised treks just for you</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrapper">

            <div class="auth-form-header">
                <h2>Create your account</h2>
                <p>Free to join. No credit card needed.</p>
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

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-field">
                    <label for="name">Full name</label>
                    <div class="field-wrap">
                        <i class="fas fa-user field-icon"></i>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Your full name"
                            required
                            autofocus
                            autocomplete="name"
                        >
                    </div>
                </div>

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
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="At least 8 characters"
                            required
                            autocomplete="new-password"
                            oninput="checkStrength(this.value)"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                        <font size="2px">Use at least one uppercase letter and unique symobl.</font>

                    <div class="strength-bar">
                        <div class="strength-track">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>
                </div>

                <div class="form-field">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repeat your password"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-terms">
                    <label class="terms-label">
                        <input type="checkbox" name="terms" required>
                        <span class="terms-box"></span>
                        <span>I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Create Account</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="auth-divider">
                    <span>Already have an account?</span>
                    <a href="{{ route('login') }}">Sign in</a>
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
        height: 50%;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 400' preserveAspectRatio='none'%3E%3Cpath d='M0 400 L0 300 L180 180 L300 250 L420 120 L540 200 L660 60 L780 160 L900 40 L1020 130 L1140 80 L1260 160 L1440 100 L1440 400Z' fill='rgba(0,0,0,0.10)'/%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom;
        pointer-events: none;
    }

    .auth-left-inner { position: relative; z-index: 1; width: 100%; }

    .auth-logo {
        display: inline-flex; align-items: center; gap: 12px;
        text-decoration: none; margin-bottom: 52px;
    }

    .auth-logo-icon {
    padding:3px;
    background: rgba(13, 115, 11, 0.51); border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: white;
    }

    .auth-logo span { font-size: 22px; font-weight: 800; color: white; }

    .auth-tagline h1 {
        font-size: clamp(28px, 3vw, 42px);
        font-weight: 800; color: white;
        line-height: 1.15; margin-bottom: 16px;
        letter-spacing: -0.5px;
    }

    .auth-tagline p {
        font-size: 15px; color: rgba(255,255,255,0.82);
        line-height: 1.6; max-width: 340px; margin-bottom: 44px;
    }

    .reg-steps { display: flex; flex-direction: column; gap: 0; }

    .reg-step {
        display: flex; align-items: center; gap: 14px;
    }

    .reg-step-num {
        width: 32px; height: 32px; border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.35);
        color: rgba(255,255,255,0.6);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; flex-shrink: 0;
        transition: all 0.2s;
    }

    .reg-step.active .reg-step-num {
        background: rgba(255,255,255,0.25);
        border-color: white; color: white;
    }

    .reg-step-title {
        font-size: 14px; font-weight: 600;
        color: rgba(255,255,255,0.9);
    }

    .reg-step-sub { font-size: 12px; color: rgba(255,255,255,0.55); }

    .reg-step-line {
        width: 2px; height: 24px;
        background: rgba(255,255,255,0.2);
        margin-left: 15px;
    }

    .auth-right {
        flex: 1;
        display: flex; align-items: center; justify-content: center;
        padding: 40px; overflow-y: auto; background: white;
    }

    .auth-form-wrapper { width: 100%; max-width: 420px; }

    .auth-form-header { margin-bottom: 28px; }

    .auth-form-header h2 {
        font-size: 26px; font-weight: 800; color: var(--color-text);
        margin-bottom: 6px; letter-spacing: -0.3px;
    }

    .auth-form-header p { font-size: 14px; color: var(--color-text-light); margin: 0; }

    .auth-alert {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px; border-radius: 10px;
        font-size: 13px; margin-bottom: 20px;
    }

    .auth-alert i { margin-top: 1px; flex-shrink: 0; }
    .auth-alert-error  { background: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2; }
    .auth-alert-success { background: #E8F5E9; color: #2E7D32; border: 1px solid #C8E6C9; }

    .auth-form { display: flex; flex-direction: column; gap: 16px; }

    .form-field { display: flex; flex-direction: column; gap: 5px; }

    .form-field label {
        font-size: 13px; font-weight: 600; color: var(--color-text);
    }

    .field-wrap { position: relative; display: flex; align-items: center; }

    .field-icon {
        position: absolute; left: 14px; color: #B0BEC5;
        font-size: 14px; pointer-events: none; z-index: 1;
    }

    .field-wrap input {
        width: 100%;
        padding: 11px 44px 11px 42px;
        border: 2px solid #E8ECEF;
        border-radius: 10px; font-size: 14px;
        color: var(--color-text); background: #FAFBFC;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none; box-sizing: border-box;
    }

    .field-wrap input:focus {
        border-color: var(--color-primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(27,94,32,0.08);
    }

    .field-wrap input::placeholder { color: #B0BEC5; }

    .toggle-password {
        position: absolute; right: 12px;
        background: none; border: none; cursor: pointer;
        color: #B0BEC5; font-size: 14px; padding: 4px;
        transition: color 0.15s;
    }

    .toggle-password:hover { color: var(--color-text); }

    .strength-bar {
        display: flex; align-items: center; gap: 10px;
        margin-top: 6px;
    }

    .strength-track {
        flex: 1; height: 4px; background: #E0E0E0;
        border-radius: 2px; overflow: hidden;
    }

    .strength-fill {
        height: 100%; width: 0;
        border-radius: 2px;
        transition: width 0.3s ease, background 0.3s ease;
    }

    .strength-label { font-size: 12px; color: var(--color-text-light); white-space: nowrap; }

    .form-terms { margin: -2px 0; }

    .terms-label {
        display: flex; align-items: flex-start; gap: 10px;
        cursor: pointer; font-size: 13px;
        color: var(--color-text-light); user-select: none;
    }

    .terms-label input[type="checkbox"] { display: none; }

    .terms-box {
        width: 18px; height: 18px; flex-shrink: 0;
        border: 2px solid #D0D7DE; border-radius: 5px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.15s; background: white; margin-top: 1px;
    }

    .terms-label input:checked + .terms-box {
        background: var(--color-primary); border-color: var(--color-primary);
    }

    .terms-label input:checked + .terms-box::after {
        content: '';
        width: 5px; height: 9px;
        border: 2px solid white;
        border-top: none; border-left: none;
        transform: rotate(45deg) translate(-1px, -1px);
        display: block;
    }

    .terms-link { color: var(--color-primary); text-decoration: none; font-weight: 500; }
    .terms-link:hover { text-decoration: underline; }

    .auth-submit-btn {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        width: 100%; padding: 14px 24px;
        background: var(--color-primary); color: white;
        border: none; border-radius: 10px;
        font-size: 15px; font-weight: 700; cursor: pointer;
        transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
    }

    .auth-submit-btn:hover {
        background: #1B5E20;
        box-shadow: 0 4px 16px rgba(27,94,32,0.3);
        transform: translateY(-1px);
    }

    .auth-submit-btn:active { transform: translateY(0); }

    .auth-divider {
        text-align: center; font-size: 14px;
        color: var(--color-text-light);
        display: flex; align-items: center; justify-content: center;
        gap: 6px; padding-top: 4px;
    }

    .auth-divider a { color: var(--color-primary); font-weight: 700; text-decoration: none; }
    .auth-divider a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        body { overflow: auto; }
        .auth-page { flex-direction: column; height: auto; min-height: 100vh; }
        .auth-left { width: 100%; padding: 32px 24px; }
        .auth-left::before { display: none; }
        .auth-tagline h1 { font-size: 26px; }
        .auth-right { padding: 32px 24px; }
    }
</style>
@endpush

@push('scripts')
<script>
function togglePassword(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

function checkStrength(val) {
    var fill  = document.getElementById('strengthFill');
    var label = document.getElementById('strengthLabel');
    var score = 0;

    if (val.length >= 8)            score++;
    if (/[A-Z]/.test(val))          score++;
    if (/[0-9]/.test(val))          score++;
    if (/[^A-Za-z0-9]/.test(val))   score++;

    var levels = [
        { pct: '0%',   color: '#E0E0E0', text: '' },
        { pct: '25%',  color: '#EF5350', text: 'Weak' },
        { pct: '50%',  color: '#FFA726', text: 'Fair' },
        { pct: '75%',  color: '#66BB6A', text: 'Good' },
        { pct: '100%', color: '#2E7D32', text: 'Strong' },
    ];

    var lvl = val.length === 0 ? levels[0] : levels[score];
    fill.style.width      = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color;
}
</script>
@endpush
@endsection