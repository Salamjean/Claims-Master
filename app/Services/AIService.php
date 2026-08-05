<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-3.5-flash');
    }

    /**
     * Obtenir la liste réglementaire par défaut des pièces obligatoires par type de sinistre.
     */
    public static function getDefaultRequiredDocsByType(string $typeSinistre, string $description = ''): array
    {
        $typeNorm = mb_strtolower($typeSinistre);
        $descNorm = mb_strtolower($description);

        $docs = [];

        // 4.1 Accident matériel
        if (str_contains($typeNorm, 'matériel') || str_contains($typeNorm, 'materiel') || str_contains($typeNorm, 'dommage')) {
            $docs = [
                'Déclaration circonstanciée du sinistre',
                'Photocopie des pièces du véhicule',
                'Photocopie du permis du conducteur',
                'Constat amiable ou PV police/gendarmerie',
                'Rapport d’expertise automobile',
                'Bon de prise en charge délivré par le courtier ou l’assureur',
                'Facture originale acquittée des réparations'
            ];
        }
        // 4.2 Accident corporel
        elseif (str_contains($typeNorm, 'corporel') || str_contains($typeNorm, 'blessure') || str_contains($typeNorm, 'décès') || str_contains($typeNorm, 'deces')) {
            $docs = [
                'Déclaration circonstanciée du sinistre',
                'PV police/gendarmerie ou constat amiable',
                'Photocopie du permis de conduire',
                'Photocopie des pièces du véhicule'
            ];

            if (str_contains($descNorm, 'décès') || str_contains($descNorm, 'deces') || str_contains($descNorm, 'mort')) {
                $docs = array_merge($docs, [
                    'Certificat de décès',
                    'Certificat de genre de mort',
                    'Acte de naissance du défunt',
                    'Acte de notoriété',
                    'Pièces d’identité des ayants droit'
                ]);
            } else {
                $docs = array_merge($docs, [
                    'Certificat médical initial',
                    'Certificat médical de guérison ou consolidation',
                    'Ordonnances médicales',
                    'Factures originales de soins',
                    'Justificatifs hospitaliers'
                ]);
            }
        }
        // 4.3 Vol / Vol à main armée
        elseif (str_contains($typeNorm, 'vol') || str_contains($typeNorm, 'braquage')) {
            $docs = [
                'Dépôt de plainte dans les 24h',
                'Déclaration circonstanciée du vol',
                'Récépissé de dépôt de plainte',
                'Photocopies : Carte grise, Visite technique, Vignette'
            ];

            if (str_contains($descNorm, 'retrouvé') || str_contains($descNorm, 'retrouve')) {
                $docs = array_merge($docs, [
                    'Attestation de retrouvaille',
                    'Description des dégâts',
                    'Lieu de retrouvaille'
                ]);
            } elseif (str_contains($descNorm, 'non retrouvé') || str_contains($descNorm, 'introuvable') || str_contains($descNorm, '1 mois') || str_contains($descNorm, 'un mois')) {
                $docs = array_merge($docs, [
                    'Attestation d’authenticité carte grise',
                    'Duplicata visite technique',
                    'Duplicata vignette',
                    'Attestation de non-gage',
                    'Certificat de vente légalisé à blanc',
                    'Double des clés',
                    'PV d’enquête préliminaire'
                ]);
            }
        }
        // 4.4 Incendie Automobile
        elseif (str_contains($typeNorm, 'incendie') || str_contains($typeNorm, 'feu')) {
            $docs = [
                'Déclaration circonstanciée du sinistre',
                'Photocopie des pièces du véhicule',
                'Photocopie permis conducteur',
                'Rapport des sapeurs-pompiers ou PV police',
                'Rapport d’expertise incendie',
                'Photographies du véhicule',
                'Facture d’achat ou justificatif de valeur'
            ];
        }
        // 4.5 Bris de glace
        elseif (str_contains($typeNorm, 'bris') || str_contains($typeNorm, 'glace') || str_contains($typeNorm, 'pare-brise') || str_contains($typeNorm, 'vitre')) {
            $docs = [
                'Déclaration de sinistre',
                'Photocopie carte grise',
                'Photocopie permis conducteur',
                'Constat amiable si accident associé',
                'Facture originale du remplacement du vitrage'
            ];
        }
        // 4.6 Recours (Sinistre Non Responsable)
        elseif (str_contains($typeNorm, 'recours') || str_contains($typeNorm, 'non responsable') || str_contains($typeNorm, 'tierce')) {
            $docs = [
                'Déclaration circonstanciée',
                'Photocopie pièces du véhicule',
                'Photocopie permis conducteur',
                'Constat amiable ou PV police/gendarmerie',
                'Rapport d’expertise original',
                'Facture acquittée des réparations'
            ];
        }
        else {
            $docs = [
                'Déclaration circonstanciée du sinistre',
                'Photocopie des pièces du véhicule',
                'Photocopie du permis du conducteur',
                'Constat amiable ou PV police/gendarmerie'
            ];
        }

        return array_values(array_unique($docs));
    }

    /**
     * Analyse la description du sinistre pour déterminer sa gravité et le contexte
     * Retourne un tableau structuré (JSON décodé).
     */
    public function analyzeDeclarationText(string $typeSinistre, string $description, array $availableTypes = [])
    {
        $defaultDocs = self::getDefaultRequiredDocsByType($typeSinistre, $description);

        if (!$this->apiKey) {
            Log::warning("Gemini API Key is missing. Using fallback for analysis.");
            return [
                'gravity' => 'medium',
                'context' => 'Analyse basée exclusivement sur la grille officielle : ' . $typeSinistre,
                'recommended_docs' => $defaultDocs
            ];
        }

        try {
            $prompt = "Tu es un expert qualifié en gestion de sinistres d'assurance automobile.\n";
            $prompt .= "Voici la GRILLE EXCLUSIVE ET OFFICIELLE DES PIÈCES OBLIGATOIRES selon le type de sinistre :\n\n";
            
            $prompt .= "1. ACCIDENT MATÉRIEL :\n";
            foreach (self::getDefaultRequiredDocsByType('Accident_matériel') as $doc) {
                $prompt .= "- {$doc}\n";
            }
            
            $prompt .= "\n2. ACCIDENT CORPOREL :\n";
            foreach (self::getDefaultRequiredDocsByType('Accident_corporel', $description) as $doc) {
                $prompt .= "- {$doc}\n";
            }
            
            $prompt .= "\n3. VOL / VOL À MAIN ARMÉE :\n";
            foreach (self::getDefaultRequiredDocsByType('Vol', $description) as $doc) {
                $prompt .= "- {$doc}\n";
            }
            
            $prompt .= "\n4. INCENDIE AUTOMOBILE :\n";
            foreach (self::getDefaultRequiredDocsByType('Incendie') as $doc) {
                $prompt .= "- {$doc}\n";
            }
            
            $prompt .= "\n5. BRIS DE GLACE :\n";
            foreach (self::getDefaultRequiredDocsByType('Bris_de_glace') as $doc) {
                $prompt .= "- {$doc}\n";
            }
            
            $prompt .= "\n6. RECOURS (SINISTRE NON RESPONSABLE) :\n";
            foreach (self::getDefaultRequiredDocsByType('Recours') as $doc) {
                $prompt .= "- {$doc}\n";
            }
            
            $prompt .= "\n---\n";
            $prompt .= "CONTEXTE DU CLIENT :\n";
            $prompt .= "- Type de sinistre déclaré : \"{$typeSinistre}\"\n";
            $prompt .= "- Description du sinistre : \"{$description}\"\n\n";

            $prompt .= "RÈGLE ABSOLUE ET STRICTE :\n";
            $prompt .= "1. Tu NE DOIS EN AUCUN CAS inventer, reformuler ou ajouter de nouveaux documents hors de la liste officielle ci-dessus.\n";
            $prompt .= "2. Tu dois UNIQUEMENT choisir parmi les pièces exactes figurant dans la liste officielle ci-dessus.\n\n";

            $prompt .= "Renvoie UNIQUEMENT un objet JSON valide avec :\n";
            $prompt .= "- 'gravity' ('low', 'medium', 'high')\n";
            $prompt .= "- 'context' (résumé succinct en français)\n";
            $prompt .= "- 'recommended_docs' (tableau avec les noms exacts des pièces de la grille).\n";

            $url = $this->baseUrl . '/' . $this->model . ':generateContent?key=' . $this->apiKey;

            $response = Http::withOptions(['verify' => false])
                ->timeout(60)
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.0,
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                $decoded = json_decode(trim(preg_replace('/```json\s*|\s*```/', '', $content)), true);
                if (is_array($decoded) && isset($decoded['recommended_docs'])) {
                    // FILTRAGE STRICT : Ne conserver EXCLUSIVEMENT que les pièces présentes dans la liste officielle
                    $filteredDocs = array_values(array_filter($decoded['recommended_docs'], function($doc) use ($defaultDocs) {
                        return in_array($doc, $defaultDocs);
                    }));

                    $decoded['recommended_docs'] = !empty($filteredDocs) ? $filteredDocs : $defaultDocs;
                    return $decoded;
                }
            } else {
                Log::error('Gemini API Error in analyzeDeclarationText: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gemini Analysis Exception: ' . $e->getMessage());
        }

        // Fallback en cas d'erreur ou d'absence de réponse IA
        return [
            'gravity' => 'medium',
            'context' => 'Analyse basée exclusivement sur la grille officielle : ' . $typeSinistre,
            'recommended_docs' => $defaultDocs
        ];
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
        $url = $this->baseUrl . '/' . $this->model . ':generateContent?key=' . $this->apiKey;
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
                } else {
                    Log::error('Gemini API Error in callGeminiVisionDetailed (Attempt ' . ($attempt+1) . '): ' . $response->body());
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
        $url = $this->baseUrl . '/' . $this->model . ':generateContent?key=' . $this->apiKey;
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
                } else {
                    Log::error('Gemini API Error in callGeminiVision (Attempt ' . ($attempt+1) . '): ' . $response->body());
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
            $url = $this->baseUrl . '/' . $this->model . ':generateContent?key=' . $this->apiKey;

            $response = Http::withOptions(['verify' => false])->timeout(60)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 600]
            ]);

            if ($response->successful()) {
                return trim($response->json('candidates.0.content.parts.0.text'));
            } else {
                Log::error('Gemini API Error in generateDocumentRequestMessage: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return "Veuillez fournir les documents suivants pour votre sinistre #" . $sinistre->id;
    }
}
