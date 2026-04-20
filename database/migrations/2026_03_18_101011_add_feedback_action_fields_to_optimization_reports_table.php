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
        Schema::table('optimization_reports', function (Blueprint $table) {
            $table->text('admin_response')->nullable()->after('content');
            $table->text('action_plan')->nullable()->after('admin_response');
            $table->string('action_status')->default('pending')->after('status');
            $table->date('action_due_date')->nullable()->after('action_status');
            $table->timestamp('action_completed_at')->nullable()->after('action_due_date');

            $table->index('action_status');
            $table->index('action_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('optimization_reports', function (Blueprint $table) {
            $table->dropIndex(['action_status']);
            $table->dropIndex(['action_due_date']);

            $table->dropColumn([
                'admin_response',
                'action_plan',
                'action_status',
                'action_due_date',
                'action_completed_at',
            ]);
        });
    }
};
