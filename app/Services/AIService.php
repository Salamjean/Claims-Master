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
        $this->model  = config('services.gemini.model', 'gemini-3.5-flash');
    }

    /**
     * Reçois les options HTTP de base (avec gestion du proxy si configuré)
     */
    protected function getHttpOptions(): array
    {
        $options = ['verify' => false];
        $proxy = config('services.gemini.proxy');
        if (!empty($proxy)) {
            $options['proxy'] = $proxy;
        }
        return $options;
    }

    /**
     * Vérifie si un document doit être exclu car ses informations sont déjà récupérées
     * (Informations véhicule, informations assuré / permis, Bon de prise en charge).
     */
    public static function isDocumentExcluded(string $docName): bool
    {
        $lower = mb_strtolower(trim($docName));

        // 1. Exclure "Bon de prise en charge"
        if (str_contains($lower, 'prise en charge') || str_contains($lower, 'bon de prise')) {
            return true;
        }

        // 2. Exclure pièces du véhicule (carte grise, pièces du véhicule)
        if (str_contains($lower, 'pièces du véhicule') || str_contains($lower, 'pieces du vehicule') || str_contains($lower, 'carte grise')) {
            return true;
        }

        // 3. Exclure permis du conducteur / permis de conduire
        return false;
    }

    /**
     * Détermine si un document est strictement indispensable / obligatoire ou optionnel.
     */
    public static function isDocumentMandatory(string $docName): bool
    {
        $lower = mb_strtolower(trim($docName));

        // Documents de base indispensables (Déclaration, Constat/PV, Plainte, Certificat médical initial/décès)
        $essentialKeywords = [
            'déclaration',
            'declaration',
            'constat',
            'pv police',
            'gendarmerie',
            'dépôt de plainte',
            'depot de plainte',
            'récépissé',
            'recepisse',
            'certificat médical initial',
            'certificat de décès',
            'certificat de genre de mort'
        ];

        foreach ($essentialKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                return true;
            }
        }

        // Tout le reste (Factures de réparation/soins, Rapports d'expertise, Photos, etc.) est optionnel / complémentaire
        return false;
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
                'Constat amiable ou PV police/gendarmerie',
                'Rapport d’expertise automobile',
                'Facture originale acquittée des réparations'
            ];
        }
        // 4.2 Accident corporel
        elseif (str_contains($typeNorm, 'corporel') || str_contains($typeNorm, 'blessure') || str_contains($typeNorm, 'décès') || str_contains($typeNorm, 'deces')) {
            $docs = [
                'Déclaration circonstanciée du sinistre',
                'PV police/gendarmerie ou constat amiable'
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
                'Récépissé de dépôt de plainte'
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
                'Constat amiable si accident associé',
                'Facture originale du remplacement du vitrage'
            ];
        }
        // 4.6 Recours (Sinistre Non Responsable)
        elseif (str_contains($typeNorm, 'recours') || str_contains($typeNorm, 'non responsable') || str_contains($typeNorm, 'tierce')) {
            $docs = [
                'Déclaration circonstanciée',
                'Constat amiable ou PV police/gendarmerie',
                'Rapport d’expertise original',
                'Facture acquittée des réparations'
            ];
        }
        else {
            $docs = [
                'Déclaration circonstanciée du sinistre',
                'Constat amiable ou PV police/gendarmerie'
            ];
        }

        $cleanDocs = array_filter($docs, fn($d) => !self::isDocumentExcluded($d));

        return array_values(array_unique($cleanDocs));
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

            $response = Http::withOptions($this->getHttpOptions())
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
                    // FILTRAGE STRICT : Ne conserver EXCLUSIVEMENT que les pièces non exclues
                    $filteredDocs = array_values(array_filter($decoded['recommended_docs'], function($doc) use ($defaultDocs) {
                        return in_array($doc, $defaultDocs) && !self::isDocumentExcluded($doc);
                    }));

                    $decoded['recommended_docs'] = !empty($filteredDocs) ? $filteredDocs : $defaultDocs;
                    return $decoded;
                }
            } else {
                $body = $response->body();
                if (str_contains($body, 'User location is not supported')) {
                    Log::warning("Google Gemini API restreint sur l'IP du serveur de production. Basculement sur la grille réglementaire locale.");
                } else {
                    Log::error('Gemini API Error in analyzeDeclarationText: ' . $body);
                }
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
     * Analyse et convertit n'importe quel format de date (ISO YYYY-MM-DD, FR DD/MM/YYYY, etc.) en objet Carbon.
     */
    public static function parseFlexibleDate(?string $dateStr): ?\Carbon\Carbon
    {
        if (empty($dateStr)) {
            return null;
        }

        $clean = trim($dateStr);

        // Si le format contient JJ/MM/AAAA ou JJ-MM-AAAA (Format Français)
        if (preg_match('/(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{4})/', $clean, $m)) {
            return \Carbon\Carbon::createFromDate((int)$m[3], (int)$m[2], (int)$m[1])->startOfDay();
        }

        // Si le format est AAAA-MM-JJ ou AAAA/MM/JJ (Format ISO)
        if (preg_match('/(\d{4})[\/\.-](\d{1,2})[\/\.-](\d{1,2})/', $clean, $m)) {
            return \Carbon\Carbon::createFromDate((int)$m[1], (int)$m[2], (int)$m[3])->startOfDay();
        }

        $normalized = str_replace('/', '-', $clean);
        try {
            return \Carbon\Carbon::parse($normalized)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Audit complet et extraction de l'attestation d'assurance (Structure ASACI, Plaque, Marque, Échéance, Assureur)
     */
    public function verifyAttestation(string $imagePath, array $formData)
    {
        if (!$this->apiKey) {
            return ['status' => 'valid', 'feedback' => 'Validation simulée.', 'date_expiration' => null];
        }

        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $prompt = "Tu es un expert qualifié en audit et contrôle de conformité des attestations d'assurance automobile en Côte d'Ivoire (Format ASACI).\n";
            $prompt .= "Ta mission est de scanner l'attestation d'assurance transmise et d'analyser les informations suivantes :\n\n";

            $prompt .= "--- DONNÉES ENREGISTRÉES DU VÉHICULE ---\n";
            $prompt .= "- Plaque d'immatriculation attendue : " . ($formData['plaque'] ?? 'Non spécifiée') . "\n";
            if (!empty($formData['marque'])) {
                $prompt .= "- Marque attendue : " . $formData['marque'] . "\n";
            }
            if (!empty($formData['modele'])) {
                $prompt .= "- Modèle attendu : " . $formData['modele'] . "\n";
            }
            if (!empty($formData['numero_contrat'])) {
                $prompt .= "- Numéro de Police / Contrat attendu : " . $formData['numero_contrat'] . "\n";
            }

            $prompt .= "\n--- EXTRACTION DES INFORMATIONS CLÉS ---\n";
            $prompt .= "1. 'assureur' : Nom de la compagnie d'assurance (ex: NSIA, SANLAM, ALLIANZ, AXA, SUNU, etc.).\n";
            $prompt .= "2. 'date_expiration' : La date de fin / d'échéance de l'assurance (la 2ème date après 'Au', 'au', 'Date d'échéance', 'Fin de validité'). Ex: '09/02/2027', '2027-02-09'. Ne mets null que si absente.\n\n";

            $prompt .= "--- RÈGLES DE VALIDATION (STATUS 'valid' OU 'invalid') ---\n";
            $prompt .= "1. STRUCTURE ASACI : Vérifie que le document a l'apparence d'une attestation d'assurance automobile officielle. Si ce n'est pas une attestation d'assurance, réponds status: 'invalid' et feedback: 'Le document téléversé n'est pas une attestation d'assurance automobile.'\n";
            $prompt .= "2. PLAQUE D'IMMATRICULATION : Compare la plaque trouvée avec la plaque attendue (" . ($formData['plaque'] ?? '') . "), en ignorant tirets et espaces. Si la plaque sur le document diffère de la plaque attendue, réponds status: 'invalid'.\n";
            $prompt .= "3. MARQUE / POLICE : Si la marque ou le numéro de police sur l'attestation contredit le véhicule enregistré, réponds status: 'invalid'.\n";
            $prompt .= "4. SI TOUT EST CONFORME : Réponds status: 'valid' et un feedback positif concise.\n\n";

            $prompt .= "Réponds UNIQUEMENT au format JSON strict : {\"status\": \"valid\"|\"invalid\", \"feedback\": \"Explication concise en français\", \"assureur\": \"NOM_ASSUREUR\", \"date_expiration\": \"DATE_EXTRAITE_OU_NULL\"}";

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
        $modelsToTry = array_unique([
            $this->model,
            'gemini-1.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-pro'
        ]);

        foreach ($modelsToTry as $m) {
            $url = $this->baseUrl . '/' . $m . ':generateContent?key=' . $this->apiKey;

            try {
                $response = Http::withOptions($this->getHttpOptions())->timeout(15)->post($url, [
                    'contents' => $contents,
                    'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 600, 'responseMimeType' => 'application/json']
                ]);

                if ($response->successful()) {
                    $content = $response->json('candidates.0.content.parts.0.text');
                    $decoded = json_decode(trim(preg_replace('/```json\s*|\s*```/', '', $content)), true);
                    if (is_array($decoded)) {
                        return $decoded;
                    }
                } else {
                    $body = $response->body();
                    $status = $response->status();
                    if (str_contains($body, 'User location is not supported')) {
                        Log::warning("Gemini API non disponible pour l'IP du serveur de production (User location not supported). Basculement automatique sur la règle locale.");
                        break;
                    }
                    if ($status === 403 || $status === 401) {
                        Log::warning("Gemini API Erreur d'accès/clé (Status $status) sur modèle '$m' : " . $body);
                        break; // Inutile de boucler sur d'autres modèles si la clé/compte est refusé
                    }
                    Log::warning("Gemini Vision Detailed model '$m' attempt failed (Status " . $status . ")");
                }
            } catch (\Exception $e) {
                Log::error("Gemini Vision Detailed model '$m' exception: " . $e->getMessage());
            }
        }

        return ['status' => 'pending', 'feedback' => 'Analyse manuelle requise (Accès ou localisation non supportés).'];
    }

    /**
     * Helper pour appeler Gemini Vision API.
     */
    protected function callGeminiVision(string $prompt, string $imageData, string $mimeType)
    {
        $modelsToTry = array_unique([
            $this->model,
            'gemini-1.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-pro'
        ]);

        foreach ($modelsToTry as $m) {
            $url = $this->baseUrl . '/' . $m . ':generateContent?key=' . $this->apiKey;

            try {
                $response = Http::withOptions($this->getHttpOptions())->timeout(15)->post($url, [
                    'contents' => [['parts' => [['text' => $prompt], ['inlineData' => ['mimeType' => $mimeType, 'data' => $imageData]]]]],
                    'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 600, 'responseMimeType' => 'application/json']
                ]);

                if ($response->successful()) {
                    $content = $response->json('candidates.0.content.parts.0.text');
                    $decoded = json_decode(trim(preg_replace('/```json\s*|\s*```/', '', $content)), true);
                    if (is_array($decoded)) {
                        return $decoded;
                    }
                } else {
                    $body = $response->body();
                    $status = $response->status();
                    if (str_contains($body, 'User location is not supported')) {
                        Log::warning("Gemini API non disponible pour l'IP du serveur de production (User location not supported). Basculement automatique sur la règle locale.");
                        break;
                    }
                    if ($status === 403 || $status === 401) {
                        Log::warning("Gemini API Erreur d'accès/clé (Status $status) sur modèle '$m' : " . $body);
                        break;
                    }
                    Log::warning("Gemini Vision model '$m' attempt failed (Status " . $status . ")");
                }
            } catch (\Exception $e) {
                Log::error("Gemini Vision model '$m' exception: " . $e->getMessage());
            }
        }

        return ['status' => 'pending', 'feedback' => 'Analyse manuelle requise (Accès ou localisation non supportés).'];
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

            $response = Http::withOptions($this->getHttpOptions())->timeout(60)->post($url, [
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
