<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('constats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained('sinistres')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('users')->onDelete('cascade'); // police/gendarmerie ou service d'agent user
            $table->enum('type_constat', ['accident', 'general'])->default('general');

            // ── Champs simplifiés ──
            $table->string('lieu')->nullable();
            $table->dateTime('date_heure')->nullable();
            $table->text('description_faits')->nullable(); 
            $table->text('dommages')->nullable();
            $table->text('observations')->nullable();
            $table->text('temoins')->nullable();

            // ── Fichiers ──
            $table->longText('croquis')->nullable(); // On autorise le Base64 long ou le chemin
            $table->string('ass1_photo')->nullable(); // Photo assurance A
            $table->string('ass2_photo')->nullable(); // Photo assurance B
            $table->json('photos_plus')->nullable(); // Autres photos si besoin

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constats');
    }
};
