<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssureAccessNotification extends Notification
{
    use Queueable;

    public string $nom;
    public string $codeUser;
    public string $plainPassword;

    public function __construct(string $nom, string $codeUser, string $plainPassword)
    {
        $this->nom = $nom;
        $this->codeUser = $codeUser;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Canaux de notification
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Contenu de l'email
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CLAIMS MASTER : Vos accès assurés')
            ->from('infos@plateau-apps.com', 'CLAIMS MASTER')
            ->view('emails.assure_access', [
                'nom' => $this->nom,
                'codeUser' => $this->codeUser,
                'plainPassword' => $this->plainPassword,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
