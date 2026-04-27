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
        Schema::table('actions', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('responsable_id');
            $table->date('commitment_date')->nullable()->after('start_date');
            $table->date('actual_closure_date')->nullable()->after('commitment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'commitment_date', 'actual_closure_date']);
        });
    }
};
