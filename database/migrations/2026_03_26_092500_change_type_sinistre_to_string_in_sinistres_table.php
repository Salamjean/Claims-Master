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
        Schema::table('sinistres', function (Blueprint $table) {
            $table->string('type_sinistre')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            // Revenir à l'enum si nécessaire (attention aux données existantes qui pourraient ne plus correspondre)
            $table->enum('type_sinistre', [
                'Vol',
                'Incendie',
                'Accident_matériel',
                'Accident_corporel',
                'Bris_de_glace',
                'Autre'
            ])->default('Accident_matériel')->change();
        });
    }
};
