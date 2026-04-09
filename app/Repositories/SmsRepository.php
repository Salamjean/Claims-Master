<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsRepository
{
    protected string $to;
    protected string $message;

    public function __construct(string $to, string $message)
    {
        $this->to = $this->formatPhone($to);
        $this->message = $this->stripAccents($message);
    }

    /**
     * Formate le numéro en format international (ex: 2250700000000)
     */
    private function formatPhone(string $phone): string
    {
        // Garde uniquement les chiffres
        $phone = preg_replace('/\D/', '', $phone);

        // Si déjà en format international (commence par 225 + 10 chiffres = 13 digits)
        if (str_starts_with($phone, '225') && strlen($phone) === 13) {
            return $phone;
        }

        // Numéro local ivoirien à 10 chiffres (ex: 0798278981) → +2250798278981
        if (strlen($phone) === 10) {
            return '+225' . $phone;
        }

        // Si le numéro commence déjà par 225 (format international sans +)
        if (str_starts_with($phone, '225')) {
            return '+' . $phone;
        }

        // Autres cas : on préfixe par +225
        return '+225' . $phone;
    }

    /**
     * Supprime les accents pour la compatibilité SMS Plain Text (vs Unicode)
     */
    private function stripAccents(string $str): string
    {
        $search  = ['À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'];
        $replace = ['A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y'];
        return str_replace($search, $replace, $str);
    }

    /**
     * Envoie le SMS via l'API 1smsafrica
     */
    public function send(): array
    {
        $apiUrl = rtrim(env('YELLIKA_API_URL', 'https://app.1smsafrica.com/api/v3'), '/');
        $apiKey = env('YELLIKA_API_KEY');
        $senderId = env('YELLIKA_SENDER_ID', 'Notify');

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->post($apiUrl . '/sms/send', [
                    'recipient' => $this->to,
                    'sender_id' => $senderId,
                    'message' => $this->message,
                    'type' => 'plain',
                ]);

        Log::info('Yellika SMS response', ['status' => $response->status(), 'body' => $response->json()]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return [
            'success' => false,
            'error' => $response->json()['message'] ?? 'Erreur inconnue (' . $response->status() . ')',
        ];
    }
}
