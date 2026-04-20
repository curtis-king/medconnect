<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous_professionnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_professionnel_id')->constrained('dossiers_professionnels')->cascadeOnDelete();
            $table->foreignId('service_professionnel_id')->nullable()->constrained('services_professionnels')->nullOnDelete();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('dossier_medical_id')->nullable()->constrained('dossiers_medicaux')->nullOnDelete();
            $table->string('reference')->unique();
            $table->enum('type_demande', ['consultation', 'examen', 'autre'])->default('consultation');
            $table->enum('statut', ['en_attente', 'accepte', 'decline', 'termine', 'annule'])->default('en_attente');
            $table->dateTime('date_proposee');
            $table->text('motif')->nullable();

            // Données cliniques renseignées par le patient lors de la demande
            $table->decimal('temperature', 4, 1)->nullable();       // ex: 37.5
            $table->decimal('poids', 5, 2)->nullable();             // en kg, ex: 70.50
            $table->string('tension_arterielle')->nullable();        // ex: "120/80"
            $table->text('symptomes')->nullable();

            $table->text('notes_patient')->nullable();
            $table->text('notes_professionnel')->nullable();
            $table->timestamp('decision_le')->nullable();
            $table->timestamps();

            $table->index(['dossier_professionnel_id', 'statut']);
            $table->index(['patient_user_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous_professionnels');
    }
};
