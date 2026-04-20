<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'cible_type',
        'cible_id',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cible(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public static function types(): array
    {
        return [
            'demande_validee' => 'Demande validée',
            'demande_rejetee' => 'Demande rejetée',
            'rendez_vous_accepte' => 'Rendez-vous accepté',
            'rendez_vous_confirme' => 'Rendez-vous confirmé',
            'rendez_vous_annule' => 'Rendez-vous annulé',
            'rappel_rendez_vous' => 'Rappel rendez-vous',
            'synchronisation_complete' => 'Synchronisation complète',
            'facture_payee' => 'Facture payée',
        ];
    }
}
