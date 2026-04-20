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
        Schema::table('examens_professionnels', function (Blueprint $table) {
            $table->unsignedBigInteger('dossier_professionnel_recommande_id')->nullable()->after('dossier_professionnel_id');
            $table->unsignedBigInteger('recommande_par_user_id')->nullable()->after('dossier_professionnel_recommande_id');
            $table->decimal('commission_recommandation_montant', 12, 2)->default(0)->after('resultat_fichier_path');
            $table->enum('statut_commission', ['en_attente', 'validee', 'payee'])->default('en_attente')->after('commission_recommandation_montant');
            $table->timestamp('commission_validee_le')->nullable()->after('statut_commission');
            $table->timestamp('commission_payee_le')->nullable()->after('commission_validee_le');

            $table->foreign('dossier_professionnel_recommande_id', 'fk_exam_dossier_recommande')
                ->references('id')->on('dossiers_professionnels')->nullOnDelete();
            $table->foreign('recommande_par_user_id', 'fk_exam_recommande_user')
                ->references('id')->on('users')->nullOnDelete();

            $table->index(['dossier_professionnel_recommande_id', 'statut_commission'], 'examen_reco_commission_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examens_professionnels', function (Blueprint $table) {
            $table->dropForeign('fk_exam_dossier_recommande');
            $table->dropForeign('fk_exam_recommande_user');
            $table->dropIndex('examen_reco_commission_idx');
            $table->dropColumn([
                'dossier_professionnel_recommande_id',
                'recommande_par_user_id',
                'commission_recommandation_montant',
                'statut_commission',
                'commission_validee_le',
                'commission_payee_le',
            ]);
        });
    }
};
