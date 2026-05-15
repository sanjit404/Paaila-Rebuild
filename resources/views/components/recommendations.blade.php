@props([
    'recommendations',          // Collection from RecommendationService
    'title'       => 'Treks You May Like',
    'subtitle'    => null,
    'showReason'  => true,
    'reasonKey'   => 'reason',  // key on each item that holds reason string
    'reasonIcon'  => 'fa-lightbulb',
    'reasonColor' => 'var(--color-success)',
    'reasonBg'    => '#E8F5E9',
    'limit'       => 6,
])

@if($recommendations->isNotEmpty())
<section style="padding: var(--space-2xl) 0;">
    <div class="container">

            <div>
                <h2 style="font-size: 22px; font-weight: 700; margin-bottom: var(--space-xs); display: flex; align-items: center; gap: var(--space-sm);">
                    {{ $title }}
                </h2>
                <p style="color: var(--color-text-light); font-size: 14px; margin: 0;">
                    {{ $subtitle ?? ($showReason ? 'Personalised based on your preferences and history' : 'Explore these treks') }}
                </p>
                <br>
            </div>
            

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: var(--space-xl);">

            @foreach($recommendations->take($limit) as $item)

            @php
                /*
                 * Items can come in two shapes:
                 *  A) ['package' => TourPackage, 'reason' => '...', ...]  ← from getForUser()
                 *  B) TourPackage directly  ← from getPopularNow(), getTrending(), getSimilar(), getLikedEarlier()
                 */
                $package = is_array($item) ? $item['package'] : $item;
                $reason  = is_array($item)
                    ? ($item[$reasonKey] ?? null)
                    : ($package->{$reasonKey} ?? null);
            @endphp

            <div class="rec-card"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)';"
                 onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';">

                    <a href="{{ route('tours.show', $package) }}" style="display: block; text-decoration: none;">

                <div style="height: 185px; position: relative; overflow: hidden; border-radius: var(--radius-lg) var(--radius-lg) 0 0;
                            background: linear-gradient(135deg, var(--color-primary) 0%, #2E7D32 100%);">

                     @if($package->image == null)
            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-mountain" style="font-size: 56px; color: rgba(255,255,255,0.25);"></i>
                </div>
                
            @else
                <img src="{{ $package->image }}"
                     alt="{{ $package->name }}"
                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                     onmouseover="this.style.transform='scale(1.04)'"
                     onmouseout="this.style.transform='scale(1)'">
            @endif

                    <div style="position: absolute; top: 10px; left: 10px;">
                        <span style="background: rgba(0,0,0,0.55); color: white; padding: 3px 10px;
                                     border-radius: 20px; font-size: 11px; font-weight: 600;
                                     text-transform: capitalize; backdrop-filter: blur(4px);">
                            {{ $package->difficulty_level }}
                        </span>
                    </div>

                    @if($package->trek_type ?? false)
                    <div style="position: absolute; top: 10px; right: 10px;">
                        <span style="background: rgba(255,255,255,0.9); color: var(--color-primary);
                                     padding: 3px 10px; border-radius: 20px; font-size: 11px;
                                     font-weight: 600; text-transform: capitalize;">
                            {{ $package->trek_type }}
                        </span>
                    </div>
                    @endif

                    @if(($package->rating_count ?? 0) > 0)
                    <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.6);
                                color: white; padding: 3px 8px; border-radius: 12px;
                                font-size: 12px; font-weight: 600;
                                display: flex; align-items: center; gap: 4px; backdrop-filter: blur(4px);">
                        <i class="fas fa-star" style="color: #FFC107; font-size: 11px;"></i>
                        {{ number_format($package->rating_avg, 1) }}
                        <span style="opacity: 0.75; font-size: 11px;">({{ $package->rating_count }})</span>
                    </div>
                    @endif
                </div>
                </a>
                <div style="padding: var(--space-lg);">

                    @if($showReason && $reason && is_string($reason))
                    <div style="display: inline-flex; align-items: center; gap: 5px;
                                background: {{ $reasonBg }}; color: {{ $reasonColor }};
                                padding: 4px 10px; border-radius: 20px;
                                font-size: 11px; font-weight: 600; margin-bottom: var(--space-md);">
                        <i class="fas {{ $reasonIcon }}" style="font-size: 10px;"></i>
                        {{ $reason }}
                    </div>
                    @endif

                    <h3 style="font-size: 15px; font-weight: 700; margin-bottom: var(--space-sm); color: var(--color-text); line-height: 1.3;">
                        {{ $package->name }}
                    </h3>

                    <p style="font-size: 13px; color: var(--color-text-light); margin-bottom: var(--space-md); line-height: 1.5;">
                        {{ Str::limit($package->description, 75) }}
                    </p>

                    <div style="display: flex; gap: var(--space-md); font-size: 12px;
                                color: var(--color-text-light); margin-bottom: var(--space-lg);
                                flex-wrap: wrap;">
                        <span><i class="fas fa-calendar"></i> {{ $package->duration_days }} {{ Str::plural('day', $package->duration_days) }}</span>
                        <span><i class="fas fa-users"></i> Max {{ $package->max_participants }}</span>
                        @if($package->region ?? false)
                            <span><i class="fas fa-map-pin"></i> {{ $package->region }}</span>
                        @endif
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 18px; font-weight: 700; color: var(--color-primary);">
                                Rs. {{ number_format($package->price, 0) }}
                            </div>
                            <div style="font-size: 11px; color: var(--color-text-light);">per person</div>
                        </div>
                        <a href="{{ route('tours.show', $package) }}" class="btn btn-primary btn-sm">
                            View Trek <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            @endforeach
        </div>

    </div>
</section>

@push('styles')
<style>
    .rec-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
</style>
@endpush

@endif
