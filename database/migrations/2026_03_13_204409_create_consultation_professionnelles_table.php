<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_professionnelles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rendez_vous_professionnel_id');
            $table->foreign('rendez_vous_professionnel_id', 'fk_consult_rdv')
                ->references('id')->on('rendez_vous_professionnels')->cascadeOnDelete();
            $table->unique('rendez_vous_professionnel_id', 'uq_consult_rdv');
            $table->foreignId('dossier_professionnel_id')->constrained('dossiers_professionnels')->cascadeOnDelete();
            $table->foreignId('dossier_medical_id')->nullable()->constrained('dossiers_medicaux')->nullOnDelete();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type_service', ['consultation', 'examen', 'autre'])->default('consultation');

            // Champs remplis par le professionnel (tous types)
            $table->text('diagnostic')->nullable();
            $table->text('recommandations')->nullable();
            $table->text('ordonnance')->nullable();
            $table->text('observations')->nullable();

            // Champs spécifiques aux examens
            $table->string('fichier_resultat_path')->nullable(); // fichier joint (image, PDF, etc.)
            $table->text('note_resultat')->nullable();           // note optionnelle sur le résultat

            $table->enum('statut', ['brouillon', 'finalise'])->default('brouillon');
            $table->timestamp('finalise_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_professionnelles');
    }
};
