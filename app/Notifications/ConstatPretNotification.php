<?php

namespace App\Notifications;

use App\Models\Sinistre;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ConstatPretNotification extends Notification
{
    use Queueable;

    public function __construct(public Sinistre $sinistre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $serviceName = $this->sinistre->service->name ?? '';
        return [
            'title'   => '📋 Votre constat est prêt',
            'message' => "Le constat de votre sinistre #{$this->sinistre->numero_sinistre} a été rédigé. "
                       . "Vous pouvez le récupérer auprès du service {$serviceName}.",
            'sinistre_id'     => $this->sinistre->id,
            'numero_sinistre' => $this->sinistre->numero_sinistre,
            'type'            => 'constat_pret',
        ];
    }
}
