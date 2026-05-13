{{--
    USAGE:
    <x-package-card :package="$package" />
    <x-package-card :package="$package" :show-score="false" />
--}}

@props(['package', 'showScore' => false])

<div class="pkg-card"
     onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)';"
     onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';">

    <a href="{{ route('tours.show', $package) }}" style="display: block; text-decoration: none;">
        <div style="height: 190px; position: relative; overflow: hidden;
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
                @php
                    $diffColor = match($package->difficulty_level) {
                        'easy'     => '#2E7D32',
                        'moderate' => '#F57C00',
                        'hard'     => '#D32F2F',
                        default    => '#546E7A',
                    };
                @endphp
                <span style="background: rgba(0,0,0,0.55); color: white;
                             padding: 3px 10px; border-radius: 20px;
                             font-size: 11px; font-weight: 600;
                             text-transform: capitalize;
                             backdrop-filter: blur(4px);">
                    {{ $package->difficulty_level }}
                </span>
            </div>

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

            @if(($package->rating_count ?? 0) > 0)
                <div style="position: absolute; bottom: 10px; right: 10px;
                             background: rgba(0,0,0,0.6); color: white;
                             padding: 3px 9px; border-radius: 12px;
                             font-size: 12px; font-weight: 600;
                             display: flex; align-items: center; gap: 4px;
                             backdrop-filter: blur(4px);">
                    <i class="fas fa-star" style="color: #FFC107; font-size: 11px;"></i>
                    {{ number_format($package->rating_avg, 1) }}
                    <span style="opacity: 0.75; font-size: 11px;">({{ $package->rating_count }})</span>
                </div>
            @else
                <div style="position: absolute; bottom: 10px; right: 10px;
                             background: rgba(0,0,0,0.45); color: rgba(255,255,255,0.8);
                             padding: 3px 9px; border-radius: 12px;
                             font-size: 11px; backdrop-filter: blur(4px);">
                    No ratings yet
                </div>
            @endif

            <div style="position: absolute; bottom: 10px; left: 10px;
                         background: rgba(0,0,0,0.55); color: white;
                         padding: 3px 9px; border-radius: 12px;
                         font-size: 11px; font-weight: 600;
                         backdrop-filter: blur(4px);">
                <i class="fas fa-calendar" style="font-size: 10px;"></i>
                {{ $package->duration_days }} {{ Str::plural('day', $package->duration_days) }}
            </div>
        </div>

    <div style="padding: var(--space-lg);">

        @if($package->region ?? false)
            <div style="font-size: 12px; color: var(--color-text-light);
                         margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                <i class="fas fa-map-pin" style="font-size: 10px;"></i>
                {{ $package->region }}
            </div>
        @endif

        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: var(--space-sm);
                    color: var(--color-text); line-height: 1.3;">
                {{ $package->name }}
        </h3>

        <p style="font-size: 13px; color: var(--color-text-light);
                    margin-bottom: var(--space-md); line-height: 1.5;">
            {{ Str::limit($package->description, 85) }}
        </p>

            @if(!empty($package->tags))
                @php
                    $rawTags = is_string($package->tags)
                        ? json_decode($package->tags, true)
                        : $package->tags;

                    $tags = collect($rawTags ?? [])
                        ->filter()
                        ->take(3)
                        ->map(function ($tag) {
                            return '#' . ltrim(trim($tag), '#');
                        });
                @endphp

                @if($tags->isNotEmpty())
                    <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: var(--space-md); align-items: center;">
                        @foreach($tags as $tag)
                            <span style="background: #F5F5F5; color: var(--color-text-light);
                                        padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif
            @endif


            @if(!empty($package->season))
                @php
                    $rawSeason = is_string($package->season)
                        ? json_decode($package->season, true)
                        : $package->season;

                    $seasonText = collect($rawSeason ?? [])
                        ->filter()
                        ->map(function ($item) {
                            return ucfirst(trim($item));
                        })
                        ->implode(', ');
                @endphp

                @if(!empty($seasonText))
                    <div style="font-size: 12px; color: var(--color-text-light); margin-bottom: var(--space-md);">
                        <i class="fas fa-sun" style="color: #FFA000;"></i>
                        <strong>Best time to visit:</strong> {{ $seasonText }}
                    </div>
                @endif
            @endif

        <div style="display: flex; align-items: center; justify-content: space-between;
                     padding-top: var(--space-md); border-top: 1px solid #F0F0F0;">
            <div>
                <div style="font-size: 20px; font-weight: 700; color: var(--color-primary);">
                    Rs. {{ number_format($package->price, 0) }}
                </div>
                <div style="font-size: 11px; color: var(--color-text-light);">per person</div>
            </div>
           
        </div>
    </div>
    </a>
</div>

@once
@push('styles')
<style>
    .pkg-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
</style>
@endpush
@endonce
