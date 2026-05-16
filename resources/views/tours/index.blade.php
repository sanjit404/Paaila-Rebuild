@extends('layouts.app')

@section('title', 'Paaila Treks')

@section('content')
<style>
        html{
            scroll-behavior: smooth;
        }
        .fade-in-up {
            opacity: 0;
            transform: translateY(15px);
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .pref-card {
            background: linear-gradient(135deg, #FFFDF7 0%, #FFF8E1 100%);
            border: 1px solid rgba(245, 127, 23, 0.15);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            padding: 20px 24px;
            margin: var(--space-xl) auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pref-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(245, 127, 23, 0.08);
        }

        .filter-scroll-wrapper {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 12px;
            margin-bottom: -12px;
            -ms-overflow-style: none;
            scrollbar-width: none; 
            scroll-behavior: smooth;
            -webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
            mask-image: linear-gradient(to right, black 90%, transparent 100%);
        }
        .filter-scroll-wrapper::-webkit-scrollbar {
            display: none; 
        }

        .filter-chip {
            white-space: nowrap;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            border: 1px solid #EBEBEB;
            background: #FFFFFF;
            color: #555555;
        }
        
        .filter-chip:hover {
            background: #F7F7F7;
            border-color: #DDDDDD;
            color: #222222;
        }

        .filter-chip.active {
            background: var(--color-primary); 
            color: #FFFFFF;
            border-color: #222222;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .section-title {
            font-size: 26px;
            font-weight: 800;
            color: #222222;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        #packagesGrid {
            transition: opacity 0.3s ease;
        }
        
        .see-all-link {
            font-size: 15px; 
            color: var(--color-primary); 
            text-decoration: none; 
            font-weight: 600; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }
        .see-all-link:hover {
            background: rgba(0,0,0,0.03);
        }
    </style>
<section style="background: linear-gradient(135deg, rgba(27, 94, 32, 0.95) 0%, rgba(46, 125, 50, 0.3) 100%), url('https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=1600') center/cover; padding: 80px 0; color: white;">
    <div class="container">
        <div style="max-width: 700px;">
            <h1 style="font-size: 48px; font-weight: 800; color: white; margin-bottom: var(--space-md); line-height: 1.1;">
                Trek Nepal with Paaila<br>Real-Time GPS Tracking
            </h1>
            <p style="font-size: 18px; color: rgba(255,255,255,0.9); margin-bottom: var(--space-xl); line-height: 1.6;">
                Use PAAILA, do nothing. <br>Explore the Himalayas safely. Every step tracked. Every checkpoint monitored. Your loved ones always know where you are.
            </p>
            <div class="flex gap-md" style="flex-wrap: wrap;">
                <a href="#treks" class="btn btn-cta btn-lg" >
                    <i class="fas fa-hiking"></i>
                    Browse Treks
                </a>
                <a href="{{ route('tracking.pin.entry') }}" class="shiny-tbg btn btn-secondary btn-lg" style="border-color: white; background:transparent; color: white;">
                    <i class="fas fa-map-marker-alt fa-bounce"></i>
                    Track Someone
                </a>
            </div>
        </div>
    </div>
</section>



<section class="section" id="treks" style="background: var(--color-bg);">
    <div class="container">

        <div style="margin-bottom: 32px;">
            <h2 class="section-title">Discover All Treks</h2>
            <p style="color: #717171; font-size: 15px; ">Find your perfect adventure by category.</p>
           
            <div class="filter-scroll-wrapper">
                @php
                    $types = [
                        'all'         => 'All Treks',
                        'nature'      => 'Nature & Scenery',
                        'historical'  => 'Historical',
                        'cultural'    => 'Cultural',
                        'adventure'   => 'Extreme Adventure',
                        'spiritual'   => 'Spiritual',
                        'wildlife'    => 'Wildlife',
                        'village'     => 'Village Tours'
                    ];
                @endphp
                @foreach($types as $value => $label)
                    <button
                        onclick="filterTreks('{{ $value }}')"
                        id="filter-{{ $value }}"
                        class="filter-chip {{ $value === 'all' ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @if($packages->count() > 0)
            <div class="grid grid-3" id="packagesGrid">
                @foreach($packages as $package)
                        <a 
                        style="text-decoration:none; "
                        href="{{ route('tours.show', $package) }}" 
                        class="card shiny-tbg package-item" 
                        data-type="{{ $package->trek_type }}">
                        @if($package->image == null)
                        <img
                            src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&h=400&fit=crop"
                            alt="{{ $package->name }}"
                            class="card-image"
                        >
                        @else
                        <img
                            src="{{ $package->image }}"
                            alt="{{ $package->name }}"
                            class="card-image"
                        >
                        @endif
                        <div class="card-body">
                            <div style="display:flex; margin-bottom: var(--space-md); ">
                                @if($package->difficulty_level === 'easy')
                                    <span class="badge badge-success">
                                        <i class="fas fa-circle"></i> Easy
                                    </span>
                                @elseif($package->difficulty_level === 'moderate')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-circle"></i> Moderate
                                    </span>
                                @else
                                    <span class="badge badge-error">
                                        <i class="fas fa-circle"></i> Hard
                                    </span>
                                @endif
                                @if(($package->rating_count ?? 0) > 0)
                                <div style="
                                color: var(--color-primary-dark); padding: 3px 8px; border-radius: 12px;
                                font-size: 12px; font-weight: 600;
                                display: flex; align-items: center; gap: 4px; backdrop-filter: blur(4px);"
                                >
                                <span class="badge badge-success" style="opacity: 0.75; font-size: 11px;">
                                <i class="fas fa-star" style="color: #FFC107; font-size: 11px;"></i>
                                {{ number_format($package->rating_avg, 1) }}
                                ({{ $package->rating_count }})</span>
                                </div>
                                 @endif
                                 @if($package->trek_type ?? false)
                                <div style="position: absolute; top: 10px; right: 10px;">
                                    <span style="background: rgba(255,255,255,0.92); color: var(--color-primary);
                                                padding: 3px 10px; border-radius: 20px;
                                                font-size: 11px; font-weight: 700;
                                                text-transform: capitalize;">
                                        {{ $package->trek_type }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            

                            <h3 class="card-title">{{ $package->name }}</h3>

                            <p class="card-text">{{ Str::limit($package->description, 100) }}</p>

                            <div style="display: flex; gap: var(--space-lg); margin-bottom: var(--space-md); font-size: 14px; color: var(--color-text-light);">
                                <div class="flex" style="align-items: center; gap: var(--space-xs);">
                                    <i class="fas fa-calendar" style="color: var(--color-primary);"></i>
                                    <span>{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}</span>
                                </div>
                                <div class="flex" style="align-items: center; gap: var(--space-xs);">
                                    <i class="fas fa-map-marker-alt" style="color: var(--color-primary);"></i>
                                    <span>{{ $package->checkpoints->count() }} Stops</span>
                                </div>
                                @if($package->region ?? false)
                                <span style="color: var(--color-primary);
                                             text-transform: capitalize;"
                                            >
                                <i class="fas fa-map-pin"></i> 
                                {{ $package->region }}
                                </span>
                                 @endif
                            </div>
                            

                            <div class="flex-between" style="margin-top: var(--space-lg);">
                                <div>
                                    <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: 2px;">From</div>
                                    <div style="font-size: 24px; font-weight: 700; color: var(--color-primary);">
                                        Rs. {{ number_format($package->price, 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                </a>

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
</section>

<center>
<div class="vibe-card-wrap ">
    <div class="vibe-card shiny-tbg">
        <p class="vibe-text">Can’t find a trek that matches your vibe?</p>

        <a href="{{ route('tour.foryou') }}" class="vibe-link">
             <i class="fas fa-heart fa-fade" style="color:var(--color-primary-dark);"></i>Click here to find the perfect trek for you.
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

<style>
.vibe-card-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 28px 28px 0px 0px;
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
    color: var(--color-primary-dark);
    background: white;
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

#packagesGrid {
    transition: opacity 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script>
let activeFilter = 'all';

function filterTreks(type) {
    if (activeFilter === type) return;
    activeFilter = type;

    document.querySelectorAll('.filter-chip').forEach(function(btn) {
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
</script>
@endpush