<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoie un SMS via l'API Yellika (app.1smsafrica.com)
     */
    public static function send(string $to, string $message): bool
    {
        $apiUrl   = rtrim(config('services.yellika.api_url'), '/');
        $apiKey   = config('services.yellika.api_key');
        $senderId = config('services.yellika.sender_id', 'Plateau app');

        // Formatage du numéro pour l'API (International sans le '+')
        // Ex: 0707070707 -> 2250707070707
        $to = preg_replace('/\D/', '', $to); // Garde uniquement les chiffres

        // Si le numéro commence par 0, on suppose que c'est un numéro local (Côte d'Ivoire)
        if (str_starts_with($to, '0')) {
            $to = '225' . ltrim($to, '0');
        }

        // Si le numéro fait 10 chiffres (nouveau format CI sans indicatif), on ajoute 225
        if (strlen($to) === 10) {
            $to = '225' . $to;
        }

        Log::info("[Yellika SMS] Tentative vers {$to} | Message: {$message}");

        try {
            // L'API Yellika (v3) utilise généralement l'endpoint 'sms/send' ou 'sms/quick-send'
            // On tente l'envoi avec les paramètres standards
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post("{$apiUrl}/sms/send", [
                'recipient' => $to,
                'sender_id' => $senderId,
                'type'      => 'plain',
                'message'   => $message,
            ]);

            $status = $response->status();
            $body   = $response->json();

            if ($response->successful()) {
                Log::info("[Yellika SMS] Succès pour {$to}. API Response: " . json_encode($body));
                return true;
            }

            Log::error("[Yellika SMS] Erreur API ({$status}): " . json_encode($body));
            return false;

        } catch (\Throwable $e) {
            Log::error("[Yellika SMS] Exception fatale: " . $e->getMessage());
            return false;
        }
    }
}
