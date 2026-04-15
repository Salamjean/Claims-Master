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
            $table->foreignId('assigned_personnel_id')->nullable()->after('assurance_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_personnel_at')->nullable()->after('assigned_personnel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropForeign(['assigned_personnel_id']);
            $table->dropColumn(['assigned_personnel_id', 'assigned_personnel_at']);
        });
    }
};
