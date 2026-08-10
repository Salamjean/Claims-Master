<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Services\YellikaSmsService;
use App\Services\HospitalService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AlertePubliqueController extends Controller
{
    protected YellikaSmsService $sms;
    protected HospitalService $hospitalService;

    public function __construct(YellikaSmsService $sms, HospitalService $hospitalService)
    {
        $this->sms = $sms;
        $this->hospitalService = $hospitalService;
    }

    /**
     * Affiche le formulaire public de signalement d'urgence.
     */
    public function showForm()
    {
        return view('public.signaler_urgence');
    }

    /**
     * Traite et enregistre la déclaration d'urgence anonyme.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_sinistre'    => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:5000'],
            'lieu'             => ['required', 'string', 'max:500'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'declarant_nom'    => ['nullable', 'string', 'max:255'],
            'declarant_contact' => ['nullable', 'string', 'max:50'],
            'photos'           => ['required', 'array', 'min:1', 'max:3'],
            'photos.*'         => ['required', 'image', 'max:5120'], // 5MB par photo
        ]);

        // Upload des photos si fournies
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

            // Chercher le SP le plus proche dans la base de données
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

        // Création du sinistre
        $sinistre = Sinistre::create([
            'user_id'            => null, // Déclaration sans compte
            'type_sinistre'      => $validated['type_sinistre'],
            'description'        => $validated['description'] ?? null,
            'lieu'               => $validated['lieu'],
            'latitude'           => $validated['latitude'] ?? null,
            'longitude'          => $validated['longitude'] ?? null,
            'photos'             => $photosPaths ?: null,
            'declarant_nom'      => $validated['declarant_nom'] ?? null,
            'declarant_contact'  => $validated['declarant_contact'] ?? null,
            'token_suivi'        => $token,
            'status'             => 'en_attente',
            'moyen_declaration'  => 'temoin_public',
            'date_declaration'   => now(),
            'date_survenance'    => now(),
            'nearest_hospital_id' => $nearestHospitalId,
            'hospital_status'    => 'en_attente',
        ]);

        // Envoi SMS de confirmation avec lien de suivi (si contact fourni)
        if (!empty($validated['declarant_contact'])) {
            $lienSuivi = route('alerte.suivi', $token);
            $nomDeclarant = $validated['declarant_nom'] ? ', ' . $validated['declarant_nom'] : '';
            $message = "Claims Master{$nomDeclarant}: Votre signalement d'urgence a ete enregistre. Ref: {$sinistre->numero_sinistre}. Suivez l'intervention ici: {$lienSuivi}";

            try {
                $this->sms->sendSms($validated['declarant_contact'], $message);
            } catch (\Exception $e) {
                Log::warning('SMS suivi alerte anonyme non envoye: ' . $e->getMessage());
            }
        }

        return redirect()->route('alerte.suivi', $token)
            ->with('success', 'Votre alerte a été transmise aux Sapeurs-Pompiers. Merci pour votre signalement !');
    }

    /**
     * Affiche la page de suivi d'une alerte par son token.
     */
    public function suiviAlerte(string $token)
    {
        $sinistre = Sinistre::where('token_suivi', $token)
            ->with(['nearestHospital', 'assignedGroupe'])
            ->firstOrFail();

        return view('public.suivi_alerte', compact('sinistre', 'token'));
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
