<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'trek_types',
        'difficulty',
        'duration',
        'budget',
        'group_size',
        'preferred_seasons',
        'preferences_set',
    ];

    protected $casts = [
        'trek_types'        => 'array',
        'preferred_seasons' => 'array',
        'preferences_set'   => 'boolean',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getBudgetRangeAttribute(): array
    {
        return match($this->budget) {
            'budget'  => [0,     5000],
            'mid'     => [5001,  15000],
            'premium' => [15001, 999999],
            default   => [0,     999999],
        };
    }

    public function getDurationRangeAttribute(): array
    {
        return match($this->duration) {
            '1-3'   => [1,  3],
            '4-7'   => [4,  7],
            '8-14'  => [8,  14],
            default => [1,  99],
        };
    }


    public static function hasPreferences(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('preferences_set', true)
            ->exists();
    }

    public static function forUser(int $userId): ?self
    {
        return self::where('user_id', $userId)->first();
    }


    public static function trekTypeOptions(): array
    {
        return [
            ['value' => 'nature',     'label' => 'Nature & Wildlife',  'icon' => 'fa-tree',          'color' => '#2E7D32', 'bg' => '#E8F5E9', 'desc' => 'Forests, rivers, animals'],
            ['value' => 'historical', 'label' => 'Historical',         'icon' => 'fa-landmark',      'color' => '#1565C0', 'bg' => '#E3F2FD', 'desc' => 'Ancient sites & ruins'],
            ['value' => 'cultural',   'label' => 'Cultural',           'icon' => 'fa-theater-masks', 'color' => '#6A1B9A', 'bg' => '#F3E5F5', 'desc' => 'Villages & traditions'],
            ['value' => 'adventure',  'label' => 'Adventure',          'icon' => 'fa-mountain',      'color' => '#E65100', 'bg' => '#FFF3E0', 'desc' => 'High altitude & peaks'],
            ['value' => 'spiritual',  'label' => 'Spiritual',          'icon' => 'fa-om',            'color' => '#F57C00', 'bg' => '#FFF8E1', 'desc' => 'Temples & monasteries'],
            ['value' => 'scenic',     'label' => 'Scenic Views',       'icon' => 'fa-eye',           'color' => '#00838F', 'bg' => '#E0F7FA', 'desc' => 'Valleys & viewpoints'],
            ['value' => 'wildlife',   'label' => 'Wildlife Safari',    'icon' => 'fa-paw',           'color' => '#558B2F', 'bg' => '#F1F8E9', 'desc' => 'National parks'],
            ['value' => 'village',    'label' => 'Village Life',       'icon' => 'fa-home',          'color' => '#4527A0', 'bg' => '#EDE7F6', 'desc' => 'Rural culture & food'],
        ];
    }

    public static function difficultyOptions(): array
    {
        return [
            ['value' => 'easy',     'label' => 'Easy',     'icon' => 'fa-walking',  'desc' => 'Flat paths, short walks',    'color' => '#2E7D32'],
            ['value' => 'moderate', 'label' => 'Moderate', 'icon' => 'fa-hiking',   'desc' => 'Some hills, half-day treks', 'color' => '#F57C00'],
            ['value' => 'hard',     'label' => 'Hard',     'icon' => 'fa-mountain', 'desc' => 'Steep terrain, full day',    'color' => '#D32F2F'],
            ['value' => 'any',      'label' => 'Any',      'icon' => 'fa-infinity', 'desc' => 'Surprise me!',              'color' => '#546E7A'],
        ];
    }

    public static function durationOptions(): array
    {
        return [
            ['value' => '1-3',  'label' => '1–3 Days',  'icon' => 'fa-sun',      'desc' => 'Quick weekend getaway'],
            ['value' => '4-7',  'label' => '4–7 Days',  'icon' => 'fa-calendar', 'desc' => 'A proper week trek'],
            ['value' => '8-14', 'label' => '8–14 Days', 'icon' => 'fa-route',    'desc' => 'Extended adventure'],
            ['value' => 'any',  'label' => 'Any',       'icon' => 'fa-infinity', 'desc' => 'Flexible'],
        ];
    }

    public static function budgetOptions(): array
    {
        return [
            ['value' => 'budget',  'label' => 'Budget',    'desc' => 'Under Rs. 5,000',     'icon' => 'fa-piggy-bank', 'color' => '#2E7D32'],
            ['value' => 'mid',     'label' => 'Mid-range', 'desc' => 'Rs. 5,000 – 15,000', 'icon' => 'fa-wallet',     'color' => '#1565C0'],
            ['value' => 'premium', 'label' => 'Premium',   'desc' => 'Above Rs. 15,000',    'icon' => 'fa-crown',      'color' => '#F57C00'],
            ['value' => 'any',     'label' => 'Flexible',  'desc' => 'No limit',            'icon' => 'fa-infinity',   'color' => '#546E7A'],
        ];
    }
}
