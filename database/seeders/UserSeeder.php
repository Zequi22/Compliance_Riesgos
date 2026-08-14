<?php

namespace Database\Seeders;

use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear roles con el guard correcto ANTES de asignarlos
        $roleNames = [
            'super_admin',
            'Responsable Compliance',
            'Responsable Área',
            'Técnico',
            'Consulta',
        ];

        foreach ($roleNames as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }

        // 2. Definir usuarios de demostración (datos ficticios)
        $users = [
            ['name' => '***REMOVED***',        'last_name' => 'García López',    'email' => 'admin@example.com',       'job_title' => 'Directora General',     'role' => 'super_admin',            'area' => 'Dirección',  'department' => 'Dirección'],
            ['name' => 'Carlos',     'last_name' => 'Ruiz Fernández',  'email' => 'carlos.ruiz@example.com', 'job_title' => 'Responsable Legal',     'role' => 'Responsable Compliance', 'area' => 'Operaciones','department' => 'Legal'],
            ['name' => 'Lucía',      'last_name' => 'Martín Sánchez',  'email' => 'lucia.martin@example.com','job_title' => 'Técnica Administración','role' => 'Técnico',                 'area' => 'Operaciones','department' => 'Administración'],
            ['name' => 'Pedro',      'last_name' => '***REMOVED*** Navarro',   'email' => 'pedro.gomez@example.com', 'job_title' => 'Responsable Comercial', 'role' => 'Responsable Área',        'area' => 'Operaciones','department' => 'Comercial'],
            ['name' => 'Marta',      'last_name' => 'Torres Vidal',    'email' => 'marta.torres@example.com','job_title' => 'Técnica Comercial',    'role' => 'Consulta',                'area' => 'Operaciones','department' => 'Comercial'],
            ['name' => 'Javier',     'last_name' => 'Moreno Ramos',    'email' => 'javier.moreno@example.com','job_title' => 'Responsable Formación', 'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => 'Sara',       'last_name' => 'Domínguez Peña',  'email' => 'sara.dominguez@example.com','job_title' => 'Técnica Formación',   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => 'Miguel',     'last_name' => 'Ortega Ruiz',     'email' => 'miguel.ortega@example.com','job_title' => 'Responsable IT',       'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'IT'],
            ['name' => 'Elena',      'last_name' => 'Castro Prieto',   'email' => 'elena.castro@example.com','job_title' => 'Desarrolladora IT',    'role' => 'super_admin',             'area' => 'Negocio',    'department' => 'IT'],
            ['name' => 'Diego',      'last_name' => 'Vega Morales',    'email' => 'diego.vega@example.com',  'job_title' => 'Técnico Igualdad',     'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Consultoría Igualdad'],
        ];

        // 3. Crear / actualizar usuarios y sincronizar su rol
        foreach ($users as $u) {
            // Buscar la unidad organizativa por el nombre del departamento
            $orgUnit = \App\Models\OrganizationalUnit::where('name', $u['department'])->first();

            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'                   => $u['name'],
                    'last_name'              => $u['last_name'],
                    'job_title'              => $u['job_title'],
                    'area'                   => $u['area'],
                    'department'             => $u['department'],
                    'organizational_unit_id' => $orgUnit?->id,
                    'is_active'              => true,
                    'email_verified_at'      => now(),
                    'password'               => Hash::make('password'),
                ]
            );

            // syncRoles evita roles duplicados al re-ejecutar el seeder
            if (!empty($u['role'])) {
                $user->syncRoles([$u['role']]);
            }
        }
    }
}
