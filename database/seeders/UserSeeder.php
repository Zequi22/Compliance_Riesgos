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

        // 2. Definir usuarios
        $users = [
            ['name' => '***REMOVED***',  'last_name' => '***REMOVED*** Núñez',     'email' => 'u1612m@example.com',  'job_title' => 'CEO',                                'role' => 'super_admin',             'area' => 'Dirección',  'department' => 'Dirección'],
            ['name' => '***REMOVED***',       'last_name' => '***REMOVED*** Oliver',     'email' => 'u5286l@example.com',   'job_title' => 'Responsable de Desarrollo de Negocio','role' => 'super_admin',             'area' => 'Dirección',  'department' => 'Dirección'],
            ['name' => '***REMOVED***',   'last_name' => '***REMOVED***',     'email' => 'u4855z@example.com',   'job_title' => 'Responsable Área Legal',              'role' => 'Responsable Compliance',  'area' => 'Operaciones','department' => 'Legal'],
            ['name' => '***REMOVED***',      'last_name' => '***REMOVED***',       'email' => 'u2534c@example.com',   'job_title' => 'Técnica Administración',              'role' => 'Técnico',                 'area' => 'Operaciones','department' => 'Administración'],
            ['name' => '***REMOVED***',     'last_name' => '***REMOVED***',    'email' => 'u8638a@example.com',   'job_title' => 'Responsable Comercial',               'role' => 'Responsable Área',        'area' => 'Operaciones','department' => 'Comercial'],
            ['name' => '***REMOVED***',     'last_name' => '***REMOVED***',  'email' => 'u5542r@example.com',    'job_title' => 'Técnica Comercial',                   'role' => 'Consulta',                'area' => 'Operaciones','department' => 'Comercial'],
            ['name' => '***REMOVED***',      'last_name' => '***REMOVED***',     'email' => 'u6583i@example.com',   'job_title' => 'Responsable Formación',               'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => '***REMOVED***',      'last_name' => '***REMOVED***',      'email' => 'u2849g@example.com',   'job_title' => 'Técnica Formación',                   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => '***REMOVED***',      'last_name' => '***REMOVED***',    'email' => 'u8123z@example.com',   'job_title' => 'Técnica Formación',                   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => '***REMOVED***',        'last_name' => '***REMOVED***',     'email' => 'u7879h@example.com',   'job_title' => 'Tutora',                              'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => '***REMOVED***',    'last_name' => '***REMOVED***',      'email' => 'u2279k@example.com',     'job_title' => 'Responsable Igualdad',                'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Consultoría Igualdad'],
            ['name' => '***REMOVED***',        'last_name' => '***REMOVED***',            'email' => 'u6758k@example.com',  'job_title' => 'Técnica Igualdad',                    'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Consultoría Igualdad'],
            ['name' => '***REMOVED***',     'last_name' => '***REMOVED***',              'email' => 'u4366j@example.com',     'job_title' => 'Responsable Consultoría RRHH',        'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Consultoría RRHH'],
            ['name' => '***REMOVED***',     'last_name' => '***REMOVED***',        'email' => 'u1105e@example.com',    'job_title' => 'Responsable IT',                      'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'IT'],
            ['name' => '***REMOVED***',   'last_name' => '***REMOVED***',              'email' => 'u5993k@example.com',    'job_title' => 'Desarrollador IT',                    'role' => 'super_admin',             'area' => 'Negocio',    'department' => 'IT'],
            ['name' => '***REMOVED***',       'last_name' => '***REMOVED***',       'email' => 'u4298d@example.com',   'job_title' => 'Desarrolladora IT',                   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'IT'],
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
