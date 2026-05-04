@extends('layouts.app')

@section('title', 'Card Payment')

@section('content')
<section class="section" style="background: var(--color-bg);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <div class="text-center" style="margin-bottom: var(--space-xl);">
                        <div style="display: flex; justify-content: center; gap: var(--space-md); margin-bottom: var(--space-md);">
                            <i class="fab fa-cc-visa" style="font-size: 40px; color: #1A1F71;"></i>
                            <i class="fab fa-cc-mastercard" style="font-size: 40px; color: #EB001B;"></i>
                            <i class="fab fa-cc-amex" style="font-size: 40px; color: #006FCF;"></i>
                        </div>
                        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">
                            Card Payment
                        </h2>
                        <p style="color: var(--color-text-light); margin: 0;">
                            Secure payment via Stripe
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

                    <form method="POST" action="{{ route('payment.stripe.process', $booking) }}" id="stripeForm">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">Card Number</label>
                            <div id="card-element" style="padding: 12px; border: 2px solid #E0E0E0; border-radius: var(--radius-md);">
                            </div>
                            <div id="card-errors" class="form-error" style="margin-top: 8px;"></div>
                        </div>

                        <input type="hidden" name="stripeToken" id="stripeToken">

                        <button type="submit" class="btn btn-cta btn-lg btn-block" id="submitButton">
                            <i class="fas fa-lock"></i>
                            Pay Rs. {{ number_format($booking->total_amount, 2) }}
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

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ $stripeConfig["publishable_key"] }}');
    var elements = stripe.elements();

    var cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                fontFamily: '"Inter", sans-serif',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });

    cardElement.mount('#card-element');

    cardElement.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var form = document.getElementById('stripeForm');
    var submitButton = document.getElementById('submitButton');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const {token, error} = await stripe.createToken(cardElement);

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-lock"></i> Pay Rs. {{ number_format($booking->total_amount, 2) }}';
        } else {
            document.getElementById('stripeToken').value = token.id;
            form.submit();
        }
    });
</script>
@endpush
@endsection