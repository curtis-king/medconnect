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
        Schema::create('dossiers_medicaux', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('numero_unique')->unique();

            $table->enum('source_creation', ['guichet', 'en_ligne']);

            $table->boolean('actif')->default(true);

            $table->boolean('partage_actif')->default(false);
            $table->string('code_partage')->nullable()->unique();
            $table->timestamp('partage_active_le')->nullable();

            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();

            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();

            $table->string('groupe_sanguin')->nullable();
            $table->text('allergies')->nullable();
            $table->text('maladies_chroniques')->nullable();
            $table->text('traitements_en_cours')->nullable();
            $table->text('antecedents_familiaux')->nullable();
            $table->text('antecedents_personnels')->nullable();

            $table->string('contact_urgence_nom')->nullable();
            $table->string('contact_urgence_telephone')->nullable();
            $table->string('contact_urgence_relation')->nullable();

            $table->enum('type_piece_identite', ['cni', 'passeport', 'permis', 'autre'])->nullable();
            $table->string('numero_piece_identite')->nullable();
            $table->date('date_expiration_piece_identite')->nullable();
            $table->string('piece_identite_recto_path')->nullable();
            $table->string('piece_identite_verso_path')->nullable();

            $table->string('photo_profil_path')->nullable();

            $table->foreignId('frais_inscription_id')->nullable()->constrained('frais_inscriptions')->nullOnDelete();

            $table->enum('statut_paiement_inscription', ['en_attente', 'paye', 'exonere'])->default('en_attente');
            $table->enum('mode_paiement_inscription', ['cash', 'en_ligne', 'mobile_money', 'carte'])->nullable();
            $table->string('reference_paiement_inscription')->nullable();
            $table->foreignId('encaisse_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('encaisse_le')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers_medicaux');
    }
};
