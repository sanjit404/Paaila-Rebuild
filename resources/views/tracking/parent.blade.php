@extends('layouts.app')

@section('title', 'Monitoring — ' . $booking->tourPackage->name)

@section('content')

@php
    $jsCheckpoints = $booking->tourPackage->checkpoints->sortBy('order')->map(fn($c) => [
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
        'reached'       => (bool) $p->reached_at,
        'reached_at'    => $p->reached_at ? $p->reached_at->toIso8601String() : null,
    ])->values()->toArray();

    $startLat = (float) $booking->tourPackage->start_lat;
    $startLng = (float) $booking->tourPackage->start_lng;
    $endLat   = (float) $booking->tourPackage->end_lat;
    $endLng   = (float) $booking->tourPackage->end_lng;
    $totalCps = $booking->tourPackage->checkpoints->count();
@endphp

<div class="trk-wrap">

    <div class="trk-map-area">
        <div id="map"></div>

        <div class="trk-topbar">
            <div class="trk-topbar-left">
                <div class="trk-pkg-name">
                    <i class="fas fa-eye" style="color:var(--color-primary); margin-right:4px;"></i>
                    Monitoring {{ $booking->user->name }}
                </div>
                <div id="onlineChip" class="gps-chip searching">
                    <span class="gps-dot"></span>
                    <span id="onlineLabel">Connecting…</span>
                </div>
            </div>

            <div class="trk-topbar-right">
                <button class="trk-icon-btn" onclick="toggleSidebar()" title="Show / hide sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="trk-icon-btn" onclick="cycleMapStyle()" title="Change map style">
                    <i class="fas fa-layer-group"></i>
                </button>
                <button class="trk-icon-btn" onclick="centerOnTrekker()" title="Center on trekker">
                    <i class="fas fa-crosshairs"></i>
                </button>
            </div>
        </div>

        <div class="trk-progress-overlay">
            <div class="trk-prog-track">
                <div class="trk-prog-fill" id="progressFill" style="width:{{ $booking->progress_percentage }}%"></div>
            </div>
            <span class="trk-prog-label" id="progressLabel">
                {{ $booking->completed_checkpoints }}/{{ $totalCps }} checkpoints
            </span>
        </div>

        <div id="cpNotif" class="cp-toast" style="display:none;">
            <div class="cp-toast-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="cp-toast-body">
                <div class="cp-toast-title" id="cpNotifTitle">Checkpoint reached!</div>
                <div class="cp-toast-sub" id="cpNotifSub"></div>
            </div>
        </div>
    </div>

    <div class="trk-sidebar {{ $booking->tourPackage->checkpoints->count() ? '' : '' }}" id="trkSidebar">
        <div class="trk-sidebar-inner">

            <div class="trk-section" style="background:#F0FFF4; border-color:#C8E6C9;">
                <div style="display:flex; align-items:center; gap:12px;">
    
                    <div style="width:44px; height:44px; background:var(--color-primary); cursor:pointer; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; color:white; flex-shrink:0;">
                        {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:15px; color:var(--color-text);">{{ $booking->user->name }}</div>
                        <div style="font-size:12px; color:var(--color-text-light);">
                            {{ $booking->tourPackage->name }}
                        </div>
                        <div style="font-size:11px; color:var(--color-text-light); margin-top:2px;">
                            <i class="fas fa-calendar"></i> {{ $booking->tour_date->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="trk-section">
                <div class="trk-stats-grid">
                    <div class="trk-stat">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="trk-stat-val" id="statCompleted">{{ $booking->completed_checkpoints }}</div>
                        <div class="trk-stat-lbl">Reached</div>
                    </div>
                    <div class="trk-stat">
                        <i class="fas fa-route"></i>
                        <div class="trk-stat-val">{{ $totalCps }}</div>
                        <div class="trk-stat-lbl">Total CPs</div>
                    </div>
                    <div class="trk-stat">
                        <i class="fas fa-percent"></i>
                        <div class="trk-stat-val" id="statPct">{{ $booking->progress_percentage }}</div>
                        <div class="trk-stat-lbl">Done</div>
                    </div>
                    <div class="trk-stat">
                        <i class="fas fa-calendar-day"></i>
                        <div class="trk-stat-val">{{ $booking->tourPackage->duration_days }}</div>
                        <div class="trk-stat-lbl">Days</div>
                    </div>
                </div>
            </div>

            <div class="trk-section">
                <div class="trk-section-title"><i class="fas fa-satellite-dish"></i> Live Location</div>
                <div class="trk-gps-grid">
                    <div><span class="trk-gps-lbl">Accuracy</span><span class="trk-gps-val" id="gpsAccuracy" title="GPS Accuracy">—</span></div>
                    <div><span class="trk-gps-lbl">Speed</span><span class="trk-gps-val" id="gpsSpeed" title="Speed">—</span></div>
                    <div><span class="trk-gps-lbl">Battery</span><span class="trk-gps-val" id="gpsBattery" title="Device Battery">—</span></div>
                    <div><span class="trk-gps-lbl">Last seen</span><span class="trk-gps-val" id="gpsTime" title="Visiblity">—</span></div>
                </div>
            </div>

            <div class="trk-section">
                <div class="trk-section-title"><i class="fas fa-map"></i> Map Style</div>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    <button class="map-style-btn active" onclick="setMapStyle('hybrid', this)">🛰️ Satellite</button>
                    <button class="map-style-btn" onclick="setMapStyle('outdoor', this)">⛷️ Trek</button>
                    <button class="map-style-btn" onclick="setMapStyle('street', this)">🗺️ Street</button>
                </div>
            </div>

            <div class="trk-section">
                <div class="trk-section-title"><i class="fas fa-list-ol"></i> Checkpoint Progress</div>
                <div class="trk-timeline" id="cpTimeline">
                    @foreach($booking->tourPackage->checkpoints->sortBy('order') as $cp)
                        @php
                            $prog    = $booking->checkpointProgress->where('checkpoint_id', $cp->id)->first();
                            $reached = $prog && $prog->reached_at;
                        @endphp
                        <div class="trk-tl-item {{ $reached ? 'reached' : '' }}" id="tl-{{ $cp->id }}">
                            <div class="trk-tl-spine">
                                <div class="trk-tl-dot">{{ $cp->order }}</div>
                                @if(!$loop->last)
                                    <div class="trk-tl-line"></div>
                                @endif
                            </div>
                            <div class="trk-tl-body">
                                <div class="trk-tl-name">{{ $cp->name }}</div>
                                @if($reached)
                                    <div class="trk-tl-status reached-label">
                                        <i class="fas fa-check-circle"></i>
                                        {{ $prog->reached_at->format('g:i A') }}
                                    </div>
                                @else
                                    <div class="trk-tl-status pending-label" id="tl-status-{{ $cp->id }}">
                                        Not reached yet
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="trk-section">
                <a href="{{ route('tracking.pin.entry') }}" class="trk-back-btn">
                    <i class="fas fa-sign-out-alt"></i> Exit Monitoring
                </a>
                <button onclick="centerOnTrekker()" class="trk-center-btn">
                    <i class="fas fa-crosshairs"></i> Center on Trekker
                </button>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
html,body {
	height: 100%;
}

body {
	overflow: hidden;
}

.trk-wrap {
	display: flex;
	height: calc(100vh - 70px);
	position: relative;
	overflow: hidden;
	min-height: 0;
}

.trk-map-area {
	flex: 1;
	position: relative;
	min-height: 0;
}

#map {
	width: 100%;
	height: 100%;
}

.trk-topbar {
	position: absolute;
	top: 14px;
	left: 14px;
	right: 14px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 10px;
	z-index: 800;
	pointer-events: none;
}

.trk-topbar-left,
.trk-topbar-right {
	pointer-events: all;
	display: flex;
	align-items: center;
	gap: 8px;
	flex-wrap: wrap;
}

.trk-pkg-name {
	background: white;
	border-radius: 20px;
	padding: 7px 14px;
	font-size: 13px;
	font-weight: 700;
	color: var(--color-text);
	box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
}

.gps-chip {
	display: flex;
	align-items: center;
	gap: 7px;
	padding: 6px 12px;
	border-radius: 20px;
	font-size: 12px;
	font-weight: 600;
	box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
}

.gps-chip.searching {
	background: #FFF3E0;
	color: #E65100;
}

.gps-chip.active {
	background: #E8F5E9;
	color: #1B5E20;
}

.gps-chip.recent {
	background: #FFF8E1;
	color: #F57F17;
}

.gps-chip.offline {
	background: #FFEBEE;
	color: #C62828;
}

.gps-dot {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: currentColor;
	flex-shrink: 0;
}

.gps-chip.active .gps-dot {
	animation: gpsPulse 1.8s infinite;
}

@keyframes gpsPulse {

	0%,
	100% {
		opacity: 1;
		box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.6)
	}

	50% {
		opacity: 0.7;
		box-shadow: 0 0 0 5px rgba(46, 125, 50, 0)
	}
}

.trk-icon-btn {
	width: 38px;
	height: 38px;
	border-radius: 50%;
	background: white;
	border: none;
	cursor: pointer;
	font-size: 15px;
	color: var(--color-text);
	box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
	display: flex;
	align-items: center;
	justify-content: center;
	transition: background 0.15s, transform 0.1s;
}

.trk-icon-btn:hover {
	background: #F5F5F5;
	transform: scale(1.05);
}

.trk-progress-overlay {
	position: absolute;
	bottom: 20px;
	left: 14px;
	right: 14px;
	display: flex;
	align-items: center;
	gap: 10px;
	z-index: 800;
	pointer-events: none;
}

.trk-prog-track {
	flex: 1;
	height: 6px;
	background: rgba(255, 255, 255, 0.5);
	border-radius: 3px;
	overflow: hidden;
}

.trk-prog-fill {
	height: 100%;
	background: var(--color-primary);
	border-radius: 3px;
	transition: width 0.6s ease;
}

.trk-prog-label {
	background: white;
	border-radius: 12px;
	padding: 4px 10px;
	font-size: 12px;
	font-weight: 700;
	color: var(--color-primary);
	white-space: nowrap;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.cp-toast {
	position: absolute;
	top: 70px;
	left: 50%;
	transform: translateX(-50%);
	background: white;
	border-radius: 14px;
	box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2);
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 14px 18px;
	z-index: 850;
	min-width: 280px;
	max-width: 380px;
	animation: toastSlide 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes toastSlide {
	from {
		opacity: 0;
		transform: translateX(-50%) translateY(-20px) scale(0.92)
	}

	to {
		opacity: 1;
		transform: translateX(-50%) translateY(0) scale(1)
	}
}

.cp-toast-icon {
	width: 40px;
	height: 40px;
	background: var(--color-primary);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 16px;
	flex-shrink: 0;
}

.cp-toast-body {
	flex: 1;
}

.cp-toast-title {
	font-weight: 700;
	font-size: 14px;
	color: var(--color-text);
}

.cp-toast-sub {
	font-size: 12px;
	color: var(--color-text-light);
	margin-top: 2px;
}

.trk-sidebar {
	width: 340px;
	flex-shrink: 0;
	background: white;
	border-left: 1px solid #E0E0E0;
	overflow-y: auto;
	scrollbar-width: thin;
	min-height: 0;
}

.trk-sidebar-inner {
	min-height: 0;
}

.trk-section {
	padding: 16px 20px;
	border-bottom: 1px solid #F0F0F0;
}

.trk-section-title {
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.6px;
	color: var(--color-text-light);
	margin-bottom: 12px;
	display: flex;
	align-items: center;
	gap: 6px;
}

.trk-stats-grid {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 8px;
}

.trk-stat {
	background: #F9F9F9;
	border-radius: 10px;
	padding: 10px 8px;
	text-align: center;
}

.trk-stat i {
	font-size: 14px;
	color: var(--color-primary);
	margin-bottom: 4px;
	display: block;
}

.trk-stat-val {
	font-size: 20px;
	font-weight: 800;
	color: var(--color-text);
	line-height: 1;
}

.trk-stat-lbl {
	font-size: 10px;
	color: var(--color-text-light);
	margin-top: 3px;
	font-weight: 600;
}

.trk-gps-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 8px;
}

.trk-gps-grid>div {
	display: flex;
	flex-direction: column;
	gap: 2px;
}

.trk-gps-lbl {
	font-size: 10px;
	color: var(--color-text-light);
	text-transform: uppercase;
	font-weight: 600;
	letter-spacing: 0.4px;
}

.trk-gps-val {
    cursor:pointer;
	font-size: 13px;
	font-weight: 700;
	color: var(--color-text);
	font-family: monospace;
}

.map-style-btn {
	padding: 6px 12px;
	border-radius: 16px;
	border: 1.5px solid #E0E0E0;
	background: white;
	font-size: 12px;
	font-weight: 600;
	cursor: pointer;
	color: var(--color-text);
	transition: all 0.15s;
}

.map-style-btn.active {
	background: var(--color-primary);
	color: white;
	border-color: var(--color-primary);
}

.trk-timeline {
	display: flex;
	flex-direction: column;
}

.trk-tl-item {
	display: flex;
	gap: 12px;
}

.trk-tl-spine {
	display: flex;
	flex-direction: column;
	align-items: center;
	flex-shrink: 0;
	width: 32px;
}

.trk-tl-dot {
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: #D0D0D0;
	color: white;
	font-size: 12px;
	font-weight: 800;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: background 0.3s;
	z-index: 1;
}

.trk-tl-item.reached .trk-tl-dot {
	background: var(--color-primary);
}

.trk-tl-item.current .trk-tl-dot {
	background: #1976D2;
	box-shadow: 0 0 0 4px rgba(25, 118, 210, 0.2);
}

.trk-tl-line {
	width: 2px;
	flex: 1;
	min-height: 20px;
	background: #E0E0E0;
	margin: 3px 0;
	transition: background 0.3s;
}

.trk-tl-item.reached .trk-tl-line {
	background: var(--color-primary);
}

.trk-tl-body {
	flex: 1;
	padding-bottom: 18px;
	min-width: 0;
}

.trk-tl-name {
	font-size: 13px;
	font-weight: 700;
	color: var(--color-text);
	margin-top: 6px;
	margin-bottom: 2px;
}

.trk-tl-status {
	font-size: 11px;
	display: flex;
	align-items: center;
	gap: 4px;
}

.reached-label {
	color: var(--color-primary);
}

.pending-label {
	color: var(--color-text-light);
}

.trk-back-btn {
	display: flex;
	align-items: center;
	gap: 8px;
	color: var(--color-text-light);
	text-decoration: none;
	font-size: 13px;
	font-weight: 600;
	padding: 10px 0;
	transition: color 0.15s;
}

.trk-back-btn:hover {
	color: var(--color-error);
}

.trk-center-btn {
	margin-top: 10px;
	width: 100%;
	background: var(--color-primary);
	color: white;
	border: none;
	border-radius: 8px;
	padding: 10px;
	font-size: 13px;
	font-weight: 700;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
}

@media (max-width:900px) {
	.trk-wrap {
		flex-direction: column;
		height: 100vh;
		min-height: 100vh;
	}

	.trk-map-area {
		height: 55vh;
		min-height: 300px;
		flex-shrink: 0;
	}

	.trk-sidebar {
		width: 100%;
		border-left: none;
		border-top: 1px solid #E0E0E0;
		max-height: 45vh;
	}

	.trk-sidebar.collapsed {
		display: none;
	}

	.trk-topbar-right {
		gap: 6px;
	}

	.trk-icon-btn {
		width: 36px;
		height: 36px;
	}
}

</style>
@endpush

@push('scripts')
@include('components.map-config')
@include('components.routing-helper')

<script>
const BOOKING_ID     = {{ $booking->id }};
const CPS            = @json($jsCheckpoints);
const INIT_PROGRESS  = @json($jsProgress);
const START_LAT      = {{ $startLat }};
const START_LNG      = {{ $startLng }};
const END_LAT        = {{ $endLat }};
const END_LNG        = {{ $endLng }};

const MAP_STYLES     = ['hybrid', 'outdoor', 'street'];
let mapStyleIdx      = 0;
let map, userMarker, userCircle, updateTimer;
let currentStyle     = 'hybrid';
const reachedIds     = new Set(INIT_PROGRESS.filter(p => p.reached).map(p => p.checkpoint_id));
const cpMarkers      = {};

async function initMap() {
    if (map) { map.remove(); }
    map = createMap('map', { center: [START_LAT, START_LNG], zoom: 12, style: currentStyle });
    await drawRouteAndMarkers();
    setTimeout(() => {
        if (map) map.invalidateSize();
    }, 200);
    startPolling();
}

async function drawRouteAndMarkers() {
    const waypoints = [
        { lat: START_LAT, lng: START_LNG },
        ...CPS.map(c => ({ lat: c.latitude, lng: c.longitude })),
        { lat: END_LAT, lng: END_LNG },
    ];
    await drawSmartRoute(waypoints, map);

    L.marker([START_LAT, START_LNG], { icon: L.divIcon({
        html: `<div style="background:#1B5E20;color:white;padding:5px 10px;border-radius:16px;font-weight:800;font-size:11px;box-shadow:0 2px 8px rgba(0,0,0,0.25);">▶ START</div>`,
        className: '', iconSize: [70, 26],
    })}).addTo(map);

    L.marker([END_LAT, END_LNG], { icon: L.divIcon({
        html: `<div style="background:#C62828;color:white;padding:5px 10px;border-radius:16px;font-weight:800;font-size:11px;box-shadow:0 2px 8px rgba(0,0,0,0.25);">⏹ END</div>`,
        className: '', iconSize: [64, 26],
    })}).addTo(map);

    CPS.forEach(cp => {
        const reached = reachedIds.has(cp.id);
        const marker  = makeMarker(cp, reached);
        cpMarkers[cp.id] = marker;
        marker.addTo(map);

        L.circle([cp.latitude, cp.longitude], {
            radius: cp.detection_radius,
            color: reached ? '#2E7D32' : '#90A4AE',
            fillColor: reached ? '#2E7D32' : '#90A4AE',
            fillOpacity: 0.07, weight: 1, dashArray: '4 4',
        }).addTo(map);
    });
}

function makeMarker(cp, reached) {
    const bg   = reached ? '#2E7D32' : '#607D8B';
    const ring = reached ? 'box-shadow:0 0 0 3px rgba(46,125,50,0.3);' : '';
    return L.marker([cp.latitude, cp.longitude], { icon: L.divIcon({
        html: `<div style="background:${bg};color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.25);${ring}">
            ${cp.order}
        </div>`,
        className: '', iconSize: [36, 36], iconAnchor: [18, 18],
    })}).bindTooltip(`<strong>${cp.name}</strong><br><small>${reached ? '✓ Reached' : 'Not yet'}</small>`, { direction: 'top' });
}

function startPolling() {
    fetchLocation();
    updateTimer = setInterval(fetchLocation, 5000);
}

async function fetchLocation() {
    try {
        const res  = await fetch(`/api/track/${BOOKING_ID}/location`);
        if (res.status === 404) { setOnline('searching', 'Waiting for trekker…'); return; }
        if (!res.ok) throw new Error();
        const data = await res.json();

        if (!data.location) { setOnline('searching', 'No location yet'); return; }

        const { latitude: lat, longitude: lng, accuracy, speed, battery_level } = data.location;

        if (!userMarker) {
            userMarker = L.marker([lat, lng], { icon: L.divIcon({
                html: `<div style="background:#1565C0;width:18px;height:18px;border-radius:50%;border:3px solid white;box-shadow:0 0 0 4px rgba(21,101,192,0.3),0 2px 8px rgba(0,0,0,0.25);"></div>`,
                className: '', iconSize: [18, 18], iconAnchor: [9, 9],
            })}).addTo(map).bindTooltip('{{ $booking->user->name }}', { permanent: true, direction: 'top', offset: [0, -14] });

            userCircle = L.circle([lat, lng], {
                radius: accuracy ?? 20, color: '#1565C0', fillColor: '#1565C0',
                fillOpacity: 0.12, weight: 1.5,
            }).addTo(map);
        } else {
            userMarker.setLatLng([lat, lng]);
            userCircle.setLatLng([lat, lng]).setRadius(accuracy ?? 20);
        }

        const secs = Math.floor((Date.now() - new Date(data.location.updated_at)) / 1000);
        if (secs < 30)       setOnline('active',   'Online now');
        else if (secs < 300)  setOnline('recent',   'Last seen ' + timeAgo(new Date(data.location.updated_at)));
        else                  setOnline('offline',  'Offline — ' + timeAgo(new Date(data.location.updated_at)));

        document.getElementById('gpsAccuracy').textContent = accuracy ? Math.round(accuracy) + 'm' : '—';
        document.getElementById('gpsSpeed').textContent    = speed ? (speed * 3.6).toFixed(1) + ' km/h' : '—';
        document.getElementById('gpsBattery').textContent  = battery_level || '—';
        document.getElementById('gpsTime').textContent     = timeAgo(new Date(data.location.updated_at));

        if (data.progress_percentage !== undefined) {
            document.getElementById('progressFill').style.width  = data.progress_percentage + '%';
            document.getElementById('statPct').textContent       = data.progress_percentage;
            document.getElementById('statCompleted').textContent = data.completed_checkpoints;
            document.getElementById('progressLabel').textContent =
                data.completed_checkpoints + '/{{ $totalCps }} checkpoints';
        }

        if (data.progress) {
            data.progress.forEach(p => {
                if (!p.reached || reachedIds.has(p.checkpoint_id)) return;
                reachedIds.add(p.checkpoint_id);

                const cp = CPS.find(c => c.id === p.checkpoint_id);
                if (cp && cpMarkers[p.checkpoint_id]) {
                    cpMarkers[p.checkpoint_id].remove();
                    cpMarkers[p.checkpoint_id] = makeMarker(cp, true);
                    cpMarkers[p.checkpoint_id].addTo(map);
                }

                const item = document.getElementById('tl-' + p.checkpoint_id);
                if (item) {
                    item.classList.add('reached');
                    const st = document.getElementById('tl-status-' + p.checkpoint_id);
                    if (st) {
                        st.className = 'trk-tl-status reached-label';
                        const t = p.reached_at ? new Date(p.reached_at) : new Date();
                        st.innerHTML = '<i class="fas fa-check-circle"></i> ' + t.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
                    }
                }

                const cpName = CPS.find(c => c.id === p.checkpoint_id)?.name || 'Checkpoint';
                showNotif(cpName, '{{ $booking->user->name }} just reached this checkpoint');
            });
        }

    } catch(e) {
        setOnline('offline', 'Connection lost — retrying');
    }
}

function setOnline(cls, label) {
    const chip = document.getElementById('onlineChip');
    chip.className = 'gps-chip ' + cls;
    document.getElementById('onlineLabel').textContent = label;
}

let notifTimer;
function showNotif(title, sub) {
    document.getElementById('cpNotifTitle').textContent = title;
    document.getElementById('cpNotifSub').textContent   = sub;
    const el = document.getElementById('cpNotif');
    el.style.display = 'flex';
    clearTimeout(notifTimer);
    notifTimer = setTimeout(() => { el.style.display = 'none'; }, 7000);
}

function centerOnTrekker() {
    if (userMarker) map.setView(userMarker.getLatLng(), 16, { animate: true });
    else alert('Trekker location not available yet.');
}

function cycleMapStyle() {
    mapStyleIdx  = (mapStyleIdx + 1) % MAP_STYLES.length;
    currentStyle = MAP_STYLES[mapStyleIdx];
    initMap();
}

function setMapStyle(style, btn) {
    currentStyle = style;
    document.querySelectorAll('.map-style-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    initMap();
}

function toggleSidebar() {
    const sidebar = document.getElementById('trkSidebar');
    sidebar.classList.toggle('collapsed');
    setTimeout(() => {
        if (map) map.invalidateSize();
    }, 250);
}

function timeAgo(date) {
    const s = Math.floor((Date.now() - date) / 1000);
    if (s < 60)    return 'just now';
    if (s < 3600)  return Math.floor(s / 60) + 'm ago';
    if (s < 86400) return Math.floor(s / 3600) + 'h ago';
    return Math.floor(s / 86400) + 'd ago';
}

document.addEventListener('DOMContentLoaded', initMap);
window.addEventListener('resize', () => {
    if (map) setTimeout(() => map.invalidateSize(), 150);
});
window.addEventListener('beforeunload', () => { clearInterval(updateTimer); });
</script>
@endpush
@endsection