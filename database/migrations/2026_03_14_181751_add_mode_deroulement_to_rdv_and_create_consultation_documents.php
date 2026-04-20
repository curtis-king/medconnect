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
        Schema::table('rendez_vous_professionnels', function (Blueprint $table) {
            $table->enum('mode_deroulement', ['presentiel', 'teleconsultation'])->default('presentiel')->nullable()->after('type_rendez_vous');
            $table->string('lien_teleconsultation_patient')->nullable()->after('mode_deroulement');
        });

        Schema::create('consultation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_professionnelle_id')
                ->constrained('consultation_professionnelles')
                ->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('nom_fichier');
            $table->string('file_path');
            $table->unsignedBigInteger('taille_octets')->nullable();
            $table->string('mime_type')->nullable();
            $table->enum('source', ['patient', 'professionnel', 'backoffice'])->default('professionnel');
            $table->timestamps();

            $table->index('consultation_professionnelle_id', 'consult_doc_consult_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_documents');

        Schema::table('rendez_vous_professionnels', function (Blueprint $table) {
            $table->dropColumn(['mode_deroulement', 'lien_teleconsultation_patient']);
        });
    }
};
