<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandePieceJointe extends Model
{
    use HasFactory;

    protected $table = 'demande_pieces_jointes';

    protected $fillable = [
        'demande_service_id',
        'type',
        'nom_fichier',
        'chemin_fichier',
        'mime_type',
        'taille_fichier',
        'uploaded_by_user_id',
    ];

    protected $casts = [
        'taille_fichier' => 'integer',
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeService::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public const TYPES = [
        'document' => 'Document',
        'prescription' => 'Prescription',
        'certificat' => 'Certificat',
        'autre' => 'Autre',
    ];
}
