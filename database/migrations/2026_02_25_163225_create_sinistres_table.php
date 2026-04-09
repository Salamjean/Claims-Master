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
        Schema::create('sinistres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contrat_id')->nullable()->constrained('contrats')->onDelete('set null');
            
            $table->enum('type_sinistre', [
                'Vol',
                'Incendie',
                'Accident_matériel',
                'Accident_corporel',
                'Bris_de_glace',
                'Autre'
            ])->default('Accident_matériel');

            $table->string('methode_constat')->nullable();
            $table->boolean('assistance_sollicitee')->default(false);
            $table->string('nom_assisteur')->nullable();

            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('photos')->nullable();
            $table->enum('status', ['en_attente', 'en_cours', 'cloture'])->default('en_attente');
            
            // IA & Workflow
            $table->string('ai_analysis_status')->nullable()->default('pending');
            $table->json('ai_analysis_report')->nullable();
            $table->string('workflow_step')->default('declaration');

            // Relations & Services
            $table->foreignId('assigned_service_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assurance_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('expert_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('garage_id')->nullable()->constrained('users')->onDelete('set null');

            // Détails Sinistre
            $table->string('numero_sinistre')->nullable()->unique();
            $table->dateTime('date_survenance')->nullable();
            $table->dateTime('date_declaration')->nullable();
            $table->dateTime('date_ouverture')->nullable();
            $table->enum('moyen_declaration', ['email', 'courrier', 'accueil'])->nullable();
            $table->boolean('est_couvert')->nullable();
            $table->text('motif_rejet')->nullable();

            // Workflow Tracking Dates
            $table->dateTime('date_mandat_expert')->nullable();
            $table->dateTime('date_rapport_expert')->nullable();
            $table->dateTime('date_bon_prise_charge')->nullable();
            $table->dateTime('date_bon_sortie')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinistres');
    }
};
