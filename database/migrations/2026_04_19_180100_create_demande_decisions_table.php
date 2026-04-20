<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_service_id')->constrained('demande_services')->onDelete('cascade');
            $table->string('type'); // validation, rejet, termine, ajour
            $table->text('motif')->nullable();
            $table->foreignId('taken_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('taken_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_decisions');
    }
};
