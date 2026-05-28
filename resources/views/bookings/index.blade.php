@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px);">
    <div class="container" style="padding-top: var(--space-xl); padding-bottom: var(--space-xl);">

        <div style="margin-bottom: var(--space-xl);">
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: var(--space-sm);">My Bookings</h1>
            <p style="color: var(--color-text-light); margin: 0;">Your trek history and recommended adventures</p>
        @if(!$bookings->isEmpty())
            <a href="#recomm"
                       style="font-size: 14px; color: var(--color-primary); text-decoration: underline; font-weight: 600; white-space: nowrap;">
                       View Recommended Treks <i class="fa-solid fa-person-hiking fa-shake"></i>
            </a>
            <br>
            <a href="{{ route('tour.foryou') }}"
                       style="font-size: 14px; color: var(--color-primary); text-decoration: underline; font-weight: 600; white-space: nowrap;">
                       View Trending Treks <i class="fa-solid fa-fire fa-shake"></i>
            </a>
        @endif
        </div>



        @if($bookings->isEmpty())
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <i class="fas fa-hiking" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-lg);"></i>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">No Bookings Yet</h3>
                    <p style="color: var(--color-text-light); margin-bottom: var(--space-xl);">Your trek adventures will appear here</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-mountain"></i> Browse Treks
                    </a>
                </div>
            </div>

        @else

            <div id="hiss" style="display: flex; flex-direction: column; gap: var(--space-lg);">

                @foreach($bookings as $booking)
                @php
                    $canRate  = $booking->status === 'completed'
                             && !\App\Models\TrekRating::hasRated(auth()->id(), $booking->tour_package_id);
                    $myRating = \App\Models\TrekRating::getRating(auth()->id(), $booking->tour_package_id);
                    $statusColors = [
                        'pending'   => 'var(--color-warning)',
                        'confirmed' => 'var(--color-success)',
                        'active'    => '#2196F3',
                        'completed' => '#9E9E9E',
                        'cancelled' => 'var(--color-error)',
                    ];
                    $accentColor = $statusColors[$booking->status] ?? '#ccc';
                @endphp

                <div class="card" style="overflow: hidden;">
                    <div style="display: flex;">

                        <div style="width: 5px; flex-shrink: 0; background: {{ $accentColor }};"></div>

                        <div style="flex: 1; padding: var(--space-xl); min-width: 0;">

                            <div class="flex-between" style="flex-wrap: wrap; gap: var(--space-md); margin-bottom: var(--space-lg); align-items: flex-start;">
                                <div>
                                    <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: 4px;">
                                        {{ $booking->booking_number }}
                                    </div>
                                    <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                                        {{ $booking->tourPackage->name }}
                                    </h3>
                                </div>

                                @if($booking->status === 'pending')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pending Payment
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Confirmed
                                    </span>
                                @elseif($booking->status === 'active')
                                    <span class="badge" style="background: #E3F2FD; color: #1976D2;">
                                        <i class="fas fa-hiking"></i> In Progress
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge" style="background: #F5F5F5; color: #666;">
                                        <i class="fas fa-flag-checkered"></i> Completed
                                    </span>
                                @else
                                    <span class="badge badge-error">
                                        <i class="fas fa-times-circle"></i> Cancelled
                                    </span>
                                @endif
                            </div>

                            {{-- Trek details row --}}
                            <div style="display: flex; gap: var(--space-xl); flex-wrap: wrap; font-size: 14px; color: var(--color-text-light); margin-bottom: var(--space-lg);">
                                <span><i class="fas fa-calendar"></i> {{ $booking->tour_date->format('M d, Y') }}</span>
                                <span><i class="fas fa-users"></i> {{ $booking->participants }} {{ Str::plural('person', $booking->participants) }}</span>
                                <span><i class="fas fa-credit-card"></i> {{ ucfirst($booking->payment_method) }}</span>
                                <span style="font-weight: 600; color: var(--color-primary);">
                                    <i class="fas fa-money-bill"></i> Rs. {{ number_format($booking->total_amount, 0) }}
                                </span>
                            </div>

                            @if($booking->status === 'completed')
                                <div style="padding: var(--space-lg); background: #F9F9F9; border: 1px solid #E8E8E8; border-radius: var(--radius-md); margin-bottom: var(--space-lg);">

                                    @if($myRating)
                                        {{-- READ state — immutable --}}
                                        <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: var(--space-md);">
                                            <div>
                                                <div style="font-size: 12px; color: var(--color-text-light); font-weight: 600; text-transform: uppercase; margin-bottom: var(--space-sm);">
                                                    Your Rating
                                                </div>
                                                <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star" style="font-size: 22px; color: {{ $i <= $myRating->rating ? '#FFC107' : '#E0E0E0' }};"></i>
                                                    @endfor
                                                    <span style="font-size: 16px; font-weight: 700; color: var(--color-text);">
                                                        {{ $myRating->rating }}/5
                                                    </span>
                                                    <span style="font-size: 13px; color: var(--color-text-light);">
                                                        — {{ $myRating->star_label }}
                                                    </span>
                                                </div>
                                                @if($myRating->review)
                                                    <p style="font-size: 14px; color: var(--color-text-light); margin: 0; font-style: italic; line-height: 1.5;">
                                                        "{{ $myRating->review }}"
                                                    </p>
                                                @endif
                                            </div>
                                            <div style="text-align: right; flex-shrink: 0;">
                                                <div style="font-size: 11px; color: var(--color-text-light);">
                                                    <i class="fas fa-lock"></i>
                                                    Rated {{ $myRating->created_at->format('M d, Y') }}
                                                </div>
                                                <div style="font-size: 11px; color: var(--color-text-light); margin-top: 2px;">
                                                    Cannot be changed
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($canRate)
                                        <div>
                                            <div style="font-size: 14px; font-weight: 600; margin-bottom: var(--space-md); color: var(--color-text);">
                                                <i class="fas fa-star" style="color: #FFC107;"></i>
                                                How was your trek? Rate it once — helps other trekkers.
                                            </div>

                                            <form method="POST"
                                                  action="{{ route('trek.rate', $booking) }}"
                                                  id="rateForm{{ $booking->id }}"
                                                  onsubmit="return confirmRating(event, {{ $booking->id }})">
                                                @csrf

                                                <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-lg);">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <button type="button"
                                                                onclick="setStar({{ $booking->id }}, {{ $i }})"
                                                                onmouseenter="previewStar({{ $booking->id }}, {{ $i }})"
                                                                onmouseleave="clearPreview({{ $booking->id }})"
                                                                style="background: none; border: none; cursor: pointer; padding: 2px; line-height: 1;">
                                                            <i class="fas fa-star"
                                                               id="s{{ $booking->id }}_{{ $i }}"
                                                               style="font-size: 30px; color: #E0E0E0; transition: color 0.1s, transform 0.1s;"></i>
                                                        </button>
                                                    @endfor
                                                    <input type="hidden"
                                                           name="rating"
                                                           id="ratingVal{{ $booking->id }}"
                                                           value="">
                                                    <span id="ratingLbl{{ $booking->id }}"
                                                          style="font-size: 14px; color: var(--color-text-light); margin-left: var(--space-sm);">
                                                        Select a rating
                                                    </span>
                                                </div>

                                                <textarea
                                                    name="review"
                                                    placeholder="Share your experience (optional) — max 1000 characters"
                                                    maxlength="1000"
                                                    rows="2"
                                                    style="width: 100%; padding: var(--space-md); border: 2px solid #E0E0E0; border-radius: var(--radius-md); font-size: 14px; resize: vertical; font-family: inherit; color: var(--color-text); margin-bottom: var(--space-md); box-sizing: border-box;"
                                                    onfocus="this.style.borderColor='var(--color-primary)'"
                                                    onblur="this.style.borderColor='#E0E0E0'"
                                                ></textarea>

                                                <div style="display: flex; align-items: center; gap: var(--space-md);">
                                                    <button type="submit"
                                                            id="submitBtn{{ $booking->id }}"
                                                            disabled
                                                            class="btn btn-primary"
                                                            style="opacity: 0.45; cursor: not-allowed;">
                                                        <i class="fas fa-paper-plane"></i> Submit Rating
                                                    </button>
                                                    <span style="font-size: 12px; color: var(--color-text-light);">
                                                        <i class="fas fa-lock"></i>
                                                        Cannot be changed after submission
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                </div>
                            @endif

                            <div style="display: flex; gap: var(--space-md); flex-wrap: wrap;">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                @if($booking->status === 'completed')
                                        <button
                                            onclick="reliveTrip({{ $booking->id }})"
                                            class="btn btn-relive btn-sm">
                                            <i class="fas fa-film"></i> Relive This Trip
                                        </button>
                                    @endif
                                @if($booking->status === 'active')
                                    <a href="{{ route('tracking.traveler', $booking) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-map-marker-alt"></i> Continue Tracking
                                    </a>
                                @endif

                                @if($booking->status === 'confirmed')
                                    <form method="POST" action="{{ route('bookings.start', $booking) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-cta btn-sm">
                                            <i class="fas fa-play-circle"></i> Start Trek
                                        </button>
                                    </form>
                                @endif

                                @if($booking->status === 'pending')
                                    <a href="{{ route('payment.' . $booking->payment_method, $booking) }}" class="btn btn-cta btn-sm">
                                        <i class="fas fa-credit-card"></i> Complete Payment
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                @endforeach

            </div>
            @include('bookings.partials.relive-modal')
        @endif

        @php
            $likedEarlier = \App\Services\RecommendationService::getLikedEarlier(auth()->id(), 4);
        @endphp

        @if($likedEarlier->isNotEmpty())
            <div id="recomm" style="margin-top: var(--space-2xl); padding-top: var(--space-xl); border-top: 2px solid #E0E0E0;">

                <div class="flex-between" style="margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-md);">
                    <div>
                        <h2 style="font-size: 22px; font-weight: 700; margin-bottom: var(--space-xs); display: flex; align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-clover" style="color: #1c7a2a; font-size: 20px;"></i>
                            Recommended For You
                        </h2>
                        <p style="color: var(--color-text-light); font-size: 14px; margin: 0;">
                            Based on treks you rated 4 stars or higher
                        </p>
                    </div>
                    <a href="#hiss"
                       style="font-size: 14px; color: var(--color-primary); text-decoration: none; font-weight: 600; white-space: nowrap;">
                       Go Up <i class="fas fa-arrow-up"></i>
                    </a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: var(--space-xl);">
                    @foreach($likedEarlier as $package)
                    <div class="rec-card"
                         onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)';"
                         onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';">

                        <a href="{{ route('tours.show', $package) }}" style="display: block; text-decoration: none;">
                            <div style="height: 170px; position: relative; overflow: hidden; border-radius: var(--radius-lg) var(--radius-lg) 0 0;
                                        background: linear-gradient(135deg, var(--color-primary) 0%, #2E7D32 100%);">
                                @if($package->image ?? false)
                                    <img src="{{ $package->image }}" alt="{{ $package->name }}"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-mountain" style="font-size: 48px; color: rgba(255,255,255,0.25);"></i>
                                    </div>
                                @endif

                                @if($package->trek_type ?? false)
                                    <div style="position: absolute; top: 10px; left: 10px;">
                                        <span style="background: rgba(0,0,0,0.55); color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize;">
                                            {{ $package->trek_type }}
                                        </span>
                                    </div>
                                @endif

                                @if(($package->rating_count ?? 0) > 0)
                                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 3px 9px; border-radius: 12px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-star" style="color: #FFC107; font-size: 11px;"></i>
                                        {{ number_format($package->rating_avg, 1) }}
                                        <span style="opacity: 0.75; font-size: 11px;">({{ $package->rating_count }})</span>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div style="padding: var(--space-lg);">

                            @if($package->liked_reason ?? false)
                                <div style="display: inline-flex; align-items: center; gap: 5px;
                                            background: #FCE4EC; color: #C2185B;
                                            padding: 4px 10px; border-radius: 20px;
                                            font-size: 11px; font-weight: 600;
                                            margin-bottom: var(--space-md);">
                                    <i class="fas fa-heart" style="font-size: 10px;"></i>
                                    {{ $package->liked_reason }}
                                </div>
                            @endif

                            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: var(--space-sm); color: var(--color-text); line-height: 1.3;">
                                <a href="{{ route('tours.show', $package) }}" style="text-decoration: none; color: inherit;">
                                    {{ $package->name }}
                                </a>
                            </h3>

                            <p style="font-size: 13px; color: var(--color-text-light); margin-bottom: var(--space-md); line-height: 1.5;">
                                {{ Str::limit($package->description, 75) }}
                            </p>

                            <div style="display: flex; gap: var(--space-md); font-size: 12px; color: var(--color-text-light); margin-bottom: var(--space-lg); flex-wrap: wrap;">
                                <span><i class="fas fa-calendar"></i> {{ $package->duration_days }} {{ Str::plural('day', $package->duration_days) }}</span>
                                <span style="text-transform: capitalize;"><i class="fas fa-signal"></i> {{ $package->difficulty_level }}</span>
                                @if($package->region ?? false)
                                    <span><i class="fas fa-map-pin"></i> {{ $package->region }}</span>
                                @endif
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: var(--space-md); border-top: 1px solid #F0F0F0;">
                                <div>
                                    <div style="font-size: 18px; font-weight: 700; color: var(--color-primary);">
                                        Rs. {{ number_format($package->price, 0) }}
                                    </div>
                                    <div style="font-size: 11px; color: var(--color-text-light);">per person</div>
                                </div>
                                <a href="{{ route('tours.show', $package) }}" class="btn btn-primary btn-sm">
                                    View <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
        @endif
        {{-- end liked earlier --}}

    </div>
</section>

@push('styles')
<style>
    html,body{
        scroll-behavior: smooth;
    }
    .rec-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
</style>
@endpush

@push('scripts')
<script>
var starLabels = { 1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Great', 5: 'Outstanding!' };

function setStar(bookingId, value) {
    for (var i = 1; i <= 5; i++) {
        var star = document.getElementById('s' + bookingId + '_' + i);
        star.style.color     = i <= value ? '#FFC107' : '#E0E0E0';
        star.style.transform = i === value ? 'scale(1.25)' : 'scale(1)';
    }
    document.getElementById('ratingVal' + bookingId).value = value;
    document.getElementById('ratingLbl' + bookingId).textContent = starLabels[value] || '';

    var btn = document.getElementById('submitBtn' + bookingId);
    btn.disabled      = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';
}

function previewStar(bookingId, value) {
    if (document.getElementById('ratingVal' + bookingId).value) return;
    for (var i = 1; i <= 5; i++) {
        document.getElementById('s' + bookingId + '_' + i).style.color = i <= value ? '#FFD54F' : '#E0E0E0';
    }
}

function clearPreview(bookingId) {
    if (document.getElementById('ratingVal' + bookingId).value) return;
    for (var i = 1; i <= 5; i++) {
        document.getElementById('s' + bookingId + '_' + i).style.color = '#E0E0E0';
    }
}

function confirmRating(event, bookingId) {
    var stars = document.getElementById('ratingVal' + bookingId).value;
    if (!stars) { event.preventDefault(); return false; }
    return confirm(
        'Submit ' + stars + '-star rating?\n\nThis rating CANNOT be changed after submission.'
    );
}
</script>
@endpush
@endsection