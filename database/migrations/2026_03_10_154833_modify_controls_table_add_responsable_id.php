<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('controls', function (Blueprint $table) {
            // Agregar la columna responsable_id como foreign key
            $table->unsignedBigInteger('responsable_id')->nullable()->after('risk_id');
            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('set null');
            
            // Remover las columnas antiguas
            $table->dropColumn(['owner_name', 'owner_area', 'owner_team', 'owner_department']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('controls', function (Blueprint $table) {
            // Remover la foreign key y columna
            $table->dropForeign(['responsable_id']);
            $table->dropColumn('responsable_id');
            
            // Re-agregar las columnas antiguas
            $table->string('owner_name')->nullable();
            $table->string('owner_area')->nullable();
            $table->string('owner_team')->nullable();
            $table->string('owner_department')->nullable();
        });
    }
};
