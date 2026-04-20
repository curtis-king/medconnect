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
        Schema::table('paiements', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte de clé étrangère
            $table->dropForeign(['frais_inscription_id']);

            // Ajouter la nouvelle contrainte pointant vers la table frais
            $table->foreign('frais_inscription_id')->references('id')->on('frais')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Supprimer la nouvelle contrainte
            $table->dropForeign(['frais_inscription_id']);

            // Remettre l'ancienne contrainte pointant vers frais_inscriptions
            $table->foreign('frais_inscription_id')->references('id')->on('frais_inscriptions')->nullOnDelete();
        });
    }
};
