<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'dossier_medical_id',
        'frais_id',
        'date_debut',
        'date_fin',
        'nombre_mois',
        'montant',
        'plafond_couverture',
        'plafond_utilise',
        'statut',
        'mode_paiement',
        'reference_paiement',
        'encaisse_par_user_id',
        'date_paiement',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_paiement' => 'datetime',
            'montant' => 'decimal:2',
            'plafond_couverture' => 'decimal:2',
            'plafond_utilise' => 'decimal:2',
            'nombre_mois' => 'integer',
        ];
    }

    /**
     * Relation avec le dossier médical.
     */
    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class);
    }

    /**
     * Relation avec les frais (réabonnement).
     */
    public function frais()
    {
        return $this->belongsTo(Frais::class);
    }

    /**
     * Relation avec l'utilisateur qui a encaissé.
     */
    public function encaissePar()
    {
        return $this->belongsTo(User::class, 'encaisse_par_user_id');
    }

    /**
     * Relation avec les soumissions à la mutuelle.
     */
    public function soumissionsMutuelle()
    {
        return $this->hasMany(SoumissionMutuelle::class, 'subscription_id');
    }

    /**
     * Scope pour les subscriptions actives.
     */
    public function scopeActives($query)
    {
        return $query->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString());
    }

    /**
     * Scope pour les subscriptions expirées.
     */
    public function scopeExpirees($query)
    {
        return $query->where(function ($q) {
            $q->where('statut', 'expire')
                ->orWhere(function ($q2) {
                    $q2->where('statut', 'actif')
                        ->where('date_fin', '<', now()->toDateString());
                });
        });
    }

    /**
     * Vérifie si la subscription est active.
     */
    public function isActive(): bool
    {
        return $this->statut === 'actif' && $this->date_fin >= now()->toDateString();
    }

    /**
     * Vérifie si la subscription est expirée.
     */
    public function isExpired(): bool
    {
        return $this->date_fin < now()->toDateString();
    }

    /**
     * Calcule la date de fin basée sur la date de début et le nombre de mois.
     */
    public static function calculateEndDate(Carbon $startDate, int $months): Carbon
    {
        return $startDate->copy()->addMonths($months)->subDay();
    }

    /**
     * Crée une nouvelle subscription avec calcul automatique des dates.
     *
     * @param  Carbon|null  $dateDebut  Si null, utilise la date de fin de la dernière subscription active ou aujourd'hui
     */
    public static function createWithAutoDate(
        int $dossierMedicalId,
        int $fraisId,
        int $nombreMois,
        ?Carbon $dateDebut = null,
        ?string $modePaiement = null,
        ?int $encaisseParUserId = null
    ): self {
        // Si pas de date de début fournie, chercher la dernière subscription active
        if (! $dateDebut) {
            $lastSubscription = self::where('dossier_medical_id', $dossierMedicalId)
                ->where('statut', '!=', 'annule')
                ->orderBy('date_fin', 'desc')
                ->first();

            if ($lastSubscription && $lastSubscription->date_fin >= now()->toDateString()) {
                // Si la dernière subscription est encore valide, commencer le jour suivant
                $dateDebut = $lastSubscription->date_fin->copy()->addDay();
            } else {
                // Sinon, commencer aujourd'hui
                $dateDebut = now();
            }
        }

        $frais = Frais::findOrFail($fraisId);
        $montant = $frais->prix * $nombreMois;
        $dateFin = self::calculateEndDate($dateDebut, $nombreMois);

        return self::create([
            'dossier_medical_id' => $dossierMedicalId,
            'frais_id' => $fraisId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nombre_mois' => $nombreMois,
            'montant' => $montant,
            'statut' => 'actif',
            'mode_paiement' => $modePaiement,
            'encaisse_par_user_id' => $encaisseParUserId,
            'date_paiement' => now(),
        ]);
    }

    /**
     * Met à jour le statut des subscriptions expirées.
     */
    public static function updateExpiredStatuses(): int
    {
        return self::where('statut', 'actif')
            ->where('date_fin', '<', now()->toDateString())
            ->update(['statut' => 'expire']);
    }

    /**
     * Retourne le nombre de jours restants.
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->date_fin, false);
    }

    /**
     * Retourne le statut formaté pour l'affichage.
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'actif' => $this->isExpired() ? 'Expiré' : 'Actif',
            'expire' => 'Expiré',
            'annule' => 'Annulé',
            default => ucfirst($this->statut),
        };
    }
}
