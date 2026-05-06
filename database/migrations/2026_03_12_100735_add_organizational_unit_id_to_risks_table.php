<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->after('category')
                ->constrained('organizational_units')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\OrganizationalUnit::class);
            $table->dropColumn('organizational_unit_id');
        });
    }
};
