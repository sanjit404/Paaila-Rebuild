@extends('layouts.app')

@section('title', 'Live Tracking — ' . $booking->tourPackage->name)

@section('content')

@php
    $jsCheckpoints = $booking->tourPackage->checkpoints->map(fn($c) => [
        'id'               => $c->id,
        'name'             => $c->name,
        'description'      => $c->description,
        'image'            => $c->image,
        'latitude'         => (float) $c->latitude,
        'longitude'        => (float) $c->longitude,
        'order'            => $c->order,
        'detection_radius' => (int) ($c->detection_radius ?? 50),
        'facts'            => $c->facts->map(fn($f) => [
            'id'         => $f->id,
            'title'      => $f->title,
            'content'    => $f->content,
            'type'       => $f->type ?? 'info',
            'icon_class' => $f->icon_class ?? 'fas fa-info-circle',
        ])->values()->toArray(),
    ])->values()->toArray();

    $jsProgress = $booking->checkpointProgress->map(fn($p) => [
        'checkpoint_id' => $p->checkpoint_id,
        'reached_at'    => $p->reached_at ? $p->reached_at->toIso8601String() : null,
    ])->values()->toArray();

    $startLat = (float) $booking->tourPackage->start_lat;
    $startLng = (float) $booking->tourPackage->start_lng;
    $endLat   = (float) $booking->tourPackage->end_lat;
    $endLng   = (float) $booking->tourPackage->end_lng;
    $totalCps = $booking->tourPackage->checkpoints->count();
    $completedCps = $booking->completed_checkpoints;
@endphp

<div class="trk-wrap">
    <div class="trk-map-area">
        <div id="map"></div>

        <div class="trk-topbar">
            <div class="trk-topbar-left">
                <div class="trk-pkg-name">{{ Str::limit($booking->tourPackage->name, 28) }}</div>
                <div id="gpsChip" class="gps-chip searching" title="GPS Accuracy">
                    <span class="gps-dot"></span>
                    <span id="gpsLabel" >Acquiring GPS <i class="fa fa-solid fa-spinner fa-spin-pulse"></i></span>
                </div>
            </div>
            <div class="trk-topbar-right">
                <button class="trk-icon-btn" onclick="cycleMapStyle()" title="Change map style">
                    <i class="fas fa-layer-group"></i>
                </button>
                <button class="trk-icon-btn" onclick="centerOnUser()" title="Center on me">
                    <i class="fas fa-crosshairs"></i>
                </button>
                <button class="trk-icon-btn" id="sidebarToggle" onclick="toggleSidebar()" title="Toggle panel">
                    <i class="fas fa-bars" id="sidebarIcon"></i>
                </button>
            </div>
        </div>

        <div class="trk-progress-overlay">
            <div class="trk-prog-track">
                <div class="trk-prog-fill" id="progressFill" style="width:{{ $booking->progress_percentage }}%"></div>
            </div>
            <span class="trk-prog-label" id="progressLabel">
                {{ $completedCps }}/{{ $totalCps }} checkpoints
            </span>
        </div>

        <div id="cpToast" class="cp-toast" style="display:none;">
            <div class="cp-toast-icon"><i class="fas fa-flag-checkered"></i></div>
            <div class="cp-toast-body">
                <div class="cp-toast-title" id="cpToastTitle">Checkpoint reached!</div>
                <div class="cp-toast-sub" id="cpToastSub"></div>
            </div>
            <button class="cp-toast-btn" id="cpToastBtn" onclick="openFactsPanel()">View Facts</button>
        </div>
    </div>

    <div class="trk-sidebar" id="trkSidebar">
        <div class="trk-section">
            <div class="trk-section-title"><i class="fas fa-route"></i> Trek Info</div>
            <div class="trk-stats-grid">
                <div class="trk-stat">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="trk-stat-val" id="statCompleted">{{ $completedCps }}</div>
                    <div class="trk-stat-lbl">Reached</div>
                </div>
                <div class="trk-stat">
                    <i class="fas fa-route"></i>
                    <div class="trk-stat-val" id="statTotal">{{ $totalCps }}</div>
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
            <div class="trk-section-title"><i class="fas fa-satellite-dish"></i> GPS Info</div>
            <div class="trk-gps-grid">
                <div><span class="trk-gps-lbl">Accuracy</span><span class="trk-gps-val" id="gpsAccuracy">—</span></div>
                <div><span class="trk-gps-lbl">Speed</span><span class="trk-gps-val" id="gpsSpeed">—</span></div>
                <div><span class="trk-gps-lbl">Altitude</span><span class="trk-gps-val" id="gpsAlt">—</span></div>
                <div><span class="trk-gps-lbl">Updated</span><span class="trk-gps-val" id="gpsTime">—</span></div>
            </div>
        </div>

        <div class="trk-section trk-pin-section">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--color-success);">
                    <i class="fa-solid fa-lock "></i> Paaila Tracking PIN
                </span>
                <div style="display:flex; gap:6px;">
                    <button class="trk-pin-btn" onclick="copyPin()" title="Copy"><i class="fas fa-copy"></i></button>
                    <button class="trk-pin-btn" onclick="sharePin()" title="Share"><i class="fas fa-share"></i></button>
                </div>
            </div>
            <div class="trk-pin-display">
				<span id="pin" data-code="{{ $booking->trackingPin->pin }}">
					XXX XXX
				</span>
					<small><i id="eyeIcon" class="fa fa-eye" style="cursor:pointer;" title="Toggle PIN"></i></small>
			</div>
            <div style="font-size:11px; color:var(--color-text-light); margin-top:4px; text-align:center;">
                Share only with trusted ones to track you live. <br> <strong style="color:red;"> <i class="fa fa-solid fa-warning"></i> Don't make it public !</strong>
            </div>
        </div>

        <div class="trk-section">
            <div class="trk-section-title"><i class="fas fa-compass"></i> Next Stop</div>
            <div id="nextCpCard" class="trk-next-card">
                <div class="trk-next-num" id="nextCpNum">—</div>
                <div>
                    <div class="trk-next-name" id="nextCpName">Continue along the route</div>
                    <div class="trk-next-dist" id="nextCpDist"></div>
                </div>
            </div>
        </div>

        <div class="trk-section">
            <div class="trk-section-title"><i class="fas fa-list-ol"></i> Checkpoint Timeline</div>
            <div class="trk-timeline" id="cpTimeline">
                @foreach($booking->tourPackage->checkpoints->sortBy('order') as $cp)
                    @php
                        $prog = $booking->checkpointProgress->where('checkpoint_id', $cp->id)->first();
                        $reached = $prog && $prog->reached_at;
                    @endphp
                    <div class="trk-tl-item {{ $reached ? 'reached' : '' }}" id="tl-{{ $cp->id }}" data-cp-id="{{ $cp->id }}">
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
                                <div class="trk-tl-status pending-label">Not reached yet</div>
                            @endif
                            @if($cp->facts->count() > 0 && $reached)
                                <button class="trk-facts-btn" onclick="openFactsPanelFor({{ $cp->id }})">
                                    <i class="fas fa-book-open"></i> {{ $cp->facts->count() }} facts
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        

        <div class="trk-section" style="padding-top:0;">
            <a href="{{ route('bookings.show', $booking) }}" class="trk-back-btn">
                <i class="fas fa-arrow-left"></i> Back to Booking
            </a>
        </div>
    </div>
</div>

<div id="factsOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999;" onclick="closeFactsPanel()"></div>
<div id="factsPanel" class="facts-panel">
    <div class="facts-panel-header">
        <div>
            <div class="facts-panel-label">CHECKPOINT FACTS</div>
            <div class="facts-panel-title" id="factsCpName"></div>
        </div>
        <button class="facts-close-btn" onclick="closeFactsPanel()"><i class="fas fa-times"></i></button>
    </div>
    <div class="facts-panel-body" id="factsBody"></div>
</div>

<div class="trk-debug-panel" id="debugPanel">
    <div class="trk-debug-title"><i class="fas fa-flask"></i> Dev Jump</div>
    <button onclick="jumpTo({{ $startLat }}, {{ $startLng }})" class="trk-debug-btn">▶ Start</button>
    @foreach($booking->tourPackage->checkpoints->sortBy('order') as $cp)
        <button onclick="jumpTo({{ $cp->latitude }}, {{ $cp->longitude }})" class="trk-debug-btn">CP{{ $cp->order }}</button>
    @endforeach
    <button onclick="jumpTo({{ $endLat }}, {{ $endLng }})" class="trk-debug-btn">⏹ End</button>
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
    margin-left:32px;
	background: white;
	border-radius: 20px;
	padding: 7px 14px;
	font-size: 13px;
	font-weight: 700;
	color: var(--color-text);
	box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
}

.gps-chip {
    cursor:help;
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

.gps-chip.error {
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

.trk-icon-btn:active {
	transform: scale(0.96);
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
	backdrop-filter: blur(4px);
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

.cp-toast-btn {
	background: var(--color-primary);
	color: white;
	border: none;
	border-radius: 8px;
	padding: 7px 12px;
	font-size: 12px;
	font-weight: 700;
	cursor: pointer;
	white-space: nowrap;
	flex-shrink: 0;
}

.trk-sidebar {
	width: 340px;
	flex-shrink: 0;
	background: white;
	border-left: 1px solid #E0E0E0;
	overflow-y: auto;
	transition: width 0.25s ease, opacity 0.25s ease;
	scrollbar-width: thin;
}

.trk-sidebar.collapsed {
	width: 0;
	opacity: 0;
	overflow: hidden;
	pointer-events: none;
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

.trk-pin-section {
	background: #F0FFF4;
	border-color: #C8E6C9;
}

.trk-pin-display {
	font-size: 32px;
	font-weight: 800;
	letter-spacing: 8px;
	color: var(--color-primary);
	text-align: center;
	padding: 8px 0 4px;
}

.trk-pin-btn {
	width: 30px;
	height: 30px;
	border-radius: 50%;
	border: 1.5px solid #C8E6C9;
	background: white;
	color: var(--color-primary);
	font-size: 12px;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
}

.trk-next-card {
	display: flex;
	align-items: center;
	gap: 12px;
	background: #F9F9F9;
	border-radius: 10px;
	padding: 12px;
}

.trk-next-num {
	width: 36px;
	height: 36px;
	border-radius: 50%;
	background: var(--color-primary);
	color: white;
	font-size: 14px;
	font-weight: 800;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
}

.trk-next-name {
	font-weight: 700;
	font-size: 14px;
	color: var(--color-text);
}

.trk-next-dist {
	font-size: 12px;
	color: var(--color-text-light);
	margin-top: 2px;
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
	flex-shrink: 0;
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

.trk-facts-btn {
	margin-top: 6px;
	background: #E8F5E9;
	color: var(--color-primary);
	border: none;
	border-radius: 6px;
	padding: 4px 10px;
	font-size: 11px;
	font-weight: 700;
	cursor: pointer;
	display: inline-flex;
	align-items: center;
	gap: 4px;
	transition: background 0.15s;
}

.trk-facts-btn:hover {
	background: #C8E6C9;
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
	font-size: 13px;
	font-weight: 700;
	color: var(--color-text);
	font-family: monospace;
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
	color: var(--color-primary);
}

.facts-panel {
	position: fixed;
	bottom: -100%;
	left: 0;
	right: 0;
	max-height: 75vh;
	background: white;
	border-radius: 20px 20px 0 0;
	box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.2);
	z-index: 10000;
	transition: bottom 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
	display: flex;
	flex-direction: column;
}

.facts-panel.open {
	bottom: 0;
}

.facts-panel-header {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	padding: 20px 24px 14px;
	border-bottom: 1px solid #F0F0F0;
	flex-shrink: 0;
}

.facts-panel-label {
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.8px;
	color: var(--color-primary);
	margin-bottom: 4px;
}

.facts-panel-title {
	font-size: 18px;
	font-weight: 800;
	color: var(--color-text);
}

.facts-close-btn {
	width: 32px;
	height: 32px;
	border-radius: 50%;
	border: none;
	background: #F0F0F0;
	color: var(--color-text);
	font-size: 14px;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
}

.facts-panel-body {
	flex: 1;
	overflow-y: auto;
	padding: 16px 24px 32px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.fact-card {
	display: flex;
	gap: 14px;
	padding: 14px;
	border-radius: 12px;
	background: #F9F9F9;
	border: 1px solid #EEEEEE;
}

.fact-card-icon {
	width: 40px;
	height: 40px;
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 16px;
	flex-shrink: 0;
}

.fact-card.type-historical .fact-card-icon,
.fact-card.type-history .fact-card-icon {
	background: #E3F2FD;
	color: #1565C0;
}

.fact-card.type-cultural .fact-card-icon,
.fact-card.type-culture .fact-card-icon {
	background: #FCE4EC;
	color: #AD1457;
}

.fact-card.type-natural .fact-card-icon,
.fact-card.type-nature .fact-card-icon {
	background: #E8F5E9;
	color: #2E7D32;
}

.fact-card.type-safety .fact-card-icon {
	background: #FFF3E0;
	color: #E65100;
}

.fact-card.type-tip .fact-card-icon {
	background: #F3E5F5;
	color: #6A1B9A;
}

.fact-card.type-info .fact-card-icon {
	background: #E8F5E9;
	color: #388E3C;
}

.fact-card-title {
	font-size: 14px;
	font-weight: 700;
	color: var(--color-text);
	margin-bottom: 5px;
}

.fact-card-text {
	font-size: 13px;
	color: var(--color-text-light);
	line-height: 1.6;
	margin: 0;
}

.trk-debug-panel {
	position: fixed;
	bottom: 20px;
	left: 20px;
	background: rgba(20, 40, 20, 0.92);
	color: white;
	padding: 10px 12px;
	border-radius: 10px;
	z-index: 2000;
	display: flex;
	flex-wrap: wrap;
	gap: 4px;
	max-width: 220px;
}

.trk-debug-title {
	width: 100%;
	font-size: 11px;
	font-weight: 700;
	margin-bottom: 4px;
	opacity: 0.7;
}

.trk-debug-btn {
	background: rgba(255, 255, 255, 0.12);
	color: white;
	border: none;
	border-radius: 5px;
	padding: 4px 7px;
	font-size: 10px;
	cursor: pointer;
}

.trk-debug-btn:hover {
	background: rgba(255, 255, 255, 0.22);
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
		width: 100%;
		height: 0;
		opacity: 0;
		overflow: hidden;
		pointer-events: none;
	}

	.trk-debug-panel {
		display: none;
	}
}

</style>
@endpush

@push('scripts')
@include('components.map-config')
@include('components.routing-helper')
<script>
const BOOKING_ID  = {{ $booking->id }};
const CSRF_TOKEN  = '{{ csrf_token() }}';
const CPS         = @json($jsCheckpoints);
const PROGRESS    = @json($jsProgress);
const START_LAT   = {{ $startLat }};
const START_LNG   = {{ $startLng }};
const END_LAT     = {{ $endLat }};
const END_LNG     = {{ $endLng }};

const START_RADIUS            = 50;   // arrival detection
const ROUTE_REFRESH_DISTANCE  = 30;   // nav route redraw threshold

const MAP_STYLES  = ['hybrid', 'outdoor', 'street'];
let mapStyleIdx   = 0;
let map, userMarker, userCircle;
let navigationRoute = null;  // polyline: user → start
let trekRoute       = null;  // polyline: start → cps → end
let hasReachedStart = {{ $booking->start_reached_at ? 'true' : 'false' }};
let lastRouteUpdatePos = null; // {lat, lng} of last nav route draw
let watchId         = null;
let currentLat      = null;
let currentLng      = null;
let sidebarOpen     = true;
let toastTimer      = null;
let activeFacts     = null;
let mapInitialized  = false;

const reachedIds  = new Set(PROGRESS.filter(p => p.reached_at).map(p => p.checkpoint_id));
const cpMarkers   = {};

// ── ROUTING HELPER (returns layer reference) ──────────
// Wraps existing drawSmartRoute so we can remove old layers.
// drawSmartRoute from routing-helper.blade.php adds the polyline
// directly to map and returns {success, provider, coordinates, distance, duration}.
// We capture the last added layer by watching map layers before/after.

async function drawRouteReturningLayer(waypoints, options = {}) {

    const result = await drawSmartRoute(waypoints, map);

    if (!result || !result.line) {
        return result;
    }

    if (options.color) {
        result.line.setStyle({
            color: options.color,
            weight: options.weight ?? 4,
            opacity: options.opacity ?? 0.85,
            dashArray: options.dashArray ?? null
        });
    }

    return result;
}

function removeLayer(layer) {
    if (layer && map.hasLayer(layer)) map.removeLayer(layer);
}


// for route even after page reloads.
async function initMap() {
    if (map) {
        removeLayer(navigationRoute);
        removeLayer(trekRoute);
        navigationRoute = null;
        trekRoute       = null;
        map.remove();
    }

    map = createMap('map', {
        center: [START_LAT, START_LNG],
        zoom:   12,
        style:  MAP_STYLES[mapStyleIdx],
    });

    placeStaticMarkers();

    // If start reached, direct static route draw from st to cp to end
    if (hasReachedStart) {
        await drawTrekRoute();
    }

    // If we are switching map styles while GPS is already running
    if (currentLat !== null) {
        placeUserMarker(currentLat, currentLng, 20);
        
        // Only draw nav route if we haven't reached the start yet
        if (!hasReachedStart) {
            await drawNavRoute(currentLat, currentLng, false);
        }
    }
    mapInitialized = true;
}


// Markers on the maps ko lagi
function placeStaticMarkers() {
    L.marker([START_LAT, START_LNG], { icon: L.divIcon({
        html: `<div style="background:#1B5E20;color:white;padding:5px 10px;border-radius:16px;font-weight:800;font-size:11px;box-shadow:0 2px 8px rgba(0,0,0,0.25);white-space:nowrap;">▶ START</div>`,
        className: '', iconSize: [70, 26],
    })}).addTo(map).bindTooltip('Trek starts here', { direction: 'top' });

    L.marker([END_LAT, END_LNG], { icon: L.divIcon({
        html: `<div style="background:#C62828;color:white;padding:5px 10px;border-radius:16px;font-weight:800;font-size:11px;box-shadow:0 2px 8px rgba(0,0,0,0.25);white-space:nowrap;">⏹ END</div>`,
        className: '', iconSize: [64, 26],
    })}).addTo(map);

    CPS.forEach(cp => {
        const reached = reachedIds.has(cp.id);
        const marker  = makeCheckpointMarker(cp, reached);
        cpMarkers[cp.id] = marker;
        marker.addTo(map);

        L.circle([cp.latitude, cp.longitude], {
            radius:      cp.detection_radius,
            color:       reached ? '#2E7D32' : '#90A4AE',
            fillColor:   reached ? '#2E7D32' : '#90A4AE',
            fillOpacity: 0.07,
            weight:      1,
            dashArray:   '4 4',
        }).addTo(map);
    });
}

function makeCheckpointMarker(cp, reached) {
    const hasFacts = cp.facts && cp.facts.length > 0;
    const bg   = reached ? '#2E7D32' : '#607D8B';
    const ring = reached ? 'box-shadow:0 0 0 3px rgba(46,125,50,0.3);' : '';

    return L.marker([cp.latitude, cp.longitude], { icon: L.divIcon({
        html: `<div onclick="cpMarkerClick(${cp.id})" style="
            background:${bg}; color:white;
            width:36px; height:36px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-weight:800; font-size:13px;
            border:3px solid white;
            box-shadow:0 3px 10px rgba(0,0,0,0.25); ${ring}
            cursor:pointer; position:relative;">
            ${cp.order}
            ${reached && hasFacts ? '<div style="position:absolute;top:-3px;right:-3px;width:10px;height:10px;background:#FFC107;border-radius:50%;border:1.5px solid white;"></div>' : ''}
        </div>`,
        className: '', iconSize: [36, 36], iconAnchor: [18, 18],
    })}).bindTooltip(
        `<strong>${cp.name}</strong><br><small>${reached ? '✓ Reached' : 'Not yet reached'}</small>`,
        { permanent: false, direction: 'top' }
    );
}

window.cpMarkerClick = function(cpId) {
    const cp = CPS.find(c => c.id === cpId);
    if (!cp) return;
    if (reachedIds.has(cpId) && cp.facts && cp.facts.length) {
        openFactsPanelFor(cpId);
    } else if (!reachedIds.has(cpId)) {
        showToast(cp.name, 'Reach this checkpoint to unlock facts', false);
    }
};

function placeUserMarker(lat, lng, accuracy) {
    if (!userMarker) {
        userMarker = L.marker([lat, lng], { icon: L.divIcon({
            html: `<div style="
                background:#1565C0; width:18px; height:18px; border-radius:50%;
                border:3px solid white;
                box-shadow:0 0 0 4px rgba(21,101,192,0.3), 0 2px 8px rgba(0,0,0,0.25);">
            </div>`,
            className: '', iconSize: [18, 18], iconAnchor: [9, 9],
        })}).addTo(map);

        userCircle = L.circle([lat, lng], {
            radius: accuracy ?? 20, color: '#1565C0',
            fillColor: '#1565C0', fillOpacity: 0.12, weight: 1.5,
        }).addTo(map);
    } else {
        userMarker.setLatLng([lat, lng]);
        userCircle.setLatLng([lat, lng]).setRadius(accuracy ?? 20);
    }
}

//Route drawwwwww
async function drawNavRoute(lat, lng, doFitBounds = true) {
    // Remove previous nav route
    removeLayer(navigationRoute);
    navigationRoute = null;

    const result = await drawRouteReturningLayer(
        [{ lat, lng }, { lat: START_LAT, lng: START_LNG }],
        { color: '#1565C0', weight: 4, opacity: 0.8, dashArray: '8 6' }
    );

    navigationRoute        = result.line || null;
    lastRouteUpdatePos     = { lat, lng };

    if (doFitBounds && navigationRoute) {
        map.fitBounds(navigationRoute.getBounds(), { padding: [60, 60] });
    }

    
}


// Normal routesss
async function drawTrekRoute() {
    removeLayer(trekRoute);
    trekRoute = null;

    const waypoints = [
        { lat: START_LAT, lng: START_LNG },
        ...CPS.map(c => ({ lat: c.latitude, lng: c.longitude })),
        { lat: END_LAT, lng: END_LNG },
    ];

    const result = await drawRouteReturningLayer(waypoints, {
    });

    trekRoute = result.line || null;

    if (result.distance) {
        const el = document.getElementById('nextCpDist');
        if (el) el.textContent = parseFloat(result.distance).toFixed(1) + ' km total route';
    }
}


// JS haversine to check distance between user current location and start point(Trek ko)
function haversine(lat1, lng1, lat2, lng2) {
    const R  = 6_371_000;
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lng2 - lng1) * Math.PI / 180;
    const a  = Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

function startGPS() {
    if (!navigator.geolocation) {
        setGPS('error', 'GPS not supported');
        return;
    }
    setGPS('searching', 'Acquiring GPS…');

    watchId = navigator.geolocation.watchPosition(onPosition, onGPSError, {
        enableHighAccuracy: true, timeout: 12000, maximumAge: 0,
    });
}


// Position and pre-start routing handling ko lagi
async function onPosition(pos) {
    const { latitude: lat, longitude: lng, accuracy, speed, altitude } = pos.coords;
    currentLat = lat;
    currentLng = lng;

    setGPS('active', `±${Math.round(accuracy ?? 0)}m`);
    document.getElementById('gpsAccuracy').textContent = accuracy? `±${accuracy}m` : '0 km/h';
    document.getElementById('gpsSpeed').textContent = speed ? (speed * 3.6).toFixed(1) + ' km/h' : '0 km/h';
    document.getElementById('gpsAlt').textContent   = altitude ? Math.round(altitude) + 'm' : '—';
    document.getElementById('gpsTime').textContent  = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    placeUserMarker(lat, lng, accuracy);

    // Location API call garera fetching GPS infos.
    const data = await sendLocation({ lat, lng, accuracy, speed, altitude });

    if (!hasReachedStart) {
        if (data?.start_reached_at) {
            hasReachedStart = true;
            removeLayer(navigationRoute);
            navigationRoute = null;

            showToast('Trek started!', 'You have reached the starting point. Good luck!', false);
            await drawTrekRoute(); // Draw trek route ONCE when start is reached
            return;
        }

        const shouldRedraw = !lastRouteUpdatePos || haversine(lat, lng, lastRouteUpdatePos.lat, lastRouteUpdatePos.lng) >= ROUTE_REFRESH_DISTANCE;
        if (shouldRedraw && mapInitialized) {
            await drawNavRoute(lat, lng, !lastRouteUpdatePos);
        }
    }
}


// For sending location to db through API and showing pops for related CPs
async function sendLocation({ lat, lng, accuracy, speed, altitude }) {
    try {
        const res = await fetch(`/api/tracking/${BOOKING_ID}/location`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ latitude: lat, longitude: lng, accuracy, speed, altitude }),
        });
        
        if (!res.ok) return null;
        const data = await res.json();
        

        if (data.progress !== undefined) {
            document.getElementById('progressFill').style.width = data.progress + '%';
            document.getElementById('statPct').textContent      = data.progress;
        }
        
        if (data.completed_checkpoints !== undefined) {
            document.getElementById('statCompleted').textContent = data.completed_checkpoints;
            document.getElementById('progressLabel').textContent = data.completed_checkpoints + '/' + data.total_checkpoints + ' checkpoints';
        }

        if (data.next_checkpoint) {
            document.getElementById('nextCpNum').textContent  = data.next_checkpoint.order;
            document.getElementById('nextCpName').textContent = data.next_checkpoint.name;
            document.getElementById('nextCpDist').textContent = data.distance_to_next ? formatDist(data.distance_to_next) + ' away' : '';
            markCurrentInTimeline(data.next_checkpoint.id);
        } else if (data.completed_checkpoints === data.total_checkpoints && data.total_checkpoints > 0) {
            document.getElementById('nextCpNum').textContent  = '✓';
            document.getElementById('nextCpName').textContent = 'All checkpoints reached!';
            document.getElementById('nextCpDist').textContent = '';
        }

        if (data.checkpoint_reached && data.checkpoint && !reachedIds.has(data.checkpoint.id)) {
            const apiCp = data.checkpoint;
            const cp = CPS.find(c => c.id === apiCp.id) || apiCp; 
            
            reachedIds.add(cp.id);

            if (cpMarkers[cp.id]) {
                cpMarkers[cp.id].remove();
                cpMarkers[cp.id] = makeCheckpointMarker(cp, true);
                cpMarkers[cp.id].addTo(map);
            }

            const tlItem = document.getElementById('tl-' + cp.id);
            if (tlItem) {
                tlItem.classList.add('reached');
                tlItem.classList.remove('current');
                const statusEl = tlItem.querySelector('.trk-tl-status');
                if (statusEl) {
                    statusEl.className = 'trk-tl-status reached-label';
                    statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Just now';
                }
                if (cp.facts && cp.facts.length) {
                    const body = tlItem.querySelector('.trk-tl-body');
                    if (body && !body.querySelector('.trk-facts-btn')) {
                        const btn = document.createElement('button');
                        btn.className = 'trk-facts-btn';
                        btn.innerHTML = '<i class="fas fa-book-open"></i> ' + cp.facts.length + ' facts';
                        btn.onclick   = () => openFactsPanelFor(cp.id);
                        body.appendChild(btn);
                    }
                }
            }
            
            activeFacts = { cpId: cp.id, name: cp.name, facts: cp.facts };
            const hasFacts = cp.facts && cp.facts.length > 0;
            
            showToast(
                cp.name, 
                hasFacts ? cp.facts.length + ' facts unlocked — tap to read' : 'Checkpoint reached!',
                hasFacts
            );
        }
        
        return data;

    } catch(e) { 
        console.error("GPS Tracking Error:", e);
        return null; 
    }
}

function onGPSError(err) {
    const msgs = { 1: 'GPS permission denied', 2: 'GPS unavailable', 3: 'GPS timeout — retrying' };
    setGPS('error', msgs[err.code] || 'GPS error');
}

function setGPS(state, label) {
    const chip = document.getElementById('gpsChip');
    chip.className = 'gps-chip ' + state;
    document.getElementById('gpsLabel').textContent = label;
}

function showToast(title, sub, hasFacts) {
    document.getElementById('cpToastTitle').textContent = title;
    document.getElementById('cpToastSub').textContent   = sub;
    document.getElementById('cpToastBtn').style.display = hasFacts ? '' : 'none';
    const el = document.getElementById('cpToast');
    el.style.display = 'flex';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { el.style.display = 'none'; }, 8000);
}

function openFactsPanel() {
    if (!activeFacts) return;
    openFactsPanelFor(activeFacts.cpId);
}

function openFactsPanelFor(cpId) {
    const cp = CPS.find(c => c.id === cpId);
    if (!cp) return;
    activeFacts = { cpId: cp.id, name: cp.name, facts: cp.facts };

    document.getElementById('factsCpName').textContent = cp.name;

    const body = document.getElementById('factsBody');
    if (!cp.facts || !cp.facts.length) {
        body.innerHTML = '<p style="color:var(--color-text-light);text-align:center;padding:24px 0;">No facts for this checkpoint.</p>';
    } else {
        body.innerHTML = cp.facts.map(f => `
            <div class="fact-card type-${f.type}">
                <div class="fact-card-icon"><i class="${f.icon_class}"></i></div>
                <div>
                    <div class="fact-card-title">${f.title}</div>
                    <p class="fact-card-text">${f.content}</p>
                </div>
            </div>
        `).join('');
    }

    document.getElementById('factsOverlay').style.display = 'block';
    document.getElementById('factsPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeFactsPanel() {
    document.getElementById('factsPanel').classList.remove('open');
    document.getElementById('factsOverlay').style.display = 'none';
    document.body.style.overflow = '';
}

function markCurrentInTimeline(nextCpId) {
    document.querySelectorAll('.trk-tl-item').forEach(el => el.classList.remove('current'));
    const el = document.getElementById('tl-' + nextCpId);
    if (el && !el.classList.contains('reached')) {
        el.classList.add('current');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function cycleMapStyle() {
    mapStyleIdx = (mapStyleIdx + 1) % MAP_STYLES.length;
    initMap();
}

function centerOnUser() {
    if (userMarker) map.setView(userMarker.getLatLng(), 16, { animate: true });
}

function toggleSidebar() {
    sidebarOpen = !sidebarOpen;
    document.getElementById('trkSidebar').classList.toggle('collapsed', !sidebarOpen);
}

function jumpTo(lat, lng) {
    if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
    onPosition({ coords: { latitude: lat, longitude: lng, accuracy: 5, speed: 0.5, altitude: 1400 }});
}

function formatDist(m) {
    return m < 1000 ? Math.round(m) + 'm' : (m / 1000).toFixed(1) + 'km';
}

function copyPin() {
    const pin = '{{ $booking->trackingPin->pin }}';
    navigator.clipboard?.writeText(pin).then(() => showToast('PIN copied!', '', false));
}

function sharePin() {
    const pin = '{{ $booking->trackingPin->pin }}';
    if (navigator.share) {
        navigator.share({ title: 'Track my trek live with PAAILA', text: 'Use this PIN: ' + pin, url: '{{ route("tracking.pin.entry") }}' });
    } else copyPin();
}

document.getElementById("eyeIcon").addEventListener('click', e=>{
const pin = document.getElementById("pin");
    const icon = document.getElementById("eyeIcon");

    if (pin.textContent.trim() === "XXX XXX") {
        pin.textContent = pin.dataset.code;
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        pin.textContent = "XXX XXX";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeFactsPanel(); });

document.addEventListener('DOMContentLoaded', async () => {
    await initMap();
    startGPS();
});

window.addEventListener('beforeunload', () => {
    if (watchId) navigator.geolocation.clearWatch(watchId);
});
</script>
@endpush
@endsection