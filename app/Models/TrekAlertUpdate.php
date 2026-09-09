<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrekAlertUpdate extends Model
{
    protected $fillable = ['trek_alert_id', 'message'];

    public function trekAlert(): BelongsTo
    {
        return $this->belongsTo(TrekAlert::class);
    }
}