<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOTP extends Notification
{
    use Queueable;

    public $code;
    public $email;
    public $logoUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($code, $email)
    {
        $this->code = $code;
        $this->email = $email;
        $this->logoUrl = asset('assetsPoster/assets/images/logo_car225.png'); // Réutilisation du logo existant
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
            ->subject('Réinitialisation de votre mot de passe - CLAIMS MASTER')
            ->from('infos@plateau-apps.com', 'CLAIMS MASTER')
            ->view('emails.password_reset_otp', [
                'code' => $this->code,
                'email' => $this->email,
                'logoUrl' => $this->logoUrl,
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
            //
        ];
    }
}
