<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    // Desactiva el uso automático de created_at y updated_at
    public $timestamps = false;

    protected $fillable = [
        'risk_id',
        'type',
        'probability',
        'economic_impact',
        'operational_impact',
        'reputational_impact',
        'management_level',
        'impact',
        'score',
        'assessed_at',
    ];

    // Para convertir la fecha que llega en string cambie a formato fecha
    protected $casts = [
        'assessed_at' => 'datetime',
    ];

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Assessment $assessment) {
            // El impacto final es el peor (máximo) de los tres tipos de impacto
            $impacts = array_filter([
                $assessment->economic_impact,
                $assessment->operational_impact,
                $assessment->reputational_impact,
            ]);

            if (! empty($impacts)) {
                $assessment->impact = max($impacts);
            }

            if (! isset($assessment->probability, $assessment->impact)) {
                return;
            }

            // Puntuación base = probabilidad × impacto (riesgo inherente puro)
            $baseScore = (int) $assessment->probability * (int) $assessment->impact;

            if ($assessment->type === 'residual') {
                $risk = $assessment->risk;

                // getAverageControlEffectiveness() carga $risk->controls en memoria (lazy load)
                $effectiveness = $risk?->getAverageControlEffectiveness() ?? 0;

                // Si hay controles definidos, usamos su efectividad real.
                // Si no, se toma el nivel de gestión estimado manualmente como fallback.
                // Usamos $risk->controls (colección ya cargada) para no lanzar otra consulta SQL.
                $hasControls  = $risk && $risk->controls->isNotEmpty();
                $reductionRatio = $hasControls
                    ? $effectiveness / 100
                    : (int) $assessment->management_level / 100;

                // El ratio de reducción debe estar en [0, 1]
                $reductionRatio = max(0, min(1, $reductionRatio));

                // Riesgo residual = base × (1 − reducción), redondeado hacia arriba
                $assessment->score = (int) ceil($baseScore * (1 - $reductionRatio));
            } else {
                // Riesgo inherente: sin reducción por controles
                $assessment->score = $baseScore;
            }
        });
    }
}
