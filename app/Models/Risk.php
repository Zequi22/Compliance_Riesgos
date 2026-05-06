<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Risk extends Model
{
    public const STATUS_IDENTIFICADO = 'Identificado';

    public const STATUS_EVALUADO = 'Evaluado';

    public const STATUS_TRATAMIENTO = 'En tratamiento';

    public const STATUS_SEGUIMIENTO = 'En seguimiento';

    public const STATUS_CERRADO = 'Cerrado / Revisado';

    public const TREATMENT_ACEPTAR = 'aceptar';

    public const TREATMENT_EVITAR = 'evitar';

    public const TREATMENT_REDUCIR = 'reducir';

    public const TREATMENT_TRANSFERIR = 'transferir';

    protected $fillable = [
        'name',
        'description',
        'category',
        'organizational_unit_id',
        'responsable_id',
        'type_crime',
        'status',
        'treatment',
        'last_review_at',
        'next_review_at',
    ];

    protected $casts = [
        'last_review_at' => 'date',
        'next_review_at' => 'date',
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function controls(): HasMany
    {
        return $this->hasMany(Control::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(RiskIndicator::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RiskDocument::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    protected function criticality(): Attribute
    {
        return Attribute::make(
            get: function () {
                $assessments = $this->assessments;

                if ($assessments->isEmpty()) {
                    return 'No Evaluado';
                }

                // Filtrar residual y luego inherente
                $residual = $assessments->where('type', 'residual')->sortByDesc('assessed_at')->first();
                if ($residual) {
                    $score = $residual->score;
                } else {
                    $inherent = $assessments->where('type', 'inherent')->sortByDesc('assessed_at')->first();
                    $score = $inherent ? $inherent->score : null;
                }

                if (! $score) {
                    return 'No Evaluado';
                }

                if ($score <= 2) {
                    return 'Muy Bajo';
                }
                if ($score <= 4) {
                    return 'Bajo';
                }
                if ($score <= 9) {
                    return 'Medio';
                }
                if ($score <= 14) {
                    return 'Alto';
                }

                return 'Crítico';
            }
        );
    }

    protected function criticalityColor(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->criticality) {
                    'Muy Bajo' => 'success',
                    'Bajo' => 'info',
                    'Medio' => 'warning',
                    'Alto' => 'danger',
                    'Crítico' => 'danger',
                    default => 'gray',
                };
            }
        );
    }

    public function isReviewOverdue(): bool
    {
        return $this->next_review_at && $this->next_review_at->isPast();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(RiskStatusHistory::class);
    }

    protected static function booted()
    {
        static::updated(function (Risk $risk) {
            if ($risk->isDirty('status')) {
                RiskStatusHistory::create([
                    'risk_id' => $risk->id,
                    'old_status' => $risk->getOriginal('status'),
                    'new_status' => $risk->status,
                    'changed_by' => auth()->id(),
                ]);
            }
        });
    }

    public function getAverageControlEffectiveness(): int
    {
        $controls = $this->controls;
        if ($controls->isEmpty()) {
            return 0;
        }

        $totalScore = 0;
        $count = $controls->count();

        foreach ($controls as $control) {
            $score = match ($control->effectiveness) {
                'Suficiente' => 100,
                'Medio' => 50,
                'Insuficiente' => 0,
                default => 0,
            };
            $totalScore += $score;
        }

        return (int) round($totalScore / $count);
    }
}
