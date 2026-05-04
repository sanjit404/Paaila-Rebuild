@extends('layouts.app')

@section('title', 'Discover Nepal Treks')

@section('content')
<section style="background: linear-gradient(135deg, rgba(27, 94, 32, 0.95) 0%, rgba(46, 125, 50, 0.3) 100%), url('https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=1600') center/cover; padding: 80px 0; color: white;">
    <div class="container">
        <div style="max-width: 700px;">
            <h1 style="font-size: 48px; font-weight: 800; color: white; margin-bottom: var(--space-md); line-height: 1.1;">
                Trek Nepal with<br>Real-Time GPS Tracking
            </h1>
            <p style="font-size: 18px; color: rgba(255,255,255,0.9); margin-bottom: var(--space-xl); line-height: 1.6;">
                Explore the Himalayas safely. Every step tracked. Every checkpoint monitored. Your loved ones always know where you are.
            </p>
            <div class="flex gap-md" style="flex-wrap: wrap;">
                <a href="#treks" class="btn btn-cta btn-lg" >
                    <i class="fas fa-hiking"></i>
                    Browse Treks
                </a>
                <a href="{{ route('tracking.pin.entry') }}" class="shiny-tbg btn btn-secondary btn-lg" style="border-color: white; background:transparent; color: white;">
                    <i class="fas fa-map-marker-alt"></i>
                    Track Someone
                </a>
            </div>
        </div>
    </div>
</section>

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

<section class="section" id="treks" style="background: var(--color-bg);">
    <div class="container">
        <div class="text-center mb-xl">
            <h2 style="font-size: 32px; font-weight: 700; margin-bottom: var(--space-sm);">Featured Treks</h2>
            <p style="color: var(--color-text-light); font-size: 16px;">Choose your adventure. Track your journey.</p>
        </div>

        @if($packages->count() > 0)
            <div class="grid grid-3">
                @foreach($packages as $package)
                    <div class="card shiny-tbg">
                        <img 
                            src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&h=400&fit=crop" 
                            alt="{{ $package->name }}" 
                            class="card-image"
                        >

                        <div class="card-body">
                            <div style="margin-bottom: var(--space-md);">
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
                            </div>

                            <div class="flex-between" style="margin-top: var(--space-lg);">
                                <div>
                                    <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: 2px;">From</div>
                                    <div style="font-size: 24px; font-weight: 700; color: var(--color-primary);">
                                        Rs. {{ number_format($package->price, 0) }}
                                    </div>
                                </div>
                                <a href="{{ route('tours.show', $package) }}" class="btn btn-primary">
                                    View Details
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
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

<section class="section" style="background: white;">
    <div class="container">
        <div class="text-center mb-xl">
            <h2 style="font-size: 32px; font-weight: 700; margin-bottom: var(--space-sm);">How It Works</h2>
            <p style="color: var(--color-text-light); font-size: 16px;">Simple. Safe. Tracked.</p>
        </div>

        <div class="grid grid-3">
            <div class="text-center">
                <div style="width: 80px; height: 80px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg); font-size: 32px; font-weight: 700;">
                    1
                </div>
                <h3 style="margin-bottom: var(--space-md);">Book Your Trek</h3>
                <p style="color: var(--color-text-light); font-size: 14px;">
                    Choose a trek, select your date, and complete booking in minutes.
                </p>
            </div>

            <div class="text-center">
                <div style="width: 80px; height: 80px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg); font-size: 32px; font-weight: 700;">
                    2
                </div>
                <h3 style="margin-bottom: var(--space-md);">Start Tracking</h3>
                <p style="color: var(--color-text-light); font-size: 14px;">
                    Click "Start Tour" to activate GPS. Get your unique PIN to share.
                </p>
            </div>

            <div class="text-center">
                <div style="width: 80px; height: 80px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg); font-size: 32px; font-weight: 700;">
                    3
                </div>
                <h3 style="margin-bottom: var(--space-md);">Trek Safely</h3>
                <p style="color: var(--color-text-light); font-size: 14px;">
                    Your family monitors your progress. Automatic checkpoint detection.
                </p>
            </div>
        </div>
    </div>
</section>

<section style="background: var(--color-primary); padding: var(--space-2xl) 0; color: white;">
    <div class="container text-center">
        <h2 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: var(--space-md);">
            Ready to Trek Nepal Safely?
        </h2>
        <p style="font-size: 18px; color: rgba(255,255,255,0.9); margin-bottom: var(--space-xl); max-width: 600px; margin-left: auto; margin-right: auto;">
            Book now and get real-time GPS tracking for your entire journey.
        </p>
        <a href="#treks" class="btn btn-cta btn-lg">
            <i class="fas fa-hiking"></i>
            Browse All Treks
        </a>
    </div>
</section>



@push('styles')
<style>


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
    0% {
        left: -150%;
    }
    100% {
        left: 150%;
    }
}

</style>
@endpush
@endsection