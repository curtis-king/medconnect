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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dossier_medical_id')->constrained('dossiers_medicaux')->onDelete('cascade');

            $table->foreignId('frais_inscription_id')->nullable()->constrained('frais')->nullOnDelete();

            $table->enum('type_paiement', ['inscription', 'reabonnement'])->default('reabonnement');

            $table->decimal('montant', 10, 2);

            // Période couverte par ce paiement
            $table->date('periode_debut');
            $table->date('periode_fin');

            // Nombre de mois couverts par ce paiement
            $table->integer('nombre_mois')->default(1);

            $table->enum('statut', ['en_attente', 'paye', 'annule', 'rembourse'])->default('en_attente');

            $table->enum('mode_paiement', ['cash', 'en_ligne', 'mobile_money', 'carte', 'virement'])->nullable();

            $table->string('reference_paiement')->nullable()->unique();

            $table->text('notes')->nullable();

            // Qui a encaissé le paiement
            $table->foreignId('encaisse_par_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Dates importantes
            $table->timestamp('date_encaissement')->nullable();
            $table->timestamp('date_echeance')->nullable();

            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['dossier_medical_id', 'periode_debut', 'periode_fin']);
            $table->index(['statut', 'date_echeance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
