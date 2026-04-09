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
        Schema::create('assurance_profiles', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers la table users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Informations RCCM
            $table->string('numero_rccm')->nullable()->comment('Numéro du Registre du Commerce');
            $table->string('path_rccm')->nullable()->comment('Chemin vers le fichier de la fiche RCCM');

            // Informations DFE
            $table->string('numero_dfe')->nullable()->comment('Numéro du Dossier Fiscal de l\'Entreprise');
            $table->string('path_dfe')->nullable()->comment('Chemin vers le fichier de la fiche DFE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assurance_profiles');
    }
};
