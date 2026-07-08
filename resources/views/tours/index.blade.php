@extends('layouts.app')

@section('title', 'Paaila')

@section('content')
<section style="background: linear-gradient(135deg, rgba(9, 29, 10, 0.95) 0%, rgba(2, 21, 3, 0.3) 100%), url('{{ asset('images/bg_wall_trek.jpg') }}') center/cover; padding: 80px 0; color: white;">
    <div class="container" style="display:flex;">
        <div class="hero-content">
            <div class="hero-logo-row">
                <img src="{{ asset('images/paailaLogo.png') }}" alt="Paaila logo" class="hero-logo-img ">
                <h1 class="hero-wordmark almendra-bold">Paaila </h1>
                <img src="{{ asset('images/Flag_of_Nepal.gif') }}" alt="Paaila logo" class="hero-logo-img ">
            </div>

            <p class="hero-tagline tangerine-regular">because every step matters</p>
            <div class="hero-rule" aria-hidden="true"></div>

            <p class="hero-desc">
                Explore Nepal with planned treks,
                real-time tracking, and experiences designed to keep every journey 
                meaningful, and unforgettable <i class="fas fa-solid fa-heart"></i>
            </p>

            <div class="hero-rule" aria-hidden="true"></div>

            <p class="hero-desc" style="margin-left:130px;color:white;">
             पाइला  |  𑐥𑐵𑐂𑐮𑑂𑐴
            </p>

            <div class="hero-actions">
                <a href="#treks" class="btn-hero-primary">
                    <i class="fas fa-solid fa-person-hiking" aria-hidden="true"></i>
                    Browse Treks
                </a>
                <a href="{{ route('tracking.pin.entry') }}" class="btn-hero-ghost shiny-tbg">
                    <span class="hero-live-dot" aria-hidden="true"></span>
                    Track Someone
                </a>
            </div>
        </div>

        <div class="trek-carousel-wrapper" aria-label="Featured treks carousel">
            <div class="trek-carousel-container shiny-tbg" style="top:15%; left:150%;">
                <center>
                    <p style="font-family: 'Tangerine',cursive;
                     font-size:32px; 
                     color:white;
                     border-bottom:1px solid gray;
                     align-text:centre;">
                        Experience Nepal With Us
                    </p>
                </center>

                <div class="carousel-track-wrapper">
                    <x-prayer-flags />
                    <div class="carousel-track" id="carouselTrack">
                        @foreach($packages->take(8) as $package)
                        <a
                            href="{{ route('tours.show', $package) }}"
                            class="carousel-card"
                            data-name="{{ strtolower($package->name) }}"
                            @if($package->image == null)
                                style="background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&h=800&fit=crop');"
                            @else
                                style="background-image: url('{{ $package->image }}');"
                            @endif
                        >
                            <div class="carousel-overlay"></div>
                            <h2 class="carousel-title">{{ $package->name }}</h2>
                        </a>
                        @endforeach
                    </div>
                </div><br>

                <div class="carousel-dots" id="carouselDots" role="tablist" aria-label="carousel navigation"></div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="treks" style="background: var(--color-bg);">
    <div class="container">

        <div style="margin-bottom: 32px;">
            <h2 class="section-title">Discover All Treks</h2>
            <p style="color: #717171; font-size: 15px;">Search your perfect adventure.</p>

            <div style="display: flex; gap: 12px; align-items: center; margin-top: 12px;">
                <div style="flex: 1; position: relative; min-width: 200px;">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 13px; color: #9E9E9E;"></i>
                    <input
                        id="treksSearchInput"
                        type="text"
                        class="search-input-treks"
                        placeholder="Search by name, region, difficulty, season, price"
                        aria-label="Search treks">
                    <button id="treksSearchClearBtn" style="display: none; position: absolute; right: 10px; top: 8px; border: none; background: transparent; padding: 6px; cursor: pointer; color: #777;">
                        <i class="fas fa-times"></i>
                    </button>

                    <br>

                    <div class="filter-scroll-wrapper" style="flex: 0 1 auto;">
                        @php
                            $types = [
                                'all' => [
                                    'label'    => 'All treks',
                                    'subtitle' => 'Show everything',
                                    'image'    => 'https://images.unsplash.com/photo-1611516491426-03025e6043c8?w=600&auto=format&fit=crop&q=60',
                                ],
                                'nature' => [
                                    'label'    => 'Nature & scenery',
                                    'subtitle' => 'Lakes, forests, peaks',
                                    'image'    => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=600&auto=format&fit=crop&q=60',
                                ],
                                'historical' => [
                                    'label'    => 'Historical',
                                    'subtitle' => 'Heritage & monuments',
                                    'image'    => 'https://images.unsplash.com/photo-1550642249-b715bc35f898?w=600&auto=format&fit=crop&q=60',
                                ],
                                'cultural' => [
                                    'label'    => 'Cultural',
                                    'subtitle' => 'Festivals & homestays',
                                    'image'    => 'https://images.unsplash.com/photo-1622598661631-3a46559a4817?w=600&auto=format&fit=crop&q=60',
                                ],
                                'adventure' => [
                                    'label'    => 'Extreme adventure',
                                    'subtitle' => 'High passes & climbs',
                                    'image'    => 'https://plus.unsplash.com/premium_photo-1691735666207-be6e91326e3a?w=600&auto=format&fit=crop&q=60',
                                ],
                                'spiritual' => [
                                    'label'    => 'Spiritual',
                                    'subtitle' => 'Monasteries & temples',
                                    'image'    => 'https://images.unsplash.com/photo-1507743617593-0a422c9bb7f5?q=80&w=1374&auto=format&fit=crop',
                                ],
                                'wildlife' => [
                                    'label'    => 'Wildlife',
                                    'subtitle' => 'Safaris & reserves',
                                    'image'    => 'https://images.unsplash.com/photo-1549888668-19281758dfbe?w=600&auto=format&fit=crop&q=60',
                                ],
                                'village' => [
                                    'label'    => 'Village tours',
                                    'subtitle' => 'Slow travel in Nepal',
                                    'image'    => 'https://plus.unsplash.com/premium_photo-1697729729075-3e56242aef49?w=600&auto=format&fit=crop&q=60',
                                ],
                            ];
                        @endphp
                    </div>
                </div>
            </div>

            <p style="color: #717171; font-size: 15px;">Find By Categories</p>

            <div class="filter-scroll-wrapper">
                @foreach($types as $value => $meta)
                    <button
                        type="button"
                        onclick="filterTreks('{{ $value }}')"
                        id="filter-{{ $value }}"
                        class="filter-chip-card filter-chip-card--large {{ $value === 'all' ? 'active' : '' }}"
                    >
                        <div class="filter-chip-card-thumb-wrap">
                            <img
                                src="{{ $meta['image'] }}"
                                alt="{{ $meta['label'] }}"
                                loading="lazy"
                                class="filter-chip-card-thumb"
                            >
                            <div class="filter-chip-card-thumb-overlay"></div>
                        </div>

                        <div class="filter-chip-card-text">
                            <div class="filter-chip-card-label">
                                {{ $meta['label'] }}
                            </div>
                            @if(!empty($meta['subtitle']))
                                <div class="filter-chip-card-subtitle">
                                    {{ $meta['subtitle'] }}
                                </div>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>

            <br><hr><br>
            <h2 class="section-title" id="typ">ALL TREKS</h2>

            @if($packages->count() > 0)
                <div class="grid grid-4" id="packagesGrid">
                    @foreach($packages as $package)
                        <x-package-card :package="$package" :show-score="false" />
                    @endforeach
                </div>

                <div id="emptyFilter" style="display:none; text-align: center; padding: 80px 20px; background: #FFFFFF; border-radius: 16px; border: 1px solid #EBEBEB; margin-top: 20px;">
                    <div style="background: #F7F7F7; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                        <i class="fas fa-search" style="font-size: 32px; color: #BBBBBB;"></i>
                    </div>
                    <h3 style="font-size: 18px; color: #222222; margin-bottom: 8px;">No treks found</h3>
                    <p style="color: #717171; font-size: 15px; margin: 0;">No packages match this category right now.</p>
                </div>
            @else
                <div style="text-align: center; padding: var(--space-2xl) 0;">
                    <i class="fas fa-mountain" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-md);"></i>
                    <h3 style="margin-bottom: var(--space-sm);">No Treks Available</h3>
                    <p style="color: var(--color-text-light);">Check back soon for new adventures!</p>
                </div>
            @endif
        </div>
    </div>
</section>

<br>
<center>
    <div class="vibe-card-wrap">
        <div class="vibe-card shiny-tbg" style="background: linear-gradient(135deg, rgba(9, 29, 10, 0.95) 0%, rgba(2, 21, 3, 0.3) 100%), url('{{ asset('images/bg_wall_trek.jpg') }}') center/cover;">
            <p class="vibe-text">Can't find a trek that matches your vibe?</p>

            <a href="{{ route('tour.foryou') }}" class="vibe-link">
                <i class="fas fa-heart fa-fade" style="color:white;"></i>Click here to find the perfect trek for you.
            </a>
        </div>
    </div>
</center>
<br>

<section class="section" style="background: white; padding: var(--space-xl) 0;">
    <div class="container">
        <div class="grid grid-4">
            <div class="text-center">
                <div style="width: 60px; height: 60px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md);">
                    <i class="fas fa-satellite-dish" style="font-size: 24px; color: var(--color-primary);"></i>
                </div>
                <h4 style="margin-bottom: var(--space-sm);">Live GPS Tracking</h4>
                <p style="font-size: 14px; color: var(--color-text-light); margin: 0;">Real-time location updates every 5 seconds</p>
            </div>
            <div class="text-center">
                <div style="width: 60px; height: 60px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md);">
                    <i class="fas fa-shield-alt" style="font-size: 24px; color: var(--color-primary);"></i>
                </div>
                <h4 style="margin-bottom: var(--space-sm);">Safety First</h4>
                <p style="font-size: 14px; color: var(--color-text-light); margin: 0;">Automated checkpoint detection</p>
            </div>
            <div class="text-center">
                <div style="width: 60px; height: 60px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md);">
                    <i class="fas fa-users" style="font-size: 24px; color: var(--color-primary);"></i>
                </div>
                <h4 style="margin-bottom: var(--space-sm);">Family Monitoring</h4>
                <p style="font-size: 14px; color: var(--color-text-light); margin: 0;">Share PIN with loved ones</p>
            </div>
            <div class="text-center">
                <div style="width: 60px; height: 60px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md);">
                    <i class="fas fa-mountain" style="font-size: 24px; color: var(--color-primary);"></i>
                </div>
                <h4 style="margin-bottom: var(--space-sm);">Expert Guides</h4>
                <p style="font-size: 14px; color: var(--color-text-light); margin: 0;">Verified local trekking guides</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    html{ scroll-behavior: smooth; }

    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }

    /* container stays horizontally scrollable */
    .filter-scroll-wrapper {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        padding: 10px 2px 18px;
        margin-bottom: -10px;
        -ms-overflow-style: none;
        scrollbar-width: none;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        -webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
        mask-image: linear-gradient(to right, black 90%, transparent 100%);
    }

    .filter-scroll-wrapper::-webkit-scrollbar { display: none; }

    .filter-chip-card {
        scroll-snap-align: start;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        border-radius: 18px;
        border: 1px solid #e0e0e0;
        background: #ffffff;
        padding: 0;
        cursor: pointer;
        text-align: left;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        transition:
            box-shadow 0.2s ease,
            transform 0.18s ease,
            border-color 0.18s ease,
            background 0.18s ease;
    }

    .filter-chip-card--large {
        min-width: 220px;
        max-width: 260px;
    }

    .filter-chip-card-thumb-wrap {
        position: relative;
        height: 140px;
        overflow: hidden;
    }

    .filter-chip-card-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.03);
        transition: transform 0.4s ease;
    }

    .filter-chip-card-thumb-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.0) 0%,
            rgba(0, 0, 0, 0.35) 100%
        );
        pointer-events: none;
    }

    .filter-chip-card-text {
        padding: 10px 12px 12px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .filter-chip-card-label {
        font-size: 14px;
        font-weight: 700;
        color: #222222;
    }

    .filter-chip-card-subtitle {
        font-size: 12px;
        color: #717171;
    }

    .filter-chip-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
        border-color: #c2d6ff;
    }

    .filter-chip-card:hover .filter-chip-card-thumb {
        transform: scale(1.08);
    }

    .filter-chip-card.active {
        border-color: #0f5cd6;
        box-shadow: 0 0 0 2px rgba(15, 92, 214, 0.5);
    }

    .filter-chip-card.active .filter-chip-card-label {
        color: #0f5cd6;
    }

    @media (max-width: 640px) {
        .filter-scroll-wrapper {
            scroll-snap-type: x mandatory;
        }

        .filter-chip-card--large {
            min-width: 200px;
            max-width: 220px;
        }

        .filter-chip-card-thumb-wrap {
            height: 130px;
        }
    }

    .section-title {
        font-size: 26px;
        font-weight: 800;
        color: #222222;
        letter-spacing: -0.5px;
        margin-bottom: 8px;
    }

    .carousel-dots {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 0 12px 0;
    }

    .carousel-dots button {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        background: rgba(255, 255, 255, 0.35);
        cursor: pointer;
        padding: 0;
        transition: background 0.25s ease, transform 0.25s ease;
    }

    .carousel-dots button.active,
    .carousel-dots button[aria-selected="true"] {
        background: #ffffff;
        transform: scale(1.35);
    }

    .carousel-dots button:not(.active):hover {
        background: rgba(255, 255, 255, 0.65);
    }

    #packagesGrid {
        transition: opacity 0.3s ease;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 520px;
        padding: 72px 0 80px;
    }

    .hero-wordmark {
        font-family: 'Almendra', Georgia, serif;
        font-size: 64px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 12px;
        line-height: 1;
        margin: 0;
    }

    .hero-tagline {
        font-family: 'Tangerine', cursive;
        font-size: 28px;
        font-weight: 700;
        color: rgba(210, 210, 210, 0.78);
        margin: 0 0 0 95px;
        line-height: 1.5;
    }

    .hero-rule {
        width: 400px;
        height: 1px;
        background: rgba(255, 255, 255, 0.22);
        margin: 20px 0 22px;
        border: none;
    }

    .hero-desc {
        font-size: 15px;
        color: rgba(198, 197, 197, 0.69);
        line-height: 1.72;
        max-width: 400px;
        margin: 0 0 32px;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-hero-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #16631b;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        padding: 11px 26px;
        border-radius: 5px;
        border: none;
        text-decoration: none;
        transition: background 0.15s, transform 0.10s;
        letter-spacing: 0.2px;
    }
    .btn-hero-primary:hover  { background: #fff; transform: translateY(-1px); color: #1a1612; text-decoration: none; }
    .btn-hero-primary:active { transform: scale(0.98); }

    .btn-hero-ghost {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        color: rgba(255, 255, 255, 0.72);
        font-size: 14px;
        font-weight: 500;
        padding: 11px 26px;
        border-radius: 5px;
        border: 1px solid rgba(255, 255, 255, 0.86);
        text-decoration: none;
        transition: border-color 0.15s, color 0.15s, transform 0.10s;
        letter-spacing: 0.2px;
    }
    .btn-hero-ghost:hover  { border-color: rgba(255,255,255,0.48); color: #fff; transform: translateY(-1px); text-decoration: none; }
    .btn-hero-ghost:active { transform: scale(0.98); }

    .trek-carousel-wrapper {
        width: 340px;
        flex-shrink: 0;
    }

    .trek-carousel-container {
        background: rgba(67, 65, 65, 0.53);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        overflow: hidden;
    }

    .carousel-track-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        height: 280px;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.62s cubic-bezier(.22, .9, .32, 1);
        height: 100%;
    }

    .carousel-card {
        flex: 0 0 100%;
        display: block;
        text-decoration: none;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .carousel-card:hover { transform: scale(1.02); }

    .carousel-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.2) 0%,
            rgba(0, 0, 0, 0.35) 40%,
            rgba(0, 0, 0, 0.7) 100%
        );
        pointer-events: none;
    }

    .carousel-title {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        font-family: 'Tangerine', cursive;
        font-size: 32px;
        font-weight: 700;
        color: white;
        margin: 0;
        text-align: center;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        letter-spacing: 1px;
        width: 90%;
        line-height: 1;
        z-index: 2;
    }

    .search-input-treks {
        width: 100%;
        padding: 12px 40px 12px 42px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #E8E8E8;
        font-size: 14px;
        color: #222;
    }
    .search-input-treks::placeholder {
        color: #9E9E9E;
    }

    .hero-live-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #7ae87e;
        flex-shrink: 0;
        animation: live-pulse 2s ease-out infinite;
    }

    .vibe-card-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 28px 28px 0 0;
        padding: 0 16px;
    }

    .vibe-card {
        width: 100%;
        max-width: 560px;
        text-align: center;
        background: var(--color-primary-dark);
        border: 1px solid rgba(27, 94, 32, 0.12);
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        animation: pulseSoft 2.8s ease-in-out infinite;
        position: relative;
    }

    .vibe-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top, rgba(27, 94, 32, 0.08), transparent 55%);
        pointer-events: none;
    }

    .vibe-text {
        margin: 0 0 10px;
        font-size: 16px;
        line-height: 1.5;
        color: white;
        font-weight: 700;
        letter-spacing: -0.2px;
        position: relative;
        z-index: 1;
    }

    .vibe-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #ffffff;
        background: #1a7b24;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        padding: 10px 18px;
        border-radius: 999px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        position: relative;
        z-index: 1;
        box-shadow: 0 8px 20px rgba(27, 94, 32, 0.18);
    }

    .vibe-link:hover {
        transform: translateY(-1px);
        opacity: 0.95;
        box-shadow: 0 10px 24px rgba(27, 94, 32, 0.24);
    }

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

    @media (max-width: 640px) {
        .vibe-card {
            padding: 18px 16px;
            border-radius: 16px;
        }

        .vibe-text {
            font-size: 15px;
        }

        .vibe-link {
            width: 100%;
            padding: 11px 16px;
        }

        .trek-carousel-wrapper {
            display: none;
        }
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
            rgba(255, 255, 255, 0.1) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        transform: skewX(-25deg);
        animation: shine 2.5s infinite;
    }

    @keyframes shine {
        0%   { left: -150%; }
        100% { left: 150%; }
    }

    @keyframes live-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(122, 232, 122, 0.75); }
        60%  { box-shadow: 0 0 0 6px rgba(232, 201, 122, 0); }
        100% { box-shadow: 0 0 0 0 rgba(232, 201, 122, 0); }
    }

    @media (max-width: 576px) {
        .hero-content   { padding: 52px 0 60px; }
        .hero-wordmark  { font-size: 46px; letter-spacing: 8px; }
        .hero-logo-img  { height: 48px; }
        .btn-hero-primary,
        .btn-hero-ghost { padding: 10px 20px; font-size: 14px; }
        .trek-carousel-wrapper { display: none; }
    }

    #packagesGrid { transition: opacity 0.3s ease; }
</style>
@endpush

@push('scripts')
<script>
let activeFilter = 'all';

function filterTreks(type) {
    if (activeFilter === type) return;
    activeFilter = type;
    document.getElementById('typ').innerHTML = `${activeFilter.toUpperCase()} TREKS`;

    document.querySelectorAll('.filter-chip-card').forEach(function(btn) {
        if (btn.id === 'filter-' + type) {
            btn.classList.add('active');
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else {
            btn.classList.remove('active');
        }
    });

    const grid = document.getElementById('packagesGrid');
    const emptyMsg = document.getElementById('emptyFilter');

    if (!grid) return;

    grid.style.opacity = '0';

    setTimeout(function() {
        let visibleCount = 0;

        document.querySelectorAll('.package-item').forEach(function(item) {
            const pkgType = (item.dataset.type || '').trim();
            if (type === 'all' || pkgType === type) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        grid.style.opacity = '1';
    }, 300);
}

const track = document.getElementById('carouselTrack');

if (track) {
    const cards = Array.from(track.querySelectorAll('.carousel-card'));
    const dotsContainer = document.getElementById('carouselDots');
    let index = 0;
    let autoTimer = null;
    const slideInterval = 3500;
    const allowAuto = cards.length > 1;

    cards.forEach((_, i) => {
        const btn = document.createElement('button');
        btn.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        btn.setAttribute('aria-label', 'Slide ' + (i + 1));
        btn.addEventListener('click', () => goTo(i));
        dotsContainer.appendChild(btn);
    });
    const dots = Array.from(dotsContainer.querySelectorAll('.carousel-dot'));

    function update() {
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === index));
    }

    function next() {
        index = (index + 1) % cards.length;
        update();
    }

    function startAuto() { if (allowAuto && !autoTimer) autoTimer = setInterval(next, slideInterval); }
    function stopAuto() { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } }
    function goTo(i) { index = i % cards.length; update(); }

    update();
    startAuto();

    track.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft')  { index = (index - 1 + cards.length) % cards.length; update(); }
        if (e.key === 'ArrowRight') { index = (index + 1) % cards.length; update(); }
    });
}

const treksSearchInput = document.getElementById('treksSearchInput');
const treksSearchClear = document.getElementById('treksSearchClearBtn');

function normalizeText(s) {
    return (s || '').toString().toLowerCase();
}

if (treksSearchInput) {
    treksSearchInput.addEventListener('input', function(e) {
        const q = normalizeText(e.target.value).trim();
        treksSearchClear.style.display = q ? 'block' : 'none';

        const packageItems = document.querySelectorAll('.package-item');

        packageItems.forEach(item => {
            const name = normalizeText(item.querySelector('.pkg-title')?.textContent);
            const difficulty = normalizeText(item.querySelector('.pkg-difficulty')?.textContent);
            const region = normalizeText(item.querySelector('.pkg-region')?.textContent);
            const season = normalizeText(item.dataset.season || '');
            const price = normalizeText(item.querySelector('.pkg-price')?.textContent);

            const combined = [name, difficulty, region, season, price].join(' ');
            item.style.display = (!q || combined.includes(q)) ? '' : 'none';
        });
    });

    treksSearchClear.addEventListener('click', function() {
        treksSearchInput.value = '';
        treksSearchClear.style.display = 'none';
        document.querySelectorAll('.package-item').forEach(item => item.style.display = '');
    });
}
</script>
@endpush