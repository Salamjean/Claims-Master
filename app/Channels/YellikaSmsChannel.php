<?php

namespace App\Channels;

use App\Services\YellikaSmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class YellikaSmsChannel
{
    protected $smsService;

    public function __construct(YellikaSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toYellika')) {
            return;
        }

        $message = $notification->toYellika($notifiable);
        
        // On récupère le numéro de téléphone (contact)
        $to = $notifiable->routeNotificationFor('yellika') ?: $notifiable->contact;

        if (!$to) {
            Log::warning('Impossible d\'envoyer le SMS : aucun numéro de contact trouvé pour ' . get_class($notifiable));
            return;
        }

        // Nettoyage du numéro
        $to = preg_replace('/[^0-9]/', '', $to);

        if (strlen($to) >= 8) {
            $this->smsService->sendSms($to, $message);
        } else {
            Log::warning("Numéro de téléphone invalide pour l'envoi SMS Yellika : " . $to);
        }
    }
}
