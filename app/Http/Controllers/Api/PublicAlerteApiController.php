<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublicAlerteApiController extends Controller
{
    protected YellikaSmsService $sms;

    public function __construct(YellikaSmsService $sms)
    {
        $this->sms = $sms;
    }

    /**
     * POST /api/public/signaler-urgence
     * Déclaration d'une urgence publique via API JSON / Multipart.
     */
    public function signalerUrgence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_sinistre'     => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:5000'],
            'lieu'              => ['required', 'string', 'max:500'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'declarant_nom'     => ['nullable', 'string', 'max:255'],
            'declarant_contact'  => ['required', 'string', 'max:50'],
            'photos'            => ['required', 'array', 'min:1', 'max:3'],
            'photos.*'          => ['required', 'image', 'max:5120'], // 5MB max par photo
        ], [
            'type_sinistre.required'    => 'Le type d’incident est obligatoire.',
            'lieu.required'             => 'Le lieu de l’urgence est obligatoire.',
            'declarant_contact.required' => 'Le numéro de téléphone du déclarant est obligatoire.',
            'photos.required'           => 'Au moins 1 photo de la situation est requise.',
            'photos.min'                => 'Au moins 1 photo de la situation est requise.',
            'photos.max'                => 'Vous pouvez joindre au maximum 3 photos.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Enregistrement des photos
        $photosPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo->isValid()) {
                    $path = $photo->store('urgences', 'public');
                    $photosPaths[] = $path;
                }
            }
        }

        // Génération du token de suivi unique
        $token = Str::uuid()->toString();

        // Trouver le service Sapeurs-Pompiers le plus proche
        $nearestHospitalId = null;
        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $lat = (float) $validated['latitude'];
            $lng = (float) $validated['longitude'];

            $nearestHospital = \App\Models\User::where('role', 'hopital')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->sortBy(function ($h) use ($lat, $lng) {
                    return $this->haversine($lat, $lng, (float) $h->latitude, (float) $h->longitude);
                })
                ->first();

            $nearestHospitalId = $nearestHospital?->id;
        }

        // Création de l'enregistrement du sinistre
        $sinistre = Sinistre::create([
            'user_id'            => null,
            'type_sinistre'      => $validated['type_sinistre'],
            'description'        => $validated['description'] ?? null,
            'lieu'               => $validated['lieu'],
            'latitude'           => $validated['latitude'] ?? null,
            'longitude'          => $validated['longitude'] ?? null,
            'photos'             => $photosPaths ?: null,
            'declarant_nom'      => $validated['declarant_nom'] ?? null,
            'declarant_contact'  => $validated['declarant_contact'],
            'token_suivi'        => $token,
            'status'             => 'en_attente',
            'moyen_declaration'  => 'temoin_public',
            'date_declaration'   => now(),
            'date_survenance'    => now(),
            'nearest_hospital_id' => $nearestHospitalId,
            'hospital_status'    => 'en_attente',
        ]);

        // Envoi SMS de confirmation avec lien de suivi
        $lienSuivi = route('alerte.suivi', $token);
        $nomDeclarant = !empty($validated['declarant_nom']) ? ', ' . $validated['declarant_nom'] : '';
        $refSinistre = $sinistre->numero_sinistre ?? ('URG-' . $sinistre->id);
        $smsMessage = "Claims Master{$nomDeclarant}: Votre signalement d'urgence a ete enregistre. Ref: {$refSinistre}. Suivez l'intervention ici: {$lienSuivi}";

        try {
            $this->sms->sendSms($validated['declarant_contact'], $smsMessage);
        } catch (\Exception $e) {
            Log::warning('API Alerte SMS non envoyé: ' . $e->getMessage());
        }

        // Formater les URLs complètes des photos
        $photosUrls = array_map(function ($path) {
            return asset('storage/' . $path);
        }, $photosPaths);

        return response()->json([
            'success' => true,
            'message' => 'Votre alerte d’urgence a été transmise avec succès aux Sapeurs-Pompiers.',
            'data'    => [
                'id'                   => $sinistre->id,
                'numero_sinistre'      => $sinistre->numero_sinistre ?? ('URG-' . $sinistre->id),
                'token_suivi'          => $token,
                'lien_suivi_web'       => $lienSuivi,
                'type_sinistre'        => $sinistre->type_sinistre,
                'description'          => $sinistre->description,
                'lieu'                 => $sinistre->lieu,
                'latitude'             => $sinistre->latitude,
                'longitude'            => $sinistre->longitude,
                'declarant_nom'        => $sinistre->declarant_nom,
                'declarant_contact'    => $sinistre->declarant_contact,
                'photos_urls'          => $photosUrls,
                'hospital_status'      => $sinistre->hospital_status,
                'date_declaration'     => $sinistre->created_at?->toIso8601String(),
            ]
        ], 201);
    }

    /**
     * GET /api/public/suivi-alerte/{token}
     * Récupère le statut et les détails en temps réel d'une alerte publique.
     */
    public function suiviAlerte(string $token)
    {
        $sinistre = Sinistre::where('token_suivi', $token)
            ->with(['nearestHospital', 'assignedGroupe'])
            ->first();

        if (!$sinistre) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune alerte trouvée pour ce jeton de suivi.'
            ], 404);
        }

        $hospitalStatusLabels = [
            'en_attente'         => 'Alerte transmise - En attente de prise en charge',
            'ambulance_en_route' => 'Secours en route vers les lieux',
            'arrive'             => 'Sapeurs-Pompiers sur place - Prise en charge en cours',
            'termine'            => 'Intervention terminée',
        ];

        // Formatage des URLs de photos
        $photosUrls = [];
        if (is_array($sinistre->photos)) {
            $photosUrls = array_map(function ($p) {
                return asset('storage/' . $p);
            }, $sinistre->photos);
        }

        return response()->json([
            'success' => true,
            'message' => 'Détails de l’alerte d’urgence récupérés.',
            'data'    => [
                'sinistre' => [
                    'id'                    => $sinistre->id,
                    'numero_sinistre'       => $sinistre->numero_sinistre ?? ('URG-' . $sinistre->id),
                    'token_suivi'           => $sinistre->token_suivi,
                    'type_sinistre'         => $sinistre->type_sinistre,
                    'description'           => $sinistre->description,
                    'lieu'                  => $sinistre->lieu,
                    'latitude'              => $sinistre->latitude,
                    'longitude'             => $sinistre->longitude,
                    'declarant_nom'         => $sinistre->declarant_nom,
                    'declarant_contact'     => $sinistre->declarant_contact,
                    'photos_urls'           => $photosUrls,
                    'hospital_status'       => $sinistre->hospital_status,
                    'hospital_status_label' => $hospitalStatusLabels[$sinistre->hospital_status] ?? $sinistre->hospital_status,
                    'created_at'            => $sinistre->created_at?->toIso8601String(),
                    'updated_at'            => $sinistre->updated_at?->toIso8601String(),
                ],
                'caserne' => $sinistre->nearestHospital ? [
                    'id'        => $sinistre->nearestHospital->id,
                    'name'      => $sinistre->nearestHospital->name,
                    'contact'   => $sinistre->nearestHospital->contact ?? null,
                    'latitude'  => $sinistre->nearestHospital->latitude ?? null,
                    'longitude' => $sinistre->nearestHospital->longitude ?? null,
                ] : null,
                'groupe' => $sinistre->assignedGroupe ? [
                    'id'      => $sinistre->assignedGroupe->id,
                    'name'    => $sinistre->assignedGroupe->name,
                    'contact' => $sinistre->assignedGroupe->contact ?? null,
                ] : null,
            ]
        ], 200);
    }

    /**
     * Calcule la distance haversine entre deux coordonnées GPS (en km).
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        if (!$lat2 || !$lng2) return 9999;
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
