<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->string('declarant_nom')->nullable()->after('lieu');
            $table->string('declarant_contact')->nullable()->after('declarant_nom');
            $table->string('token_suivi')->nullable()->unique()->after('declarant_contact');
        });
    }

    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropColumn(['declarant_nom', 'declarant_contact', 'token_suivi']);
        });
    }
};
