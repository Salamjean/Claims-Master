<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->boolean('recupere_par_assure')->default(false)->after('redaction_validee_at');
            $table->timestamp('recupere_at')->nullable()->after('recupere_par_assure');
        });
    }

    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn(['recupere_par_assure', 'recupere_at']);
        });
    }
};
