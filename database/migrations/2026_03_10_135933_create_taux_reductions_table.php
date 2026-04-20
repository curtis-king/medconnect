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
        Schema::create('taux_reductions', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->decimal('taux', 5, 2); // Pourcentage (ex: 15.50 pour 15.50%)
            $table->enum('type', ['inscription', 'reabonnement', 'contribution', 'special']);
            $table->text('detail')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taux_reductions');
    }
};
