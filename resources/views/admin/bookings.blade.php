@extends('layouts.app')

@section('title', 'Manage Bookings')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div class="flex-between" style="margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-md);">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">Bookings Management</h1>
                <p style="color: var(--color-text-light); margin: 0;">Track and manage all trek bookings</p>
            </div>
            <button onclick="exportBookings()" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Export CSV
            </button>
        </div>

        <div class="card" style="margin-bottom: var(--space-xl);">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bookings') }}" class="grid grid-4" style="gap: var(--space-md); align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Search Customer</label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-input" 
                            placeholder="Name or email..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Date Range</label>
                        <select name="date_range" class="form-select">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>

                    <div class="flex gap-sm">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'date_range']))
                            <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-4" style="margin-bottom: var(--space-xl);">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-text);">
                        {{ $stats['total'] }}
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Total</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-warning);">
                        {{ $stats['pending'] }}
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Pending</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-success);">
                        {{ $stats['paid'] }}
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Paid</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: #1976D2;">
                        {{ $stats['active'] }}
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Active</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 28px; font-weight: 700; color: #666;">
                        {{ $stats['completed'] }}
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-light);">Completed</div>
                </div>
            </div>
        </div>

        @if($bookings->count() > 0)
            <div class="card">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #E0E0E0; background: #F5F5F5;">
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    ID
                                </th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Customer
                                </th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Trek Package
                                </th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Date
                                </th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Trekkers
                                </th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Amount
                                </th>
                                <th style="padding: var(--space-md); text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Status
                                </th>
                                <th style="padding: var(--space-md); text-align: right; font-size: 13px; font-weight: 600; color: var(--color-text-light);">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr style="border-bottom: 1px solid #E0E0E0;">
                                    <td style="padding: var(--space-md); font-size: 13px; font-weight: 600; color: var(--color-text);">
                                        #{{ $booking->id }}
                                    </td>
                                    <td style="padding: var(--space-md);">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">{{ $booking->user->name }}</div>
                                        <div style="font-size: 12px; color: var(--color-text-light);">{{ $booking->user->email }}</div>
                                    </td>
                                    <td style="padding: var(--space-md);">
                                        <div style="font-size: 14px; color: var(--color-text);">{{ Str::limit($booking->tourPackage->name, 30) }}</div>
                                    </td>
                                    <td style="padding: var(--space-md); font-size: 13px; color: var(--color-text);">
                                        {{ $booking->tour_date->format('M d, Y') }}
                                    </td>
                                    <td style="padding: var(--space-md); font-size: 13px; color: var(--color-text);">
                                        {{ $booking->participants }}
                                    </td>
                                    <td style="padding: var(--space-md); font-size: 14px; font-weight: 600; color: var(--color-primary);">
                                        Rs. {{ number_format($booking->total_amount, 0) }}
                                    </td>
                                    <td style="padding: var(--space-md);">
                                        @if($booking->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($booking->status === 'confirmed')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($booking->status === 'active')
                                            <span class="badge" style="background: #E3F2FD; color: #1976D2;">Active</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="badge" style="background: #F5F5F5; color: #666;">Completed</span>
                                        @else
                                            <span class="badge badge-error">Cancelled</span>
                                        @endif
                                    </td>
                                    <td style="padding: var(--space-md); text-align: right;">
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

                                                @if(in_array($booking->status, ['confirmed', 'active']))
                                                    <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: var(--space-sm);">
                                                            <i class="fas fa-flag-checkered"></i> Mark as Completed
                                                        </button>
                                                    </form>

                                                    <div style="padding: var(--space-md); background: #E3F2FD; border-radius: var(--radius-md); margin-bottom: var(--space-sm); font-size: 13px; color: #1976D2;">
                                                        <i class="fas fa-info-circle"></i> 
                                                        Progress: <strong>{{ $booking->progress_percentage }}%</strong> 
                                                        ({{ $booking->completed_checkpoints }}/{{ $booking->total_checkpoints }} checkpoints)
                                                    </div>

                                                    @if($booking->trackingPin)
                                                        <a href="{{ route('tracking.parent', $booking) }}" class="btn btn-secondary btn-block" target="_blank">
                                                            <i class="fas fa-map-marker-alt"></i> Track Live (PIN: {{ $booking->trackingPin->pin }})
                                                        </a>
                                                    @endif
                                                @endif

                                                @if($booking->status === 'completed')
                                                    <div class="alert alert-success">
                                                        <i class="fas fa-check-circle"></i> This booking is completed
                                                        @if($booking->admin_verified)
                                                            <span class="badge badge-success" style="margin-left: 8px;">Admin Verified</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(!in_array($booking->status, ['completed', 'cancelled']))
                                                    <button onclick="showCancelModal()" class="btn btn-block" style="background: #FFEBEE; color: var(--color-error); margin-top: var(--space-sm);">
                                                        <i class="fas fa-ban"></i> Cancel Booking
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: var(--space-xl);">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <i class="fas fa-inbox" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-lg);"></i>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">No Bookings Found</h3>
                    <p style="color: var(--color-text-light); margin: 0;">
                        @if(request()->hasAny(['search', 'status', 'date_range']))
                            Try adjusting your filters
                        @else
                            Bookings will appear here once customers start booking
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    function markAsPaid(bookingId) {
        if (confirm('Mark this booking as paid?')) {
            fetch(`/admin/bookings/${bookingId}/mark-paid`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to update booking');
                }
            })
            .catch(() => alert('Error occurred'));
        }
    }

    function exportBookings() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'csv');
        window.location.href = '{{ route('admin.bookings') }}?' + params.toString();
    }
</script>
@endpush
@endsection