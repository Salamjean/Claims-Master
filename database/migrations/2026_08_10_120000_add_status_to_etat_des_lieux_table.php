<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('etat_des_lieux', function (Blueprint $table) {
            if (!Schema::hasColumn('etat_des_lieux', 'status')) {
                $table->string('status')->default('en_attente');
            }
            if (!Schema::hasColumn('etat_des_lieux', 'validated_at')) {
                $table->dateTime('validated_at')->nullable();
            }
            if (!Schema::hasColumn('etat_des_lieux', 'validated_by')) {
                $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('etat_des_lieux', function (Blueprint $table) {
            if (Schema::hasColumn('etat_des_lieux', 'validated_by')) {
                $table->dropForeign(['validated_by']);
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('etat_des_lieux', 'status') ? 'status' : null,
                Schema::hasColumn('etat_des_lieux', 'validated_at') ? 'validated_at' : null,
                Schema::hasColumn('etat_des_lieux', 'validated_by') ? 'validated_by' : null,
            ]));
        });
    }
};
