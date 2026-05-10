<?php

namespace Database\Seeders;

use App\Models\Control;
use App\Models\Risk;
use Illuminate\Database\Seeder;

class ControlSeeder extends Seeder
{
    public function run(): void
    {
        $controls = [
            // ── Incumplimiento de plazos en Plan de Igualdad ─────────────────
            [
                'risk'          => 'Incumplimiento de plazos en Plan de Igualdad',
                'title'         => 'Cronograma maestro con hitos y alertas de desviación',
                'description'   => 'Planificación detallada por proyecto con fechas clave, responsables y alertas automáticas cuando se supera el 80% del plazo.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Incumplimiento de plazos en Plan de Igualdad',
                'title'         => 'Revisión semanal de estado y bloqueos por proyecto',
                'description'   => 'Reunión interna semanal con checklist de estado, impedimentos detectados y acciones correctoras inmediatas.',
                'type'          => 'Reactivo',
                'frequency'     => 'Semanal',
                'effectiveness' => 'Medio',
            ],

            // ── Diagnóstico con evidencias insuficientes ──────────────────────
            [
                'risk'          => 'Diagnóstico de igualdad con evidencias insuficientes',
                'title'         => 'Checklist de evidencias mínimas obligatorias por fase',
                'description'   => 'Lista de control con los documentos requeridos para cada fase del diagnóstico; bloquea el avance si no está completa.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Diagnóstico de igualdad con evidencias insuficientes',
                'title'         => 'Revisión por pares antes de entrega al cliente',
                'description'   => 'Revisión interna obligatoria por una segunda consultora antes de enviar cualquier entregable al cliente.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Medio',
            ],

            // ── Auditoría retributiva ─────────────────────────────────────────
            [
                'risk'          => 'Auditoría retributiva con datos salariales inconsistentes',
                'title'         => 'Validación cruzada de datos salariales con tres fuentes',
                'description'   => 'Comparación de datos de nómina, sistema de RRHH y tabla de puestos antes de iniciar el análisis retributivo.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Medio',
            ],
            [
                'risk'          => 'Auditoría retributiva con datos salariales inconsistentes',
                'title'         => 'Revisión jurídica del informe final antes de entrega',
                'description'   => 'Validación del área legal del informe de auditoría retributiva antes de su presentación al cliente.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],

            // ── Protocolo de acoso ────────────────────────────────────────────
            [
                'risk'          => 'Protocolo de acoso sexual y laboral incompleto o no comunicado',
                'title'         => 'Revisión legal anual del protocolo de acoso',
                'description'   => 'Actualización del protocolo conforme a normativa vigente con validación del área jurídica y aprobación de la Dirección.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Protocolo de acoso sexual y laboral incompleto o no comunicado',
                'title'         => 'Comunicación y formación sobre el protocolo a toda la plantilla',
                'description'   => 'Difusión anual del protocolo con sesión formativa obligatoria y acuse de recibo firmado por cada persona de la plantilla.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Medio',
            ],

            // ── Bonificaciones FUNDAE ─────────────────────────────────────────
            [
                'risk'          => 'Pérdida de bonificaciones FUNDAE por incumplimientos formales',
                'title'         => 'Control documental previo al inicio de cada acción formativa',
                'description'   => 'Verificación de todos los requisitos FUNDAE (comunicación, plataforma RLT, participantes) antes del inicio de cada acción.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Pérdida de bonificaciones FUNDAE por incumplimientos formales',
                'title'         => 'Revisión mensual de expedientes FUNDAE en curso',
                'description'   => 'Revisión de todos los expedientes activos para detectar documentación pendiente o plazos próximos a vencer.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Medio',
            ],

            // ── Justificación horas formativas ────────────────────────────────
            [
                'risk'          => 'Errores en la justificación de horas formativas ante FUNDAE',
                'title'         => 'Registro de asistencia firmado diariamente por participantes y formador',
                'description'   => 'Hoja de asistencia con firma obligatoria de cada participante y del formador al inicio y al final de cada jornada.',
                'type'          => 'Preventivo',
                'frequency'     => 'Semanal',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Errores en la justificación de horas formativas ante FUNDAE',
                'title'         => 'Revisión de partes de asistencia antes de la comunicación a FUNDAE',
                'description'   => 'Comprobación manual de coherencia y completitud de los partes de asistencia antes de realizar la comunicación oficial.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],

            // ── Accesos internos ──────────────────────────────────────────────
            [
                'risk'          => 'Accesos internos sin control por roles',
                'title'         => 'Revisión trimestral de permisos y cuentas activas',
                'description'   => 'Inventario completo de usuarios, roles y permisos con limpieza de cuentas inactivas y ajuste de permisos excesivos.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Medio',
            ],
            [
                'risk'          => 'Accesos internos sin control por roles',
                'title'         => 'Política de control de acceso basado en roles (RBAC)',
                'description'   => 'Definición y documentación de perfiles de acceso por función con proceso de aprobación por el responsable de área.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Insuficiente',
            ],

            // ── Retrasos en cobros ────────────────────────────────────────────
            [
                'risk'          => 'Retrasos en cobros por ausencia de seguimiento sistematizado',
                'title'         => 'Seguimiento semanal de facturas vencidas pendientes de cobro',
                'description'   => 'Revisión semanal del listado de facturas vencidas sin cobrar con envío de recordatorio automatizado a los 7 y 21 días.',
                'type'          => 'Reactivo',
                'frequency'     => 'Semanal',
                'effectiveness' => 'Medio',
            ],
            [
                'risk'          => 'Retrasos en cobros por ausencia de seguimiento sistematizado',
                'title'         => 'Alerta de vencimiento de facturas en el sistema de gestión',
                'description'   => 'Configuración de alertas automáticas en el ERP cuando una factura supera su fecha de vencimiento sin registrar el cobro.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Insuficiente',
            ],

            // ── Alta rotación ─────────────────────────────────────────────────
            [
                'risk'          => 'Alta rotación sin plan de retención ni proceso de onboarding',
                'title'         => 'Encuesta de clima y satisfacción laboral semestral',
                'description'   => 'Encuesta anónima de clima laboral con análisis de resultados y plan de acción para los puntos críticos detectados.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Insuficiente',
            ],
            [
                'risk'          => 'Alta rotación sin plan de retención ni proceso de onboarding',
                'title'         => 'Plan de onboarding estructurado para nuevas incorporaciones',
                'description'   => 'Proceso de acogida de 90 días con mentoring asignado, formación interna y seguimiento periódico de adaptación.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Insuficiente',
            ],

            // ── Documentos sensibles sin cifrado ─────────────────────────────
            [
                'risk'          => 'Documentos con datos sensibles sin cifrado ni control de acceso',
                'title'         => 'Cifrado de repositorios con expedientes de clientes',
                'description'   => 'Aplicación de cifrado en carpetas que contengan datos personales de clientes en los sistemas de almacenamiento corporativos.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Insuficiente',
            ],
            [
                'risk'          => 'Documentos con datos sensibles sin cifrado ni control de acceso',
                'title'         => 'Control de acceso a repositorios documentales por proyecto',
                'description'   => 'Permisos de acceso granulares por proyecto con registro de accesos y alertas ante accesos no habituales.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Medio',
            ],

            // ── Plan de continuidad ───────────────────────────────────────────
            [
                'risk'          => 'Ausencia de plan de continuidad ante caída de sistemas críticos',
                'title'         => 'Backup automático diario con verificación de integridad',
                'description'   => 'Copia de seguridad automatizada diaria de todos los datos críticos con comprobación mensual de restauración exitosa.',
                'type'          => 'Preventivo',
                'frequency'     => 'Semanal',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Ausencia de plan de continuidad ante caída de sistemas críticos',
                'title'         => 'Prueba anual del plan de continuidad de negocio (BCP)',
                'description'   => 'Simulacro anual de caída de sistemas para verificar tiempo de recuperación (RTO), punto de recuperación (RPO) y efectividad de los procedimientos.',
                'type'          => 'Reactivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Insuficiente',
            ],

            // ── Seguimiento sin indicadores ───────────────────────────────────
            [
                'risk'          => 'Seguimiento sin indicadores del Plan de Igualdad',
                'title'         => 'Cuadro de mando trimestral de indicadores del Plan de Igualdad',
                'description'   => 'Dashboard con KPIs de actividad, cumplimiento e impacto actualizado trimestralmente y compartido con el cliente y la RLT.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Insuficiente',
            ],

            // ── Compras sin criterios ─────────────────────────────────────────
            [
                'risk'          => 'Compras y proveedores sin criterios de evaluación definidos',
                'title'         => 'Plantilla de evaluación y homologación de proveedores',
                'description'   => 'Formulario de evaluación con criterios de coste, RGPD, seguridad, calidad y continuidad de servicio para nuevos proveedores.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Insuficiente',
            ],
        ];

        foreach ($controls as $c) {
            $risk = Risk::where('name', $c['risk'])->first();
            if (!$risk) {
                continue;
            }

            Control::firstOrCreate(
                ['risk_id' => $risk->id, 'title' => $c['title']],
                [
                    'description'           => $c['description'],
                    'type'                  => $c['type'],
                    'frequency'             => $c['frequency'],
                    'effectiveness'         => $c['effectiveness'],
                    'responsable_id'        => null,
                    'due_date'              => null,
                    'organizational_unit_id' => $risk->organizational_unit_id,
                ]
            );
        }
    }
}
