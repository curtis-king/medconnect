<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeDecision extends Model
{
    use HasFactory;

    protected $table = 'demande_decisions';

    protected $fillable = [
        'demande_service_id',
        'type',
        'motif',
        'taken_by_user_id',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeService::class);
    }

    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by_user_id');
    }

    public const TYPES = [
        'validation' => 'Validation',
        'rejet' => 'Rejet',
        'terminer' => 'Terminé',
        'ajour' => 'Ajourné',
    ];
}
