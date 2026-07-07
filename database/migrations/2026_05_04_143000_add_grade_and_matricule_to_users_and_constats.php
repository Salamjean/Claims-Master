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
        Schema::table('users', function (Blueprint $table) {
            $table->string('grade')->nullable()->after('prenom');
            $table->string('matricule')->nullable()->after('grade');
        });

        Schema::table('constats', function (Blueprint $table) {
            $table->string('agent_nom')->nullable()->after('service_id');
            $table->string('agent_grade')->nullable()->after('agent_nom');
            $table->string('agent_matricule')->nullable()->after('agent_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['grade', 'matricule']);
        });

        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn(['agent_nom', 'agent_grade', 'agent_matricule']);
        });
    }
};
