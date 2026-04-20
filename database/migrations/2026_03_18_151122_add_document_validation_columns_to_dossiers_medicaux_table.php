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
        Schema::table('dossiers_medicaux', function (Blueprint $table) {
            $table->enum('documents_validation_statut', ['en_attente', 'valide', 'rejete'])
                ->default('en_attente')
                ->after('piece_identite_verso_path');
            $table->string('documents_validation_ia_risk_level')->nullable()->after('documents_validation_statut');
            $table->unsignedSmallInteger('documents_validation_ia_score')->nullable()->after('documents_validation_ia_risk_level');
            $table->json('documents_validation_ia_reasons')->nullable()->after('documents_validation_ia_score');
            $table->foreignId('documents_validation_personnel_user_id')->nullable()->after('documents_validation_ia_reasons')
                ->constrained('users')->nullOnDelete();
            $table->text('documents_validation_personnel_note')->nullable()->after('documents_validation_personnel_user_id');
            $table->timestamp('documents_validation_personnel_at')->nullable()->after('documents_validation_personnel_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossiers_medicaux', function (Blueprint $table) {
            $table->dropConstrainedForeignId('documents_validation_personnel_user_id');
            $table->dropColumn([
                'documents_validation_statut',
                'documents_validation_ia_risk_level',
                'documents_validation_ia_score',
                'documents_validation_ia_reasons',
                'documents_validation_personnel_note',
                'documents_validation_personnel_at',
            ]);
        });
    }
};
