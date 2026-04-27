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
            ['name' => 'Jose Luis',  'last_name' => 'Moncayo Núñez',     'email' => 'jlmoncayo@talention.es',  'job_title' => 'CEO',                                'role' => 'super_admin',             'area' => 'Dirección',  'department' => 'Dirección'],
            ['name' => 'Hugo',       'last_name' => 'Moncayo Oliver',     'email' => 'hmoncayo@talention.es',   'job_title' => 'Responsable de Desarrollo de Negocio','role' => 'super_admin',             'area' => 'Dirección',  'department' => 'Dirección'],
            ['name' => 'Macarena',   'last_name' => 'Jiménez Andrés',     'email' => 'mjandres@talention.es',   'job_title' => 'Responsable Área Legal',              'role' => 'Responsable Compliance',  'area' => 'Operaciones','department' => 'Legal'],
            ['name' => 'Paola',      'last_name' => 'Aguilar Cano',       'email' => 'paguilar@talention.es',   'job_title' => 'Técnica Administración',              'role' => 'Técnico',                 'area' => 'Operaciones','department' => 'Administración'],
            ['name' => 'Noelia',     'last_name' => 'Camacho Diéguez',    'email' => 'ncamacho@talention.es',   'job_title' => 'Responsable Comercial',               'role' => 'Responsable Área',        'area' => 'Operaciones','department' => 'Comercial'],
            ['name' => 'Isabel',     'last_name' => 'Pineda Ballesteros',  'email' => 'ipineda@talention.es',    'job_title' => 'Técnica Comercial',                   'role' => 'Consulta',                'area' => 'Operaciones','department' => 'Comercial'],
            ['name' => 'Paula',      'last_name' => 'Machado Padilla',     'email' => 'pmachado@talention.es',   'job_title' => 'Responsable Formación',               'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => 'Rocío',      'last_name' => 'Sánchez Pineda',      'email' => 'rsanchez@talention.es',   'job_title' => 'Técnica Formación',                   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => 'María',      'last_name' => 'Carmona Valencia',    'email' => 'mcarmona@talention.es',   'job_title' => 'Técnica Formación',                   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => 'Ada',        'last_name' => 'Fournier Torres',     'email' => 'aftorres@talention.es',   'job_title' => 'Tutora',                              'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Formación'],
            ['name' => 'Varinia',    'last_name' => 'Fenoy González',      'email' => 'vfenoy@talention.es',     'job_title' => 'Responsable Igualdad',                'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Consultoría Igualdad'],
            ['name' => 'Ana',        'last_name' => 'Trujillo',            'email' => 'atrujillo@talention.es',  'job_title' => 'Técnica Igualdad',                    'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'Consultoría Igualdad'],
            ['name' => 'Israel',     'last_name' => 'Gómez',              'email' => 'igomez@talention.es',     'job_title' => 'Responsable Consultoría RRHH',        'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'Consultoría RRHH'],
            ['name' => 'Daniel',     'last_name' => 'Romero Ávila',        'email' => 'dromero@talention.es',    'job_title' => 'Responsable IT',                      'role' => 'Responsable Área',        'area' => 'Negocio',    'department' => 'IT'],
            ['name' => 'Ezequiel',   'last_name' => 'Campos',              'email' => 'ecampos@talention.es',    'job_title' => 'Desarrollador IT',                    'role' => 'super_admin',             'area' => 'Negocio',    'department' => 'IT'],
            ['name' => 'Gema',       'last_name' => 'Miranda Gómez',       'email' => 'gmiranda@talention.es',   'job_title' => 'Desarrolladora IT',                   'role' => 'Consulta',                'area' => 'Negocio',    'department' => 'IT'],
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
                    'password'               => Hash::make('12345678'),
                ]
            );

            // syncRoles evita roles duplicados al re-ejecutar el seeder
            if (!empty($u['role'])) {
                $user->syncRoles([$u['role']]);
            }
        }
    }
}
