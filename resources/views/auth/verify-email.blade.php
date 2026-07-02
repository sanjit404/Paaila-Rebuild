@extends('layouts.app')

@section('title', 'Verify Your Email')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px); display: flex; align-items: center; justify-content: center; padding: var(--space-xl);">
    <div style="max-width: 520px; width: 100%;">
        <div class="card">
            <div class="card-body" style="padding: var(--space-xl); text-align: center;">

                <div style="width: 80px; height: 80px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg);">
                    <i class="fas fa-envelope-open-text" style="font-size: 32px; color: var(--color-primary);"></i>
                </div>

                <h1 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">Check your email</h1>
                <p style="color: var(--color-text-light); margin-bottom: var(--space-xl);">
                    We sent a verification link to 
                    <span style="color:var(--color-primary-dark);font-weight:900;">
                        {{ auth()->user()->email }}
                    </span>
                    . Click it to activate your account before continuing.
                </p>

                @if(session('status') == 'verification-link-sent')
                    <div class="alert alert-success" style="margin-bottom: var(--space-lg);">
                        <i class="fas fa-check-circle"></i>
                        Verification link has been sent to your email address.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" id="verificationForm" style="margin-bottom: var(--space-lg);">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="btn">
                        <i class="fas fa-paper-plane" id="send"></i>
                        <i class="fas fa-spinner fa-spin-pulse" style="display:none;" id="load"></i>
                        <span id="text">Send Verification Email</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-sign-out-alt"></i>
                        Sign Out
                    </button>
                </form>

            </div>
        </div>
    </div>
</section>
<script>
const btn = document.getElementById('btn');
const form = document.getElementById('verificationForm');
const send = document.getElementById('send');
const load = document.getElementById('load');
const text = document.getElementById('text');

const COOLDOWN = 60;

let timer = localStorage.getItem('verification_timer');

if (timer && timer > Date.now()) {
    startCountdown(Math.ceil((timer - Date.now()) / 1000));
}

form.addEventListener('submit', function () {
    btn.disabled = true;
    send.style.display = 'none';
    load.style.display = 'inline-block';

    localStorage.setItem(
        'verification_timer',
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
            text.textContent = 'Resend Verification Email';
            localStorage.removeItem('verification_timer');
            return;
        }

        text.textContent = `Resend in ${seconds}s`;
    }, 1000);
}

setInterval(async () => {
    const response = await fetch('/email/verification-status', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const data = await response.json();

    if (data.verified) {
        location.reload(); // or window.location = '/dashboard';
    }
}, 5000);
</script>
@endsection