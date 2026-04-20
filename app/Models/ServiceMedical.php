<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMedical extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'type',
        'actif',
    ];

    protected $casts = [
        'prix' => 'decimal:0',
        'actif' => 'boolean',
    ];

    public const TYPES = [
        'prise_rendez_vous' => 'Prise de rendez-vous à domicile',
        'teleconsultation' => 'Téléconsultation',
        'demande_examen' => 'Demande d\'examen à domicile',
        'prelevement_domicile' => 'Prélèvement à domicile',
        'livraison_medicament' => 'Livraison de médicaments',
        'hospitalisation_domicile' => 'Hospitalisation à domicile (HAD)',
        'consultation' => 'Consultation',
        'prescription' => 'Prescription',
    ];

    public function demandes()
    {
        return $this->hasMany(DemandeService::class, 'service_medical_id');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
