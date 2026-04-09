<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceConstatAccessNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Accès à votre espace Service de Constats - Claims Master')
            ->greeting('Bonjour ' . $this->user->name . ',')
            ->line('Votre compte de Service de Constats (' . ucfirst($this->user->role) . ') a été créé avec succès sur la plateforme Claims Master.')
            ->line('Voici vos identifiants pour vous connecter :')
            ->line('Email : ' . $this->user->email)
            ->line('Mot de passe temporaire : ' . $this->password)
            ->action('Se Connecter', route('login'))
            ->line('Il vous sera demandé de changer ce mot de passe lors de votre première connexion.')
            ->line('Merci de faire confiance à Claims Master pour la gestion de vos opérations.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
