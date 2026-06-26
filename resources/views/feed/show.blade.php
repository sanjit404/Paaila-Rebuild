@extends('layouts.app')

@section('title', $post->title)

@section('content')
<section class="section" style="background: var(--color-bg);">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="margin-bottom: var(--space-lg);">
                <a href="{{ route('feed.index') }}" style="color: var(--color-text-light); text-decoration: none; font-size: 14px;">
                    <i class="fas fa-arrow-left"></i> Back to Feed
                </a>
            </div>

            <div class="card">
                @if($post->image)
                    <img 
                        src="{{ $post->image }}" 
                        alt="{{ $post->title }}"
                        style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg) var(--radius-lg) 0 0;"
                    >
                @endif

                <div class="card-body" style="padding: var(--space-xl);">
                    <div style="margin-bottom: var(--space-md);">
                        @if($post->type === 'trek')
                            <span class="badge badge-primary">
                                <i class="fas fa-mountain"></i> TREK
                            </span>
                        @elseif($post->type === 'offer')
                            <span class="badge badge-warning">
                                <i class="fas fa-tag"></i> SPECIAL OFFER
                            </span>
                        @else
                            <span class="badge" style="background: #E3F2FD; color: #1976D2;">
                                <i class="fas fa-newspaper"></i> NEWS
                            </span>
                        @endif
                    </div>

                    <h1 style="font-size: 32px; font-weight: 700; margin-bottom: var(--space-lg); color: var(--color-text);">
                        {{ $post->title }}
                    </h1>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-md) 0; border-top: 1px solid #E0E0E0; border-bottom: 1px solid #E0E0E0; margin-bottom: var(--space-xl);">
                        <button 
                            onclick="toggleLike({{ $post->id }})"
                            id="likeBtn"
                            class="btn btn-secondary"
                            style="border: none; background: {{ $hasLiked ? '#FFEBEE' : '#F5F5F5' }}; color: {{ $hasLiked ? '#E53935' : 'var(--color-text)' }};"
                        >
                            <i class="fas fa-heart"></i>
                            <span id="likesCount">{{ $post->likes_count }}</span> Likes
                        </button>

                        
                    </div>

                    <div style="font-size: 16px; line-height: 1.8; color: var(--color-text); margin-bottom: var(--space-xl);">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    @if($post->trek)
                        <div style="padding: var(--space-xl); background: #E8F5E9; border-radius: var(--radius-md); border-left: 4px solid var(--color-primary);">
                            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: var(--space-md); color: var(--color-text);">
                                <i class="fas fa-hiking"></i> Related Trek Package
                            </h3>
                            <div class="flex-between" style="flex-wrap: wrap; gap: var(--space-md);">
                                <div>
                                    <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">{{ $post->trek->name }}</div>
                                    <div style="font-size: 14px; color: var(--color-text-light);">
                                        {{ $post->trek->duration_days }} days • Rs. {{ number_format($post->trek->price, 0) }}
                                    </div>
                                </div>
                                <div class="flex gap-sm">
                                    <a href="{{ route('tours.show', $post->trek) }}" class="btn btn-primary">
                                        View Trek
                                    </a>
                                    <a href="{{ route('bookings.create', $post->trek) }}" class="shiny-tbg btn btn-cta" style="background:var(--color-primary-dark);">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    const identifier = '{{ $identifier }}';
    let hasLiked = {{ $hasLiked ? 'true' : 'false' }};

    async function toggleLike(postId) {
        try {
            const response = await fetch(`/feed/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                hasLiked = data.action === 'liked';
                
                const btn = document.getElementById('likeBtn');
                btn.style.background = hasLiked ? '#FFEBEE' : '#F5F5F5';
                btn.style.color = hasLiked ? '#E53935' : 'var(--color-text)';
                
                document.getElementById('likesCount').textContent = data.likes_count;
            }
        } catch (error) {
            console.error('Like error:', error);
        }
    }

    
</script>

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
        rgba(255, 255, 255, 0.4) 50%,
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
@endpush
@endsection