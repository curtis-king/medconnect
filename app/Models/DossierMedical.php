<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierMedical extends Model
{
    /** @use HasFactory<\Database\Factories\DossierMedicalFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dossiers_medicaux';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'est_personne_a_charge',
        'lien_avec_responsable',
        'numero_unique',
        'source_creation',
        'actif',
        'partage_actif',
        'code_partage',
        'partage_active_le',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'telephone',
        'adresse',
        'groupe_sanguin',
        'allergies',
        'maladies_chroniques',
        'traitements_en_cours',
        'antecedents_familiaux',
        'antecedents_personnels',
        'contact_urgence_nom',
        'contact_urgence_telephone',
        'contact_urgence_relation',
        'type_piece_identite',
        'numero_piece_identite',
        'date_expiration_piece_identite',
        'piece_identite_recto_path',
        'piece_identite_verso_path',
        'documents_validation_statut',
        'documents_validation_ia_risk_level',
        'documents_validation_ia_score',
        'documents_validation_ia_reasons',
        'documents_validation_personnel_user_id',
        'documents_validation_personnel_note',
        'documents_validation_personnel_at',
        'photo_profil_path',
        'frais_id',
        'statut_paiement_inscription',
        'mode_paiement_inscription',
        'reference_paiement_inscription',
        'encaisse_par_user_id',
        'encaisse_le',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'est_personne_a_charge' => 'boolean',
            'actif' => 'boolean',
            'partage_actif' => 'boolean',
            'date_naissance' => 'date',
            'date_expiration_piece_identite' => 'date',
            'documents_validation_ia_reasons' => 'array',
            'documents_validation_personnel_at' => 'datetime',
            'partage_active_le' => 'datetime',
            'encaisse_le' => 'datetime',
        ];
    }

    /**
     * Get the user associated with the dossier.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the frais (type inscription) associated with the dossier.
     */
    public function frais()
    {
        return $this->belongsTo(Frais::class, 'frais_id');
    }

    /**
     * Get the paiements associated with this dossier.
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'dossier_medical_id');
    }

    /**
     * Get the latest paiement for this dossier.
     */
    public function latestPaiement()
    {
        return $this->hasOne(Paiement::class, 'dossier_medical_id')->latest();
    }

    /**
     * Get the active paiements for this dossier.
     */
    public function paiementsActifs()
    {
        return $this->paiements()->actifs();
    }

    /**
     * Check if this dossier has active paiement (is valid).
     */
    public function hasActivePaiement(): bool
    {
        return $this->paiementsActifs()->exists();
    }

    /**
     * Get the subscriptions associated with this dossier.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'dossier_medical_id');
    }

    /**
     * Get the active subscription for this dossier.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'dossier_medical_id')
            ->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString())
            ->latest('date_fin');
    }

    /**
     * Check if this dossier has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString())
            ->exists();
    }

    /**
     * Get the subscription expiration date.
     */
    public function getSubscriptionExpirationDate()
    {
        $activeSubscription = $this->activeSubscription;

        return $activeSubscription ? $activeSubscription->date_fin : null;
    }

    /**
     * Get the expiration date of the current active paiement.
     */
    public function getExpirationDate()
    {
        $activePaiement = $this->paiementsActifs()->first();

        return $activePaiement ? $activePaiement->periode_fin : null;
    }

    /**
     * Get the user who encaisse the payment.
     */
    public function encaissePar()
    {
        return $this->belongsTo(User::class, 'encaisse_par_user_id');
    }

    public function validateurDocuments()
    {
        return $this->belongsTo(User::class, 'documents_validation_personnel_user_id');
    }

    /**
     * Get the full name attribute.
     */
    protected function nomComplet(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prenom.' '.$this->nom,
        );
    }

    protected function lienAvecResponsableLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ((string) $this->lien_avec_responsable) {
                'enfant' => 'Enfant',
                'conjoint' => 'Conjoint(e)',
                'parent' => 'Parent',
                'frere_soeur' => 'Frere / Soeur',
                'autre' => 'Autre personne a charge',
                default => null,
            },
        );
    }

    /**
     * Scope for active dossiers.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope for dossiers with partage actif.
     */
    public function scopePartageActif($query)
    {
        return $query->where('partage_actif', true);
    }
}
