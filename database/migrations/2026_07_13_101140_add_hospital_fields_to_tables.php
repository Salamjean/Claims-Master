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
            $table->boolean('has_ambulance')->default(false)->after('longitude');
        });

        Schema::table('sinistres', function (Blueprint $table) {
            $table->foreignId('nearest_hospital_id')->nullable()->after('assigned_personnel_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('constats', function (Blueprint $table) {
            $table->foreignId('hospital_id')->nullable()->after('sinistre_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropColumn('hospital_id');
        });

        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropForeign(['nearest_hospital_id']);
            $table->dropColumn('nearest_hospital_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_ambulance');
        });
    }
};
