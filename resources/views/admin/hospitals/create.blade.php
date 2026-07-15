@extends('admin.layouts.template')

@section('title', 'Ajouter une caserne de sapeurs-pompiers')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Intégration de Select2 avec le design Tailwind CSS de l'application */
        .select2-container--default .select2-selection--single {
            height: 3.125rem; /* ~50px */
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0; /* border-slate-200 */
            padding-left: 0.75rem;
            padding-top: 0.45rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            background-color: #f8fafc; /* bg-slate-50 */
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 3.125rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b; /* text-slate-800 */
            font-size: 0.875rem; /* text-sm */
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #243a8f !important;
            box-shadow: 0 0 0 2px rgba(36, 58, 143, 0.2);
        }
        .select2-dropdown {
            border-radius: 0.75rem;
            border-color: #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .select2-search__field {
            outline: none !important;
            border-radius: 0.375rem !important;
            border-color: #cbd5e1 !important;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto space-y-6" style="width:70%">

        {{-- En-tête --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.hospitals.index') }}"
                class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 hover:text-slate-800 hover:shadow-sm transition-all border border-slate-200">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Ajouter une caserne de sapeurs-pompiers</h1>
                <p class="text-slate-500 text-sm mt-1">Créez un accès pour une nouvelle caserne de sapeurs-pompiers.</p>
            </div>
        </div>

        {{-- Actions Retour / alertes --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erreur lors de la création</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <form action="{{ route('admin.hospitals.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Type de service --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Type d'entité <span class="text-red-500">*</span></label>
                            <input type="text" value="Sapeurs-pompiers" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl font-semibold text-slate-700 outline-none cursor-not-allowed" readonly>
                        </div>
                    </div>

                    {{-- Nom de la caserne --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nom de la Caserne <span
                                class="text-red-500">*</span></label>
                        <select name="name" id="name" style="width: 100%" required>
                            @if(old('name'))
                                <option value="{{ old('name') }}" selected>{{ old('name') }}</option>
                            @else
                                <option value="" selected disabled>Rechercher une caserne...</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-slate-400 text-base"></i> Coordonnées de contact
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Adresse Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            placeholder="caserne@onpc.ci"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 transition-all"
                            required>
                        <p class="text-xs text-amber-600 mt-1.5 font-medium"><i
                                class="fa-solid fa-envelope-open-text mr-1"></i> L'identifiant et le mot de passe de connexion seront envoyés à cette adresse.</p>
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="contact" class="block text-sm font-semibold text-slate-700 mb-2">Numéro de téléphone
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                            placeholder="Ex: 0102030405"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 transition-all"
                            required>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-slate-400 text-base"></i> Localisation
                </h3>

                <div class="grid grid-cols-2 gap-8">
                    {{-- Commune --}}
                    <div class="space-y-6">
                        <div>
                            <label for="commune" class="block text-sm font-semibold text-slate-700 mb-2">Commune / Ville</label>
                            <input type="text" name="commune" id="commune" value="{{ old('commune') }}" placeholder="Ex: Cocody"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 transition-all">
                        </div>
                        
                        <div>
                            <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/10">
                                <input type="checkbox" name="has_ambulance" value="1" {{ old('has_ambulance', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-rose-600 focus:ring-rose-500 border-slate-300 rounded">
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-800">Dispose d'une ambulance / VPSP</span>
                                    <span class="block text-[10px] text-slate-400">Cette caserne dispose de véhicules de secours et d'assistance aux victimes (Ambulance).</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Recherche d'adresse et Carte --}}
                    <div>
                        <label for="adresse" class="block text-sm font-semibold text-slate-700 mb-2">Adresse géographique
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}"
                            placeholder="Rechercher uniquement les casernes de sapeurs-pompiers..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 transition-all mb-4"
                            required>

                        {{-- Affichage de la carte --}}
                        <div id="map"
                            class="w-full h-[230px] rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>

                        {{-- Champs cachés pour envoyer les coordonnées --}}
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '5.30966') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '-4.01266') }}">

                        <p class="text-xs text-slate-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i> Vous pouvez
                            déplacer le repère rouge sur la carte pour ajuster la position.</p>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.hospitals.index') }}"
                        class="px-6 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-50 transition-colors border border-slate-200">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold flex items-center shadow-lg shadow-rose-600/20 transition-all">
                        <i class="fa-solid fa-check mr-2"></i> Enregistrer la caserne
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery & Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Script Google Maps API --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"
        async defer></script>
    <script>
        function initMap() {
            // Coordonnées par défaut (Abidjan)
            const defaultLocation = { lat: 5.30966, lng: -4.01266 };
            let currentLat = document.getElementById('latitude').value;
            let currentLng = document.getElementById('longitude').value;

            const startLocation = (currentLat && currentLng)
                ? { lat: parseFloat(currentLat), lng: parseFloat(currentLng) }
                : defaultLocation;

            // Initialiser la carte
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: startLocation,
                mapTypeControl: false,
                streetViewControl: false,
            });

            // Initialiser le marqueur (draggable)
            const marker = new google.maps.Marker({
                map: map,
                position: startLocation,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: "Position de la caserne"
            });

            // Mettre à jour les champs cachés au déplacement du marqueur
            const geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(marker, 'dragend', function (event) {
                document.getElementById('latitude').value = event.latLng.lat();
                document.getElementById('longitude').value = event.latLng.lng();

                geocoder.geocode({ location: event.latLng }, function(results, status) {
                    if (status === "OK" && results[0]) {
                        document.getElementById('adresse').value = results[0].formatted_address;
                    }
                });
            });

            // Lier l'input d'adresse à l'autocomplétion Google Places (recherche manuelle)
            const adresseInput = document.getElementById("adresse");
            const autocomplete = new google.maps.places.Autocomplete(adresseInput, {
                componentRestrictions: { country: 'ci' }
            });
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    return;
                }

                // Centrer la carte et déplacer le marqueur
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setPosition(place.geometry.location);

                // Mettre à jour les inputs
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();

                // Tenter d'extraire la ville/commune
                let communeStr = '';
                for (const component of place.address_components) {
                    const componentType = component.types[0];
                    if (componentType === 'locality' || componentType === 'administrative_area_level_2') {
                        communeStr = component.long_name;
                        break;
                    }
                }
                if (communeStr) {
                    document.getElementById('commune').value = communeStr;
                }
            });

            // Initialize Services pour le Select2
            const placesService = new google.maps.places.PlacesService(map);

            $(document).ready(function() {
                $('#name').select2({
                    placeholder: "Rechercher une caserne...",
                    allowClear: true,
                    tags: true, // Permet de saisir une caserne manuellement
                    ajax: {
                        delay: 500,
                        transport: function (params, success, failure) {
                            var query = params.data.q || "";
                            
                            // Liste complète et corrigée des casernes du GSPM et CSU en Côte d'Ivoire
                            var gspm = [
                                "1ère Compagnie d'Incendie et de Secours (Indénié - Adjamé)",
                                "2ème Compagnie d'Incendie et de Secours (Zone 4 - Marcory)",
                                "3ème Compagnie d'Incendie et de Secours (Bouaké)",
                                "4ème Compagnie d'Incendie et de Secours (Yopougon)",
                                "5ème Compagnie d'Incendie et de Secours (Yamoussoukro)",
                                "6ème Compagnie d'Incendie et de Secours (Korhogo)",
                                "7ème Compagnie d'Incendie et de Secours (N'Zianouan)",
                                "8ème Compagnie d'Incendie et de Secours (Bingerville)",
                                "Caserne d'Incendie et de Secours d'Abobo (GSPM)",
                                "Caserne d'Incendie et de Secours (Port-Bouët / Vridi)",
                                "Caserne d'Incendie et de Secours (San-Pédro)",
                                "Poste de Secours de la Zone Industrielle de Yopougon (GSPM)",
                                "Poste de Secours Routier de Singrobo (GSPM)",
                                "Poste de Secours Routier de Gesco (Yopougon)",
                                "Poste de Secours Routier d'Elibou (GSPM)"
                            ];

                            var formattedResults = [];
                            var queryLower = query.toLowerCase().trim();

                            // Filtrage local de la liste des sapeurs-pompiers
                            gspm.forEach(function(nom) {
                                if (!query || nom.toLowerCase().indexOf(queryLower) !== -1) {
                                    formattedResults.push({ id: nom, text: nom });
                                }
                            });

                            // Amélioration du terme de recherche pour cibler les sapeurs-pompiers et CSU via Google Maps
                            var request = {
                                query: query ? query + " Sapeurs-Pompiers Côte d'Ivoire" : "Sapeurs-Pompiers Côte d'Ivoire",
                            };
                            
                            placesService.textSearch(request, function (results, status) {
                                // Ajout des résultats trouvés par Google Maps en évitant les doublons
                                if (status === google.maps.places.PlacesServiceStatus.OK && results) {
                                    var googleResults = results.map(function (p) {
                                        return {
                                            id: p.name,
                                            text: p.name + (p.formatted_address ? ' (' + p.formatted_address + ')' : ''),
                                            place_id: p.place_id
                                        };
                                    });
                                    
                                    googleResults.forEach(function(gRes) {
                                        var exists = formattedResults.some(function(item) {
                                            return item.id.toLowerCase() === gRes.id.toLowerCase();
                                        });
                                        if (!exists) {
                                            formattedResults.push(gRes);
                                        }
                                    });
                                }
                                
                                success({ results: formattedResults });
                            });
                        }
                    }
                });

                // Lors de la sélection d'une caserne dans Select2
                $('#name').on('select2:select', function (e) {
                    var place_id = e.params.data.place_id;
                    var text = e.params.data.text;
                    
                    if (place_id) {
                        // Récupération des détails depuis Google Places
                        placesService.getDetails({ placeId: place_id }, function (place, status) {
                            if (status === google.maps.places.PlacesServiceStatus.OK && place.geometry && place.geometry.location) {
                                if (place.geometry.viewport) {
                                    map.fitBounds(place.geometry.viewport);
                                } else {
                                    map.setCenter(place.geometry.location);
                                    map.setZoom(16);
                                }
                                marker.setPosition(place.geometry.location);
                                marker.setAnimation(google.maps.Animation.DROP);

                                document.getElementById('latitude').value = place.geometry.location.lat();
                                document.getElementById('longitude').value = place.geometry.location.lng();
                                
                                const adresseInput = document.getElementById('adresse');
                                adresseInput.value = place.formatted_address || place.name;
                                adresseInput.readOnly = true;
                                adresseInput.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-80');

                                // Tenter d'extraire la commune
                                let communeStr = '';
                                if (place.address_components) {
                                    for (const component of place.address_components) {
                                        const componentType = component.types[0];
                                        if (componentType === 'locality' || componentType === 'administrative_area_level_2') {
                                            communeStr = component.long_name;
                                            break;
                                        }
                                    }
                                }
                                if (communeStr) {
                                    document.getElementById('commune').value = communeStr;
                                }
                            }
                        });
                    } else if (text) {
                        // Géocodage de secours pour les entrées manuelles
                        geocoder.geocode({ address: text + ", Côte d'Ivoire" }, function(results, status) {
                            if (status === "OK" && results[0]) {
                                var loc = results[0].geometry.location;
                                map.setCenter(loc);
                                map.setZoom(15);
                                marker.setPosition(loc);
                                marker.setAnimation(google.maps.Animation.DROP);

                                document.getElementById('latitude').value = loc.lat();
                                document.getElementById('longitude').value = loc.lng();
                                
                                const adresseInput = document.getElementById('adresse');
                                adresseInput.value = results[0].formatted_address || text;
                                adresseInput.readOnly = true;
                                adresseInput.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-80');

                                // Extraire la commune
                                let communeStr = '';
                                for (const component of results[0].address_components) {
                                    const componentType = component.types[0];
                                    if (componentType === 'locality' || componentType === 'administrative_area_level_2') {
                                        communeStr = component.long_name;
                                        break;
                                    }
                                }
                                if (communeStr) {
                                    document.getElementById('commune').value = communeStr;
                                }
                            } else {
                                const adresseInput = document.getElementById('adresse');
                                adresseInput.value = text;
                                adresseInput.readOnly = true;
                                adresseInput.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-80');
                            }
                        });
                    }
                });

                // Lors du nettoyage du Select2
                $('#name').on('select2:clear', function (e) {
                    const adresseInput = document.getElementById('adresse');
                    adresseInput.value = '';
                    adresseInput.readOnly = false;
                    adresseInput.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-80');
                    document.getElementById('latitude').value = '5.30966';
                    document.getElementById('longitude').value = '-4.01266';
                    document.getElementById('commune').value = '';
                    map.setCenter({ lat: 5.30966, lng: -4.01266 });
                    map.setZoom(13);
                    marker.setPosition({ lat: 5.30966, lng: -4.01266 });
                });
            });
        }
    </script>
@endpush
