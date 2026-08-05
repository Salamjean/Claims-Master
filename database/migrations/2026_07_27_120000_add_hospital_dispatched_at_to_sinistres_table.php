<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            if (!Schema::hasColumn('sinistres', 'hospital_dispatched_at')) {
                $table->timestamp('hospital_dispatched_at')->nullable()->after('hospital_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sinistres', function (Blueprint $table) {
            if (Schema::hasColumn('sinistres', 'hospital_dispatched_at')) {
                $table->dropColumn('hospital_dispatched_at');
            }
        });
    }
};
