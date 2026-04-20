<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_medical_id',
        'dossier_medical_id',
        'statut',
        'notes',
        'reponse_backoffice',
        'traite_par_user_id',
        'traite_le',
    ];

    protected $casts = [
        'traite_le' => 'datetime',
    ];

    public const STATUTS = [
        'en_attente' => 'En attente',
        'en_cours' => 'En cours',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
        'termine' => 'Terminé',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(ServiceMedical::class, 'service_medical_id');
    }

    public function dossier()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par_user_id');
    }

    public function piecesJointes()
    {
        return $this->hasMany(DemandePieceJointe::class);
    }

    public function decisions()
    {
        return $this->hasMany(DemandeDecision::class)->orderByDesc('taken_at');
    }

    public function rendezVous()
    {
        return $this->hasMany(DemandeRendezVous::class)->orderByDesc('date_rendez_vous');
    }

    public function factures()
    {
        return $this->hasMany(DemandeFacture::class);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeTraitees($query)
    {
        return $query->whereIn('statut', ['valide', 'rejete', 'termine']);
    }
}
