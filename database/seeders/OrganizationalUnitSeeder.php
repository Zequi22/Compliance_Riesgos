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
            ['type' => 'Departamento', 'name' => 'Consultoría'],
            ['type' => 'Departamento', 'name' => 'Legal'],
            ['type' => 'Departamento', 'name' => 'Formación'],
            ['type' => 'Departamento', 'name' => 'Comercial'],
            ['type' => 'Departamento', 'name' => 'Marketing'],
            ['type' => 'Departamento', 'name' => 'RRHH'],
            ['type' => 'Departamento', 'name' => 'IT'],
            ['type' => 'Proceso Operativo', 'name' => 'Gestión de proyectos de consultoría'],
            ['type' => 'Proceso Operativo', 'name' => 'Ejecución de servicios al cliente'],
            ['type' => 'Proceso Operativo', 'name' => 'Coordinación de acciones formativas'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Gestión administrativa'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Gestión de personal (RRHH interno)'],
            ['type' => 'Proceso de Apoyo', 'name' => 'Sistemas e infraestructuras'],
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
