<?php

namespace Database\Seeders;

use App\Models\Risk;
use App\Models\Assessment;
use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RiskSeeder extends Seeder
{
    public function run(): void
    {
        $risks = [
            ['name' => 'Incumplimiento de plazos en Plan de Igualdad', 'description' => 'Entregables fuera de plazo por falta de planificación o recursos', 'category' => 'Legal/Compliance', 'area' => 'Negocio', 'department' => 'Consultoría Igualdad', 'proceso' => 'Ejecución de Planes de Igualdad', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => 'Reducir', 'status' => 'Identificado', 'type_crime' => '', 'probability' => 4, 'eco' => 3, 'ope' => 4, 'rep' => 4, 'last_review' => '', 'next_review' => ''],
            ['name' => 'Diagnóstico de igualdad con evidencias insuficientes', 'description' => 'Datos incompletos o no contrastados in la fase de diagnóstico', 'category' => 'Operacional', 'area' => 'Negocio', 'department' => 'Consultoría Igualdad', 'proceso' => 'Ejecución de Planes de Igualdad', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => 'Reducir', 'status' => 'Identificado', 'type_crime' => '', 'probability' => 3, 'eco' => 2, 'ope' => 4, 'rep' => 3, 'last_review' => '', 'next_review' => ''],
            ['name' => 'Seguimiento sin indicadores del Plan de Igualdad', 'description' => 'Falta de KPIs y medición para evaluación periódica', 'category' => 'Operacional', 'area' => 'Negocio', 'department' => 'Consultoría Igualdad', 'proceso' => 'Seguimiento y evaluación de Planes de Igualdad', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => 'Reducir', 'status' => 'Identificado', 'type_crime' => '', 'probability' => 3, 'eco' => 2, 'ope' => 3, 'rep' => 3, 'last_review' => '', 'next_review' => ''],
            ['name' => 'Auditoría retributiva con datos salariales inconsistentes', 'description' => 'Errores o incoherencias en nóminas/variables/puestos que distorsionan resultados', 'category' => 'Legal/Compliance', 'area' => 'Negocio', 'department' => 'Consultoría Igualdad', 'proceso' => 'Auditoría retributiva y valoración de puestos', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => '', 'status' => '', 'type_crime' => '', 'probability' => null, 'eco' => null, 'ope' => null, 'rep' => null, 'last_review' => '', 'next_review' => ''],
            ['name' => 'Compras/proveedores sin criterios (coste/seguridad)', 'description' => '', 'category' => 'Operacional', 'area' => 'Negocio', 'department' => 'Operaciones', 'proceso' => 'Compras y proveedores', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => '', 'status' => 'Identificado', 'type_crime' => 'Corrupción en los negocios', 'probability' => null, 'eco' => null, 'ope' => null, 'rep' => null, 'last_review' => '', 'next_review' => ''],
            ['name' => 'Accesos internos sin control por roles', 'description' => '', 'category' => 'Seguridad', 'area' => 'Negocio', 'department' => 'IT', 'proceso' => 'Sistemas e infra (backups, accesos, continuidad)', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => '', 'status' => 'Identificado', 'type_crime' => 'Descubrimiento y revelación de secretos', 'probability' => null, 'eco' => null, 'ope' => null, 'rep' => null, 'last_review' => '', 'next_review' => ''],
            ['name' => 'Pérdida de trazabilidad de cambios en informes entregados', 'description' => '', 'category' => 'Operacional', 'area' => 'Negocio', 'department' => 'Consultoría Igualdad', 'proceso' => 'Gestión documental y evidencias', 'responsable_nombre' => '', 'responsable_apellidos' => '', 'treatment' => '', 'status' => 'Identificado', 'type_crime' => 'Daños informáticos', 'probability' => null, 'eco' => null, 'ope' => null, 'rep' => null, 'last_review' => '', 'next_review' => ''],
        ];

        foreach ($risks as $r) {
            $unit = OrganizationalUnit::where('name', $r['proceso'])->first();
            
            $responsable = null;
            if (!empty($r['responsable_nombre'])) {
                $responsable = User::where('name', $r['responsable_nombre'])
                                   ->where('last_name', $r['responsable_apellidos'])
                                   ->first();
            }

            $risk = Risk::create([
                'name' => $r['name'],
                'description' => $r['description'],
                'category' => $r['category'],
                'organizational_unit_id' => $unit?->id,
                'responsable_id' => $responsable?->id,
                'type_crime' => !empty($r['type_crime']) ? $r['type_crime'] : null,
                'treatment' => !empty($r['treatment']) ? $r['treatment'] : null,
                'status' => !empty($r['status']) ? $r['status'] : Risk::STATUS_IDENTIFICADO,
                'last_review_at' => !empty($r['last_review']) ? Carbon::parse($r['last_review']) : null,
                'next_review_at' => !empty($r['next_review']) ? Carbon::parse($r['next_review']) : null,
            ]);

            // Solo crea Assessment si hay probabilidad en el Excel
            if (!empty($r['probability'])) {
                Assessment::create([
                    'risk_id' => $risk->id, 
                    'type' => 'inherent',
                    'probability' => $r['probability'],
                    'economic_impact' => $r['eco'],
                    'operational_impact' => $r['ope'],
                    'reputational_impact' => $r['rep'],
                    'assessed_at' => now(), // Assessed_at es obligatorio en tu modelo, por eso se pasa now()
                ]);
            }
        }
    }
}
