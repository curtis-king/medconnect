<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_service_id')->constrained('demande_services')->onDelete('cascade');
            $table->string('type'); // document, prescription, certificat, autre
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('taille_fichier')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_pieces_jointes');
    }
};
