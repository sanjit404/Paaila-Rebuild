@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">
        <div style="margin-bottom: var(--space-xl);">
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">Admin Dashboard</h1>
            <p style="color: var(--color-text-light); margin: 0;">Manage treks, monitor bookings, and oversee operations</p>
        </div>
        <div class="grid grid-4" style="margin-bottom: var(--space-xl);">
            <div class="card" style="
            background: linear-gradient(135deg, rgba(9, 29, 10, 0.95) 0%, rgba(2, 21, 3, 0.3) 100%), url('{{ asset('images/mns_try.jpg') }}') center/cover;
            ">
                <div class="card-body">
                    <div class="flex-between" style="margin-bottom: var(--space-md);">
                        <div style="width: 50px; height: 50px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-box" style="font-size: 20px; color: var(--color-primary);"></i>
                        </div>
                        <span class="badge badge-primary">{{ $stats['active_packages'] }} active</span>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: white; margin-bottom: var(--space-xs);">
                        {{ $stats['total_packages'] }}
                    </div>
                    <div style="font-size: 13px; color: white;">Trek Packages</div>
                </div>
            </div>

            <div class="card" style="
            background: linear-gradient(135deg, rgba(9, 29, 10, 0.95) 0%, rgba(2, 21, 3, 0.3) 100%), url('{{ asset('images/lake_side.jpg') }}') center/cover;
            ">
                <div class="card-body">
                    <div class="flex-between" style="margin-bottom: var(--space-md);">
                        <div style="width: 50px; height: 50px; background: #FFF3E0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-ticket-alt" style="font-size: 20px; color: var(--color-warning);"></i>
                        </div>
                        <span class="badge badge-warning">{{ $stats['pending_bookings'] }} pending</span>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: white; margin-bottom: var(--space-xs);">
                        {{ $stats['total_bookings'] }}
                    </div>
                    <div style="font-size: 13px; color: white;">Total Bookings</div>
                </div>
            </div>

            <div class="card" style="
            background: linear-gradient(135deg, rgba(9, 29, 10, 0.95) 0%, rgba(2, 21, 3, 0.3) 100%), url('{{ asset('images/pashupatinath_temple.jpg') }}') center/cover;
            ">
                <div class="card-body">
                    <div class="flex-between" style="margin-bottom: var(--space-md);">
                        <div style="width: 50px; height: 50px; background: #E3F2FD; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-hiking" style="font-size: 20px; color: #1976D2;"></i>
                        </div>
                        <span class="badge" style="background: #E3F2FD; color: #1976D2;">{{ $stats['completed_tours'] }} done</span>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: white; margin-bottom: var(--space-xs);">
                        {{ $stats['active_tours'] }}
                    </div>
                    <div style="font-size: 13px; color: white;">Active Treks</div>
                </div>
            </div>

            <div class="card" style="
            background: linear-gradient(135deg, rgba(9, 29, 10, 0.95) 0%, rgba(2, 21, 3, 0.3) 100%), url('{{ asset('images/patan_durbar.jpg') }}') center/cover;
            ">
                <div class="card-body">
                    <div class="flex-between" style="margin-bottom: var(--space-md);">
                        <div style="width: 50px; height: 50px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-rupee-sign" style="font-size: 20px; color: var(--color-success);"></i>
                        </div>
                        <span class="badge badge-success">This month</span>
                    </div>
                    <div style="font-size: 28px; font-weight: 700; color: white; margin-bottom: var(--space-xs);">
                        Rs. {{ number_format($stats['total_revenue'], 0) }}
                    </div>
                    <div style="font-size: 13px; color: white;">Total Revenue</div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: var(--space-xl);">
            <div class="card-body" style="padding: var(--space-xl);">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h3>
                <div class="grid grid-4" style="gap: var(--space-md);">
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus-circle"></i>
                        New Trek
                    </a>
                    <a href="{{ route('admin.packages') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-box-open"></i>
                        Manage Treks
                    </a>
                    <a href="{{ route('admin.bookings') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i>
                        View Bookings
                    </a>
                    <a href="{{ route('admin.posts') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i>
                        View Posts
                    </a>
                    
                </div>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="card" >
                <div class="card-body" style="padding: var(--space-xl);">
                    <div class="flex-between" style="margin-bottom: var(--space-lg);">
                        <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                            <i class="fas fa-clock"></i> Recent Bookings
                        </h3>
                        <a href="{{ route('admin.bookings') }}" style="color: var(--color-primary); text-decoration: none; font-size: 14px; font-weight: 600;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    @if($recent_bookings->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                            @foreach($recent_bookings->take(5) as $booking)
                                <div style="padding: var(--space-md); background: #F5F5F5; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px; color: var(--color-text);">
                                            {{ $booking->user->name }}
                                        </div>
                                        <div style="font-size: 13px; color: var(--color-text-light);">
                                            {{ Str::limit($booking->tourPackage->name, 30) }}
                                        </div>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0; margin-left: var(--space-md);">
                                        @if($booking->status === 'pending')
                                            <span class="badge badge-warning" style="font-size: 11px;">Pending</span>
                                        @elseif($booking->status === 'paid')
                                            <span class="badge badge-success" style="font-size: 11px;">Paid</span>
                                        @elseif($booking->status === 'active')
                                            <span class="badge" style="background: #E3F2FD; color: #1976D2; font-size: 11px;">Active</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="badge" style="background: #F5F5F5; color: #666; font-size: 11px;">Done</span>
                                        @endif
                                        <div style="font-size: 12px; color: var(--color-text-light); margin-top: 4px;">
                                            Rs. {{ number_format($booking->total_amount, 0) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="text-align: center; color: var(--color-text-light); padding: var(--space-xl); margin: 0;">
                            No bookings yet
                        </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="padding: var(--space-xl);">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-lg);">
                        <i class="fas fa-hiking"></i> Active Treks (Live)
                    </h3>

                    @php
                        $activeBookings = \App\Models\TourBooking::where('status', 'active')
                            ->with(['user', 'tourPackage', 'trackingPin'])
                            ->latest('started_at')
                            ->take(5)
                            ->get();
                    @endphp

                    @if($activeBookings->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                            @foreach($activeBookings as $booking)
                                <div style="padding: var(--space-md); background: #E8F5E9; border-radius: var(--radius-md); border: 1px solid #C8E6C9;">
                                    <div class="flex-between" style="margin-bottom: var(--space-sm);">
                                        <div style="font-weight: 600; font-size: 14px; color: var(--color-text);">
                                            {{ $booking->user->name }}
                                        </div>
                                        <span style="display: flex; align-items: center; gap: 4px; font-size: 12px; color: var(--color-success);">
                                            <span style="width: 8px; height: 8px; background: var(--color-success); border-radius: 50%; animation: pulse 2s infinite;"></span>
                                            Live
                                        </span>
                                    </div>
                                    <div style="font-size: 13px; color: var(--color-text-light); margin-bottom: var(--space-sm);">
                                        {{ Str::limit($booking->tourPackage->name, 30) }}
                                    </div>
                                    <div class="flex-between">
                                        <div style="font-size: 12px; color: var(--color-text-light);">
                                            PIN: <strong style="color: var(--color-success);">{{ $booking->trackingPin->pin }}</strong>
                                        </div>
                                        <div style="font-size: 12px; color: var(--color-success); font-weight: 600;">
                                            {{ $booking->progress_percentage }}% complete
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="text-align: center; color: var(--color-text-light); padding: var(--space-xl); margin: 0;">
                            No active treks at the moment
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.7); }
        50% { box-shadow: 0 0 0 6px rgba(46, 125, 50, 0); }
    }
</style>
@endpush
@endsection