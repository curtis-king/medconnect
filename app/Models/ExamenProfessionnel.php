<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenProfessionnel extends Model
{
    use HasFactory;

    protected $table = 'examens_professionnels';

    /** @var list<string> */
    protected $fillable = [
        'consultation_professionnelle_id',
        'service_professionnel_id',
        'facture_professionnelle_id',
        'dossier_medical_id',
        'dossier_professionnel_id',
        'dossier_professionnel_recommande_id',
        'recommande_par_user_id',
        'professionnel_user_id',
        'patient_user_id',
        'numero_dossier_reference',
        'libelle',
        'note_orientation',
        'observations',
        'resultat_text',
        'resultat_fichier_path',
        'commission_recommandation_montant',
        'statut_commission',
        'commission_validee_le',
        'commission_payee_le',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'commission_recommandation_montant' => 'decimal:2',
            'commission_validee_le' => 'datetime',
            'commission_payee_le' => 'datetime',
        ];
    }

    public function consultation()
    {
        return $this->belongsTo(ConsultationProfessionnelle::class, 'consultation_professionnelle_id');
    }

    public function serviceProfessionnel()
    {
        return $this->belongsTo(ServiceProfessionnel::class, 'service_professionnel_id');
    }

    public function facture()
    {
        return $this->belongsTo(FactureProfessionnelle::class, 'facture_professionnelle_id');
    }

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function dossierProfessionnel()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_id');
    }

    public function dossierProfessionnelRecommande()
    {
        return $this->belongsTo(DossierProfessionnel::class, 'dossier_professionnel_recommande_id');
    }

    public function professionnel()
    {
        return $this->belongsTo(User::class, 'professionnel_user_id');
    }

    public function recommandePar()
    {
        return $this->belongsTo(User::class, 'recommande_par_user_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }
}
