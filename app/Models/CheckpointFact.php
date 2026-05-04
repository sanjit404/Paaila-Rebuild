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
        'type',        // historical | cultural | natural | safety | tip | info
        'icon_class',  // FontAwesome class, e.g. "fas fa-landmark"
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Resolve icon class — use stored value or fall back to type defaults.
     */
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

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
