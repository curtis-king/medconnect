<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facture_professionnelles', function (Blueprint $table) {
            $table->index('rendez_vous_professionnel_id', 'idx_facture_rdv');
            $table->dropUnique('uq_facture_rdv');
        });

        DB::statement('ALTER TABLE facture_professionnelles MODIFY rendez_vous_professionnel_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE facture_professionnelles MODIFY rendez_vous_professionnel_id BIGINT UNSIGNED NOT NULL');

        Schema::table('facture_professionnelles', function (Blueprint $table) {
            $table->dropIndex('idx_facture_rdv');
            $table->unique('rendez_vous_professionnel_id', 'uq_facture_rdv');
        });
    }
};
