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

                // Prefiere la evaluación residual más reciente; si no existe, usa la inherente
                $score = $assessments->where('type', 'residual')->sortByDesc('assessed_at')->first()?->score
                    ?? $assessments->where('type', 'inherent')->sortByDesc('assessed_at')->first()?->score;

                if (! $score) {
                    return 'No Evaluado';
                }

                // Escala: 1-2 Muy Bajo | 3-4 Bajo | 5-9 Medio | 10-14 Alto | 15+ Crítico
                return match (true) {
                    $score <= 2  => 'Muy Bajo',
                    $score <= 4  => 'Bajo',
                    $score <= 9  => 'Medio',
                    $score <= 14 => 'Alto',
                    default      => 'Crítico',
                };
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
        // Registra automáticamente cada transición de estado en el historial de auditoría
        static::updated(function (Risk $risk) {
            if ($risk->isDirty('status')) {
                RiskStatusHistory::create([
                    'risk_id'    => $risk->id,
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

        // Suficiente = 100 % | Medio = 50 % | Insuficiente = 0 %
        $avg = $controls->avg(fn ($control) => match ($control->effectiveness) {
            'Suficiente' => 100,
            'Medio'      => 50,
            default      => 0,   // Insuficiente o valor desconocido
        });

        return (int) round($avg);
    }
}
