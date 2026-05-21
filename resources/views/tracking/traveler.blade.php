@extends('layouts.app')

@section('title', 'Live Tracking - ' . $booking->tourPackage->name)

@section('content')
<div style="display: flex; height: calc(100vh - 70px); position: relative;">
    <div id="map" style="flex: 1; position: relative;"></div>

    <div style="width: 380px; background: white; box-shadow: -2px 0 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: var(--space-lg); background: var(--color-primary); color: white;">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: var(--space-xs);">LIVE TRACKING</div>
            <h2 style="font-size: 18px; font-weight: 700; color: white; margin: 0;">{{ $booking->tourPackage->name }}</h2>
        </div>

        <div style="flex: 1; overflow-y: auto;">

            <div id="gpsStatus" class="gps-status searching" style="padding: var(--space-md) var(--space-lg); display: flex; align-items: center; gap: var(--space-md); border-bottom: 1px solid #E0E0E0; font-weight: 500; font-size: 14px;">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Searching for GPS...</span>
            </div>

            <div style="padding: var(--space-lg); border-bottom: 1px solid #E0E0E0;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="text-align: center; padding: var(--space-md); background: #F5F5F5; border-radius: var(--radius-md);">
                        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);" id="completedCount">
                            {{ $booking->completed_checkpoints }}
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-light); margin-top: var(--space-xs);">CHECKPOINTS</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: #F5F5F5; border-radius: var(--radius-md);">
                        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);" id="progressPercent">
                            {{ $booking->progress_percentage }}%
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-light); margin-top: var(--space-xs);">COMPLETE</div>
                    </div>
                </div>

                <div style="background: #E0E0E0; height: 8px; border-radius: 4px; overflow: hidden;">
                    <div id="progressBar" style="height: 100%; background: var(--color-primary); width: {{ $booking->progress_percentage }}%; transition: width 0.5s ease;"></div>
                </div>
                <p id="progressText" style="text-align: center; font-size: 13px; color: var(--color-text-light); margin: var(--space-sm) 0 0 0;">
                    {{ $booking->completed_checkpoints }} of {{ $booking->total_checkpoints }} reached
                </p>
            </div>

            <div style="padding: var(--space-lg); background: #E8F5E9; border-bottom: 1px solid #C8E6C9;">
                <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                    <i class="fas fa-shield-alt" style="color: var(--color-success); font-size: 20px;"></i>
                    <div>
                        <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: 2px;">TRACKING PIN</div>
                        <div style="font-size: 24px; font-weight: 700; color: var(--color-success); letter-spacing: 3px;">
                            {{ $booking->trackingPin->pin }}
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: var(--space-sm);">
                    <button onclick="copyPin()" class="btn btn-secondary btn-sm copy-pin-btn" style="flex: 1; font-size: 13px;">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                    <button onclick="sharePin()" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 13px;">
                        <i class="fas fa-share-alt"></i> Share
                    </button>
                </div>
            </div>

            <div style="padding: var(--space-md) var(--space-lg); background: #E8F5E9; border-bottom: 1px solid #C8E6C9; display: flex; align-items: center; gap: var(--space-md);">
                <span style="width: 10px; height: 10px; background: var(--color-success); border-radius: 50%; animation: pulse 2s infinite;"></span>
                <span style="font-size: 13px; font-weight: 600; color: var(--color-success);">You are visible to trackers</span>
            </div>

            <div id="nextCheckpoint" style="padding: var(--space-lg); border-bottom: 1px solid #E0E0E0;">
                <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-md);">
                    <i class="fas fa-compass" style="color: var(--color-primary);"></i>
                    <h4 style="font-size: 14px; font-weight: 700; margin: 0; text-transform: uppercase; color: var(--color-text-light);">Next Stop</h4>
                </div>
                <p style="color: var(--color-text-light); font-size: 14px; margin: 0;">Continue along the route...</p>
            </div>

            <div style="padding: var(--space-lg); background: #FFF3E0; border-bottom: 1px solid #FFE0B2;">
                <div style="display: flex; gap: var(--space-md);">
                    <i class="fas fa-info-circle" style="color: var(--color-warning); font-size: 20px; flex-shrink: 0;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 14px; margin-bottom: var(--space-xs); color: var(--color-text);">Safety Reminder</div>
                        <p style="font-size: 13px; color: var(--color-text-light); margin: 0; line-height: 1.5;">
                            GPS updates every 5 seconds. Family can track you with your PIN. Stay on the marked route.
                        </p>
                    </div>
                </div>
            </div>

            <div style="padding: var(--space-lg);">
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i>
                    Back to Booking
                </a>
            </div>
        </div>
    </div>
</div>


<div id="checkpointModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; padding: var(--space-lg);">
    <div style="background: white; border-radius: var(--radius-lg); max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; animation: modalAppear 0.3s ease;">
        <!-- Modal Header -->
        <div style="padding: var(--space-xl); background: var(--color-primary); color: white; border-radius: var(--radius-lg) var(--radius-lg) 0 0; position: relative;">
            <button onclick="closeCheckpointModal()" style="position: absolute; top: var(--space-md); right: var(--space-md); background: rgba(255,255,255,0.2); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; transition: background 0.2s;">&times;</button>
            <div style="text-align: center;">
                <i class="fas fa-flag-checkered" style="font-size: 48px; margin-bottom: var(--space-md); opacity: 0.9;"></i>
                <h2 style="font-size: 24px; font-weight: 700; color: white; margin: 0;">Checkpoint Reached!</h2>
            </div>
        </div>

        <!-- Modal Body -->
        <div style="padding: var(--space-xl);">
            <h3 id="checkpointName" style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);"></h3>
            <p id="checkpointDescription" style="color: var(--color-text-light); margin-bottom: var(--space-xl);"></p>

            <div id="checkpointFacts"></div>

            <div style="margin-top: var(--space-xl);">
                <button onclick="closeCheckpointModal()" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-hiking"></i>
                    Continue Trek
                </button>
            </div>
        </div>
    </div>
</div>

@if(true)
<div style="position: fixed; bottom: 20px; left: 20px; background: var(--color-primary-dark); color: white; padding: var(--space-lg); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 2000; max-width: 280px; max-height: 80vh; overflow-y: auto;">
    <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-md);">
        <i class="fas fa-flask"></i>
        <div style="font-weight: 700; font-size: 14px;">TEST CONTROLS</div>
    </div>
    <p style="color:white; font-size: 12px; opacity: 0.9; margin-bottom: var(--space-md);">Debug mode active</p>

    <button onclick="resetGPS()" class="btn btn-block" style="background: white; color: var(--color-primary-light); margin-bottom: var(--space-md); font-size: 13px;">
        <i class="fas fa-redo"></i> Reset GPS
    </button>

    <div style="font-size: 12px; font-weight: 600; margin-bottom: var(--space-sm); opacity: 0.9;">Jump to Checkpoint:</div>
<button onclick="jumpToCheckpoint(START_LAT, START_LNG)" class="btn btn-block" style="background: white; color: var(--color-text); margin-bottom: var(--space-xs); font-size: 12px; padding: 8px 12px;">Jump to START</button>
    @foreach($booking->tourPackage->checkpoints as $checkpoint)
        <button
            onclick="jumpToCheckpoint({{ $checkpoint->latitude }}, {{ $checkpoint->longitude }})"
            class="btn btn-block"
            style="background: white; color: var(--color-text); margin-bottom: var(--space-xs); font-size: 12px; padding: 8px 12px;">
            {{ $checkpoint->order }}. {{ $checkpoint->name }}
        </button>
    @endforeach
    <button onclick="jumpToCheckpoint(END_LAT, END_LNG)" class="btn btn-block"
            style="background: white; color: var(--color-text); margin-bottom: var(--space-xs); font-size: 12px; padding: 8px 12px;">Jump to END</button>
</div>
@endif

@push('styles')
<style>
    .gps-status.searching { background: #FFF3E0; color: var(--color-warning); }
    .gps-status.active    { background: #E8F5E9; color: var(--color-success); }
    .gps-status.error     { background: #FFEBEE; color: var(--color-error);   }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(46,125,50,0.7); }
        50%       { box-shadow: 0 0 0 8px rgba(46,125,50,0); }
    }

    @keyframes modalAppear {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1);    }
    }

    .fact-item {
        padding: var(--space-md);
        background: #F5F5F5;
        border-radius: var(--radius-md);
        margin-bottom: var(--space-md);
        display: flex;
        gap: var(--space-md);
    }
    .fact-item:last-child { margin-bottom: 0; }

    .fact-icon {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }

    .fact-item.type-historical .fact-icon,
    .fact-item.type-history    .fact-icon { background: #E3F2FD; color: #1976D2; }
    .fact-item.type-cultural   .fact-icon,
    .fact-item.type-culture    .fact-icon { background: #FCE4EC; color: #C2185B; }
    .fact-item.type-natural    .fact-icon,
    .fact-item.type-nature     .fact-icon { background: #E8F5E9; color: #2E7D32; }
    .fact-item.type-safety     .fact-icon { background: #FFF3E0; color: #F57C00; }
    .fact-item.type-tip        .fact-icon { background: #F3E5F5; color: #7B1FA2; }
    .fact-item.type-info       .fact-icon { background: #E8F5E9; color: #388E3C; }

    .fact-content h5 { font-size: 14px; font-weight: 600; margin-bottom: var(--space-xs); color: var(--color-text);       }
    .fact-content p  { font-size: 13px; color: var(--color-text-light); margin: 0; line-height: 1.6; }

    /* Mobile */
    @media (max-width: 768px) {
        div[style*="display: flex; height: calc(100vh - 70px)"] { flex-direction: column; }
        div[style*="width: 380px"] { width: 100%; height: 50vh; }
        #map { height: 50vh !important; }
    }
</style>
@endpush

@push('scripts')
@include('components.map-config')
@include('components.routing-helper')

@php
    // Pre-compute all PHP data here so @json() stays on ONE line inside <script>
    $jsCheckpoints = $booking->tourPackage->checkpoints->map(fn($c) => [
        'id'               => $c->id,
        'name'             => $c->name,
        'description'      => $c->description,
        'latitude'         => (float) $c->latitude,
        'longitude'        => (float) $c->longitude,
        'order'            => $c->order,
        'detection_radius' => (int) ($c->detection_radius ?? 50),
    ])->values()->toArray();

    $jsProgress = $booking->checkpointProgress->map(fn($p) => [
        'checkpoint_id' => $p->checkpoint_id,
        'reached_at'    => $p->reached_at ? $p->reached_at->toIso8601String() : null,
    ])->values()->toArray();

    $startLat = (float) $booking->tourPackage->start_lat;
    $startLng = (float) $booking->tourPackage->start_lng;
    $endLat   = (float) $booking->tourPackage->end_lat;
    $endLng   = (float) $booking->tourPackage->end_lng;
@endphp

<script>

const BOOKING_ID   = {{ $booking->id }};
const CSRF_TOKEN   = '{{ csrf_token() }}';
const CHECKPOINTS  = @json($jsCheckpoints);
const PROGRESS     = @json($jsProgress);
const START_LAT    = {{ $startLat }};
const START_LNG    = {{ $startLng }};
const END_LAT      = {{ $endLat }};
const END_LNG      = {{ $endLng }};
let userToStartLayer = null; 
let startReached = false;

let map, userMarker, userCircle, watchId = null, routeDrawn = false;
// Track which checkpoints have already triggered a modal this session
// (prevents re-firing if server and client get out of sync momentarily)
const shownCheckpoints = new Set(
    PROGRESS.filter(p => p.reached_at).map(p => p.checkpoint_id)
);


async function initMap() {
    map = createMap('map', {
        center: [START_LAT, START_LNG],
        zoom:   13,
        style:  'streets',
    });

    // Build waypoints: start → checkpoints → end
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
        const reached = shownCheckpoints.has(cp.id);

        L.marker([cp.latitude, cp.longitude], {
            icon: L.divIcon({
                html: `<div style="background:${reached ? '#2E7D32' : '#1B5E20'};color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.2);">${cp.order}</div>`,
                className: '', iconSize: [36, 36],
            }),
        }).addTo(map).bindPopup(`<strong>${cp.name}</strong><br><small>${cp.description ?? ''}</small>`);

        L.circle([cp.latitude, cp.longitude], {
            radius:      cp.detection_radius,
            color:       reached ? '#2E7D32' : '#1B5E20',
            fillColor:   reached ? '#2E7D32' : '#1B5E20',
            fillOpacity: 0.08,
            weight:      1,
            dashArray:   '5, 5',
        }).addTo(map);
    });

    startTracking();
}


function startTracking() {
    if (!('geolocation' in navigator)) {
        setGPSStatus('error', 'GPS not supported on this device.');
        return;
    }

    setGPSStatus('searching', '<i class="fas fa-spinner fa-spin"></i><span>Acquiring GPS signal...</span>');

    watchId = navigator.geolocation.watchPosition(
        onPositionUpdate,
        onPositionError,
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}


async function onPositionUpdate(position) {
    const { latitude: lat, longitude: lng, accuracy, speed, altitude, heading } = position.coords;

    setGPSStatus('active', `<i class="fas fa-satellite-dish"></i><span>GPS Active • ±${Math.round(accuracy ?? 0)}m</span>`);

    // Update / create user marker
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
            fillColor: '#2196F3', fillOpacity: 0.1, weight: 2,
        }).addTo(map);

        map.setView([lat, lng], 15);
    }

    const userLatLng = L.latLng(lat, lng);
    const startLatLng = L.latLng(START_LAT, START_LNG);
    if (userLatLng.distanceTo(startLatLng) < 50) {
        startReached = true;
    }

    await updateUserToStartRoute(lat, lng);

    sendLocationToServer({ lat, lng, accuracy, speed, altitude, heading });
}



async function updateUserToStartRoute(userLat, userLng) {
    if (startReached) {
        if (userToStartLayer) {
            map.removeLayer(userToStartLayer);
            userToStartLayer = null;
        }
        return;
    }

    try {
        const waypoints = [
            { lat: userLat, lng: userLng },
            { lat: START_LAT, lng: START_LNG }
        ];

       
        const newRouteLayer = await drawSmartRoute(waypoints, map, {
            color: '#2196F3', 
            weight: 5,
            opacity: 0.7,
        });

        if (userToStartLayer && userToStartLayer !== newRouteLayer) {
            map.removeLayer(userToStartLayer);
        }

        userToStartLayer = newRouteLayer;
    } catch (e) {
        console.warn("Could not calculate actual route to start:", e);
    }
}

async function sendLocationToServer({ lat, lng, accuracy, speed, altitude, heading }) {
    try {
        const res = await fetch(`/api/tracking/${BOOKING_ID}/location`, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  CSRF_TOKEN,
            },
            body: JSON.stringify({
                latitude:  lat,
                longitude: lng,
                accuracy:  accuracy ?? null,
                speed:     speed    ?? null,
                altitude:  altitude ?? null,
                heading:   heading  ?? null,
            }),
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (data.progress !== undefined) {
            document.getElementById('progressBar').style.width    = data.progress + '%';
            document.getElementById('progressPercent').textContent = data.progress + '%';
        }
        if (data.completed_checkpoints !== undefined) {
            document.getElementById('completedCount').textContent = data.completed_checkpoints;
            document.getElementById('progressText').textContent   =
                `${data.completed_checkpoints} of ${data.total_checkpoints} reached`;
        }

        if (data.next_checkpoint) {
            const dist = data.distance_to_next ? `${data.distance_to_next}m away` : '';
            document.getElementById('nextCheckpoint').innerHTML = `
                <div style="display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-md);">
                    <i class="fas fa-compass" style="color:var(--color-primary);"></i>
                    <h4 style="font-size:14px;font-weight:700;margin:0;text-transform:uppercase;color:var(--color-text-light);">Next Stop</h4>
                </div>
                <p style="font-weight:600;font-size:16px;margin-bottom:var(--space-sm);color:var(--color-text);">${data.next_checkpoint.name}</p>
                <p style="color:var(--color-text-light);font-size:13px;margin:0;">${dist}</p>
            `;
        } else if (data.completed_checkpoints === data.total_checkpoints && data.total_checkpoints > 0) {
            document.getElementById('nextCheckpoint').innerHTML = `
                <div style="text-align:center;padding:var(--space-lg);">
                    <i class="fas fa-flag-checkered" style="font-size:32px;color:var(--color-success);margin-bottom:var(--space-sm);"></i>
                    <p style="font-weight:700;color:var(--color-success);margin:0;">All checkpoints complete!</p>
                </div>
            `;
        }

        if (data.checkpoint_reached && data.checkpoint && !shownCheckpoints.has(data.checkpoint.id)) {
            shownCheckpoints.add(data.checkpoint.id);
            showCheckpointModal(data.checkpoint);
            updateCheckpointMarker(data.checkpoint);
        }

    } catch (err) {
        console.warn('Location send failed:', err.message);
    }
}


function showCheckpointModal(checkpoint) {
    document.getElementById('checkpointName').textContent        = checkpoint.name;
    document.getElementById('checkpointDescription').textContent = checkpoint.short_description ?? checkpoint.description ?? '';

    const container = document.getElementById('checkpointFacts');
    container.innerHTML = '';

    if (checkpoint.facts && checkpoint.facts.length > 0) {
        checkpoint.facts.forEach(fact => {
            const el = document.createElement('div');
            el.className = `fact-item type-${fact.type ?? 'info'}`;
            el.innerHTML = `
                <div class="fact-icon"><i class="${fact.icon_class ?? 'fas fa-info-circle'}"></i></div>
                <div class="fact-content">
                    <h5>${fact.title}</h5>
                    <p>${fact.content}</p>
                </div>
            `;
            container.appendChild(el);
        });

        markFactsViewed(checkpoint.id);
    } else {
        container.innerHTML = '<p style="text-align:center;color:var(--color-text-light);font-size:14px;">No additional information for this checkpoint.</p>';
    }

    document.getElementById('checkpointModal').style.display = 'flex';
}

function closeCheckpointModal() {
    document.getElementById('checkpointModal').style.display = 'none';
}


function updateCheckpointMarker(checkpoint) {
    map.eachLayer(layer => {
        if (!(layer instanceof L.Marker)) return;
        const ll = layer.getLatLng();
        if (
            Math.abs(ll.lat - checkpoint.latitude)  < 0.0001 &&
            Math.abs(ll.lng - checkpoint.longitude) < 0.0001
        ) {
            layer.setIcon(L.divIcon({
                html: `<div style="background:#2E7D32;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.2);">${checkpoint.order}</div>`,
                className: '', iconSize: [36, 36],
            }));
        }
    });
}


async function markFactsViewed(checkpointId) {
    try {
        await fetch(`/api/tracking/${BOOKING_ID}/facts-viewed/${checkpointId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        });
    } catch (e) { /* non-critical */ }
}


function onPositionError(error) {
    const messages = {
        [error.PERMISSION_DENIED]:    'GPS permission denied — please enable in browser settings.',
        [error.POSITION_UNAVAILABLE]: 'GPS unavailable — move to open area.',
        [error.TIMEOUT]:              'GPS timeout — retrying...',
    };
    const msg = messages[error.code] ?? 'GPS error.';
    setGPSStatus('error', `<i class="fas fa-exclamation-triangle"></i><span>${msg}</span>`);

    if (error.code === error.TIMEOUT) {
        setTimeout(() => {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            startTracking();
        }, 3000);
    }
}

function setGPSStatus(state, html) {
    const el = document.getElementById('gpsStatus');
    el.className = `gps-status ${state}`;
    el.innerHTML = html;
}


function copyPin() {
    const pin = '{{ $booking->trackingPin->pin }}';
    if (navigator.clipboard) {
        navigator.clipboard.writeText(pin).then(() => {
            const btn = document.querySelector('.copy-pin-btn');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.style.background = 'var(--color-success)';
            btn.style.color      = 'white';
            setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; btn.style.color = ''; }, 2000);
        }).catch(() => alert('PIN: ' + pin));
    } else {
        alert('Your Tracking PIN: ' + pin);
    }
}

function sharePin() {
    const pin = '{{ $booking->trackingPin->pin }}';
    const url = '{{ route("tracking.pin.entry") }}';
    if (navigator.share) {
        navigator.share({ title: 'Track My Trek', text: `Track my trek live! PIN: ${pin}`, url })
            .catch(() => copyPin());
    } else {
        copyPin();
    }
}


@if(config('app.debug'))
function jumpToCheckpoint(lat, lng) {
    if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
    onPositionUpdate({
        coords: { latitude: parseFloat(lat), longitude: parseFloat(lng), accuracy: 5, speed: 0, altitude: null, heading: null },
    });
}

function resetGPS() {
    if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
    if (userMarker) { map.removeLayer(userMarker); userMarker = null; }
    if (userCircle) { map.removeLayer(userCircle); userCircle = null; }
    if (userToStartLayer) { map.removeLayer(userToStartLayer); userToStartLayer = null; }
    startTracking();
}
@endif


document.addEventListener('DOMContentLoaded', initMap);
window.addEventListener('beforeunload', () => { if (watchId) navigator.geolocation.clearWatch(watchId); });
</script>
@endpush
@endsection
