<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{

    /**
     * Atributos que pueden ser asignados en masa (mass assignment)
     */
    protected $fillable = [
        'title',
        'responsable_id',
        'due_date',
        'start_date',
        'commitment_date',
        'actual_closure_date',
        'status',
        'progress',
        'risk_id',
        'depends_on_action_id',
        'notes',
        'organizational_unit_id',
        'priority',
    ];

    /**
     * Los atributos que deben ser convertidos a fechas.
     */
    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'commitment_date' => 'date',
        'actual_closure_date' => 'date',
    ];

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'depends_on_action_id');
    }

    public function organizationalUnit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function updates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActionUpdate::class)->orderBy('created_at', 'desc');
    }

    public function latestUpdate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ActionUpdate::class)->latestOfMany();
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiskDocument::class, 'action_id');
    }

    public const STATUS_PENDIENTE = 'pendiente';
    public const STATUS_EN_CURSO = 'en_curso';
    public const STATUS_BLOQUEADA = 'bloqueada';
    public const STATUS_EN_REVISION = 'en_revision';
    public const STATUS_CERRADA = 'cerrada';
    public const STATUS_CANCELADA = 'cancelada';

    public const PRIORITY_ALTA = 'alta';
    public const PRIORITY_MEDIA = 'media';
    public const PRIORITY_BAJA = 'baja';

    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_ALTA => 'Alta',
            self::PRIORITY_MEDIA => 'Media',
            self::PRIORITY_BAJA => 'Baja',
        ];
    }

    public static function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            self::PRIORITY_ALTA => 'danger',
            self::PRIORITY_MEDIA => 'warning',
            self::PRIORITY_BAJA => 'info',
            default => 'gray',
        };
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDIENTE => 'Pendiente',
            self::STATUS_EN_CURSO => 'En curso',
            self::STATUS_BLOQUEADA => 'Bloqueada',
            self::STATUS_EN_REVISION => 'En revisión',
            self::STATUS_CERRADA => 'Cerrada',
            self::STATUS_CANCELADA => 'Cancelada',
        ];
    }

    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_EN_CURSO => 'info',
            self::STATUS_BLOQUEADA => 'danger',
            self::STATUS_EN_REVISION => 'warning',
            self::STATUS_CERRADA => 'success',
            default => 'gray', // Cubre pendiente, cancelada y cualquier otro.
        };
    }

    /**
     * Determina si la acción está vencida.
     * (hoy > fecha compromiso y no cerrada)
     */
    public function isOverdue(): bool
    {
        if ($this->status === self::STATUS_CERRADA) {
            return false;
        }

        if (!$this->commitment_date) {
            return false;
        }

        return $this->commitment_date->isPast() && !$this->commitment_date->isToday();
    }

    /**
     * Verifica si la acción tiene al menos una evidencia (documento) vinculada.
     */
    public function hasEvidence(): bool
    {
        return $this->documents()->exists();
    }
}
