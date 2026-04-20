<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frais extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'prix',
        'type',
        'detail',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'type' => 'string',
    ];

    public const TYPES = [
        'inscription' => 'Inscription Patient',
        'inscription_pro' => 'Inscription Professionnel',
        'abonnement_mensuel' => 'Abonnement Mensuel',
        'abonnement_bimestriel' => 'Abonnement Bimestriel',
        'abonnement_semestriel' => 'Abonnement Semestriel',
        'abonnement_annuel' => 'Abonnement Annuel',
        'contribution' => 'Contribution',
    ];

    /**
     * Get the formatted prix.
     */
    public function getPrixFormattedAttribute(): string
    {
        return number_format($this->prix, 0, ',', ' ').' Fcfa';
    }
}
