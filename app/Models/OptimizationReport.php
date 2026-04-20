<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptimizationReport extends Model
{
    protected $fillable = [
        'stale_invoice_days',
        'stale_backoffice_days',
        'upcoming_window_days',
        'provider',
        'content',
        'admin_response',
        'action_plan',
        'metrics',
        'status',
        'action_status',
        'action_due_date',
        'action_completed_at',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'metrics' => 'json',
            'generated_at' => 'datetime',
            'action_due_date' => 'date',
            'action_completed_at' => 'datetime',
        ];
    }
}
