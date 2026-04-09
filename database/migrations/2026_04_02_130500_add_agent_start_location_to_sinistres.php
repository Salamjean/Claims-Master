<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->decimal('agent_start_lat', 10, 8)->nullable()->after('assigned_service_id');
            $table->decimal('agent_start_lng', 11, 8)->nullable()->after('agent_start_lat');
        });
    }

    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            $table->dropColumn(['agent_start_lat', 'agent_start_lng']);
        });
    }
};
