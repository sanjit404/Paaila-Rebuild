@extends('layouts.app')

@section('title', 'Card Payment')

@section('content')
<section class="section" style="background: var(--color-bg);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <div class="text-center" style="margin-bottom: var(--space-xl);">
                        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">
                            Pay with Card
                        </h2>
                        <p style="color: var(--color-text-light); margin: 0;">
                            Secure payment powered by Stripe
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

                    {{-- Error message --}}
                    <div id="payment-error" style="display:none; color: #dc3545; margin-bottom: var(--space-md); padding: var(--space-md); background: #fff0f0; border-radius: var(--radius-md);"></div>

                    {{-- Stripe Card Element --}}
                    <div style="margin-bottom: var(--space-lg);">
                        <label style="display: block; font-weight: 600; margin-bottom: var(--space-sm);">Card Details</label>
                        <div id="card-element" style="padding: 12px; border: 1px solid #E0E0E0; border-radius: var(--radius-md); background: white;">
                        </div>
                    </div>

                    <button id="pay-button" class="btn btn-lg btn-block"
                        style="background: var(--color-primary); color: white; padding: var(--space-lg); width: 100%; border: none; border-radius: var(--radius-md); cursor: pointer; font-size: 16px; font-weight: 600;">
                        <span id="pay-button-text">
                            <i class="fas fa-lock"></i>
                            Pay Rs. {{ number_format($booking->total_amount, 2) }}
                        </span>
                        <span id="pay-button-loading" style="display:none;">Processing...</span>
                    </button>

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

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ $publicKey }}');
    const elements = stripe.elements();

    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': { color: '#aab7c4' }
            },
            invalid: { color: '#9e2146' }
        }
    });

    cardElement.mount('#card-element');

    document.getElementById('pay-button').addEventListener('click', async function () {
        const button = this;
        button.disabled = true;
        document.getElementById('pay-button-text').style.display = 'none';
        document.getElementById('pay-button-loading').style.display = 'inline';
        document.getElementById('payment-error').style.display = 'none';

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            document.getElementById('payment-error').style.display = 'block';
            document.getElementById('payment-error').textContent = error.message;
            button.disabled = false;
            document.getElementById('pay-button-text').style.display = 'inline';
            document.getElementById('pay-button-loading').style.display = 'none';
            return;
        }

        const response = await fetch('{{ route('payment.stripe.verify', $booking) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ payment_method_id: paymentMethod.id })
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            document.getElementById('payment-error').style.display = 'block';
            document.getElementById('payment-error').textContent = data.message;
            button.disabled = false;
            document.getElementById('pay-button-text').style.display = 'inline';
            document.getElementById('pay-button-loading').style.display = 'none';
        }
    });
</script>
@endpush
@endsection