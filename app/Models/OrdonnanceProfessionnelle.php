<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdonnanceProfessionnelle extends Model
{
    use HasFactory;

    protected $table = 'ordonnances_professionnelles';

    /** @var list<string> */
    protected $fillable = [
        'consultation_professionnelle_id',
        'dossier_medical_id',
        'professionnel_user_id',
        'produits',
        'prescription',
        'recommandations',
        'instructions_complementaires',
        'fichier_joint_path',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'produits' => 'array',
        ];
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
}
