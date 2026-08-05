<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('etat_des_lieux');

        Schema::create('etat_des_lieux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained('sinistres')->cascadeOnDelete();
            $table->foreignId('groupe_id')->constrained('users')->cascadeOnDelete();

            // 1. Informations générales
            $table->string('numero_intervention')->nullable();
            $table->dateTime('date_heure_alerte')->nullable();
            $table->string('heure_depart_caserne')->nullable();
            $table->string('heure_arrivee_lieux')->nullable();
            $table->string('heure_fin_intervention')->nullable();
            $table->string('lieu_exact')->nullable();

            // 2. Nature de l'intervention
            $table->string('nature_intervention')->nullable();

            // 3. Informations sur le sinistre
            $table->text('description_situation')->nullable();
            $table->string('cause_presumee')->nullable();
            $table->string('niveau_gravite')->nullable();
            $table->text('risques_identifies')->nullable();
            $table->string('conditions_meteo')->nullable();

            // 4. Victimes (JSON Array)
            $table->json('victimes')->nullable();

            // 5. Véhicules impliqués (JSON Array)
            $table->json('vehicules_impliques')->nullable();

            // 6. Dégâts matériels
            $table->text('biens_endommages')->nullable();
            $table->text('batiments_touches')->nullable();
            $table->string('surface_brulee')->nullable();
            $table->string('estimation_degats')->nullable();
            $table->text('biens_sauves')->nullable();

            // 7. Moyens engagés
            $table->string('casernes_mobilisees')->nullable();
            $table->json('vehicules_utilises')->nullable();
            $table->string('nombre_pompiers')->nullable();
            $table->text('materiel_utilise')->nullable();
            $table->string('quantite_eau_utilisee')->nullable();
            $table->string('produits_extincteurs_utilises')->nullable();

            // 8. Actions réalisées (JSON Array)
            $table->json('actions_realisees')->nullable();

            // 9. Autorités présentes (JSON Array)
            $table->json('autorites_presentes')->nullable();

            // 10. Témoins (JSON Array)
            $table->json('temoins')->nullable();

            // 11. Chronologie (JSON Array)
            $table->json('chronologie')->nullable();

            // 12. Conclusion
            $table->string('situation_maitrisee')->nullable();
            $table->text('cause_probable')->nullable();
            $table->text('recommandations')->nullable();
            $table->text('suites_a_donner')->nullable();

            $table->timestamps();

            $table->unique('sinistre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etat_des_lieux');
    }
};
