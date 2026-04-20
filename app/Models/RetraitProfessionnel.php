<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetraitProfessionnel extends Model
{
    use HasFactory;

    protected $table = 'retraits_professionnels';

    /** @var list<string> */
    protected $fillable = [
        'dossier_professionnel_id',
        'reference',
        'montant_demande',
        'montant_approuve',
        'statut',
        'date_demande',
        'date_traitement',
        'date_paiement',
        'motif_rejet',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'montant_demande' => 'decimal:2',
            'montant_approuve' => 'decimal:2',
            'date_demande' => 'datetime',
            'date_traitement' => 'datetime',
            'date_paiement' => 'datetime',
        ];
    }

    public function dossierProfessionnel()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_id');
    }

    public function factures()
    {
        return $this->belongsToMany(
            FactureProfessionnelle::class,
            'retrait_facture_professionnelle',
            'retrait_professionnel_id',
            'facture_professionnelle_id'
        )->withPivot('montant')->withTimestamps();
    }
}
