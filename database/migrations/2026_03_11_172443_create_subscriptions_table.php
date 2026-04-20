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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_medical_id')->constrained('dossiers_medicaux')->onDelete('cascade');
            $table->foreignId('frais_id')->constrained('frais')->onDelete('restrict');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_mois')->default(1);
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['actif', 'expire', 'annule'])->default('actif');
            $table->string('mode_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->foreignId('encaisse_par_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['dossier_medical_id', 'statut']);
            $table->index(['date_fin', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
