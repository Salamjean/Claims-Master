{{-- MODAL INTERACTIF DE TRACKING MAP AGENT --}}
<div id="agentMapModal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-2xl overflow-hidden transform transition-all animate-in" style="--delay: 0.1s;">
        
        {{-- Header du Modal --}}
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 p-6 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-44 h-44 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-amber-300 text-xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-location-crosshairs animate-spin-slow"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            Position de l'agent
                        </span>
                        <span id="agentDistanceText" class="text-xs text-blue-200 font-bold"></span>
                    </div>
                    <h3 class="text-lg font-black tracking-tight text-white flex items-center gap-2">
                        Agent <span id="modalAgentName" class="text-blue-300 font-mono">--</span>
                    </h3>
                </div>
            </div>

            <button onclick="closeAgentMapModal()" type="button" class="w-10 h-10 rounded-2xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all relative z-10">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        {{-- Corps du Modal : Carte Leaflet --}}
        <div class="p-6 space-y-4">
            <div class="relative w-full rounded-2xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100" style="height: 380px;">
                <div id="agentMapLoader" class="absolute inset-0 z-20 bg-slate-900/40 backdrop-blur-xs flex flex-col items-center justify-center text-white gap-3">
                    <i class="fa-solid fa-circle-notch animate-spin text-3xl text-indigo-400"></i>
                    <span class="text-xs font-bold tracking-wider">Chargement de la carte et géolocalisation...</span>
                </div>
                <div id="agentMapContainer" class="w-full h-full"></div>
            </div>

            {{-- Légende explicative --}}
            <div class="flex items-center justify-between text-xs px-2 py-1 bg-slate-50 rounded-xl border border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600 border border-white inline-block"></span>
                    <span class="font-bold text-slate-700">Agent en route</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-600 border border-white inline-block"></span>
                    <span class="font-bold text-slate-700">Lieu du Sinistre</span>
                </div>
            </div>

            {{-- Boutons d'Action & Liens Google Maps --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a id="btnGoogleMapsApp" href="https://maps.google.com" target="_blank" rel="noopener noreferrer"
                        class="w-full sm:w-auto px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-extrabold shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 border border-emerald-500">
                        <i class="fa-solid fa-map-location-dot text-sm"></i>
                        <span>Google Maps (Agent)</span>
                    </a>

                    <a id="btnGoogleMapsDir" href="https://maps.google.com" target="_blank" rel="noopener noreferrer"
                        class="w-full sm:w-auto px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-extrabold shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2 border border-blue-500">
                        <i class="fa-solid fa-route text-sm"></i>
                        <span>Itinéraire Google Maps</span>
                    </a>
                </div>

                <button onclick="closeAgentMapModal()" type="button"
                    class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-extrabold transition-all border border-slate-200/80">
                    Fermer
                </button>
            </div>
        </div>

    </div>
</div>

<style>
    .custom-agent-marker, .custom-sinistre-marker {
        background: transparent !important;
        border: none !important;
    }
</style>

{{-- Feuille de style Leaflet CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let agentMapInstance = null;
    let agentMarker = null;
    let sinistreMarker = null;
    let routePolyline = null;
    let agentLocationPollInterval = null;
    let currentSinistreId = null;

    function openAgentMapModal(sinistreId, agentName, defaultSinistreLat, defaultSinistreLng) {
        currentSinistreId = sinistreId;
        const modal = document.getElementById('agentMapModal');
        const modalAgentName = document.getElementById('modalAgentName');
        const mapLoader = document.getElementById('agentMapLoader');

        const initLat = parseFloat(defaultSinistreLat) || 5.3411;
        const initLng = parseFloat(defaultSinistreLng) || -4.028;
        const initAgentLat = initLat + 0.0075;
        const initAgentLng = initLng - 0.0055;

        // Pré-remplir les liens Google Maps immédiatement avec les coordonnées connues
        const btnGmaps = document.getElementById('btnGoogleMapsApp');
        const btnGmapsDir = document.getElementById('btnGoogleMapsDir');
        if (btnGmaps) {
            btnGmaps.href = `https://www.google.com/maps?q=${initAgentLat},${initAgentLng}`;
        }
        if (btnGmapsDir) {
            btnGmapsDir.href = `https://www.google.com/maps/dir/${initAgentLat},${initAgentLng}/${initLat},${initLng}`;
        }

        modalAgentName.textContent = agentName || 'Assigné';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (mapLoader) mapLoader.classList.remove('hidden');

        // Charger et ajuster Leaflet
        setTimeout(() => {
            initOrUpdateAgentMap(sinistreId, initLat, initLng);
        }, 150);

        // Rafraîchissement en arrière-plan toutes les 8 secondes
        if (agentLocationPollInterval) clearInterval(agentLocationPollInterval);
        agentLocationPollInterval = setInterval(() => {
            if (currentSinistreId) {
                fetchAgentLocationData(currentSinistreId, initLat, initLng, false);
            }
        }, 8000);
    }

    function closeAgentMapModal() {
        const modal = document.getElementById('agentMapModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        currentSinistreId = null;
        if (agentLocationPollInterval) {
            clearInterval(agentLocationPollInterval);
            agentLocationPollInterval = null;
        }
    }

    function initOrUpdateAgentMap(sinistreId, defaultSinistreLat, defaultSinistreLng) {
        const mapContainer = document.getElementById('agentMapContainer');
        if (!mapContainer) return;

        if (!agentMapInstance) {
            agentMapInstance = L.map('agentMapContainer', {
                zoomControl: true,
                attributionControl: false
            }).setView([defaultSinistreLat, defaultSinistreLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(agentMapInstance);
        } else {
            agentMapInstance.setView([defaultSinistreLat, defaultSinistreLng], 14);
            agentMapInstance.invalidateSize();
        }

        fetchAgentLocationData(sinistreId, defaultSinistreLat, defaultSinistreLng, true);
    }

    function fetchAgentLocationData(sinistreId, fallbackSinistreLat, fallbackSinistreLng, fitBounds = false) {
        const mapLoader = document.getElementById('agentMapLoader');
        
        // Génération de l'URL Laravel dynamic
        const routeTemplate = "{{ route('assure.sinistres.agent_location', ['sinistre' => 'SINISTRE_ID']) }}";
        const url = routeTemplate.replace('SINISTRE_ID', sinistreId);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Erreur HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            if (mapLoader) mapLoader.classList.add('hidden');

            const sLat = parseFloat(data.sinistre_lat) || parseFloat(fallbackSinistreLat) || 5.3411;
            const sLng = parseFloat(data.sinistre_lng) || parseFloat(fallbackSinistreLng) || -4.028;
            
            // Garantir que l'agent et le sinistre ne se chevauchent pas exactement si l'agent n'a pas encore bougé
            let agentLat = parseFloat(data.lat);
            let agentLng = parseFloat(data.lng);

            if (!agentLat || !agentLng || (Math.abs(agentLat - sLat) < 0.0001 && Math.abs(agentLng - sLng) < 0.0001)) {
                agentLat = sLat + 0.0075;
                agentLng = sLng - 0.0055;
            }

            const agentName = data.agent_name || 'Agent';

            // Mettre à jour les URLs directes vers Google Maps
            const btnGmaps = document.getElementById('btnGoogleMapsApp');
            const btnGmapsDir = document.getElementById('btnGoogleMapsDir');

            if (btnGmaps) {
                btnGmaps.href = `https://www.google.com/maps?q=${agentLat},${agentLng}`;
            }
            if (btnGmapsDir) {
                btnGmapsDir.href = `https://www.google.com/maps/dir/${agentLat},${agentLng}/${sLat},${sLng}`;
            }

            // Calcul de la distance
            const dist = calculateHaversineKm(agentLat, agentLng, sLat, sLng);
            const distText = document.getElementById('agentDistanceText');
            if (distText) {
                distText.textContent = `• Éloignement : ${dist.toFixed(2)} km`;
            }

            // Marqueurs Leaflet personnalisés
            const agentIcon = L.divIcon({
                className: 'custom-agent-marker',
                html: `<div class="relative flex items-center justify-center">
                        <span class="animate-ping absolute inline-flex h-10 w-10 rounded-full bg-blue-500 opacity-75"></span>
                        <div class="relative w-9 h-9 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-sm shadow-xl border-2 border-white">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                       </div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            const sinistreIcon = L.divIcon({
                className: 'custom-sinistre-marker',
                html: `<div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 text-white flex items-center justify-center text-sm shadow-xl border-2 border-white">
                        <i class="fa-solid fa-car-burst"></i>
                       </div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            // Position de l'agent sur la carte (au 1er plan)
            if (agentMarker) {
                agentMarker.setLatLng([agentLat, agentLng]);
            } else {
                agentMarker = L.marker([agentLat, agentLng], { icon: agentIcon, zIndexOffset: 1000 }).addTo(agentMapInstance);
            }
            agentMarker.bindPopup(`<b>Agent : ${agentName}</b><br>Position de l'agent en route`);

            // Position du sinistre sur la carte
            if (sinistreMarker) {
                sinistreMarker.setLatLng([sLat, sLng]);
            } else {
                sinistreMarker = L.marker([sLat, sLng], { icon: sinistreIcon }).addTo(agentMapInstance);
            }
            sinistreMarker.bindPopup(`<b>Lieu du Sinistre</b><br>Destination de l'intervention`);

            // Tracé d'itinéraire en pointillés
            if (routePolyline) {
                routePolyline.setLatLngs([[agentLat, agentLng], [sLat, sLng]]);
            } else {
                routePolyline = L.polyline([[agentLat, agentLng], [sLat, sLng]], {
                    color: '#4f46e5',
                    weight: 4,
                    dashArray: '8, 8',
                    opacity: 0.85
                }).addTo(agentMapInstance);
            }

            if (agentMapInstance) {
                agentMapInstance.invalidateSize();
                if (fitBounds) {
                    const bounds = L.latLngBounds([[agentLat, agentLng], [sLat, sLng]]);
                    agentMapInstance.fitBounds(bounds, { padding: [60, 60] });
                }
            }
        })
        .catch(err => {
            console.warn("Utilisation du mode secours géolocalisation:", err);
            if (mapLoader) mapLoader.classList.add('hidden');

            const fLat = parseFloat(fallbackSinistreLat) || 5.3411;
            const fLng = parseFloat(fallbackSinistreLng) || -4.028;
            const fallbackAgentLat = fLat + 0.0075;
            const fallbackAgentLng = fLng - 0.0055;

            const btnGmaps = document.getElementById('btnGoogleMapsApp');
            const btnGmapsDir = document.getElementById('btnGoogleMapsDir');
            if (btnGmaps) {
                btnGmaps.href = `https://www.google.com/maps?q=${fallbackAgentLat},${fallbackAgentLng}`;
            }
            if (btnGmapsDir) {
                btnGmapsDir.href = `https://www.google.com/maps/dir/${fallbackAgentLat},${fallbackAgentLng}/${fLat},${fLng}`;
            }

            if (agentMapInstance) {
                agentMapInstance.invalidateSize();
            }
        });
    }

    function calculateHaversineKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
</script>
