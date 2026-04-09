<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enrichir la table constats pour les infos de livraison
        Schema::table('constats', function (Blueprint $table) {
            $table->string('mode_retrait')->nullable()->comment('sur_place, livraison');
            $table->string('nom_destinataire')->nullable();
            $table->string('prenom_destinataire')->nullable();
            $table->string('email_destinataire')->nullable();
            $table->string('telephone_destinataire')->nullable();
            $table->text('adresse_livraison')->nullable();
            $table->string('ville_livraison')->nullable();
            $table->string('commune_livraison')->nullable();
            $table->string('quartier_livraison')->nullable();
            $table->date('date_livraison')->nullable();
            $table->time('heure_livraison')->nullable();
            $table->integer('montant_timbres')->default(500);
            $table->integer('montant_livraison')->default(1000);
            $table->string('statut_paiement')->default('pending')->comment('pending, success, failed');
        });

        // Table des paiements/transactions
        Schema::create('constat_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('constat_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->string('payment_method')->nullable()->comment('wave, orange, mtn, moov');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('status')->default('pending');
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constat_payments');
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn([
                'mode_retrait', 'nom_destinataire', 'prenom_destinataire', 
                'email_destinataire', 'telephone_destinataire', 'adresse_livraison',
                'ville_livraison', 'commune_livraison', 'quartier_livraison',
                'date_livraison', 'heure_livraison', 'montant_timbres', 
                'montant_livraison', 'statut_paiement'
            ]);
        });
    }
};
