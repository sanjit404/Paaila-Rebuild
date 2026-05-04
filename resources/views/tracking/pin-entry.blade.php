@extends('layouts.app')

@section('title', 'Track a Tour')

@section('content')
<div class="container">
    <div class="pin-entry-wrapper">
        <div class="pin-entry-card">

            <div class="pin-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>

            <h1>Track Your Loved One's Journey</h1>
            <p class="subtitle">Enter the 6-digit PIN shared by the traveler to view their real-time location</p>

            @if($errors->any())
                <div style="background: #FFEBEE; border: 1px solid #FFCDD2; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; color: #C62828; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Invalid PIN</strong>
                    </div>
                    @foreach($errors->all() as $error)
                        <div style="font-size: 14px;">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div style="background: #FFEBEE; border: 1px solid #FFCDD2; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; color: #C62828;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('tracking.verify.pin') }}" class="pin-form" id="pinForm">
                @csrf

                <div class="pin-input-group">
                    <input
                        type="text"
                        name="pin"
                        id="pinInput"
                        maxlength="6"
                        placeholder="000000"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                        autofocus
                        class="pin-input"
                        value="{{ old('pin') }}"
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-large" id="submitBtn">
                    <i class="fas fa-search"></i> Track Location
                </button>
            </form>

            <div class="help-section">
                <h3><i class="fas fa-question-circle"></i> How it works</h3>
                <div class="help-steps">
                    <div class="help-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <strong>Get PIN from Traveler</strong>
                            <p>The traveler receives a 6-digit PIN when they start their tour</p>
                        </div>
                    </div>
                    <div class="help-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <strong>Enter PIN Above</strong>
                            <p>Type the 6-digit code in the input field</p>
                        </div>
                    </div>
                    <div class="help-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <strong>View Live Location</strong>
                            <p>See real-time location updates and tour progress</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-cards">
                <div class="info-card">
                    <i class="fas fa-lock"></i>
                    <strong>Secure</strong>
                    <p>PIN-based access ensures privacy</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <strong>Real-time</strong>
                    <p>Location updates every 5 seconds</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-mobile-alt"></i>
                    <strong>Any Device</strong>
                    <p>Works on phone, tablet, or computer</p>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    .pin-entry-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    .pin-entry-card {
        background: white;
        padding: 3rem;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        max-width: 600px;
        width: 100%;
        text-align: center;
    }

    .pin-icon {
        width: 100px; height: 100px;
        margin: 0 auto 2rem;
        background: linear-gradient(135deg, #3fea41 0%, #25a52f 100%);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; color: white;
        animation: pulse-icon 2s infinite;
    }

    @keyframes pulse-icon {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.05); }
    }

    .pin-entry-card h1   { font-size: 2rem; margin-bottom: 0.5rem; color: #333; }
    .subtitle            { color: #666; margin-bottom: 2rem; font-size: 1.1rem; }
    .pin-form            { margin-bottom: 3rem; }
    .pin-input-group     { margin-bottom: 2rem; }

    .pin-input {
        width: 100%; max-width: 300px;
        padding: 1.5rem;
        font-size: 2.5rem;
        text-align: center;
        letter-spacing: 0.5rem;
        border: 3px solid #e0e0e0;
        border-radius: 16px;
        font-weight: 700;
        color: #348033;
        transition: all 0.3s ease;
        display: block;
        margin: 0 auto;
    }

    .pin-input:focus {
        outline: none;
        border-color: #14ba25;
        box-shadow: 0 0 0 4px rgba(20,186,37,0.15);
    }

    .pin-input::placeholder { color: #e0e0e0; }

    .pin-input.has-error { border-color: #D32F2F; }

    .btn-large {
        padding: 1rem 2.5rem;
        font-size: 1.1rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .btn-large:disabled { opacity: 0.6; cursor: not-allowed; }

    .help-section {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: left;
    }

    .help-section h3  { text-align: center; margin-bottom: 1.5rem; color: #228113; }
    .help-steps       { display: flex; flex-direction: column; gap: 1rem; }
    .help-step        { display: flex; gap: 1rem; align-items: flex-start; }

    .step-number {
        flex-shrink: 0;
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #23c957 0%, #1b7e30 100%);
        color: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1.2rem;
    }

    .step-content strong { display: block; margin-bottom: 0.25rem; color: #333; }
    .step-content p      { color: #666; font-size: 0.95rem; margin: 0; }

    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .info-card {
        padding: 1.5rem 1rem;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .info-card i      { font-size: 2rem; color: #249732; margin-bottom: 0.5rem; display: block; }
    .info-card strong { display: block; margin-bottom: 0.25rem; color: #333; }
    .info-card p      { font-size: 0.85rem; color: #666; margin: 0; }

    @media (max-width: 768px) {
        .pin-entry-card { padding: 2rem 1.5rem; }
        .pin-entry-card h1 { font-size: 1.5rem; }
        .pin-input { font-size: 2rem; letter-spacing: 0.3rem; }
        .info-cards { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
    const pinInput  = document.getElementById('pinInput');
    const pinForm   = document.getElementById('pinForm');
    const submitBtn = document.getElementById('submitBtn');

    pinInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        this.classList.remove('has-error');

        if (this.value.length === 6) {
            submitBtn.disabled    = true;
            submitBtn.innerHTML   = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            setTimeout(() => pinForm.submit(), 300);
        }
    });

    pinInput.addEventListener('paste', function (e) {
        e.preventDefault();
        const text   = (e.clipboardData || window.clipboardData).getData('text');
        const digits = text.replace(/[^0-9]/g, '').substring(0, 6);
        this.value   = digits;
        if (digits.length === 6) {
            submitBtn.disabled  = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            setTimeout(() => pinForm.submit(), 300);
        }
    });

    pinForm.addEventListener('submit', function (e) {
        const v = pinInput.value;
        if (!/^\d{6}$/.test(v)) {
            e.preventDefault();
            pinInput.classList.add('has-error');
            pinInput.focus();
        }
    });

    @if($errors->any())
        pinInput.classList.add('has-error');
    @endif
</script>
@endpush
@endsection
