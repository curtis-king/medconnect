<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierProfessionnel extends Model
{
    /** @use HasFactory<\Database\Factories\DossierProfessionnelFactory> */
    use HasFactory;

    protected $table = 'dossiers_professionnels';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'raison_sociale',
        'type_structure',
        'image_identite_path',
        'specialite',
        'attestation_professionnelle_path',
        'document_prise_de_fonction_path',
        'NIU',
        'forme_juridique',
        'statut',
        'numero_licence',
        'frais_id',
        'statut_paiement_inscription',
        'mode_paiement_inscription',
        'reference_paiement_inscription',
        'encaisse_par_user_id',
        'encaisse_le',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'encaisse_le' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function frais()
    {
        return $this->belongsTo(Frais::class, 'frais_id');
    }

    public function encaissePar()
    {
        return $this->belongsTo(User::class, 'encaisse_par_user_id');
    }

    public function services()
    {
        return $this->hasMany(ServiceProfessionnel::class, 'dossier_professionnel_id');
    }

    public function servicesActifs()
    {
        return $this->services()->where('actif', true);
    }

    public function subscriptions()
    {
        return $this->hasMany(SubscriptionProfessionnelle::class, 'dossier_professionnel_id');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVousProfessionnel::class, 'dossier_professionnel_id');
    }

    public function factures()
    {
        return $this->hasMany(FactureProfessionnelle::class, 'dossier_professionnel_id');
    }

    public function consultations()
    {
        return $this->hasMany(ConsultationProfessionnelle::class, 'dossier_professionnel_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(SubscriptionProfessionnelle::class, 'dossier_professionnel_id')
            ->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString())
            ->latest('date_fin');
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString())
            ->exists();
    }

    public function isValide(): bool
    {
        return $this->statut === 'valide';
    }

    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function isRecale(): bool
    {
        return $this->statut === 'recale';
    }

    protected function typeStructureLabel(): Attribute
    {
        return Attribute::make(get: fn () => match ($this->type_structure) {
            'individuel' => 'Individuel',
            'clinique' => 'Clinique',
            'hopital' => 'H\u00f4pital',
            'dispensaire' => 'Dispensaire',
            'autre' => 'Autre',
            default => ucfirst($this->type_structure),
        });
    }

    protected function statutLabel(): Attribute
    {
        return Attribute::make(get: fn () => match ($this->statut) {
            'en_attente' => 'En attente',
            'valide' => 'Valid\u00e9',
            'recale' => 'Recal\u00e9',
            default => ucfirst($this->statut),
        });
    }

    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeRecale($query)
    {
        return $query->where('statut', 'recale');
    }
}
