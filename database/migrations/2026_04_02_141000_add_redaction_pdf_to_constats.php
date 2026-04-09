<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->string('redaction_pdf')->nullable()->after('redaction_contenu');
        });
    }

    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropColumn('redaction_pdf');
        });
    }
};
