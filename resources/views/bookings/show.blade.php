@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="container">
    <div class="booking-detail-wrapper">
        <div class="booking-header">
            <div class="status-badge status-{{ $booking->status }}">
                <i class="fas fa-{{ $booking->status === 'paid' ? 'check-circle' : ($booking->status === 'active' ? 'play-circle' : 'clock') }}"></i>
                {{ ucfirst($booking->status) }}
            </div>
            <h1>Booking #{{ $booking->booking_number }}</h1>
            <p>{{ $booking->tourPackage->name }}</p>
        </div>

        <div class="booking-details-grid">
            <div class="detail-card">
                <h3><i class="fas fa-info-circle"></i> Booking Information</h3>
                <div class="detail-row">
                    <span>Tour Date:</span>
                    <strong>{{ $booking->tour_date->format('M d, Y') }}</strong>
                </div>
                <div class="detail-row">
                    <span>Participants:</span>
                    <strong>{{ $booking->participants }}</strong>
                </div>
                <div class="detail-row">
                    <span>Total Amount:</span>
                    <strong>Rs. {{ number_format($booking->total_amount, 2) }}</strong>
                </div>
                <div class="detail-row">
                    <span>Payment Method:</span>
                    <strong>{{ ucfirst($booking->payment_method) }}</strong>
                </div>
                <div class="detail-row">
                    <span>Booked On:</span>
                    <strong>{{ $booking->created_at->format('M d, Y h:i A') }}</strong>
                </div>
            </div>

            @if($booking->status === 'paid' && $booking->trackingPin)
            <div class="detail-card pin-card">
                <h3><i class="fas fa-key"></i> Tracking PIN</h3>
                <p>Share this PIN with parents/guardians to track your tour:</p>
                <div class="pin-display">
                    {{ $booking->trackingPin->pin }}
                </div>
                <p class="pin-note">
                    <i class="fas fa-clock"></i> 
                    Valid until {{ $booking->trackingPin->expires_at->format('M d, Y') }}
                </p>
            </div>
            @endif
        </div>

        <div class="actions">
            @if($booking->status === 'confirmed' && !$booking->started_at)
                <form method="POST" action="{{ route('bookings.start', $booking) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play"></i> Start Tour
                    </button>
                </form>
            @endif

            @if($booking->status === 'active')
                <a href="{{ route('tracking.traveler', $booking) }}" class="btn btn-primary">
                    <i class="fas fa-map-marked-alt"></i> View Live Tracking
                </a>
            @endif

            @if($booking->status === 'pending')
                <form method="POST" action="{{ route('bookings.start', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel Booking
                    </button>
                </form>
            @endif

            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> My Bookings
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .booking-detail-wrapper {
        max-width: 800px;
        margin: 2rem auto;
    }

    .booking-header {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
    }

    .status-active {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-completed {
        background: #e2e3e5;
        color: #383d41;
    }

    .booking-header h1 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .booking-details-grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .detail-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .detail-card h3 {
        margin-bottom: 1.5rem;
        color: #333;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .pin-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .pin-display {
        font-size: 3rem;
        font-weight: 700;
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        letter-spacing: 0.5rem;
        margin: 1rem 0;
    }

    .pin-note {
        text-align: center;
        opacity: 0.9;
    }

    .actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .actions .btn {
        flex: 1;
        min-width: 200px;
    }
</style>
@endpush
@endsection