@extends('admin.layouts.template')

@section('title', 'Ajouter un service de constats')

@section('content')
    <div class="mx-auto space-y-6" style="width:70%">

        {{-- En-tête --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.services.index') }}"
                class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 hover:text-slate-800 hover:shadow-sm transition-all border border-slate-200">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Ajouter un service</h1>
                <p class="text-slate-500 text-sm mt-1">Créez un accès pour un nouveau commissariat ou brigade.</p>
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
            <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Type de service --}}
                    <div>
                        <label for="type_service" class="block text-sm font-semibold text-slate-700 mb-2">Type d'entité
                            <span class="text-red-500">*</span></label>
                        <select name="type_service" id="type_service"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all"
                            required>
                            <option value="">Sélectionner...</option>
                            <option value="police" {{ old('type_service') == 'police' ? 'selected' : '' }}>Police Nationale
                                (Commissariat)</option>
                            <option value="gendarmerie" {{ old('type_service') == 'gendarmerie' ? 'selected' : '' }}>
                                Gendarmerie Nationale (Brigade)</option>
                        </select>
                        <p class="text-xs text-slate-400 mt-1.5"><i class="fa-solid fa-circle-info mr-1"></i> Ce choix
                            définit les droits sur la plateforme.</p>
                    </div>

                    {{-- Nom du service --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nom du service <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            placeholder="Ex: Commissariat du 1er Arrondissement"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all"
                            required>
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
                            placeholder="email@exemple.com"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all"
                            required>
                        <p class="text-xs text-amber-600 mt-1.5 font-medium"><i
                                class="fa-solid fa-envelope-open-text mr-1"></i> L'identifiant et le mot de passe seront
                            envoyés à cette adresse.</p>
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="contact" class="block text-sm font-semibold text-slate-700 mb-2">Numéro de téléphone
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                            placeholder="Ex: 0102030405"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all"
                            required>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-slate-400 text-base"></i> Localisation
                </h3>

                <div class="grid grid-cols-2 gap-8">
                    {{-- Commune --}}
                    <div>
                        <label for="commune" class="block text-sm font-semibold text-slate-700 mb-2">Commune / Ville</label>
                        <input type="text" name="commune" id="commune" value="{{ old('commune') }}" placeholder="Ex: Cocody"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all">
                    </div>

                    {{-- Recherche d'adresse et Carte --}}
                    <div>
                        <label for="adresse" class="block text-sm font-semibold text-slate-700 mb-2">Adresse géographique
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}"
                            placeholder="Rechercher une adresse sur la carte..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all mb-4"
                            required>

                        {{-- Affichage de la carte --}}
                        <div id="map"
                            class="w-full h-[230px] rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>

                        {{-- Champs cachés pour envoyer les coordonnées --}}
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                        <p class="text-xs text-slate-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i> Vous pouvez
                            déplacer le repère rouge sur la carte pour ajuster la position.</p>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.services.index') }}"
                        class="px-6 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-50 transition-colors border border-slate-200">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold flex items-center shadow-lg shadow-primary-600/20 transition-all">
                        <i class="fa-solid fa-check mr-2"></i> Créer le service
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script Google Maps API --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"
        async defer></script>
    <script>
        function initMap() {
            // Coordonnées par défaut (ex: Abidjan)
            const defaultLocation = { lat: 5.345317, lng: -4.024429 };
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
            });

            // Mettre à jour les champs cachés au déplacement du marqueur
            google.maps.event.addListener(marker, 'dragend', function (event) {
                document.getElementById('latitude').value = event.latLng.lat();
                document.getElementById('longitude').value = event.latLng.lng();
            });

            // Lier l'input d'adresse à l'autocomplétion Google Places
            const input = document.getElementById("adresse");
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            // Gestionnaire de changement d'adresse via l'autocomplétion
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    return; // Le lieu n'a pas été trouvé ou l'utilisateur a tapé sans sélectionner
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
        }
    </script>
@endpush