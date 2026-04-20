<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_medical_id',
        'frais_inscription_id',
        'type_paiement',
        'montant',
        'periode_debut',
        'periode_fin',
        'nombre_mois',
        'statut',
        'mode_paiement',
        'reference_paiement',
        'notes',
        'encaisse_par_user_id',
        'date_encaissement',
        'date_echeance',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'periode_debut' => 'date',
            'periode_fin' => 'date',
            'date_encaissement' => 'datetime',
            'date_echeance' => 'datetime',
            'nombre_mois' => 'integer',
        ];
    }

    /**
     * Get the dossier medical associated with this paiement.
     */
    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class);
    }

    /**
     * Get the user who encaisse this paiement.
     */
    public function encaissePar()
    {
        return $this->belongsTo(User::class, 'encaisse_par_user_id');
    }

    /**
     * Get the frais associated with this paiement.
     */
    public function frais()
    {
        return $this->belongsTo(Frais::class, 'frais_inscription_id');
    }

    /**
     * Alias for frais relationship (for compatibility).
     */
    public function fraisInscription()
    {
        return $this->belongsTo(Frais::class, 'frais_inscription_id');
    }

    /**
     * Scope for paiements payes.
     */
    public function scopePayes($query)
    {
        return $query->where('statut', 'paye');
    }

    /**
     * Scope for paiements en attente.
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope for paiements echus.
     */
    public function scopeEchus($query)
    {
        return $query->where('date_echeance', '<', now());
    }

    /**
     * Scope for paiements actifs (couvrant la période actuelle).
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'paye')
            ->where('periode_debut', '<=', now())
            ->where('periode_fin', '>=', now());
    }

    /**
     * Check if this paiement is expired.
     */
    public function isExpired(): bool
    {
        return $this->date_echeance && $this->date_echeance->isPast();
    }

    /**
     * Check if this paiement is active (covering current period).
     */
    public function isActive(): bool
    {
        return $this->statut === 'paye' &&
               $this->periode_debut <= now() &&
               $this->periode_fin >= now();
    }

    /**
     * Get the formatted montant.
     */
    public function getMontantFormattedAttribute(): string
    {
        return number_format($this->montant, 2, ',', ' ').' €';
    }
}
