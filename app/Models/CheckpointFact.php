<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckpointFact extends Model
{
    protected $fillable = [
        'checkpoint_id',
        'title',
        'content',
        'type',        
        'icon_class',  
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];


    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function getIconClassAttribute(?string $value): string
    {
        if ($value) return $value;

        return match($this->type) {
            'historical' => 'fas fa-landmark',
            'cultural'   => 'fas fa-theater-masks',
            'natural'    => 'fas fa-tree',
            'safety'     => 'fas fa-shield-alt',
            'tip'        => 'fas fa-lightbulb',
            default      => 'fas fa-info-circle',
        };
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
