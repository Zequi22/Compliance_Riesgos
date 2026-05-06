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
        Schema::create('risk_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('risk_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('classification', ['risk_evidence', 'control_evidence', 'action_evidence']);
            $table->string('file_path');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('risk_id')->references('id')->on('risks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_documents');
    }
};
