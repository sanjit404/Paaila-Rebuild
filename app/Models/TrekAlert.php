<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrekAlert extends Model
{
    protected $fillable = [
        'tour_package_id',
        'checkpoint_id',
        'title',
        'description',
        'severity',
        'status',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TrekAlertUpdate::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'monitoring']);
    }

    public function getSeverityIconAttribute(): string
    {
        return match ($this->severity) {
            'danger'  => 'fa-triangle-exclamation',
            'warning' => 'fa-exclamation-circle',
            default   => 'fa-circle-info',
        };
    }
}