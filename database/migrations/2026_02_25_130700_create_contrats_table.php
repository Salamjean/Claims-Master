<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('numero_contrat')->unique();
            $table->string('type_contrat');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('prime', 12, 2)->nullable();
            $table->string('statut')->default('actif');
            $table->string('document_pdf')->nullable();
            $table->text('resume_ia')->nullable();
            
            // Garanties & Paiement
            $table->json('garanties')->nullable();
            $table->decimal('franchise', 12, 2)->nullable();
            $table->boolean('prime_payee')->default(true);

            // Véhicule & Assurance
            $table->foreignId('assurance_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('plaque')->nullable();
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();
            $table->string('type_vehicule')->nullable();
            $table->string('immatriculation')->nullable();
            $table->string('attestation_assurance')->nullable();
            $table->string('attestation_ai_status')->default('pending');
            $table->text('attestation_ai_feedback')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
