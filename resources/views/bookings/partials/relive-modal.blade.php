@php
   
    $completedBookings = $bookings
        ->where('status', 'completed')
        ->map(function ($booking) {
            $booking->load([
                'tourPackage.checkpoints',
                'checkpointProgress.checkpoint',
                'trackingPin',
            ]);

            $checkpointEvents = $booking->checkpointProgress
                ->filter(fn($p) => $p->reached_at && $p->checkpoint)
                ->sortBy('reached_at')
                ->values()
                ->map(fn($p) => [
                    'name'       => $p->checkpoint->name,
                    'order'      => $p->checkpoint->order,
                    'reached_at' => $p->reached_at->toIso8601String(),
                    'reached_human' => $p->reached_at->format('g:i A'),
                ]);

            $duration = null;
            if ($booking->started_at && $booking->completed_at) {
                $mins = $booking->started_at->diffInMinutes($booking->completed_at);
                $duration = $mins >= 60
                    ? floor($mins / 60) . 'h ' . ($mins % 60) . 'm'
                    : $mins . ' min';
            }

            return [
                'id'                => $booking->id,
                'booking_number'    => $booking->booking_number,
                'package_name'      => $booking->tourPackage->name,
                'trek_type'         => $booking->tourPackage->trek_type ?? 'trek',
                'difficulty'        => $booking->tourPackage->difficulty_level,
                'duration_days'     => $booking->tourPackage->duration_days,
                'region'            => $booking->tourPackage->region ?? null,
                'participants'      => $booking->participants,
                'total_amount'      => $booking->total_amount,
                'tour_date'         => $booking->tour_date->format('F j, Y'),
                'created_at'        => $booking->created_at->toIso8601String(),
                'created_human'     => $booking->created_at->format('M j, Y · g:i A'),
                'confirmed_at'      => $booking->confirmed_at?->toIso8601String(),
                'confirmed_human'   => $booking->confirmed_at?->format('M j, Y · g:i A'),
                'started_at'        => $booking->started_at?->toIso8601String(),
                'started_human'     => $booking->started_at?->format('M j, Y · g:i A'),
                'completed_at'      => $booking->completed_at?->toIso8601String(),
                'completed_human'   => $booking->completed_at?->format('M j, Y · g:i A'),
                'trek_duration'     => $duration,
                'checkpoints'       => $checkpointEvents,
                'total_checkpoints' => $booking->tourPackage->checkpoints->count(),
                'pin'               => $booking->trackingPin?->pin,
                'my_rating'         => \App\Models\TrekRating::getRating(auth()->id(), $booking->tour_package_id)?->rating,
            ];
        })
        ->keyBy('id');
@endphp

<script>
    window.__RELIVE_DATA__ = @json($completedBookings);
</script>

<div id="reliveOverlay" class="relive-overlay" onclick="closeRelive(event)">
    <div class="relive-modal" id="reliveModal">

        <button class="relive-close" onclick="closeReliveModal()">
            <i class="fas fa-times"></i>
        </button>

        <div class="relive-header" id="reliveHeader">
            <div class="relive-header-bg" id="reliveHeaderBg"></div>
            <div class="relive-header-content">
                <div class="relive-label">
                    <i class="fas fa-film"></i>
                    Reliving Your Trip
                </div>
                <h2 class="relive-title" id="reliveTitle">—</h2>
                <div class="relive-meta" id="reliveMeta"></div>
            </div>
        </div>

        <div class="relive-stats" id="reliveStats"></div>

        <div class="relive-body">
            <div class="relive-timeline" id="reliveTimeline"></div>
        </div>

        <div class="relive-footer" id="reliveFooter"></div>

    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap');

    .relive-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 20, 10, 0.75);
        backdrop-filter: blur(6px);
        z-index: 99000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .relive-overlay.open {
        display: flex;
        animation: overlayIn 0.25s ease;
    }

    @keyframes overlayIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .relive-modal {
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        background: #FAFAF8;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow:
            0 32px 80px rgba(0,0,0,0.35),
            0 8px 24px rgba(0,0,0,0.15);
        animation: modalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.88) translateY(24px); }
        to   { opacity: 1; transform: scale(1)    translateY(0); }
    }

    .relive-close {
        position: absolute;
        top: 16px; right: 16px;
        width: 36px; height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
        z-index: 10;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
        backdrop-filter: blur(4px);
    }

    .relive-close:hover { background: rgba(255,255,255,0.35); }

    .relive-header {
        position: relative;
        padding: 36px 32px 28px;
        background: var(--color-primary);
        flex-shrink: 0;
        overflow: hidden;
    }

    .relive-header-bg {
        position: absolute;
        inset: 0;
        opacity: 0.3;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 300' preserveAspectRatio='none'%3E%3Cpath d='M0 300 L0 200 L100 130 L200 170 L300 90 L400 150 L500 60 L600 120 L700 40 L800 100 L800 300Z' fill='rgba(255,255,255,0.12)'/%3E%3Cpath d='M0 300 L0 250 L150 190 L250 220 L380 140 L480 200 L600 110 L700 170 L800 130 L800 300Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom;
        pointer-events: none;
    }

    .relive-header-content { position: relative; z-index: 1; }

    .relive-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.75);
        margin-bottom: 10px;
    }

    .relive-title {
        font-family: 'DM Serif Display', serif;
        font-size: 28px;
        font-weight: 400;
        color: white;
        line-height: 1.2;
        margin-bottom: 10px;
    }

    .relive-meta {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        color: rgba(255,255,255,0.8);
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .relive-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .relive-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        border-bottom: 1px solid #EBEBEB;
        flex-shrink: 0;
        background: white;
    }

    .relive-stat {
        padding: 16px 12px;
        text-align: center;
        border-right: 1px solid #EBEBEB;
    }

    .relive-stat:last-child { border-right: none; }

    .relive-stat-value {
        font-family: 'DM Serif Display', serif;
        font-size: 22px;
        color: var(--color-primary);
        line-height: 1;
        margin-bottom: 4px;
    }

    .relive-stat-label {
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #999;
    }

    .relive-body {
        flex: 1;
        overflow-y: auto;
        padding: 32px;
        scroll-behavior: smooth;
    }

    .relive-body::-webkit-scrollbar { width: 4px; }
    .relive-body::-webkit-scrollbar-track { background: transparent; }
    .relive-body::-webkit-scrollbar-thumb { background: #D0D0D0; border-radius: 2px; }

    .relive-timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .tl-event {
        display: flex;
        gap: 20px;
        opacity: 0;
        transform: translateX(-16px);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .tl-event.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .tl-left {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
        width: 44px;
    }

    .tl-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
        z-index: 1;
        border: 3px solid #FAFAF8;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .tl-line {
        width: 2px;
        flex: 1;
        min-height: 28px;
        background: linear-gradient(to bottom, #E0E0E0 0%, #E0E0E0 100%);
        margin: 4px 0;
    }

    .tl-right {
        flex: 1;
        padding-bottom: 28px;
        min-width: 0;
    }

    .tl-event:last-child .tl-right { padding-bottom: 8px; }
    .tl-event:last-child .tl-line  { display: none; }

    .tl-time {
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #ABABAB;
        margin-bottom: 6px;
    }

    .tl-card {
        background: white;
        border-radius: 14px;
        padding: 16px 18px;
        border: 1px solid #EBEBEB;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s;
    }

    .tl-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }

    .tl-card-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: #1A1A1A;
        margin-bottom: 4px;
    }

    .tl-card-desc {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        color: #888;
        line-height: 1.5;
    }

    .tl-card-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .tl-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        font-family: 'DM Sans', sans-serif;
        letter-spacing: 0.3px;
    }

    .tl-checkpoint-number {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: var(--color-primary);
        color: white;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .tl-event-start    .tl-icon { background: #E3F2FD; color: #1565C0; }
    .tl-event-payment  .tl-icon { background: #E8F5E9; color: #2E7D32; }
    .tl-event-begin    .tl-icon { background: #FFF8E1; color: #F9A825; }
    .tl-event-checkpoint .tl-icon { background: #F3E5F5; color: #6A1B9A; }
    .tl-event-finish   .tl-icon { background: #E8F5E9; color: #1B5E20; }

    .tl-event-finish .tl-card {
        background: linear-gradient(135deg, #E8F5E9 0%, #F1F8E9 100%);
        border-color: #C8E6C9;
    }

    .tl-event-finish .tl-card-title { color: #1B5E20; font-size: 16px; }

    .relive-footer {
        padding: 20px 32px;
        border-top: 1px solid #EBEBEB;
        background: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .relive-footer-rating {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .relive-footer-stars {
        display: flex;
        gap: 4px;
    }

    .relive-footer-star {
        font-size: 20px;
        transition: transform 0.15s;
    }

    .relive-footer-star:hover { transform: scale(1.2); }

    .btn-relive {
        background: linear-gradient(135deg, #E8F5E9 0%, #F1F8E9 100%);
        color: var(--color-primary);
        border: 1px solid #C8E6C9;
        font-weight: 700;
        transition: all 0.2s;
    }

    .btn-relive:hover {
        background: linear-gradient(135deg, #C8E6C9 0%, #DCEDC8 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(27,94,32,0.15);
    }

    @media (max-width: 640px) {
        .relive-overlay { padding: 0; align-items: flex-end; }
        .relive-modal {
            max-height: 95vh;
            border-radius: 24px 24px 0 0;
            animation: mobileModalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes mobileModalIn {
            from { opacity: 0; transform: translateY(60px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .relive-title { font-size: 22px; }
        .relive-body  { padding: 20px; }
        .relive-footer { padding: 16px 20px; }
        .relive-stats { grid-template-columns: repeat(2, 1fr); }
        .relive-stat:nth-child(2) { border-right: none; }
        .relive-stat:nth-child(3) { border-top: 1px solid #EBEBEB; }
        .relive-stat:nth-child(4) { border-top: 1px solid #EBEBEB; border-right: none; }
    }
</style>

<script>
(function () {
    var data        = window.__RELIVE_DATA__ || {};
    var animTimers  = [];

    window.reliveTrip = function (bookingId) {
        var booking = data[bookingId];
        if (!booking) {
            console.error('No relive data for booking', bookingId);
            return;
        }

        buildModal(booking);

        var overlay = document.getElementById('reliveOverlay');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeReliveModal = function () {
        var overlay = document.getElementById('reliveOverlay');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        animTimers.forEach(clearTimeout);
        animTimers = [];
    };

    window.closeRelive = function (e) {
        if (e.target === document.getElementById('reliveOverlay')) {
            closeReliveModal();
        }
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeReliveModal();
    });

    function buildModal(b) {

        document.getElementById('reliveTitle').textContent = b.package_name;
        document.getElementById('reliveMeta').innerHTML =
            (b.region ? '<span><i class="fas fa-map-pin"></i>' + b.region + '</span>' : '') +
            '<span><i class="fas fa-calendar"></i>' + b.tour_date + '</span>' +
            '<span><i class="fas fa-users"></i>' + b.participants + ' ' + (b.participants > 1 ? 'trekkers' : 'trekker') + '</span>';

        var statsData = [
            { v: b.total_checkpoints, l: 'Checkpoints' },
            { v: b.trek_duration || (b.duration_days + 'd'),    l: 'Duration'    },
            { v: 'Rs. ' + Number(b.total_amount).toLocaleString(), l: 'Spent'   },
            { v: b.my_rating ? b.my_rating + '★' : '—',          l: 'Your Rating' },
        ];

        document.getElementById('reliveStats').innerHTML = statsData.map(function (s) {
            return '<div class="relive-stat">' +
                '<div class="relive-stat-value">' + s.v + '</div>' +
                '<div class="relive-stat-label">' + s.l + '</div>' +
            '</div>';
        }).join('');

        var events = [];

        events.push({
            type:  'start',
            icon:  'fas fa-ticket-alt',
            time:  b.created_human,
            title: 'Booking Created',
            desc:  'You booked ' + b.package_name + ' · Booking #' + b.booking_number,
            tags:  [
                { text: ucFirst(b.difficulty), bg: '#E8F5E9', color: '#2E7D32' },
                { text: ucFirst(b.trek_type || 'trek'), bg: '#E3F2FD', color: '#1565C0' },
            ],
        });

        if (b.confirmed_at) {
            events.push({
                type:  'payment',
                icon:  'fas fa-check-circle',
                time:  b.confirmed_human,
                title: 'Payment Confirmed',
                desc:  'Booking confirmed. Your spot was secured.',
                tags:  [{ text: 'Paid · Rs. ' + Number(b.total_amount).toLocaleString(), bg: '#E8F5E9', color: '#2E7D32' }],
            });
        }

        if (b.started_at) {
            events.push({
                type:  'begin',
                icon:  'fas fa-hiking',
                time:  b.started_human,
                title: 'Trek Began',
                desc:  'GPS tracking started. ' +
                    (b.pin ? 'Family tracking PIN: <strong>' + b.pin + '</strong>' : 'The adventure was underway.'),
                tags:  [{ text: 'Live GPS Active', bg: '#FFF8E1', color: '#F9A825' }],
            });
        }

        if (b.checkpoints && b.checkpoints.length > 0) {
            b.checkpoints.forEach(function (cp, idx) {
                events.push({
                    type:       'checkpoint',
                    icon:       'fas fa-map-marker-alt',
                    time:       cp.reached_human,
                    title:      cp.name,
                    cpNumber:   cp.order,
                    cpTotal:    b.total_checkpoints,
                    isFirst:    idx === 0,
                    isLast:     idx === b.checkpoints.length - 1,
                });
            });
        }

        if (b.completed_at) {
            events.push({
                type:  'finish',
                icon:  'fas fa-flag-checkered',
                time:  b.completed_human,
                title: 'Trek Complete!',
                desc:  'You completed ' + b.package_name + '.' +
                    (b.trek_duration ? ' Total time: ' + b.trek_duration + '.' : '') +
                    (b.my_rating ? ' You gave it ' + b.my_rating + ' stars.' : ''),
                tags:  [{ text: ' Adventure Complete', bg: '#E8F5E9', color: '#1B5E20' }],
            });
        }

        var timeline = document.getElementById('reliveTimeline');
        timeline.innerHTML = events.map(function (ev, i) {
            return renderEvent(ev, i);
        }).join('');

        var items = timeline.querySelectorAll('.tl-event');
        items.forEach(function (el, i) {
            var t = setTimeout(function () { el.classList.add('visible'); }, 80 + i * 120);
            animTimers.push(t);
        });

        buildFooter(b);
    }

    function renderEvent(ev, idx) {
        var iconHtml   = '<div class="tl-icon"><i class="' + ev.icon + '"></i></div>';
        var lineHtml   = '<div class="tl-line"></div>';

        var cardInner  = '';

        if (ev.type === 'checkpoint') {
            cardInner = '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">' +
                '<span class="tl-checkpoint-number">' + ev.cpNumber + '</span>' +
                '<div>' +
                    '<div class="tl-card-title">' + ev.title + '</div>' +
                    '<div class="tl-card-desc">Checkpoint ' + ev.cpNumber + ' of ' + ev.cpTotal + ' reached' +
                        (ev.isFirst ? ' · <strong style="color:var(--color-primary)">First checkpoint! 🌟</strong>' : '') +
                        (ev.isLast  ? ' · <strong style="color:var(--color-success)">Final checkpoint! 🏔️</strong>' : '') +
                    '</div>' +
                '</div>' +
            '</div>';
        } else {
            cardInner = '<div class="tl-card-title">' + ev.title + '</div>';
            if (ev.desc) {
                cardInner += '<div class="tl-card-desc">' + ev.desc + '</div>';
            }
            if (ev.tags && ev.tags.length) {
                cardInner += '<div class="tl-card-tags">' +
                    ev.tags.map(function (t) {
                        return '<span class="tl-tag" style="background:' + t.bg + ';color:' + t.color + ';">' + t.text + '</span>';
                    }).join('') +
                '</div>';
            }
        }

        return '<div class="tl-event tl-event-' + ev.type + '">' +
            '<div class="tl-left">' +
                iconHtml +
                lineHtml +
            '</div>' +
            '<div class="tl-right">' +
                '<div class="tl-time">' + ev.time + '</div>' +
                '<div class="tl-card">' + cardInner + '</div>' +
            '</div>' +
        '</div>';
    }

    function buildFooter(b) {
        var footer = document.getElementById('reliveFooter');

        if (b.my_rating) {
            var stars = '';
            for (var i = 1; i <= 5; i++) {
                stars += '<span class="relive-footer-star" style="color:' + (i <= b.my_rating ? '#FFC107' : '#E0E0E0') + ';">' +
                    '<i class="fas fa-star"></i></span>';
            }
            footer.innerHTML = '<div class="relive-footer-rating">' +
                '<span style="font-family:DM Sans,sans-serif;font-size:13px;font-weight:600;color:#888;">Your rating</span>' +
                '<div class="relive-footer-stars">' + stars + '</div>' +
                '<span style="font-family:DM Serif Display,serif;font-size:18px;color:var(--color-primary);">' + b.my_rating + '/5</span>' +
            '</div>' +
            '<button onclick="closeReliveModal()" style="font-family:DM Sans,sans-serif;padding:10px 24px;background:var(--color-primary);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">Close</button>';
        } else {
            footer.innerHTML = '<div style="font-family:DM Sans,sans-serif;font-size:13px;color:#888;">Head to My Bookings to rate this trek.</div>' +
                '<button onclick="closeReliveModal()" style="font-family:DM Sans,sans-serif;padding:10px 24px;background:var(--color-primary);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">Close</button>';
        }
    }

    function ucFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
})();
</script>