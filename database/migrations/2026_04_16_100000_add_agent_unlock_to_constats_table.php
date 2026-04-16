<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->boolean('agent_unlocked')->default(false)->after('wave_session_id');
            $table->timestamp('agent_unlocked_at')->nullable()->after('agent_unlocked');
            $table->unsignedBigInteger('agent_unlocked_by')->nullable()->after('agent_unlocked_at');
            $table->foreign('agent_unlocked_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('constats', function (Blueprint $table) {
            $table->dropForeign(['agent_unlocked_by']);
            $table->dropColumn(['agent_unlocked', 'agent_unlocked_at', 'agent_unlocked_by']);
        });
    }
};
