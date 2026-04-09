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
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('carte_grise')->nullable()->after('attestation_ai_feedback');
            $table->string('visite_technique')->nullable()->after('carte_grise');
            $table->string('permis_conduire')->nullable()->after('visite_technique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['carte_grise', 'visite_technique', 'permis_conduire']);
        });
    }
};
