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
        // 1. Suppression des anciennes relations/colonnes
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

        // 2. Ajout des nouveaux champs textuels
        Schema::table('sinistres', function (Blueprint $table) {
            $table->string('nearest_hospital_name')->nullable()->after('assigned_personnel_id');
            $table->string('nearest_hospital_contact')->nullable()->after('nearest_hospital_name');
            $table->string('nearest_hospital_adresse')->nullable()->after('nearest_hospital_contact');
            $table->double('nearest_hospital_distance')->nullable()->after('nearest_hospital_adresse');
        });

        Schema::table('constats', function (Blueprint $table) {
            $table->string('hospital_name')->nullable()->after('sinistre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn('hospital_name');
            $table->foreignId('hospital_id')->nullable()->after('sinistre_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropColumn([
                'nearest_hospital_name',
                'nearest_hospital_contact',
                'nearest_hospital_adresse',
                'nearest_hospital_distance'
            ]);
            $table->foreignId('nearest_hospital_id')->nullable()->after('assigned_personnel_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_ambulance')->default(false)->after('longitude');
        });
    }
};
