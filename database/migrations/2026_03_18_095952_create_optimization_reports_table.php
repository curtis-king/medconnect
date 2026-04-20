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
        Schema::create('optimization_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('stale_invoice_days')->default(7);
            $table->integer('stale_backoffice_days')->default(5);
            $table->integer('upcoming_window_days')->default(2);
            $table->string('provider')->default('gemini');
            $table->longText('content');
            $table->json('metrics')->nullable();
            $table->string('status')->default('completed');
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->index('generated_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimization_reports');
    }
};
