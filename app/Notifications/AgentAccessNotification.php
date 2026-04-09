<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentAccessNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $password;
    protected $serviceName;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $password, $serviceName)
    {
        $this->user = $user;
        $this->password = $password;
        $this->serviceName = $serviceName;
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
        $actionUrl = route('account.define', ['email' => $this->user->email]);

        return (new MailMessage)
            ->subject('Activation de votre compte Agent - Claims Master')
            ->from('infos@plateau-apps.com', 'CLAIMS MASTER')
            ->view('emails.agent_invitation', [
                'name' => $this->user->name,
                'serviceName' => $this->serviceName,
                'code' => $this->password, // Ici $this->password contient le code OTP
                'actionUrl' => $actionUrl,
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
