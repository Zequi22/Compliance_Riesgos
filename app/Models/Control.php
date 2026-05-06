<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Risk;

class Control extends Model
{
    protected $fillable = [
        'risk_id',
        'type',
        'frequency',
        'effectiveness',
        'title',
        'description',
        'responsable_id',
        'due_date',
        'organizational_unit_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /** ¿El control está vencido? */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /** ¿El control vence en los próximos N días? */
    public function isDueSoon(int $days = 15): bool
    {
        return $this->due_date
            && !$this->due_date->isPast()
            && $this->due_date->diffInDays(Carbon::today()) <= $days;
    }


    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiskDocument::class, 'control_id');
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }
}
