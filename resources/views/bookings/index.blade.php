@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<section class="section">
    <div class="container">
        <div style="margin-bottom: var(--space-xl);">
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">My Bookings</h1>
            <p style="color: var(--color-text-light); margin: 0;">Track your trek bookings and start your journey</p>
        </div>

        @if($bookings->count() > 0)
            <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                @foreach($bookings as $booking)
                    <div class="card">
                        <div class="card-body">
                            <div class="flex-between" style="margin-bottom: var(--space-md); flex-wrap: wrap; gap: var(--space-md);">
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-xs);">
                                        {{ $booking->tourPackage->name }}
                                    </h3>
                                    <p style="font-size: 14px; color: var(--color-text-light); margin: 0;">
                                        Booking #{{ $booking->id }} • {{ $booking->tour_date->format('M d, Y') }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                @if($booking->status === 'pending')
                                    <span class="badge badge-warning" style="padding: 8px 16px; font-size: 14px;">
                                        <i class="fas fa-clock"></i> Pending Payment
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge badge-success" style="padding: 8px 16px; font-size: 14px;">
                                        <i class="fas fa-check-circle"></i> Confirmed
                                    </span>
                                @elseif($booking->status === 'active')
                                    <span class="badge badge-primary" style="padding: 8px 16px; font-size: 14px; background: #E3F2FD; color: #1976D2;">
                                        <i class="fas fa-hiking"></i> In Progress
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge" style="padding: 8px 16px; font-size: 14px; background: #F5F5F5; color: #666;">
                                        <i class="fas fa-flag-checkered"></i> Completed
                                    </span>
                                @else
                                    <span class="badge badge-error" style="padding: 8px 16px; font-size: 14px;">
                                        <i class="fas fa-times-circle"></i> Cancelled
                                    </span>
                                @endif
                            </div>

                            <div class="flex-between" style="flex-wrap: wrap; gap: var(--space-lg);">
                                <!-- Booking Details -->
                                <div class="flex gap-lg" style="flex-wrap: wrap; font-size: 14px; color: var(--color-text-light);">
                                    <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                        <i class="fas fa-users" style="color: var(--color-primary);"></i>
                                        <span>{{ $booking->participants }} {{ Str::plural('trekker', $booking->participants) }}</span>
                                    </div>
                                    <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                        <i class="fas fa-rupee-sign" style="color: var(--color-primary);"></i>
                                        <span style="font-weight: 600; color: var(--color-text);">Rs. {{ number_format($booking->total_amount, 0) }}</span>
                                    </div>
                                    @if($booking->status === 'active' && $booking->trackingPin)
                                        <div class="flex" style="gap: var(--space-xs); align-items: center;">
                                            <i class="fas fa-key" style="color: var(--color-primary);"></i>
                                            <span style="font-weight: 700; color: var(--color-primary);">PIN: {{ $booking->trackingPin->pin }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-sm">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary">
                                        View Details
                                    </a>

                                    @if($booking->status === 'active')
                                        <a href="{{ route('tracking.traveler', $booking) }}" class="btn btn-cta">
                                            <i class="fas fa-location-arrow"></i>
                                            Track Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <i class="fas fa-hiking" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-lg);"></i>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">No Bookings Yet</h3>
                    <p style="color: var(--color-text-light); margin-bottom: var(--space-xl);">
                        Start your trekking adventure by booking a trek
                    </p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i>
                        Browse Treks
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection