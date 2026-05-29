@extends('layouts.app')

@section('title', 'Paaila — Explore Nepal\'s Finest Treks')

@php
    $isLoggedIn = auth()->check();

    if ($isLoggedIn) {
        $userId          = auth()->id();
        $hasPrefs        = \App\Models\UserPreference::hasPreferences($userId);
        $recommendations = \App\Services\RecommendationService::getForUser($userId, 6);
    } else {
        $hasPrefs        = false;
        $recommendations = collect();
    }

    $popularNow = \App\Services\RecommendationService::getPopularNow(6);
    $trending   = \App\Services\RecommendationService::getTrending(4);
@endphp

@section('content')

    <style>
        .fade-in-up {
            opacity: 0;
            transform: translateY(15px);
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.3s; }
        .delay-3 { animation-delay: 0.4s; }

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
            transition: transform 0.3s ease, box-shadow 0.6s ease;
        }
        .pref-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(245, 127, 23, 0.08);
        }

        .filter-scroll-wrapper {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 12px;
    margin-bottom: -12px;

    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;

    -ms-overflow-style: none;
    scrollbar-width: none;

    -webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
    mask-image: linear-gradient(to right, black 90%, transparent 100%);

    will-change: transform;
}

        .filter-scroll-wrapper::-webkit-scrollbar {
            display: none;
        }

        .section-title {
            font-size: 26px;
            font-weight: 800;
            color: #222222;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        #packagesGrid {
            transition: opacity 0.5s ease;
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
            transition: background 0.5s ease;
        }
        .see-all-link:hover {
            background: rgba(0,0,0,0.03);
        }
    </style>

    @auth
        @if(!$hasPrefs)
            <div class="container fade-in-up delay-1">
                <div class="pref-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--space-md);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="background: #FFF; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                                <i class="fas fa-sparkles" style="font-size: 20px; color: #F57F17;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-weight: 700; font-size: 17px; color: #222;">
                                    Personalise your journey
                                </h3>
                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #666;">
                                    Tell us what you love to do. It only takes 30 seconds.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('preferences.create') }}" class="btn btn-cta" style="border-radius: 30px; padding: 10px 24px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            Set Preferences
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    @if($isLoggedIn && $hasPrefs && $recommendations->isNotEmpty())
    
        <div class="fade-in-up delay-1">
            <x-recommendations
                :recommendations="$recommendations"
                title="Recommended for You"
                subtitle="Curated based on your travel style"
                reason-icon="fa-compass"
                reason-color="#2E7D32"
                reason-bg="#E8F5E9"
            />
        </div>
        <div class="container"><hr style="border: 0; border-top: 1px solid #EBEBEB; margin: var(--space-lg) 0;"></div>
    @endif

    @if($popularNow->isNotEmpty())
        <section class="fade-in-up delay-2" style="padding: var(--space-xl) 0; background: #FFFFFF;">
            <div class="container">
                <div class="flex-between" style="margin-bottom: var(--space-xl); align-items: flex-end; flex-wrap: wrap; gap: var(--space-md);">
                    <div>
                        <h2 class="section-title">
                            Trending Destinations <i class="fas fa-fire-alt" style="color: #FF5A5F; font-size: 22px; margin-left: 4px;"></i>
                        </h2>
                        <p style="color: #717171; font-size: 15px; margin: 0;">
                            The most highly-rated and frequently booked treks this season
                        </p>
                    </div>
                    <a href="{{ route('home') }}" class="see-all-link">
                        Explore all <i class="fas fa-chevron-right" style="font-size: 11px;"></i>
                    </a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                    @foreach($popularNow as $package)
                        <x-package-card :package="$package" :show-score="false" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    

@endsection

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
    grid.style.opacity = '0';

    setTimeout(() => {
        let visibleCount = 0;
        document.querySelectorAll('.package-item').forEach(function(item) {
            const pkgType = item.dataset.type || 'all';
            if (type === 'all' || pkgType === type) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        grid.style.opacity = '1';
    }, 300); 
}
</script>
@endpush