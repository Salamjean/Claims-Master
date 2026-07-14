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
            $table->string('hospital_status')->default('en_attente')->after('nearest_hospital_id');
            $table->string('hospital_severity')->nullable()->after('hospital_status'); // 'leger', 'grave', 'deces'
            $table->text('hospital_notes')->nullable()->after('hospital_severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropColumn(['hospital_status', 'hospital_severity', 'hospital_notes']);
        });
    }
};
