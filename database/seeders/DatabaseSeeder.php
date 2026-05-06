<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OrganizationalUnitSeeder::class, // Debe ir ANTES de UserSeeder
            UserSeeder::class,
            RiskSeeder::class,
            ActionSeeder::class,
        ]);
    }
}
