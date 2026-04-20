<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services_professionnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_professionnel_id')->constrained('dossiers_professionnels')->cascadeOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->enum('type', ['consultation', 'examen', 'hospitalisation', 'chirurgie', 'urgence', 'autre'])->default('consultation');
            $table->decimal('prix', 10, 2);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services_professionnels');
    }
};
