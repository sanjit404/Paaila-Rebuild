@extends('layouts.app')

@section('title', 'Test Payment')

@section('content')
<div class="container">
    <div class="test-payment-wrapper">
        <div class="test-payment-card">
            <div class="test-badge">
                <i class="fas fa-flask"></i> TEST MODE
            </div>

            <h1>Payment Simulation</h1>
            <p>This is a test environment. No real payment will be processed.</p>

            <div class="booking-summary">
                <h3>Booking Details</h3>
                <div class="summary-row">
                    <span>Booking Number:</span>
                    <strong>{{ $booking->booking_number }}</strong>
                </div>
                <div class="summary-row">
                    <span>Tour:</span>
                    <strong>{{ $booking->tourPackage->name }}</strong>
                </div>
                <div class="summary-row">
                    <span>Amount:</span>
                    <strong>Rs. {{ number_format($booking->total_amount, 2) }}</strong>
                </div>
                <div class="summary-row">
                    <span>Payment Method:</span>
                    <strong>{{ ucfirst($booking->payment_method) }}</strong>
                </div>
            </div>

            <div class="payment-actions">
                <form method="POST" action="{{ route('payment.test.process', $booking) }}">
                    @csrf
                    <input type="hidden" name="status" value="success">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Simulate Successful Payment
                    </button>
                </form>

                <form method="POST" action="{{ route('payment.test.process', $booking) }}">
                    @csrf
                    <input type="hidden" name="status" value="failed">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle"></i> Simulate Failed Payment
                    </button>
                </form>

                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>

            <div class="test-info">
                <i class="fas fa-info-circle"></i>
                <p>In production, this would redirect to the actual payment gateway ({{ ucfirst($booking->payment_method) }})</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .test-payment-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    .test-payment-card {
        background: white;
        padding: 3rem;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        max-width: 600px;
        width: 100%;
        text-align: center;
    }

    .test-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        background: #ffc107;
        color: #000;
        border-radius: 20px;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .test-payment-card h1 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .test-payment-card > p {
        color: #666;
        margin-bottom: 2rem;
    }

    .booking-summary {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: left;
    }

    .booking-summary h3 {
        text-align: center;
        margin-bottom: 1rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .payment-actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .payment-actions .btn {
        width: 100%;
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        color: white;
    }

    .test-info {
        background: #e3f2fd;
        padding: 1rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-align: left;
    }

    .test-info i {
        font-size: 1.5rem;
        color: #1976d2;
    }

    .test-info p {
        margin: 0;
        color: #1565c0;
        font-size: 0.9rem;
    }
</style>
@endpush
@endsection