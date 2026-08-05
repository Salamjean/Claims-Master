<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('etat_des_lieux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained('sinistres')->cascadeOnDelete();
            $table->foreignId('groupe_id')->constrained('users')->cascadeOnDelete();

            $table->string('activite_lors_accident')->nullable();
            $table->string('poste_travail_agent')->nullable();
            $table->string('tache_realisee')->nullable();
            $table->json('tache_caracteristiques')->nullable();
            $table->text('materiels_utilises')->nullable();
            $table->string('execution_tache_mode')->nullable();

            $table->string('element_materiel')->nullable();
            $table->json('proximite_accident')->nullable();
            $table->json('ambiance_physique')->nullable();
            $table->string('ambiance_autre_precision')->nullable();
            $table->text('recit_accident')->nullable();

            $table->string('lateralite_blessure')->nullable();
            $table->json('zones_blessees')->nullable();
            $table->string('hospitalise')->nullable();
            $table->string('premiers_soins')->nullable();
            $table->string('autres_victimes')->nullable();
            $table->string('sauvetage_collectif')->nullable();
            $table->string('acmo')->nullable();
            $table->text('propositions_amelioration')->nullable();

            $table->string('temoin_nom')->nullable();
            $table->string('temoin_prenom')->nullable();
            $table->string('temoin_adresse')->nullable();
            $table->string('temoin_telephone')->nullable();

            $table->json('blessures_constatees')->nullable();

            $table->string('filiere_code')->nullable();
            $table->string('activite_lese_code')->nullable();
            $table->string('element_materiel_code')->nullable();

            $table->string('agent_fait_a')->nullable();
            $table->date('agent_fait_le')->nullable();
            $table->string('agent_signature_nom')->nullable();

            $table->string('autorite_fait_a')->nullable();
            $table->date('autorite_fait_le')->nullable();
            $table->string('autorite_signature_nom')->nullable();

            $table->timestamps();

            $table->unique('sinistre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etat_des_lieux');
    }
};
