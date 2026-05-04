<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'post_id',
        'identifier',
        'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    
    public static function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
        ];
    }

    
    protected static function boot()
    {
        parent::boot();

        static::created(function ($rating) {
            $rating->post->recalculateRating();
        });

        static::updated(function ($rating) {
            $rating->post->recalculateRating();
        });

        static::deleted(function ($rating) {
            $rating->post->recalculateRating();
        });
    }
}