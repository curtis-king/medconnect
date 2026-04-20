<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TauxReduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'taux',
        'type',
        'detail',
        'actif',
    ];

    protected $casts = [
        'taux' => 'decimal:2',
        'actif' => 'boolean',
    ];

    const TYPES = [
        'inscription' => 'Inscription',
        'reabonnement' => 'Réabonnement',
        'contribution' => 'Contribution',
        'special' => 'Spécial',
    ];

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
