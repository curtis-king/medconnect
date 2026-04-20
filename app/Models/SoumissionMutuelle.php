<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoumissionMutuelle extends Model
{
    use HasFactory;

    protected $table = 'soumissions_mutuelle';

    /** @var list<string> */
    protected $fillable = [
        'facture_professionnelle_id',
        'dossier_medical_id',
        'subscription_id',
        'reference',
        'montant_soumis',
        'montant_pris_en_charge',
        'montant_rejete',
        'statut',
        'date_soumission',
        'date_traitement',
        'motif_rejet',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'montant_soumis' => 'decimal:2',
            'montant_pris_en_charge' => 'decimal:2',
            'montant_rejete' => 'decimal:2',
            'date_soumission' => 'datetime',
            'date_traitement' => 'datetime',
        ];
    }

    public function factureProfessionnelle()
    {
        return $this->belongsTo(FactureProfessionnelle::class, 'facture_professionnelle_id');
    }

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
