<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PersonnelAccessNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $code;
    protected $assuranceName;

    public function __construct($user, $code, $assuranceName)
    {
        $this->user          = $user;
        $this->code          = $code;
        $this->assuranceName = $assuranceName;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $actionUrl = route('account.define', ['email' => $this->user->email]);

        return (new MailMessage)
            ->subject('Activation de votre compte Personnel - Claims Master')
            ->from('infos@plateau-apps.com', 'CLAIMS MASTER')
            ->view('emails.personnel_access', [
                'name'          => $this->user->name . ' ' . ($this->user->prenom ?? ''),
                'assuranceName' => $this->assuranceName,
                'code'          => $this->code,
                'actionUrl'     => $actionUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
