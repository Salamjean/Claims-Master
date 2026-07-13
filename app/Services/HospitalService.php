<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HospitalService
{
    /**
     * Liste statique de repli des hôpitaux/SAMU en Côte d'Ivoire (Abidjan)
     */
    protected static $staticHospitals = [
        [
            'name' => "CHU d'Angré (SAMU)",
            'adresse' => "Abidjan, Angré",
            'contact' => "22471111",
            'latitude' => 5.4050,
            'longitude' => -3.9850,
            'has_ambulance' => true,
        ],
        [
            'name' => "CHU de Cocody",
            'adresse' => "Abidjan, Cocody",
            'contact' => "2722481000",
            'latitude' => 5.3411,
            'longitude' => -3.9812,
            'has_ambulance' => true,
        ],
        [
            'name' => "CHU de Treichville",
            'adresse' => "Abidjan, Treichville",
            'contact' => "2721249122",
            'latitude' => 5.3014,
            'longitude' => -4.0092,
            'has_ambulance' => true,
        ],
        [
            'name' => "CHU de Yopougon",
            'adresse' => "Abidjan, Yopougon",
            'contact' => "2723537550",
            'latitude' => 5.3622,
            'longitude' => -4.0934,
            'has_ambulance' => true,
        ],
        [
            'name' => "Hôpital Général de Marcory",
            'adresse' => "Abidjan, Marcory",
            'contact' => "2721268840",
            'latitude' => 5.3120,
            'longitude' => -3.9922,
            'has_ambulance' => false,
        ],
        [
            'name' => "PISAM (Polyclinique Sainte Anne-Marie)",
            'adresse' => "Abidjan, Cocody PISAM",
            'contact' => "2722483131",
            'latitude' => 5.3325,
            'longitude' => -4.0080,
            'has_ambulance' => true,
        ],
        [
            'name' => "Clinique Farah",
            'adresse' => "Abidjan, Marcory Zone 4",
            'contact' => "2722510000",
            'latitude' => 5.3050,
            'longitude' => -3.9990,
            'has_ambulance' => false,
        ]
    ];

    /**
     * Recherche les hôpitaux les plus proches à partir de coordonnées GPS.
     * Tente d'abord d'appeler l'API Google Places, sinon bascule sur la liste statique.
     */
    public function getNearbyHospitals($lat, $lng, $limit = 5): array
    {
        $lat = (float) $lat;
        $lng = (float) $lng;
        
        $key = config('services.google_maps_key');
        
        if ($key) {
            try {
                // Appel API Google Places Nearby Search
                $response = Http::get("https://maps.googleapis.com/maps/api/place/nearbysearch/json", [
                    'location' => "$lat,$lng",
                    'radius' => 15000, // 15km
                    'type' => 'hospital',
                    'key' => $key,
                    'language' => 'fr'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $results = $data['results'] ?? [];
                    
                    if (!empty($results)) {
                        $hospitals = [];
                        foreach (array_slice($results, 0, $limit) as $place) {
                            $hLat = (float) $place['geometry']['location']['lat'];
                            $hLng = (float) $place['geometry']['location']['lng'];
                            $dist = $this->haversine($lat, $lng, $hLat, $hLng);
                            
                            // On récupère le contact via place details si possible
                            $contact = 'N/A';
                            if (isset($place['place_id'])) {
                                $details = Http::get("https://maps.googleapis.com/maps/api/place/details/json", [
                                    'place_id' => $place['place_id'],
                                    'fields' => 'formatted_phone_number,international_phone_number',
                                    'key' => $key
                                ]);
                                if ($details->successful()) {
                                    $detailsData = $details->json();
                                    $contact = $detailsData['result']['formatted_phone_number'] 
                                        ?? $detailsData['result']['international_phone_number'] 
                                        ?? 'N/A';
                                }
                            }

                            $hospitals[] = [
                                'name' => $place['name'],
                                'adresse' => $place['vicinity'] ?? ($place['formatted_address'] ?? 'Adresse non spécifiée'),
                                'contact' => $contact,
                                'distance' => round($dist, 2),
                                'latitude' => $hLat,
                                'longitude' => $hLng,
                                'has_ambulance' => true // Par défaut pour l'affichage dynamique
                            ];
                        }
                        return $hospitals;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Erreur Google Places dans HospitalService : " . $e->getMessage());
            }
        }

        // Repli (Fallback) : calcul sur la liste statique locale
        $hospitals = [];
        foreach (self::$staticHospitals as $item) {
            $dist = $this->haversine($lat, $lng, $item['latitude'], $item['longitude']);
            $hospitals[] = array_merge($item, [
                'distance' => round($dist, 2)
            ]);
        }

        // Tri par distance
        usort($hospitals, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return array_slice($hospitals, 0, $limit);
    }

    /**
     * Formule de Haversine pour calculer la distance
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }
}
