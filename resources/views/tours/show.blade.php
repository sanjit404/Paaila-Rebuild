@extends('layouts.app')

@section('title', $package->name)

@section('content')

<section class="trek-hero">
    @if($package->image)
        <div class="trek-hero__bg" style="background-image: url('{{ $package->image }}')"></div>
    @else
        <div class="trek-hero__bg trek-hero__bg--fallback"></div>
    @endif
    <div class="trek-hero__overlay"></div>
    <div class="container trek-hero__content">
        <div class="trek-hero__meta">
            @if($package->difficulty_level === 'easy')
                <span class="diff-badge diff-badge--easy"><i class="fas fa-circle"></i> Easy</span>
            @elseif($package->difficulty_level === 'moderate')
                <span class="diff-badge diff-badge--moderate"><i class="fas fa-circle"></i> Moderate</span>
            @else
                <span class="diff-badge diff-badge--hard"><i class="fas fa-mountain"></i> Hard</span>
            @endif
            <span class="trek-type-badge">{{ ucfirst($package->trek_type) }}</span>
        </div>
        <h1 class="trek-hero__title">{{ $package->name }}</h1>
    </div>
</section>


<div class="trek-body">
    <div class="container trek-layout">

        <div class="trek-main">
            <a href="{{ route('home') }}" class="trek-back-link">
                        <i class="fas fa-arrow-left"></i> Back to Treks
            </a>
            <div class="content-block">
            <h2 class="section-heading">
                <span class="section-heading__icon"><i class="fas fa-compass"></i></span>
                About This Trek
            </h2>
            <p class="trek-desc">{{ $package->description }}</p>
            <br>
            <div class="trek-hero__stats" style="border-top: 1px solid black;">
            </div>

            <div class="hstat">
                <i class="fas fa-calendar"></i>
                <span>{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}</span>
            </div>
            <div class="hstat">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $package->checkpoints->count() }} Checkpoints</span>
            </div>
            <div class="hstat">
                <i class="fas fa-users"></i>
                <span>Max {{ $package->max_participants }} People</span>
            </div>
            <div class="hstat">
                <i class="fas fa-route"></i>
                <span id="routeDistance">Calculating…</span>
            </div>
            <div class="hstat">
                <i class="fas fa-star"></i>
                <span>{{ number_format($package->rating_avg, 1) }}
                    <small>({{ $package->rating_count }})</small>
                </span>
            </div>

        @if(!empty($package->season))
            @php
                $rawSeason = is_string($package->season) ? json_decode($package->season, true) : $package->season;
                $seasonText = collect($rawSeason ?? [])->filter()->map(fn($i) => ucfirst(trim($i)))->implode(' · ');
            @endphp
            @if(!empty($seasonText))
                <div class="hstat">
                    <i class="fas fa-sun"></i> Best seasons: {{ $seasonText }}
                </div>
            @endif
        @endif

        @if(!empty($package->tags))
                @php
                    $rawTags = is_string($package->tags) ? json_decode($package->tags, true) : $package->tags;
                    $tagList = collect($rawTags ?? [])->filter()->map(fn($t) => '#' . ltrim(trim(trim($t,'[]"\'')), '#'));
                @endphp
                @if($tagList->isNotEmpty())
                    <div class="hstat">
                        <i class="fas fa-tags" style="color:black;"></i>Tags: 
                        @foreach($tagList as $tag)
                            <span class="trek-hero__season">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            @endif
            </div>

            <div class="content-block">
                <h2 class="section-heading">
                    <span class="section-heading__icon"><i class="fas fa-route"></i></span>
                    Trek Route & Checkpoints
                </h2>

                <div class="map-controls">
                    <label class="map-layer-label">Map Layer</label>
                    <div class="map-layer-pills">
                        <button class="layer-pill active" data-value="hybrid" onclick="switchLayer(this,'hybrid')">🛰️ Satellite</button>
                        <button class="layer-pill" data-value="street" onclick="switchLayer(this,'street')">🗺️ Street</button>
                        <button class="layer-pill" data-value="outdoor" onclick="switchLayer(this,'outdoor')">⛷️ Trek</button>
                    </div>
                </div>

                <div class="map-wrapper">
                    <div id="tourMap"></div>
                </div>

                <div class="route-endpoints">
                    <div class="endpoint endpoint--start">
                        <div class="endpoint__dot">S</div>
                        <div>
                            <div class="endpoint__label">Start Point</div>
                            <div class="endpoint__name">{{ $package->start_location_name }}</div>
                        </div>
                    </div>
                    <div class="endpoint-divider">
                        <i class="fas fa-ellipsis-v"></i>
                        <span>{{ $package->checkpoints->count() }} checkpoints</span>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                    <div class="endpoint endpoint--end">
                        <div class="endpoint__dot endpoint__dot--end">E</div>
                        <div>
                            <div class="endpoint__label">End Point</div>
                            <div class="endpoint__name">{{ $package->end_location_name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-block">
                <h2 class="section-heading">
                    <span class="section-heading__icon"><i class="fas fa-list-ol"></i></span>
                    What You'll Experience
                </h2>

                @if($package->checkpoints->count() > 0)
                    <div class="checkpoint-timeline">
                        @foreach($package->checkpoints->sortBy('order') as $i => $checkpoint)
                            <div class="cp-item">
                                <div class="cp-spine">
                                    <div class="cp-number">{{ $checkpoint->order }}</div>
                                    @if(!$loop->last)
                                        <div class="cp-line"></div>
                                    @endif
                                </div>
                                <div class="cp-card">
                                    <div class="cp-card__header">
                                        <h3 class="cp-card__title">{{ $checkpoint->name }}</h3>
                                        @if($checkpoint->estimated_time_from_previous)
                                            <div class="cp-card__time">
                                                <i class="fas fa-clock"></i>
                                                {{ $checkpoint->estimated_time_from_previous }} mins from prev
                                            </div>
                                        @endif
                                    </div>

                                    @if($checkpoint->image)
                                        <img
                                            src="{{ $checkpoint->image }}"
                                            alt="{{ $checkpoint->name }}"
                                            class="cp-card__img"
                                            loading="lazy"
                                        >
                                    @endif

                                    <p class="cp-card__desc">{{ $checkpoint->description }}</p>

                                    @if($checkpoint->facts->count() > 0)
                                        <div class="cp-facts">
                                            @foreach($checkpoint->facts->take(3) as $fact)
                                                <span class="cp-fact">
                                                    <i class="{{ $fact->icon_class }}"></i>
                                                    {{ Str::limit($fact->title, 30) }}
                                                </span>
                                            @endforeach
                                            @if($checkpoint->facts->count() > 3)
                                                <span class="cp-fact cp-fact--more">+{{ $checkpoint->facts->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="empty-state">No checkpoints listed for this trek yet.</p>
                @endif
            </div>

        </div>

        <aside class="trek-sidebar">
            <div class="booking-card">
                <div class="booking-card__price">
                    <h1 style="color:white;">{{ $package->name }}</h1>
                    <br><hr><br>
                    <div class="booking-card__from">Starting from</div>
                    <div class="booking-card__amount">Rs. {{ number_format($package->price, 0) }}</div>
                    <div class="booking-card__per">per person</div>
                </div>

                <a href="{{ route('bookings.create', $package) }}" class="btn btn-cta btn-block shiny-btn">
                    <i class="fas fa-ticket-alt"></i> Book This Trek
                </a>

                <ul class="booking-card__perks">
                    <li><i class="fas fa-check-circle"></i> Real-time GPS tracking</li>
                    <li><i class="fas fa-check-circle"></i> Expert local guide</li>
                    <li><i class="fas fa-check-circle"></i> Safety monitoring</li>
                    <li><i class="fas fa-check-circle"></i> Ready accommodation</li>
                    <li><i class="fas fa-check-circle"></i> Well managed</li>
                </ul>

                <div class="booking-card__guarantee">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure booking · Free cancellation</span>
                </div>
            </div>
        </aside>

    </div>
</div>

<div class="mobile-book-bar">
    <div>
        <h6 style="color:white;  font-size: clamp(8px, 12px, 16px);">{{ $package->name }}</h6>
        <div class="mobile-book-bar__price">Rs. {{ number_format($package->price, 0) }}</div>
        <div class="mobile-book-bar__per">per person</div>
    </div>
    <a href="{{ route('bookings.create', $package) }}" class="btn btn-cta shiny-btn">
        <i class="fas fa-ticket-alt"></i> Book Now
    </a>
</div>

@endsection


@push('styles')
<style>

.trek-hero {
    position: relative;
    min-height: 540px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
}

.trek-hero__bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transform: scale(1.04);
    transition: transform 8s ease;
}

.trek-hero:hover .trek-hero__bg {
    transform: scale(1);
}

.trek-hero__bg--fallback {
    background: linear-gradient(135deg, #0d2e14 0%, #1b5e20 60%, #2e7d32 100%);
}

.trek-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(5,14,6,0.92) 0%,
        rgba(5,14,6,0.55) 45%,
        rgba(5,14,6,0.15) 100%
    );
}

.trek-hero__content {
    position: relative;
    z-index: 2;
    padding-top: 100px;
    padding-bottom: var(--space-2xl);
}

.trek-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(0, 0, 0, 0.84);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.03em;
    margin-bottom: var(--space-lg);
    transition: color 0.2s;
}

.trek-back-link:hover {
    color: rgba(0, 0, 0, 0.4);

}

.trek-hero__meta {
    display: flex;
    gap: 10px;
    margin-bottom: var(--space-md);
    flex-wrap: wrap;
}

.diff-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.diff-badge--easy     { background: rgba(46,125,50,0.85);  color: #b9f5be; border: 1px solid rgba(100,220,100,0.3); }
.diff-badge--moderate { background: rgba(230,121,0,0.85);  color: #ffe4b0; border: 1px solid rgba(255,180,50,0.3); }
.diff-badge--hard     { background: rgba(198,40,40,0.85);  color: #ffd0d0; border: 1px solid rgba(255,100,100,0.3); }

.trek-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(255,255,255,0.15);
    color: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(4px);
    text-transform: capitalize;
}

.trek-hero__title {
    font-size: clamp(26px, 5vw, 46px);
    font-weight: 800;
    color: white;
    line-height: 1.1;
    margin-bottom: var(--space-lg);
    letter-spacing: -0.02em;
    text-shadow: 0 2px 20px rgba(0,0,0,0.4);
}

.trek-hero__stats {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: var(--space-md);
}

.hstat {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    color: rgba(0, 0, 0, 0.92);
    font-size: 13px;
    font-weight: 500;
}

.hstat i {
    color: #81c784;
    font-size: 12px;
}

.hstat small {
    opacity: 0.6;
    font-size: 11px;
}

.trek-hero__season {
    font-size: 13px;
    color: rgba(0, 0, 0, 0.65);
    display: flex;
    align-items: center;
    gap: 6px;
}

.trek-hero__season i {
    color: #ffc107;
}

.trek-body {
    background: #f7f7f5;
    padding: var(--space-2xl) 0;
}

.trek-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--space-xl);
    align-items: start;
}

.trek-main {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
    min-width: 0;
}

.content-block {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.section-heading {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 700;
    color: var(--color-text);
    margin-bottom: var(--space-lg);
}

.section-heading__icon {
    width: 36px;
    height: 36px;
    background: #e8f5e9;
    color: var(--color-primary);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}

.trek-desc {
    font-size: 15px;
    line-height: 1.85;
    color: #455a64;
    margin: 0;
}

.tag-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag {
    padding: 5px 14px;
    background: #e8f5e9;
    color: var(--color-primary-dark);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #c8e6c9;
}

.map-controls {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    margin-bottom: var(--space-md);
    flex-wrap: wrap;
}

.map-layer-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--color-text-light);
}

.map-layer-pills {
    display: flex;
    gap: 6px;
}

.layer-pill {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1.5px solid #e0e0e0;
    background: white;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text-light);
    cursor: pointer;
    transition: all 0.2s ease;
}

.layer-pill:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
}

.layer-pill.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: white;
}

.map-wrapper {
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    margin-bottom: var(--space-lg);
}

#tourMap {
    height: 400px;
    width: 100%;
}

.route-endpoints {
    display: flex;
    align-items: center;
    gap: 185px;
    padding: var(--space-md) var(--space-lg);
    background: #f5f5f5;
    border-radius: var(--radius-md);
}

.endpoint {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.endpoint__dot {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--color-primary);
    color: white;
    font-weight: 800;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(27,94,32,0.3);
}

.endpoint__dot--end {
    background: var(--color-error);
    box-shadow: 0 2px 8px rgba(211,47,47,0.3);
}

.endpoint__label {
    font-size: 11px;
    color: var(--color-text-light);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
}

.endpoint__name {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-text);
}

.endpoint-divider {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    color: var(--color-text-light);
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    flex-shrink: 0;
}


.checkpoint-timeline {
    display: flex;
    flex-direction: column;
}

.cp-item {
    display: flex;
    gap: var(--space-md);
}

.cp-spine {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
    width: 44px;
}

.cp-number {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--color-primary);
    color: white;
    font-weight: 800;
    font-size: 17px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 12px rgba(27,94,32,0.25);
    flex-shrink: 0;
    z-index: 1;
}

.cp-line {
    width: 2px;
    flex: 1;
    min-height: 24px;
    background: linear-gradient(to bottom, #c8e6c9, #e8f5e9);
    margin: 4px 0;
}

.cp-card {
    flex: 1;
    background: #fafafa;
    border: 1px solid #eeeeee;
    border-radius: var(--radius-md);
    padding: var(--space-lg);
    margin-bottom: var(--space-lg);
    min-width: 0;
    transition: box-shadow 0.2s, border-color 0.2s;
}

.cp-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    border-color: #c8e6c9;
}

.cp-card__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-md);
    margin-bottom: var(--space-sm);
    flex-wrap: wrap;
}

.cp-card__title {
    font-size: 17px;
    font-weight: 700;
    color: var(--color-text);
    margin: 0;
}

.cp-card__time {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-primary);
    background: #e8f5e9;
    padding: 3px 10px;
    border-radius: 12px;
    white-space: nowrap;
    flex-shrink: 0;
}

.cp-card__img {
    width: 100%;
    max-height: 260px;
    object-fit: cover;
    border-radius: var(--radius-sm);
    margin: var(--space-sm) 0 var(--space-md);
}

.cp-card__desc {
    font-size: 14px;
    line-height: 1.75;
    color: var(--color-text-light);
    margin: 0 0 var(--space-sm);
}

.cp-facts {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: var(--space-sm);
}

.cp-fact {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: #e8f5e9;
    color: var(--color-primary-dark);
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.cp-fact--more {
    background: #eeeeee;
    color: var(--color-text-light);
}

.empty-state {
    color: var(--color-text-light);
    font-style: italic;
    padding: var(--space-lg) 0;
}


.trek-sidebar {
    position: sticky;
    top: 140px;
}

.booking-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    overflow: hidden;
}

.booking-card__price {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    padding: var(--space-xl) var(--space-lg);
    text-align: center;
}

.booking-card__from {
    font-size: 12px;
    color: rgba(255,255,255,0.65);
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
}

.booking-card__amount {
    font-size: 34px;
    font-weight: 800;
    color: white;
    line-height: 1;
    letter-spacing: -0.02em;
}

.booking-card__per {
    font-size: 12px;
    color: rgba(255,255,255,0.65);
    margin-top: 4px;
}

.booking-card .btn {
    margin: var(--space-md);
    width: calc(100% - var(--space-xl));
}

.booking-card__perks {
    list-style: none;
    padding: 0 var(--space-lg) var(--space-md);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.booking-card__perks li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--color-text);
    font-weight: 500;
}

.booking-card__perks li i {
    color: var(--color-success);
    font-size: 14px;
    flex-shrink: 0;
}

.booking-card__guarantee {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: var(--space-md) var(--space-lg);
    background: #f5f5f5;
    font-size: 12px;
    color: var(--color-text-light);
    font-weight: 600;
    border-top: 1px solid #eeeeee;
}

.booking-card__guarantee i {
    color: var(--color-primary);
}

.shiny-btn {
    position: relative;
    overflow: hidden;
}

.shiny-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -150%;
    width: 50%;
    height: 100%;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.35) 50%, transparent 100%);
    transform: skewX(-25deg);
    animation: shine 2.8s infinite;
}

@keyframes shine {
    0%   { left: -150%; }
    100% { left: 160%; }
}


.mobile-book-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 999;
    background: var(--color-primary-dark);
    padding: 12px var(--space-lg);
    align-items: center;
    justify-content: space-between;
    gap: var(--space-md);
    box-shadow: 0 -4px 24px rgba(0,0,0,0.2);
}

.mobile-book-bar__from {
    font-size: 10px;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
}

.mobile-book-bar__price {
    font-size: 20px;
    font-weight: 800;
    color: white;
    line-height: 1.2;
}

.mobile-book-bar__per {
    font-size: 10px;
    color: rgba(255,255,255,0.6);
}


@media (max-width: 960px) {
    .trek-layout {
        grid-template-columns: 1fr;
    }

    .trek-sidebar {
        display: none;
    }

    .mobile-book-bar {
        display: flex;
    }

    body {
        padding-bottom: 80px;
    }

    .cta-band__inner {
        flex-direction: column;
        text-align: center;
    }

    
}

@media (max-width: 640px) {
    .trek-hero {
        min-height: 420px;
    }

    .trek-hero__title {
        font-size: 26px;
    }

    #tourMap {
        height: 280px !important;
    }

    .content-block {
        padding: var(--space-lg);
    }

    .route-endpoints {
        flex-direction: column;
        align-items: flex-start;
    }

    .endpoint-divider {
        flex-direction: row;
        padding-left: calc(38px + 12px);
        color: #bdbdbd;
    }

    .cp-card__header {
        flex-direction: column;
    }
}

</style>
@endpush


@push('scripts')
@include('components.map-config')
@include('components.routing-helper')

<script>
    let map;
    let routeDrawn   = false;
    let tourDataCache = null;
    let currentLayer  = 'hybrid';

    function switchLayer(btn, value) {
        document.querySelectorAll('.layer-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentLayer = value;
        initMap();
    }

    async function initMap() {
        if (map) {
            map.remove();
            routeDrawn = false;
        }

        map = createMap('tourMap', {
            center: [{{ $package->start_lat }}, {{ $package->start_lng }}],
            zoom: 10,
            style: currentLayer
        });

        if (!tourDataCache) {
            const res = await fetch('{{ route('tours.route', $package) }}');
            tourDataCache = await res.json();
        }

        const data = tourDataCache;

        const startIcon = L.divIcon({
            html: '<div style="background:#1B5E20;color:white;padding:6px 11px;border-radius:7px;font-weight:800;font-size:12px;box-shadow:0 2px 10px rgba(0,0,0,0.25);white-space:nowrap;">START</div>',
            className: '',
            iconSize: [58, 28]
        });

        const endIcon = L.divIcon({
            html: '<div style="background:#D32F2F;color:white;padding:6px 11px;border-radius:7px;font-weight:800;font-size:12px;box-shadow:0 2px 10px rgba(0,0,0,0.25);white-space:nowrap;">END</div>',
            className: '',
            iconSize: [52, 28]
        });

        L.marker([data.package.start_lat, data.package.start_lng], { icon: startIcon })
            .addTo(map)
            .bindPopup(`<strong>Start:</strong> ${data.package.start_location_name}`);

        data.checkpoints.forEach(cp => {
            const icon = L.divIcon({
                html: `<div style="background:#1B5E20;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,0.25);">${cp.order}</div>`,
                className: '',
                iconSize: [36, 36]
            });
            L.marker([cp.latitude, cp.longitude], { icon })
                .addTo(map)
                .bindPopup(`<strong>${cp.name}</strong>${cp.description ? '<br><span style="font-size:13px;color:#555">' + cp.description + '</span>' : ''}`);
        });

        L.marker([data.package.end_lat, data.package.end_lng], { icon: endIcon })
            .addTo(map)
            .bindPopup(`<strong>End:</strong> ${data.package.end_location_name}`);

        if (!routeDrawn) {
            const waypoints = [
                { lat: data.package.start_lat, lng: data.package.start_lng },
                { lat: data.package.end_lat,   lng: data.package.end_lng   }
            ];
            const result = await drawSmartRoute(waypoints, map);
            routeDrawn = true;
            if (result && result.distance) {
                document.getElementById('routeDistance').textContent = result.distance + ' km route';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', initMap);

    (function () {
        const bar  = document.getElementById('stickyBar');
        const hero = document.querySelector('.trek-hero');
        if (!bar || !hero) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                bar.classList.toggle('visible', !entry.isIntersecting);
            },
            { rootMargin: '-70px 0px 0px 0px' }
        );
        observer.observe(hero);
    })();
</script>
@endpush