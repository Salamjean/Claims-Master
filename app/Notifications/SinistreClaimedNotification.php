<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SinistreClaimedNotification extends Notification
{
    protected $sinistre;
    protected $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct($sinistre, $agent)
    {
        $this->sinistre = $sinistre;
        $this->agent = $agent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', \App\Channels\YellikaSmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Prise en charge de votre déclaration de sinistre')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous informons qu\'un agent a récupéré votre déclaration de sinistre #' . $this->sinistre->id . '.')
            ->line('L\'agent **' . $this->agent->name . '** est désormais en charge de votre dossier et est en route pour effectuer le constat.')
            ->line('Type de sinistre : ' . str_replace('_', ' ', $this->sinistre->type_sinistre))
            ->line('Merci de rester disponible sur le lieu du sinistre si nécessaire.')
            ->action('Voir les détails', url('/assure/sinistres/' . $this->sinistre->id))
            ->line('Merci de votre confiance !');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toYellika(object $notifiable): string
    {
        return "CLAIMS MASTER : Bonjour {$notifiable->name}, l'agent {$this->agent->name} a recupere votre declaration #{$this->sinistre->id} et est en route.";
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sinistre_id' => $this->sinistre->id,
            'agent_name' => $this->agent->name,
            'message' => 'L\'agent ' . $this->agent->name . ' a récupéré votre sinistre et est en route.',
            'type' => 'claim_recovered'
        ];
    }
}
