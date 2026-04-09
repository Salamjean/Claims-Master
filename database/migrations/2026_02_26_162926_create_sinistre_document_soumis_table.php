<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinistre_document_soumis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_document_attendu_id')->constrained('sinistre_document_attendus', 'id', 'fk_sds_sda_id')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->text('file_value')->nullable();
            $table->string('ai_compliance_status')->nullable()->default('pending');
            $table->text('ai_feedback')->nullable();
            $table->string('manager_override_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinistre_document_soumis');
    }
};
