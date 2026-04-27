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
    Schema::create('controls', function (Blueprint $table) {
        $table->id();
        $table->foreignId('risk_id')->constrained('risks')->onDelete('cascade')->onUpdate('cascade');
        $table->string('type', 50)->nullable();
        $table->string('frequency', 50)->nullable();
        $table->string('effectiveness', 50)->nullable();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('owner_name')->nullable();
        $table->string('owner_area')->nullable();
        $table->string('owner_department')->nullable();
        $table->string('owner_team')->nullable();
        $table->string('evidence')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controls');
    }
};
