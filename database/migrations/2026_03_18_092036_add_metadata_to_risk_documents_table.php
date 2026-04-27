<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_documents', function (Blueprint $table) {
            $table->string('document_type')->after('description')->nullable();
            $table->date('document_date')->after('document_type')->nullable();
            // Para la relacion con controles y acciones
            $table->foreignId('control_id')->nullable()->after('document_date')->constrained('controls')->nullOnDelete();
            $table->foreignId('action_id')->nullable()->after('control_id')->constrained('actions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('risk_documents', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['action_id']);
            
            $table->dropColumn(['document_type', 'document_date', 'control_id', 'action_id']);
        });
    }
};
