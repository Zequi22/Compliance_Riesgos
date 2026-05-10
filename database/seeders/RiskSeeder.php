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
            // ── Ejecución de Planes de Igualdad ─────────────────────────────
            [
                'name'        => 'Incumplimiento de plazos en Plan de Igualdad',
                'description' => 'Entregables fuera de plazo por falta de planificación o recursos, con riesgo de penalizaciones contractuales y pérdida de confianza del cliente.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Ejecución de Planes de Igualdad',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 3, 'ope' => 4, 'rep' => 4,
                'last_review' => '2026-03-15', 'next_review' => '2026-06-15',
            ],
            [
                'name'        => 'Diagnóstico de igualdad con evidencias insuficientes',
                'description' => 'Datos incompletos o no contrastados en la fase de diagnóstico que invalidan conclusiones y obligan a repetir el trabajo.',
                'category'    => 'Operacional',
                'proceso'     => 'Ejecución de Planes de Igualdad',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 2, 'ope' => 4, 'rep' => 3,
                'last_review' => '2026-03-15', 'next_review' => '2026-06-15',
            ],
            [
                'name'        => 'Falta de registro de negociación del Plan de Igualdad',
                'description' => 'Ausencia de actas o evidencias documentales de las negociaciones con la RLT que impide acreditar el proceso ante la autoridad laboral.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Ejecución de Planes de Igualdad',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 2, 'eco' => 3, 'ope' => 2, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Seguimiento y evaluación de Planes de Igualdad ──────────────
            [
                'name'        => 'Seguimiento sin indicadores del Plan de Igualdad',
                'description' => 'Falta de KPIs cuantificables y medición periódica que impide evaluar el avance real y detectar desviaciones a tiempo.',
                'category'    => 'Operacional',
                'proceso'     => 'Seguimiento y evaluación de Planes de Igualdad',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 2, 'ope' => 3, 'rep' => 3,
                'last_review' => '2026-03-15', 'next_review' => '2026-06-15',
            ],
            [
                'name'        => 'Informe de seguimiento anual no presentado a la RLT',
                'description' => 'Incumplimiento del Art. 46 de la Ley Orgánica de Igualdad por no presentar el informe de seguimiento en plazo a la representación de los trabajadores.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Seguimiento y evaluación de Planes de Igualdad',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 4, 'ope' => 2, 'rep' => 4,
                'last_review' => '2026-04-01', 'next_review' => '2026-07-01',
            ],

            // ── Auditoría retributiva y valoración de puestos ────────────────
            [
                'name'        => 'Auditoría retributiva con datos salariales inconsistentes',
                'description' => 'Errores o incoherencias en nóminas, complementos y puestos que distorsionan los resultados del análisis y pueden generar conclusiones incorrectas.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Auditoría retributiva y valoración de puestos',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 4, 'ope' => 3, 'rep' => 4,
                'last_review' => '2026-02-28', 'next_review' => '2026-05-28',
            ],
            [
                'name'        => 'Registro retributivo no actualizado conforme al RD 902/2020',
                'description' => 'Ausencia o desactualización del registro retributivo obligatorio, exponiéndose a sanciones de la ITSS de hasta 187.515 €.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Auditoría retributiva y valoración de puestos',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 4, 'ope' => 2, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Ejecución de Planes LGTBI ────────────────────────────────────
            [
                'name'        => 'Falta de protocolo LGTBI actualizado según Ley 4/2023',
                'description' => 'El protocolo LGTBI no recoge los requisitos de la Ley 4/2023 para la igualdad real y efectiva de las personas trans, exponiendo a la empresa a sanciones y reclamaciones.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Ejecución de Planes LGTBI',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 3, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],
            [
                'name'        => 'Ausencia de medidas de apoyo a la transición de género en empresa',
                'description' => 'No existen procedimientos internos para gestionar solicitudes de adaptación de nombre, vestuario o instalaciones, generando riesgo de discriminación y conflicto laboral.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Ejecución de Planes LGTBI',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 2, 'eco' => 3, 'ope' => 2, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-08-01',
            ],

            // ── Elaboración e implantación de Protocolos ─────────────────────
            [
                'name'        => 'Protocolo de acoso sexual y laboral incompleto o no comunicado',
                'description' => 'Incumplimiento del Art. 48 LOI por ausencia o deficiencia del protocolo de acoso, con riesgo de sanción grave de la ITSS y responsabilidad penal para la dirección.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Elaboración e implantación de Protocolos (acoso / desconexión)',
                'treatment'   => 'reducir',
                'status'      => 'Evaluado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 4, 'ope' => 3, 'rep' => 5,
                'last_review' => '2026-03-20', 'next_review' => '2026-06-20',
            ],
            [
                'name'        => 'Ausencia de política de desconexión digital efectiva',
                'description' => 'Incumplimiento del Art. 88 LOPD-GDD por falta de política de desconexión digital documentada y comunicada, con exposición a reclamaciones de empleados.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Elaboración e implantación de Protocolos (acoso / desconexión)',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 2, 'rep' => 3,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Gestión integral de Formación Bonificada (FUNDAE) ────────────
            [
                'name'        => 'Pérdida de bonificaciones FUNDAE por incumplimientos formales',
                'description' => 'Expedientes incompletos o comunicados fuera de plazo que generan devolución de bonificaciones y posibles sanciones de la SEPE.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Gestión integral de Formación Bonificada (FUNDAE)',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 4, 'ope' => 3, 'rep' => 3,
                'last_review' => '2026-04-01', 'next_review' => '2026-07-01',
            ],
            [
                'name'        => 'Errores en la justificación de horas formativas ante FUNDAE',
                'description' => 'Partes de asistencia incorrectos, no firmados o con datos incoherentes que invalidan la bonificación de la acción formativa.',
                'category'    => 'Operacional',
                'proceso'     => 'Gestión integral de Formación Bonificada (FUNDAE)',
                'treatment'   => 'reducir',
                'status'      => 'En seguimiento',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 3, 'ope' => 4, 'rep' => 2,
                'last_review' => '2026-04-15', 'next_review' => '2026-07-15',
            ],

            // ── Coordinación de acciones formativas y proveedores ────────────
            [
                'name'        => 'Dependencia de proveedor formativo único sin alternativas',
                'description' => 'Riesgo de cancelación de acciones formativas por falta de proveedores alternativos homologados, con impacto en los plazos de bonificación FUNDAE.',
                'category'    => 'Operacional',
                'proceso'     => 'Coordinación de acciones formativas y proveedores',
                'treatment'   => 'transferir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 4, 'rep' => 2,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Atención y soporte a cliente (operativa) ─────────────────────
            [
                'name'        => 'Quejas de clientes sin registro ni protocolo de respuesta',
                'description' => 'Ausencia de sistema de gestión de reclamaciones que puede derivar en pérdida de clientes, deterioro de la reputación y exposición a reclamaciones formales.',
                'category'    => 'Reputacional',
                'proceso'     => 'Atención y soporte a cliente (operativa)',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 3, 'rep' => 4,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],

            // ── Gestión administrativa ────────────────────────────────────────
            [
                'name'        => 'Facturas emitidas con errores fiscales o de datos del cliente',
                'description' => 'Errores en datos fiscales (NIF, IBAN, conceptos) que generan reclamaciones, retrasos en cobros y posibles infracciones de la normativa de facturación electrónica.',
                'category'    => 'Legal/Compliance',
                'proceso'     => 'Gestión administrativa (facturación, cobros, documentación)',
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
                'proceso'     => 'Gestión administrativa (facturación, cobros, documentación)',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 4, 'eco' => 5, 'ope' => 3, 'rep' => 2,
                'last_review' => '2026-04-01', 'next_review' => '2026-07-01',
            ],

            // ── Gestión de personal (RRHH interno) ───────────────────────────
            [
                'name'        => 'Contratos laborales desactualizados o sin cláusulas de protección de datos',
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

            // ── Compras y proveedores ─────────────────────────────────────────
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

            // ── Sistemas e infra ──────────────────────────────────────────────
            [
                'name'        => 'Accesos internos sin control por roles',
                'description' => 'Usuarios con permisos excesivos o no acordes a su función que exponen información sensible de clientes y datos personales a accesos no autorizados.',
                'category'    => 'Seguridad',
                'proceso'     => 'Sistemas e infra (backups, accesos, continuidad)',
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
                'proceso'     => 'Sistemas e infra (backups, accesos, continuidad)',
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
                'proceso'     => 'Sistemas e infra (backups, accesos, continuidad)',
                'treatment'   => 'reducir',
                'status'      => 'En tratamiento',
                'type_crime'  => null,
                'probability' => 3, 'eco' => 3, 'ope' => 4, 'rep' => 4,
                'last_review' => '2026-03-01', 'next_review' => '2026-06-01',
            ],

            // ── Gestión documental y evidencias ──────────────────────────────
            [
                'name'        => 'Pérdida de trazabilidad de cambios en informes entregados',
                'description' => 'Sin versionado ni registro de cambios, es imposible acreditar qué versión fue aprobada y entregada al cliente, generando riesgo de disputas y responsabilidad profesional.',
                'category'    => 'Operacional',
                'proceso'     => 'Gestión documental y evidencias',
                'treatment'   => 'reducir',
                'status'      => 'Identificado',
                'type_crime'  => 'Daños informáticos',
                'probability' => 3, 'eco' => 2, 'ope' => 4, 'rep' => 3,
                'last_review' => '', 'next_review' => '2026-07-01',
            ],
            [
                'name'        => 'Documentos con datos sensibles sin cifrado ni control de acceso',
                'description' => 'Expedientes de clientes con datos personales almacenados sin cifrado y sin control de acceso diferenciado, incumpliendo el Art. 32 del RGPD.',
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
                'description' => 'Difusión en RRSS o web de contenido con afirmaciones inexactas o que infringen derechos de imagen/propiedad intelectual sin revisión previa del equipo legal.',
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
