<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'image',
        'likes_count',
        'rating_avg',
        'rating_count',
        'is_highlighted',
        'trek_id',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
        'likes_count' => 'integer',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
    ];

    
    public function trek(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'trek_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    
    public function scopeTrending($query)
    {
        return $query->orderBy('likes_count', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    public function scopeHighlighted($query)
    {
        return $query->where('is_highlighted', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    
    public function isLikedBy(string $identifier): bool
    {
        return $this->likes()->where('identifier', $identifier)->exists();
    }

    
    public function getRatingBy(string $identifier): ?int
    {
        $rating = $this->ratings()->where('identifier', $identifier)->first();
        return $rating ? $rating->rating : null;
    }

   
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    
    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }

   
    public function recalculateRating(): void
    {
        $ratings = $this->ratings()->get();
        
        if ($ratings->isEmpty()) {
            $this->update([
                'rating_avg' => 0,
                'rating_count' => 0,
            ]);
            return;
        }

        $totalRating = $ratings->sum('rating');
        $count = $ratings->count();

        $this->update([
            'rating_avg' => round($totalRating / $count, 2),
            'rating_count' => $count,
        ]);
    }
}