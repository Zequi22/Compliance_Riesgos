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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->after('team')
                ->constrained('organizational_units')
                ->nullOnDelete();
        });

        Schema::table('controls', function (Blueprint $table) {
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->after('due_date')
                ->constrained('organizational_units')
                ->nullOnDelete();
        });

        Schema::table('actions', function (Blueprint $table) {
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->after('status')
                ->constrained('organizational_units')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignId(['organizational_unit_id']);
            $table->dropColumn('organizational_unit_id');
        });

        Schema::table('controls', function (Blueprint $table) {
            $table->dropForeignId(['organizational_unit_id']);
            $table->dropColumn('organizational_unit_id');
        });

        Schema::table('actions', function (Blueprint $table) {
            $table->dropForeignId(['organizational_unit_id']);
            $table->dropColumn('organizational_unit_id');
        });
    }
};
