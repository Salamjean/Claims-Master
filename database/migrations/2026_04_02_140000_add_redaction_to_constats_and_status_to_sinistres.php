<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter le statut intermédiaire 'constat_terrain_ok' dans sinistres
        DB::statement("ALTER TABLE sinistres MODIFY COLUMN status ENUM('en_attente', 'en_cours', 'constat_terrain_ok', 'traite', 'cloture') DEFAULT 'en_attente'");

        // 2. Ajouter les champs de rédaction dans constats
        Schema::table('constats', function (Blueprint $table) {
            $table->boolean('terrain_valide')->default(false)->after('photos_plus');
            $table->text('redaction_contenu')->nullable()->after('terrain_valide');
            $table->boolean('redaction_validee')->default(false)->after('redaction_contenu');
            $table->timestamp('redaction_validee_at')->nullable()->after('redaction_validee');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sinistres MODIFY COLUMN status ENUM('en_attente', 'en_cours', 'traite', 'cloture') DEFAULT 'en_attente'");

        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn(['terrain_valide', 'redaction_contenu', 'redaction_validee', 'redaction_validee_at']);
        });
    }
};
