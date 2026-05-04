@extends('layouts.app')

@section('title', 'Discover Treks & Offers')

@section('content')
<section style="background: white; position: sticky; top: 70px; z-index: 100; border-bottom: 1px solid #E0E0E0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div class="container">
        <div style="display: flex; gap: var(--space-sm); padding: var(--space-md) 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <a href="{{ route('feed.index') }}" 
               class="filter-btn {{ !request('type') && !request('trending') ? 'active' : '' }}">
                All
            </a>
            <a href="{{ route('feed.index', ['type' => 'trek']) }}" 
               class="filter-btn {{ request('type') == 'trek' ? 'active' : '' }}">
                Treks
            </a>
            <a href="{{ route('feed.index', ['type' => 'offer']) }}" 
               class="filter-btn {{ request('type') == 'offer' ? 'active' : '' }}">
                Offers
            </a>
            <a href="{{ route('feed.index', ['type' => 'news']) }}" 
               class="filter-btn {{ request('type') == 'news' ? 'active' : '' }}">
                News
            </a>
            <a href="{{ route('feed.index', ['trending' => 1]) }}" 
               class="filter-btn {{ request('trending') ? 'tactive' : '' }} shiny-tbg" style="background:transparent;">
                <i class="fas fa-fire"></i> Trending
            </a>
        </div>
    </div>
</section>

<section class="section" style="background: var(--color-bg);">
    <div class="container">
        @if($highlighted)
            <div style="margin-bottom: var(--space-xl);">
                <div style="position: relative; height: 400px; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg);">
                    <img 
                        src="{{ $highlighted->image ?: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200' }}" 
                        alt="{{ $highlighted->title }}"
                        style="width: 100%; height: 100%; object-fit: cover;"
                    >
                    
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);"></div>
                    
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: var(--space-xl); color: white;">
                            @if($highlighted->type=='news')
                        <span class="badge badge-warning" style="background-color:#ff0000; margin-bottom: var(--space-md); font-size: 13px; animation:pulse 1s infinite;">
                            <i class="fas fa-fire" style="color: #ffffff; "></i> 
                                  <font color="#ffffff">HOT NEWS</font>
                        </span>
                            @else
                        <span class="badge badge-warning shiny-bg" style="margin-bottom: var(--space-md); font-size: 13px; ">
                            <i class="fas fa-star " style="color:white;"></i> 
                                <font color="white">FEATURED OFFER</font>
                        </span>
                            @endif
                        <h2 style="font-size: 32px; font-weight: 700; color: white; margin-bottom: var(--space-md); text-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                            {{ $highlighted->title }}
                        </h2>
                        <p style="font-size: 16px; color: rgba(255,255,255,0.9); margin-bottom: var(--space-lg); max-width: 600px; text-shadow: 0 1px 4px rgba(0,0,0,0.3);">
                            {{ Str::limit($highlighted->content, 150) }}
                        </p>
                        <div class="flex gap-md" style="align-items: center;">
                            <a href="{{ route('feed.show', $highlighted) }}" class="btn btn-cta btn-sm" style="background-color:var(--color-primary-light)">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                            
                            @if($highlighted->type!='news')
                            @if($highlighted->trek_id)
                                <a href="{{ route('tours.show', $highlighted->trek_id) }}" class="btn btn-secondary btn-sm shiny-bg" style="border-color: white; color: white;background:transparent;">
                                    <i class="fas fa-hiking"></i>
                                    Book Trek
                                </a>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($posts->count() > 0)
            <div class="grid grid-2" style="gap: var(--space-lg);">
                @foreach($posts as $post)
                    <div class="card feed-card">
                        <!-- Image -->
                        <div style="position: relative; overflow: hidden; border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                            <img 
                                src="{{ $post->image ?: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600' }}" 
                                alt="{{ $post->title }}"
                                style="width: 100%; height: 200px; object-fit: cover; transition: transform 0.3s ease;"
                                class="feed-card-image"
                            >
                            
                            <div style="position: absolute; top: var(--space-md); left: var(--space-md);">
                                @if($post->type === 'trek')
                                    <span class="badge badge-primary shiny-tag" style="font-size: 11px;">
                                        <i class="fas fa-mountain"></i> TREK
                                    </span>
                                @elseif($post->type === 'offer')
                                    <span class="badge badge-warning shiny-tag" style="font-size: 11px;">
                                        <i class="fas fa-tag"></i> OFFER
                                    </span>
                                @else
                                    <span class="badge shiny-tag" style="background: #fae5e5; color: #ff0000; font-size: 11px;">
                                        <i class="fas fa-newspaper"></i> NEWS
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: var(--space-sm); color: var(--color-text);">
                                {{ Str::limit($post->title, 60) }}
                            </h3>
                            <p style="font-size: 14px; color: var(--color-text-light); margin-bottom: var(--space-md); line-height: 1.6;">
                                {{ Str::limit(strip_tags($post->content), 100) }}
                            </p>

                            <div class="flex gap-lg" style="margin-bottom: var(--space-md); font-size: 13px; color: var(--color-text-light);">
                                <div class="flex" style="align-items: center; gap: 4px;">
                                    <i class="fas fa-heart" style="color: {{ in_array($post->id, $likedPostIds) ? '#E53935' : 'currentColor' }};"></i>
                                    <span>{{ $post->likes_count }}</span>
                                </div>

                                <div class="flex" style="align-items: center; gap: 4px;">
                                    <i class="fas fa-star" style="color: #FFA000;"></i>
                                    <span>{{ number_format($post->rating_avg, 1) }}</span>
                                    <span style="opacity: 0.6;">({{ $post->rating_count }})</span>
                                </div>
                            </div>

                            <div class="flex gap-sm">
                                <a href="{{ route('feed.show', $post) }}" class="btn btn-primary btn-sm" style="flex: 1;">
                                    View Details
                                </a>
                                
                                @if($post->trek_id)
                                    <a href="{{ route('tours.show', $post->trek_id) }}" class="btn btn-cta btn-sm">
                                        Book
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: var(--space-xl);">
                {{ $posts->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-2xl);">
                    <i class="fas fa-inbox" style="font-size: 64px; color: #E0E0E0; margin-bottom: var(--space-lg);"></i>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: var(--space-sm);">No Posts Found</h3>
                    <p style="color: var(--color-text-light); margin: 0;">Check back later for updates</p>
                </div>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        color: var(--color-text-light);
        background: white;
        border: 2px solid #E0E0E0;
        border-radius: 20px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        border-color: var(--color-primary);
        color: var(--color-primary);
    }

    .filter-btn.active {
        background: var(--color-primary);
        border-color: var(--color-primary);
        color: white;
    }
.filter-btn.tactive {
        background: var(--color-primary);
        border-color: var(--color-primary);
        color: var(--color-primary);
    }
    .feed-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feed-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .feed-card:hover .feed-card-image {
        transform: scale(1.05);
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 140, 0, 0.7); }
        50% { box-shadow: 0 0 0 20px rgba(255, 0, 0, 0.28); }
        100% { box-shadow: 0 0 0 0 rgba(255, 140, 0, 0); }

    }
    

.shiny-bg {
    position: relative;
    overflow: hidden;
    background: linear-gradient(120deg, #0e8216, #2d8916, #297b20);
}

.shiny-bg::before {
    content: "";
    position: absolute;
    top: 0;
    left: -150%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.4) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    transform: skewX(-25deg);
    animation: shine 2.5s infinite;
}

.shiny-tag {
    position: relative;
    overflow: hidden;
}

.shiny-tag::before {
    content: "";
    position: absolute;
    top: 0;
    left: -150%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.4) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    transform: skewX(-25deg);
    animation: shine 2.5s infinite;
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
        rgba(55, 255, 0, 0.4) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    transform: skewX(-25deg);
    animation: shine 2s infinite;
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