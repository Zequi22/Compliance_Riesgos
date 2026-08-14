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
            // ── Retrasos en la entrega de proyectos ───────────────────────────
            [
                'risk'          => 'Retrasos en la entrega de proyectos de consultoría',
                'title'         => 'Cronograma maestro con hitos y alertas de desviación',
                'description'   => 'Planificación detallada por proyecto con fechas clave, responsables y alertas automáticas cuando se supera el 80% del plazo.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Retrasos en la entrega de proyectos de consultoría',
                'title'         => 'Revisión semanal de estado y bloqueos por proyecto',
                'description'   => 'Reunión interna semanal con checklist de estado, impedimentos detectados y acciones correctoras inmediatas.',
                'type'          => 'Reactivo',
                'frequency'     => 'Semanal',
                'effectiveness' => 'Medio',
            ],

            // ── Evidencias insuficientes ──────────────────────────────────────
            [
                'risk'          => 'Evidencias insuficientes en los entregables',
                'title'         => 'Checklist de evidencias mínimas obligatorias por fase',
                'description'   => 'Lista de control con los documentos requeridos para cada fase del proyecto; bloquea el avance si no está completa.',
                'type'          => 'Preventivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Evidencias insuficientes en los entregables',
                'title'         => 'Revisión por pares antes de entrega al cliente',
                'description'   => 'Revisión interna obligatoria por una segunda persona cualificada antes de enviar cualquier entregable al cliente.',
                'type'          => 'Reactivo',
                'frequency'     => 'Mensual',
                'effectiveness' => 'Medio',
            ],

            // ── Incumplimiento normativo ──────────────────────────────────────
            [
                'risk'          => 'Incumplimiento normativo en la prestación de servicios',
                'title'         => 'Revisión jurídica anual de procedimientos y protocolos',
                'description'   => 'Actualización de procedimientos conforme a la normativa vigente con validación del área jurídica y aprobación de la Dirección.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Suficiente',
            ],
            [
                'risk'          => 'Incumplimiento normativo en la prestación de servicios',
                'title'         => 'Comunicación y formación sobre procedimientos a la plantilla',
                'description'   => 'Difusión anual de los procedimientos con sesión formativa obligatoria y acuse de recibo firmado por cada persona.',
                'type'          => 'Preventivo',
                'frequency'     => 'Anual',
                'effectiveness' => 'Medio',
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
                'description'   => 'Configuración de alertas automáticas cuando una factura supera su fecha de vencimiento sin registrar el cobro.',
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
