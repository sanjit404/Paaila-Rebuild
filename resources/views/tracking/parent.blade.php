@extends('layouts.app')

@section('title', 'Monitor Trek - ' . $booking->tourPackage->name)

@section('content')
<div style="display: flex; height: calc(100vh - 70px); position: relative;">
    <div id="map" style="flex: 1; position: relative;"></div>

    <div style="width: 400px; background: white; box-shadow: -2px 0 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: var(--space-lg); background: var(--color-primary); color: white;">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: var(--space-xs);">MONITORING</div>
            <h2 style="font-size: 18px; font-weight: 700; color: white; margin-bottom: var(--space-sm);">{{ $booking->user->name }}</h2>
            <p style="font-size: 13px; opacity: 0.9; margin: 0;">{{ $booking->tourPackage->name }}</p>
        </div>

        <div style="flex: 1; overflow-y: auto;">

            <div id="onlineStatus" style="padding: var(--space-md) var(--space-lg); display: flex; align-items: center; gap: var(--space-md); border-bottom: 1px solid #E0E0E0; font-weight: 500; font-size: 14px;">
                <span id="statusDot" class="status-dot"></span>
                <span id="statusText">Connecting...</span>
            </div>

            <div style="padding: var(--space-lg); border-bottom: 1px solid #E0E0E0;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="text-align: center; padding: var(--space-md); background: #F5F5F5; border-radius: var(--radius-md);">
                        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);" id="completedCount">
                            {{ $booking->completed_checkpoints }}
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-light); margin-top: var(--space-xs);">REACHED</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: #F5F5F5; border-radius: var(--radius-md);">
                        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);" id="progressPercent">
                            {{ $booking->progress_percentage }}%
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-light); margin-top: var(--space-xs);">COMPLETE</div>
                    </div>
                </div>

                <div style="background: #E0E0E0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: var(--space-sm);">
                    <div id="progressBar" style="height: 100%; background: var(--color-primary); width: {{ $booking->progress_percentage }}%; transition: width 0.5s ease;"></div>
                </div>
                <p id="progressText" style="text-align: center; font-size: 13px; color: var(--color-text-light); margin: 0;">
                    <span id="completedText">{{ $booking->completed_checkpoints }}</span> of
                    <span id="totalCount">{{ $booking->total_checkpoints }}</span> checkpoints
                </p>
            </div>
<label for="style" class="map-label">Map Layer</label>
    <select name="style" id="style" onchange="initMap()" class="map-select">
        <option value="hybrid">Satellite 🛰️</option>
        <option value="street">Street 🗺️</option>
        <option value="outdoor">Trek ⛷️</option>
    </select>
            <div id="locationDetails" style="padding: var(--space-lg); border-bottom: 1px solid #E0E0E0;">
                <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-md);">
                    <i class="fas fa-map-pin" style="color: var(--color-primary);"></i>
                    <h4 style="font-size: 14px; font-weight: 700; margin: 0; text-transform: uppercase; color: var(--color-text-light);">Current Location</h4>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); font-size: 13px;">
                    <div>
                        <div style="color: var(--color-text-light); margin-bottom: 4px;">Coordinates</div>
                        <div id="coordinates" style="font-weight: 600; font-family: monospace; color: var(--color-text);">-</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-light); margin-bottom: 4px;">Accuracy</div>
                        <div id="accuracy" style="font-weight: 600; color: var(--color-text);">-</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-light); margin-bottom: 4px;">Speed</div>
                        <div id="speed" style="font-weight: 600; color: var(--color-text);">-</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-light); margin-bottom: 4px;">Battery</div>
                        <div id="battery" style="font-weight: 600; color: var(--color-text);">-</div>
                    </div>
                </div>
            </div>

            <div style="padding: var(--space-lg); background: #F5F5F5; border-bottom: 1px solid #E0E0E0;">
                <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: var(--space-md); text-transform: uppercase; font-weight: 600;">Trek Information</div>
                <div style="display: flex; flex-direction: column; gap: var(--space-sm); font-size: 13px;">
                    <div class="flex-between">
                        <span style="color: var(--color-text-light);">Date</span>
                        <span style="font-weight: 600;">{{ $booking->tour_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex-between">
                        <span style="color: var(--color-text-light);">Trekkers</span>
                        <span style="font-weight: 600;">{{ $booking->participants }}</span>
                    </div>
                    <div class="flex-between">
                        <span style="color: var(--color-text-light);">Started</span>
                        <span style="font-weight: 600;">{{ $booking->started_at ? $booking->started_at->diffForHumans() : 'Not started' }}</span>
                    </div>
                </div>
            </div>

            <div style="padding: var(--space-lg);">
                <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-md);">
                    <i class="fas fa-list" style="color: var(--color-primary);"></i>
                    <h4 style="font-size: 14px; font-weight: 700; margin: 0; text-transform: uppercase; color: var(--color-text-light);">Checkpoints</h4>
                </div>

                <div id="checkpointsList" style="display: flex; flex-direction: column; gap: var(--space-sm);">
                    @foreach($booking->tourPackage->checkpoints->sortBy('order') as $checkpoint)
                        @php
                            $progress = $booking->checkpointProgress->where('checkpoint_id', $checkpoint->id)->first();
                            $reached  = $progress && $progress->reached_at;
                        @endphp
                        <div
                            id="checkpoint-{{ $checkpoint->id }}"
                            class="checkpoint-item {{ $reached ? 'completed' : '' }}"
                            style="padding: var(--space-md); background: {{ $reached ? '#E8F5E9' : '#F5F5F5' }}; border-radius: var(--radius-md); display: flex; gap: var(--space-md); align-items: center; transition: all 0.3s ease;">

                            <div style="width: 32px; height: 32px; background: {{ $reached ? '#2E7D32' : '#B0BEC5' }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                                {{ $checkpoint->order }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px; color: var(--color-text);">{{ $checkpoint->name }}</div>
                                @if($reached)
                                    <div style="font-size: 12px; color: #2E7D32; display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="reached-time">{{ $progress->reached_at->diffForHumans() }}</span>
                                    </div>
                                @else
                                    <div class="not-reached-label" style="font-size: 12px; color: var(--color-text-light);">Not reached</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="lastUpdate" style="padding: var(--space-md) var(--space-lg); background: #F5F5F5; display: flex; align-items: center; gap: var(--space-sm); font-size: 13px; color: var(--color-text-light); border-top: 1px solid #E0E0E0;">
                <i class="fas fa-sync-alt fa-spin"></i>
                <span>Connecting...</span>
            </div>

            <div style="padding: var(--space-lg); border-top: 1px solid #E0E0E0;">
                <button onclick="centerOnTraveler()" class="btn btn-primary btn-block" style="margin-bottom: var(--space-sm);">
                    <i class="fas fa-crosshairs"></i>
                    Center on Trekker
                </button>
                <a href="{{ route('tracking.pin.entry') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-sign-out-alt"></i>
                    Exit Monitoring
                </a>
            </div>

        </div>
    </div>
</div>

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
    margin-left: 10px;
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
    margin-left: 10px;
}

.map-select:hover {
    border-color: var(--color-primary);
}

.map-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.1); 
}

    .status-dot { width: 12px; height: 12px; border-radius: 50%; }

    .status-dot.online {
        background: #2E7D32;
        box-shadow: 0 0 0 3px rgba(46,125,50,0.3);
        animation: pulse-dot 2s infinite;
    }
    .status-dot.recent  { background: #F57C00; box-shadow: 0 0 0 3px rgba(245,124,0,0.3); }
    .status-dot.offline { background: #D32F2F; box-shadow: 0 0 0 3px rgba(211,47,47,0.3); }

    @keyframes pulse-dot {
        0%, 100% { box-shadow: 0 0 0 3px rgba(46,125,50,0.3); }
        50%       { box-shadow: 0 0 0 8px rgba(46,125,50,0);   }
    }

    .checkpoint-item.completed { animation: checkpointReached 0.5s ease; }

    @keyframes checkpointReached {
        0%   { transform: scale(0.95); opacity: 0.8; }
        50%  { transform: scale(1.02); }
        100% { transform: scale(1);    opacity: 1;   }
    }

    @media (max-width: 768px) {
        div[style*="display: flex; height: calc(100vh - 70px)"] { flex-direction: column; }
        div[style*="width: 400px"] { width: 100%; height: 50vh; }
        #map { height: 50vh !important; }
    }
</style>
@endpush

@push('scripts')
@include('components.map-config')
@include('components.routing-helper')

@php
    $jsCheckpoints = $booking->tourPackage->checkpoints->sortBy('order')->map(fn($c) => [
        'id'               => $c->id,
        'name'             => $c->name,
        'latitude'         => (float) $c->latitude,
        'longitude'        => (float) $c->longitude,
        'order'            => $c->order,
        'detection_radius' => (int) ($c->detection_radius ?? 50),
    ])->values()->toArray();

    $jsInitialProgress = $booking->checkpointProgress->map(fn($p) => [
        'checkpoint_id' => $p->checkpoint_id,
        'reached'       => (bool) $p->reached_at,
        'reached_at'    => $p->reached_at ? $p->reached_at->toIso8601String() : null,
    ])->values()->toArray();

    $startLat = (float) $booking->tourPackage->start_lat;
    $startLng = (float) $booking->tourPackage->start_lng;
    $endLat   = (float) $booking->tourPackage->end_lat;
    $endLng   = (float) $booking->tourPackage->end_lng;
@endphp

<script>

const BOOKING_ID       = {{ $booking->id }};
const CHECKPOINTS      = @json($jsCheckpoints);
const INITIAL_PROGRESS = @json($jsInitialProgress);
const START_LAT        = {{ $startLat }};
const START_LNG        = {{ $startLng }};
const END_LAT          = {{ $endLat }};
const END_LNG          = {{ $endLng }};


let map, userMarker, userCircle, updateInterval, routeDrawn = false;


async function initMap() {

const styleId = document.getElementById('style').value;

        if (map) {
            map.remove(); 
            routeDrawn = false; 
        }


    map = createMap('map', {
        center: [START_LAT, START_LNG],
        zoom:   13,
        style:  styleId,
    });

    const waypoints = [
        { lat: START_LAT, lng: START_LNG },
        ...CHECKPOINTS.map(c => ({ lat: c.latitude, lng: c.longitude })),
        { lat: END_LAT, lng: END_LNG },
    ];

    if (!routeDrawn) {
        await drawSmartRoute(waypoints, map);
        routeDrawn = true;
    }

    L.marker([START_LAT, START_LNG], {
        icon: L.divIcon({
            html: '<div style="background:#1B5E20;color:white;padding:6px 10px;border-radius:6px;font-weight:700;font-size:11px;box-shadow:0 2px 6px rgba(0,0,0,0.2);">START</div>',
            className: '', iconSize: [50, 24],
        }),
    }).addTo(map);

    L.marker([END_LAT, END_LNG], {
        icon: L.divIcon({
            html: '<div style="background:#D32F2F;color:white;padding:6px 10px;border-radius:6px;font-weight:700;font-size:11px;box-shadow:0 2px 6px rgba(0,0,0,0.2);">END</div>',
            className: '', iconSize: [45, 24],
        }),
    }).addTo(map);

    CHECKPOINTS.forEach(cp => {
        const prog    = INITIAL_PROGRESS.find(p => p.checkpoint_id === cp.id);
        const reached = prog && prog.reached;

        L.marker([cp.latitude, cp.longitude], {
            icon: L.divIcon({
                html: `<div style="background:${reached ? '#2E7D32' : '#1B5E20'};color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.2);">${cp.order}</div>`,
                className: '', iconSize: [36, 36],
            }),
        }).addTo(map).bindPopup(`<strong>${cp.name}</strong>`);

        L.circle([cp.latitude, cp.longitude], {
            radius:      cp.detection_radius,
            color:       reached ? '#2E7D32' : '#1B5E20',
            fillColor:   reached ? '#2E7D32' : '#1B5E20',
            fillOpacity: 0.08,
            weight:      1,
            dashArray:   '5, 5',
        }).addTo(map);
    });

    fetchLocation();
    updateInterval = setInterval(fetchLocation, 5000);
}


async function fetchLocation() {
    try {
        const res = await fetch(`/api/track/${BOOKING_ID}/location`);

        if (res.status === 404) {
            setLastUpdate('hourglass', '#F5F5F5', 'var(--color-text-light)', 'Waiting for trekker to start GPS...');
            setStatus('offline', 'Waiting...');
            return;
        }

        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (!data.location) {
            setLastUpdate('hourglass', '#F5F5F5', 'var(--color-text-light)', 'Waiting for trekker...');
            return;
        }

        const { latitude: lat, longitude: lng, accuracy, speed, battery_level } = data.location;

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
            if (userCircle) {
                userCircle.setLatLng([lat, lng]);
                userCircle.setRadius(accuracy ?? 0);
            }
        } else {
            userMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    html: '<div style="background:#2196F3;width:18px;height:18px;border-radius:50%;border:3px solid white;box-shadow:0 0 12px rgba(33,150,243,0.6);"></div>',
                    className: '', iconSize: [18, 18],
                }),
            }).addTo(map);

            userCircle = L.circle([lat, lng], {
                radius: accuracy ?? 0, color: '#2196F3',
                fillColor: '#2196F3', fillOpacity: 0.15, weight: 2,
            }).addTo(map);

            map.setView([lat, lng], 15);
        }

        setStatus(data.status_class, data.online_status);

        document.getElementById('coordinates').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        document.getElementById('accuracy').textContent    = accuracy ? Math.round(accuracy) + 'm' : '-';
        document.getElementById('speed').textContent       = speed ? (speed * 3.6).toFixed(1) + ' km/h' : '0 km/h';
        document.getElementById('battery').textContent     = battery_level ?? '-';

        document.getElementById('progressBar').style.width      = data.progress_percentage + '%';
        document.getElementById('progressPercent').textContent  = data.progress_percentage + '%';
        document.getElementById('completedCount').textContent   = data.completed_checkpoints;
        document.getElementById('completedText').textContent    = data.completed_checkpoints;

        if (data.progress && data.progress.length) {
            data.progress.forEach(p => {
                if (!p.reached) return;

                const el = document.getElementById(`checkpoint-${p.checkpoint_id}`);
                if (!el || el.classList.contains('completed')) return;

                el.classList.add('completed');
                el.style.background = '#E8F5E9';

                const badge = el.querySelector('div:first-child');
                if (badge) badge.style.background = '#2E7D32';

                const notReached = el.querySelector('.not-reached-label');
                if (notReached) {
                    const time = p.reached_at ? new Date(p.reached_at) : new Date();
                    notReached.outerHTML = `
                        <div style="font-size:12px;color:#2E7D32;display:flex;align-items:center;gap:4px;">
                            <i class="fas fa-check-circle"></i>
                            <span>${timeAgo(time)}</span>
                        </div>`;
                }

                map.eachLayer(layer => {
                    if (!(layer instanceof L.Marker)) return;
                    const cp = CHECKPOINTS.find(c => c.id === p.checkpoint_id);
                    if (!cp) return;
                    const ll = layer.getLatLng();
                    if (Math.abs(ll.lat - cp.latitude) < 0.0001 && Math.abs(ll.lng - cp.longitude) < 0.0001) {
                        layer.setIcon(L.divIcon({
                            html: `<div style="background:#2E7D32;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.2);">${cp.order}</div>`,
                            className: '', iconSize: [36, 36],
                        }));
                    }
                });
            });
        }

        setLastUpdate('check-circle', '#E8F5E9', '#2E7D32', `Updated ${new Date().toLocaleTimeString()}`);

    } catch (err) {
        console.warn('Fetch failed:', err.message);
        setLastUpdate('exclamation-triangle', '#FFEBEE', '#D32F2F', 'Connection error — retrying...');
        setStatus('offline', 'Connection lost');
    }
}


function setStatus(cssClass, text) {
    document.getElementById('statusDot').className  = `status-dot ${cssClass}`;
    document.getElementById('statusText').textContent = text;
}

function setLastUpdate(icon, bg, color, text) {
    const el = document.getElementById('lastUpdate');
    el.style.background = bg;
    el.style.color      = color;
    el.innerHTML        = `<i class="fas fa-${icon}"></i><span>${text}</span>`;
}

function centerOnTraveler() {
    if (userMarker) {
        map.setView(userMarker.getLatLng(), 16, { animate: true });
    } else {
        alert('Trekker location not available yet. Please wait.');
    }
}

function timeAgo(date) {
    const s = Math.floor((Date.now() - date) / 1000);
    if (s < 60)   return 'just now';
    if (s < 3600) return Math.floor(s / 60) + ' min ago';
    if (s < 86400) return Math.floor(s / 3600) + ' hr ago';
    return Math.floor(s / 86400) + ' day(s) ago';
}


document.addEventListener('DOMContentLoaded', initMap);
window.addEventListener('beforeunload', () => { if (updateInterval) clearInterval(updateInterval); });
</script>
@endpush
@endsection
