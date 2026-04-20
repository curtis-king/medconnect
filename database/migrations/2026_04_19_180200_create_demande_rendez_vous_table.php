<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_service_id')->constrained('demande_services')->onDelete('cascade');
            $table->dateTime('date_rendez_vous');
            $table->string('lieu')->nullable();
            $table->text('adresse')->nullable();
            $table->string('status')->default('planifie'); // planifie, confirme, annule, realise
            $table->foreignId('professional_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_rendez_vous');
    }
};
