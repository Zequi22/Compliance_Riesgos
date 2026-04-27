<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskIndicator extends Model
{
    protected $fillable = [
        'risk_id',
        'name',
        'target_value',
        'current_value',
        'tolerance_level',
        'last_measured_at',
        'history',
    ];

    protected $casts = [
        'last_measured_at' => 'datetime',
        'history' => 'array',
    ];

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }
}
