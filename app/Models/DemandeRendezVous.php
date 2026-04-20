<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeRendezVous extends Model
{
    use HasFactory;

    protected $table = 'demande_rendez_vous';

    protected $fillable = [
        'demande_service_id',
        'date_rendez_vous',
        'lieu',
        'adresse',
        'status',
        'professional_user_id',
        'notes',
    ];

    protected $casts = [
        'date_rendez_vous' => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeService::class);
    }

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_user_id');
    }

    public const STATUS = [
        'planifie' => 'Planifié',
        'confirme' => 'Confirmé',
        'annule' => 'Annulé',
        'realise' => 'Réalisé',
    ];
}
