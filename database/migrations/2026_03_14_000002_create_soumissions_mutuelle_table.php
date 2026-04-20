<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soumissions_mutuelle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_professionnelle_id')->constrained('facture_professionnelles')->cascadeOnDelete();
            $table->foreignId('dossier_medical_id')->constrained('dossiers_medicaux')->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->restrictOnDelete();
            $table->string('reference')->unique();
            $table->decimal('montant_soumis', 10, 2);
            $table->decimal('montant_pris_en_charge', 10, 2)->default(0); // accordé par la mutuelle
            $table->decimal('montant_rejete', 10, 2)->default(0);         // rejeté ou hors plafond
            $table->enum('statut', ['soumis', 'en_traitement', 'approuve', 'rejete', 'partiel'])->default('soumis');
            $table->timestamp('date_soumission')->useCurrent();
            $table->timestamp('date_traitement')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['dossier_medical_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soumissions_mutuelle');
    }
};
