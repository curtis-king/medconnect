<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE frais MODIFY COLUMN type ENUM('inscription', 'reabonnement', 'contribution', 'inscription_pro', 'reabonnement_pro')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE frais MODIFY COLUMN type ENUM('inscription', 'reabonnement', 'contribution')");
    }
};
