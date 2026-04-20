<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_service_id')->constrained('demande_services')->onDelete('cascade');
            $table->string('numero_facture')->unique();
            $table->decimal('montant', 12, 0);
            $table->string('statut')->default('en_attente'); // en_attente, paye, annule
            $table->dateTime('date_echeance')->nullable();
            $table->dateTime('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_factures');
    }
};
