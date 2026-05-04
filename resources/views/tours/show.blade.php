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
                </div>
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
                                        <p style="color: var(--color-text-light); margin-bottom: var(--space-md); font-size: 14px;">
                                            {{ $checkpoint->short_description }}
                                        </p>

                                        @if($checkpoint->facts->count() > 0)
                                            <div style="display: flex; flex-wrap: wrap; gap: var(--space-sm);">
                                                @foreach($checkpoint->facts->take(2) as $fact)
                                                    <span class="badge badge-primary">
                                                        <i class="{{ $fact->icon_class }}"></i>
                                                        {{ Str::limit($fact->title, 30) }}
                                                    </span>
                                                @endforeach
                                                @if($checkpoint->facts->count() > 2)
                                                    <span class="badge" style="background: #E0E0E0; color: var(--color-text);">
                                                        +{{ $checkpoint->facts->count() - 2 }} more facts
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($checkpoint->estimated_time_from_previous)
                                    <div style="text-align: right;">
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
        @endif
    </div>
    
</section>

<section style="background: var(--color-primary); padding: var(--space-2xl) 0; color: white;">
    <div class="container text-center">
        <h2 style="font-size: 28px; font-weight: 700; color: white; margin-bottom: var(--space-md);">
            Ready to Start This Adventure?
        </h2>
        <p style="font-size: 16px; color: rgba(255,255,255,0.9); margin-bottom: var(--space-xl);">
            Book now and trek with confidence. GPS tracking included.
        </p>
        <a href="{{ route('bookings.create', $package) }}" class="shiny-tbg btn btn-cta btn-lg">
            <i class="fas fa-ticket-alt"></i>
            Book for Rs. {{ number_format($package->price, 0) }}
        </a>
    </div>
</section>
@endsection

@push('scripts')
@include('components.map-config')
@include('components.routing-helper')

<script>
    let map;
    let routeDrawn = false;
    let tourDataCache = null; // Variable to store the fetched data

    async function initMap() {
        // 1. Get the current style directly inside the function
        const styleId = document.getElementById('style').value;

        // 2. Destroy the existing map instance before recreating it
        if (map) {
            map.remove(); 
            routeDrawn = false; // Reset route flag so it redraws on the new map
        }

        // 3. Initialize the new map
        map = createMap('tourMap', {
            center: [{{ $package->start_lat }}, {{ $package->start_lng }}],
            zoom: 10,
            style: styleId
        });

        // 4. Fetch data only if we haven't already (saves server bandwidth)
        if (!tourDataCache) {
            const response = await fetch('{{ route('tours.route', $package) }}');
            tourDataCache = await response.json();
        }

        const data = tourDataCache;

        // Add Start Marker
        L.marker([data.package.start_lat, data.package.start_lng], {
            icon: L.divIcon({
                html: '<div style="background: #1B5E20; color: white; padding: 8px 12px; border-radius: 8px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">START</div>',
                className: '',
                iconSize: [60, 30]
            })
        }).addTo(map).bindPopup(`<strong>Start:</strong> ${data.package.start_location_name}`);

        // Add Checkpoint Markers
        data.checkpoints.forEach((checkpoint) => {
            L.marker([checkpoint.latitude, checkpoint.longitude], {
                icon: L.divIcon({
                    html: `<div style="background: #1B5E20; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">${checkpoint.order}</div>`,
                    className: '',
                    iconSize: [40, 40]
                })
            }).addTo(map).bindPopup(`<strong>${checkpoint.name}</strong><br>${checkpoint.short_description || ''}`);
        });

        // Add End Marker
        L.marker([data.package.end_lat, data.package.end_lng], {
            icon: L.divIcon({
                html: '<div style="background: #D32F2F; color: white; padding: 8px 12px; border-radius: 8px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">END</div>',
                className: '',
                iconSize: [60, 30]
            })
        }).addTo(map).bindPopup(`<strong>End:</strong> ${data.package.end_location_name}`);

        // Draw Route
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

    // Initialize the map on page load
    document.addEventListener('DOMContentLoaded', initMap);
</script>

@push('styles')
<style>

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
    border:none;
    border-bottom: 2px solid var(--color-primary-dark);
    padding: 10px 10px 10px 10px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text);
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 10px;
}

.map-select:hover {
    border-color: var(--color-primary);
}

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
    0% {
        left: -150%;
    }
    100% {
        left: 150%;
    }
}

</style>
@endpush
@endpush