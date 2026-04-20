<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    public function sendToUser(
        int $userId,
        string $type,
        string $title,
        string $body,
        array $data = [],
        ?string $cibleType = null,
        ?int $cibleId = null
    ): void {
        $userTokens = UserDeviceToken::where('user_id', $userId)->get();

        if ($userTokens->isEmpty()) {
            return;
        }

        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'cible_type' => $cibleType,
            'cible_id' => $cibleId,
        ]);

        foreach ($userTokens as $token) {
            $this->sendPushNotification($token->token, $title, $body, $data, $token->platform);
        }
    }

    public function sendPushNotification(
        string $token,
        string $title,
        string $body,
        array $data = [],
        string $platform = 'android'
    ): void {
        try {
            if ($platform === 'android') {
                $this->sendAndroidNotification($token, $title, $body, $data);
            } else {
                $this->sendIOSNotification($token, $title, $body, $data);
            }
        } catch (\Exception $e) {
            \Log::error('Push notification failed: '.$e->getMessage());
        }
    }

    private function sendAndroidNotification(string $token, string $title, string $body, array $data): void
    {
        $fcmKey = config('services.fcm.server_key');

        if (! $fcmKey) {
            return;
        }

        Http::withHeaders([
            'Authorization' => 'key='.$fcmKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
        ]);
    }

    private function sendIOSNotification(string $token, string $title, string $body, array $data): void
    {
        // For iOS, you would typically use APNS
        // This is a placeholder for APNS integration
    }

    public function notifyDemandeValidee(int $userId, int $demandeId, string $serviceNom): void
    {
        $this->sendToUser(
            $userId,
            'demande_validee',
            'Demande validée',
            "Votre demande de {$serviceNom} a été validée.",
            ['demande_id' => $demandeId, 'type' => 'demande'],
            'App\Models\DemandeService',
            $demandeId
        );
    }

    public function notifyDemandeRejetee(int $userId, int $demandeId, string $serviceNom, string $motif): void
    {
        $this->sendToUser(
            $userId,
            'demande_rejetee',
            'Demande rejetée',
            "Votre demande de {$serviceNom} a été rejetée. Motif: {$motif}",
            ['demande_id' => $demandeId, 'type' => 'demande'],
            'App\Models\DemandeService',
            $demandeId
        );
    }

    public function notifyRendezVousAccepte(int $userId, int $rdvId, string $service, string $date): void
    {
        $this->sendToUser(
            $userId,
            'rendez_vous_accepte',
            'Rendez-vous accepté',
            "Votre rendez-vous pour {$service} le {$date} a été accepté.",
            ['rdv_id' => $rdvId, 'type' => 'rendez_vous'],
            'App\Models\RendezVousProfessionnel',
            $rdvId
        );
    }

    public function notifyRendezVousConfirme(int $userId, int $rdvId, string $service, string $date): void
    {
        $this->sendToUser(
            $userId,
            'rendez_vous_confirme',
            'Rendez-vous confirmé',
            "Votre rendez-vous pour {$service} le {$date} est confirmé.",
            ['rdv_id' => $rdvId, 'type' => 'rendez_vous'],
            'App\Models\RendezVousProfessionnel',
            $rdvId
        );
    }

    public function notifyRappelRendezVous(int $userId, int $rdvId, string $service, string $date): void
    {
        $this->sendToUser(
            $userId,
            'rappel_rendez_vous',
            'Rappel: Rendez-vous demain',
            "Rappel: Vous avez un rendez-vous pour {$service} demain.",
            ['rdv_id' => $rdvId, 'type' => 'rendez_vous'],
            'App\Models\RendezVousProfessionnel',
            $rdvId
        );
    }

    public function notifySynchronisationCompletee(int $userId): void
    {
        $this->sendToUser(
            $userId,
            'synchronisation_complete',
            'Synchronisation terminée',
            'Vos données ont été synchronisées avec succès.',
            ['type' => 'synchronisation']
        );
    }
}
