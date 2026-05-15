@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="error-container">
    <div class="error-left">
        <div class="error-left-inner">
            <a href="{{ route('home') }}" class="error-logo">
                <img src="{{ asset('images/paailaLogo.png') }}" alt="Paaila Logo">
                <span>Paaila</span>
            </a>

            <div class="error-tagline">
                <h1>Oops, something broke.</h1>
                <p>We’re having trouble processing your request right now. Please try again in a moment.</p>
            </div>
        </div>
    </div>

    <div class="error-right">
        <div class="error-card">
            <div class="error-code">
                <i class="fa-solid fa-triangle-exclamation error-digit"></i>
            </div>

            <h2 class="error-title">Server Error</h2>

            <p class="error-message">
                Something went wrong on our side. Please refresh the page or come back later.
            </p>

            <a href="{{ route('home') }}" class="error-link">
                <i class="fa-solid fa-arrow-left"></i>
                Go Back Home
            </a>

            <div class="error-footer">
                If this keeps happening, please contact support.
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    :root {
        --color-primary: #1B5E20;
        --color-primary-light: #2E7D32;
        --color-primary-dark: #144416;
        --color-bg: #F9FDF9;
        --color-white: #FFFFFF;
        --color-text: #263238;
        --color-text-light: #546E7A;
        --color-border: #E0E0E0;
        --font-heading: 'Poppins', sans-serif;
        --font-body: 'Inter', sans-serif;
        --space-sm: 8px;
        --space-md: 16px;
        --space-lg: 24px;
        --space-xl: 32px;
        --space-2xl: 48px;
        --radius-md: 8px;
        --radius-lg: 12px;
    }

    .error-container {
        min-height: 100vh;
        display: flex;
        background: var(--color-bg);
    }

    .error-left {
        width: 44%;
        flex-shrink: 0;
        background: var(--color-primary);
        background-image:
            radial-gradient(ellipse at 20% 80%, rgba(255,255,255,0.08) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(0,0,0,0.15) 0%, transparent 50%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding: 48px;
    }

    .error-left::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 55%;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 400' preserveAspectRatio='none'%3E%3Cpath d='M0 400 L0 280 L120 180 L240 240 L360 100 L480 200 L600 80 L720 160 L840 50 L960 140 L1080 90 L1200 170 L1320 120 L1440 200 L1440 400Z' fill='rgba(0,0,0,0.12)'/%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom;
        pointer-events: none;
    }

    .error-left-inner {
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .error-logo {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        margin-bottom: 32px;
    }

    .error-logo img {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: block;
    }

    .error-logo span {
        font-size: 22px;
        font-weight: 800;
        color: white;
        letter-spacing: -0.3px;
    }

    .error-tagline h1 {
        font-family: var(--font-heading);
        font-size: clamp(32px, 4vw, 48px);
        font-weight: 800;
        color: white;
        line-height: 1.15;
        margin-bottom: 16px;
        letter-spacing: -0.5px;
    }

    .error-tagline p {
        font-family: var(--font-body);
        font-size: 15px;
        color: rgba(255,255,255,0.82);
        line-height: 1.6;
        margin-bottom: 32px;
    }

    .error-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: linear-gradient(135deg, #F9FDF9 0%, #F0F5F0 100%);
        border-left: 1px solid var(--color-border);
    }

    .error-card {
        width: 100%;
        max-width: 420px;
        padding: var(--space-2xl) var(--space-lg);
        background: var(--color-white);
        border-radius: var(--radius-lg);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .error-code {
        font-size: 86px;
        font-weight: 800;
        margin-bottom: 8px;
        color: var(--color-primary-dark);
        line-height: 1;
    }

    .error-digit {
        animation: beat 1.3s infinite;
    }

    .error-title {
        font-family: var(--font-heading);
        font-size: 28px;
        color: var(--color-text);
        margin: var(--space-sm) 0 var(--space-md);
    }

    .error-message {
        font-family: var(--font-body);
        font-size: 15px;
        color: var(--color-text-light);
        margin-bottom: var(--space-lg);
        line-height: 1.6;
    }

    .error-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: var(--radius-md);
        border: 1px solid var(--color-primary);
        background: var(--color-white);
        color: var(--color-primary);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .error-link:hover {
        background: var(--color-primary-light);
        color: var(--color-white);
        box-shadow: 0 4px 16px rgba(27,94,32,0.3);
        transform: translateY(-1px);
    }

    .error-footer {
        margin-top: var(--space-lg);
        font-size: 13px;
        color: var(--color-text-light);
    }

    @keyframes beat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    @media (max-width: 768px) {
        .error-container {
            flex-direction: column;
        }

        .error-left {
            width: 100%;
            padding: 32px 24px;
        }

        .error-left::before {
            display: none;
        }

        .error-right {
            padding: 32px 24px;
            border-left: 0;
        }

        .error-card {
            padding: var(--space-lg) var(--space-md);
        }

        .error-code {
            font-size: 68px;
        }

        .error-title {
            font-size: 24px;
        }
    }
</style>
@endpush