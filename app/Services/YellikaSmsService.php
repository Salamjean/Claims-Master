<?php

namespace App\Services;

use App\Repositories\SmsRepository;
use Illuminate\Support\Facades\Log;

class YellikaSmsService
{
    /**
     * Envoie un SMS via le service Yellika (1smsafrica).
     *
     * @param string $to      Numéro de téléphone du destinataire.
     * @param string $message Contenu du message.
     * @return array|bool La réponse du service ou false en cas d'erreur.
     */
    public function sendSms(string $to, string $message): array|bool
    {
        Log::info('Tentative d\'envoi de SMS Yellika à : ' . $to);

        try {
            $sms = new SmsRepository($to, $message);
            $result = $sms->send();

            if ($result['success']) {
                Log::info('SMS Yellika envoyé avec succès.');
                return $result;
            }

            Log::error('Échec de l\'envoi du SMS Yellika : ' . ($result['error'] ?? 'Erreur inconnue'));
            return false;

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi du SMS Yellika : ' . $e->getMessage());
            return false;
        }
    }
}
