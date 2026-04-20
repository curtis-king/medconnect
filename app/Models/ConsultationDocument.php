<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationDocument extends Model
{
    protected $table = 'consultation_documents';

    /** @var list<string> */
    protected $fillable = [
        'consultation_professionnelle_id',
        'uploaded_by_user_id',
        'nom_fichier',
        'file_path',
        'taille_octets',
        'mime_type',
        'source',
    ];

    public function consultation()
    {
        return $this->belongsTo(ConsultationProfessionnelle::class, 'consultation_professionnelle_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
