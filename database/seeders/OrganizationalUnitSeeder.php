<?php

namespace Database\Seeders;

use App\Models\OrganizationalUnit;
use Illuminate\Database\Seeder;

class OrganizationalUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['type' => 'Área', 'name' => 'Dirección'],
            ['type' => 'Área', 'name' => 'Negocio'],
            ['type' => 'Área', 'name' => 'Operaciones'],
            ['type' => 'Área', 'name' => 'Tecnología'],
            ['type' => 'Departamento', 'name' => 'Dirección'],
            ['type' => 'Departamento', 'name' => 'Administración'],
            ['type' => 'Departamento', 'name' => 'Consultoría Igualdad'],
            ['type' => 'Departamento', 'name' => 'Consultoría RRHH'],
            ['type' => 'Departamento', 'name' => 'Legal'],
            ['type' => 'Departamento', 'name' => 'Formación'],
            ['type' => 'Departamento', 'name' => 'Comercial'],
            ['type' => 'Departamento', 'name' => 'Marketing'],
            ['type' => 'Departamento', 'name' => 'IT'],
            ['type' => 'Proceso Operativo', 'name' => 'Ejecución de Planes de Igualdad'],
            ['type' => 'Proceso Operativo', 'name' => 'Ejecución de Planes LGTBI'],
            ['type' => 'Proceso Operativo', 'name' => 'Seguimiento y evaluación de Planes de Igualdad'],
            ['type' => 'Proceso Operativo', 'name' => 'Auditoría retributiva y valoración de puestos'],
            ['type' => 'Proceso Operativo', 'name' => 'Elaboración e implantación de Protocolos (acoso / desconexión)'],
            ['type' => 'Proceso Operativo', 'name' => 'Gestión integral de Formación Bonificada (FUNDAE)'],
            ['type' => 'Proceso Operativo', 'name' => 'Coordinación de acciones formativas y proveedores'],
            ['type' => 'Proceso Operativo', 'name' => 'Atención y soporte a cliente (operativa)'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Gestión administrativa (facturación, cobros, documentación)'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Gestión de personal (RRHH interno)'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Sistemas e infra (backups, accesos, continuidad)'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Gestión documental y evidencias'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Marketing y comunicación'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Soporte legal a proyectos'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Compras y proveedores'],
        ];

        foreach ($units as $unit) {
            OrganizationalUnit::firstOrCreate(
                ['name' => $unit['name']],
                ['type' => $unit['type'], 'is_active' => true]
            );
        }
    }
}
