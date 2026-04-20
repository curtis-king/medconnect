<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivationDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $profileType,
        public string $decision,
        public string $message,
        public ?string $dossierReference = null,
        public ?string $note = null,
        public ?string $actionUrl = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $decisionLabel = $this->decision === 'valide' ? 'validee' : 'rejetee';
        $profileLabel = $this->profileType === 'professionnel' ? 'profil professionnel' : 'profil patient';

        $mail = (new MailMessage)
            ->subject('Decision d activation de votre profil')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('La verification de votre '.$profileLabel.' a ete '.$decisionLabel.'.')
            ->line($this->message);

        if ($this->dossierReference) {
            $mail->line('Reference dossier: '.$this->dossierReference);
        }

        if ($this->note) {
            $mail->line('Note de verification: '.$this->note);
        }

        if ($this->actionUrl) {
            $mail->action('Voir le statut de mes profils', $this->actionUrl);
        }

        return $mail->line('La reponse d activation vous est communiquee automatiquement par email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'activation_decision',
            'profile_type' => $this->profileType,
            'decision' => $this->decision,
            'message' => $this->message,
            'dossier_reference' => $this->dossierReference,
            'note' => $this->note,
        ];
    }
}
