@extends('assure.layouts.template')

@section('title', 'Déclarer un sinistre')
@section('page-title', 'Nouveau Sinistre')

@section('content')
    <div class="mx-auto pb-12" style="width: 95%;">

        {{-- En-tête de la page --}}
        <div class="mb-8 text-center sm:text-left animate-in" style="--delay: 0.1s">
            <h2
                class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center justify-center sm:justify-start gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 border border-red-200 flex items-center justify-center text-red-600 shadow-sm">
                    <i class="fa-solid fa-car-burst text-2xl"></i>
                </div>
                Déclaration de sinistre
            </h2>
            <p class="mt-3 text-slate-500 text-sm md:text-base max-w-2xl leading-relaxed">
                Signalez rapidement votre accident. Votre position est détectée automatiquement pour dépêcher les forces de
                l'ordre (Police/Gendarmerie) les plus proches de votre position exacte en temps réel.
            </p>
        </div>

        <form action="{{ route('assure.sinistres.store') }}" method="POST" enctype="multipart/form-data" id="sinistre-form"
            class="flex flex-col gap-6 lg:gap-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- 1. LOCALISATION AUTOMATIQUE --}}
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden relative animate-in flex flex-col"
                    style="--delay: 0.2s">
                    <div class="absolute top-0 left-0 w-full h-2 bg-blue-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                                1</div>
                            <h3 class="text-lg font-bold text-slate-800">Géolocalisation</h3>
                        </div>

                        <div
                            class="bg-slate-50 rounded-2xl p-5 border border-slate-200 flex flex-col items-center text-center gap-5 flex-1 justify-center">
                            {{-- Icône radar / map --}}
                            <div
                                class="relative w-20 h-20 rounded-full bg-white shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-satellite-dish text-3xl text-blue-500 animate-pulse"></i>
                                <div class="absolute inset-0 rounded-full border border-blue-400 animate-ping opacity-20">
                                </div>
                            </div>

                            <div class="w-full">
                                <h4 class="font-bold text-slate-800 mb-1 text-base">Recherche de précision</h4>
                                <p class="text-sm text-slate-500 mb-4 line-clamp-2">Calcul de vos coordonnées GPS pour
                                    trouver le poste le plus proche.</p>

                                {{-- Indicateur de statut --}}
                                <div id="btn-geoloc"
                                    class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-blue-50 text-blue-700 font-semibold rounded-xl text-sm border border-blue-100">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Recherche GPS...
                                </div>

                                <div id="geoloc-status" class="mt-3 text-xs font-semibold hidden"></div>

                                {{-- Liste des services à proximité --}}
                                <div id="nearest-services-list" class="mt-6 space-y-2 hidden text-left w-full">
                                    {{-- Injecté par JS --}}
                                </div>

                                {{-- Hidden inputs --}}
                                <input type="hidden" name="latitude" id="latitude" required>
                                <input type="hidden" name="longitude" id="longitude" required>
                                <input type="hidden" name="lieu" id="lieu_input">
                                <input type="hidden" name="contrat_id" id="contrat_id" required>
                                <input type="hidden" name="amiable_data" id="amiable_data">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. INFORMATIONS DU SINISTRE --}}
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden relative animate-in flex flex-col"
                    style="--delay: 0.3s">
                    <div class="absolute top-0 left-0 w-full h-2 bg-orange-400"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center font-bold text-sm shrink-0">
                                2</div>
                            <h3 class="text-lg font-bold text-slate-800">Nature de l'incident</h3>
                        </div>

                        <div class="flex flex-col gap-5 flex-1">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3">Types d'incidents <span
                                        class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 gap-2">
                                    @php
                                        $incidents = [
                                            'Accident_matériel' => ['label' => 'Accident matériel', 'icon' => 'fa-car-burst', 'color' => 'text-blue-500'],
                                            'Accident_corporel' => ['label' => 'Accident corporel', 'icon' => 'fa-user-injured', 'color' => 'text-red-500'],
                                            'Vol' => ['label' => 'Vol / Braquage', 'icon' => 'fa-mask', 'color' => 'text-slate-700'],
                                            'Incendie' => ['label' => 'Incendie / Feu', 'icon' => 'fa-fire', 'color' => 'text-orange-500'],
                                            'Bris_de_glace' => ['label' => 'Bris de glace', 'icon' => 'fa-hammer', 'color' => 'text-cyan-500'],
                                            'Autre' => ['label' => 'Autre...', 'icon' => 'fa-ellipsis', 'color' => 'text-slate-400'],
                                        ];
                                    @endphp
                                    @foreach($incidents as $value => $data)
                                        <label
                                            class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50/30">
                                            <input type="checkbox" name="type_sinistre[]" value="{{ $value }}"
                                                class="w-4 h-4 text-orange-500 focus:ring-orange-400 border-slate-300 rounded incident-checkbox">
                                            <div class="ml-3 flex items-center gap-2">
                                                <i
                                                    class="fa-solid {{ $data['icon'] }} {{ $data['color'] }} text-sm w-5 text-center"></i>
                                                <span class="text-sm font-bold text-slate-800">{{ $data['label'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('type_sinistre')
                                    <p class="text-xs text-red-500 mt-1 font-medium"><i
                                            class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                @enderror

                                {{-- Affichage du véhicule sélectionné --}}
                                <div id="selected-vehicle-display" class="mt-4 pt-4 border-t border-slate-100 hidden">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Véhicule
                                        sélectionné</p>
                                    <div
                                        class="flex items-center justify-between bg-slate-50/50 rounded-2xl p-3 border border-slate-100 group/veh">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-sm shadow-sm">
                                                <i class="fa-solid fa-car"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <h5 id="display-vehicle-name"
                                                    class="text-xs font-bold text-slate-800 leading-tight truncate"></h5>
                                                <p id="display-vehicle-plate"
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="showVehicleSelection()"
                                            class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-orange-500 hover:border-orange-200 hover:bg-orange-50 transition-all flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-sync-alt text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. CONSTATATION DE L'ACCIDENT --}}
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden relative animate-in flex flex-col"
                    style="--delay: 0.35s">
                    <div class="absolute top-0 left-0 w-full h-2 bg-purple-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm shrink-0">
                                2.5
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Constatation & Assurance</h3>
                        </div>

                        <div class="flex flex-col gap-5 flex-1">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3">Établissement du constat <span
                                        class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 gap-2">
                                    <label id="label-constat-amiable"
                                        class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50/30">
                                        <input type="radio" name="methode_constat" id="constat-amiable" value="Amiable"
                                            class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-slate-300" required>
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-slate-800">Constat amiable</span>
                                            <span class="block text-[10px] text-slate-500">Rempli avec l'autre partie
                                                impliquée</span>
                                        </div>
                                    </label>
                                    <label
                                        class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50/30">
                                        <input type="radio" name="methode_constat" id="constat-police"
                                            value="Police_Gendarmerie"
                                            class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-slate-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-slate-800">Police / Gendarmerie</span>
                                            <span class="block text-[10px] text-slate-500">Procès-verbal établi par les
                                                autorités</span>
                                        </div>
                                    </label>
                                </div>
                                <p id="corporel-warning" class="text-[10px] text-red-600 mt-2 font-bold hidden"><i
                                        class="fa-solid fa-triangle-exclamation mr-1"></i> Accident corporel : le constat
                                    amiable est interdit par la loi.</p>
                                <p id="bris-glace-info" class="text-[10px] text-blue-600 mt-2 font-bold hidden"><i
                                        class="fa-solid fa-info-circle mr-1"></i> Bris de glace : déclaration directe sans
                                    alerte des autorités.</p>
                            </div>

                            @php
                                $assurances = \App\Models\User::where('role', 'assurance')->get();
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="assurance_id" class="block text-sm font-bold text-slate-700">Compagnie
                                        d'assurance (Optionnel)</label>
                                    <span id="assurance-linked-badge"
                                        class="hidden px-2 py-0.5 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100 animate-pulse">
                                        <i class="fa-solid fa-link mr-1"></i> Liée au véhicule
                                    </span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-building-shield text-slate-400"></i>
                                    </div>
                                    <select name="assurance_id" id="assurance_id"
                                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all font-medium text-slate-700 appearance-none cursor-pointer text-sm">
                                        <option value="" selected>Aucune (Transmettre direct à la Police/Gendarmerie)
                                        </option>
                                        @foreach($assurances as $assurance)
                                            <option value="{{ $assurance->id }}">{{ $assurance->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                                    </div>
                                </div>
                                @error('assurance_id')
                                    <p class="text-xs text-red-500 mt-1 font-medium"><i
                                            class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="flex-1 flex flex-col pt-2">
                                <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Circonstances
                                    brèves</label>
                                <textarea name="description" id="description" placeholder="Ex: Percuté par l'arrière..."
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all resize-none text-slate-700 text-sm h-32"></textarea>
                                @error('description')
                                    <p class="text-xs text-red-500 mt-1 font-medium"><i
                                            class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. PREUVES VISUELLES --}}
                <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden relative animate-in flex flex-col"
                    style="--delay: 0.4s">
                    <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                                3</div>
                            <h3 class="text-lg font-bold text-slate-800">Photos (Max 5)</h3>
                        </div>

                        {{-- Conteneur pour les aperçus des photos --}}
                        <div id="photos-container" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 empty:hidden w-full">
                            {{-- Les photos ajoutées apparaîtront ici --}}
                        </div>

                        {{-- Bouton d'ajout --}}
                        <div id="add-photo-btn"
                            class="group relative bg-slate-50 sm:hover:bg-slate-100 border-2 border-dashed border-slate-300 sm:hover:border-emerald-400 rounded-2xl p-6 text-center transition-all cursor-pointer overflow-hidden flex flex-col items-center justify-center w-full"
                            onclick="triggerPhotoUpload()">
                            <div class="relative z-10 flex flex-col items-center justify-center w-full">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-emerald-500 group-hover:scale-110 transition-all duration-300 mb-3">
                                    <i class="fa-solid fa-camera text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 mb-1">Prendre une photo</h4>
                                <p class="text-xs text-slate-500 mb-4 max-w-xs mx-auto"><span id="photo-count">0</span>/5
                                    photo(s) ajoutée(s).</p>

                                <span
                                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-sm group-hover:bg-emerald-500 group-hover:text-white group-hover:border-emerald-500 transition-colors">
                                    <i class="fa-solid fa-plus mr-1.5"></i> Ajouter
                                </span>
                            </div>
                        </div>

                        {{-- Conteneur pour stocker les vrais inputs file --}}
                        <div id="hidden-inputs-container" class="hidden"></div>

                        @error('photos')
                            <p class="text-xs text-red-500 mt-2 font-medium"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                        @error('photos.*')
                            <p class="text-xs text-red-500 mt-2 font-medium"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-col-reverse sm:flex-row shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:items-center justify-between gap-4 p-6 bg-white rounded-2xl border border-slate-100 animate-in"
                style="--delay: 0.5s">
                <a href="{{ route('assure.dashboard') }}"
                    class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors text-center">
                    Annuler
                </a>

                <button type="submit" id="btn-submit" disabled
                    class="w-full sm:w-auto px-10 py-3.5 rounded-xl bg-slate-200 text-slate-400 font-bold flex items-center justify-center transition-all duration-300 cursor-not-allowed">
                    <span>Transmettre l'alerte</span>
                    <i class="fa-solid fa-paper-plane ml-3"></i>
                </button>
            </div>

            <p class="text-center text-xs font-medium text-slate-400 px-4 mt-6">
                <i class="fa-solid fa-lock mr-1"></i> Vos informations sont cryptées et transmises directement aux forces de
                l'ordre.
            </p>

        </form>
    <style>
        /* Impact Selector Styles */
        .impact-selector {
            position: relative;
            width: 140px;
            height: 220px;
            background: white;
            border-radius: 2rem;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .car-shape {
            width: 50px;
            height: 100px;
            background: #cbd5e1;
            border-radius: 12px;
            position: relative;
            z-index: 1;
        }
        .car-shape::after {
            content: '';
            position: absolute;
            top: 10px;
            left: 5px;
            right: 5px;
            height: 20px;
            background: #94a3b8;
            border-radius: 4px;
        }
        .impact-arrow {
            position: absolute;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10;
            background: white;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .impact-arrow:hover {
            color: #94a3b8;
            border-color: #94a3b8;
            transform: scale(1.1);
        }
        .impact-arrow.active-a {
            color: white;
            background: #2563eb;
            border-color: #2563eb;
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .impact-arrow.active-b {
            color: white;
            background: #ea580c;
            border-color: #ea580c;
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }
        
        /* Arrow Positions */
        .arrow-front { top: 5px; left: 50%; transform: translateX(-50%); }
        .arrow-back { bottom: 5px; left: 50%; transform: translateX(-50%); }
        .arrow-left { left: 5px; top: 50%; transform: translateY(-50%); }
        .arrow-right { right: 5px; top: 50%; transform: translateY(-50%); }
        .arrow-fl { top: 20px; left: 15px; }
        .arrow-fr { top: 20px; right: 15px; }
        .arrow-bl { bottom: 20px; left: 15px; }
        .arrow-br { bottom: 20px; right: 15px; }
    </style>

    {{-- MODAL CONSTAT AMIABLE --}}
    <div id="constat-amiable-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeAmiableModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-5xl border border-slate-200">
                {{-- Header --}}
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <i class="fa-solid fa-file-signature text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800" id="modal-title">Constat Amiable d'Accident</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rapport détaillé des circonstances</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeAmiableModal()" class="w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 transition-all flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body (Scrollable) --}}
                <div class="px-8 py-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 relative">
                        
                        {{-- Colonne Véhicule A (Bleu) --}}
                        <div class="md:col-span-12 lg:col-span-4 space-y-6">
                            <div class="p-6 rounded-3xl bg-blue-50 border border-blue-100 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 -mr-8 -mt-8 rounded-full"></div>
                                <h4 class="text-blue-700 font-black text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center text-[10px]">A</span>
                                    VÉHICULE & CONDUCTEUR A
                                </h4>
                                
                                <div class="space-y-4">
                                    {{-- Section Véhicule --}}
                                    <div class="p-3 bg-white rounded-2xl border border-blue-100 shadow-sm">
                                        <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                                            <i class="fa-solid fa-car"></i> Véhicule
                                        </p>
                                        <p id="amiable-a-vehicle" class="text-sm font-bold text-slate-800"></p>
                                        <p id="amiable-a-name" class="text-[10px] text-slate-500 font-medium"></p>
                                    </div>
                                    
                                    {{-- Section Conducteur --}}
                                    <div class="p-3 bg-white rounded-2xl border border-blue-100 shadow-sm">
                                        <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                                            <i class="fa-solid fa-user-tie"></i> Conducteur
                                        </p>
                                        <input type="text" id="amiable-a-driver" class="w-full text-sm font-bold text-slate-800 bg-transparent border-none p-0 focus:ring-0" placeholder="Nom complet du conducteur">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Chocs A (Grid visuel simplifié avec diagramme) --}}
                            <div class="text-center p-6 border border-slate-100 rounded-3xl bg-slate-50/30">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Indiquer le point de choc initial (A)</p>
                                <div class="impact-selector" id="impact-a">
                                    <div class="car-shape"></div>
                                    <button type="button" class="impact-arrow arrow-front" data-point="avant" title="Avant"><i class="fa-solid fa-arrow-down"></i></button>
                                    <button type="button" class="impact-arrow arrow-back" data-point="arriere" title="Arrière"><i class="fa-solid fa-arrow-up"></i></button>
                                    <button type="button" class="impact-arrow arrow-left" data-point="gauche" title="Gauche"><i class="fa-solid fa-arrow-right"></i></button>
                                    <button type="button" class="impact-arrow arrow-right" data-point="droite" title="Droite"><i class="fa-solid fa-arrow-left"></i></button>
                                    <button type="button" class="impact-arrow arrow-fl" data-point="avant-gauche" title="Avant Gauche"><i class="fa-solid fa-arrow-down-right"></i></button>
                                    <button type="button" class="impact-arrow arrow-fr" data-point="avant-droite" title="Avant Droite"><i class="fa-solid fa-arrow-down-left"></i></button>
                                    <button type="button" class="impact-arrow arrow-bl" data-point="arriere-gauche" title="Arrière Gauche"><i class="fa-solid fa-arrow-up-right"></i></button>
                                    <button type="button" class="impact-arrow arrow-br" data-point="arriere-droite" title="Arrière Droite"><i class="fa-solid fa-arrow-up-left"></i></button>
                                </div>
                                <input type="hidden" id="amiable-a-impact" value="">
                            </div>
                        </div>

                        {{-- Colonne Circonstances (Milieu) --}}
                        <div class="md:col-span-12 lg:col-span-4 bg-slate-50/50 rounded-3xl border border-slate-100 p-6">
                            <h4 class="text-slate-500 font-black text-sm uppercase tracking-wider mb-6 text-center">12. Circonstances</h4>
                            
                            <div class="space-y-3">
                                @php
                                    $circonstances = [
                                        "En stationnement / à l'arrêt",
                                        "Quittait un stationnement / ouvrait une portière",
                                        "Prenait un stationnement",
                                        "Sortait d'un parking, d'un lieu privé, d'un chemin de terre",
                                        "S’engageait dans un parking, un lieu privé, un chemin de terre",
                                        "S’engageait sur une place à sens giratoire",
                                        "Roulait sur une place à sens giratoire",
                                        "Heurtait à l’arrière, en roulant dans le même sens et sur une même file",
                                        "Roulait dans le même sens et sur une file différente",
                                        "Changeait de file",
                                        "Doublait",
                                        "Virait à droite",
                                        "Virait à gauche",
                                        "Reculait",
                                        "Empiétait sur une voie réservée à la circulation en sens inverse",
                                        "Venait de droite (dans un carrefour)",
                                        "N’avait pas observé un signal de priorité ou un feu rouge"
                                    ];
                                @endphp

                                <div class="flex items-center justify-between mb-4 px-2">
                                    <span class="text-[10px] font-black text-blue-500 uppercase">A</span>
                                    <span class="text-[10px] font-black text-slate-300 uppercase italic">Cocher si concerné</span>
                                    <span class="text-[10px] font-black text-orange-500 uppercase">B</span>
                                </div>

                                @foreach($circonstances as $index => $label)
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" id="circ-a-{{ $index + 1 }}" class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500">
                                        <p class="flex-1 text-[11px] font-bold text-slate-600 text-center leading-tight">{{ $label }}</p>
                                        <input type="checkbox" id="circ-b-{{ $index + 1 }}" class="w-4 h-4 text-orange-600 bg-white border-slate-300 rounded focus:ring-orange-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Colonne Véhicule B (Orange/Jaune) --}}
                        <div class="md:col-span-12 lg:col-span-4 space-y-6">
                            <div class="p-6 rounded-3xl bg-orange-50 border border-orange-100 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-600/5 -mr-8 -mt-8 rounded-full"></div>
                                <h4 class="text-orange-700 font-black text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-orange-600 text-white flex items-center justify-center text-[10px]">B</span>
                                    VÉHICULE & CONDUCTEUR B
                                </h4>
                                
                                <div class="space-y-4">
                                    {{-- Section Véhicule --}}
                                    <div class="p-3 bg-white rounded-2xl border border-orange-100 shadow-sm">
                                        <p class="text-[9px] font-black text-orange-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                                            <i class="fa-solid fa-car"></i> Véhicule
                                        </p>
                                        <input type="text" id="amiable-b-vehicle" class="w-full text-sm font-bold text-slate-800 bg-transparent border-none p-0 focus:ring-0" placeholder="Marque & Immatriculation (B)">
                                    </div>
                                    
                                    {{-- Section Conducteur --}}
                                    <div class="p-4 bg-white rounded-2xl border border-orange-100 shadow-sm space-y-3">
                                        <p class="text-[9px] font-black text-orange-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                                            <i class="fa-solid fa-user-tie"></i> Conducteur
                                        </p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="text" id="amiable-b-nom" class="w-full text-xs font-bold text-slate-800 bg-slate-50 p-2 rounded-lg border border-orange-100 focus:outline-none focus:ring-1 focus:ring-orange-500/20" placeholder="Nom">
                                            <input type="text" id="amiable-b-prenom" class="w-full text-xs font-bold text-slate-800 bg-slate-50 p-2 rounded-lg border border-orange-100 focus:outline-none focus:ring-1 focus:ring-orange-500/20" placeholder="Prénom">
                                        </div>
                                        <input type="text" id="amiable-b-contact" class="w-full text-xs font-bold text-slate-800 bg-slate-50 p-2 rounded-lg border border-orange-100 focus:outline-none focus:ring-1 focus:ring-orange-500/20" placeholder="Tél ou email">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Chocs B --}}
                            <div class="text-center p-6 border border-slate-100 rounded-3xl bg-slate-50/30">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Indiquer le point de choc initial (B)</p>
                                <div class="impact-selector" id="impact-b">
                                    <div class="car-shape"></div>
                                    <button type="button" class="impact-arrow arrow-front" data-point="avant" title="Avant"><i class="fa-solid fa-arrow-down"></i></button>
                                    <button type="button" class="impact-arrow arrow-back" data-point="arriere" title="Arrière"><i class="fa-solid fa-arrow-up"></i></button>
                                    <button type="button" class="impact-arrow arrow-left" data-point="gauche" title="Gauche"><i class="fa-solid fa-arrow-right"></i></button>
                                    <button type="button" class="impact-arrow arrow-right" data-point="droite" title="Droite"><i class="fa-solid fa-arrow-left"></i></button>
                                    <button type="button" class="impact-arrow arrow-fl" data-point="avant-gauche" title="Avant Gauche"><i class="fa-solid fa-arrow-down-right"></i></button>
                                    <button type="button" class="impact-arrow arrow-fr" data-point="avant-droite" title="Avant Droite"><i class="fa-solid fa-arrow-down-left"></i></button>
                                    <button type="button" class="impact-arrow arrow-bl" data-point="arriere-gauche" title="Arrière Gauche"><i class="fa-solid fa-arrow-up-right"></i></button>
                                    <button type="button" class="impact-arrow arrow-br" data-point="arriere-droite" title="Arrière Droite"><i class="fa-solid fa-arrow-up-left"></i></button>
                                </div>
                                <input type="hidden" id="amiable-b-impact" value="">
                            </div>
                        </div>

                        {{-- Section Témoins (Optionnel) --}}
                        <div class="md:col-span-12 pt-6 border-t border-slate-100">
                            <h4 class="text-slate-500 font-black text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-users text-blue-500"></i>
                                3. Témoins (Noms, Adresses, Tél) - Optionnel
                            </h4>
                            <textarea id="amiable-temoins" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none text-slate-700 text-sm h-20" placeholder="Ex: Jean Dupont, 0102030405, Rue des Jardins..."></textarea>
                        </div>

                        {{-- Section Signature & Croquis --}}
                        <div class="md:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-100">
                            {{-- Croquis --}}
                            <div>
                                <h4 class="text-slate-500 font-black text-sm uppercase tracking-wider mb-4 flex items-center justify-between">
                                    <span>13. Croquis de l'accident</span>
                                    <button type="button" onclick="clearSketch()" class="text-[10px] text-red-500 hover:underline">Effacer</button>
                                </h4>
                                <div class="bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 p-2 h-64 relative">
                                    <canvas id="sketch-pad" class="w-full h-full cursor-crosshair"></canvas>
                                    <p class="absolute bottom-4 right-4 text-[9px] text-slate-400 italic pointer-events-none">Dessinez les positions des véhicules</p>
                                </div>
                            </div>

                            {{-- Signatures --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-blue-500 font-black text-[10px] uppercase tracking-wider mb-3 flex items-center justify-between">
                                        <span>Signature A</span>
                                        <button type="button" onclick="clearSigA()" class="text-[8px] text-red-400 hover:underline">Effacer</button>
                                    </h4>
                                    <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-2 h-48 relative">
                                        <canvas id="sig-a-pad" class="w-full h-full"></canvas>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-orange-500 font-black text-[10px] uppercase tracking-wider mb-3 flex items-center justify-between">
                                        <span>Signature B</span>
                                        <button type="button" onclick="clearSigB()" class="text-[8px] text-red-400 hover:underline">Effacer</button>
                                    </h4>
                                    <div class="bg-orange-50/50 rounded-2xl border border-orange-100 p-2 h-48 relative">
                                        <canvas id="sig-b-pad" class="w-full h-full"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-[10px] text-slate-400 font-medium max-w-sm">
                        <i class="fa-solid fa-circle-info mr-1 text-blue-500"></i> En validant, vous certifiez l'exactitude des informations rapportées.
                    </p>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button" onclick="closeAmiableModal()" class="flex-1 sm:flex-none px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-white transition-all">Annuler</button>
                        <button type="button" onclick="validateAmiableData()" class="flex-1 sm:flex-none px-10 py-3 rounded-xl bg-blue-600 text-white font-black shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
                            <span>Valider le constat</span>
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles d'animation locales --}}
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            opacity: 0;
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: var(--delay, 0s);
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnGeoloc = document.getElementById('btn-geoloc');
            const geolocStatus = document.getElementById('geoloc-status');
            const inputLat = document.getElementById('latitude');
            const inputLng = document.getElementById('longitude');
            const btnSubmit = document.getElementById('btn-submit');
            const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
            const photosContainer = document.getElementById('photos-container');
            const addPhotoBtn = document.getElementById('add-photo-btn');
            const photoCountSpan = document.getElementById('photo-count');

            let photoIndex = 0;
            let currentPhotoCount = 0;

            window.toggleAssisteurField = function (checkbox) {
                const field = document.getElementById('assisteur_field');
                if (checkbox.checked) {
                    field.classList.remove('hidden');
                } else {
                    field.classList.add('hidden');
                }
            };

            window.triggerPhotoUpload = function () {
                if (currentPhotoCount >= 5) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limite atteinte',
                        text: 'Vous ne pouvez pas ajouter plus de 5 photos.',
                        confirmButtonColor: '#10b981',
                        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl' }
                    });
                    return;
                }

                // Créer un nouvel input file caché
                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'photos[]';
                input.accept = 'image/*';
                input.capture = 'environment'; // Pour ouvrir l'appareil photo sur mobile
                input.className = 'hidden';
                input.id = `photo-input-${photoIndex}`;

                hiddenInputsContainer.appendChild(input);

                // Écouter le changement sur cet input
                input.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];

                        // Créer un aperçu
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            addPhotoPreview(e.target.result, file.name, input.id);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        // Si l'utilisateur annule, on supprime l'input
                        input.remove();
                    }
                });

                // Déclencher le clic sur ce nouvel input
                input.click();
            };

            function addPhotoPreview(imgSrc, fileName, inputId) {
                currentPhotoCount++;
                photoIndex++;
                photoCountSpan.innerText = currentPhotoCount;

                // Masquer le bouton d'ajout si limite atteinte
                if (currentPhotoCount >= 5) {
                    addPhotoBtn.classList.add('hidden');
                    addPhotoBtn.classList.remove('flex');
                }

                // Afficher le conteneur s'il était vide
                photosContainer.classList.remove('hidden');

                // Créer la carte de prévisualisation
                const div = document.createElement('div');
                div.className = 'relative group rounded-xl overflow-hidden border border-slate-200 bg-white shadow-sm flex items-center p-2 gap-3 animate-in';
                div.style.setProperty('--delay', '0s');
                div.id = `preview-${inputId}`;

                // Image miniature
                const img = document.createElement('div');
                img.className = 'w-12 h-12 rounded-lg bg-cover bg-center bg-slate-100 shrink-0';
                img.style.backgroundImage = `url('${imgSrc}')`;

                // Nom du fichier tronqué
                const name = document.createElement('div');
                name.className = 'flex-1 min-w-0';
                name.innerHTML = `<p class="text-xs font-semibold text-slate-700 truncate">${fileName}</p><p class="text-[10px] text-emerald-500 font-bold"><i class="fa-solid fa-check"></i> Prête</p>`;

                // Bouton de suppression
                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors shrink-0';
                delBtn.innerHTML = '<i class="fa-solid fa-trash-can text-sm"></i>';
                delBtn.onclick = function () {
                    // Supprimer l'input caché correspondant
                    document.getElementById(inputId).remove();
                    // Supprimer l'aperçu
                    div.remove();

                    currentPhotoCount--;
                    photoCountSpan.innerText = currentPhotoCount;

                    // Réafficher le bouton d'ajout
                    addPhotoBtn.classList.remove('hidden');
                    addPhotoBtn.classList.add('flex');
                };

                div.appendChild(img);
                div.appendChild(name);
                div.appendChild(delBtn);

                photosContainer.appendChild(div);
            }

            const incidentCheckboxes = document.querySelectorAll('.incident-checkbox');
            const incidentError = document.getElementById('incident-error');
            const radioAmiable = document.getElementById('constat-amiable');
            const radioPolice = document.getElementById('constat-police');
            const labelAmiable = document.getElementById('label-constat-amiable');
            const labelPolice = radioPolice.closest('label');
            const corporelWarning = document.getElementById('corporel-warning');
            const brisGlaceInfo = document.getElementById('bris-glace-info');
            const contratIdInput = document.getElementById('contrat_id');
            let isSelectionInProgress = false;

            const userVehicles = @json($contrats);

            window.showVehicleSelection = async function () {
                if (isSelectionInProgress) return;
                isSelectionInProgress = true;

                try {
                    if (userVehicles.length === 0) {
                        await Swal.fire({
                            title: 'Aucun véhicule trouvé',
                            text: 'Vous devez avoir un contrat d\'assurance valide pour déclarer un sinistre.',
                            icon: 'warning',
                            confirmButtonText: 'Ajouter une assurance',
                            showCancelButton: true,
                            confirmButtonColor: '#3b82f6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('assure.contrats.create') }}";
                            }
                        });
                        return;
                    }

                    let vehicleHtml = `
                            <div id="vehicle-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-center">
                                ${userVehicles.map(v => `
                                    <div class="vehicle-card group relative p-5 rounded-2xl border-2 border-slate-100 hover:border-orange-200 hover:bg-orange-50/30 transition-all cursor-pointer flex flex-col items-center gap-3 bg-white shadow-sm h-full"
                                         data-id="${v.id}">
                                        <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center shrink-0 group-hover:bg-orange-100 transition-colors">
                                            <i class="fa-solid fa-car text-2xl"></i>
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h4 class="font-bold text-slate-800 text-sm truncate px-1">${v.marque} ${v.modele}</h4>
                                            <div class="mt-1 px-2 py-0.5 rounded-lg bg-slate-50 border border-slate-100 inline-block">
                                                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest leading-none">${v.immatriculation}</p>
                                            </div>
                                            ${v.assureur ? `<p class="text-[8px] text-blue-500 font-bold mt-1 uppercase tracking-tighter"><i class="fa-solid fa-shield-halved mr-1"></i>${v.assureur.name}</p>` : ''}
                                        </div>
                                        <div class="select-indicator absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center transition-all group-hover:border-orange-300 bg-white">
                                            <div class="w-2.5 h-2.5 rounded-full bg-orange-500 scale-0 transition-transform"></div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        `;

                    let selectedId = contratIdInput.value || null;

                    const { value: result } = await Swal.fire({
                        title: '<span class="text-xl font-bold text-slate-800">Votre véhicule</span>',
                        html: `
                                <p class="text-sm text-slate-500 text-center mb-2">Lequel de vos véhicules a subi l'incident ?</p>
                                ${vehicleHtml}
                            `,
                        showCancelButton: true,
                        cancelButtonText: 'Annuler',
                        confirmButtonText: 'Confirmer la sélection',
                        confirmButtonColor: '#f97316',
                        allowOutsideClick: false,
                        width: '900px',
                        padding: '2.5rem',
                        customClass: {
                            popup: 'rounded-[2.5rem]',
                            confirmButton: 'rounded-xl px-12 shadow-lg shadow-orange-500/20 font-bold py-3.5 disabled:opacity-50 disabled:cursor-not-allowed',
                            cancelButton: 'rounded-xl px-8 font-bold py-3.5'
                        },
                        didRender: () => {
                            const confirmBtn = Swal.getConfirmButton();
                            confirmBtn.disabled = !selectedId;

                            const cards = Swal.getHtmlContainer().querySelectorAll('.vehicle-card');
                            cards.forEach(card => {
                                // Gérer le clic
                                card.addEventListener('click', () => {
                                    selectedId = card.getAttribute('data-id');

                                    // Reset other cards
                                    cards.forEach(c => {
                                        c.classList.remove('border-orange-500', 'bg-orange-50');
                                        c.classList.add('border-slate-100');
                                        const ind = c.querySelector('.select-indicator');
                                        const dot = ind.querySelector('div');
                                        ind.classList.remove('border-orange-500');
                                        dot.classList.remove('scale-100');
                                        dot.classList.add('scale-0');
                                    });

                                    // Highlight this card
                                    card.classList.add('border-orange-500', 'bg-orange-50');
                                    card.classList.remove('border-slate-100');
                                    const indicator = card.querySelector('.select-indicator');
                                    const dot = indicator.querySelector('div');
                                    indicator.classList.add('border-orange-500');
                                    dot.classList.add('scale-100');
                                    dot.classList.remove('scale-0');

                                    // Enable confirm button
                                    confirmBtn.disabled = false;
                                });

                                // Si déjà sélectionné, on simule un clic (ou on applique direct)
                                if (selectedId && card.getAttribute('data-id') == selectedId) {
                                    card.classList.add('border-orange-500', 'bg-orange-50');
                                    card.classList.remove('border-slate-100');
                                    const indicator = card.querySelector('.select-indicator');
                                    const dot = indicator.querySelector('div');
                                    indicator.classList.add('border-orange-500');
                                    dot.classList.add('scale-100');
                                    dot.classList.remove('scale-0');
                                }
                            });
                        },
                        preConfirm: () => {
                            if (!selectedId) {
                                Swal.showValidationMessage('Veuillez choisir un véhicule');
                                return false;
                            }
                            return selectedId;
                        }
                    });

                    if (result) {
                        const vehicleId = result;
                        contratIdInput.value = vehicleId;

                        const selectedVehicle = userVehicles.find(v => v.id == vehicleId);

                        // Mise à jour de l'affichage dans la carte 2
                        const displayContainer = document.getElementById('selected-vehicle-display');
                        const displayName = document.getElementById('display-vehicle-name');
                        const displayPlate = document.getElementById('display-vehicle-plate');

                        if (displayContainer && selectedVehicle) {
                            displayName.textContent = `${selectedVehicle.marque} ${selectedVehicle.modele}`;
                            displayPlate.textContent = selectedVehicle.immatriculation;
                            displayContainer.classList.remove('hidden');
                        }

                        // Auto-sélection de l'assurance
                        const badge = document.getElementById('assurance-linked-badge');
                        if (selectedVehicle && selectedVehicle.assurance_id) {
                            const assuranceSelect = document.getElementById('assurance_id');
                            if (assuranceSelect) {
                                assuranceSelect.value = selectedVehicle.assurance_id;
                                // Afficher le badge indicateur
                                if (badge) badge.classList.remove('hidden');

                                // Verrouiller le champ (l'utilisateur ne peut plus le changer)
                                assuranceSelect.disabled = true;
                                assuranceSelect.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-80');

                                // Ajouter un effet visuel sur le select
                                assuranceSelect.classList.add('border-blue-300');
                                setTimeout(() => {
                                    assuranceSelect.classList.remove('border-blue-300');
                                }, 2000);
                            }
                        } else {
                            if (badge) badge.classList.add('hidden');
                        }

                        await Swal.fire({
                            title: 'Véhicule validé !',
                            text: `${selectedVehicle.marque} ${selectedVehicle.modele}`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'rounded-3xl'
                            }
                        });
                        checkFormValidity();
                    }
                } finally {
                    isSelectionInProgress = false;
                }
            }

            function checkFormValidity() {
                const isGeolocated = inputLat.value !== "" && inputLng.value !== "";
                const selectedIncidents = Array.from(incidentCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                const isIncidentSelected = selectedIncidents.length > 0;

                const isOnlyBrisDeGlace = selectedIncidents.length === 1 && selectedIncidents.includes('Bris_de_glace');

                // LOGIQUE BRIS DE GLACE SEUL
                if (isOnlyBrisDeGlace) {
                    labelAmiable.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                    labelPolice.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                    radioAmiable.disabled = true;
                    radioPolice.disabled = true;
                    radioAmiable.checked = false;
                    radioPolice.checked = false;
                    radioAmiable.required = false;
                    brisGlaceInfo.classList.remove('hidden');
                } else {
                    labelPolice.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                    radioPolice.disabled = false;
                    radioAmiable.required = true;
                    brisGlaceInfo.classList.add('hidden');

                    // LOGIQUE CORPOREL (imbriquée car elle dépend de "pas bris de glace seul")
                    if (selectedIncidents.includes('Accident_corporel')) {
                        labelAmiable.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                        radioAmiable.disabled = true;
                        radioAmiable.checked = false;
                        radioPolice.checked = true;
                        corporelWarning.classList.remove('hidden');
                    } else {
                        labelAmiable.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                        radioAmiable.disabled = false;
                        corporelWarning.classList.add('hidden');
                    }
                }

                // LOGIQUE VEHICULE (SweetAlert2)
                if (isIncidentSelected && !contratIdInput.value) {
                    showVehicleSelection();
                }

                if (isGeolocated && isIncidentSelected && contratIdInput.value) {
                    enableSubmitButton();
                    incidentError.classList.add('hidden');
                } else {
                    disableSubmitButton();
                    if (!isIncidentSelected && isGeolocated) {
                        incidentError.classList.remove('hidden');
                    } else {
                        incidentError.classList.add('hidden');
                    }
                }
            }

            incidentCheckboxes.forEach(cb => {
                cb.addEventListener('change', checkFormValidity);
            });

            function fetchNearestServices(lat, lng) {
                const listContainer = document.getElementById('nearest-services-list');
                listContainer.classList.remove('hidden');
                listContainer.innerHTML = `
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Postes à proximité</p>
                        </div>
                        <div class="flex items-center justify-center py-6 bg-slate-100/50 rounded-2xl border border-slate-200 border-dashed">
                            <div class="flex flex-col items-center gap-2">
                                 <i class="fa-solid fa-satellite fa-spin text-blue-500 text-lg"></i>
                                 <p class="text-[10px] font-bold text-slate-400 animate-pulse">Localisation des postes...</p>
                            </div>
                        </div>
                    `;

                fetch(`{{ route('assure.sinistres.services-proches') }}?latitude=${lat}&longitude=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.services.length > 0) {
                            let html = `
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">3 meilleurs postes à proximité</p>
                                    </div>
                                `;
                            data.services.forEach((service, index) => {
                                const dist = parseFloat(service.distance).toFixed(1);
                                const icon = service.role === 'police' ? 'fa-building-shield' : (service.role === 'gendarmerie' ? 'fa-shield-halved' : 'fa-user-shield');
                                const color = service.role === 'police' ? 'text-blue-600' : (service.role === 'gendarmerie' ? 'text-emerald-600' : 'text-indigo-600');
                                const bgColor = service.role === 'police' ? 'bg-blue-100/50' : (service.role === 'gendarmerie' ? 'bg-emerald-100/50' : 'bg-indigo-100/50');

                                const roleLabel = service.role.charAt(0).toUpperCase() + service.role.slice(1);
                                const displayName = service.role === 'agent' && service.service
                                    ? `${service.name} <span class="text-[10px] font-medium text-slate-400">(${service.service.name})</span>`
                                    : service.name;

                                html += `
                                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white border border-slate-100 shadow-sm animate-in" style="--delay: ${index * 0.1}s">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl ${bgColor} ${color} flex items-center justify-center text-sm shadow-sm">
                                                    <i class="fa-solid ${icon}"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <h5 class="text-xs font-bold text-slate-800 truncate">${displayName}</h5>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-[8px] font-bold text-slate-500 uppercase tracking-tighter">${roleLabel}</span>
                                                        <span class="text-[10px] font-bold text-blue-600">${dist} km</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-6 h-6 rounded-lg bg-slate-50 flex items-center justify-center text-slate-300">
                                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                            </div>
                                        </div>
                                    `;
                            });
                            listContainer.innerHTML = html;
                        } else {
                            listContainer.innerHTML = '<div class="p-4 bg-orange-50 border border-orange-100 rounded-xl text-center"><p class="text-[10px] font-bold text-orange-600 uppercase">Aucun poste trouvé dans cette zone.</p></div>';
                        }
                    })
                    .catch(err => {
                        console.error('Erreur AJAX services:', err);
                        listContainer.innerHTML = '<div class="p-4 bg-red-50 border border-red-100 rounded-xl text-center"><p class="text-[10px] font-bold text-red-600 uppercase">Erreur de connexion aux services.</p></div>';
                    });
            }

            // Fonction automatique
            function autoLocate() {
                if (!navigator.geolocation) {
                    showGeolocError("API Géolocalisation non supportée.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        inputLat.value = lat;
                        inputLng.value = lng;

                        // Reverse Geocoding pour obtenir le nom du lieu
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, {
                            headers: { 'Accept-Language': 'fr' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.display_name) {
                                document.getElementById('lieu_input').value = data.display_name;
                                geolocStatus.innerHTML = `<i class="fa-solid fa-location-dot mr-1"></i> ${data.display_name}`;
                            }
                        })
                        .catch(err => console.error("Erreur geocoding:", err));

                        // UI success
                        btnGeoloc.className = 'inline-flex items-center justify-center md:justify-start w-full md:w-auto px-5 py-2.5 bg-green-50 text-green-700 font-bold rounded-xl text-sm border border-green-200';
                        btnGeoloc.innerHTML = '<i class="fa-solid fa-check-circle text-lg mr-2"></i> Localisation acquise';

                        geolocStatus.classList.remove('hidden', 'text-red-500');
                        geolocStatus.className = 'mt-3 text-xs font-bold text-green-600 bg-white px-3 py-1.5 rounded-lg border border-green-100 inline-block';
                        geolocStatus.innerHTML = `📍 LAT: ${lat.toFixed(4)} &nbsp;|&nbsp; LNG: ${lng.toFixed(4)}`;

                        // Récupération des 3 services les plus proches
                        fetchNearestServices(lat, lng);

                        checkFormValidity();
                    },
                    function (error) {
                        let errorMsg = "Erreur GPS.";
                        if (error.code === error.PERMISSION_DENIED) errorMsg = "Autorisation GPS refusée ! Autorisez-la dans la barre d'adresse et rechargez.";
                        if (error.code === error.POSITION_UNAVAILABLE) errorMsg = "Signal GPS perdu !";
                        if (error.code === error.TIMEOUT) errorMsg = "Délai d'attente GPS dépassé.";
                        showGeolocError(errorMsg);
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            }

            // --- LOGIQUE CONSTAT AMIABLE ---
            const amiableModal = document.getElementById('constat-amiable-modal');
            const amiableDataInput = document.getElementById('amiable_data');
            
            let sketchPad, sigAPad, sigBPad;

            // Initialisation des canvas
            function initPads() {
                const sketchCanvas = document.getElementById('sketch-pad');
                const sigACanvas = document.getElementById('sig-a-pad');
                const sigBCanvas = document.getElementById('sig-b-pad');

                // Resize observer pour gérer le responsive
                const resizeCanvas = (canvas) => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                };

                resizeCanvas(sketchCanvas);
                resizeCanvas(sigACanvas);
                resizeCanvas(sigBCanvas);

                sketchPad = new SignaturePad(sketchCanvas, { penColor: "rgb(30, 41, 59)", minWidth: 1, maxWidth: 3 });
                sigAPad = new SignaturePad(sigACanvas, { penColor: "rgb(37, 99, 235)", minWidth: 1, maxWidth: 2 });
                sigBPad = new SignaturePad(sigBCanvas, { penColor: "rgb(234, 88, 12)", minWidth: 1, maxWidth: 2 });
            }

            window.clearSketch = () => sketchPad.clear();
            window.clearSigA = () => sigAPad.clear();
            window.clearSigB = () => sigBPad.clear();

            window.openAmiableModal = function() {
                // Pré-remplissage Vehicule A
                const selectedId = contratIdInput.value;
                const vehicle = userVehicles.find(v => v.id == selectedId);
                const user = @json(auth('user')->user());

                document.getElementById('amiable-a-name').textContent = `${user.name} ${user.prenom || ''}`;
                document.getElementById('amiable-a-vehicle').textContent = vehicle ? `${vehicle.marque} ${vehicle.modele} (${vehicle.immatriculation})` : 'Véhicule non sélectionné';
                document.getElementById('amiable-a-driver').value = `${user.name} ${user.prenom || ''}`;

                amiableModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Un petit délai pour s'assurer que le modal est rendu avant d'init les pads
                setTimeout(initPads, 100);
            };

            window.closeAmiableModal = function() {
                amiableModal.classList.add('hidden');
                document.body.style.overflow = '';
                // Si on ferme sans valider, on remet le choix sur Police ?
                // radioPolice.checked = true;
                // checkFormValidity();
            };

            window.validateAmiableData = function() {
                // Collecte des données
                const data = {
                    partie_a: {
                        conducteur: document.getElementById('amiable-a-driver').value,
                        circonstances: []
                    },
                    partie_a: {
                        nom: document.getElementById('amiable-a-name').innerText,
                        vehicule: document.getElementById('amiable-a-vehicle').innerText,
                        conducteur: document.getElementById('amiable-a-driver').value,
                        point_choc: document.getElementById('amiable-a-impact').value,
                        circonstances: []
                    },
                    partie_b: {
                        nom: document.getElementById('amiable-b-nom').value,
                        prenom: document.getElementById('amiable-b-prenom').value,
                        contact: document.getElementById('amiable-b-contact').value,
                        vehicule: document.getElementById('amiable-b-vehicle').value,
                        point_choc: document.getElementById('amiable-b-impact').value,
                        circonstances: []
                    },
                    temoins: document.getElementById('amiable-temoins').value,
                    croquis: sketchPad.isEmpty() ? null : sketchPad.toDataURL(),
                    signature_a: sigAPad.isEmpty() ? null : sigAPad.toDataURL(),
                    signature_b: sigBPad.isEmpty() ? null : sigBPad.toDataURL()
                };

                // Checkboxes circonstances
                for(let i=1; i<=17; i++) {
                    if(document.getElementById(`circ-a-${i}`).checked) data.partie_a.circonstances.push(i);
                    if(document.getElementById(`circ-b-${i}`).checked) data.partie_b.circonstances.push(i);
                }

                // Validation minimale
                if(!data.partie_b.nom || !data.partie_b.vehicule) {
                    Swal.fire({ icon: 'error', title: 'Infos manquantes', text: 'Veuillez remplir au moins le nom et le véhicule de la partie B.', customClass: { popup: 'rounded-2xl' } });
                    return;
                }

                amiableDataInput.value = JSON.stringify(data);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Constat validé',
                    text: 'Les informations du constat ont été rattachées à votre déclaration.',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl' }
                });

                closeAmiableModal();
            };

            // Trigger modal on radio change
            radioAmiable.addEventListener('change', function() {
                if(this.checked) {
                    if(!contratIdInput.value) {
                        Swal.fire({ icon: 'warning', title: 'Véhicule requis', text: 'Veuillez d\'abord sélectionner votre véhicule.', customClass: { popup: 'rounded-2xl' } });
                        this.checked = false;
                        radioPolice.checked = false;
                        return;
                    }
                    openAmiableModal();
                }
            });

            // Gestion des points de choc
            document.querySelectorAll('#impact-a .impact-arrow').forEach(arrow => {
                arrow.addEventListener('click', function() {
                    document.querySelectorAll('#impact-a .impact-arrow').forEach(a => a.classList.remove('active-a'));
                    this.classList.add('active-a');
                    document.getElementById('amiable-a-impact').value = this.dataset.point;
                });
            });

            document.querySelectorAll('#impact-b .impact-arrow').forEach(arrow => {
                arrow.addEventListener('click', function() {
                    document.querySelectorAll('#impact-b .impact-arrow').forEach(a => a.classList.remove('active-b'));
                    this.classList.add('active-b');
                    document.getElementById('amiable-b-impact').value = this.dataset.point;
                });
            });

            // --- FIN LOGIQUE CONSTAT AMIABLE ---

            autoLocate();

            function showGeolocError(msg) {
                btnGeoloc.className = 'inline-flex items-center justify-center md:justify-start w-full md:w-auto px-5 py-2.5 bg-red-50 text-red-700 font-bold rounded-xl text-sm border border-red-200 cursor-pointer hover:bg-red-100 transition-colors';
                btnGeoloc.innerHTML = '<i class="fa-solid fa-rotate-right mr-2"></i> Réessayer';
                btnGeoloc.setAttribute('onclick', 'window.location.reload()');

                geolocStatus.classList.remove('hidden');
                geolocStatus.className = 'mt-3 text-sm font-bold text-red-500';
                geolocStatus.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i> ${msg}`;

                inputLat.value = "";
                inputLng.value = "";
                checkFormValidity();
            }

            // S'assurer que les champs disabled sont envoyés lors du submit
            const form = document.getElementById('sinistre-form');
            if (form) {
                form.addEventListener('submit', function () {
                    const assuranceSelect = document.getElementById('assurance_id');
                    if (assuranceSelect) {
                        assuranceSelect.disabled = false;
                    }
                });
            }

            function enableSubmitButton() {
                btnSubmit.removeAttribute('disabled');
                btnSubmit.className = 'w-full sm:w-auto px-10 py-3.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold flex items-center justify-center shadow-lg shadow-red-600/30 transition-all duration-300 transform hover:-translate-y-0.5';
            }

            function disableSubmitButton() {
                btnSubmit.setAttribute('disabled', 'disabled');
                btnSubmit.className = 'w-full sm:w-auto px-10 py-3.5 rounded-xl bg-slate-200 text-slate-400 font-bold flex items-center justify-center transition-all duration-300 cursor-not-allowed';
            }
        });
    </script>
@endpush