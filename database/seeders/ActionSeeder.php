<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ActionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            ['risk_name' => 'Incumplimiento de plazos en Plan de Igualdad', 'title' => 'Definir calendario maestro del proyecto', 'description' => 'Crear cronograma con hitos, entregables y responsables para cada fase del plan', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'En curso', 'start_date' => '2026-03-16', 'due_date' => '2026-03-30', 'close_date' => '', 'progress' => '40', 'comments' => ''],
            ['risk_name' => 'Incumplimiento de plazos en Plan de Igualdad', 'title' => 'Implantar control semanal de seguimiento', 'description' => 'Reunión interna semanal + checklist de estado + bloqueo/impedimentos', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-05', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Diagnóstico de igualdad con evidencias insuficientes', 'title' => 'Checklist de evidencias mínimas', 'description' => 'Crear checklist por fase (diagnóstico, negociación, aprobación, seguimiento)', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-03-25', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Diagnóstico de igualdad con evidencias insuficientes', 'title' => 'Revisión por pares antes de entrega', 'description' => 'Implementar revisión interna por segunda persona antes de enviar al cliente', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-05', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Seguimiento sin indicadores del Plan de Igualdad', 'title' => 'Definir KPIs y ficha de indicadores', 'description' => 'Crear KPIs (actividad, cumplimiento, impacto) y plantilla para reporting periódico', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-10', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Seguimiento sin indicadores del Plan de Igualdad', 'title' => 'Dashboard de seguimiento trimestral', 'description' => 'Preparar cuadro de mando trimestral (avance medidas, desviaciones, comentarios)', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-20', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Compras/proveedores sin criterios (coste/seguridad)', 'title' => 'Plantilla de evaluación de proveedores', 'description' => 'Criterios: coste, RGPD, seguridad, calidad, continuidad', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Baja', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-30', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Accesos internos sin control por roles', 'title' => 'Revisión trimestral de accesos', 'description' => 'Inventario de usuarios y permisos + limpieza + registro de cambios', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-15', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Pérdida de trazabilidad de cambios en informes entregados', 'title' => 'Versionado y registro de cambios', 'description' => 'Activar versionado y log de cambios por entregable antes de envío', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-18', 'close_date' => '', 'progress' => '', 'comments' => ''],
        ];

        foreach ($actions as $a) {
            $risk = Risk::where('name', $a['risk_name'])->first();

            $responsable = null;
            if (!empty($a['responsable_nombre'])) {
                $responsable = User::where('name', $a['responsable_nombre'])
                                   ->where('last_name', $a['responsable_apellidos'])
                                   ->first();
            }

            // Usar los nuevos estados del modelo
            $status = match(strtolower($a['status'])) {
                'en curso' => Action::STATUS_EN_CURSO,
                'pendiente' => Action::STATUS_PENDIENTE,
                default => Action::STATUS_PENDIENTE,
            };

            $notes = $a['description'];
            if (!empty($a['priority'])) $notes .= "\nPrioridad: " . $a['priority'];
            if (!empty($a['progress'])) $notes .= "\nAvance: " . $a['progress'] . "%";

            if ($risk) {
                Action::create([
                    'risk_id' => $risk->id, 
                    'responsable_id' => $responsable?->id, // Será null si viene vacío del excel
                    'title' => $a['title'],
                    'notes' => $notes,
                    'due_date' => !empty($a['due_date']) ? Carbon::parse($a['due_date']) : null,
                    'status' => $status,
                ]);
            }
        }
    }
}