<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaveService
{
    protected $apiKey;
    protected $webhookSecret;
    protected $baseUrl = 'https://api.wave.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.wave.api_key', env('WAVE_API_KEY'));
        $this->webhookSecret = config('services.wave.webhook_secret', env('WAVE_WEBHOOK_SECRET'));
    }

    /**
     * Crée une session de paiement Wave
     */
    public function createCheckoutSession($amount, $currency, $successUrl, $errorUrl, $metadata = [])
    {
        try {
            $request = Http::withToken($this->apiKey);
            
            // Bypass SSL en local si nécessaire pour cURL error 60
            if (app()->environment('local')) {
                $request->withoutVerifying();
            }

            // Wave CI exige du HTTPS pour les URLs de retour
            $successUrl = str_replace('http://', 'https://', $successUrl);
            $errorUrl = str_replace('http://', 'https://', $errorUrl);

            $payload = [
                'amount'      => (string)$amount,
                'currency'    => $currency,
                'success_url' => $successUrl,
                'error_url'   => $errorUrl,
            ];

            Log::info('Tentative Création Session Wave', ['payload' => $payload]);

            $response = $request->post($this->baseUrl . '/checkout/sessions', $payload);

            if ($response->successful()) {
                Log::info('Session Wave créee avec succès', ['id' => $response->json()['id'] ?? 'N/A']);
                return $response->json();
            }

            Log::error('Erreur API Wave', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Exception API Wave : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les détails d'une session de paiement Wave
     */
    public function retrieveCheckoutSession($sessionId)
    {
        try {
            $request = Http::withToken($this->apiKey);
            
            if (app()->environment('local')) {
                $request->withoutVerifying();
            }

            $response = $request->get($this->baseUrl . '/checkout/sessions/' . $sessionId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erreur Récupération Wave : ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Exception Récupération Wave : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifie la signature du webhook Wave
     */
    public function verifyWebhook($body, $signatureHeader)
    {
        // Format du header : t=TIMESTAMP,v1=SIGNATURE
        if (!$signatureHeader) return false;

        $parts = explode(',', $signatureHeader);
        if (count($parts) < 2) return false;

        $timestamp = str_replace('t=', '', $parts[0]);
        $signature = str_replace('v1=', '', $parts[1]);

        $signedPayload = $timestamp . $body;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }
}
