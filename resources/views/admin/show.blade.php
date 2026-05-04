@extends('layouts.app')

@section('title', 'Booking #' . $booking->id)

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <!-- Header -->
        <div style="margin-bottom: var(--space-xl);">
            <a href="{{ route('admin.bookings') }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px; margin-bottom: var(--space-md); display: inline-block;">
                <i class="fas fa-arrow-left"></i> Back to Bookings
            </a>
            <div class="flex-between" style="flex-wrap: wrap; gap: var(--space-md);">
                <div>
                    <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">Booking #{{ $booking->id }}</h1>
                    <p style="color: var(--color-text-light); margin: 0;">{{ $booking->tourPackage->name }}</p>
                </div>
                
                @if($booking->status === 'pending')
                    <span class="badge badge-warning" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-clock"></i> PENDING
                    </span>
                @elseif($booking->status === 'confirmed')
                    <span class="badge badge-success" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-check-circle"></i> CONFIRMED
                    </span>
                @elseif($booking->status === 'active')
                    <span class="badge" style="background: #E3F2FD; color: #1976D2; padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-hiking"></i> ACTIVE
                    </span>
                @elseif($booking->status === 'completed')
                    <span class="badge" style="background: #F5F5F5; color: #666; padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-flag-checkered"></i> COMPLETED
                    </span>
                @else
                    <span class="badge badge-error" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-times-circle"></i> CANCELLED
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-2" style="gap: var(--space-lg);">
            <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                <div class="card">
                    <div class="card-body">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                            <i class="fas fa-user"></i> Customer Information
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: var(--space-md); font-size: 14px;">
                            <div>
                                <div style="color: var(--color-text-light); margin-bottom: 4px;">Name</div>
                                <div style="font-weight: 600;">{{ $booking->user->name }}</div>
                            </div>
                            <div>
                                <div style="color: var(--color-text-light); margin-bottom: 4px;">Email</div>
                                <div style="font-weight: 600;">{{ $booking->user->email }}</div>
                            </div>
                            <div>
                                <div style="color: var(--color-text-light); margin-bottom: 4px;">Phone</div>
                                <div style="font-weight: 600;">{{ $booking->user->phone ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                            <i class="fas fa-info-circle"></i> Booking Details
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: var(--space-md); font-size: 14px;">
                            <div class="flex-between">
                                <span style="color: var(--color-text-light);">Trek Date</span>
                                <span style="font-weight: 600;">{{ $booking->tour_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex-between">
                                <span style="color: var(--color-text-light);">Participants</span>
                                <span style="font-weight: 600;">{{ $booking->participants }}</span>
                            </div>
                            <div class="flex-between">
                                <span style="color: var(--color-text-light);">Payment Method</span>
                                <span style="font-weight: 600; text-transform: capitalize;">{{ $booking->payment_method }}</span>
                            </div>
                            <div class="flex-between" style="padding-top: var(--space-md); border-top: 2px solid #E0E0E0;">
                                <span style="font-size: 16px; font-weight: 600;">Total Amount</span>
                                <span style="font-size: 20px; font-weight: 700; color: var(--color-primary);">Rs. {{ number_format($booking->total_amount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                            <i class="fas fa-history"></i> Timeline
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                            <div style="display: flex; gap: var(--space-md);">
                                <div style="width: 40px; height: 40px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-plus" style="color: var(--color-success);"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">Booking Created</div>
                                    <div style="font-size: 13px; color: var(--color-text-light);">{{ $booking->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>

                            @if($booking->confirmed_at)
                                <div style="display: flex; gap: var(--space-md);">
                                    <div style="width: 40px; height: 40px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-check" style="color: var(--color-success);"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">Confirmed</div>
                                        <div style="font-size: 13px; color: var(--color-text-light);">{{ $booking->confirmed_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($booking->started_at)
                                <div style="display: flex; gap: var(--space-md);">
                                    <div style="width: 40px; height: 40px; background: #E3F2FD; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-hiking" style="color: #1976D2;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">Trek Started</div>
                                        <div style="font-size: 13px; color: var(--color-text-light);">{{ $booking->started_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($booking->completed_at)
                                <div style="display: flex; gap: var(--space-md);">
                                    <div style="width: 40px; height: 40px; background: #F5F5F5; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-flag-checkered" style="color: #666;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">Completed</div>
                                        <div style="font-size: 13px; color: var(--color-text-light);">
                                            {{ $booking->completed_at->format('M d, Y H:i') }}
                                            @if($booking->admin_verified)
                                                <span class="badge badge-success" style="font-size: 10px; margin-left: 8px;">Admin Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($booking->cancelled_at)
                                <div style="display: flex; gap: var(--space-md);">
                                    <div style="width: 40px; height: 40px; background: #FFEBEE; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-times" style="color: var(--color-error);"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">Cancelled</div>
                                        <div style="font-size: 13px; color: var(--color-text-light);">{{ $booking->cancelled_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                <div class="card">
                    <div class="card-body">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                            <i class="fas fa-cog"></i> Admin Actions
                        </h3>

                        @if($booking->status === 'pending')
                            <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block btn-lg" style="margin-bottom: var(--space-sm);">
                                    <i class="fas fa-check"></i> Confirm Booking
                                </button>
                            </form>
                        @endif

                        @if($booking->status === 'active')
                            <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: var(--space-sm);">
                                    <i class="fas fa-flag-checkered"></i> Mark as Completed
                                </button>
                            </form>

                            @if($booking->trackingPin)
                                <a href="{{ route('tracking.parent', $booking) }}" class="btn btn-secondary btn-block" target="_blank">
                                    <i class="fas fa-map-marker-alt"></i> Track Live (PIN: {{ $booking->trackingPin->pin }})
                                </a>
                            @endif
                        @endif

                        @if(!in_array($booking->status, ['completed', 'cancelled']))
                            <button onclick="showCancelModal()" class="btn btn-block" style="background: #FFEBEE; color: var(--color-error); margin-top: var(--space-sm);">
                                <i class="fas fa-ban"></i> Cancel Booking
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                            <i class="fas fa-chart-line"></i> Trek Progress
                        </h3>

                        <div style="text-align: center; margin-bottom: var(--space-lg);">
                            <div style="font-size: 48px; font-weight: 700; color: var(--color-primary);">
                                {{ $booking->progress_percentage }}%
                            </div>
                            <div style="font-size: 14px; color: var(--color-text-light);">
                                {{ $booking->completed_checkpoints }} of {{ $booking->total_checkpoints }} checkpoints
                            </div>
                        </div>

                        <div style="background: #E0E0E0; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: var(--space-lg);">
                            <div style="height: 100%; background: var(--color-primary); width: {{ $booking->progress_percentage }}%;"></div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                            @foreach($booking->tourPackage->checkpoints->sortBy('order') as $checkpoint)
                                @php
                                    $progress = $booking->checkpointProgress->where('checkpoint_id', $checkpoint->id)->first();
                                    $reached = $progress && $progress->reached_at;
                                @endphp
                                <div style="display: flex; gap: var(--space-md); align-items: center; padding: var(--space-sm); background: {{ $reached ? '#E8F5E9' : '#F5F5F5' }}; border-radius: var(--radius-md);">
                                    <div style="width: 28px; height: 28px; background: {{ $reached ? '#2E7D32' : '#B0BEC5' }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">
                                        {{ $checkpoint->order }}
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 600; font-size: 13px; margin-bottom: 2px;">{{ $checkpoint->name }}</div>
                                        @if($reached)
                                            <div style="font-size: 11px; color: #2E7D32;">
                                                <i class="fas fa-check"></i> {{ $progress->reached_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($booking->travelerLocations->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                                <i class="fas fa-satellite-dish"></i> Recent GPS Updates
                            </h3>

                            <div style="display: flex; flex-direction: column; gap: var(--space-sm); font-size: 13px;">
                                @foreach($booking->travelerLocations as $location)
                                    <div style="padding: var(--space-sm); background: #F5F5F5; border-radius: var(--radius-md);">
                                        <div class="flex-between" style="margin-bottom: 4px;">
                                            <span style="font-family: monospace; font-size: 12px;">{{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}</span>
                                            <span style="color: var(--color-text-light);">±{{ round($location->accuracy) }}m</span>
                                        </div>
                                        <div style="color: var(--color-text-light); font-size: 11px;">
                                            {{ $location->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; padding: var(--space-lg);">
    <div style="background: white; border-radius: var(--radius-lg); max-width: 500px; width: 100%;">
        <div style="padding: var(--space-xl);">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-md);">Cancel Booking?</h2>
            <p style="color: var(--color-text-light); margin-bottom: var(--space-lg);">This action cannot be undone.</p>

            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Reason (Optional)</label>
                    <textarea name="reason" class="form-textarea" rows="3" placeholder="Enter cancellation reason..."></textarea>
                </div>

                <div class="flex gap-md">
                    <button type="button" onclick="closeCancelModal()" class="btn btn-secondary btn-lg" style="flex: 1;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-lg" style="flex: 1; background: var(--color-error); color: white;">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showCancelModal() {
        document.getElementById('cancelModal').style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
</script>
@endpush
@endsection