@extends('layouts.app')

@section('title', 'My Profile — Paaila')

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

    $allBookings = \App\Models\TourBooking::where('user_id', $user->id)
        ->with('tourPackage')
        ->latest()
        ->get();

    $myRatings = \App\Models\TrekRating::where('user_id', $user->id)
        ->with('tourPackage')
        ->latest()
        ->take(5)
        ->get();

    $emailLocked = !empty($user->email);
    $phoneLocked = !empty($user->phone);

    $missingFields = [];
    if (empty($user->email)) $missingFields[] = 'email address';
    if (empty($user->phone)) $missingFields[] = 'phone number';
    if (empty($user->address)) $missingFields[] = 'address';
@endphp

@section('content')
<div class="profile-page">
    <div class="container profile-wrap">
        <div class="profile-header card">
            <div class="card-body">
                <div class="profile-top">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                        @if($user->role === 'admin')
                            <span class="avatar-badge"><i class="fas fa-crown"></i></span>
                        @endif
                    </div>

                    <div class="profile-meta">
                        <h1>{{ $user->name }}</h1>
                        <p>{{ $user->email }}</p>
                        <span>Member since {{ optional($user->created_at)->format('F Y') }}</span><br>

                        @if(count($missingFields))
                            <span style="color:red;">
                                Please set your {{ implode(' and ', $missingFields) }}
                                <a href="#settings" onclick="switchTab('settings'); return false;" style="text-decoration:none;">
                                    here
                                </a>.
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-tabs">
            @foreach([
                ['id' => 'overview', 'icon' => 'fa-user', 'label' => 'Overview'],
                ['id' => 'bookings', 'icon' => 'fa-ticket-alt', 'label' => 'Bookings'],
                ['id' => 'ratings', 'icon' => 'fa-star', 'label' => 'Ratings'],
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

        

        <div id="tab-overview" class="tab-panel">
            <div class="grid-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title"><i class="fas fa-chart-bar"></i> Trek Summary</h3>
                        <div class="summary-grid">
                            <div class="summary-box">
                                <div class="summary-number">{{ $stats['total'] }}</div>
                                <div class="summary-label">Total Booked</div>
                            </div>
                            <div class="summary-box">
                                <div class="summary-number">{{ $stats['completed'] }}</div>
                                <div class="summary-label">Completed</div>
                            </div>
                            <div class="summary-box">
                                <div class="summary-number">{{ $stats['active'] }}</div>
                                <div class="summary-label">In Progress</div>
                            </div>
                            <div class="summary-box">
                                <div class="summary-number">{{ $stats['ratings'] }}</div>
                                <div class="summary-label">Ratings Given</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="card-head">
                            <h3 class="section-title"><i class="fas fa-sliders-h"></i> My Preferences</h3>
                            <button type="button" onclick="switchTab('preferences')" class="btn btn-secondary btn-sm">Edit</button>
                        </div>

                        @if($preferences && $preferences->preferences_set)
                            <div class="details-list">
                                @if($preferences->trek_types)
                                    <div>
                                        <div class="mini-label">Trek Types</div>
                                        <div class="chip-wrap">
                                            @foreach($preferences->trek_types as $type)
                                                <span class="chip">{{ ucfirst($type) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="detail-row"><span>Difficulty</span><strong>{{ ucfirst($preferences->difficulty) }}</strong></div>
                                <div class="detail-row"><span>Duration</span><strong>{{ $preferences->duration }} days</strong></div>
                                <div class="detail-row"><span>Budget</span><strong>{{ ucfirst($preferences->budget) }}</strong></div>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-sliders-h"></i>
                                <p>No preferences set yet</p>
                                <a href="{{ route('preferences.create') }}" class="btn btn-primary btn-sm">Set Preferences</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($recentBookings->isNotEmpty())
                <div class="card mt-24">
                    <div class="card-body">
                        <div class="card-head">
                            <h3 class="section-title"><i class="fas fa-history"></i> Recent Bookings</h3>
                            <button type="button" onclick="switchTab('bookings')" class="btn btn-secondary btn-sm">View All</button>
                        </div>
                        <br>
                        <div class="item-list">
                            @foreach($recentBookings as $booking)
                                @php
                                    $statusColors = ['pending' => '#B26A00', 'confirmed' => '#166534', 'active' => '#1D4ED8', 'completed' => '#6B7280', 'cancelled' => '#B91C1C'];
                                    $c = $statusColors[$booking->status] ?? '#9CA3AF';
                                @endphp
                                <div class="list-item">
                                    <div class="list-left">
                                        <div class="list-title">{{ $booking->tourPackage->name }}</div>
                                        <div class="list-subtitle">{{ $booking->tour_date->format('M d, Y') }} · {{ $booking->booking_number }}</div>
                                    </div>
                                    <div class="list-right" style="color: {{ $c }};">{{ $booking->status }}</div>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-sm">View</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div id="tab-bookings" class="tab-panel" style="display:none;">
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title"><i class="fas fa-ticket-alt"></i> All Bookings</h3>
                    <br>
                    @if($allBookings->isNotEmpty())
                        <div class="item-list">
                            @foreach($allBookings as $booking)
                                @php
                                    $statusColors = ['pending' => '#B26A00', 'confirmed' => '#166534', 'active' => '#1D4ED8', 'completed' => '#6B7280', 'cancelled' => '#B91C1C'];
                                    $c = $statusColors[$booking->status] ?? '#9CA3AF';
                                @endphp
                                <div class="list-item">
                                    <div class="list-left">
                                        <div class="list-title">{{ $booking->tourPackage->name }}</div>
                                        <div class="list-subtitle">
                                            {{ $booking->tour_date->format('M d, Y') }} ·
                                            {{ $booking->participants }} {{ \Illuminate\Support\Str::plural('person', $booking->participants) }} ·
                                            Rs. {{ number_format($booking->total_amount, 0) }}
                                        </div>
                                    </div>
                                    <div class="list-right" style="color: {{ $c }};">{{ $booking->status }}</div>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-sm">View</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-hiking"></i>
                            <p>No bookings yet</p>
                            <a href="{{ route('home') }}" class="btn btn-primary btn-sm">Browse Treks</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="tab-ratings" class="tab-panel" style="display:none;">
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title"><i class="fas fa-star"></i> My Ratings</h3>
                    <br>
                    <p class="muted-note">
                        <i class="fas fa-lock"></i> Ratings are permanent and cannot be changed.
                    </p>

                    @if($myRatings->isNotEmpty())
                        <div class="rating-list">
                            @foreach($myRatings as $rating)
                                <div class="rating-card">
                                    <div class="card-head">
                                        <div>
                                            <div class="list-title">{{ $rating->tourPackage->name }}</div>
                                            <div class="list-subtitle">Rated {{ $rating->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star" style="color: {{ $i <= $rating->rating ? '#B26A00' : '#E5E7EB' }};"></i>
                                            @endfor
                                            <strong>{{ $rating->rating }}/5</strong>
                                        </div>
                                    </div>

                                    @if($rating->review)
                                        <p class="review-text">{{ $rating->review }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-star"></i>
                            <p>No ratings yet. Complete a trek to rate it!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="tab-preferences" class="tab-panel" style="display:none;">
            <div class="card">
                <div class="card-body">
                    <div class="card-head">
                        <h3 class="section-title"><i class="fas fa-sliders-h"></i> Trek Preferences</h3>
                        @if($preferences && $preferences->preferences_set)
                            <span class="status-ok"><i class="fas fa-check-circle"></i> Set up</span>
                        @endif
                    </div>
                    <p class="muted-note">Update your preferences anytime. Your recommendations refresh automatically.</p>
                    <a href="{{ route('preferences.edit') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-edit"></i>
                        {{ $preferences && $preferences->preferences_set ? 'Update Preferences' : 'Set Up Preferences' }}
                    </a>
                </div>
            </div>
        </div>

        <div id="tab-settings" class="tab-panel" style="display:none;">
            <div class="stack">
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title"><i class="fas fa-user-edit"></i> Update Profile</h3>
                        <br>
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
                                <label>Email Address @if($emailLocked)<span class="locked-tag">Locked</span>@endif</label>
                                <div class="field-wrap">
                                    <i class="fas fa-envelope field-icon"></i>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" {{ $emailLocked ? 'readonly' : 'required' }}>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Phone @if($phoneLocked)<span class="locked-tag">Locked</span>@endif</label>
                                <div class="field-wrap">
                                    <i class="fas fa-phone field-icon"></i>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" {{ $phoneLocked ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Address</label>
                                <div class="field-wrap">
                                    <i class="fas fa-map-marker-alt field-icon"></i>
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}">
                                </div>
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
                        <br>
                        <form method="POST" action="{{ route('profile.password.update') }}" class="settings-form">
                            @csrf
                            @method('PUT')

                            <div class="form-field-inline">
                                <label>Current Password</label>
                                <div class="field-wrap">
                                    <i class="fas fa-lock field-icon"></i>
                                    <input type="password" name="current_password" required placeholder="Current Password">
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>New Password</label>
                                <div class="field-wrap">
                                    <i class="fas fa-lock field-icon"></i>
                                    <input type="password" name="password" required placeholder="New Password">
                                </div>
                            </div>

                            <div class="form-field-inline">
                                <label>Confirm New Password</label>
                                <div class="field-wrap">
                                    <i class="fas fa-lock field-icon"></i>
                                    <input type="password" name="password_confirmation" required placeholder="New Password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card danger-card">
                    <div class="card-body">
                        <h3 class="section-title danger-title"><i class="fas fa-exclamation-triangle"></i>Delete Account</h3>
                        <br>
                        <p class="muted-note">
                            Deleting your account is permanent. All bookings, ratings, and preferences will be removed.
                        </p>
                        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <div class="form-field-inline" style="max-width: 320px;">
                                <label>Type your password to confirm</label>
                                <div class="field-wrap">
                                    <i class="fas fa-key field-icon"></i>
                                    <input type="password" name="password" required placeholder="Password">
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete My Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-page {
    background: #F8FAFC;
    min-height: calc(100vh - 70px);
    padding: 24px 0 40px;
}

.profile-wrap {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.profile-header .card-body {
    padding: 24px;
}

.profile-top {
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
}

.profile-avatar {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: #1B5E20;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    font-weight: 800;
    position: relative;
    border: 1px solid #D1D5DB;
}

.avatar-badge {
    position: absolute;
    right: -2px;
    bottom: -2px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #B26A00;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    border: 2px solid #fff;
}

.profile-meta h1 {
    margin: 0 0 4px;
    font-size: 24px;
    color: #111827;
}

.profile-meta p {
    margin: 0 0 4px;
    color: #4B5563;
}

.profile-meta span {
    color: #6B7280;
    font-size: 13px;
}

.stats-grid {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.stat-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
}

.stat-value {
    font-size: 26px;
    font-weight: 800;
    color: #1B5E20;
    line-height: 1;
}

.stat-label {
    margin-top: 6px;
    font-size: 12px;
    color: #6B7280;
}

.profile-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.profile-tab {
    border: 1px solid #D1D5DB;
    background: #FFFFFF;
    color: #374151;
    border-radius: 10px;
    padding: 10px 16px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.profile-tab.active {
    background: #1B5E20;
    color: #fff;
    border-color: #1B5E20;
}

.profile-alert {
    margin: 0;
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.section-title {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.summary-box {
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    background: #FAFAFA;
    padding: 18px;
    text-align: center;
}

.summary-number {
    font-size: 28px;
    font-weight: 800;
    color: #1B5E20;
}

.summary-label {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
}

.details-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.mini-label {
    font-size: 12px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.chip-wrap {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.chip {
    border: 1px solid #CFE7D1;
    background: #F3FAF3;
    color: #1B5E20;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #EEF2F7;
    color: #374151;
}

.detail-row strong {
    color: #111827;
}

.muted-note {
    color: #6B7280;
    font-size: 14px;
    margin-bottom: 16px;
}

.item-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.list-item {
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    background: #FFFFFF;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.list-left {
    flex: 1;
    min-width: 220px;
}

.list-title {
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}

.list-subtitle {
    font-size: 13px;
    color: #6B7280;
}

.list-right {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.rating-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.rating-card {
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    background: #FFFFFF;
    padding: 16px;
}

.stars {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    color: #111827;
}

.review-text {
    margin: 12px 0 0;
    color: #4B5563;
    font-style: italic;
    line-height: 1.6;
    border-left: 3px solid #E5E7EB;
    padding-left: 12px;
}

.empty-state {
    text-align: center;
    padding: 32px 16px;
    color: #6B7280;
}

.empty-state i {
    font-size: 42px;
    color: #D1D5DB;
    margin-bottom: 12px;
}

.stack {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.settings-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 520px;
}

.form-field-inline {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-field-inline label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.field-wrap {
    position: relative;
}

.field-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
    pointer-events: none;
}

.form-field-inline input,
.form-field-inline select {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 1px solid #D1D5DB;
    border-radius: 10px;
    background: #FFFFFF;
    color: #111827;
    outline: none;
    box-sizing: border-box;
}

.form-field-inline input:focus,
.form-field-inline select:focus {
    border-color: #1B5E20;
    box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.10);
}

.locked-tag {
    font-size: 12px;
    color: #166534;
    font-weight: 600;
    margin-left: 6px;
}

.danger-card {
    border: 1px solid #F5C2C7;
    background: #FFF7F7;
}

.danger-title {
    color: #B91C1C;
}

.btn-danger {
    background: #B91C1C;
    color: #fff;
    border: none;
}

.mt-24 {
    margin-top: 24px;
}

@media (max-width: 900px) {
    .grid-2,
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 640px) {
    .grid-2,
    .stats-grid,
    .summary-grid {
        grid-template-columns: 1fr;
    }

    .profile-meta h1 {
        font-size: 20px;
    }
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