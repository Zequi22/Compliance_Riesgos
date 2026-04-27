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
    Schema::create('risks', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('category', 100)->nullable();
        $table->string('area_process', 100)->nullable();
        $table->string('owner', 100)->nullable();
        $table->unsignedBigInteger('responsable_id')->nullable();
        $table->enum('status', ['Identificado', 'Evaluado', 'Mitigado'])->default('Identificado');
        $table->string('type_crime')->nullable();
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
