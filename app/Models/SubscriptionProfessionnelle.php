<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionProfessionnelle extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionProfessionnelleFactory> */
    use HasFactory;

    protected $table = 'subscriptions_professionnelles';

    /** @var list<string> */
    protected $fillable = [
        'dossier_professionnel_id',
        'frais_id',
        'date_debut',
        'date_fin',
        'nombre_mois',
        'montant',
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
            'nombre_mois' => 'integer',
        ];
    }

    public function dossierProfessionnel()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_id');
    }

    public function frais()
    {
        return $this->belongsTo(Frais::class);
    }

    public function encaissePar()
    {
        return $this->belongsTo(User::class, 'encaisse_par_user_id');
    }

    public function scopeActives($query)
    {
        return $query->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString());
    }

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

    public function isActive(): bool
    {
        return $this->statut === 'actif' && $this->date_fin >= now()->toDateString();
    }

    public function isExpired(): bool
    {
        return $this->date_fin < now()->toDateString();
    }

    public static function calculateEndDate(Carbon $startDate, int $months): Carbon
    {
        return $startDate->copy()->addMonths($months)->subDay();
    }

    /**
     * Cr\u00e9e une subscription avec calcul automatique des dates.
     */
    public static function createWithAutoDate(
        int $dossierProfessionnelId,
        int $fraisId,
        int $nombreMois,
        ?Carbon $dateDebut = null,
        ?string $modePaiement = null,
        ?int $encaisseParUserId = null
    ): self {
        if (! $dateDebut) {
            $lastSubscription = self::where('dossier_professionnel_id', $dossierProfessionnelId)
                ->where('statut', '!=', 'annule')
                ->orderBy('date_fin', 'desc')
                ->first();

            if ($lastSubscription && $lastSubscription->date_fin >= now()->toDateString()) {
                $dateDebut = $lastSubscription->date_fin->copy()->addDay();
            } else {
                $dateDebut = now();
            }
        }

        $frais = Frais::findOrFail($fraisId);
        $montant = $frais->prix * $nombreMois;
        $dateFin = self::calculateEndDate($dateDebut, $nombreMois);

        return self::create([
            'dossier_professionnel_id' => $dossierProfessionnelId,
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

    public static function updateExpiredStatuses(): int
    {
        return self::where('statut', 'actif')
            ->where('date_fin', '<', now()->toDateString())
            ->update(['statut' => 'expire']);
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) now()->diffInDays($this->date_fin, false);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'actif' => $this->isExpired() ? 'Expir\u00e9' : 'Actif',
            'expire' => 'Expir\u00e9',
            'annule' => 'Annul\u00e9',
            default => ucfirst($this->statut),
        };
    }
}
