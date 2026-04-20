<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProfessionnel extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceProfessionnelFactory> */
    use HasFactory;

    protected $table = 'services_professionnels';

    /** @var list<string> */
    protected $fillable = [
        'dossier_professionnel_id',
        'nom',
        'description',
        'type',
        'prix',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function dossierProfessionnel()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_id');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVousProfessionnel::class, 'service_professionnel_id');
    }

    public function factures()
    {
        return $this->hasMany(FactureProfessionnelle::class, 'service_professionnel_id');
    }

    public function getMontantFormattedAttribute(): string
    {
        return number_format((float) $this->prix, 0, ',', ' ').' XAF';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'consultation' => 'Consultation',
            'examen' => 'Examen',
            'hospitalisation' => 'Hospitalisation',
            'chirurgie' => 'Chirurgie',
            'urgence' => 'Urgence',
            'autre' => 'Autre',
            default => ucfirst($this->type),
        };
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
