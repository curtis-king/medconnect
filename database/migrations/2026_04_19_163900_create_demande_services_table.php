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
        Schema::create('demande_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_medical_id')->constrained('service_medicals')->onDelete('cascade');
            $table->foreignId('dossier_medical_id')->nullable()->constrained('dossiers_medicaux')->onDelete('set null');
            $table->enum('statut', ['en_attente', 'en_cours', 'valide', 'rejete', 'termine'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->text('reponse_backoffice')->nullable();
            $table->foreignId('traite_par_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('traite_le')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_services');
    }
};
