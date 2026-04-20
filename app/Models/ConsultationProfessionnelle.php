<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationProfessionnelle extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'rendez_vous_professionnel_id',
        'dossier_professionnel_id',
        'dossier_medical_id',
        'numero_dossier_reference',
        'patient_user_id',
        'type_service',
        'type_consultation',
        'lien_teleconsultation',
        'temperature',
        'tension_arterielle',
        'taux_glycemie',
        'poids',
        'symptomes',
        'conclusion',
        'diagnostic_medecin',
        'diagnostic',
        'recommandations',
        'ordonnance',
        'observations',
        'fichier_resultat_path',
        'note_resultat',
        'statut',
        'finalise_le',
    ];

    protected function casts(): array
    {
        return [
            'finalise_le' => 'datetime',
            'temperature' => 'decimal:1',
            'taux_glycemie' => 'decimal:2',
            'poids' => 'decimal:2',
        ];
    }

    public function rendezVous()
    {
        return $this->belongsTo(RendezVousProfessionnel::class, 'rendez_vous_professionnel_id');
    }

    public function dossierProfessionnel()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_id');
    }

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function factures()
    {
        return $this->hasMany(FactureProfessionnelle::class, 'consultation_professionnelle_id');
    }

    public function ordonnances()
    {
        return $this->hasMany(OrdonnanceProfessionnelle::class, 'consultation_professionnelle_id');
    }

    public function examens()
    {
        return $this->hasMany(ExamenProfessionnel::class, 'consultation_professionnelle_id');
    }

    public function documents()
    {
        return $this->hasMany(ConsultationDocument::class, 'consultation_professionnelle_id');
    }

    public function getServiceProfessionnelAttribute(): ?ServiceProfessionnel
    {
        return $this->rendezVous?->serviceProfessionnel;
    }
}
