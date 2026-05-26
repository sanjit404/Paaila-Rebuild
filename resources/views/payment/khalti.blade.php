@extends('layouts.app')

@section('title', 'Khalti Payment')

@section('content')
<section class="section" style="background: var(--color-bg);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <div class="text-center" style="margin-bottom: var(--space-xl);">
                        <img src="https://khalti.com/static/images/logo1.png" alt="Khalti" style="height: 50px; margin-bottom: var(--space-md);">
                        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">
                            Pay with Khalti
                        </h2>
                        <p style="color: var(--color-text-light); margin: 0;">
                            Digital wallet payment
                        </p>
                    </div>

                    <div style="padding: var(--space-lg); background: #F5F5F5; border-radius: var(--radius-md); margin-bottom: var(--space-xl);">
                        <div class="flex-between" style="margin-bottom: var(--space-md);">
                            <span>Booking Number</span>
                            <span style="font-weight: 700;">{{ $booking->booking_number }}</span>
                        </div>
                        <div class="flex-between" style="margin-bottom: var(--space-md);">
                            <span>Trek</span>
                            <span style="font-weight: 600;">{{ $booking->tourPackage->name }}</span>
                        </div>
                        <div class="flex-between" style="padding-top: var(--space-md); border-top: 2px solid #E0E0E0;">
                            <span style="font-size: 16px; font-weight: 600;">Amount to Pay</span>
                            <span style="font-size: 24px; font-weight: 700; color: var(--color-primary);">
                                Rs. {{ number_format($booking->total_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    {{-- Show any errors --}}
                    @if(session('error'))
                        <div class="alert alert-danger" style="margin-bottom: var(--space-lg);">
                            {{ session('error') }}
                        </div>
                    @endif

                    <a href="{{ route('payment.khalti', $booking) }}"
                        class="btn btn-lg btn-block"
                        style="background: #5C2D91; color: white; padding: var(--space-lg); text-align: center; display: block; text-decoration: none; border-radius: var(--radius-md);">
                        <i class="fas fa-mobile-alt"></i>
                        Pay with Khalti
                    </a>

                    <div class="text-center" style="margin-top: var(--space-lg);">
                        <a href="{{ route('bookings.show', $booking) }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px;">
                            <i class="fas fa-arrow-left"></i> Cancel Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection