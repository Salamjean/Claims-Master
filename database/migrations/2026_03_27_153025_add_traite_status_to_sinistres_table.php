<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // En MySQL/MariaDB, on modifie l'énumération via un statement brut.
        // On rajoute 'traite' entre 'en_cours' et 'cloture'.
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE sinistres MODIFY COLUMN status ENUM('en_attente', 'en_cours', 'traite', 'cloture') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On repasse à l'énumération d'origine.
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE sinistres MODIFY COLUMN status ENUM('en_attente', 'en_cours', 'cloture') DEFAULT 'en_attente'");
    }
};
