<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactureProfessionnelle extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'rendez_vous_professionnel_id',
        'consultation_professionnelle_id',
        'dossier_professionnel_id',
        'professionnel_user_id',
        'service_professionnel_id',
        'patient_user_id',
        'dossier_medical_id',
        'numero_dossier_reference',
        'reference',
        'type_service',
        'type_facture',
        'montant_total',
        'montant_couvert_mutuelle',
        'montant_a_charge_patient',
        'statut',
        'statut_mutuelle',
        'statut_backoffice',
        'envoyee_backoffice',
        'statut_paiement_patient',
        'mode_paiement',
        'reference_paiement',
        'soumise_backoffice_le',
        'prise_en_charge_confirmee_le',
        'emise_le',
        'payee_le',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'montant_total' => 'decimal:2',
            'montant_couvert_mutuelle' => 'decimal:2',
            'montant_a_charge_patient' => 'decimal:2',
            'envoyee_backoffice' => 'boolean',
            'soumise_backoffice_le' => 'datetime',
            'prise_en_charge_confirmee_le' => 'datetime',
            'emise_le' => 'datetime',
            'payee_le' => 'datetime',
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

    public function consultation()
    {
        return $this->belongsTo(ConsultationProfessionnelle::class, 'consultation_professionnelle_id');
    }

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function professionnel()
    {
        return $this->belongsTo(User::class, 'professionnel_user_id');
    }

    public function serviceProfessionnel()
    {
        return $this->belongsTo(ServiceProfessionnel::class, 'service_professionnel_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function soumissionsMutuelle()
    {
        return $this->hasMany(SoumissionMutuelle::class, 'facture_professionnelle_id');
    }

    public function retraits()
    {
        return $this->belongsToMany(
            RetraitProfessionnel::class,
            'retrait_facture_professionnelle',
            'facture_professionnelle_id',
            'retrait_professionnel_id'
        )->withPivot('montant')->withTimestamps();
    }

    public function examens()
    {
        return $this->hasMany(ExamenProfessionnel::class, 'facture_professionnelle_id');
    }
}
