@extends('layouts.app')

@section('title', $package->name)

@section('content')
<section style="background: white; border-bottom: 1px solid #E0E0E0;">
    <div class="container" style="padding-top: var(--space-lg); padding-bottom: var(--space-lg);">
        <div style="display: grid; grid-template-columns: 1fr auto; gap: var(--space-xl); align-items: start;">
            <div>
                <div style="margin-bottom: var(--space-md);">
                    <a href="{{ route('home') }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px;">
                        <i class="fas fa-arrow-left"></i> Back to Treks
                    </a>
                </div>

                <div style="margin-bottom: var(--space-sm);">
                    @if($package->difficulty_level === 'easy')
                        <span class="badge badge-success">
                            <i class="fas fa-circle"></i> Easy Trek
                        </span>
                    @elseif($package->difficulty_level === 'moderate')
                        <span class="badge badge-warning">
                            <i class="fas fa-circle"></i> Moderate Trek
                        </span>
                    @else
                        <span class="badge badge-error">
                            <i class="fas fa-circle"></i> Hard Trek
                        </span>
                    @endif
                </div>

                <h1 style="font-size: 32px; font-weight: 700; margin-bottom: var(--space-md); color: var(--color-text);">
                    {{ $package->name }}
                </h1>

                @if($package->image)
                    <div style="margin-bottom: var(--space-lg);">
                        <img
                            src="{{ $package->image }}"
                            alt="{{ $package->name }}"
                            style="width: 100%; max-height: 420px; object-fit: cover; border-radius: 16px; box-shadow: var(--shadow-md);"
                        >
                    </div>
                @endif

                <div class="flex gap-lg" style="flex-wrap: wrap; font-size: 14px; color: var(--color-text-light);">
                    
                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                        <i class="fas fa-calendar" style="color: var(--color-primary);"></i>
                        <span>{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}</span>
                    </div>
                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                        <i class="fas fa-map-marker-alt" style="color: var(--color-primary);"></i>
                        <span>{{ $package->checkpoints->count() }} Checkpoints</span>
                    </div>
                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                        <i class="fas fa-users" style="color: var(--color-primary);"></i>
                        <span>Max {{ $package->max_participants }} People</span>
                    </div>
                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                        <i class="fas fa-route" style="color: var(--color-primary);"></i>
                        <span id="routeDistance">Calculating route...</span>
                    </div>
                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                        <i class="fas fa-star fa-beat-fade" style="color: var(--color-primary);"></i>
                        {{ number_format($package->rating_avg, 1) }}
                        <span style="opacity: 0.75; font-size: 11px;">({{ $package->rating_count }})</span>
                    </div>
                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                        <i class="fas fa-info" style="color: var(--color-primary);"></i>
                        <span>{{ ucfirst($package->trek_type) }}</span>
                    </div>
                </div>
                    @if(!empty($package->tags))
                    @php
                        $rawTags = is_string($package->tags)
                            ? json_decode($package->tags, true)
                            : $package->tags;

                        $tags = collect($rawTags ?? [])
                            ->filter()
                            ->map(function ($tag) {
                                $tag = trim($tag);
                                $tag = trim($tag, '[]"\'');

                                return '#' . ltrim($tag, '#');
                            })
                            ->implode(' ');
                    @endphp

                    @if(!empty($tags))
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                            <span style="color: var(--color-text-light); font-size: 16px;">
                                <i class="fas fa-tags" style="color: var(--color-text-light);"></i>
                                Tags:
                            </span>

                            <span style="color: var(--color-text-light); font-size: 16px;">
                                {{ $tags }}
                            </span>
                        </div>
                    @endif
                @endif
                @if(!empty($package->season))
                    @php
                        $rawSeason = is_string($package->season)
                            ? json_decode($package->season, true)
                            : $package->season;

                        $seasonText = collect($rawSeason ?? [])
                            ->filter()
                            ->map(function ($item) {
                                return ucfirst(trim($item));
                            })
                            ->implode(', ');
                    @endphp

                    @if(!empty($seasonText))
                        <div style="font-size: 16px; color: var(--color-text-light); margin-bottom: var(--space-md);">
                            <i class="fas fa-sun" style="color: #FFA000;"></i>
                            Best time to visit: {{ $seasonText }}
                        </div>
                    @endif
                @endif
            </div>

            <div class="card" style="min-width: 300px; box-shadow: var(--shadow-md);">
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: var(--space-lg);">
                        <div style="font-size: 14px; color: var(--color-text-light); margin-bottom: var(--space-xs);">
                            Starting from
                        </div>
                        <div style="font-size: 36px; font-weight: 700; color: var(--color-primary);">
                            Rs. {{ number_format($package->price, 0) }}
                        </div>
                        <div style="font-size: 13px; color: var(--color-text-light);">
                            per person
                        </div>
                    </div>

                    <a href="{{ route('bookings.create', $package) }}" class="shiny-tbg btn btn-cta btn-block btn-lg">
                        <i class="fas fa-ticket-alt"></i>
                        Book This Trek
                    </a>

                    <div style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid #E0E0E0; font-size: 13px; color: var(--color-text-light);">
                        <div class="flex" style="align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                            <i class="fas fa-check-circle" style="color: var(--color-success);"></i>
                            <span>Real-time GPS tracking</span>
                        </div>
                        <div class="flex" style="align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                            <i class="fas fa-check-circle" style="color: var(--color-success);"></i>
                            <span>Expert local guide</span>
                        </div>
                        <div class="flex" style="align-items: center; gap: var(--space-sm);">
                            <i class="fas fa-check-circle" style="color: var(--color-success);"></i>
                            <span>Safety monitoring</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: white;">
    <div class="container">
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-lg);">
            <i class="fas fa-route"></i> Trek Route & Checkpoints
        </h2>

        <label for="style" class="map-label">Map Layer</label>
        <select name="style" id="style" onchange="initMap()" class="map-select">
            <option value="hybrid">Satellite 🛰️</option>
            <option value="street">Street 🗺️</option>
            <option value="outdoor">Trek ⛷️</option>
        </select>

        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md);">
            <div id="tourMap" style="height: 500px; width: 100%;"></div>
        </div>

        <div style="margin-top: var(--space-lg); padding: var(--space-lg); background: #F5F5F5; border-radius: var(--radius-md);">
            <div class="grid grid-3">
                <div class="flex" style="align-items: center; gap: var(--space-md);">
                    <div style="width: 40px; height: 40px; background: #1B5E20; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        S
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--color-text-light);">Start Point</div>
                        <div style="font-weight: 600; font-size: 14px;">{{ $package->start_location_name }}</div>
                    </div>
                </div>

                <div class="flex" style="align-items: center; gap: var(--space-md);">
                    <div style="width: 40px; height: 40px; background: #1B5E20; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        {{ $package->checkpoints->count() }}
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--color-text-light);">Checkpoints</div>
                        <div style="font-weight: 600; font-size: 14px;">Auto-tracked</div>
                    </div>
                </div>

                <div class="flex" style="align-items: center; gap: var(--space-md);">
                    <div style="width: 40px; height: 40px; background: #D32F2F; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        E
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--color-text-light);">End Point</div>
                        <div style="font-weight: 600; font-size: 14px;">{{ $package->end_location_name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--color-bg);">
    <div class="container">
        <div style="max-width: 800px;">
            <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-lg);">About This Trek</h2>
            <p style="font-size: 16px; line-height: 1.8; color: var(--color-text);">
                {{ $package->description }}
            </p>
               
        </div>
    </div>
</section>

<section class="section" style="background: white;">
    <div class="container">
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-lg);">
            <i class="fas fa-list"></i> What You'll Experience
        </h2>

        @if($package->checkpoints->count() > 0)
            <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                @foreach($package->checkpoints->sortBy('order') as $checkpoint)
                    <div class="card" style="border-left: 4px solid var(--color-primary);">
                        <div class="card-body">
                            <div class="flex-between" style="align-items: start;">
                                <div class="flex" style="gap: var(--space-lg); align-items: start;">
                                    <div style="width: 50px; height: 50px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; flex-shrink: 0;">
                                        {{ $checkpoint->order }}
                                    </div>

                                    <div style="flex: 1;">
                                        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: var(--space-sm);">
                                            {{ $checkpoint->name }}
                                        </h3>

                                        @if($checkpoint->image)
                                            <img
                                                src="{{ $checkpoint->image }}"
                                                alt="{{ $checkpoint->name }}"
                                                style="width: 100%; max-width: 520px; height: auto; object-fit: cover; border-radius: 12px; margin: 10px 0 14px;"
                                            >
                                        @endif

                                        <p style="color: var(--color-text-light); font-size: 14px; line-height: 1.7; margin: 0;">
                                            {{ $checkpoint->description }}
                                        </p>

                                        @if($checkpoint->facts->count() > 0)
                                            <div style="display: flex; flex-wrap: wrap; gap: var(--space-sm); margin-top: 12px;">
                                                @foreach($checkpoint->facts->take(3) as $fact)
                                                    <span class="badge badge-primary">
                                                        <i class="{{ $fact->icon_class }}"></i>
                                                        {{ Str::limit($fact->title, 30) }}
                                                    </span>
                                                @endforeach

                                                @if($checkpoint->facts->count() > 3)
                                                    <span class="badge" style="background: #E0E0E0; color: var(--color-text);">
                                                        +{{ $checkpoint->facts->count() - 3 }} more facts
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($checkpoint->estimated_time_from_previous)
                                    <div style="text-align: right; min-width: 120px;">
                                        <div style="font-size: 12px; color: var(--color-text-light);">From previous</div>
                                        <div style="font-size: 16px; font-weight: 600; color: var(--color-primary);">
                                            {{ $checkpoint->estimated_time_from_previous }} mins
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--color-text-light);">No checkpoints added yet.</p>
        @endif
    </div>
            <br><br>
            <center>
            <div class="card" style="background: var(--color-primary-dark); max-width: 555px; box-shadow: var(--shadow-md); animation:pulseSoft 2s infinite;">
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: var(--space-lg);">
                        <div style="font-size: 14px; color: white; margin-bottom: var(--space-xs);">
                            Just at
                        </div>
                        <div style="font-size: 36px; font-weight: 700; color: white;">
                            Rs. {{ number_format($package->price, 0) }}
                                    </div>
                                    <div style="font-size: 13px; color: white;">
                                        per person
                                    </div>
                                </div>

                                <a href="{{ route('bookings.create', $package) }}" class="shiny-tbg btn btn-cta btn-block btn-lg">
                                    <i class="fas fa-ticket-alt"></i>
                                    Book This Trek Now
                                </a>

                                <div style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid #E0E0E0; font-size: 13px; color: white;">
                                    <div class="flex" style="align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                                        <i class="fas fa-check-circle" style="color: white;"></i>
                                        <span>Real-time GPS tracking</span>
                                    </div>
                                    <div class="flex" style="align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                                        <i class="fas fa-check-circle" style="color:white;"></i>
                                        <span>Ready accomodation</span>
                                    </div>
                                    <div class="flex" style="align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                                        <i class="fas fa-check-circle" style="color: white;"></i>
                                        <span>Well managed</span>
                                    </div>
                                    <div class="flex" style="align-items: center; gap: var(--space-sm); margin-bottom: var(--space-sm);">
                                        <i class="fas fa-check-circle" style="color: white;"></i>
                                        <span>Expert local guide</span>
                                    </div>
                                    <div class="flex" style="align-items: center; gap: var(--space-sm);">
                                        <i class="fas fa-check-circle" style="color: white;"></i>
                                        <span>Safety monitoring</span>
                                    </div>
                                </div>
                            </div>
                        </div>
            </center>
</section>


@endsection

@push('styles')
<style>
@keyframes pulseSoft {
    0% {
        transform: scale(1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 12px 34px rgba(27, 94, 32, 0.10);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
}
.map-select option {
    padding: 10px;
    background-color: white;
    color: var(--color-text);
    font-size: 14px;
}

.map-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--color-text-light);
    letter-spacing: 0.5px;
}

.map-select {
    appearance: none;
    background-color: transparent;
    border: none;
    border-bottom: 2px solid var(--color-primary-dark);
    padding: 10px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text);
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 10px;
}

.map-select:hover,
.map-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.1);
}

.shiny-tbg {
    position: relative;
    overflow: hidden;
}

.shiny-tbg::before {
    content: "";
    position: absolute;
    top: 0;
    left: -150%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.4) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    transform: skewX(-25deg);
    animation: shine 2.5s infinite;
}

@keyframes shine {
    0% { left: -150%; }
    100% { left: 150%; }
}
</style>
@endpush

@push('scripts')
@include('components.map-config')
@include('components.routing-helper')

<script>
    let map;
    let routeDrawn = false;
    let tourDataCache = null;

    async function initMap() {
        const styleId = document.getElementById('style').value;

        if (map) {
            map.remove();
            routeDrawn = false;
        }

        map = createMap('tourMap', {
            center: [{{ $package->start_lat }}, {{ $package->start_lng }}],
            zoom: 10,
            style: styleId
        });

        if (!tourDataCache) {
            const response = await fetch('{{ route('tours.route', $package) }}');
            tourDataCache = await response.json();
        }

        const data = tourDataCache;

        L.marker([data.package.start_lat, data.package.start_lng], {
            icon: L.divIcon({
                html: '<div style="background: #1B5E20; color: white; padding: 8px 12px; border-radius: 8px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">START</div>',
                className: '',
                iconSize: [60, 30]
            })
        }).addTo(map).bindPopup(`<strong>Start:</strong> ${data.package.start_location_name}`);

        data.checkpoints.forEach((checkpoint) => {
            L.marker([checkpoint.latitude, checkpoint.longitude], {
                icon: L.divIcon({
                    html: `<div style="background: #1B5E20; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">${checkpoint.order}</div>`,
                    className: '',
                    iconSize: [40, 40]
                })
            }).addTo(map).bindPopup(`<strong>${checkpoint.name}</strong><br>${checkpoint.description || ''}`);
        });

        L.marker([data.package.end_lat, data.package.end_lng], {
            icon: L.divIcon({
                html: '<div style="background: #D32F2F; color: white; padding: 8px 12px; border-radius: 8px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">END</div>',
                className: '',
                iconSize: [60, 30]
            })
        }).addTo(map).bindPopup(`<strong>End:</strong> ${data.package.end_location_name}`);

        const waypoints = [
            { lat: data.package.start_lat, lng: data.package.start_lng },
            { lat: data.package.end_lat, lng: data.package.end_lng },
        ];

        if (!routeDrawn) {
            const result = await drawSmartRoute(waypoints, map);
            routeDrawn = true;

            if (result && result.distance) {
                document.getElementById('routeDistance').textContent = result.distance + ' km route';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush