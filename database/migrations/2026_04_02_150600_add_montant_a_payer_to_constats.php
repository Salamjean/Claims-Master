<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->integer('montant_a_payer')->default(0)->after('redaction_validee_at');
        });
    }

    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn('montant_a_payer');
        });
    }
};
