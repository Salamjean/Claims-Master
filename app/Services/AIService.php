<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Analyse la description du sinistre pour déterminer sa gravité et le contexte
     * Retourne un tableau structuré (JSON décodé).
     */
    public function analyzeDeclarationText(string $typeSinistre, string $description, array $availableTypes = [])
    {
        if (!$this->apiKey) {
            Log::warning("Gemini API Key is missing. Using fallback for analysis.");
            return [
                'gravity' => 'medium',
                'context' => 'Analyse simulée: ' . substr($description, 0, 50),
                'recommended_docs' => $availableTypes
            ];
        }

        try {
            $prompt = "Tu es un expert en assurance. L'assuré a déclaré un sinistre de type : \"{$typeSinistre}\".\n";
            $prompt .= "Analyse la description suivante pour en déduire le contexte :\n\"{$description}\"\n\n";
            if (!empty($availableTypes)) {
                $prompt .= "Voici une liste de types de documents déjà configurés par l'assurance : " . implode(', ', $availableTypes) . ".\n";
                $prompt .= "Ta mission : Propose une liste de documents strictement obligatoires pour ce cas précis.\n";
            }
            $prompt .= "Renvoie UNIQUEMENT un objet JSON avec :\n";
            $prompt .= "- 'gravity' (low, medium, high)\n";
            $prompt .= "- 'context' (résumé court)\n";
            $prompt .= "- 'recommended_docs' (tableau de noms).\n";

            $url = $this->baseUrl . '/gemini-2.5-flash:generateContent?key=' . $this->apiKey;

            $response = Http::withOptions(['verify' => false])
                ->timeout(60)
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                $decoded = json_decode(trim(preg_replace('/```json\s*|\s*```/', '', $content)), true);
                return is_array($decoded) ? $decoded : null;
            }
        } catch (\Exception $e) {
            Log::error('Gemini Analysis Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Vérifie si l'image correspond au document attendu.
     */
    public function verifyDocumentImage(string $imagePath, string $expectedDocument)
    {
        if (!$this->apiKey) {
            return ['status' => 'valid', 'feedback' => 'Validation simulée.'];
        }

        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $prompt = "Ceci est un document de type : \"{$expectedDocument}\". Est-ce correct et lisible ?\n";
            $prompt .= "Renvoie JSON : {\"status\": \"valid\"|\"invalid\", \"feedback\": \"...\"}";

            return $this->callGeminiVision($prompt, $imageData, $mimeType);
        } catch (\Exception $e) {
            Log::error('Gemini Vision Exception: ' . $e->getMessage());
        }

        return ['status' => 'pending', 'feedback' => 'Erreur technique.'];
    }

    /**
     * Vérifie spécifiquement l'attestation d'assurance par rapport aux données du formulaire.
     */
    public function verifyAttestation(string $imagePath, array $formData)
    {
        if (!$this->apiKey) {
            return ['status' => 'valid', 'feedback' => 'Validation simulée.'];
        }

        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $prompt = "Tu es un expert en audit de documents d'assurance en Côte d'Ivoire. Ta mission est de scanner l'attestation d'assurance jointe et de vérifier UNIQUEMENT si l'immatriculation du véhicule correspond.\n\n";
            $prompt .= "Données à vérifier :\n";
            $prompt .= "- Immatriculation attendue : " . ($formData['plaque'] ?? 'Non spécifiée') . "\n\n";
            $prompt .= "Extraits également le nom de la compagnie d'assurance (Assureur) mentionné sur le document.\n\n";
            $prompt .= "Règles d'analyse :\n";
            $prompt .= "1. Localise le numéro d'immatriculation sur le document.\n";
            $prompt .= "2. COMPARE consciencieusement. Ignore absolument les tirets ou espaces (ex: '1234 AB 01' est identique à '1234AB01').\n";
            $prompt .= "3. Si l'immatriculation lue sur le document est différente de celle attendue, le statut est 'invalid'.\n";
            $prompt .= "4. Si l'immatriculation correspond parfaitement ou est très proche (faute de frappe mineure évidente), réponds 'valid'.\n";
            $prompt .= "5. TRÈS IMPORTANT : IGNORE toutes les autres informations (numéro de contrat, marque, modèle, dates) SAUF le nom de l'assureur.\n\n";
            $prompt .= "Réponds UNIQUEMENT au format JSON : {\"status\": \"valid\"|\"invalid\", \"feedback\": \"Explication concise\", \"assureur\": \"NOM_DE_L_ASSUREUR_TROUVE\"}";

            return $this->callGeminiVision($prompt, $imageData, $mimeType);
        } catch (\Exception $e) {
            Log::error('Gemini Attestation Exception: ' . $e->getMessage());
        }

        return ['status' => 'pending', 'feedback' => 'Erreur technique.'];
    }

    /**
     * Vérifie si l'image téléchargée correspond au modèle de référence.
     */
    public function verifyTemplateMatch(string $uploadedImagePath)
    {
        if (!$this->apiKey) {
            return ['status' => 'valid', 'feedback' => 'Validation simulée.'];
        }

        $templatePath = storage_path('app/public/templates/asaci_template.jpg');
        if (!file_exists($templatePath))
            return ['status' => 'valid', 'feedback' => 'Modèle absent.'];

        try {
            $uploadedData = base64_encode(file_get_contents($uploadedImagePath));
            $templateData = base64_encode(file_get_contents($templatePath));

            $mimeType = mime_content_type($uploadedImagePath);

            $contents = [
                [
                    'parts' => [
                        ['text' => "Tu as deux images : le 'MODÈLE ASACI' (image 1) et le 'DOCUMENT SCANNE' (image 2). 
                        Vérifie si le document scanné respecte la STRUCTURE TYPE des attestations d'assurance automobile en Côte d'Ivoire (Modèle ASACI).
                        
                        Critères :
                        - Disposition des cadres et des lignes identique.
                        - Présence des en-têtes standards.
                        - Même format général de formulaire.
                        
                        Note : Les données remplies au stylo ou à l'imprimante varient d'un client à l'autre, c'est NORMAL. Fais abstraction du contenu des textes remplis, concentre-toi sur le FOND et la MISE EN PAGE.
                        
                        Réponds JSON : {\"status\": \"valid\"|\"invalid\", \"feedback\": \"Justification concise en français\"}"],
                        ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => $templateData]],
                        ['inlineData' => ['mimeType' => $mimeType, 'data' => $uploadedData]]
                    ]
                ]
            ];

            return $this->callGeminiVisionDetailed($contents);
        } catch (\Exception $e) {
            Log::error('Gemini Template Exception: ' . $e->getMessage());
        }

        return ['status' => 'pending', 'feedback' => 'Erreur technique.'];
    }

    /**
     * Helper pour appeler Gemini Vision avec une structure de contenu personnalisée.
     */
    protected function callGeminiVisionDetailed(array $contents)
    {
        $url = $this->baseUrl . '/gemini-2.5-flash:generateContent?key=' . $this->apiKey;
        $maxAttempts = 3;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $response = Http::withOptions(['verify' => false])->timeout(60)->post($url, [
                    'contents' => $contents,
                    'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 600, 'responseMimeType' => 'application/json']
                ]);

                if ($response->successful()) {
                    $content = $response->json('candidates.0.content.parts.0.text');
                    $decoded = json_decode(trim(preg_replace('/```json\s*|\s*```/', '', $content)), true);
                    if (is_array($decoded))
                        return $decoded;
                }

                if ($response->status() === 503 && $attempt < $maxAttempts - 1) {
                    $attempt++;
                    sleep(2);
                    continue;
                }
            } catch (\Exception $e) {
                Log::error('Attempt fail: ' . $e->getMessage());
            }
            break;
        }

        return ['status' => 'pending', 'feedback' => 'Format de réponse IA invalide ou serveur indisponible.'];
    }

    /**
     * Helper pour appeler Gemini Vision API.
     */
    protected function callGeminiVision(string $prompt, string $imageData, string $mimeType)
    {
        $url = $this->baseUrl . '/gemini-2.5-flash:generateContent?key=' . $this->apiKey;
        $maxAttempts = 3;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $response = Http::withOptions(['verify' => false])->timeout(60)->post($url, [
                    'contents' => [['parts' => [['text' => $prompt], ['inlineData' => ['mimeType' => $mimeType, 'data' => $imageData]]]]],
                    'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 600, 'responseMimeType' => 'application/json']
                ]);

                if ($response->successful()) {
                    $content = $response->json('candidates.0.content.parts.0.text');
                    $decoded = json_decode(trim(preg_replace('/```json\s*|\s*```/', '', $content)), true);
                    if (is_array($decoded))
                        return $decoded;
                }

                if ($response->status() === 503 && $attempt < $maxAttempts - 1) {
                    $attempt++;
                    sleep(2);
                    continue;
                }
            } catch (\Exception $e) {
                Log::error('Attempt fail: ' . $e->getMessage());
            }
            break;
        }

        return ['status' => 'pending', 'feedback' => 'Format de réponse IA invalide ou serveur indisponible.'];
    }

    /**
     * Génère un message professionnel.
     */
    public function generateDocumentRequestMessage(\App\Models\Sinistre $sinistre, array $requiredDocs)
    {
        if (!$this->apiKey)
            return "Documents requis : " . implode(', ', $requiredDocs);

        try {
            $prompt = "Rédige un message pour demander : " . implode(', ', $requiredDocs) . " pour un sinistre " . $sinistre->type_sinistre;
            $url = $this->baseUrl . '/gemini-2.5-flash:generateContent?key=' . $this->apiKey;

            $response = Http::withOptions(['verify' => false])->timeout(60)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 600]
            ]);

            if ($response->successful()) {
                return trim($response->json('candidates.0.content.parts.0.text'));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return "Veuillez fournir les documents suivants pour votre sinistre #" . $sinistre->id;
    }
}
