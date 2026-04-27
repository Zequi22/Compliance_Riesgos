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
            $impacts = array_filter([
                $assessment->economic_impact,
                $assessment->operational_impact,
                $assessment->reputational_impact,
            ]);

            if (! empty($impacts)) {
                $assessment->impact = max($impacts);
            }

            if (isset($assessment->probability) && isset($assessment->impact)) {
                $baseScore = (int) $assessment->probability * (int) $assessment->impact;

                if ($assessment->type === 'residual') {
                    // Try to get actual effectiveness from controls if risk relation is loaded or loadable
                    $risk = $assessment->risk;
                    $effectiveness = 0;
                    
                    if ($risk) {
                        $effectiveness = $risk->getAverageControlEffectiveness();
                    }

                    // Calculamos usando la efectividad real si existe (los controles la definen mayor o igual a 0).
                    // Si el usuario puso un management_level y no hay efectividad real, usamos el management_level como fallback
                    // Si la efectividad_real es 0 y el management_level es > 0, podríamos usar el valor del usuario, 
                    // dependiento de la lógica. Para respetar la evaluación de controles:
                    // Si el riesgo tiene controles, tiene sentido basarnos estrictamente en ellos, 
                    // pero es más seguro aplicar cualquiera de los dos (o el máximo) que sea mayor de 0, o priorizar uno.
                    // Priorizamos la efectividad calculada de los controles reales
                    
                    if ($risk && $risk->controls()->count() > 0) {
                        $reductionRatio = $effectiveness / 100;
                    } else {
                        // Fallback al nivel de gestión estimado si no hay controles analizados
                        $mgnt = (int) $assessment->management_level;
                        $reductionRatio = $mgnt / 100;
                    }
                    
                    // Asegurarse de que el ratio no sea negativo ni mayor a 1
                    $reductionRatio = max(0, min(1, $reductionRatio));
                    
                    $assessment->score = (int) ceil($baseScore * (1 - $reductionRatio));
                } else {
                    $assessment->score = $baseScore;
                }
            }
        });
    }
}
