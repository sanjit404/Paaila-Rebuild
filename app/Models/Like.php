<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    protected $fillable = [
        'post_id',
        'identifier',
    ];

    
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    
    protected static function boot()
    {
        parent::boot();

        static::created(function ($like) {
            $like->post->incrementLikes();
        });

        static::deleted(function ($like) {
            $like->post->decrementLikes();
        });
    }
}