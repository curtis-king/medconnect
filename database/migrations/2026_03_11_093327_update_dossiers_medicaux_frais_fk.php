<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dossiers_medicaux', function (Blueprint $table) {
            if (Schema::hasColumn('dossiers_medicaux', 'frais_inscription_id')) {
                $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
                    ->select('CONSTRAINT_NAME')
                    ->whereRaw('TABLE_SCHEMA = DATABASE()')
                    ->where('TABLE_NAME', 'dossiers_medicaux')
                    ->where('COLUMN_NAME', 'frais_inscription_id')
                    ->whereNotNull('REFERENCED_TABLE_NAME')
                    ->value('CONSTRAINT_NAME');

                if (is_string($constraint) && $constraint !== '') {
                    $table->dropForeign($constraint);
                }

                $table->dropColumn('frais_inscription_id');
            }

            $table->foreignId('frais_id')
                ->nullable()
                ->after('photo_profil_path')
                ->constrained('frais')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossiers_medicaux', function (Blueprint $table) {
            if (Schema::hasColumn('dossiers_medicaux', 'frais_id')) {
                $table->dropConstrainedForeignId('frais_id');
            }

            $table->foreignId('frais_inscription_id')
                ->nullable()
                ->after('photo_profil_path')
                ->constrained('frais_inscriptions')
                ->nullOnDelete();
        });
    }
};
