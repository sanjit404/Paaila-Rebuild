@extends('layouts.app')

@section('title', 'eSewa Payment')

@section('content')
<section class="section" style="background: var(--color-bg);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <div class="text-center" style="margin-bottom: var(--space-xl);">
                        <img src="https://esewa.com.np/common/images/esewa-logo.png" alt="eSewa" style="height: 50px; margin-bottom: var(--space-md);">
                        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">
                            Pay with eSewa
                        </h2>
                        <p style="color: var(--color-text-light); margin: 0;">
                            Secure payment gateway
                        </p>
                    </div>

                    <!-- Booking Summary -->
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

                    <!-- eSewa Form -->
                    <form action="https://uat.esewa.com.np/epay/main" method="POST" id="esewaForm">
                        <input type="hidden" name="tAmt" value="{{ $booking->total_amount }}">
                        <input type="hidden" name="amt" value="{{ $booking->total_amount }}">
                        <input type="hidden" name="txAmt" value="0">
                        <input type="hidden" name="psc" value="0">
                        <input type="hidden" name="pdc" value="0">
                        <input type="hidden" name="scd" value="{{ $esewaConfig['merchant_code'] }}">
                        <input type="hidden" name="pid" value="{{ $booking->booking_number }}">
                        <input type="hidden" name="su" value="{{ $esewaConfig['success_url'] }}">
                        <input type="hidden" name="fu" value="{{ $esewaConfig['failure_url'] }}">

                        <button type="submit" class="btn btn-cta btn-lg btn-block">
                            <i class="fas fa-wallet"></i>
                            Proceed to eSewa
                        </button>
                    </form>

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