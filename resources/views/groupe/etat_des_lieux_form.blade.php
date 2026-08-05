@extends('groupe.layouts.app')

@section('title', 'État des lieux — Intervention')
@section('page-title', 'État des lieux')

@push('styles')
    <style>
        .etat-form input[type='text'],
        .etat-form input[type='datetime-local'],
        .etat-form input[type='time'],
        .etat-form input[type='number'],
        .etat-form textarea,
        .etat-form select {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 0.85rem;
            background: #ffffff;
            color: #0f172a;
            padding: 0.75rem 0.9rem;
            font-size: 0.88rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .etat-form input[type='text']:focus,
        .etat-form input[type='datetime-local']:focus,
        .etat-form input[type='time']:focus,
        .etat-form input[type='number']:focus,
        .etat-form textarea:focus,
        .etat-form select:focus {
            outline: none;
            border-color: #f43f5e;
            box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.12);
        }

        /* Choice Chip Buttons (Radio & Checkbox Cards) */
        .choice-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.75rem 1rem;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.9rem;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }

        .choice-card:hover {
            border-color: #be123c;
            background: #fff1f2;
            transform: translateY(-1px);
        }

        .choice-card input[type="radio"],
        .choice-card input[type="checkbox"] {
            appearance: none;
            width: 1.15rem;
            height: 1.15rem;
            border: 2px solid #cbd5e1;
            border-radius: 0.35rem;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
            display: grid;
            place-content: center;
        }

        .choice-card input[type="radio"] {
            border-radius: 9999px;
        }

        .choice-card input[type="radio"]:checked,
        .choice-card input[type="checkbox"]:checked {
            background-color: #be123c;
            border-color: #be123c;
        }

        .choice-card input[type="radio"]:checked::before {
            content: "";
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 9999px;
            background-color: #ffffff;
        }

        .choice-card input[type="checkbox"]:checked::before {
            content: "✓";
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .choice-card.is-selected {
            border-color: #be123c;
            background: #fff1f2;
            box-shadow: 0 4px 12px rgba(190, 18, 60, 0.08);
        }

        /* Floating Action Bar */
        .floating-footer {
            position: sticky;
            bottom: 1rem;
            z-index: 40;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid #e2e8f0;
            border-radius: 1.1rem;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.12);
        }

        /* Step Wizard Bar */
        .step-pill {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            min-width: 180px;
        }

        .step-pill:hover {
            border-color: #f43f5e;
            color: #be123c;
            background: #fff1f2;
        }

        .step-pill.active {
            background: linear-gradient(135deg, #e11d48, #be123c);
            color: #ffffff;
            border-color: #be123c;
            box-shadow: 0 4px 15px rgba(190, 18, 60, 0.2);
        }

        .step-pill.completed {
            border-color: #10b981;
            background: #ecfdf5;
            color: #047857;
        }
    </style>
@endpush

@section('content')
@php
    $record = $etatDesLieux;
    $checkedArr = fn($field, $val) => in_array($val, old($field, $record?->$field ?? []), true);
    $jsonVictimes = json_encode($record?->victimes ?? []);
    $jsonVehicules = json_encode($record?->vehicules_impliques ?? []);
    $jsonTemoins = json_encode($record?->temoins ?? []);
    $jsonChronologie = json_encode($record?->chronologie ?? []);
    $initialHeureFin = $record?->heure_fin_intervention ?? '';
    $initialMaterielStr = json_encode($record?->materiel_utilise ?? '');
@endphp

<div class="max-w-6xl mx-auto space-y-6 pb-8" x-data="etatFormHandler({{ $jsonVictimes }}, {{ $jsonVehicules }}, {{ $jsonTemoins }}, {{ $jsonChronologie }}, '{{ $initialHeureFin }}', {{ $initialMaterielStr }})" x-init="initClock()">
    <!-- Hero Header -->
    <div class="bg-gradient-to-br from-rose-700 via-rose-800 to-rose-900 p-6 md:p-8 rounded-3xl text-white shadow-xl shadow-rose-950/20 border border-rose-600/30">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full border border-white/20 text-xs font-semibold text-white">
                    <i class="fa-solid fa-fire-extinguisher"></i> Espace Sapeurs-Pompiers
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">État des Lieux de l'Intervention</h1>
                <p class="text-rose-100 text-sm max-w-xl">Complétez le rapport officiel structuré en 4 étapes simples.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
                <div class="px-3 py-2 bg-white/15 rounded-2xl border border-white/20 backdrop-blur-md">
                    <span class="text-rose-200 block">N° Sinistre</span>
                    <span class="font-bold text-white text-sm">{{ $sinistre->numero_sinistre ?? $sinistre->reference ?? '#' . $sinistre->id }}</span>
                </div>
                <div class="px-3 py-2 bg-white/15 rounded-2xl border border-white/20 backdrop-blur-md">
                    <span class="text-rose-200 block">Lieu Déclaré</span>
                    <span class="font-bold text-white text-sm">{{ $sinistre->lieu ?? 'Non précisé' }}</span>
                </div>

                @if($record)
                    <a href="{{ route('groupe.sinistres.etat_des_lieux.pdf', $sinistre) }}"
                        class="px-4 py-3 bg-white hover:bg-rose-50 text-rose-800 rounded-2xl font-extrabold shadow-lg transition-all inline-flex items-center gap-2 text-xs border border-white/40">
                        <i class="fa-solid fa-file-pdf text-rose-600 text-base"></i> Télécharger le Rapport PDF
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i> Veuillez corriger les erreurs ci-dessous :
            </div>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Barre d'Étapes Stepper (4 Steps) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <button type="button" @click="setStep(1)" :class="{'active': step === 1, 'completed': step > 1}" class="step-pill">
            <span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center font-bold text-xs" x-html="step > 1 ? '<i class=\'fa-solid fa-check\'></i>' : '1'"></span>
            <div class="text-left">
                <span class="block text-[10px] uppercase opacity-75">Étape 1</span>
                <span>Contexte & Sinistre</span>
            </div>
        </button>

        <button type="button" @click="setStep(2)" :class="{'active': step === 2, 'completed': step > 2}" class="step-pill">
            <span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center font-bold text-xs" x-html="step > 2 ? '<i class=\'fa-solid fa-check\'></i>' : '2'"></span>
            <div class="text-left">
                <span class="block text-[10px] uppercase opacity-75">Étape 2</span>
                <span>Victimes & Dégâts</span>
            </div>
        </button>

        <button type="button" @click="setStep(3)" :class="{'active': step === 3, 'completed': step > 3}" class="step-pill">
            <span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center font-bold text-xs" x-html="step > 3 ? '<i class=\'fa-solid fa-check\'></i>' : '3'"></span>
            <div class="text-left">
                <span class="block text-[10px] uppercase opacity-75">Étape 3</span>
                <span>Moyens & Actions</span>
            </div>
        </button>

        <button type="button" @click="setStep(4)" :class="{'active': step === 4}" class="step-pill">
            <span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center font-bold text-xs">4</span>
            <div class="text-left">
                <span class="block text-[10px] uppercase opacity-75">Étape 4</span>
                <span>Chronologie & Bilan</span>
            </div>
        </button>
    </div>

    <form action="{{ route('groupe.sinistres.etat_des_lieux.store', $sinistre) }}" method="POST" class="space-y-8 etat-form">
        @csrf

        <!-- ==================== ÉTAPE 1 : CONTEXTE & SINISTRE ==================== -->
        <div x-show="step === 1" x-transition.opacity class="space-y-8">
            <!-- SECTION 1 : INFORMATIONS GÉNÉRALES -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold">1</div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">1. Informations Générales</h2>
                            <p class="text-xs text-slate-500">Horaires et localisation exacte de l'intervention.</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-auto min-w-[260px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nature de l'intervention <span class="text-rose-500">*</span></label>
                        <select name="nature_intervention" required class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 bg-slate-50 py-2.5">
                            <option value="">Sélectionner la nature</option>
                            @foreach([
                                'Incendie' => '🔥 Incendie',
                                'Accident de circulation' => '🚗 Accident de circulation',
                                'Malaise' => '🩺 Malaise',
                                'Sauvetage' => '🛟 Sauvetage',
                                'Inondation' => '🌊 Inondation',
                                'Fuite de gaz' => '⚠️ Fuite de gaz',
                                'Effondrement' => '🏢 Effondrement',
                                'Intervention avec matières dangereuses' => '☣️ Matières dangereuses',
                                'Autre' => '⚡ Autre'
                            ] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('nature_intervention', $record->nature_intervention ?? '') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Numéro de l'intervention</label>
                        <input type="text" name="numero_intervention" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('numero_intervention', $record->numero_intervention ?? $sinistre->numero_sinistre ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Date & Heure de l'alerte</label>
                        <input type="datetime-local" name="date_heure_alerte" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('date_heure_alerte', optional($record->date_heure_alerte ?? null)->format('Y-m-d\TH:i') ?? $sinistre->created_at->format('Y-m-d\TH:i')) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Heure de départ de la caserne</label>
                        <input type="time" name="heure_depart_caserne" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('heure_depart_caserne', $record->heure_depart_caserne ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Heure d'arrivée sur les lieux</label>
                        <input type="time" name="heure_arrivee_lieux" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('heure_arrivee_lieux', $record->heure_arrivee_lieux ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Heure de fin d'intervention</label>
                        <input type="time" name="heure_fin_intervention" x-model="nowTime" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('heure_fin_intervention', $record->heure_fin_intervention ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lieu exact (Adresse / GPS)</label>
                        <input type="text" name="lieu_exact" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('lieu_exact', $record->lieu_exact ?? $sinistre->lieu ?? '') }}" />
                    </div>
                </div>
            </section>

            <!-- SECTION 2 : INFORMATIONS SUR LE SINISTRE -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">2</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">2. Informations sur le Sinistre</h2>
                        <p class="text-xs text-slate-500">Description globale, niveau de gravité et risques.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Description de la situation <span class="text-rose-500">*</span></label>
                        <textarea name="description_situation" rows="4" required placeholder="Décrivez l'état de la situation à votre arrivée...">{{ old('description_situation', $record->description_situation ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cause présumée (si connue) <span class="text-rose-500">*</span></label>
                        <input type="text" name="cause_presumee" required placeholder="Ex: Court-circuit, vitesse excessive..."
                            value="{{ old('cause_presumee', $record->cause_presumee ?? '') }}" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Niveau de gravité <span class="text-rose-500">*</span></label>
                        <select name="niveau_gravite" required>
                            <option value="">Sélectionner</option>
                            <option value="Faible" @selected(old('niveau_gravite', $record->niveau_gravite ?? '') === 'Faible')>🟢 Faible</option>
                            <option value="Moyen" @selected(old('niveau_gravite', $record->niveau_gravite ?? '') === 'Moyen')>🟡 Moyen</option>
                            <option value="Élevé" @selected(old('niveau_gravite', $record->niveau_gravite ?? '') === 'Élevé')>🟠 Élevé</option>
                            <option value="Critique" @selected(old('niveau_gravite', $record->niveau_gravite ?? '') === 'Critique')>🔴 Critique</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Conditions météorologiques <span class="text-rose-500">*</span></label>
                        <input type="text" name="conditions_meteo" required placeholder="Ex: Pluie battante, forte chaleur, vent..."
                            value="{{ old('conditions_meteo', $record->conditions_meteo ?? '') }}" />
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Risques identifiés</label>
                        <textarea name="risques_identifies" rows="2" placeholder="Ex: Risque d'effondrement, propagation du feu, produit toxique...">{{ old('risques_identifies', $record->risques_identifies ?? '') }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <!-- ==================== ÉTAPE 2 : VICTIMES & DÉGÂTS ==================== -->
        <div x-show="step === 2" x-transition.opacity class="space-y-8">
            <!-- SECTION 3 : VICTIMES (REPEATER DYNAMIQUE) -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold">3</div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">3. Victimes</h2>
                            <p class="text-xs text-slate-500">Bilan individuel de chaque victime prise en charge.</p>
                        </div>
                    </div>

                    <button type="button" @click="addVictime()" class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5 border border-purple-200">
                        <i class="fa-solid fa-plus"></i> Ajouter une victime
                    </button>
                </div>

                <template x-if="victimes.length === 0">
                    <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 text-xs">
                        <i class="fa-solid fa-user-slash text-2xl mb-2 block text-slate-300"></i>
                        Aucune victime enregistrée. Cliquez sur "Ajouter une victime" si nécessaire.
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(v, index) in victimes" :key="index">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 relative space-y-4 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="font-bold text-slate-800 text-xs uppercase" x-text="'Victime #' + (index + 1)"></span>
                                <button type="button" @click="removeVictime(index)" class="text-rose-600 hover:text-rose-800 text-xs font-bold inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nom (si connu)</label>
                                    <input type="text" :name="'victimes['+index+'][nom]'" x-model="v.nom" placeholder="Nom & Prénom" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Sexe <span class="text-rose-500">*</span></label>
                                    <select :name="'victimes['+index+'][sexe]'" x-model="v.sexe" required>
                                        <option value="">Sélectionner</option>
                                        <option value="Homme">Homme</option>
                                        <option value="Femme">Femme</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Âge approximatif</label>
                                    <input type="text" :name="'victimes['+index+'][age]'" x-model="v.age" placeholder="Ex: 35 ans" />
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Niveau de conscience <span class="text-rose-500">*</span></label>
                                    <select :name="'victimes['+index+'][niveau_conscience]'" x-model="v.niveau_conscience" required>
                                        <option value="">Sélectionner</option>
                                        <option value="Conscient">Conscient</option>
                                        <option value="Inconscient">Inconscient</option>
                                        <option value="Comateux">Comateux</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Décédée ou non <span class="text-rose-500">*</span></label>
                                    <select :name="'victimes['+index+'][decedee]'" x-model="v.decedee" required>
                                        <option value="Non">Non</option>
                                        <option value="Oui">Oui (Décès)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Moyen de transport <span class="text-rose-500">*</span></label>
                                    <select :name="'victimes['+index+'][moyen_transport]'" x-model="v.moyen_transport" required @change="if(v.moyen_transport === 'Sur place (Non évacué)') { v.evacuation_hopital = 'Aucune évacuation / Soigné sur place'; }">
                                        <option value="">Sélectionner</option>
                                        <option value="Ambulance / VSAV">Ambulance / VSAV</option>
                                        <option value="Hélicoptère">Hélicoptère</option>
                                        <option value="Véhicule personnel">Véhicule personnel</option>
                                        <option value="Sur place (Non évacué)">Sur place (Non évacué)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Évacuation vers quel hôpital <span class="text-rose-500">*</span></label>
                                    <select :name="'victimes['+index+'][evacuation_hopital]'" x-model="v.evacuation_hopital" required
                                        :disabled="v.moyen_transport === 'Sur place (Non évacué)'"
                                        :class="v.moyen_transport === 'Sur place (Non évacué)' ? '!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold' : ''">
                                        <option value="">Sélectionner l'hôpital (Google Maps & Proximité)</option>
                                        <option value="Aucune évacuation / Soigné sur place">Aucune évacuation / Soigné sur place</option>
                                        @foreach($hospitals as $h)
                                            <option value="{{ $h['name'] }}">{{ $h['label'] }}</option>
                                        @endforeach
                                        <option value="Autre établissement de santé">Autre établissement de santé</option>
                                    </select>
                                    <template x-if="v.moyen_transport === 'Sur place (Non évacué)'">
                                        <input type="hidden" :name="'victimes['+index+'][evacuation_hopital]'" value="Aucune évacuation / Soigné sur place" />
                                    </template>
                                </div>

                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block font-semibold text-slate-700 mb-1">Blessures observées (Ajouter plusieurs si nécessaire)</label>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="v.blessureInput" @keydown.enter.prevent="addBlessureToVictime(v)" placeholder="Tapez une blessure et appuyez sur Entrée ou Ajouter..." class="text-xs flex-1" />
                                        <button type="button" @click="addBlessureToVictime(v)" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shrink-0 shadow-sm transition-colors">
                                            <i class="fa-solid fa-plus"></i> Ajouter
                                        </button>
                                    </div>
                                    <input type="hidden" :name="'victimes['+index+'][blessures]'" :value="v.blessures || (v.blessuresList ? v.blessuresList.join(', ') : '')" />
                                    
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        <template x-for="(b, bIndex) in (v.blessuresList || (v.blessures ? v.blessures.split(',').map(s => s.trim()).filter(Boolean) : []))" :key="bIndex">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-700 rounded-lg text-xs font-semibold border border-rose-200">
                                                <i class="fa-solid fa-bandage text-rose-500 text-[10px]"></i>
                                                <span x-text="b"></span>
                                                <button type="button" @click="removeBlessureFromVictime(v, bIndex)" class="text-rose-500 hover:text-rose-900 font-bold ml-1 text-xs">
                                                    &times;
                                                </button>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- SECTION 4 : VÉHICULES IMPLIQUÉS (REPEATER DYNAMIQUE) -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">4</div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">4. Véhicules Impliqués (Accidents)</h2>
                            <p class="text-xs text-slate-500">Caractéristiques et état des véhicules accidentés.</p>
                        </div>
                    </div>

                    <button type="button" @click="addVehicule()" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5 border border-indigo-200">
                        <i class="fa-solid fa-plus"></i> Ajouter un véhicule
                    </button>
                </div>

                <template x-if="vehicules.length === 0">
                    <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 text-xs">
                        <i class="fa-solid fa-car-rear text-2xl mb-2 block text-slate-300"></i>
                        Aucun véhicule enregistré. Cliquez sur "Ajouter un véhicule" si applicable.
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(veh, index) in vehicules" :key="index">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 relative space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="font-bold text-slate-800 text-xs uppercase" x-text="'Véhicule #' + (index + 1)"></span>
                                <button type="button" @click="removeVehicule(index)" class="text-rose-600 hover:text-rose-800 text-xs font-bold inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Type de véhicule</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][type_vehicule]'" x-model="veh.type_vehicule" placeholder="Ex: Voiture, Camion, Moto" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Immatriculation</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][immatriculation]'" x-model="veh.immatriculation" placeholder="Plaque d'immatriculation" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Marque</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][marque]'" x-model="veh.marque" placeholder="Ex: Toyota, Peugeot" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Couleur</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][couleur]'" x-model="veh.couleur" placeholder="Ex: Noir, Blanc" />
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Conducteur identifié</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][conducteur_identifie]'" x-model="veh.conducteur_identifie" placeholder="Nom du conducteur" />
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nombre de passagers</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][nombre_passagers]'" x-model="veh.nombre_passagers" placeholder="Ex: 3" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">État du véhicule</label>
                                    <input type="text" :name="'vehicules_impliques['+index+'][etat_vehicule]'" x-model="veh.etat_vehicule" placeholder="Ex: Pare-chocs détruit, tonneau..." />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- SECTION 5 : DÉGÂTS MATÉRIELS -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg font-bold">5</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">5. Dégâts Matériels</h2>
                        <p class="text-xs text-slate-500">Biens endommagés, surface brûlée et biens sauvés.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Biens endommagés</label>
                        <textarea name="biens_endommages" rows="3" placeholder="Équipements, mobilier, véhicules touchés...">{{ old('biens_endommages', $record->biens_endommages ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bâtiments touchés</label>
                        <textarea name="batiments_touches" rows="3" placeholder="Immeuble, maison, magasin, entrepôt...">{{ old('batiments_touches', $record->batiments_touches ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Surface brûlée (pour incendie)</label>
                        <input type="text" name="surface_brulee" placeholder="Ex: 150 m²"
                            value="{{ old('surface_brulee', $record->surface_brulee ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Estimation des dégâts</label>
                        <input type="text" name="estimation_degats" placeholder="Ex: Importance des dommages (Faible, Partiel, Total)"
                            value="{{ old('estimation_degats', $record->estimation_degats ?? '') }}" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Biens sauvés</label>
                        <textarea name="biens_sauves" rows="2" placeholder="Partie du bâtiment ou biens matériels préservés...">{{ old('biens_sauves', $record->biens_sauves ?? '') }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <!-- ==================== ÉTAPE 3 : MOYENS & ACTIONS ==================== -->
        <div x-show="step === 3" x-transition.opacity class="space-y-8">
            <!-- SECTION 6 : MOYENS ENGAGÉS -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg font-bold">6</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">6. Moyens Engagés</h2>
                        <p class="text-xs text-slate-500">Groupe, effectifs, matériels et produits extincteurs.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Groupe d'intervention mobilisé</label>
                        <input type="text" name="casernes_mobilisees" readonly class="!bg-slate-100 !text-slate-500 cursor-not-allowed border-slate-200 font-bold"
                            value="{{ old('casernes_mobilisees', $record->casernes_mobilisees ?? $sinistre->assignedGroupe->name ?? $user->name ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre de pompiers mobilisés</label>
                        <input type="text" name="nombre_pompiers" placeholder="Ex: 12 agents"
                            value="{{ old('nombre_pompiers', $record->nombre_pompiers ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Quantité d'eau utilisée</label>
                        <input type="text" name="quantite_eau_utilisee" placeholder="Ex: 5000 Litres"
                            value="{{ old('quantite_eau_utilisee', $record->quantite_eau_utilisee ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Produits extincteurs utilisés</label>
                        <input type="text" name="produits_extincteurs_utilises" placeholder="Ex: Mousse émulseur, CO2..."
                            value="{{ old('produits_extincteurs_utilises', $record->produits_extincteurs_utilises ?? '') }}" />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Matériel spécialisé utilisé & Quantités</label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <input type="text" x-model="materielNomInput" @keydown.enter.prevent="addMateriel()" placeholder="Nom du matériel (Ex: Écarteur hydraulique, Échelle 30m)..." class="text-xs flex-1 min-w-0" />
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-xs text-slate-500 font-semibold sm:hidden">Qté :</span>
                                <input type="number" min="1" x-model="materielNombreInput" @keydown.enter.prevent="addMateriel()" placeholder="Qté" class="text-xs w-full sm:w-24 text-center font-bold" />
                            </div>
                            <button type="button" @click="addMateriel()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shrink-0 shadow-sm transition-colors flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-plus"></i> Ajouter matériel
                            </button>
                        </div>
                        <input type="hidden" name="materiel_utilise" :value="materielUtiliseStr" />

                        <div class="flex flex-wrap gap-2 pt-1">
                            <template x-for="(m, mIndex) in materielList" :key="mIndex">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-800 rounded-xl text-xs font-semibold border border-slate-200 shadow-sm">
                                    <i class="fa-solid fa-toolbox text-slate-500"></i>
                                    <span x-text="m.nom"></span>
                                    <span class="px-1.5 py-0.5 bg-rose-600 text-white rounded-md text-[10px] font-extrabold" x-text="'x' + m.nombre"></span>
                                    <button type="button" @click="removeMateriel(mIndex)" class="text-slate-400 hover:text-rose-600 font-bold ml-1 text-sm">
                                        &times;
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 7 : ACTIONS RÉALISÉES -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">7</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">7. Actions Réalisées</h2>
                        <p class="text-xs text-slate-500">Cochez toutes les opérations effectuées sur le terrain.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs font-medium">
                    @foreach([
                        'Extinction' => '🧯 Extinction',
                        'Désincarcération' => '🛠️ Désincarcération',
                        'Premiers secours' => '🩺 Premiers secours',
                        'Évacuation' => '🏃 Évacuation',
                        'Balisage' => '🚧 Balisage',
                        'Sécurisation des lieux' => '🛡️ Sécurisation des lieux',
                        'Ventilation' => '💨 Ventilation',
                        'Déblai' => '🧹 Déblai',
                        'Nettoyage de la chaussée' => '🧼 Nettoyage de la chaussée'
                    ] as $val => $lbl)
                        <label class="choice-card @if($checkedArr('actions_realisees', $val)) is-selected @endif">
                            <input type="checkbox" name="actions_realisees[]" value="{{ $val }}" @checked($checkedArr('actions_realisees', $val))>
                            <span>{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 8 : AUTORITÉS PRÉSENTES -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg font-bold">8</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">8. Autorités Présentes</h2>
                        <p class="text-xs text-slate-500">Services de secours et forces de l'ordre sur les lieux.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs font-medium">
                    @foreach([
                        'Police' => '👮 Police',
                        'Gendarmerie' => '🚓 Gendarmerie',
                        'SAMU ou services médicaux' => '🚑 SAMU / Services médicaux',
                        'Protection civile' => '🛡️ Protection civile',
                        'Autorités administratives' => '🏛️ Autorités administratives'
                    ] as $val => $lbl)
                        <label class="choice-card @if($checkedArr('autorites_presentes', $val)) is-selected @endif">
                            <input type="checkbox" name="autorites_presentes[]" value="{{ $val }}" @checked($checkedArr('autorites_presentes', $val))>
                            <span>{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 9 : TÉMOINS (REPEATER DYNAMIQUE) -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-lg font-bold">9</div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">9. Témoins</h2>
                            <p class="text-xs text-slate-500">Identités et déclarations succinctes des témoins.</p>
                        </div>
                    </div>

                    <button type="button" @click="addTemoin()" class="px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5 border border-yellow-200">
                        <i class="fa-solid fa-plus"></i> Ajouter un témoin
                    </button>
                </div>

                <template x-if="temoins.length === 0">
                    <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 text-xs">
                        <i class="fa-solid fa-user-tag text-2xl mb-2 block text-slate-300"></i>
                        Aucun témoin renseigné. Cliquez sur "Ajouter un témoin" si nécessaire.
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(t, index) in temoins" :key="index">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 relative space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="font-bold text-slate-800 text-xs uppercase" x-text="'Témoin #' + (index + 1)"></span>
                                <button type="button" @click="removeTemoin(index)" class="text-rose-600 hover:text-rose-800 text-xs font-bold inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nom & Prénom</label>
                                    <input type="text" :name="'temoins['+index+'][nom]'" x-model="t.nom" placeholder="Nom complet du témoin" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Contact (Téléphone / Adresse)</label>
                                    <input type="text" :name="'temoins['+index+'][contact]'" x-model="t.contact" placeholder="Numéro ou ville" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Déclaration succincte</label>
                                    <textarea :name="'temoins['+index+'][declaration]'" x-model="t.declaration" rows="2" placeholder="Résumé des propos ou déclarations du témoin..."></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>

        <!-- ==================== ÉTAPE 4 : CHRONOLOGIE & CONCLUSION ==================== -->
        <div x-show="step === 4" x-transition.opacity class="space-y-8">
            <!-- SECTION 10 : CHRONOLOGIE (REPEATER DYNAMIQUE) -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg font-bold">10</div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">10. Chronologie des Actions</h2>
                            <p class="text-xs text-slate-500">Heure de chaque action importante et étapes clés.</p>
                        </div>
                    </div>

                    <button type="button" @click="addChronologie()" class="px-4 py-2 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5 border border-cyan-200">
                        <i class="fa-solid fa-plus"></i> Ajouter une étape
                    </button>
                </div>

                <template x-if="chronologie.length === 0">
                    <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 text-xs">
                        <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 block text-slate-300"></i>
                        Aucune étape chronologique ajoutée.
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(c, index) in chronologie" :key="index">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 relative space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="font-bold text-slate-800 text-xs uppercase" x-text="'Étape #' + (index + 1)"></span>
                                <button type="button" @click="removeChronologie(index)" class="text-rose-600 hover:text-rose-800 text-xs font-bold inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Heure</label>
                                    <input type="time" :name="'chronologie['+index+'][heure]'" x-model="c.heure" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Évènement clef</label>
                                    <input type="text" :name="'chronologie['+index+'][evenement]'" x-model="c.evenement" placeholder="Ex: Arrivée renforts, Feu maîtrisé..." />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Description / Observations</label>
                                    <input type="text" :name="'chronologie['+index+'][description]'" x-model="c.description" placeholder="Détails supplémentaires" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- SECTION 11 : CONCLUSION -->
            <section class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold">11</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">11. Conclusion & Bilan</h2>
                        <p class="text-xs text-slate-500">Bilan final, cause probable et suites à donner.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Situation maîtrisée</label>
                        <div class="flex gap-3 text-xs font-medium">
                            <label class="choice-card flex-1 @if(old('situation_maitrisee', $record->situation_maitrisee ?? '') === 'Oui') is-selected @endif">
                                <input type="radio" name="situation_maitrisee" value="Oui" @checked(old('situation_maitrisee', $record->situation_maitrisee ?? '') === 'Oui')>
                                <span>Oui (Situation maîtrisée)</span>
                            </label>
                            <label class="choice-card flex-1 @if(old('situation_maitrisee', $record->situation_maitrisee ?? '') === 'Non') is-selected @endif">
                                <input type="radio" name="situation_maitrisee" value="Non" @checked(old('situation_maitrisee', $record->situation_maitrisee ?? '') === 'Non')>
                                <span>Non (Mesures en cours)</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cause probable</label>
                        <input type="text" name="cause_probable" placeholder="Synthèse de la cause retenue"
                            value="{{ old('cause_probable', $record->cause_probable ?? '') }}" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Recommandations</label>
                        <textarea name="recommandations" rows="3" placeholder="Recommandations de sécurité ou prévention...">{{ old('recommandations', $record->recommandations ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Suites à donner</label>
                        <textarea name="suites_a_donner" rows="3" placeholder="Procédure judiciaire, enquête, surveillance de zone...">{{ old('suites_a_donner', $record->suites_a_donner ?? '') }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <!-- Barre d'Actions Flottante (Wizard Navigation) -->
        <div class="floating-footer p-4 flex items-center justify-between gap-4">
            <div>
                <button type="button" @click="prevStep()" x-show="step > 1" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition-colors inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Étape précédente
                </button>

                <a href="{{ route('groupe.interventions') }}" x-show="step === 1" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i> Annuler / Retour
                </a>
            </div>

            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                Étape <span x-text="step" class="text-rose-600"></span> sur 4
            </div>

            <div>
                <button type="button" @click="nextStep()" x-show="step < 4" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold shadow-md transition-all inline-flex items-center gap-2">
                    Étape suivante <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <button type="submit" x-show="step === 4" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white text-sm font-bold shadow-lg shadow-rose-600/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Enregistrer l'état des lieux
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        function parseMaterielStr(str) {
            if (!str) return [];
            return str.split(',').map(item => {
                item = item.trim();
                const match = item.match(/^(.+?)\s*\(\s*x\s*(\d+)\s*\)$/i);
                if (match) {
                    return { nom: match[1].trim(), nombre: parseInt(match[2]) || 1 };
                }
                return { nom: item, nombre: 1 };
            }).filter(m => m.nom);
        }

        function etatFormHandler(initVictimes, initVehicules, initTemoins, initChronologie, initialHeureFin, initialMaterielStr) {
            return {
                step: 1,
                victimes: Array.isArray(initVictimes) ? initVictimes.map(v => ({
                    ...v,
                    blessureInput: '',
                    blessuresList: v.blessures ? v.blessures.split(',').map(s => s.trim()).filter(Boolean) : []
                })) : [],
                vehicules: Array.isArray(initVehicules) ? initVehicules : [],
                temoins: Array.isArray(initTemoins) ? initTemoins : [],
                chronologie: Array.isArray(initChronologie) ? initChronologie : [],
                nowTime: initialHeureFin || '',

                materielNomInput: '',
                materielNombreInput: '1',
                materielList: parseMaterielStr(initialMaterielStr || ''),
                materielUtiliseStr: initialMaterielStr || '',

                addMateriel() {
                    if (!this.materielNomInput || !this.materielNomInput.trim()) return;
                    const qty = parseInt(this.materielNombreInput) || 1;
                    this.materielList.push({ nom: this.materielNomInput.trim(), nombre: qty });
                    this.materielNomInput = '';
                    this.materielNombreInput = '1';
                    this.syncMaterielUtilise();
                },
                removeMateriel(mIndex) {
                    this.materielList.splice(mIndex, 1);
                    this.syncMaterielUtilise();
                },
                syncMaterielUtilise() {
                    this.materielUtiliseStr = this.materielList.map(m => `${m.nom} (x${m.nombre})`).join(', ');
                },

                initClock() {
                    if (!initialHeureFin) {
                        const updateClock = () => {
                            const now = new Date();
                            const hours = String(now.getHours()).padStart(2, '0');
                            const minutes = String(now.getMinutes()).padStart(2, '0');
                            this.nowTime = `${hours}:${minutes}`;
                        };
                        updateClock();
                        setInterval(updateClock, 1000);
                    }
                },

                validateCurrentStep() {
                    const stepContainer = document.querySelector(`.etat-form > [x-show="step === ${this.step}"]`);
                    if (!stepContainer) return true;

                    const reqInputs = stepContainer.querySelectorAll('[required]');
                    for (let input of reqInputs) {
                        if (!input.value || !input.value.trim()) {
                            input.reportValidity();
                            input.focus();
                            return false;
                        }
                    }
                    return true;
                },

                setStep(s) {
                    if (s > this.step && !this.validateCurrentStep()) {
                        return;
                    }
                    this.step = s;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                nextStep() {
                    if (!this.validateCurrentStep()) {
                        return;
                    }
                    if (this.step < 4) {
                        this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                addVictime() {
                    this.victimes.push({
                        nom: '',
                        sexe: '',
                        age: '',
                        etat: '',
                        blessures: '',
                        blessureInput: '',
                        blessuresList: [],
                        niveau_conscience: 'Conscient',
                        decedee: 'Non',
                        evacuation_hopital: '',
                        moyen_transport: 'Ambulance / VSAV'
                    });
                },
                removeVictime(index) {
                    this.victimes.splice(index, 1);
                },

                addBlessureToVictime(v) {
                    if (!v.blessureInput || !v.blessureInput.trim()) return;
                    if (!Array.isArray(v.blessuresList)) {
                        v.blessuresList = v.blessures ? v.blessures.split(',').map(s => s.trim()).filter(Boolean) : [];
                    }
                    v.blessuresList.push(v.blessureInput.trim());
                    v.blessures = v.blessuresList.join(', ');
                    v.blessureInput = '';
                },
                removeBlessureFromVictime(v, bIndex) {
                    if (!Array.isArray(v.blessuresList)) return;
                    v.blessuresList.splice(bIndex, 1);
                    v.blessures = v.blessuresList.join(', ');
                },

                addVehicule() {
                    this.vehicules.push({ type_vehicule: '', immatriculation: '', marque: '', couleur: '', conducteur_identifie: '', nombre_passagers: '', etat_vehicule: '' });
                },
                removeVehicule(index) {
                    this.vehicules.splice(index, 1);
                },

                addTemoin() {
                    this.temoins.push({ nom: '', contact: '', declaration: '' });
                },
                removeTemoin(index) {
                    this.temoins.splice(index, 1);
                },

                addChronologie() {
                    this.chronologie.push({ heure: '', evenement: '', description: '' });
                },
                removeChronologie(index) {
                    this.chronologie.splice(index, 1);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.choice-card input').forEach(input => {
                input.addEventListener('change', function() {
                    const card = this.closest('.choice-card');
                    if (this.type === 'radio') {
                        const name = this.name;
                        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                            r.closest('.choice-card')?.classList.remove('is-selected');
                        });
                    }
                    if (this.checked) {
                        card?.classList.add('is-selected');
                    } else {
                        card?.classList.remove('is-selected');
                    }
                });
            });
        });
    </script>
@endpush
