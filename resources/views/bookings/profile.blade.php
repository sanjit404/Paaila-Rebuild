@extends('layouts.app')

@section('title', 'My Profile — TourNepal')

@php
    $user = auth()->user();
    $preferences = \App\Models\UserPreference::forUser($user->id);
    $stats = [
        'total' => \App\Models\TourBooking::where('user_id', $user->id)->count(),
        'completed' => \App\Models\TourBooking::where('user_id', $user->id)->where('status', 'completed')->count(),
        'active' => \App\Models\TourBooking::where('user_id', $user->id)->where('status', 'active')->count(),
        'ratings' => \App\Models\TrekRating::where('user_id', $user->id)->count(),
    ];

    $recentBookings = \App\Models\TourBooking::where('user_id', $user->id)
        ->with('tourPackage')
        ->latest()
        ->take(5)
        ->get();

    $myRatings = \App\Models\TrekRating::where('user_id', $user->id)
        ->with('tourPackage')
        ->latest()
        ->take(5)
        ->get();
@endphp

@section('content')
<div style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div style="background: var(--color-primary); padding: var(--space-xl) 0;">
        <div class="container">
            <div style="display: flex; align-items: center; gap: var(--space-xl); flex-wrap: wrap;">
                <div style="position: relative;">
                    <div style="width: 90px; height: 90px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 800; color: white; border: 3px solid rgba(255,255,255,0.4);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @if($user->role === 'admin')
                        <div style="position: absolute; bottom: 0; right: 0; background: var(--color-accent); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white;">
                            <i class="fas fa-crown"></i>
                        </div>
                    @endif
                </div>

                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 24px; font-weight: 800; color: white; margin-bottom: 4px;">
                        {{ $user->name }}
                    </div>
                    <div style="font-size: 14px; color: rgba(255,255,255,0.8); margin-bottom: var(--space-sm);">
                        {{ $user->email }}
                    </div>
                    <div style="font-size: 13px; color: rgba(255,255,255,0.65);">
                        Member since {{ optional($user->created_at)->format('F Y') }}
                    </div>
                </div>

                <div style="display: flex; gap: var(--space-xl);">
                    @foreach([
                        ['value' => $stats['total'], 'label' => 'Total Bookings'],
                        ['value' => $stats['completed'], 'label' => 'Completed'],
                        ['value' => $stats['ratings'], 'label' => 'Ratings Given'],
                    ] as $stat)
                        <div style="text-align: center;">
                            <div style="font-size: 28px; font-weight: 800; color: white;">{{ $stat['value'] }}</div>
                            <div style="font-size: 12px; color: rgba(255,255,255,0.7);">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; gap: 0; margin-top: var(--space-xl); border-bottom: 1px solid rgba(255,255,255,0.2); overflow-x: auto;">
                @foreach([
                    ['id' => 'overview', 'icon' => 'fa-user', 'label' => 'Overview'],
                    ['id' => 'bookings', 'icon' => 'fa-ticket-alt', 'label' => 'Bookings'],
                    ['id' => 'ratings', 'icon' => 'fa-star', 'label' => 'My Ratings'],
                    ['id' => 'preferences', 'icon' => 'fa-sliders-h', 'label' => 'Preferences'],
                    ['id' => 'settings', 'icon' => 'fa-cog', 'label' => 'Settings'],
                ] as $tab)
                    <button type="button"
                        onclick="switchTab('{{ $tab['id'] }}')"
                        id="tab-btn-{{ $tab['id'] }}"
                        class="profile-tab {{ $tab['id'] === 'overview' ? 'active' : '' }}">
                        <i class="fas {{ $tab['icon'] }}"></i>
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-2xl);">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: var(--space-xl);">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: var(--space-xl);">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <div id="tab-overview" class="tab-panel">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-xl);">
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title"><i class="fas fa-chart-bar"></i> Trek Summary</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                            @foreach([
                                ['n' => $stats['total'], 'l' => 'Total Booked', 'c' => 'var(--color-primary)'],
                                ['n' => $stats['completed'], 'l' => 'Completed', 'c' => 'var(--color-success)'],
                                ['n' => $stats['active'], 'l' => 'In Progress', 'c' => '#2196F3'],
                                ['n' => $stats['ratings'], 'l' => 'Ratings Given', 'c' => '#FFC107'],
                            ] as $s)
                                <div style="padding: var(--space-lg); background: #F9F9F9; border-radius: var(--radius-md); text-align: center;">
                                    <div style="font-size: 30px; font-weight: 800; color: {{ $s['c'] }};">{{ $s['n'] }}</div>
                                    <div style="font-size: 12px; color: var(--color-text-light); margin-top: 4px;">{{ $s['l'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="flex-between" style="margin-bottom: var(--space-lg);">
                            <h3 class="section-title" style="margin: 0;"><i class="fas fa-sliders-h"></i> My Preferences</h3>
                            <button type="button" onclick="switchTab('preferences')" class="btn btn-secondary btn-sm">Edit</button>
                        </div>

                        @if($preferences && $preferences->preferences_set)
                            <div style="display: flex; flex-direction: column; gap: var(--space-md); font-size: 14px;">
                                @if($preferences->trek_types)
                                    <div>
                                        <div style="color: var(--color-text-light); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 6px;">Trek Types</div>
                                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                            @foreach($preferences->trek_types as $type)
                                                <span style="background: #E8F5E9; color: var(--color-primary); padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: capitalize;">{{ $type }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-between">
                                    <span style="color: var(--color-text-light);">Difficulty</span>
                                    <span style="font-weight: 600; text-transform: capitalize;">{{ $preferences->difficulty }}</span>
                                </div>
                                <div class="flex-between">
                                    <span style="color: var(--color-text-light);">Duration</span>
                                    <span style="font-weight: 600;">{{ $preferences->duration }} days</span>
                                </div>
                                <div class="flex-between">
                                    <span style="color: var(--color-text-light);">Budget</span>
                                    <span style="font-weight: 600; text-transform: capitalize;">{{ $preferences->budget }}</span>
                                </div>
                            </div>
                        @else
                            <div style="text-align: center; padding: var(--space-xl); color: var(--color-text-light);">
                                <i class="fas fa-sliders-h" style="font-size: 40px; color: #E0E0E0; margin-bottom: var(--space-md);"></i>
                                <p style="margin-bottom: var(--space-md);">No preferences set yet</p>
                                <a href="{{ route('preferences.create') }}" class="btn btn-primary btn-sm">Set Preferences</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($recentBookings->isNotEmpty())
                <div class="card" style="margin-top: var(--space-xl);">
                    <div class="card-body">
                        <div class="flex-between" style="margin-bottom: var(--space-lg);">
                            <h3 class="section-title" style="margin: 0;"><i class="fas fa-history"></i> Recent Bookings</h3>
                            <button type="button" onclick="switchTab('bookings')" class="btn btn-secondary btn-sm">View All</button>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                            @foreach($recentBookings as $booking)
                                @php
                                    $statusColors = ['pending' => '#F57C00', 'confirmed' => 'var(--color-success)', 'active' => '#2196F3', 'completed' => '#9E9E9E', 'cancelled' => 'var(--color-error)'];
                                    $c = $statusColors[$booking->status] ?? '#ccc';
                                @endphp
                                <div style="display: flex; align-items: center; gap: var(--space-md); padding: var(--space-md) var(--space-lg); background: #F9F9F9; border-radius: var(--radius-md); border-left: 4px solid {{ $c }};">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">{{ $booking->tourPackage->name }}</div>
                                        <div style="font-size: 12px; color: var(--color-text-light);">{{ $booking->tour_date->format('M d, Y') }} · {{ $booking->booking_number }}</div>
                                    </div>
                                    <div style="font-size: 13px; font-weight: 600; color: {{ $c }}; text-transform: capitalize; white-space: nowrap;">
                                        {{ $booking->status }}
                                    </div>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-sm">View</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div id="tab-bookings" class="tab-panel" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title"><i class="fas fa-ticket-alt"></i> All Bookings</h3>
                    @if($recentBookings->isNotEmpty())
                        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                            @foreach(\App\Models\TourBooking::where('user_id', $user->id)->with('tourPackage')->latest()->get() as $booking)
                                @php
                                    $statusColors = ['pending' => 'var(--color-warning)', 'confirmed' => 'var(--color-success)', 'active' => '#2196F3', 'completed' => '#9E9E9E', 'cancelled' => 'var(--color-error)'];
                                    $c = $statusColors[$booking->status] ?? '#ccc';
                                @endphp
                                <div style="display: flex; align-items: center; gap: var(--space-md); padding: var(--space-lg); background: #F9F9F9; border-radius: var(--radius-md); border-left: 4px solid {{ $c }}; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">{{ $booking->tourPackage->name }}</div>
                                        <div style="font-size: 13px; color: var(--color-text-light);">
                                            {{ $booking->tour_date->format('M d, Y') }} ·
                                            {{ $booking->participants }} {{ Str::plural('person', $booking->participants) }} ·
                                            Rs. {{ number_format($booking->total_amount, 0) }}
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: var(--space-md);">
                                        <span style="font-size: 12px; font-weight: 700; color: {{ $c }}; text-transform: uppercase; letter-spacing: 0.5px;">
                                            {{ $booking->status }}
                                        </span>
                                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: var(--space-2xl); color: var(--color-text-light);">
                            <i class="fas fa-hiking" style="font-size: 48px; color: #E0E0E0; margin-bottom: var(--space-md);"></i>
                            <p>No bookings yet</p>
                            <a href="{{ route('home') }}" class="btn btn-primary btn-sm" style="margin-top: var(--space-md);">Browse Treks</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="tab-ratings" class="tab-panel" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title"><i class="fas fa-star"></i> My Ratings</h3>
                    <p style="font-size: 13px; color: var(--color-text-light); margin-bottom: var(--space-xl);">
                        <i class="fas fa-lock" style="font-size: 11px;"></i>
                        Ratings are permanent and cannot be changed — they protect the integrity of reviews.
                    </p>

                    @if($myRatings->isNotEmpty())
                        <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                            @foreach($myRatings as $rating)
                                <div style="padding: var(--space-lg); background: #F9F9F9; border-radius: var(--radius-md); border: 1px solid #E8E8E8;">
                                    <div class="flex-between" style="margin-bottom: var(--space-md); flex-wrap: wrap; gap: var(--space-sm);">
                                        <div>
                                            <div style="font-weight: 700; font-size: 15px; margin-bottom: 2px;">{{ $rating->tourPackage->name }}</div>
                                            <div style="font-size: 12px; color: var(--color-text-light);">Rated {{ $rating->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star" style="font-size: 18px; color: {{ $i <= $rating->rating ? '#FFC107' : '#E0E0E0' }};"></i>
                                            @endfor
                                            <span style="font-size: 16px; font-weight: 800; color: var(--color-text); margin-left: 4px;">{{ $rating->rating }}/5</span>
                                            <span style="font-size: 13px; color: var(--color-text-light);">{{ $rating->star_label }}</span>
                                        </div>
                                    </div>
                                    @if($rating->review)
                                        <p style="font-size: 14px; color: var(--color-text-light); margin: 0; font-style: italic; line-height: 1.6; border-left: 3px solid #E0E0E0; padding-left: var(--space-md);">
                                            "{{ $rating->review }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: var(--space-2xl); color: var(--color-text-light);">
                            <i class="fas fa-star" style="font-size: 48px; color: #E0E0E0; margin-bottom: var(--space-md);"></i>
                            <p>No ratings yet. Complete a trek to rate it!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="tab-preferences" class="tab-panel" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div class="flex-between" style="margin-bottom: var(--space-lg);">
                        <h3 class="section-title" style="margin: 0;"><i class="fas fa-sliders-h"></i> Trek Preferences</h3>
                        @if($preferences && $preferences->preferences_set)
                            <span style="font-size: 13px; color: var(--color-success); font-weight: 600;">
                                <i class="fas fa-check-circle"></i> Set up
                            </span>
                        @endif
                    </div>
                    <p style="font-size: 14px; color: var(--color-text-light); margin-bottom: var(--space-xl);">
                        Update your preferences anytime. Your recommendations refresh automatically.
                    </p>
                    <a href="{{ route('preferences.edit') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-edit"></i>
                        {{ $preferences && $preferences->preferences_set ? 'Update Preferences' : 'Set Up Preferences' }}
                    </a>
                </div>
            </div>
        </div>

        <div id="tab-settings" class="tab-panel" style="display: none;">
            <div style="display: flex; flex-direction: column; gap: var(--space-xl);">
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title"><i class="fas fa-user-edit"></i> Update Profile</h3>
                        <form method="POST" action="{{ route('profile.update') }}" class="settings-form">
                            @csrf
                            @method('PATCH')

                            <div class="form-field-inline">
                                <label>Full Name</label>
                                <div class="field-wrap">
                                    <i class="fas fa-user field-icon"></i>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Email Address</label>
                                <div class="field-wrap">
                                    <i class="fas fa-envelope field-icon"></i>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Phone</label>
                                <div class="field-wrap">
                                    <i class="fas fa-phone field-icon"></i>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Address</label>
                                <div class="field-wrap">
                                    <i class="fas fa-map-marker-alt field-icon"></i>
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}">
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Latitude</label>
                                <div class="field-wrap">
                                    <i class="fas fa-location-arrow field-icon"></i>
                                    <input type="number" step="any" name="lat" value="{{ old('lat', $user->lat) }}">
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Longitude</label>
                                <div class="field-wrap">
                                    <i class="fas fa-location-arrow field-icon"></i>
                                    <input type="number" step="any" name="lng" value="{{ old('lng', $user->lng) }}">
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Map Style</label>
                                <div class="field-wrap">
                                    <i class="fas fa-map field-icon"></i>
                                    <select name="map_style" style="width: 100%; padding: 11px 14px 11px 40px; border: 2px solid #E8ECEF; border-radius: 10px; font-size: 14px; background: #FAFBFC;">
                                        @foreach(['streets', 'satellite', 'outdoors', 'dark'] as $style)
                                            <option value="{{ $style }}" {{ old('map_style', $user->map_style ?? 'streets') === $style ? 'selected' : '' }}>
                                                {{ ucfirst($style) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label style="display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox" name="sharing_enabled" value="1" {{ old('sharing_enabled', $user->sharing_enabled) ? 'checked' : '' }}>
                                    Sharing enabled
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title"><i class="fas fa-lock"></i> Change Password</h3>
                        <form method="POST" action="{{ route('profile.password.update') }}" class="settings-form">
                            @csrf
                            @method('PUT')

                            <div class="form-field-inline">
                                <label>Current Password</label>
                                <div class="field-wrap">
                                    <i class="fas fa-lock field-icon"></i>
                                    <input type="password" name="current_password" required>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>New Password</label>
                                <div class="field-wrap">
                                    <i class="fas fa-lock field-icon"></i>
                                    <input type="password" name="password" required>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Confirm New Password</label>
                                <div class="field-wrap">
                                    <i class="fas fa-lock field-icon"></i>
                                    <input type="password" name="password_confirmation" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card" style="border: 2px solid #FFEBEE;">
                    <div class="card-body">
                        <h3 class="section-title" style="color: var(--color-error);">
                            <i class="fas fa-exclamation-triangle"></i> Danger Zone
                        </h3>
                        <p style="font-size: 14px; color: var(--color-text-light); margin-bottom: var(--space-lg);">
                            Deleting your account is permanent. All bookings, ratings, and preferences will be removed.
                        </p>
                        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <div style="margin-bottom: var(--space-md);">
                                <label style="font-size: 14px; font-weight: 600; color: var(--color-text); display: block; margin-bottom: 6px;">
                                    Type your password to confirm
                                </label>
                                <input type="password" name="password" required
                                       style="padding: 10px 14px; border: 2px solid #FFCDD2; border-radius: 8px; font-size: 14px; width: 100%; max-width: 320px; outline: none; box-sizing: border-box;">
                            </div>
                            <button type="submit" class="btn" style="background: var(--color-error); color: white;">
                                <i class="fas fa-trash"></i> Delete My Account
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--space-md);">
                        <div>
                            <div style="font-weight: 600; margin-bottom: 4px;">Sign Out</div>
                            <div style="font-size: 14px; color: var(--color-text-light);">You can sign back in anytime.</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sign-out-alt"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.profile-tab {
    padding: 12px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,0.65);
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
.profile-tab:hover { color: rgba(255,255,255,0.9); }
.profile-tab.active { color: white; border-bottom-color: white; }

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text);
    margin-bottom: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.settings-form {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg);
    max-width: 480px;
}

.form-field-inline {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-field-inline label {
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text);
}

.form-field-inline .field-wrap { position: relative; }

.form-field-inline .field-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #B0BEC5;
    font-size: 14px;
    pointer-events: none;
}

.form-field-inline input,
.form-field-inline select {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 2px solid #E8ECEF;
    border-radius: 10px;
    font-size: 14px;
    color: var(--color-text);
    background: #FAFBFC;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}

.form-field-inline input:focus,
.form-field-inline select:focus {
    border-color: var(--color-primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(27,94,32,0.08);
}

@media (max-width: 768px) {
    .profile-tab { padding: 10px 14px; font-size: 13px; }
    .profile-tab i { display: none; }
}
</style>
@endpush

@push('scripts')
<script>
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(function(p) {
        p.style.display = 'none';
    });

    document.querySelectorAll('.profile-tab').forEach(function(b) {
        b.classList.remove('active');
    });

    const panel = document.getElementById('tab-' + id);
    const btn = document.getElementById('tab-btn-' + id);
    if (panel) panel.style.display = 'block';
    if (btn) btn.classList.add('active');

    history.replaceState(null, '', '#' + id);
}

document.addEventListener('DOMContentLoaded', function() {
    var hash = window.location.hash.replace('#', '');
    var valid = ['overview', 'bookings', 'ratings', 'preferences', 'settings'];
    if (hash && valid.includes(hash)) {
        switchTab(hash);
    }
});
</script>
@endpush
@endsection