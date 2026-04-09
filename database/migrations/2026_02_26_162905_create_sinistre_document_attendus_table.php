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
        Schema::create('sinistre_document_attendus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained('sinistres')->onDelete('cascade');
            $table->string('nom_document');
            $table->string('type_champ')->default('file');
            $table->boolean('is_mandatory')->default(true);
            $table->string('status_client')->default('pending'); // pending, uploaded
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinistre_document_attendus');
    }
};
