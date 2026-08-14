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
            // ── Gestión de proyectos de consultoría ─────────────────────────
            [
                'name'        => 'Retrasos en la entrega de proyectos de consultoría',
                'description' => 'Entregables fuera de plazo por falta de planificación o recursos, con riesgo de penalizaciones contractuales y pérdida de confianza del cliente.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Gestión de proyectos de consultoría',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 3, 'ope' => 4, 'rep' => 3,
                'last_review' => '2026-03-15', 'next_review' => '2026-06-15',
            ],
            [
                'name'        => 'Evidencias insuficientes en los entregables',
                'description' => 'Datos incompletos o no contrastados en las fases de diagnóstico y ejecución que invalidan conclusiones y obligan a repetir el trabajo.',
                'category'    => 'Operacional',
                'proceso'     => 'Gestión de proyectos de consultoría',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 2, 'ope' => 4, 'rep' => 3,
                'last_review' => '2026-03-15', 'next_review' => '2026-06-15',
            ],

            // ── Ejecución de servicios al cliente ────────────────────────────
            [
                'name'        => 'Incumplimiento normativo en la prestación de servicios',
                'description' => 'Ausencia o deficiencia de los procedimientos exigidos por la normativa vigente aplicable al servicio, con riesgo de sanciones y responsabilidad para la dirección.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Ejecución de servicios al cliente',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 4, 'ope' => 2, 'rep' => 4,
                'last_review' => '2026-04-01', 'next_review' => '2026-07-01',
            ],
            [
                'name'        => 'Quejas de clientes sin registro ni protocolo de respuesta',
                'description' => 'Ausencia de sistema de gestión de reclamaciones que puede derivar en pérdida de clientes, deterioro de la reputación y exposición a reclamaciones formales.',
                'category'    => 'Reputacional',
                'proceso'     => 'Ejecución de servicios al cliente',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 3, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Coordinación de acciones formativas ──────────────────────────
            [
                'name'        => 'Errores en la gestión documental de acciones formativas',
                'description' => 'Registros de asistencia incorrectos, no firmados o con datos incoherentes que invalidan la acreditación de la acción formativa y su financiación.',
                'category'    => 'Operacional',
                'proceso'     => 'Coordinación de acciones formativas',
                'treatment'   => 'reducir',
                'status'      => 'En seguimiento',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 3, 'ope' => 4, 'rep' => 2,
                'last_review' => '2026-04-15', 'next_review' => '2026-07-15',
            ],

            // ── Gestión administrativa ───────────────────────────────────────
            [
                'name'        => 'Errores en la facturación a clientes',
                'description' => 'Errores en datos fiscales (NIF, IBAN, conceptos) que generan reclamaciones, retrasos en cobros y posibles infracciones de la normativa de facturación electrónica.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Gestión administrativa',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 4, 'ope' => 3, 'rep' => 3,
                'last_review' => '2026-03-31', 'next_review' => '2026-06-30',
            ],
            [
                'name'        => 'Retrasos en cobros por ausencia de seguimiento sistematizado',
                'description' => 'Falta de control de vencimientos y procedimiento de recobro que genera tensión de tesorería y deterioro del fondo de maniobra.',
                'category'    => 'Financiero',
                'proceso'     => 'Gestión administrativa',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 5, 'ope' => 3, 'rep' => 2,
                'last_review' => '2026-04-01', 'next_review' => '2026-07-01',
            ],

            // ── Gestión de personal (RRHH interno) ───────────────────────────
            [
                'name'        => 'Contratos desactualizados sin cláusulas de protección de datos',
                'description' => 'Incumplimiento del RGPD por ausencia de cláusulas de confidencialidad y protección de datos personales en los contratos de trabajo.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Gestión de personal (RRHH interno)',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 2, 'eco' => 3, 'ope' => 2, 'rep' => 3,
                'last_review' => '2026-02-01', 'next_review' => '2026-05-01',
            ],
            [
                'name'        => 'Alta rotación sin plan de retención ni proceso de onboarding',
                'description' => 'Pérdida de talento y conocimiento clave por falta de políticas de retención estructuradas y proceso de acogida, con impacto directo en la capacidad operativa.',
                'category'    => 'Operacional',
                'proceso'     => 'Gestión de personal (RRHH interno)',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 3, 'ope' => 5, 'rep' => 3,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Compras y proveedores ────────────────────────────────────────
            [
                'name'        => 'Compras y proveedores sin criterios de evaluación definidos',
                'description' => 'Ausencia de criterios formales de selección y evaluación de proveedores en términos de coste, seguridad, cumplimiento legal y continuidad del servicio.',
                'category'    => 'Operacional',
                'proceso'     => 'Compras y proveedores',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => 'Corrupción en los negocios',
                'probability' => 3, 'eco' => 3, 'ope' => 3, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],
            [
                'name'        => 'Dependencia de proveedores externos sin alternativas',
                'description' => 'Riesgo de interrupción de servicios por falta de proveedores alternativos homologados, con impacto en los plazos de entrega al cliente.',
                'category'    => 'Operacional',
                'proceso'     => 'Compras y proveedores',
                'treatment'   => 'transferir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 4, 'rep' => 2,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Sistemas e infraestructuras ──────────────────────────────────
            [
                'name'        => 'Accesos internos sin control por roles',
                'description' => 'Usuarios con permisos excesivos o no acordes a su función que exponen información sensible de clientes y datos personales a accesos no autorizados.',
                'category'    => 'Seguridad',
                'proceso'     => 'Sistemas e infraestructuras',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => 'Descubrimiento y revelación de secretos',
                'probability' => 4, 'eco' => 4, 'ope' => 4, 'rep' => 5,
                'last_review' => '2026-03-01', 'next_review' => '2026-06-01',
            ],
            [
                'name'        => 'Ausencia de plan de continuidad ante caída de sistemas críticos',
                'description' => 'Sin BCP documentado ni probado, una caída de los sistemas cloud/SaaS puede paralizar la operación durante días con pérdida de ingresos y afectación a clientes.',
                'category'    => 'Seguridad',
                'proceso'     => 'Sistemas e infraestructuras',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 2, 'eco' => 4, 'ope' => 5, 'rep' => 4,
                'last_review' => '2026-03-01', 'next_review' => '2026-06-01',
            ],
            [
                'name'        => 'Ausencia de política de contraseñas seguras y doble factor (2FA)',
                'description' => 'Acceso no autorizado a sistemas críticos por contraseñas débiles, compartidas o reutilizadas sin segundo factor de autenticación.',
                'category'    => 'Seguridad',
                'proceso'     => 'Sistemas e infraestructuras',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 4, 'rep' => 4,
                'last_review' => '2026-03-01', 'next_review' => '2026-06-01',
            ],

            // ── Gestión documental y evidencias ──────────────────────────────
            [
                'name'        => 'Pérdida de trazabilidad de cambios en documentos',
                'description' => 'Sin versionado ni registro de cambios, es imposible acreditar qué versión fue aprobada y entregada al cliente, generando riesgo de disputas y responsabilidad profesional.',
                'category'    => 'Operacional',
                'proceso'     => 'Gestión documental y evidencias',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 2, 'ope' => 4, 'rep' => 3,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],
            [
                'name'        => 'Documentos con datos sensibles sin cifrado ni control de acceso',
                'description' => 'Expedientes de clientes con datos personales almacenados sin cifrado y sin control de acceso diferenciado, incumpliendo el artículo 32 del RGPD.',
                'category'    => 'Seguridad',
                'proceso'     => 'Gestión documental y evidencias',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => 'Descubrimiento y revelación de secretos',
                'probability' => 3, 'eco' => 3, 'ope' => 3, 'rep' => 5,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Soporte legal a proyectos ─────────────────────────────────────
            [
                'name'        => 'Interpretación errónea de normativa en informes de consultoría',
                'description' => 'Análisis jurídico deficiente que genera recomendaciones incorrectas al cliente, con riesgo de responsabilidad civil profesional y pérdida de la relación comercial.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Soporte legal a proyectos',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 2, 'eco' => 4, 'ope' => 3, 'rep' => 5,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Marketing y comunicación ──────────────────────────────────────
            [
                'name'        => 'Publicación de contenido sin validación legal previa',
                'description' => 'Difusión en redes sociales o web de contenido con afirmaciones inexactas o que infringen derechos de imagen o propiedad intelectual sin revisión previa del equipo legal.',
                'category'    => 'Reputacional',
                'proceso'     => 'Marketing y comunicación',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 2, 'ope' => 2, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-08-01',
            ],
        ];

        foreach ($risks as $r) {
            $unit = OrganizationalUnit::where('name', $r['proceso'])->first();

            $risk = Risk::firstOrCreate(
                ['name' => $r['name']],
                [
                    'description'           => $r['description'],
                    'category'              => $r['category'],
                    'organizational_unit_id' => $unit?->id,
                    'responsable_id'        => null,
                    'type_crime'            => $r['type_crime'],
                    'treatment'             => $r['treatment'],
                    'status'                => $r['status'] ?: Risk::STATUS_IDENTIFICADO,
                    'last_review_at'        => !empty($r['last_review']) ? Carbon::parse($r['last_review']) : null,
                    'next_review_at'        => !empty($r['next_review']) ? Carbon::parse($r['next_review']) : null,
                ]
            );

            if (!empty($r['probability'])) {
                Assessment::firstOrCreate(
                    ['risk_id' => $risk->id, 'type' => 'inherent'],
                    [
                        'probability'          => $r['probability'],
                        'economic_impact'      => $r['eco'],
                        'operational_impact'   => $r['ope'],
                        'reputational_impact'  => $r['rep'],
                        'assessed_at'          => now(),
                    ]
                );
            }
        }
    }
}
