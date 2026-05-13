<?php

namespace App\Services;

use App\Models\TourPackage;
use App\Models\TourBooking;
use App\Models\TrekRating;
use App\Models\UserPreference;
use App\Models\PackageRecommendationScore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
 

    const WEIGHT_PREFERENCE = 0.45;
    const WEIGHT_POPULARITY = 0.30;
    const WEIGHT_BEHAVIORAL = 0.25;



    public static function getForUser(int $userId, int $limit = 6): Collection
    {
        $cacheKey = "recommendations.user.{$userId}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($userId, $limit) {
            return self::calculate($userId, $limit);
        });
    }

  

    public static function getPopularNow(int $limit = 6): Collection
    {
        return Cache::remember('recommendations.popular_now', now()->addHours(3), function () use ($limit) {

            return TourPackage::where('is_active', true)
                ->where('rating_count', '>=', 1) // must have at least one real rating
                ->withCount([
                    'bookings as recent_bookings' => fn($q) =>
                        $q->where('created_at', '>=', now()->subDays(60))
                          ->whereIn('status', ['confirmed', 'active', 'completed']),
                ])
                ->get()
                ->map(function ($package) {
                    $C = 5;   // confidence factor
                    $m = 3.5; // global mean prior

                    $bayesian     = (($C * $m) + ($package->rating_count * $package->rating_avg))
                                  / ($C + $package->rating_count);

                    $ratingScore  = $bayesian / 5.0;
                    $bookingScore = min($package->recent_bookings / 20, 1.0);

                    $package->popular_score = round(
                        ($ratingScore  * 0.60) +
                        ($bookingScore * 0.40),
                        4
                    );

                    return $package;
                })
                ->sortByDesc('popular_score')
                ->take($limit)
                ->values();
        });
    }

   

    public static function getLikedEarlier(int $userId, int $limit = 4): Collection
    {
        $cacheKey = "recommendations.liked_earlier.{$userId}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($userId, $limit) {

            // Step 1: Get high-rated treks
            $highRatings = TrekRating::where('user_id', $userId)
                ->where('rating', '>=', 4)
                ->with('tourPackage:id,trek_type,tags')
                ->get();

            if ($highRatings->isEmpty()) {
                return collect();
            }

            // Step 2: Build weighted frequency maps
            // Star rating is used as weight so 5-star matters more than 4-star
            $likedTypes = [];
            $likedTags  = [];

            $bookedIds = TourBooking::where('user_id', $userId)
                ->pluck('tour_package_id')
                ->toArray();

            foreach ($highRatings as $rating) {
                $pkg    = $rating->tourPackage;
                $weight = $rating->rating; // 4 or 5

                if (!$pkg) continue;

                if ($pkg->trek_type) {
                    $likedTypes[$pkg->trek_type] = ($likedTypes[$pkg->trek_type] ?? 0) + $weight;
                }

                foreach ((array) ($pkg->tags ?? []) as $tag) {
                    $likedTags[$tag] = ($likedTags[$tag] ?? 0) + 1;
                }
            }

            if (empty($likedTypes) && empty($likedTags)) {
                return collect();
            }

            arsort($likedTypes);
            arsort($likedTags);

            $topTypes = array_slice(array_keys($likedTypes), 0, 3);
            $topTags  = array_slice(array_keys($likedTags),  0, 5);

            // Step 3: Find unbooked candidates
            $candidates = TourPackage::where('is_active', true)
                ->whereNotIn('id', $bookedIds)
                ->where(function ($q) use ($topTypes, $topTags) {
                    if (!empty($topTypes)) {
                        $q->whereIn('trek_type', $topTypes);
                    }
                    foreach ($topTags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                })
                ->get();

            if ($candidates->isEmpty()) {
                return collect();
            }

            $maxTypeWeight = !empty($likedTypes) ? max(array_values($likedTypes)) : 1;
            $C = 5; $m = 3.5; // Bayesian params

            // Step 4: Score each candidate
            return $candidates->map(function ($package) use (
                $likedTypes, $likedTags, $topTags, $maxTypeWeight, $C, $m
            ) {
                // Type match score (how often + how highly was this type rated?)
                $typeWeight = $likedTypes[$package->trek_type] ?? 0;
                $typeScore  = $typeWeight / ($maxTypeWeight * 5); // normalised 0-1

                // Tag overlap score (Jaccard-style)
                $pkgTags    = (array) ($package->tags ?? []);
                $tagOverlap = count(array_intersect($pkgTags, $topTags));
                $tagScore   = $tagOverlap > 0
                    ? min($tagOverlap / max(count($topTags), 1), 1.0)
                    : 0;

                // Package quality score (Bayesian)
                $n            = $package->rating_count;
                $R            = $package->rating_avg;
                $bayesian     = (($C * $m) + ($n * $R)) / ($C + $n);
                $qualityScore = $bayesian / 5.0;

                $package->liked_score  = round(
                    ($typeScore   * 0.45) +
                    ($tagScore    * 0.25) +
                    ($qualityScore * 0.30),
                    4
                );

                $package->liked_reason = self::buildLikedReason(
                    $package, $likedTypes, $topTags
                );

                return $package;
            })
            ->sortByDesc('liked_score')
            ->take($limit)
            ->values();
        });
    }

 

    public static function getTrending(int $limit = 6): Collection
    {
        return Cache::remember('recommendations.trending', now()->addHours(2), function () use ($limit) {
            return TourPackage::where('is_active', true)
                ->withCount([
                    'bookings as bookings_30d' => fn($q) =>
                        $q->where('created_at', '>=', now()->subDays(30))
                          ->whereIn('status', ['confirmed', 'active', 'completed']),
                ])
                ->orderByDesc('bookings_30d')
                ->orderByDesc('rating_avg')
                ->orderByDesc('rating_count')
                ->limit($limit)
                ->get();
        });
    }

  

    public static function getSimilar(TourPackage $package, int $limit = 4): Collection
    {
        $cacheKey = "recommendations.similar.{$package->id}";

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($package, $limit) {
            return TourPackage::where('is_active', true)
                ->where('id', '!=', $package->id)
                ->get()
                ->map(fn($candidate) => [
                    'package' => $candidate,
                    'score'   => self::contentSimilarity($package, $candidate),
                ])
                ->sortByDesc('score')
                ->take($limit)
                ->pluck('package')
                ->values();
        });
    }

    

    public static function bust(int $userId): void
    {
        Cache::forget("recommendations.user.{$userId}");
        Cache::forget("recommendations.liked_earlier.{$userId}");

        Log::info('Recommendation cache busted', ['user_id' => $userId]);
    }

    public static function bustPopular(): void
    {
        Cache::forget('recommendations.popular_now');
        Cache::forget('recommendations.trending');
    }

    public static function bustSimilar(int $packageId): void
    {
        Cache::forget("recommendations.similar.{$packageId}");
    }


    private static function calculate(int $userId, int $limit): Collection
    {
        $preferences = UserPreference::forUser($userId);

        $bookings = TourBooking::where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->with('tourPackage')
            ->get();

        // Exclude packages the user has already booked
        $bookedIds = $bookings->pluck('tour_package_id')->toArray();

        $packages = TourPackage::where('is_active', true)
            ->whereNotIn('id', $bookedIds)
            ->get();

        if ($packages->isEmpty()) {
            return collect();
        }

        // Compute popularity scores in one pass (avoids N+1 queries)
        $popularityScores = self::computePopularityScores($packages);

        $scored = $packages->map(function ($package) use (
            $userId, $preferences, $bookings, $popularityScores
        ) {
            $prefScore = $preferences
                ? self::computePreferenceScore($package, $preferences)
                : 0.5; // neutral for users who skipped preference setup

            $popScore = $popularityScores[$package->id] ?? 0;

            $behScore = $bookings->isNotEmpty()
                ? self::computeBehavioralScore($package, $bookings, $userId)
                : 0;

            $finalScore = (self::WEIGHT_PREFERENCE * $prefScore)
                        + (self::WEIGHT_POPULARITY  * $popScore)
                        + (self::WEIGHT_BEHAVIORAL  * $behScore);

            return [
                'package'          => $package,
                'final_score'      => round($finalScore, 4),
                'preference_score' => round($prefScore,  4),
                'popularity_score' => round($popScore,   4),
                'behavioral_score' => round($behScore,   4),
                'reason'           => self::buildReason(
                    $prefScore, $popScore, $behScore, $package, $preferences
                ),
            ];
        });

        // Persist scores for analytics / admin review
        self::persistScores($userId, $scored);

        return $scored
            ->sortByDesc('final_score')
            ->take($limit)
            ->values();
    }

   

    private static function computePreferenceScore(TourPackage $p, UserPreference $pref): float
    {
        $score  = 0.0;
        $weight = 0.0;

        // Trek type match (35%)
        if ($pref->trek_types && count($pref->trek_types) > 0) {
            $pkgTypes = array_merge(
                (array) ($p->tags ?? []),
                $p->trek_type ? [$p->trek_type] : []
            );
            $overlap = count(array_intersect($pref->trek_types, $pkgTypes));
            $score  += (min($overlap / max(count($pref->trek_types), 1), 1.0)) * 0.35;
            $weight += 0.35;
        }

        // Difficulty match (25%)
        if ($pref->difficulty !== 'any') {
            $score  += ($p->difficulty_level === $pref->difficulty ? 1.0 : 0.0) * 0.25;
            $weight += 0.25;
        }

        // Duration match (20%) — partial score for close misses
        if ($pref->duration !== 'any') {
            [$mn, $mx] = $pref->duration_range;
            $midpoint  = ($mn + $mx) / 2;
            $inRange   = $p->duration_days >= $mn && $p->duration_days <= $mx;
            $durScore  = $inRange
                ? 1.0
                : max(0, 1 - abs($p->duration_days - $midpoint) / 10);
            $score  += $durScore * 0.20;
            $weight += 0.20;
        }

        // Budget match (20%) — partial score if slightly out of budget
        if ($pref->budget !== 'any') {
            [$mn, $mx] = $pref->budget_range;
            $score  += ($p->price >= $mn && $p->price <= $mx ? 1.0 : 0.15) * 0.20;
            $weight += 0.20;
        }

        // Normalise so users who skipped criteria aren't penalised
        return $weight > 0 ? min($score / $weight, 1.0) : 0.5;
    }

    

    private static function computePopularityScores(Collection $packages): array
    {
        $ids = $packages->pluck('id')->toArray();

        // Recent booking counts (last 90 days)
        $bookingCounts = DB::table('tour_bookings')
            ->selectRaw('tour_package_id, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subDays(90))
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->whereIn('tour_package_id', $ids)
            ->groupBy('tour_package_id')
            ->pluck('cnt', 'tour_package_id')
            ->toArray();

        $maxBookings = max(array_values($bookingCounts) ?: [1]);
        $C = 5; $m = 3.5; // Bayesian prior params
        $scores = [];

        foreach ($packages as $package) {
            $bookingScore = ($bookingCounts[$package->id] ?? 0) / $maxBookings;

            // Bayesian rating from trek_ratings table
            $n           = $package->rating_count;
            $R           = $package->rating_avg;
            $bayesian    = (($C * $m) + ($n * $R)) / ($C + $n);
            $ratingScore = $bayesian / 5.0;

            $scores[$package->id] = round(
                ($ratingScore  * 0.55) +
                ($bookingScore * 0.45),
                4
            );
        }

        return $scores;
    }

    

    private static function computeBehavioralScore(
        TourPackage $package,
        Collection  $bookings,
        int         $userId
    ): float {
        if ($bookings->isEmpty()) return 0;
        $score = 0.0;

        // Signal 1: Trek types the user rated >= 4 stars (45%)
        // This is the strongest signal — they told us with their ratings
        $highRatedTypes = TrekRating::where('user_id', $userId)
            ->where('rating', '>=', 4)
            ->with('tourPackage:id,trek_type')
            ->get()
            ->pluck('tourPackage.trek_type')
            ->filter()
            ->countBy()
            ->toArray();

        if (!empty($highRatedTypes) && $package->trek_type) {
            $maxCount = max(array_values($highRatedTypes));
            $score   += (($highRatedTypes[$package->trek_type] ?? 0) / $maxCount) * 0.45;
        }

        // Signal 2: Trek types from past bookings (30%)
        $bookedTypes = $bookings
            ->pluck('tourPackage.trek_type')
            ->filter()
            ->countBy()
            ->toArray();

        if (!empty($bookedTypes) && $package->trek_type) {
            $maxCount = max(array_values($bookedTypes));
            $score   += (($bookedTypes[$package->trek_type] ?? 0) / $maxCount) * 0.30;
        }

        // Signal 3: Difficulty comfort zone (25%)
        $diffMap = ['easy' => 1, 'moderate' => 2, 'hard' => 3];
        $diffs   = $bookings
            ->pluck('tourPackage.difficulty_level')
            ->filter()
            ->map(fn($d) => $diffMap[$d] ?? 2)
            ->toArray();

        if (!empty($diffs)) {
            $avgDiff = array_sum($diffs) / count($diffs);
            $pkgDiff = $diffMap[$package->difficulty_level] ?? 2;
            $score  += max(0, 1 - abs($avgDiff - $pkgDiff) / 2) * 0.25;
        }

        return min($score, 1.0);
    }

    

    private static function contentSimilarity(TourPackage $a, TourPackage $b): float
    {
        $score = 0.0;

        // Same trek type (35%)
        if ($a->trek_type && $b->trek_type) {
            $score += ($a->trek_type === $b->trek_type) ? 0.35 : 0;
        }

        // Tag Jaccard similarity (25%)
        $tagsA = (array) ($a->tags ?? []);
        $tagsB = (array) ($b->tags ?? []);
        if (!empty($tagsA) && !empty($tagsB)) {
            $intersection = count(array_intersect($tagsA, $tagsB));
            $union        = count(array_unique(array_merge($tagsA, $tagsB)));
            $score       += ($union > 0 ? $intersection / $union : 0) * 0.25;
        }

        // Same difficulty (20%)
        $score += ($a->difficulty_level === $b->difficulty_level) ? 0.20 : 0;

        // Duration proximity (10%)
        $durDiff = abs($a->duration_days - $b->duration_days);
        $score  += max(0, 1 - $durDiff / 7) * 0.10;

        // Price proximity (10%)
        $maxPrice   = max($a->price, $b->price, 1);
        $priceRatio = min($a->price, $b->price) / $maxPrice;
        $score     += $priceRatio * 0.10;

        return min($score, 1.0);
    }

   

    private static function buildLikedReason(
        TourPackage $package,
        array       $likedTypes,
        array       $topTags
    ): string {
        if ($package->trek_type && isset($likedTypes[$package->trek_type])) {
            return 'You liked ' . ucfirst($package->trek_type) . ' treks earlier';
        }

        $pkgTags = (array) ($package->tags ?? []);
        $matched = array_intersect($pkgTags, $topTags);
        if (!empty($matched)) {
            return 'Matches your interest in ' . ucfirst(reset($matched));
        }

        return 'Based on treks you rated highly';
    }

    

    private static function buildReason(
        float        $prefScore,
        float        $popScore,
        float        $behScore,
        TourPackage  $package,
        ?UserPreference $pref
    ): string {
        // Find which signal drove the recommendation most
        $dominant = 'preference';
        $max      = $prefScore;

        if ($popScore > $max) { $dominant = 'popularity'; $max = $popScore; }
        if ($behScore > $max)   $dominant = 'behavioral';

        return match($dominant) {
            'popularity' => 'Trending — popular with trekkers this season',
            'behavioral' => 'Matches treks you have enjoyed',
            default      => ($pref && $pref->trek_types && $package->trek_type &&
                             in_array($package->trek_type, $pref->trek_types))
                            ? 'Matches your interest in ' . ucfirst($package->trek_type) . ' treks'
                            : 'Recommended based on your preferences',
        };
    }

   

    private static function persistScores(int $userId, Collection $scored): void
    {
        try {
            $rows = $scored->map(fn($s) => [
                'user_id'          => $userId,
                'tour_package_id'  => $s['package']->id,
                'preference_score' => $s['preference_score'],
                'popularity_score' => $s['popularity_score'],
                'behavioral_score' => $s['behavioral_score'],
                'final_score'      => $s['final_score'],
                'calculated_at'    => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ])->toArray();

            DB::table('package_recommendation_scores')->upsert(
                $rows,
                ['user_id', 'tour_package_id'],
                ['preference_score', 'popularity_score', 'behavioral_score',
                 'final_score', 'calculated_at', 'updated_at']
            );
        } catch (\Exception $e) {
            Log::warning('Failed to persist recommendation scores', [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
