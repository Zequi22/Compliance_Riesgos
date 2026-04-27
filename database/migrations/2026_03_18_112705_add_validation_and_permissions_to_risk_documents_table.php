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
        Schema::table('risk_documents', function (Blueprint $table) {
            $table->string('status')->default('pendiente')->after('file_path');
            $table->foreignId('uploaded_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->after('uploaded_by')->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            $table->text('validation_comment')->nullable()->after('validated_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['status', 'uploaded_by', 'validated_by', 'validated_at', 'validation_comment']);
            $table->dropSoftDeletes();
        });
    }
};
