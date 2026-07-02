@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px); display: flex; align-items: center; justify-content: center; padding: var(--space-xl);">
    <div style="max-width: 480px; width: 100%;">
        <div class="card">
            <div class="card-body" style="padding: var(--space-xl);">

                <div style="text-align: center; margin-bottom: var(--space-xl);">
                    <div style="width: 72px; height: 72px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md);">
                        <i class="fas fa-key" style="font-size: 28px; color: var(--color-primary);"></i>
                    </div>
                    <h1 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">Forgot your password?</h1>
                    <p style="color: var(--color-text-light); margin: 0;">
                        Enter your email and we'll send you a reset link.
                    </p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success" style="margin-bottom: var(--space-lg);">
                        <i class="fas fa-check-circle"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom: var(--space-lg);">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="resetForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #B0BEC5; pointer-events: none;"></i>
                            <input
                                type="email"
                                name="email"
                                class="form-input"
                                style="padding-left: 42px;"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="btn">
                        <i class="fas fa-paper-plane" id="send"></i>
                        <i class="fas fa-spinner fa-spin-pulse" style="display:none;" id="load"></i>
                        <span id="text">Send Reset Email</span>
                    </button>
                </form>

                <div style="text-align: center; margin-top: var(--space-lg);">
                    <a href="{{ route('login') }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px;">
                        <i class="fas fa-arrow-left"></i> Back to Sign In
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
<script>
const btn = document.getElementById('btn');
const form = document.getElementById('resetForm');
const send = document.getElementById('send');
const load = document.getElementById('load');
const text = document.getElementById('text');

const COOLDOWN = 120;

let timer = localStorage.getItem('forgot_timer');

if (timer && timer > Date.now()) {
    startCountdown(Math.ceil((timer - Date.now()) / 1000));
}

form.addEventListener('submit', function () {
    btn.disabled = true;
    send.style.display = 'none';
    load.style.display = 'inline-block';

    localStorage.setItem(
        'forgot_timer',
        Date.now() + COOLDOWN * 1000
    );
});

function startCountdown(seconds) {
    btn.disabled = true;
    send.style.display = 'none';
    load.style.display = 'none';

    text.textContent = `Resend in ${seconds}s`;

    const interval = setInterval(() => {
        seconds--;

        if (seconds <= 0) {
            clearInterval(interval);
            btn.disabled = false;
            send.style.display = 'inline-block';
            text.textContent = 'Resend Reset Email';
            localStorage.removeItem('forgot_timer');
            return;
        }

        text.textContent = `Resend in ${seconds}s`;
    }, 1000);
}
</script>
@endsection