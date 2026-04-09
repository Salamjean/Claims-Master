<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->string('wave_session_id')->nullable()->after('montant_a_payer');
        });
    }

    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn('wave_session_id');
        });
    }
};
