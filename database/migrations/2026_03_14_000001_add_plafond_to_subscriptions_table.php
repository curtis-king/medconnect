<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Plafond de couverture annuel accordé par la mutuelle
            $table->decimal('plafond_couverture', 10, 2)->nullable()->after('montant');
            // Montant déjà consommé sur ce plafond
            $table->decimal('plafond_utilise', 10, 2)->default(0)->after('plafond_couverture');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['plafond_couverture', 'plafond_utilise']);
        });
    }
};
