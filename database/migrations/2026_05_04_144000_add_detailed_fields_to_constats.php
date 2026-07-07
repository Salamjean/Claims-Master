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
        Schema::table('constats', function (Blueprint $table) {
            // Véhicule A
            $table->string('veh_a_marque')->nullable();
            $table->string('veh_a_type')->nullable();
            $table->string('veh_a_etat_general')->nullable();
            $table->string('veh_a_pneumatiques')->nullable();
            $table->string('veh_a_conducteur_nom')->nullable();
            $table->date('veh_a_conducteur_date_naissance')->nullable();
            $table->string('veh_a_conducteur_lieu_naissance')->nullable();
            $table->string('veh_a_conducteur_pere')->nullable();
            $table->string('veh_a_conducteur_mere')->nullable();
            $table->string('veh_a_conducteur_nationalite')->nullable();
            $table->string('veh_a_conducteur_tel')->nullable();
            $table->string('veh_a_conducteur_profession')->nullable();
            $table->string('veh_a_conducteur_domicile')->nullable();
            $table->string('veh_a_permis_numero')->nullable();
            $table->date('veh_a_permis_date')->nullable();
            $table->string('veh_a_permis_lieu')->nullable();
            $table->string('veh_a_permis_categories')->nullable();
            $table->date('veh_a_permis_validite')->nullable();
            $table->string('veh_a_proprietaire_nom')->nullable();
            $table->string('veh_a_proprietaire_bp')->nullable();
            $table->string('veh_a_proprietaire_tel')->nullable();
            $table->string('veh_a_assurance_nom')->nullable();
            $table->date('veh_a_assurance_debut')->nullable();
            $table->date('veh_a_assurance_fin')->nullable();
            $table->string('veh_a_police_numero')->nullable();
            $table->string('veh_a_attestation_numero')->nullable();
            $table->text('veh_a_degats_materiels')->nullable();

            // Véhicule B
            $table->string('veh_b_marque')->nullable();
            $table->string('veh_b_type')->nullable();
            $table->string('veh_b_etat_general')->nullable();
            $table->string('veh_b_pneumatiques')->nullable();
            $table->string('veh_b_conducteur_nom')->nullable();
            $table->date('veh_b_conducteur_date_naissance')->nullable();
            $table->string('veh_b_conducteur_lieu_naissance')->nullable();
            $table->string('veh_b_conducteur_pere')->nullable();
            $table->string('veh_b_conducteur_mere')->nullable();
            $table->string('veh_b_conducteur_nationalite')->nullable();
            $table->string('veh_b_conducteur_tel')->nullable();
            $table->string('veh_b_conducteur_profession')->nullable();
            $table->string('veh_b_conducteur_domicile')->nullable();
            $table->string('veh_b_permis_numero')->nullable();
            $table->date('veh_b_permis_date')->nullable();
            $table->string('veh_b_permis_lieu')->nullable();
            $table->string('veh_b_permis_categories')->nullable();
            $table->date('veh_b_permis_validite')->nullable();
            $table->string('veh_b_proprietaire_nom')->nullable();
            $table->string('veh_b_proprietaire_bp')->nullable();
            $table->string('veh_b_proprietaire_tel')->nullable();
            $table->string('veh_b_assurance_nom')->nullable();
            $table->date('veh_b_assurance_debut')->nullable();
            $table->date('veh_b_assurance_fin')->nullable();
            $table->string('veh_b_police_numero')->nullable();
            $table->string('veh_b_attestation_numero')->nullable();
            $table->text('veh_b_degats_materiels')->nullable();

            // Victime
            $table->string('victime_nom')->nullable();
            $table->date('victime_date_naissance')->nullable();
            $table->string('victime_lieu_naissance')->nullable();
            $table->string('victime_nationalite')->nullable();
            $table->string('victime_pere')->nullable();
            $table->string('victime_mere')->nullable();
            $table->string('victime_profession')->nullable();
            $table->string('victime_domicile')->nullable();
            $table->text('victime_blessures')->nullable();
            $table->string('victime_passager_vehicule')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn([
                'veh_a_marque', 'veh_a_type', 'veh_a_etat_general', 'veh_a_pneumatiques',
                'veh_a_conducteur_nom', 'veh_a_conducteur_date_naissance', 'veh_a_conducteur_lieu_naissance',
                'veh_a_conducteur_pere', 'veh_a_conducteur_mere', 'veh_a_conducteur_nationalite', 'veh_a_conducteur_tel',
                'veh_a_conducteur_profession', 'veh_a_conducteur_domicile',
                'veh_a_permis_numero', 'veh_a_permis_date', 'veh_a_permis_lieu', 'veh_a_permis_categories', 'veh_a_permis_validite',
                'veh_a_proprietaire_nom', 'veh_a_proprietaire_bp', 'veh_a_proprietaire_tel',
                'veh_a_assurance_nom', 'veh_a_assurance_debut', 'veh_a_assurance_fin',
                'veh_a_police_numero', 'veh_a_attestation_numero', 'veh_a_degats_materiels',

                'veh_b_marque', 'veh_b_type', 'veh_b_etat_general', 'veh_b_pneumatiques',
                'veh_b_conducteur_nom', 'veh_b_conducteur_date_naissance', 'veh_b_conducteur_lieu_naissance',
                'veh_b_conducteur_pere', 'veh_b_conducteur_mere', 'veh_b_conducteur_nationalite', 'veh_b_conducteur_tel',
                'veh_b_conducteur_profession', 'veh_b_conducteur_domicile',
                'veh_b_permis_numero', 'veh_b_permis_date', 'veh_b_permis_lieu', 'veh_b_permis_categories', 'veh_b_permis_validite',
                'veh_b_proprietaire_nom', 'veh_b_proprietaire_bp', 'veh_b_proprietaire_tel',
                'veh_b_assurance_nom', 'veh_b_assurance_debut', 'veh_b_assurance_fin',
                'veh_b_police_numero', 'veh_b_attestation_numero', 'veh_b_degats_materiels',

                'victime_nom', 'victime_date_naissance', 'victime_lieu_naissance', 'victime_nationalite',
                'victime_pere', 'victime_mere', 'victime_profession', 'victime_domicile',
                'victime_blessures', 'victime_passager_vehicule'
            ]);
        });
    }
};
