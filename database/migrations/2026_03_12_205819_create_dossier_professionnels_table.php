<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers_professionnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('raison_sociale')->nullable();
            $table->enum('type_structure', ['individuel', 'clinique', 'hopital', 'dispensaire', 'autre'])->default('individuel');
            $table->string('attestation_professionnelle_path')->nullable();
            $table->string('document_prise_de_fonction_path')->nullable();
            $table->string('NIU')->nullable();
            $table->string('forme_juridique')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'recale'])->default('en_attente');
            $table->string('numero_licence')->unique()->nullable();
            $table->foreignId('frais_id')->nullable()->constrained('frais')->nullOnDelete();
            $table->enum('statut_paiement_inscription', ['en_attente', 'paye', 'exonere'])->default('en_attente');
            $table->enum('mode_paiement_inscription', ['cash', 'mobile_money', 'virement', 'carte'])->nullable();
            $table->string('reference_paiement_inscription')->nullable();
            $table->foreignId('encaisse_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('encaisse_le')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers_professionnels');
    }
};
