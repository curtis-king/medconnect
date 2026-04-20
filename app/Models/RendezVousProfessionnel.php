<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVousProfessionnel extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'dossier_professionnel_id',
        'professionnel_user_id',
        'service_professionnel_id',
        'patient_user_id',
        'dossier_medical_id',
        'numero_dossier_reference',
        'reference',
        'type_demande',
        'type_rendez_vous',
        'mode_deroulement',
        'lien_teleconsultation_patient',
        'statut',
        'statut_acceptation',
        'date_proposee',
        'date_proposee_jour',
        'heure_proposee',
        'motif',
        'notes_professionnel',
        'decision_le',
    ];

    protected function casts(): array
    {
        return [
            'date_proposee' => 'datetime',
            'date_proposee_jour' => 'date',
            'heure_proposee' => 'datetime:H:i:s',
            'decision_le' => 'datetime',
            'temperature' => 'decimal:1',
            'poids' => 'decimal:2',
        ];
    }

    public function dossierProfessionnel()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_id');
    }

    public function serviceProfessionnel()
    {
        return $this->belongsTo(ServiceProfessionnel::class, 'service_professionnel_id');
    }

    public function professionnel()
    {
        return $this->belongsTo(User::class, 'professionnel_user_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function facture()
    {
        return $this->hasOne(FactureProfessionnelle::class, 'rendez_vous_professionnel_id');
    }

    public function consultation()
    {
        return $this->hasOne(ConsultationProfessionnelle::class, 'rendez_vous_professionnel_id');
    }
}
