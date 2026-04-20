<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendez_vous_professionnels', function (Blueprint $table) {
            $table->foreignId('professionnel_user_id')->nullable()->after('dossier_professionnel_id')->constrained('users')->nullOnDelete();
            $table->date('date_proposee_jour')->nullable()->after('date_proposee');
            $table->time('heure_proposee')->nullable()->after('date_proposee_jour');
            $table->enum('statut_acceptation', ['en_attente', 'accepte', 'decline', 'annule'])->default('en_attente')->after('statut');
            $table->enum('type_rendez_vous', ['consultation', 'examen', 'autre'])->default('consultation')->after('type_demande');
            $table->string('numero_dossier_reference')->nullable()->after('dossier_medical_id');
        });

        DB::statement('ALTER TABLE rendez_vous_professionnels MODIFY patient_user_id BIGINT UNSIGNED NULL');

        DB::statement('UPDATE rendez_vous_professionnels SET date_proposee_jour = DATE(date_proposee), heure_proposee = TIME(date_proposee)');
        DB::statement('UPDATE rendez_vous_professionnels SET statut_acceptation = statut WHERE statut IN ("en_attente", "accepte", "decline", "annule")');
        DB::statement('UPDATE rendez_vous_professionnels SET type_rendez_vous = type_demande WHERE type_demande IN ("consultation", "examen", "autre")');

        Schema::table('consultation_professionnelles', function (Blueprint $table) {
            $table->enum('type_consultation', ['presentiel', 'visio_teleconsultation'])->default('presentiel')->after('type_service');
            $table->string('lien_teleconsultation')->nullable()->after('type_consultation');
            $table->decimal('temperature', 4, 1)->nullable()->after('lien_teleconsultation');
            $table->string('tension_arterielle')->nullable()->after('temperature');
            $table->decimal('taux_glycemie', 5, 2)->nullable()->after('tension_arterielle');
            $table->decimal('poids', 5, 2)->nullable()->after('taux_glycemie');
            $table->text('symptomes')->nullable()->after('poids');
            $table->text('conclusion')->nullable()->after('symptomes');
            $table->text('diagnostic_medecin')->nullable()->after('conclusion');
            $table->string('numero_dossier_reference')->nullable()->after('dossier_medical_id');
        });

        DB::statement('UPDATE consultation_professionnelles SET diagnostic_medecin = diagnostic WHERE diagnostic IS NOT NULL AND diagnostic_medecin IS NULL');

        Schema::table('facture_professionnelles', function (Blueprint $table) {
            $table->foreignId('consultation_professionnelle_id')->nullable()->after('rendez_vous_professionnel_id')->constrained('consultation_professionnelles')->nullOnDelete();
            $table->foreignId('dossier_medical_id')->nullable()->after('patient_user_id')->constrained('dossiers_medicaux')->nullOnDelete();
            $table->foreignId('professionnel_user_id')->nullable()->after('dossier_professionnel_id')->constrained('users')->nullOnDelete();
            $table->enum('type_facture', ['consultation', 'examen', 'autre'])->default('consultation')->after('type_service');
            $table->enum('mode_paiement', ['cash', 'en_ligne', 'mobile_money', 'carte', 'virement'])->nullable()->after('statut_paiement_patient');
            $table->string('reference_paiement')->nullable()->after('mode_paiement');
            $table->enum('statut_backoffice', ['en_attente', 'valide', 'rejete', 'paye'])->default('en_attente')->after('statut_mutuelle');
            $table->boolean('envoyee_backoffice')->default(true)->after('statut_backoffice');
            $table->timestamp('soumise_backoffice_le')->nullable()->after('envoyee_backoffice');
            $table->timestamp('prise_en_charge_confirmee_le')->nullable()->after('soumise_backoffice_le');
            $table->string('numero_dossier_reference')->nullable()->after('dossier_medical_id');
        });

        Schema::create('ordonnances_professionnelles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_professionnelle_id');
            $table->unsignedBigInteger('dossier_medical_id')->nullable();
            $table->unsignedBigInteger('professionnel_user_id')->nullable();
            $table->json('produits')->nullable();
            $table->text('prescription')->nullable();
            $table->text('recommandations')->nullable();
            $table->text('instructions_complementaires')->nullable();
            $table->string('fichier_joint_path')->nullable();
            $table->enum('statut', ['brouillon', 'finalisee'])->default('brouillon');
            $table->timestamps();

            $table->foreign('consultation_professionnelle_id', 'fk_ord_consult')
                ->references('id')->on('consultation_professionnelles')->cascadeOnDelete();
            $table->foreign('dossier_medical_id', 'fk_ord_dossier_med')
                ->references('id')->on('dossiers_medicaux')->nullOnDelete();
            $table->foreign('professionnel_user_id', 'fk_ord_prof_user')
                ->references('id')->on('users')->nullOnDelete();

            $table->index(['consultation_professionnelle_id', 'statut'], 'ordonnance_consult_statut_idx');
        });

        Schema::create('examens_professionnels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_professionnelle_id')->nullable();
            $table->unsignedBigInteger('service_professionnel_id')->nullable();
            $table->unsignedBigInteger('facture_professionnelle_id')->nullable();
            $table->unsignedBigInteger('dossier_medical_id')->nullable();
            $table->unsignedBigInteger('dossier_professionnel_id')->nullable();
            $table->unsignedBigInteger('professionnel_user_id')->nullable();
            $table->unsignedBigInteger('patient_user_id')->nullable();
            $table->string('numero_dossier_reference')->nullable();
            $table->string('libelle');
            $table->text('note_orientation')->nullable();
            $table->text('observations')->nullable();
            $table->text('resultat_text')->nullable();
            $table->string('resultat_fichier_path')->nullable();
            $table->enum('statut', ['demande', 'en_cours', 'termine'])->default('demande');
            $table->timestamps();

            $table->foreign('consultation_professionnelle_id', 'fk_exam_consult')
                ->references('id')->on('consultation_professionnelles')->nullOnDelete();
            $table->foreign('service_professionnel_id', 'fk_exam_service')
                ->references('id')->on('services_professionnels')->nullOnDelete();
            $table->foreign('facture_professionnelle_id', 'fk_exam_facture')
                ->references('id')->on('facture_professionnelles')->nullOnDelete();
            $table->foreign('dossier_medical_id', 'fk_exam_dossier_med')
                ->references('id')->on('dossiers_medicaux')->nullOnDelete();
            $table->foreign('dossier_professionnel_id', 'fk_exam_dossier_pro')
                ->references('id')->on('dossiers_professionnels')->nullOnDelete();
            $table->foreign('professionnel_user_id', 'fk_exam_prof_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('patient_user_id', 'fk_exam_patient_user')
                ->references('id')->on('users')->nullOnDelete();

            $table->index(['dossier_professionnel_id', 'statut'], 'examen_dossier_statut_idx');
            $table->index(['patient_user_id', 'statut'], 'examen_patient_statut_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examens_professionnels');
        Schema::dropIfExists('ordonnances_professionnelles');

        Schema::table('facture_professionnelles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('consultation_professionnelle_id');
            $table->dropConstrainedForeignId('dossier_medical_id');
            $table->dropConstrainedForeignId('professionnel_user_id');
            $table->dropColumn([
                'type_facture',
                'mode_paiement',
                'reference_paiement',
                'statut_backoffice',
                'envoyee_backoffice',
                'soumise_backoffice_le',
                'prise_en_charge_confirmee_le',
                'numero_dossier_reference',
            ]);
        });

        Schema::table('consultation_professionnelles', function (Blueprint $table) {
            $table->dropColumn([
                'type_consultation',
                'lien_teleconsultation',
                'temperature',
                'tension_arterielle',
                'taux_glycemie',
                'poids',
                'symptomes',
                'conclusion',
                'diagnostic_medecin',
                'numero_dossier_reference',
            ]);
        });

        Schema::table('rendez_vous_professionnels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('professionnel_user_id');
            $table->dropColumn([
                'date_proposee_jour',
                'heure_proposee',
                'statut_acceptation',
                'type_rendez_vous',
                'numero_dossier_reference',
            ]);
        });

        DB::statement('ALTER TABLE rendez_vous_professionnels MODIFY patient_user_id BIGINT UNSIGNED NOT NULL');
    }
};
