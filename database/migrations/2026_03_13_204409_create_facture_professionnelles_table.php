<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facture_professionnelles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rendez_vous_professionnel_id');
            $table->foreign('rendez_vous_professionnel_id', 'fk_facture_rdv')
                ->references('id')->on('rendez_vous_professionnels')->cascadeOnDelete();
            $table->unique('rendez_vous_professionnel_id', 'uq_facture_rdv');
            $table->foreignId('dossier_professionnel_id')->constrained('dossiers_professionnels')->cascadeOnDelete();
            $table->foreignId('service_professionnel_id')->nullable()->constrained('services_professionnels')->nullOnDelete();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('type_service')->nullable();

            // Montants
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_couvert_mutuelle', 10, 2)->default(0); // part prise en charge par la mutuelle
            $table->decimal('montant_a_charge_patient', 10, 2)->default(0); // part restant à payer par le patient

            // Statuts
            $table->enum('statut', ['emise', 'soumise_mutuelle', 'partiellement_payee', 'payee', 'annulee'])->default('emise');
            $table->enum('statut_mutuelle', ['non_soumis', 'en_attente', 'approuve', 'rejete', 'partiel'])->default('non_soumis');
            $table->enum('statut_paiement_patient', ['en_attente', 'paye', 'exonere'])->default('en_attente');

            $table->timestamp('emise_le')->nullable();
            $table->timestamp('payee_le')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_user_id', 'statut']);
            $table->index(['dossier_professionnel_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facture_professionnelles');
    }
};
