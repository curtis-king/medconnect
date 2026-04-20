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
            $table->boolean('est_personne_a_charge')->default(false)->after('user_id');
            $table->string('lien_avec_responsable', 120)->nullable()->after('est_personne_a_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossiers_medicaux', function (Blueprint $table) {
            $table->dropColumn(['est_personne_a_charge', 'lien_avec_responsable']);
        });
    }
};
