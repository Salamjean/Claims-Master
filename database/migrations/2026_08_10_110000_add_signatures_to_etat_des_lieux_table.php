<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('etat_des_lieux', function (Blueprint $table) {
            if (!Schema::hasColumn('etat_des_lieux', 'signature_agent')) {
                $table->longText('signature_agent')->nullable()->after('suites_a_donner');
            }
            if (!Schema::hasColumn('etat_des_lieux', 'nom_agent_signataire')) {
                $table->string('nom_agent_signataire')->nullable()->after('signature_agent');
            }
            if (!Schema::hasColumn('etat_des_lieux', 'signature_assure')) {
                $table->longText('signature_assure')->nullable()->after('nom_agent_signataire');
            }
            if (!Schema::hasColumn('etat_des_lieux', 'nom_assure_signataire')) {
                $table->string('nom_assure_signataire')->nullable()->after('signature_assure');
            }
        });
    }

    public function down(): void
    {
        Schema::table('etat_des_lieux', function (Blueprint $table) {
            $table->dropColumn([
                'signature_agent',
                'nom_agent_signataire',
                'signature_assure',
                'nom_assure_signataire'
            ]);
        });
    }
};
