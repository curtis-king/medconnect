<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Demandes de retrait des gains par le professionnel auprès de la mutuelle
        Schema::create('retraits_professionnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_professionnel_id')->constrained('dossiers_professionnels')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->decimal('montant_demande', 10, 2);
            $table->decimal('montant_approuve', 10, 2)->nullable();
            $table->enum('statut', ['en_attente', 'approuve', 'rejete', 'paye'])->default('en_attente');
            $table->timestamp('date_demande')->useCurrent();
            $table->timestamp('date_traitement')->nullable();
            $table->timestamp('date_paiement')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['dossier_professionnel_id', 'statut']);
        });

        // Table pivot : factures incluses dans une demande de retrait
        Schema::create('retrait_facture_professionnelle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retrait_professionnel_id');
            $table->unsignedBigInteger('facture_professionnelle_id');
            $table->decimal('montant', 10, 2); // montant de la facture inclus dans ce retrait
            $table->timestamps();

            $table->foreign('retrait_professionnel_id', 'fk_rfp_retrait')
                ->references('id')->on('retraits_professionnels')->cascadeOnDelete();
            $table->foreign('facture_professionnelle_id', 'fk_rfp_facture')
                ->references('id')->on('facture_professionnelles')->cascadeOnDelete();

            $table->unique(
                ['retrait_professionnel_id', 'facture_professionnelle_id'],
                'uq_retrait_facture'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retrait_facture_professionnelle');
        Schema::dropIfExists('retraits_professionnels');
    }
};
