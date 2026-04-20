<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeFacture extends Model
{
    use HasFactory;

    protected $table = 'demande_factures';

    protected $fillable = [
        'demande_service_id',
        'numero_facture',
        'montant',
        'statut',
        'date_echeance',
        'date_paiement',
        'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:0',
        'date_echeance' => 'datetime',
        'date_paiement' => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeService::class);
    }

    public const STATUTS = [
        'en_attente' => 'En attente',
        'paye' => 'Payé',
        'annule' => 'Annulé',
    ];

    public static function generateNumero(): string
    {
        $prefix = 'FAC-DEM-';
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return $prefix.$year.sprintf('%05d', $count);
    }
}
