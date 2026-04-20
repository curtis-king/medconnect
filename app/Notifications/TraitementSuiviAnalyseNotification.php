<?php

namespace App\Notifications;

use App\Models\OrdonnanceProfessionnelle;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TraitementSuiviAnalyseNotification extends Notification
{
    public function __construct(
        public OrdonnanceProfessionnelle $ordonnance,
        public array $analysis
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $summary = (string) ($this->analysis['resume'] ?? 'Analyse de traitement disponible.');
        $durationDays = $this->analysis['duree_estimee_jours'] ?? null;
        $durationText = is_numeric($durationDays) ? 'Duree estimee: '.(int) $durationDays.' jour(s).' : 'Duree a confirmer.';

        return [
            'type' => 'traitement_suivi_analyse',
            'ordonnance_id' => $this->ordonnance->id,
            'consultation_id' => $this->ordonnance->consultation_professionnelle_id,
            'duree_estimee_jours' => is_numeric($durationDays) ? (int) $durationDays : null,
            'summary' => $summary,
            'message' => 'Analyse IA de suivi disponible pour l ordonnance #'.$this->ordonnance->id.'. '.$durationText,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
