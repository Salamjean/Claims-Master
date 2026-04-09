<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDeadlineNotification extends Notification
{
    use Queueable;

    protected $intervention;

    /**
     * Create a new notification instance.
     */
    public function __construct($intervention)
    {
        $this->intervention = $intervention;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'intervention_id' => $this->intervention->id,
            'reference' => $this->intervention->reference,
            'libelle' => $this->intervention->libelle,
            'montant' => $this->intervention->montant,
            'date_prevue' => $this->intervention->date_paiement_prevue,
            'message' => "Le paiement de l'intervention {$this->intervention->reference} est prévu pour le " . \Carbon\Carbon::parse($this->intervention->date_paiement_prevue)->format('d/m/Y') . ".",
        ];
    }
}
