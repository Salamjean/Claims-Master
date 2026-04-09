<?php

namespace App\Notifications;

use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSinistreNotification extends Notification
{
    use Queueable;

    protected $sinistre;
    protected $assurance;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sinistre $sinistre, User $assurance)
    {
        $this->sinistre = $sinistre;
        $this->assurance = $assurance;
    }

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
        return (new MailMessage)
            ->subject('Alerte Sinistre #' . $this->sinistre->id . ' - Claims Master')
            ->view('emails.new_sinistre', [
                'sinistre' => $this->sinistre,
                'assurance' => $this->assurance,
            ]);
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
            'type_sinistre' => $this->sinistre->type_sinistre,
            'message' => 'Nouveau sinistre déclaré par ' . $this->sinistre->assure->name,
        ];
    }
}
