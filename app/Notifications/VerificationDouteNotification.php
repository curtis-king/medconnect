<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class VerificationDouteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $scope,
        public int $recordId,
        public string $riskLevel,
        public array $reasons,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'verification_doute',
            'scope' => $this->scope,
            'record_id' => $this->recordId,
            'risk_level' => $this->riskLevel,
            'reasons' => $this->reasons,
            'message' => $this->message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
