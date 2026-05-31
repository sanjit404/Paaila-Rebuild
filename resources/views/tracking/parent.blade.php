@extends('layouts.app')

@section('title', 'Live ' . $booking->tourPackage->name)

@section('content')
<div class="monitoring-wrapper">
    <div class="monitoring-map">
        <div id="map"></div>
    </div>

    <div class="monitoring-sidebar">
        <div class="sidebar-content">
            <div class="sidebar-header">
                <div class="header-label">MONITORING</div>
                <h2 class="header-title">{{ $booking->user->name }}</h2>
                <p class="header-subtitle">{{ $booking->tourPackage->name }}</p>
            </div>

            <div class="sidebar-body">
                <div id="onlineStatus" class="online-status">
                    <span id="statusDot" class="status-dot"></span>
                    <span id="statusText">Connecting...</span>
                </div>

                <div class="progress-section">
                    <div class="progress-stats">
                        <div class="stat-card">
                            <div class="stat-value" id="completedCount">{{ $booking->completed_checkpoints }}</div>
                            <div class="stat-label">REACHED</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="progressPercent">{{ $booking->progress_percentage }}%</div>
                            <div class="stat-label">COMPLETE</div>
                        </div>
                    </div>

                    <div class="progress-bar-container">
                        <div id="progressBar" class="progress-bar"></div>
                    </div>
                    <p id="progressText" class="progress-text">
                        <span id="completedText">{{ $booking->completed_checkpoints }}</span> of
                        <span id="totalCount">{{ $booking->total_checkpoints }}</span> checkpoints
                    </p>
                </div>

                <div class="map-layer-selector">
                    <label for="style" class="map-label">Map Layer</label>
                    <select name="style" id="style" onchange="initMap()" class="map-select">
                        <option value="hybrid">Satellite 🛰️</option>
                        <option value="street">Street 🗺️</option>
                        <option value="outdoor">Trek ⛷️</option>
                    </select>
                </div>

                <div id="locationDetails" class="location-details">
                    <div class="section-header">
                        <i class="fas fa-map-pin"></i>
                        <h4>Current Location</h4>
                    </div>
                    <div class="location-grid">
                        <div class="location-item">
                            <div class="location-label">Coordinates</div>
                            <div id="coordinates" class="location-value">-</div>
                        </div>
                        <div class="location-item">
                            <div class="location-label">Accuracy</div>
                            <div id="accuracy" class="location-value">-</div>
                        </div>
                        <div class="location-item">
                            <div class="location-label">Speed</div>
                            <div id="speed" class="location-value">-</div>
                        </div>
                        <div class="location-item">
                            <div class="location-label">Battery</div>
                            <div id="battery" class="location-value">-</div>
                        </div>
                    </div>
                </div>

                <div class="trek-info-section">
                    <div class="section-title">Trek Information</div>
                    <div class="trek-details">
                        <div class="flex-between">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">{{ $booking->tour_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex-between">
                            <span class="detail-label">Trekkers</span>
                            <span class="detail-value">{{ $booking->participants }}</span>
                        </div>
                        <div class="flex-between">
                            <span class="detail-label">Started</span>
                            <span class="detail-value">{{ $booking->started_at ? $booking->started_at->diffForHumans() : 'Not started' }}</span>
                        </div>
                    </div>
                </div>

                <div class="checkpoints-section">
                    <div class="section-header">
                        <i class="fas fa-list"></i>
                        <h4>Checkpoints</h4>
                    </div>

                    <div id="checkpointsList" class="checkpoints-list">
                        @foreach($booking->tourPackage->checkpoints->sortBy('order') as $checkpoint)
                            @php
                                $progress = $booking->checkpointProgress->where('checkpoint_id', $checkpoint->id)->first();
                                $reached  = $progress && $progress->reached_at;
                            @endphp
                            <div
                                id="checkpoint-{{ $checkpoint->id }}"
                                class="checkpoint-item {{ $reached ? 'completed' : '' }}"
                            >
                                <div class="checkpoint-number">
                                    {{ $checkpoint->order }}
                                </div>
                                <div class="checkpoint-info">
                                    <div class="checkpoint-name">{{ $checkpoint->name }}</div>
                                    @if($reached)
                                        <div class="checkpoint-status reached">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="reached-time">{{ $progress->reached_at->diffForHumans() }}</span>
                                        </div>
                                    @else
                                        <div class="checkpoint-status not-reached">Not reached</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="lastUpdate" class="last-update">
                    <i class="fas fa-sync-alt fa-spin"></i>
                    <span>Connecting...</span>
                </div>

                <div class="sidebar-footer">
                    <button onclick="centerOnTraveler()" class="btn btn-primary btn-block btn-center">
                        <i class="fas fa-crosshairs"></i>
                        Center on Trekker
                    </button>
                    <a href="{{ route('tracking.pin.entry') }}" class="btn btn-secondary btn-block btn-exit">
                        <i class="fas fa-sign-out-alt"></i>
                        Exit Monitoring
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>

    .map-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--color-text-light);
        letter-spacing: 0.5px;
        margin-bottom: var(--space-sm);
        display: block;
    }

    .map-select {
        appearance: none;
        background-color: #F5F5F5;
        border: 2px solid #E0E0E0;
        border-radius: var(--radius-md);
        padding: var(--space-sm) var(--space-md);
        font-size: 14px;
        font-weight: 600;
        color: var(--color-text);
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
    }

    .map-select:hover {
        border-color: var(--color-primary);
    }

    .map-select:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.1);
    }

    .map-layer-selector {
        padding: var(--space-lg);
        border-bottom: 1px solid #E0E0E0;
        background: #FAFAFA;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-dot.online {
        background: #2E7D32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.3);
        animation: pulse-dot 2s infinite;
    }

    .status-dot.recent {
        background: #F57C00;
        box-shadow: 0 0 0 3px rgba(245, 124, 0, 0.3);
    }

    .status-dot.offline {
        background: #D32F2F;
        box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.3);
    }

    @keyframes pulse-dot {
        0%, 100% { box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.3); }
        50%      { box-shadow: 0 0 0 8px rgba(46, 125, 50, 0); }
    }

    .checkpoint-item {
        padding: var(--space-md);
        background: #F5F5F5;
        border-radius: var(--radius-md);
        display: flex;
        gap: var(--space-md);
        align-items: center;
        transition: all 0.3s ease;
    }

    .checkpoint-item.completed {
        background: #E8F5E9;
        animation: checkpointReached 0.5s ease;
    }

    .checkpoint-number {
        width: 32px;
        height: 32px;
        background: #B0BEC5;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .checkpoint-item.completed .checkpoint-number {
        background: #2E7D32;
    }

    .checkpoint-info {
        flex: 1;
        min-width: 0;
    }

    .checkpoint-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 2px;
        color: var(--color-text);
    }

    .checkpoint-status {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .checkpoint-status.reached {
        color: #2E7D32;
    }

    .checkpoint-status.not-reached {
        color: var(--color-text-light);
    }

    @keyframes checkpointReached {
        0%   { transform: scale(0.95); opacity: 0.8; }
        50%  { transform: scale(1.02); }
        100% { transform: scale(1); opacity: 1; }
    }

    .monitoring-wrapper {
        display: flex;
        height: calc(100vh - 70px);
        position: relative;
    }

    .monitoring-map {
        flex: 1;
        position: relative;
        width: 100%;
        height: 100%;
    }

    .monitoring-map #map {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    .monitoring-sidebar {
        width: 400px;
        background: white;
        box-shadow: -2px 0 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        flex-shrink: 0;
        z-index: 10;
    }

    .sidebar-content {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .sidebar-header {
        padding: var(--space-lg);
        background: var(--color-primary);
        color: white;
    }

    .header-label {
        font-size: 12px;
        opacity: 0.9;
        margin-bottom: var(--space-xs);
    }

    .header-title {
        font-size: 18px;
        font-weight: 700;
        color: white;
        margin-bottom: var(--space-sm);
    }

    .header-subtitle {
        font-size: 13px;
        opacity: 0.9;
        margin: 0;
    }

    .sidebar-body {
        flex: 1;
        overflow-y: auto;
    }

    .online-status {
        padding: var(--space-md) var(--space-lg);
        display: flex;
        align-items: center;
        gap: var(--space-md);
        border-bottom: 1px solid #E0E0E0;
        font-weight: 500;
        font-size: 14px;
    }

    .progress-section {
        padding: var(--space-lg);
        border-bottom: 1px solid #E0E0E0;
    }

    .progress-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-md);
        margin-bottom: var(--space-lg);
    }

    .stat-card {
        text-align: center;
        padding: var(--space-md);
        background: #F5F5F5;
        border-radius: var(--radius-md);
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-primary);
    }

    .stat-label {
        font-size: 12px;
        color: var(--color-text-light);
        margin-top: var(--space-xs);
    }

    .progress-bar-container {
        background: #E0E0E0;
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: var(--space-sm);
    }

    .progress-bar {
        height: 100%;
        background: var(--color-primary);
        transition: width 0.5s ease;
    }

    .progress-text {
        text-align: center;
        font-size: 13px;
        color: var(--color-text-light);
        margin: 0;
    }

    .location-details {
        padding: var(--space-lg);
        border-bottom: 1px solid #E0E0E0;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        margin-bottom: var(--space-md);
    }

    .section-header i {
        color: var(--color-primary);
    }

    .section-header h4 {
        font-size: 14px;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        color: var(--color-text-light);
    }

    .location-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-md);
        font-size: 13px;
    }

    .location-item {
        display: flex;
        flex-direction: column;
    }

    .location-label {
        color: var(--color-text-light);
        margin-bottom: 4px;
    }

    .location-value {
        font-weight: 600;
        color: var(--color-text);
        font-family: monospace;
    }

    .trek-info-section {
        padding: var(--space-lg);
        background: #F5F5F5;
        border-bottom: 1px solid #E0E0E0;
    }

    .section-title {
        font-size: 12px;
        color: var(--color-text-light);
        margin-bottom: var(--space-md);
        text-transform: uppercase;
        font-weight: 600;
    }

    .trek-details {
        display: flex;
        flex-direction: column;
        gap: var(--space-sm);
        font-size: 13px;
    }

    .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-label {
        color: var(--color-text-light);
    }

    .detail-value {
        font-weight: 600;
        color: var(--color-text);
    }

    .checkpoints-section {
        padding: var(--space-lg);
        border-bottom: 1px solid #E0E0E0;
    }

    .checkpoints-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-sm);
    }

    .last-update {
        padding: var(--space-md) var(--space-lg);
        background: #F5F5F5;
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        font-size: 13px;
        color: var(--color-text-light);
        border-top: 1px solid #E0E0E0;
    }

    .sidebar-footer {
        padding: var(--space-lg);
        border-top: 1px solid #E0E0E0;
    }

    .btn-block {
        width: 100%;
    }

    .btn-center {
        margin-bottom: var(--space-sm);
    }

    @media (max-width: 900px) {
        .monitoring-wrapper {
            flex-direction: column;
            height: auto;
            min-height: 100vh;
        }

        .monitoring-map {
            height: 50vh;
            min-height: 350px;
            width: 100%;
        }

        .monitoring-map #map {
            width: 100%;
            height: 100%;
        }

        .monitoring-sidebar {
            width: 100%;
            height: auto;
            max-height: 70vh;
        }
    }

    @media (max-width: 640px) {
        .monitoring-wrapper {
            height: auto;
            min-height: 100vh;
        }

        .monitoring-map {
            height: 50vh;
            min-height: 300px;
            width: 100%;
        }

        .monitoring-map #map {
            width: 100%;
            height: 100%;
        }

        .monitoring-sidebar {
            max-height: none;
            width: 100%;
        }

        .header-title {
            font-size: 16px;
        }

        .stat-value {
            font-size: 24px;
        }

        .location-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .progress-stats {
            gap: var(--space-sm);
        }

        .stat-card {
            padding: var(--space-sm);
        }

        .stat-value {
            font-size: 22px;
        }

        .checkpoint-item {
            padding: var(--space-sm);
        }

        .checkpoint-number {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }

        .checkpoint-name {
            font-size: 13px;
        }
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
let currentLat = null;
let currentLng = null;

async function initMap() {
    const styleId = document.getElementById('style').value;

    if (map) {
        const mapCenter = map.getCenter();
        currentLat = currentLat || mapCenter.lat;
        currentLng = currentLng || mapCenter.lng;
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

    if (currentLat && currentLng) {
        restoreUserMarker(currentLat, currentLng);
    }

    fetchLocation();
    updateInterval = setInterval(fetchLocation, 5000);
}

function restoreUserMarker(lat, lng) {
    console.log('Restoring user marker at:', lat, lng);
    
    userMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: '<div style="background:#012efa;width:20px;height:20px;border-radius:50%;border:3px solid white;box-shadow:0 0 15px rgba(76,175,80,0.8);"></div>',
            className: 'user-marker-icon',
            iconSize: [26, 26],
            iconAnchor: [13, 13],
        }),
    }).addTo(map);

    userCircle = L.circle([lat, lng], {
        radius: 20, 
        color: '#544caf',
        fillColor: '#4CAF50', 
        fillOpacity: 0.2, 
        weight: 2,
    }).addTo(map);

    map.setView([lat, lng], 15);
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

        currentLat = lat;
        currentLng = lng;

        console.log('Received location:', lat, lng);

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
            if (userCircle) {
                userCircle.setLatLng([lat, lng]);
                userCircle.setRadius(accuracy ?? 20);
            }
        } else {
            console.log('Creating user marker at:', lat, lng);
            restoreUserMarker(lat, lng);
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
window.addEventListener('load', () => { 
    if (map) map.invalidateSize(); 
});
window.addEventListener('beforeunload', () => { if (updateInterval) clearInterval(updateInterval); });
</script>
@endpush
@endsection