<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions_professionnelles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_professionnel_id')->constrained('dossiers_professionnels')->cascadeOnDelete();
            $table->foreignId('frais_id')->constrained('frais')->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_mois')->default(1);
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['actif', 'expire', 'annule'])->default('actif');
            $table->string('mode_paiement')->nullable();
            $table->string('reference_paiement')->unique()->nullable();
            $table->foreignId('encaisse_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['dossier_professionnel_id', 'statut'], 'sub_pro_dossier_statut_idx');
            $table->index(['date_fin', 'statut'], 'sub_pro_date_fin_statut_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions_professionnelles');
    }
};
