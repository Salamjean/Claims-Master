@extends('assure.layouts.template')

@section('title', 'Déclarer un sinistre')
@section('page-title', 'Nouveau sinistre')

@php
    $incidents = [
        'Accident_matériel' => ['label' => 'Accident matériel', 'icon' => 'fa-car-burst',     'hint' => 'Dégâts sur le véhicule'],
        'Accident_corporel' => ['label' => 'Accident corporel', 'icon' => 'fa-user-injured',  'hint' => 'Personne(s) blessée(s)'],
        'Vol'               => ['label' => 'Vol / Braquage',    'icon' => 'fa-mask',          'hint' => 'Véhicule ou biens volés'],
        'Incendie'          => ['label' => 'Incendie',          'icon' => 'fa-fire',          'hint' => 'Feu sur le véhicule'],
        'Bris_de_glace'     => ['label' => 'Bris de glace',     'icon' => 'fa-hammer',        'hint' => 'Pare-brise, vitres…'],
        'Autre'             => ['label' => 'Autre',             'icon' => 'fa-ellipsis',      'hint' => 'Précisez dans la description'],
    ];

    $circonstances = [
        "En stationnement / à l'arrêt",
        'Quittait un stationnement / ouvrait une portière',
        'Prenait un stationnement',
        "Sortait d'un parking, d'un lieu privé, d'un chemin de terre",
        'S’engageait dans un parking, un lieu privé, un chemin de terre',
        'S’engageait sur une place à sens giratoire',
        'Roulait sur une place à sens giratoire',
        'Heurtait à l’arrière, en roulant dans le même sens et sur une même file',
        'Roulait dans le même sens et sur une file différente',
        'Changeait de file',
        'Doublait',
        'Virait à droite',
        'Virait à gauche',
        'Reculait',
        'Empiétait sur une voie réservée à la circulation en sens inverse',
        'Venait de droite (dans un carrefour)',
        'N’avait pas observé un signal de priorité ou un feu rouge',
    ];

    // Points de choc : position de la flèche + rotation de l'icône (flèche dirigée vers le véhicule)
    $impactPoints = [
        ['point' => 'avant',          'title' => 'Avant',          'pos' => 'arrow-front', 'icon' => 'fa-arrow-down', 'rot' => 0],
        ['point' => 'arriere',        'title' => 'Arrière',        'pos' => 'arrow-back',  'icon' => 'fa-arrow-up',   'rot' => 0],
        ['point' => 'gauche',         'title' => 'Gauche',         'pos' => 'arrow-left',  'icon' => 'fa-arrow-right','rot' => 0],
        ['point' => 'droite',         'title' => 'Droite',         'pos' => 'arrow-right', 'icon' => 'fa-arrow-left', 'rot' => 0],
        ['point' => 'avant-gauche',   'title' => 'Avant gauche',   'pos' => 'arrow-fl',    'icon' => 'fa-arrow-down', 'rot' => -45],
        ['point' => 'avant-droite',   'title' => 'Avant droite',   'pos' => 'arrow-fr',    'icon' => 'fa-arrow-down', 'rot' => 45],
        ['point' => 'arriere-gauche', 'title' => 'Arrière gauche', 'pos' => 'arrow-bl',    'icon' => 'fa-arrow-up',   'rot' => 45],
        ['point' => 'arriere-droite', 'title' => 'Arrière droite', 'pos' => 'arrow-br',    'icon' => 'fa-arrow-up',   'rot' => -45],
    ];

    $authUser = auth('user')->user();

    $labelClass = 'block text-[13px] font-semibold text-slate-700 mb-1.5';
    $inputClass = 'w-full px-3.5 rounded-lg border border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10';
@endphp

@section('content')
    <div class="w-full pb-12">

        {{-- ═════════ EN-TÊTE ═════════ --}}
        @php
            // Retour vers la page précédente (ou le tableau de bord si on arrive directement ici)
            $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('assure.dashboard');
        @endphp
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl md:text-[28px] font-bold tracking-tight text-slate-900">Déclarer un sinistre</h1>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg bg-red-50 text-red-700 text-xs font-medium">
                    <i class="fa-solid fa-phone"></i> Urgence : 110 · 111 · 180
                </span>
                <a href="{{ $backUrl }}"
                    class="inline-flex items-center gap-2 h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-[13px] font-semibold text-slate-700 transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Retour
                </a>
            </div>
        </div>

        <form action="{{ route('assure.sinistres.store') }}" method="POST" enctype="multipart/form-data" id="sinistre-form"
            class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">
            @csrf

            {{-- Champs cachés --}}
            <input type="hidden" name="latitude" id="latitude" required>
            <input type="hidden" name="longitude" id="longitude" required>
            <input type="hidden" name="lieu" id="lieu_input">
            <input type="hidden" name="contrat_id" id="contrat_id" required>
            <input type="hidden" name="amiable_data" id="amiable_data">

            {{-- ═════════ COLONNE PRINCIPALE ═════════ --}}
            <div class="space-y-6 min-w-0">

                {{-- 1. VÉHICULE & NATURE DE L'INCIDENT --}}
                <section class="rounded-2xl bg-white border border-slate-200">
                    <header class="flex items-center gap-3 px-6 pt-5 pb-4 border-b border-slate-100">
                        <span class="w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-bold flex items-center justify-center">1</span>
                        <div>
                            <h2 class="text-[15px] font-semibold text-slate-900">Véhicule et nature de l'incident</h2>
                            <p class="text-xs text-slate-500">Choisissez le véhicule concerné puis ce qui s'est passé.</p>
                        </div>
                    </header>

                    <div class="p-6 grid grid-cols-1 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] gap-4 items-start">
                        {{-- Carte : véhicule --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-car text-slate-400 text-sm"></i>
                                <p class="text-[13px] font-semibold text-slate-900">Véhicule concerné <span class="text-red-500">*</span></p>
                            </div>

                            <button type="button" id="vehicle-empty" onclick="showVehicleSelection()"
                                class="w-full flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-300 bg-white hover:border-blue-500 hover:bg-blue-50/40 text-left transition">
                                <span class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-plus"></i></span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-[13px] font-semibold text-slate-900">Choisir le véhicule</span>
                                    <span class="block text-xs text-slate-500">Parmi vos assurances actives et vérifiées</span>
                                </span>
                                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                            </button>

                            <div id="selected-vehicle-display" class="hidden">
                                <div class="p-4 rounded-xl border border-blue-200 bg-white">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0"><i class="fa-solid fa-car-side"></i></span>
                                        <div class="flex-1 min-w-0">
                                            <p id="display-vehicle-name" class="text-[13px] font-semibold text-slate-900 truncate"></p>
                                            <p id="display-vehicle-plate" class="text-xs font-mono font-semibold text-slate-500 uppercase tracking-wide"></p>
                                        </div>
                                        <i class="fa-solid fa-circle-check text-blue-600"></i>
                                    </div>
                                    <button type="button" onclick="showVehicleSelection()"
                                        class="mt-3 w-full h-8 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition inline-flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Changer de véhicule
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Carte : type d'incident --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-triangle-exclamation text-slate-400 text-sm"></i>
                                <p class="text-[13px] font-semibold text-slate-900">Type(s) d'incident <span class="text-red-500">*</span></p>
                                <span class="text-xs text-slate-400">· plusieurs choix possibles</span>
                            </div>
                            <div class="grid grid-cols-2 xl:grid-cols-3 gap-2">
                                @foreach ($incidents as $value => $data)
                                    <label class="relative flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 bg-white cursor-pointer transition hover:border-slate-300 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-600/10">
                                        <input type="checkbox" name="type_sinistre[]" value="{{ $value }}"
                                            class="incident-checkbox peer sr-only">
                                        <span class="w-8 h-8 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 peer-checked:bg-blue-600 peer-checked:text-white transition">
                                            <i class="fa-solid {{ $data['icon'] }} text-xs"></i>
                                        </span>
                                        <span class="min-w-0 pr-4">
                                            <span class="block text-[13px] font-semibold text-slate-900 truncate">{{ $data['label'] }}</span>
                                            <span class="block text-[11px] text-slate-500 truncate">{{ $data['hint'] }}</span>
                                        </span>
                                        <i class="fa-solid fa-circle-check text-blue-600 text-xs absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition"></i>
                                    </label>
                                @endforeach
                            </div>
                            <p id="incident-error" class="hidden text-xs text-red-600 mt-2"><i class="fa-solid fa-circle-exclamation mr-1"></i>Sélectionnez au moins un type d'incident.</p>
                            @error('type_sinistre')
                                <p class="text-xs text-red-600 mt-2"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- 2. CONSTAT, CIRCONSTANCES & PHOTOS --}}
                <section class="rounded-2xl bg-white border border-slate-200">
                    <header class="flex items-center gap-3 px-6 pt-5 pb-4 border-b border-slate-100">
                        <span class="w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-bold flex items-center justify-center">2</span>
                        <div>
                            <h2 class="text-[15px] font-semibold text-slate-900">Constat et circonstances</h2>
                            <p class="text-xs text-slate-500">Comment le constat est établi, ce qui s'est passé et les photos.</p>
                        </div>
                    </header>

                    <div class="p-6 grid grid-cols-1 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] gap-4 items-start">

                        {{-- Carte : constat & circonstances --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-5">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-file-signature text-slate-400 text-sm"></i>
                                <p class="text-[13px] font-semibold text-slate-900">Constat et circonstances</p>
                            </div>

                            {{-- Méthode de constat --}}
                            <div>
                                <p class="{{ $labelClass }}">Établissement du constat <span class="text-red-500">*</span></p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <label id="label-constat-amiable"
                                        class="relative flex items-start gap-3 p-3 rounded-lg border border-slate-200 bg-white cursor-pointer transition hover:border-slate-300 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-600/10">
                                        <input type="radio" name="methode_constat" id="constat-amiable" value="Amiable" required
                                            class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-600">
                                        <span class="min-w-0">
                                            <span class="block text-[13px] font-semibold text-slate-900">Constat amiable</span>
                                            <span class="block text-[11px] text-slate-500">Rempli ici avec l'autre partie</span>
                                        </span>
                                    </label>
                                    <label
                                        class="relative flex items-start gap-3 p-3 rounded-lg border border-slate-200 bg-white cursor-pointer transition hover:border-slate-300 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-600/10">
                                        <input type="radio" name="methode_constat" id="constat-police" value="Police_Gendarmerie"
                                            class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-600">
                                        <span class="min-w-0">
                                            <span class="block text-[13px] font-semibold text-slate-900">Police / Gendarmerie</span>
                                            <span class="block text-[11px] text-slate-500">Procès-verbal des autorités</span>
                                        </span>
                                    </label>
                                </div>

                                {{-- Constat amiable rempli --}}
                                <div id="amiable-done" class="hidden mt-2 flex items-center gap-3 p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                                    <i class="fa-solid fa-file-circle-check text-emerald-600"></i>
                                    <p class="flex-1 text-xs text-emerald-800"><span class="font-semibold">Constat amiable rempli</span> et rattaché à la déclaration.</p>
                                    <button type="button" onclick="openAmiableModal()" class="text-xs font-semibold text-emerald-700 hover:underline">Modifier</button>
                                </div>

                                <p id="corporel-warning" class="hidden mt-2 text-xs text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Accident corporel : le constat amiable est interdit par la loi. Les autorités seront alertées.
                                </p>
                                <p id="bris-glace-info" class="hidden mt-2 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                                    <i class="fa-solid fa-circle-info mr-1"></i> Bris de glace seul : déclaration directe, sans alerte des autorités.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                            {{-- Compagnie d'assurance --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="assurance_id" class="text-[13px] font-semibold text-slate-700">Compagnie d'assurance <span class="font-normal text-slate-400">(optionnel)</span></label>
                                    <span id="assurance-linked-badge" class="hidden text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">
                                        <i class="fa-solid fa-link mr-1"></i>Liée au véhicule
                                    </span>
                                </div>
                                <div class="relative">
                                    <select name="assurance_id" id="assurance_id" class="{{ $inputClass }} h-10 appearance-none pr-10 cursor-pointer">
                                        <option value="" selected>Aucune (transmettre directement aux autorités)</option>
                                        @foreach ($assurances as $assurance)
                                            <option value="{{ $assurance->id }}">{{ $assurance->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                </div>
                                @error('assurance_id')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Circonstances --}}
                            <div>
                                <label for="description" class="{{ $labelClass }}">Circonstances <span class="font-normal text-slate-400">(brèves)</span></label>
                                <textarea name="description" id="description" rows="2" placeholder="Ex : percuté par l'arrière au feu rouge…"
                                    class="{{ $inputClass }} py-2.5 resize-y min-h-[40px]"></textarea>
                                @error('description')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                            </div>
                        </div>

                        {{-- Carte : photos --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-camera text-slate-400 text-sm"></i>
                                    <p class="text-[13px] font-semibold text-slate-900">Photos</p>
                                </div>
                                <span class="text-xs font-medium text-slate-500"><span id="photo-count">0</span> / 5</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mb-3">Dégâts, plaques, scène de l'accident.</p>

                            <div class="grid grid-cols-3 gap-2">
                                <div id="photos-container" class="contents"></div>

                                <button type="button" id="add-photo-btn" onclick="triggerPhotoUpload()"
                                    class="flex aspect-square flex-col items-center justify-center gap-1 rounded-lg border border-dashed border-slate-300 bg-white hover:border-blue-500 hover:bg-blue-50/40 text-slate-500 hover:text-blue-600 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span class="text-[11px] font-semibold">Ajouter</span>
                                </button>
                            </div>

                            <div id="hidden-inputs-container" class="hidden"></div>

                            @error('photos')
                                <p class="text-xs text-red-600 mt-3">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="text-xs text-red-600 mt-3">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>
            </div>

            {{-- ═════════ COLONNE LATÉRALE ═════════ --}}
            <aside class="space-y-6 xl:sticky xl:top-2">

                {{-- Géolocalisation --}}
                <section class="rounded-2xl bg-white border border-slate-200">
                    <header class="flex items-center justify-between gap-3 px-5 pt-5 pb-4">
                        <div>
                            <h2 class="text-[15px] font-semibold text-slate-900">Votre position</h2>
                            <p class="text-xs text-slate-500">Pour alerter le poste le plus proche</p>
                        </div>
                        <span class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-location-crosshairs"></i></span>
                    </header>

                    <div class="px-5 pb-5">
                        <div id="btn-geoloc" class="flex items-center gap-2 h-10 px-3.5 rounded-lg bg-blue-50 text-blue-700 text-[13px] font-semibold">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Recherche de votre position…
                        </div>

                        <p id="geoloc-address" class="hidden mt-3 text-[13px] text-slate-700 leading-snug"></p>
                        <div id="geoloc-status" class="hidden"></div>

                        <div id="nearest-services-list" class="hidden mt-5 space-y-2"></div>
                    </div>
                </section>

                {{-- Récapitulatif & envoi --}}
                <section class="rounded-2xl bg-white border border-slate-200 p-5">
                    <h2 class="text-[15px] font-semibold text-slate-900 mb-3">Avant d'envoyer</h2>
                    <ul class="space-y-2.5 text-[13px]">
                        @foreach(['check-geo' => 'Position détectée', 'check-vehicle' => 'Véhicule sélectionné', 'check-incident' => "Type d'incident choisi"] as $id => $label)
                            <li id="{{ $id }}" class="flex items-center gap-2.5 text-slate-500">
                                <span data-dot class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center text-[9px] text-white transition"></span>
                                {{ $label }}
                            </li>
                        @endforeach
                    </ul>

                    <button type="submit" id="btn-submit" disabled
                        class="mt-5 w-full h-11 rounded-lg bg-slate-100 text-slate-400 text-sm font-semibold flex items-center justify-center gap-2 cursor-not-allowed transition">
                        <span>Transmettre l'alerte</span>
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                    <a href="{{ route('assure.dashboard') }}" class="mt-2 w-full h-10 rounded-lg text-[13px] font-semibold text-slate-500 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center transition">
                        Annuler
                    </a>

                    <p class="mt-4 pt-4 border-t border-slate-100 text-[11px] text-slate-400 flex items-start gap-2">
                        <i class="fa-solid fa-lock mt-0.5"></i>
                        Vos informations sont chiffrées et transmises directement aux forces de l'ordre.
                    </p>
                </section>
            </aside>
        </form>

        {{-- ═════════ MODALE CONSTAT AMIABLE ═════════ --}}
        <div id="constat-amiable-modal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-slate-900/50" aria-hidden="true" onclick="closeAmiableModal()"></div>

            <div class="relative h-full w-full flex items-stretch sm:items-center justify-center sm:p-6">
                <div class="relative w-full max-w-6xl bg-white sm:rounded-2xl border border-slate-200 shadow-xl flex flex-col max-h-full">

                    {{-- En-tête --}}
                    <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-200 shrink-0">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center"><i class="fa-solid fa-file-signature"></i></span>
                            <div>
                                <h3 id="modal-title" class="text-base font-semibold text-slate-900">Constat amiable d'accident</h3>
                                <p class="text-xs text-slate-500">Rapport détaillé des circonstances</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeAmiableModal()" class="w-9 h-9 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition" title="Fermer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    {{-- Corps --}}
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                            {{-- Véhicule A --}}
                            <div class="lg:col-span-4 space-y-4">
                                <div class="rounded-xl border border-blue-200 overflow-hidden">
                                    <div class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white">
                                        <span class="w-6 h-6 rounded bg-white/20 text-xs font-bold flex items-center justify-center">A</span>
                                        <span class="text-[13px] font-semibold">Votre véhicule</span>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <div>
                                            <p class="text-[11px] text-slate-500">Véhicule</p>
                                            <p id="amiable-a-vehicle" class="text-[13px] font-semibold text-slate-900"></p>
                                            <p id="amiable-a-name" class="text-xs text-slate-500"></p>
                                        </div>
                                        <div>
                                            <label for="amiable-a-driver" class="text-[11px] text-slate-500">Conducteur</label>
                                            <input type="text" id="amiable-a-driver" placeholder="Nom complet du conducteur"
                                                class="{{ $inputClass }} h-10 mt-1">
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4 text-center">
                                    <p class="text-xs font-semibold text-slate-600 mb-4">Point de choc initial (A)</p>
                                    <div class="impact-selector" id="impact-a">
                                        <div class="car-shape"></div>
                                        @foreach($impactPoints as $ip)
                                            <button type="button" class="impact-arrow {{ $ip['pos'] }}" data-point="{{ $ip['point'] }}" title="{{ $ip['title'] }}">
                                                <i class="fa-solid {{ $ip['icon'] }}" style="transform: rotate({{ $ip['rot'] }}deg)"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" id="amiable-a-impact" value="">
                                </div>
                            </div>

                            {{-- Circonstances --}}
                            <div class="lg:col-span-4 rounded-xl border border-slate-200 overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                                    <span class="w-6 h-6 rounded bg-blue-600 text-white text-xs font-bold flex items-center justify-center">A</span>
                                    <span class="text-[13px] font-semibold text-slate-700">Circonstances</span>
                                    <span class="w-6 h-6 rounded bg-orange-500 text-white text-xs font-bold flex items-center justify-center">B</span>
                                </div>
                                <p class="text-[11px] text-slate-400 text-center pt-2">Cochez les cases concernées pour chaque véhicule</p>
                                <div class="divide-y divide-slate-100">
                                    @foreach ($circonstances as $index => $label)
                                        <div class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50">
                                            <input type="checkbox" id="circ-a-{{ $index + 1 }}" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                            <p class="flex-1 text-xs text-slate-700 text-center leading-snug">
                                                <span class="text-slate-400 mr-0.5">{{ $index + 1 }}.</span> {{ $label }}
                                            </p>
                                            <input type="checkbox" id="circ-b-{{ $index + 1 }}" class="w-4 h-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Véhicule B --}}
                            <div class="lg:col-span-4 space-y-4">
                                <div class="rounded-xl border border-orange-200 overflow-hidden">
                                    <div class="flex items-center gap-2 px-4 py-2.5 bg-orange-500 text-white">
                                        <span class="w-6 h-6 rounded bg-white/20 text-xs font-bold flex items-center justify-center">B</span>
                                        <span class="text-[13px] font-semibold">Autre véhicule</span>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <div>
                                            <label for="amiable-b-vehicle" class="text-[11px] text-slate-500">Véhicule <span class="text-red-500">*</span></label>
                                            <input type="text" id="amiable-b-vehicle" placeholder="Marque et immatriculation"
                                                class="{{ $inputClass }} h-10 mt-1">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label for="amiable-b-nom" class="text-[11px] text-slate-500">Nom <span class="text-red-500">*</span></label>
                                                <input type="text" id="amiable-b-nom" placeholder="Nom" class="{{ $inputClass }} h-10 mt-1">
                                            </div>
                                            <div>
                                                <label for="amiable-b-prenom" class="text-[11px] text-slate-500">Prénom</label>
                                                <input type="text" id="amiable-b-prenom" placeholder="Prénom" class="{{ $inputClass }} h-10 mt-1">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="amiable-b-contact" class="text-[11px] text-slate-500">Contact</label>
                                            <input type="text" id="amiable-b-contact" placeholder="Téléphone ou e-mail" class="{{ $inputClass }} h-10 mt-1">
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4 text-center">
                                    <p class="text-xs font-semibold text-slate-600 mb-4">Point de choc initial (B)</p>
                                    <div class="impact-selector" id="impact-b">
                                        <div class="car-shape"></div>
                                        @foreach($impactPoints as $ip)
                                            <button type="button" class="impact-arrow {{ $ip['pos'] }}" data-point="{{ $ip['point'] }}" title="{{ $ip['title'] }}">
                                                <i class="fa-solid {{ $ip['icon'] }}" style="transform: rotate({{ $ip['rot'] }}deg)"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" id="amiable-b-impact" value="">
                                </div>
                            </div>

                            {{-- Témoins --}}
                            <div class="lg:col-span-12">
                                <label for="amiable-temoins" class="{{ $labelClass }}">Témoins <span class="font-normal text-slate-400">(noms, adresses, téléphones · optionnel)</span></label>
                                <textarea id="amiable-temoins" rows="2" placeholder="Ex : Jean Kouassi, 01 02 03 04 05, Cocody…"
                                    class="{{ $inputClass }} py-2.5 resize-none"></textarea>
                            </div>

                            {{-- Croquis --}}
                            <div class="lg:col-span-7">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                    <p class="text-[13px] font-semibold text-slate-700">Croquis de l'accident</p>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" id="sketch-draw-btn" onclick="setSketchMode('draw')" title="Dessin à main levée"></button>
                                        <button type="button" id="sketch-move-btn" onclick="setSketchMode('select')" title="Déplacer / tourner les éléments"></button>
                                        <button type="button" onclick="clearSketch()" class="h-7 px-2.5 rounded-md text-[11px] font-semibold text-red-600 hover:bg-red-50 transition">Effacer</button>
                                    </div>
                                </div>

                                {{-- Palette de symboles --}}
                                <div id="sketch-palette" class="flex items-center gap-1.5 overflow-x-auto pb-2 select-none" style="scrollbar-width:thin;">
                                    <span class="text-[11px] text-slate-400 whitespace-nowrap shrink-0 mr-1">Cliquez ou glissez :</span>
                                    @foreach([
                                        'auto' => ['Auto', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 64" width="22" height="24"><rect x="5" y="12" width="30" height="40" rx="6" fill="#64748b" stroke="#1e293b" stroke-width="1.5"/><rect x="9" y="15" width="22" height="10" rx="3" fill="#bfdbfe"/><rect x="9" y="40" width="22" height="8" rx="3" fill="#bfdbfe"/><rect x="1" y="15" width="5" height="9" rx="2" fill="#1e293b"/><rect x="34" y="15" width="5" height="9" rx="2" fill="#1e293b"/><rect x="1" y="38" width="5" height="9" rx="2" fill="#1e293b"/><rect x="34" y="38" width="5" height="9" rx="2" fill="#1e293b"/></svg>'],
                                        'deux_roues' => ['2 roues', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 64" width="12" height="24"><rect x="5" y="6" width="12" height="52" rx="5" fill="#475569" stroke="#1e293b" stroke-width="1.5"/><rect x="1" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="16" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="1" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="16" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/></svg>'],
                                        'camion' => ['Camion', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 90" width="18" height="24"><rect x="4" y="4" width="42" height="20" rx="4" fill="#6b7280" stroke="#1e293b" stroke-width="1.5"/><rect x="8" y="7" width="34" height="11" rx="3" fill="#bfdbfe"/><rect x="3" y="28" width="44" height="58" rx="3" fill="#9ca3af" stroke="#1e293b" stroke-width="1.5"/><line x1="9" y1="57" x2="41" y2="57" stroke="#6b7280" stroke-width="1.5" stroke-dasharray="4,3"/></svg>'],
                                        'pieton' => ['Piéton', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 46" width="16" height="24"><circle cx="15" cy="7" r="6" fill="#e2e8f0" stroke="#1e293b" stroke-width="1.5"/><line x1="15" y1="13" x2="15" y2="30" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="21" x2="25" y2="21" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="30" x2="7" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="30" x2="23" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/></svg>'],
                                        'feu' => ['Feu', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 58" width="10" height="24"><rect x="2" y="2" width="20" height="40" rx="4" fill="#1e293b"/><circle cx="12" cy="10" r="5" fill="#ef4444"/><circle cx="12" cy="22" r="5" fill="#f59e0b"/><circle cx="12" cy="34" r="5" fill="#22c55e"/><rect x="10" y="42" width="4" height="12" fill="#374151"/></svg>'],
                                        'balise' => ['Balise', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 60" width="20" height="24"><polygon points="25,3 47,46 3,46" fill="white" stroke="#1e293b" stroke-width="2"/><polygon points="25,11 41,43 9,43" fill="none" stroke="#e11d48" stroke-width="1.5"/><line x1="25" y1="46" x2="25" y2="58" stroke="#374151" stroke-width="3" stroke-linecap="round"/></svg>'],
                                        'stop' => ['Stop', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 60" width="20" height="24"><polygon points="15,2 35,2 48,15 48,35 35,48 15,48 2,35 2,15" fill="#dc2626" stroke="#1e293b" stroke-width="1.5"/><text x="25" y="31" text-anchor="middle" fill="white" font-size="11" font-family="Arial" font-weight="bold">STOP</text><line x1="25" y1="48" x2="25" y2="58" stroke="#374151" stroke-width="3" stroke-linecap="round"/></svg>'],
                                    ] as $sym => [$symLabel, $svg])
                                        <div class="sketch-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 w-14 py-1.5 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition"
                                            draggable="true" data-sym="{{ $sym }}" onclick="addSketchSymbol('{{ $sym }}')" title="Ajouter : {{ $symLabel }}">
                                            {!! $svg !!}
                                            <span class="text-[10px] text-slate-600 font-medium whitespace-nowrap">{{ $symLabel }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div id="sketch-wrapper" class="relative rounded-xl border border-dashed border-slate-300 bg-white overflow-hidden" style="height:280px;">
                                    <canvas id="sketch-pad"></canvas>
                                    <p id="sketch-hint" class="absolute bottom-3 right-4 text-[11px] text-slate-300 pointer-events-none select-none">Dessinez ou déposez des symboles</p>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1.5">Astuce : en mode « Déplacer », sélectionnez un élément puis appuyez sur Suppr pour l'effacer.</p>
                            </div>

                            {{-- Signatures --}}
                            <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[13px] font-semibold text-blue-700">Signature A</p>
                                        <button type="button" onclick="clearSigA()" class="text-[11px] font-semibold text-red-600 hover:underline">Effacer</button>
                                    </div>
                                    <div class="h-36 rounded-xl border border-blue-200 bg-blue-50/40 p-1.5">
                                        <canvas id="sig-a-pad" class="w-full h-full"></canvas>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[13px] font-semibold text-orange-600">Signature B</p>
                                        <button type="button" onclick="clearSigB()" class="text-[11px] font-semibold text-red-600 hover:underline">Effacer</button>
                                    </div>
                                    <div class="h-36 rounded-xl border border-orange-200 bg-orange-50/40 p-1.5">
                                        <canvas id="sig-b-pad" class="w-full h-full"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pied --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 sm:rounded-b-2xl shrink-0">
                        <p class="text-xs text-slate-500"><i class="fa-solid fa-circle-info mr-1 text-blue-600"></i>En validant, vous certifiez l'exactitude des informations.</p>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="button" onclick="closeAmiableModal()"
                                class="flex-1 sm:flex-none h-10 px-4 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-[13px] font-semibold text-slate-700 transition">Annuler</button>
                            <button type="button" onclick="validateAmiableData()"
                                class="flex-1 sm:flex-none h-10 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition inline-flex items-center justify-center gap-2">
                                Valider le constat <i class="fa-solid fa-check text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Sélecteur de point de choc */
        .impact-selector {
            position: relative; width: 140px; height: 210px; margin: 0 auto;
            display: flex; align-items: center; justify-content: center;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1.5rem;
        }
        .car-shape { width: 50px; height: 100px; background: #cbd5e1; border-radius: 12px; position: relative; z-index: 1; }
        .car-shape::after { content: ''; position: absolute; top: 10px; left: 5px; right: 5px; height: 20px; background: #94a3b8; border-radius: 4px; }
        .impact-arrow {
            position: absolute; z-index: 10; width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 12px;
            color: #94a3b8; background: #fff; border: 1px solid #e2e8f0; cursor: pointer;
            transition: scale .15s, color .15s, background-color .15s, border-color .15s;
        }
        .impact-arrow:hover { color: #475569; border-color: #94a3b8; scale: 1.1; }
        .impact-arrow.active-a { color: #fff; background: #2563eb; border-color: #2563eb; scale: 1.15; }
        .impact-arrow.active-b { color: #fff; background: #f97316; border-color: #f97316; scale: 1.15; }
        .arrow-front { top: 6px; left: 50%; transform: translateX(-50%); }
        .arrow-back { bottom: 6px; left: 50%; transform: translateX(-50%); }
        .arrow-left { left: 6px; top: 50%; transform: translateY(-50%); }
        .arrow-right { right: 6px; top: 50%; transform: translateY(-50%); }
        .arrow-fl { top: 22px; left: 14px; }
        .arrow-fr { top: 22px; right: 14px; }
        .arrow-bl { bottom: 22px; left: 14px; }
        .arrow-br { bottom: 22px; right: 14px; }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fabric@5.3.0/dist/fabric.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnGeoloc = document.getElementById('btn-geoloc');
            const geolocStatus = document.getElementById('geoloc-status');
            const geolocAddress = document.getElementById('geoloc-address');
            const inputLat = document.getElementById('latitude');
            const inputLng = document.getElementById('longitude');
            const btnSubmit = document.getElementById('btn-submit');
            const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
            const photosContainer = document.getElementById('photos-container');
            const addPhotoBtn = document.getElementById('add-photo-btn');
            const photoCountSpan = document.getElementById('photo-count');

            let photoIndex = 0;
            let currentPhotoCount = 0;

            /** Échappe une valeur avant insertion dans du HTML. */
            const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            window.toggleAssisteurField = function(checkbox) {
                const field = document.getElementById('assisteur_field');
                if (!field) return;
                field.classList.toggle('hidden', !checkbox.checked);
            };

            /* ═════════ PHOTOS ═════════ */
            window.triggerPhotoUpload = function() {
                if (currentPhotoCount >= 5) {
                    Swal.fire({ icon: 'warning', title: 'Limite atteinte', text: 'Vous ne pouvez pas ajouter plus de 5 photos.', confirmButtonColor: '#2563eb' });
                    return;
                }

                // Un input file caché par photo
                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'photos[]';
                input.accept = 'image/*';
                input.capture = 'environment'; // appareil photo sur mobile
                input.className = 'hidden';
                input.id = `photo-input-${photoIndex}`;
                hiddenInputsContainer.appendChild(input);

                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const reader = new FileReader();
                        reader.onload = (e) => addPhotoPreview(e.target.result, file.name, input.id);
                        reader.readAsDataURL(file);
                    } else {
                        input.remove();
                    }
                });

                input.click();
            };

            function addPhotoPreview(imgSrc, fileName, inputId) {
                currentPhotoCount++;
                photoIndex++;
                photoCountSpan.innerText = currentPhotoCount;

                if (currentPhotoCount >= 5) {
                    addPhotoBtn.classList.add('hidden');
                    addPhotoBtn.classList.remove('flex');
                }

                const tile = document.createElement('div');
                tile.className = 'relative aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100 group';
                tile.id = `preview-${inputId}`;
                tile.title = fileName;

                const img = document.createElement('div');
                img.className = 'absolute inset-0 bg-cover bg-center';
                img.style.backgroundImage = `url('${imgSrc}')`;

                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.title = 'Retirer la photo';
                delBtn.className = 'absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-white/90 text-slate-600 hover:bg-red-600 hover:text-white flex items-center justify-center shadow-sm transition';
                delBtn.innerHTML = '<i class="fa-solid fa-xmark text-xs"></i>';
                delBtn.onclick = function() {
                    document.getElementById(inputId).remove();
                    tile.remove();
                    currentPhotoCount--;
                    photoCountSpan.innerText = currentPhotoCount;
                    addPhotoBtn.classList.remove('hidden');
                    addPhotoBtn.classList.add('flex');
                };

                tile.appendChild(img);
                tile.appendChild(delBtn);
                photosContainer.appendChild(tile);
            }

            /* ═════════ VÉHICULE, INCIDENT & CONSTAT ═════════ */
            const incidentCheckboxes = document.querySelectorAll('.incident-checkbox');
            const incidentError = document.getElementById('incident-error');
            const radioAmiable = document.getElementById('constat-amiable');
            const radioPolice = document.getElementById('constat-police');
            const labelAmiable = document.getElementById('label-constat-amiable');
            const labelPolice = radioPolice.closest('label');
            const corporelWarning = document.getElementById('corporel-warning');
            const brisGlaceInfo = document.getElementById('bris-glace-info');
            const contratIdInput = document.getElementById('contrat_id');
            const amiableDone = document.getElementById('amiable-done');
            const amiableModal = document.getElementById('constat-amiable-modal');
            const amiableDataInput = document.getElementById('amiable_data');
            let isSelectionInProgress = false;

            const userVehicles = @json($contrats);
            const preselectedContratId = @json($selectedContratId ?? request('contrat_id'));
            const vehiclePlate = (v) => v.plaque || v.immatriculation || '';

            window.selectVehicleById = async function(vehicleId, showSuccessToast = false) {
                const selectedVehicle = userVehicles.find(v => v.id == vehicleId);
                if (!selectedVehicle) return false;

                contratIdInput.value = vehicleId;

                document.getElementById('display-vehicle-name').textContent = `${selectedVehicle.marque} ${selectedVehicle.modele}`;
                document.getElementById('display-vehicle-plate').textContent = vehiclePlate(selectedVehicle);
                document.getElementById('selected-vehicle-display').classList.remove('hidden');
                document.getElementById('vehicle-empty').classList.add('hidden');

                // Assurance liée automatiquement au véhicule
                const badge = document.getElementById('assurance-linked-badge');
                const assuranceSelect = document.getElementById('assurance_id');
                if (selectedVehicle.assurance_id && assuranceSelect) {
                    assuranceSelect.value = selectedVehicle.assurance_id;
                    badge.classList.remove('hidden');
                    assuranceSelect.disabled = true;
                    assuranceSelect.classList.add('bg-slate-50', 'cursor-not-allowed', 'text-slate-500');
                } else {
                    badge.classList.add('hidden');
                    if (assuranceSelect) {
                        assuranceSelect.disabled = false;
                        assuranceSelect.classList.remove('bg-slate-50', 'cursor-not-allowed', 'text-slate-500');
                    }
                }

                if (showSuccessToast) {
                    await Swal.fire({
                        title: 'Véhicule sélectionné',
                        text: `${selectedVehicle.marque} ${selectedVehicle.modele}`,
                        icon: 'success',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }

                checkFormValidity();
                return true;
            };

            window.showVehicleSelection = async function() {
                if (isSelectionInProgress) return;
                isSelectionInProgress = true;

                try {
                    if (userVehicles.length === 0) {
                        const r = await Swal.fire({
                            title: 'Aucun véhicule disponible',
                            text: "Il faut une assurance active et vérifiée pour déclarer un sinistre.",
                            icon: 'warning',
                            confirmButtonText: 'Ajouter une assurance',
                            cancelButtonText: 'Fermer',
                            showCancelButton: true,
                            confirmButtonColor: '#2563eb'
                        });
                        if (r.isConfirmed) window.location.href = "{{ route('assure.contrats.create') }}";
                        return;
                    }

                    const vehicleHtml = `
                        <p class="text-sm text-slate-500 mb-4">Quel véhicule a subi l'incident ?</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-left">
                            ${userVehicles.map(v => `
                                <button type="button" class="vehicle-card relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white transition" data-id="${esc(v.id)}">
                                    <span class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-car-side"></i></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-[13px] font-semibold text-slate-900 truncate">${esc(v.marque)} ${esc(v.modele)}</span>
                                        <span class="block text-xs font-mono font-semibold text-slate-500 uppercase">${esc(vehiclePlate(v))}</span>
                                        ${v.assureur ? `<span class="block text-[11px] text-blue-700 truncate mt-0.5">${esc(v.assureur.name)}</span>` : ''}
                                    </span>
                                    <span class="select-indicator w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 scale-0 transition-transform"></span>
                                    </span>
                                </button>
                            `).join('')}
                        </div>`;

                    let selectedId = contratIdInput.value || null;

                    const markSelected = (cards) => cards.forEach(c => {
                        const on = c.getAttribute('data-id') == selectedId;
                        c.classList.toggle('border-blue-600', on);
                        c.classList.toggle('bg-blue-50', on);
                        c.classList.toggle('border-slate-200', !on);
                        c.querySelector('.select-indicator').classList.toggle('border-blue-600', on);
                        const dot = c.querySelector('.select-indicator span');
                        dot.classList.toggle('scale-100', on);
                        dot.classList.toggle('scale-0', !on);
                    });

                    const { value: result } = await Swal.fire({
                        title: 'Votre véhicule',
                        html: vehicleHtml,
                        showCancelButton: true,
                        cancelButtonText: 'Annuler',
                        confirmButtonText: 'Confirmer',
                        confirmButtonColor: '#2563eb',
                        allowOutsideClick: false,
                        width: '860px',
                        didRender: () => {
                            const confirmBtn = Swal.getConfirmButton();
                            confirmBtn.disabled = !selectedId;
                            const cards = Swal.getHtmlContainer().querySelectorAll('.vehicle-card');
                            markSelected(cards);
                            cards.forEach(card => card.addEventListener('click', () => {
                                selectedId = card.getAttribute('data-id');
                                markSelected(cards);
                                confirmBtn.disabled = false;
                            }));
                        },
                        preConfirm: () => {
                            if (!selectedId) {
                                Swal.showValidationMessage('Veuillez choisir un véhicule');
                                return false;
                            }
                            return selectedId;
                        }
                    });

                    if (result) await selectVehicleById(result, true);
                } finally {
                    isSelectionInProgress = false;
                }
            };

            if (preselectedContratId) {
                selectVehicleById(preselectedContratId, false);
            }

            function updateHospitalVisibility() {
                const corporelCheckbox = document.querySelector('input[name="type_sinistre[]"][value="Accident_corporel"]');
                const hospitalWrapper = document.getElementById('nearest-hospital-wrapper');
                if (hospitalWrapper) {
                    hospitalWrapper.classList.toggle('hidden', !(corporelCheckbox && corporelCheckbox.checked));
                }
            }

            function setCheck(id, ok) {
                const li = document.getElementById(id);
                if (!li) return;
                const dot = li.querySelector('[data-dot]');
                li.classList.toggle('text-slate-900', ok);
                li.classList.toggle('font-medium', ok);
                li.classList.toggle('text-slate-500', !ok);
                dot.className = 'w-5 h-5 rounded-full flex items-center justify-center text-[9px] text-white transition ' + (ok ? 'bg-emerald-500' : 'border border-slate-300');
                dot.innerHTML = ok ? '<i class="fa-solid fa-check"></i>' : '';
            }

            function checkFormValidity() {
                const isGeolocated = inputLat.value !== '' && inputLng.value !== '';
                const selectedIncidents = Array.from(incidentCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                const isIncidentSelected = selectedIncidents.length > 0;
                const isOnlyBrisDeGlace = selectedIncidents.length === 1 && selectedIncidents.includes('Bris_de_glace');
                const disabledCls = ['opacity-40', 'pointer-events-none', 'grayscale'];

                // Bris de glace seul : ni constat amiable ni autorités
                if (isOnlyBrisDeGlace) {
                    labelAmiable.classList.add(...disabledCls);
                    labelPolice.classList.add(...disabledCls);
                    radioAmiable.disabled = true;
                    radioPolice.disabled = true;
                    radioAmiable.checked = false;
                    radioPolice.checked = false;
                    radioAmiable.required = false;
                    brisGlaceInfo.classList.remove('hidden');
                } else {
                    labelPolice.classList.remove(...disabledCls);
                    radioPolice.disabled = false;
                    radioAmiable.required = true;
                    brisGlaceInfo.classList.add('hidden');

                    // Accident corporel : constat amiable interdit
                    if (selectedIncidents.includes('Accident_corporel')) {
                        labelAmiable.classList.add(...disabledCls);
                        radioAmiable.disabled = true;
                        radioAmiable.checked = false;
                        radioPolice.checked = true;
                        corporelWarning.classList.remove('hidden');
                    } else {
                        labelAmiable.classList.remove(...disabledCls);
                        radioAmiable.disabled = false;
                        corporelWarning.classList.add('hidden');
                    }
                }

                // Le badge « constat rempli » n'a de sens que si « Amiable » est coché
                amiableDone.classList.toggle('hidden', !(radioAmiable.checked && amiableDataInput.value));

                updateHospitalVisibility();

                // Sélection du véhicule proposée dès le premier incident coché
                if (isIncidentSelected && !contratIdInput.value) {
                    showVehicleSelection();
                }

                setCheck('check-geo', isGeolocated);
                setCheck('check-vehicle', !!contratIdInput.value);
                setCheck('check-incident', isIncidentSelected);

                if (isGeolocated && isIncidentSelected && contratIdInput.value) {
                    enableSubmitButton();
                    incidentError.classList.add('hidden');
                } else {
                    disableSubmitButton();
                    incidentError.classList.toggle('hidden', !(!isIncidentSelected && isGeolocated));
                }
            }

            incidentCheckboxes.forEach(cb => cb.addEventListener('change', checkFormValidity));
            radioPolice.addEventListener('change', checkFormValidity);

            /* ═════════ GÉOLOCALISATION & SERVICES PROCHES ═════════ */
            function sectionTitle(text, dot) {
                return `<p class="flex items-center gap-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-2"><span class="w-1.5 h-1.5 rounded-full ${dot}"></span>${text}</p>`;
            }

            function fetchNearestServices(lat, lng) {
                const listContainer = document.getElementById('nearest-services-list');
                listContainer.classList.remove('hidden');
                listContainer.innerHTML = sectionTitle('Postes à proximité', 'bg-blue-500 animate-pulse') + `
                    <div class="flex items-center justify-center gap-2 py-5 rounded-lg border border-dashed border-slate-200 text-xs text-slate-500">
                        <i class="fa-solid fa-circle-notch fa-spin text-blue-600"></i> Recherche des postes…
                    </div>`;

                fetch(`{{ route('assure.sinistres.services-proches') }}?latitude=${lat}&longitude=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.services.length > 0) {
                            let html = sectionTitle('Postes les plus proches', 'bg-emerald-500');

                            data.services.forEach(service => {
                                const dist = parseFloat(service.distance).toFixed(1);
                                const styles = {
                                    police:      { icon: 'fa-building-shield', tone: 'bg-blue-50 text-blue-600' },
                                    gendarmerie: { icon: 'fa-shield-halved',   tone: 'bg-emerald-50 text-emerald-600' },
                                };
                                const st = styles[service.role] || { icon: 'fa-user-shield', tone: 'bg-indigo-50 text-indigo-600' };
                                const roleLabel = service.role.charAt(0).toUpperCase() + service.role.slice(1);
                                const subtitle = service.role === 'agent' && service.service ? ` · ${esc(service.service.name)}` : '';

                                html += `
                                    <div class="flex items-center gap-3 p-2.5 rounded-lg border border-slate-200">
                                        <span class="w-9 h-9 rounded-lg ${st.tone} flex items-center justify-center text-sm shrink-0"><i class="fa-solid ${st.icon}"></i></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[13px] font-semibold text-slate-900 truncate">${esc(service.name)}</p>
                                            <p class="text-[11px] text-slate-500 truncate">${esc(roleLabel)}${subtitle}</p>
                                        </div>
                                        <span class="text-xs font-semibold text-blue-700 shrink-0">${dist} km</span>
                                    </div>`;
                            });

                            if (data.nearest_hospital) {
                                const h = data.nearest_hospital;
                                html += `
                                    <div id="nearest-hospital-wrapper" class="hidden pt-3">
                                        ${sectionTitle('Secours (sapeurs-pompiers)', 'bg-rose-500')}
                                        <div class="flex items-center gap-3 p-2.5 rounded-lg border border-rose-200 bg-rose-50/60">
                                            <span class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-sm shrink-0"><i class="fa-solid fa-truck-medical"></i></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[13px] font-semibold text-slate-900 truncate">${esc(h.name)}</p>
                                                <p class="text-[11px] text-rose-700">${esc(h.distance)} km</p>
                                            </div>
                                            <a href="tel:${esc(h.contact)}" class="h-8 px-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5 shrink-0 transition">
                                                <i class="fa-solid fa-phone text-[10px]"></i> Appeler
                                            </a>
                                        </div>
                                    </div>`;
                            }

                            listContainer.innerHTML = html;
                            updateHospitalVisibility();
                        } else {
                            listContainer.innerHTML = '<p class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800 text-center">Aucun poste trouvé dans cette zone.</p>';
                        }
                    })
                    .catch(err => {
                        console.error('Erreur AJAX services:', err);
                        listContainer.innerHTML = '<p class="p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 text-center">Erreur de connexion aux services.</p>';
                    });
            }

            function setGeolocButton(state, html) {
                const tones = {
                    loading: 'bg-blue-50 text-blue-700',
                    success: 'bg-emerald-50 text-emerald-700',
                    error:   'bg-red-50 text-red-700 cursor-pointer hover:bg-red-100',
                };
                btnGeoloc.className = 'flex items-center gap-2 h-10 px-3.5 rounded-lg text-[13px] font-semibold transition ' + tones[state];
                btnGeoloc.innerHTML = html;
            }

            function autoLocate() {
                btnGeoloc.onclick = null;
                setGeolocButton('loading', '<i class="fa-solid fa-circle-notch fa-spin"></i> Recherche de votre position…');

                if (!navigator.geolocation) {
                    showGeolocError('Géolocalisation non supportée par ce navigateur.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        inputLat.value = lat;
                        inputLng.value = lng;

                        // Adresse lisible (géocodage inverse)
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, { headers: { 'Accept-Language': 'fr' } })
                            .then(r => r.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    document.getElementById('lieu_input').value = data.display_name;
                                    geolocAddress.innerHTML = `<i class="fa-solid fa-location-dot text-blue-600 mr-1.5"></i>${esc(data.display_name)}`;
                                    geolocAddress.classList.remove('hidden');
                                }
                            })
                            .catch(err => console.error('Erreur geocoding:', err));

                        setGeolocButton('success', '<i class="fa-solid fa-circle-check"></i> Position acquise');
                        geolocStatus.className = 'mt-2 text-[11px] font-mono text-slate-500';
                        geolocStatus.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;

                        fetchNearestServices(lat, lng);
                        checkFormValidity();
                    },
                    function(error) {
                        let errorMsg = 'Erreur GPS.';
                        if (error.code === error.PERMISSION_DENIED) errorMsg = "Autorisation GPS refusée. Autorisez-la dans la barre d'adresse puis réessayez.";
                        if (error.code === error.POSITION_UNAVAILABLE) errorMsg = 'Signal GPS indisponible.';
                        if (error.code === error.TIMEOUT) errorMsg = "Délai d'attente GPS dépassé.";
                        showGeolocError(errorMsg);
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            }

            function showGeolocError(msg) {
                setGeolocButton('error', '<i class="fa-solid fa-rotate-right"></i> Réessayer');
                btnGeoloc.onclick = autoLocate;

                geolocStatus.className = 'mt-2 text-xs text-red-600';
                geolocStatus.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i>${esc(msg)}`;

                inputLat.value = '';
                inputLng.value = '';
                checkFormValidity();
            }

            /* ═════════ CONSTAT AMIABLE ═════════ */
            const currentUser = @json(['name' => $authUser->name, 'prenom' => $authUser->prenom]);

            let fabricSketch, sigAPad, sigBPad;

            const SKETCH_SYMBOLS = {
                auto: { scale: 0.75, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 64" width="40" height="64"><rect x="5" y="12" width="30" height="40" rx="6" fill="#64748b" stroke="#1e293b" stroke-width="1.5"/><rect x="9" y="15" width="22" height="10" rx="3" fill="#bfdbfe"/><rect x="9" y="40" width="22" height="8" rx="3" fill="#bfdbfe"/><rect x="1" y="15" width="5" height="9" rx="2" fill="#1e293b"/><rect x="34" y="15" width="5" height="9" rx="2" fill="#1e293b"/><rect x="1" y="38" width="5" height="9" rx="2" fill="#1e293b"/><rect x="34" y="38" width="5" height="9" rx="2" fill="#1e293b"/></svg>` },
                deux_roues: { scale: 0.75, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 64" width="22" height="64"><rect x="5" y="6" width="12" height="52" rx="5" fill="#475569" stroke="#1e293b" stroke-width="1.5"/><rect x="1" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="16" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="1" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="16" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/></svg>` },
                camion: { scale: 0.65, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 90" width="50" height="90"><rect x="4" y="4" width="42" height="20" rx="4" fill="#6b7280" stroke="#1e293b" stroke-width="1.5"/><rect x="8" y="7" width="34" height="11" rx="3" fill="#bfdbfe"/><rect x="3" y="28" width="44" height="58" rx="3" fill="#9ca3af" stroke="#1e293b" stroke-width="1.5"/><line x1="9" y1="57" x2="41" y2="57" stroke="#6b7280" stroke-width="1.5" stroke-dasharray="4,3"/><rect x="0" y="8" width="5" height="12" rx="2" fill="#1e293b"/><rect x="45" y="8" width="5" height="12" rx="2" fill="#1e293b"/><rect x="0" y="63" width="5" height="12" rx="2" fill="#1e293b"/><rect x="45" y="63" width="5" height="12" rx="2" fill="#1e293b"/></svg>` },
                pieton: { scale: 0.9, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 46" width="30" height="46"><circle cx="15" cy="7" r="6" fill="#e2e8f0" stroke="#1e293b" stroke-width="1.5"/><line x1="15" y1="13" x2="15" y2="30" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="21" x2="25" y2="21" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="30" x2="7" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="30" x2="23" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/></svg>` },
                feu: { scale: 0.9, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 58" width="24" height="58"><rect x="2" y="2" width="20" height="40" rx="4" fill="#1e293b" stroke="#374151" stroke-width="1"/><circle cx="12" cy="10" r="5" fill="#ef4444"/><circle cx="12" cy="22" r="5" fill="#f59e0b"/><circle cx="12" cy="34" r="5" fill="#22c55e"/><rect x="10" y="42" width="4" height="12" fill="#374151"/><rect x="4" y="52" width="16" height="4" rx="2" fill="#374151"/></svg>` },
                balise: { scale: 0.75, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="50" height="64"><polygon points="25,3 47,46 3,46" fill="white" stroke="#1e293b" stroke-width="2"/><polygon points="25,11 41,43 9,43" fill="none" stroke="#e11d48" stroke-width="1.5"/><line x1="25" y1="46" x2="25" y2="60" stroke="#374151" stroke-width="3" stroke-linecap="round"/><circle cx="25" cy="63" r="3" fill="#374151"/></svg>` },
                stop: { scale: 0.75, svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="50" height="64"><polygon points="15,2 35,2 48,15 48,35 35,48 15,48 2,35 2,15" fill="#dc2626" stroke="#1e293b" stroke-width="1.5"/><text x="25" y="31" text-anchor="middle" fill="white" font-size="12" font-family="Arial" font-weight="bold">STOP</text><line x1="25" y1="48" x2="25" y2="60" stroke="#374151" stroke-width="3" stroke-linecap="round"/><circle cx="25" cy="63" r="3" fill="#374151"/></svg>` }
            };

            const SKETCH_BTN_ON = 'h-7 px-2.5 rounded-md text-[11px] font-semibold bg-blue-600 text-white transition';
            const SKETCH_BTN_OFF = 'h-7 px-2.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition';
            document.getElementById('sketch-draw-btn').innerHTML = '<i class="fa-solid fa-pen mr-1"></i>Dessiner';
            document.getElementById('sketch-move-btn').innerHTML = '<i class="fa-solid fa-arrows-up-down-left-right mr-1"></i>Déplacer';

            window.addSketchSymbol = function(type, dropX, dropY) {
                if (!fabricSketch) return;
                const sym = SKETCH_SYMBOLS[type];
                if (!sym) return;
                fabric.loadSVGFromString(sym.svg, function(objects, options) {
                    const group = fabric.util.groupSVGElements(objects, options);
                    const cx = dropX !== undefined ? dropX : fabricSketch.width / 2;
                    const cy = dropY !== undefined ? dropY : fabricSketch.height / 2;
                    group.set({
                        left: cx - (group.width * sym.scale) / 2,
                        top: cy - (group.height * sym.scale) / 2,
                        scaleX: sym.scale,
                        scaleY: sym.scale,
                        hasControls: true,
                        hasBorders: true,
                        cornerSize: 8,
                        cornerColor: '#2563eb',
                        borderColor: '#2563eb',
                    });
                    fabricSketch.add(group);
                    fabricSketch.setActiveObject(group);
                    setSketchMode('select'); // pour positionner l'élément ajouté
                    fabricSketch.renderAll();
                    document.getElementById('sketch-hint').classList.add('hidden');
                });
            };

            window.setSketchMode = function(mode) {
                const draw = mode === 'draw';
                if (fabricSketch) fabricSketch.isDrawingMode = draw;
                document.getElementById('sketch-draw-btn').className = draw ? SKETCH_BTN_ON : SKETCH_BTN_OFF;
                document.getElementById('sketch-move-btn').className = draw ? SKETCH_BTN_OFF : SKETCH_BTN_ON;
            };
            setSketchMode('draw');

            function initPads() {
                const sigACanvas = document.getElementById('sig-a-pad');
                const sigBCanvas = document.getElementById('sig-b-pad');

                const resizeCanvas = (canvas) => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                };

                // Conserver les signatures déjà faites si on rouvre la modale
                const prevA = sigAPad && !sigAPad.isEmpty() ? sigAPad.toData() : null;
                const prevB = sigBPad && !sigBPad.isEmpty() ? sigBPad.toData() : null;

                resizeCanvas(sigACanvas);
                resizeCanvas(sigBCanvas);

                sigAPad = new SignaturePad(sigACanvas, { penColor: 'rgb(37, 99, 235)', minWidth: 1, maxWidth: 2 });
                sigBPad = new SignaturePad(sigBCanvas, { penColor: 'rgb(249, 115, 22)', minWidth: 1, maxWidth: 2 });
                if (prevA) sigAPad.fromData(prevA);
                if (prevB) sigBPad.fromData(prevB);

                // Croquis Fabric.js (conservé s'il existe déjà)
                const wrapper = document.getElementById('sketch-wrapper');
                if (fabricSketch) {
                    fabricSketch.setDimensions({ width: wrapper.clientWidth, height: wrapper.clientHeight });
                    return;
                }
                fabricSketch = new fabric.Canvas('sketch-pad', {
                    width: wrapper.clientWidth,
                    height: wrapper.clientHeight,
                    isDrawingMode: true,
                    backgroundColor: 'transparent',
                    selection: true,
                });
                fabricSketch.freeDrawingBrush.color = '#1e293b';
                fabricSketch.freeDrawingBrush.width = 2;
                fabricSketch.freeDrawingBrush.decimate = 4;
                fabricSketch.on('object:added', () => document.getElementById('sketch-hint').classList.add('hidden'));
                setSketchMode('draw');

                // Glisser-déposer depuis la palette
                wrapper.addEventListener('dragover', (e) => e.preventDefault());
                wrapper.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const sym = e.dataTransfer.getData('text/plain');
                    if (!sym) return;
                    const rect = wrapper.getBoundingClientRect();
                    addSketchSymbol(sym, e.clientX - rect.left, e.clientY - rect.top);
                });
                document.querySelectorAll('.sketch-sym-item').forEach(item => {
                    item.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', item.dataset.sym);
                        e.dataTransfer.effectAllowed = 'copy';
                    });
                });

                // Suppr / Retour arrière : effacer l'élément sélectionné (hors saisie de texte)
                document.addEventListener('keydown', (e) => {
                    if (!fabricSketch || amiableModal.classList.contains('hidden')) return;
                    if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;
                    if ((e.key === 'Delete' || e.key === 'Backspace') && !fabricSketch.isDrawingMode) {
                        const obj = fabricSketch.getActiveObject();
                        if (obj) {
                            fabricSketch.remove(obj);
                            fabricSketch.renderAll();
                        }
                    }
                });
            }

            window.clearSketch = () => {
                if (!fabricSketch) return;
                fabricSketch.clear();
                fabricSketch.backgroundColor = 'transparent';
                fabricSketch.renderAll();
                document.getElementById('sketch-hint').classList.remove('hidden');
            };
            window.clearSigA = () => sigAPad && sigAPad.clear();
            window.clearSigB = () => sigBPad && sigBPad.clear();

            window.openAmiableModal = function() {
                const vehicle = userVehicles.find(v => v.id == contratIdInput.value);
                const fullName = `${currentUser.name} ${currentUser.prenom || ''}`.trim();

                document.getElementById('amiable-a-name').textContent = fullName;
                document.getElementById('amiable-a-vehicle').textContent = vehicle
                    ? `${vehicle.marque} ${vehicle.modele} (${vehiclePlate(vehicle)})`
                    : 'Véhicule non sélectionné';
                const driver = document.getElementById('amiable-a-driver');
                if (!driver.value) driver.value = fullName;

                amiableModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(initPads, 100); // la modale doit être affichée avant de dimensionner les canvas
            };

            window.closeAmiableModal = function() {
                amiableModal.classList.add('hidden');
                document.body.style.overflow = '';
            };

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !amiableModal.classList.contains('hidden')) closeAmiableModal();
            });

            window.validateAmiableData = function() {
                const data = {
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
                    croquis: (fabricSketch && fabricSketch.getObjects().length > 0) ? fabricSketch.toDataURL({ format: 'png', multiplier: 1 }) : null,
                    signature_a: sigAPad && !sigAPad.isEmpty() ? sigAPad.toDataURL() : null,
                    signature_b: sigBPad && !sigBPad.isEmpty() ? sigBPad.toDataURL() : null
                };

                for (let i = 1; i <= {{ count($circonstances) }}; i++) {
                    if (document.getElementById(`circ-a-${i}`).checked) data.partie_a.circonstances.push(i);
                    if (document.getElementById(`circ-b-${i}`).checked) data.partie_b.circonstances.push(i);
                }

                if (!data.partie_b.nom || !data.partie_b.vehicule) {
                    Swal.fire({ icon: 'error', title: 'Informations manquantes', text: 'Renseignez au moins le nom et le véhicule de la partie B.', confirmButtonColor: '#2563eb' });
                    return;
                }

                amiableDataInput.value = JSON.stringify(data);
                amiableDone.classList.remove('hidden');

                Swal.fire({ icon: 'success', title: 'Constat validé', text: 'Le constat est rattaché à votre déclaration.', timer: 1800, showConfirmButton: false });
                closeAmiableModal();
            };

            radioAmiable.addEventListener('change', function() {
                if (!this.checked) return;
                if (!contratIdInput.value) {
                    Swal.fire({ icon: 'warning', title: 'Véhicule requis', text: "Sélectionnez d'abord votre véhicule.", confirmButtonColor: '#2563eb' });
                    this.checked = false;
                    radioPolice.checked = false;
                    return;
                }
                openAmiableModal();
                checkFormValidity();
            });

            // Points de choc
            ['a', 'b'].forEach(side => {
                const arrows = document.querySelectorAll(`#impact-${side} .impact-arrow`);
                arrows.forEach(arrow => arrow.addEventListener('click', function() {
                    arrows.forEach(a => a.classList.remove(`active-${side}`));
                    this.classList.add(`active-${side}`);
                    document.getElementById(`amiable-${side}-impact`).value = this.dataset.point;
                }));
            });

            /* ═════════ ENVOI ═════════ */
            const form = document.getElementById('sinistre-form');
            form.addEventListener('submit', function(e) {
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }
                form.dataset.submitting = 'true';

                // Un select désactivé n'est pas envoyé : on le réactive
                const assuranceSelect = document.getElementById('assurance_id');
                if (assuranceSelect) assuranceSelect.disabled = false;

                btnSubmit.setAttribute('disabled', 'disabled');
                btnSubmit.className = 'mt-5 w-full h-11 rounded-lg bg-slate-400 text-white text-sm font-semibold flex items-center justify-center gap-2 cursor-not-allowed';
                btnSubmit.innerHTML = '<span>Transmission en cours…</span><i class="fa-solid fa-circle-notch fa-spin text-xs"></i>';
            });

            function enableSubmitButton() {
                btnSubmit.removeAttribute('disabled');
                btnSubmit.className = 'mt-5 w-full h-11 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold flex items-center justify-center gap-2 transition';
            }

            function disableSubmitButton() {
                btnSubmit.setAttribute('disabled', 'disabled');
                btnSubmit.className = 'mt-5 w-full h-11 rounded-lg bg-slate-100 text-slate-400 text-sm font-semibold flex items-center justify-center gap-2 cursor-not-allowed transition';
            }

            autoLocate();
            checkFormValidity();
        });
    </script>
@endpush
