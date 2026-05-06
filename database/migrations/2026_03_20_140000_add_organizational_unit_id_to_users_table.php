<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La columna ya existe desde 2026_03_17_132440_add_organizational_unit_id_to_user_control_action.php
        // Esta migración es un no-op para evitar errores si el fresh migration la incluye
        if (Schema::hasColumn('users', 'organizational_unit_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->after('department')
                ->constrained('organizational_units')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // No hacemos nada en down para no interferir con la migración original
    }
};
