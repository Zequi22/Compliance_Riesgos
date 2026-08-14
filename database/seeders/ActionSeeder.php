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
            ['risk_name' => 'Retrasos en la entrega de proyectos de consultoría', 'title' => 'Definir calendario maestro del proyecto', 'description' => 'Crear cronograma con hitos, entregables y responsables para cada fase del proyecto', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'En curso', 'start_date' => '2026-03-16', 'due_date' => '2026-03-30', 'close_date' => '', 'progress' => '40', 'comments' => ''],
            ['risk_name' => 'Retrasos en la entrega de proyectos de consultoría', 'title' => 'Implantar control semanal de seguimiento', 'description' => 'Reunión interna semanal + checklist de estado + bloqueo/impedimentos', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-05', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Evidencias insuficientes en los entregables', 'title' => 'Checklist de evidencias mínimas', 'description' => 'Crear checklist por fase del proyecto (diagnóstico, ejecución, aprobación, seguimiento)', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-03-25', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Evidencias insuficientes en los entregables', 'title' => 'Revisión por pares antes de entrega', 'description' => 'Implementar revisión interna por segunda persona antes de enviar al cliente', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-05', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Accesos internos sin control por roles', 'title' => 'Revisión trimestral de accesos', 'description' => 'Inventario de usuarios y permisos + limpieza + registro de cambios', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-15', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Accesos internos sin control por roles', 'title' => 'Definir política RBAC', 'description' => 'Documentar perfiles de acceso por función con proceso de aprobación', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-30', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Retrasos en cobros por ausencia de seguimiento sistematizado', 'title' => 'Configurar alertas de vencimiento', 'description' => 'Activar alertas automáticas en el sistema de gestión para facturas vencidas', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-20', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Pérdida de trazabilidad de cambios en documentos', 'title' => 'Versionado y registro de cambios', 'description' => 'Activar versionado y log de cambios por entregable antes de envío', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Media', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-18', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Compras y proveedores sin criterios de evaluación definidos', 'title' => 'Plantilla de evaluación de proveedores', 'description' => 'Criterios: coste, RGPD, seguridad, calidad, continuidad', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Baja', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-04-30', 'close_date' => '', 'progress' => '', 'comments' => ''],
            ['risk_name' => 'Incumplimiento normativo en la prestación de servicios', 'title' => 'Revisar procedimientos conforme a normativa vigente', 'description' => 'Actualizar procedimientos con validación jurídica y aprobación de Dirección', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'priority' => 'Alta', 'status' => 'Pendiente', 'start_date' => '', 'due_date' => '2026-05-15', 'close_date' => '', 'progress' => '', 'comments' => ''],
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
                    'responsable_id' => $responsable?->id,
                    'title' => $a['title'],
                    'notes' => $notes,
                    'due_date' => !empty($a['due_date']) ? Carbon::parse($a['due_date']) : null,
                    'status' => $status,
                ]);
            }
        }
    }
}
