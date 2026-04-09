@extends('assure.layouts.template')

@section('title', 'Suivi de l\'intervention')

@push('styles')
<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 1.5rem;
        z-index: 1;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .tracking-card {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226,232,240,0.8);
        border-radius: 1.25rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    }
    .eta-pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }
</style>
@endpush

@section('content')
<div class="mx-auto px-4 py-8" style="width: 100%;">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-black uppercase tracking-widest border border-blue-100 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Suivi GPS en direct
            </div>
            <h1 class="text-2xl font-black text-slate-800">Votre agent est en route</h1>
            <p class="text-sm text-slate-500 mt-1">Dossier <span class="text-blue-600 font-bold">#{{ $sinistre->numero_sinistre }}</span></p>
        </div>
        <a href="{{ route('assure.dashboard') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-600 font-bold text-sm rounded-xl border border-slate-200 hover:bg-slate-50 transition-all">
            <i class="fa-solid fa-arrow-left text-xs"></i> Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Carte GPS --}}
        <div class="lg:col-span-2">
            <div class="tracking-card overflow-hidden">
                <div id="map"></div>

                {{-- Overlay info ETA --}}
                <div class="p-5 border-t border-slate-100">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white text-xl shadow">
                                <i class="fa-solid fa-route"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-black uppercase tracking-widest">Temps d'arrivée estimé</p>
                                <p class="text-2xl font-black text-slate-800 eta-pulse" id="eta-text">Calcul en cours...</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 font-bold" id="last-update-wrap">
                            <i class="fa-solid fa-satellite-dish text-blue-400"></i>
                            <span id="last-update">Connexion GPS...</span>
                        </div>
                    </div>

                    {{-- Barre de progression --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-xs font-bold text-slate-400 mb-1">
                            <span>Départ agent</span>
                            <span>Lieu du sinistre</span>
                        </div>
                        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                            <div id="progress-bar" class="h-full bg-gradient-to-r from-blue-500 to-blue-700 rounded-full transition-all duration-700" style="width:0%"></div>
                        </div>
                        <p class="text-xs text-slate-400 font-bold mt-1 text-right"><span id="progress-pct">0</span>% parcouru</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carte Agent --}}
        <div class="space-y-5">
            <div class="tracking-card p-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-5">Votre intervenant</h3>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-2xl bg-blue-50 border-4 border-blue-100 flex items-center justify-center mb-4 overflow-hidden shadow">
                        @if($sinistre->assignedAgent->profile_picture ?? false)
                            <img src="{{ asset('storage/'.$sinistre->assignedAgent->profile_picture) }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user-shield text-3xl text-blue-400"></i>
                        @endif
                    </div>
                    <p class="text-lg font-black text-slate-800">{{ $sinistre->assignedAgent->name ?? 'Agent' }}</p>
                    <p class="text-sm font-bold text-blue-600 mb-1">{{ $sinistre->service->name ?? 'Service' }}</p>
                    <p class="text-xs text-slate-400 mb-5">Agent de constat terrain</p>

                    @if($sinistre->assignedAgent->contact ?? false)
                    <a href="tel:{{ $sinistre->assignedAgent->contact }}"
                        class="w-full py-3 bg-blue-600 text-white font-black text-sm rounded-xl flex items-center justify-center gap-2 hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-phone"></i> Appeler l'agent
                    </a>
                    @endif
                </div>
            </div>

            {{-- Infos sinistre --}}
            <div class="tracking-card p-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Incident déclaré</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-red-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Type</p>
                            <p class="text-sm font-black text-slate-700">{{ str_replace('_',' ', $sinistre->type_sinistre) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-location-dot text-amber-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Position GPS</p>
                            <p class="text-sm font-mono font-bold text-slate-700">{{ number_format($sinistre->latitude,4) }}, {{ number_format($sinistre->longitude,4) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Constantes PHP → JS ──────────────────────────────────────────────────
    const ACCIDENT_LAT = {{ (float)($sinistre->latitude  ?? 5.3484) }};
    const ACCIDENT_LNG = {{ (float)($sinistre->longitude ?? -4.0191) }};
    const START_LAT    = {{ (float)($sinistre->agent_start_lat ?? ($sinistre->latitude  + 0.015)) }};
    const START_LNG    = {{ (float)($sinistre->agent_start_lng ?? ($sinistre->longitude - 0.015)) }};
    const POLL_URL     = "{{ route('assure.sinistres.agent_location', $sinistre->id) }}";

    let map, agentMarker, accidentMarker, directionsService, directionsRenderer, fallbackPolyline;
    let lastLat = START_LAT, lastLng = START_LNG;

    // ── Initialisation Google Maps ───────────────────────────────────────────
    window.initMap = function () {
        const container = document.getElementById('map');
        if (!container) return;

        map = new google.maps.Map(container, {
            zoom: 14,
            center: { lat: START_LAT, lng: START_LNG },
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: [
                { featureType: 'poi', stylers: [{ visibility: 'off' }] },
                { featureType: 'transit', stylers: [{ visibility: 'off' }] }
            ]
        });

        directionsService  = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            polylineOptions: {
                strokeColor:   '#2563eb',
                strokeOpacity: 0.85,
                strokeWeight:  6
            }
        });
        directionsRenderer.setMap(map);

        // ── Marqueur AGENT (voiture bleue) ──
        agentMarker = new google.maps.Marker({
            position: { lat: START_LAT, lng: START_LNG },
            map: map,
            zIndex: 100,
            title: 'Position de l\'agent',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                scaledSize: new google.maps.Size(48, 48),
                anchor: new google.maps.Point(24, 24)
            }
        });

        // ── Marqueur SINISTRE (pin rouge) ──
        accidentMarker = new google.maps.Marker({
            position: { lat: ACCIDENT_LAT, lng: ACCIDENT_LNG },
            map: map,
            zIndex: 90,
            title: 'Lieu du sinistre',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(48, 48),
                anchor: new google.maps.Point(24, 48)
            }
        });

        // ── Calcul de l'itinéraire initial ──
        calculateRoute(START_LAT, START_LNG);

        // ── Polling toutes les 10 secondes ──
        setInterval(pollAgentPosition, 10000);
    };

    // ── Directions API ───────────────────────────────────────────────────────
    function calculateRoute(fromLat, fromLng) {
        directionsService.route({
            origin:      { lat: parseFloat(fromLat), lng: parseFloat(fromLng) },
            destination: { lat: ACCIDENT_LAT, lng: ACCIDENT_LNG },
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC
        }, function (result, status) {
            if (status === 'OK') {
                // Supprime le fallback polyline si présent
                if (fallbackPolyline) { fallbackPolyline.setMap(null); fallbackPolyline = null; }

                directionsRenderer.setDirections(result);

                const leg = result.routes[0].legs[0];
                document.getElementById('eta-text').innerText = leg.duration.text;
                document.getElementById('eta-text').classList.remove('eta-pulse');

                updateProgress(fromLat, fromLng);
            } else {
                console.warn('Directions API: ' + status + ' — Utilisation du fallback polyline.');
                showFallbackLine(fromLat, fromLng);
                document.getElementById('eta-text').innerText = 'Estimation GPS...';
            }
        });
    }

    // ── Fallback : ligne droite si Directions API non dispo ─────────────────
    function showFallbackLine(fromLat, fromLng) {
        if (fallbackPolyline) fallbackPolyline.setMap(null);
        fallbackPolyline = new google.maps.Polyline({
            path: [
                { lat: parseFloat(fromLat), lng: parseFloat(fromLng) },
                { lat: ACCIDENT_LAT, lng: ACCIDENT_LNG }
            ],
            map: map,
            strokeColor:   '#2563eb',
            strokeOpacity: 0.7,
            strokeWeight:  5,
            icons: [{
                icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 3 },
                repeat: '80px'
            }]
        });
        updateProgress(fromLat, fromLng);
    }

    // ── Polling position GPS de l'agent ─────────────────────────────────────
    function pollAgentPosition() {
        fetch(POLL_URL, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            const lat = parseFloat(data.lat);
            const lng = parseFloat(data.lng);
            if (!lat || !lng || (lat === 0 && lng === 0)) return;

            // Mise à jour du marqueur agent
            const pos = new google.maps.LatLng(lat, lng);
            agentMarker.setPosition(pos);

            // Recalcule le tracé uniquement si la position a changé
            if (Math.abs(lat - lastLat) > 0.0001 || Math.abs(lng - lastLng) > 0.0001) {
                lastLat = lat; lastLng = lng;
                calculateRoute(lat, lng);
                map.panTo(pos);
            }

            // Horodatage de la dernière mise à jour
            const now = new Date();
            const hms = now.getHours() + ':' +
                        String(now.getMinutes()).padStart(2,'0') + ':' +
                        String(now.getSeconds()).padStart(2,'0');
            document.getElementById('last-update').innerText = 'Mis à jour à ' + hms;
        })
        .catch(err => console.error('GPS polling error:', err));
    }

    // ── Barre de progression ─────────────────────────────────────────────────
    function updateProgress(agentLat, agentLng) {
        const total   = haversine(START_LAT, START_LNG, ACCIDENT_LAT, ACCIDENT_LNG);
        const left    = haversine(agentLat, agentLng, ACCIDENT_LAT, ACCIDENT_LNG);
        const pct     = Math.max(0, Math.min(100, Math.round(((total - left) / total) * 100)));

        document.getElementById('progress-bar').style.width = pct + '%';
        document.getElementById('progress-pct').innerText   = pct;

        if (pct >= 98) {
            document.getElementById('eta-text').innerText = '🎯 Arrivée imminente !';
        }
    }

    // ── Haversine distance (km) ──────────────────────────────────────────────
    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371, toRad = x => x * Math.PI / 180;
        const dLat = toRad(lat2 - lat1), dLng = toRad(lng2 - lng1);
        const a = Math.sin(dLat/2)**2 +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps_key') }}&libraries=geometry&callback=initMap"
    async defer>
</script>
@endpush
