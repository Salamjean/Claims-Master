@extends('assure.layouts.template')

@php
    $isRenew = isset($contratExistant) && $contratExistant;

    $inputClass = 'w-full h-11 px-3.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10';
    $lockedClass = 'w-full h-11 px-3.5 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium text-slate-500 outline-none cursor-not-allowed';
    $vehicleInput = $isRenew ? $lockedClass : $inputClass;
    $labelClass = 'block text-[13px] font-semibold text-slate-700 mb-1.5';

    $documents = [
        ['name' => 'attestation_assurance', 'label' => "Attestation d'assurance", 'hint' => 'Format ASACI', 'icon' => 'fa-file-shield', 'required' => true, 'ai' => true],
        ['name' => 'carte_grise', 'label' => 'Carte grise', 'hint' => 'Recto lisible', 'icon' => 'fa-id-card', 'required' => !$isRenew, 'ai' => true],
        ['name' => 'visite_technique', 'label' => 'Visite technique', 'hint' => 'Attestation en cours de validité', 'icon' => 'fa-clipboard-check', 'required' => !$isRenew, 'ai' => true],
        ['name' => 'permis_conduire', 'label' => 'Permis de conduire', 'hint' => 'Recto lisible', 'icon' => 'fa-id-badge', 'required' => !$isRenew, 'ai' => true],
    ];

    $docErrors = collect($documents)->pluck('name')->contains(fn ($n) => $errors->has($n));
@endphp

@section('title', $isRenew ? 'Renouveler une assurance' : 'Ajouter une assurance')
@section('page-title', $isRenew ? 'Renouvellement Assurance' : 'Nouvelle Assurance')

@section('content')
    <div class="w-full pb-16"
        x-data="{
            currentStep: 1,
            totalSteps: 3,
            steps: [
                { title: 'Véhicule', desc: 'Identification' },
                { title: 'Contrat', desc: 'N° de police' },
                { title: 'Documents', desc: 'Pièces justificatives' }
            ],
            goTo(step) {
                for (let s = 1; s < step; s++) {
                    if (!this.validateStep(s)) { this.currentStep = s; return; }
                }
                this.currentStep = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            nextStep() { this.goTo(Math.min(this.currentStep + 1, this.totalSteps)); },
            prevStep() {
                this.currentStep = Math.max(this.currentStep - 1, 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            validateStep(step) {
                const ids = step === 1
                    ? ['plaque', 'marque', 'modele', 'type_vehicule']
                    : (step === 2 ? ['numero_contrat'] : []);
                for (const id of ids) {
                    const el = document.getElementById(id);
                    if (el && !el.checkValidity()) {
                        this.currentStep = step;
                        this.$nextTick(() => el.reportValidity());
                        return false;
                    }
                }
                return true;
            },
            init() {
                @if($docErrors || $errors->has('immatriculation'))
                    this.currentStep = 3;
                @elseif($errors->has('numero_contrat'))
                    this.currentStep = 2;
                @endif
            }
        }">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('assure.contrats.index') }}"
                    class="inline-flex items-center gap-2 text-[13px] font-medium text-slate-500 hover:text-slate-900 transition mb-3">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Mes assurances
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $isRenew ? "Renouveler l'assurance" : 'Ajouter une assurance' }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    @if($isRenew)
                        Véhicule <span class="font-semibold text-slate-700">{{ $contratExistant->plaque }}</span>
                        · {{ $contratExistant->marque }} {{ $contratExistant->modele }}
                    @else
                        Renseignez votre véhicule, votre contrat puis vos justificatifs.
                    @endif
                </p>
            </div>

            <span class="inline-flex items-center gap-2 self-start sm:self-auto px-3 py-1.5 rounded-full bg-slate-100 text-xs font-medium text-slate-600">
                <i class="fa-solid fa-shield-halved text-blue-600"></i>
                Vérifié par IA
            </span>
        </div>

        {{-- Pré-remplissage IA --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900">Gagnez du temps avec le pré-remplissage</p>
                    <p class="text-[13px] text-slate-500">Téléversez votre attestation : l'IA remplit le formulaire et joint le document automatiquement.</p>
                </div>
                <label for="ai_attestation_uploader"
                    class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-[13px] font-semibold cursor-pointer transition shrink-0">
                    <i id="ai_uploader_icon" class="fa-solid fa-arrow-up-from-bracket text-xs"></i>
                    <span id="ai_uploader_btn_text">Téléverser l'attestation</span>
                </label>
                <input type="file" id="ai_attestation_uploader" accept="image/*,application/pdf" class="hidden" onchange="handleAIAttestationUpload(this)">
            </div>
            <p id="ai_scan_status" class="hidden mt-4 pt-4 border-t border-slate-100 text-[13px]"></p>
        </div>

        {{-- Étapes --}}
        <nav class="mb-6">
            <ol class="grid grid-cols-3 gap-3">
                <template x-for="(step, index) in steps" :key="index">
                    <li>
                        <button type="button" @click="goTo(index + 1)" class="w-full text-left group">
                            <div class="h-1 rounded-full mb-3 transition-colors"
                                :class="currentStep >= index + 1 ? 'bg-blue-600' : 'bg-slate-200'"></div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 transition-colors"
                                    :class="currentStep > index + 1 ? 'bg-blue-600 text-white' : (currentStep === index + 1 ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-600' : 'bg-slate-100 text-slate-400')">
                                    <i x-show="currentStep > index + 1" class="fa-solid fa-check text-[10px]"></i>
                                    <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold truncate"
                                        :class="currentStep >= index + 1 ? 'text-slate-900' : 'text-slate-400'" x-text="step.title"></p>
                                    <p class="text-xs text-slate-400 truncate hidden sm:block" x-text="step.desc"></p>
                                </div>
                            </div>
                        </button>
                    </li>
                </template>
            </ol>
        </nav>

        {{-- Formulaire --}}
        <form action="{{ route('assure.contrats.store') }}" method="POST" enctype="multipart/form-data" id="contractForm" novalidate
            class="bg-white border border-slate-200 rounded-2xl">
            @csrf

            @if($isRenew)
                <input type="hidden" name="renouveler_id" value="{{ $contratExistant->id }}">
            @endif

            {{-- ÉTAPE 1 : VÉHICULE --}}
            <section x-show="currentStep === 1" class="p-6 md:p-8">
                <header class="mb-6">
                    <h2 class="text-base font-semibold text-slate-900">Informations du véhicule</h2>
                    <p class="text-[13px] text-slate-500 mt-0.5">Telles qu'elles figurent sur la carte grise.</p>
                </header>

                @if($isRenew)
                    <div class="flex items-start gap-3 p-3.5 rounded-lg bg-amber-50 border border-amber-200 text-[13px] text-amber-900 mb-6">
                        <i class="fa-solid fa-lock text-amber-600 mt-0.5"></i>
                        <span>Mode renouvellement : les informations du véhicule restent identiques. Seuls le n° de police, l'assureur et l'échéance seront mis à jour.</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-5 gap-y-5">
                    {{-- Plaque --}}
                    <div class="md:col-span-3">
                        <label for="plaque" class="{{ $labelClass }}">Plaque d'immatriculation <span class="text-red-500">*</span></label>
                        <input type="text" name="plaque" id="plaque"
                            value="{{ old('plaque', $contratExistant->plaque ?? '') }}"
                            placeholder="Ex : AA-665-TQ-01 ou 1234 AB 01"
                            required @readonly($isRenew)
                            class="{{ $vehicleInput }} uppercase tracking-wide">
                        @error('plaque') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Marque --}}
                    <div>
                        <label for="marque" class="{{ $labelClass }}">Marque <span class="text-red-500">*</span></label>
                        <input type="text" name="marque" id="marque"
                            value="{{ old('marque', $contratExistant->marque ?? '') }}"
                            placeholder="Ex : Toyota, Ford…"
                            required @readonly($isRenew)
                            class="{{ $vehicleInput }}">
                        @error('marque') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Modèle --}}
                    <div>
                        <label for="modele" class="{{ $labelClass }}">Modèle <span class="text-red-500">*</span></label>
                        <input type="text" name="modele" id="modele"
                            value="{{ old('modele', $contratExistant->modele ?? '') }}"
                            placeholder="Ex : Corolla, Tucson…"
                            required @readonly($isRenew)
                            class="{{ $vehicleInput }}">
                        @error('modele') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type de véhicule --}}
                    <div>
                        <label for="type_vehicule" class="{{ $labelClass }}">Type de véhicule <span class="text-red-500">*</span></label>
                        @php $typeValue = old('type_vehicule', $contratExistant->type_vehicule ?? ''); @endphp
                        <div class="relative">
                            <select name="type_vehicule" id="type_vehicule" required @disabled($isRenew)
                                class="{{ $vehicleInput }} appearance-none pr-10">
                                <option value="" disabled @selected($typeValue === '')>Sélectionnez une catégorie</option>
                                @foreach(['Berline' => 'Berline', 'SUV' => 'SUV / 4x4', 'Citadine' => 'Citadine', 'Camionnette' => 'Camionnette / Utilitaire', 'Coupé' => 'Coupé / Cabriolet'] as $value => $text)
                                    <option value="{{ $value }}" @selected($typeValue == $value)>{{ $text }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                        @if($isRenew)
                            <input type="hidden" name="type_vehicule" value="{{ $contratExistant->type_vehicule }}">
                        @endif
                        @error('type_vehicule') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- ÉTAPE 2 : CONTRAT --}}
            <section x-show="currentStep === 2" x-cloak class="p-6 md:p-8">
                <header class="mb-6">
                    <h2 class="text-base font-semibold text-slate-900">Contrat d'assurance</h2>
                    <p class="text-[13px] text-slate-500 mt-0.5">Le numéro figure sur votre attestation ou votre police.</p>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    <div>
                        <label for="numero_contrat" class="{{ $labelClass }}">Numéro de police / contrat <span class="text-red-500">*</span></label>
                        <input type="text" name="numero_contrat" id="numero_contrat"
                            value="{{ old('numero_contrat', $contratExistant->numero_contrat ?? '') }}"
                            placeholder="Ex : POL-998877"
                            required
                            class="{{ $inputClass }} uppercase">
                        @error('numero_contrat') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <ul class="divide-y divide-slate-100 border border-slate-200 rounded-xl">
                        <li class="flex gap-3 p-4">
                            <i class="fa-solid fa-building-shield text-slate-400 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-[13px] font-semibold text-slate-800">Assureur détecté automatiquement</p>
                                <p class="text-xs text-slate-500 mt-0.5">Inutile de choisir votre compagnie : elle est lue sur l'attestation.</p>
                            </div>
                        </li>
                        <li class="flex gap-3 p-4">
                            <i class="fa-regular fa-calendar text-slate-400 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-[13px] font-semibold text-slate-800">Échéance extraite par l'IA</p>
                                <p class="text-xs text-slate-500 mt-0.5">La date de fin de validité est récupérée lors de l'enregistrement.</p>
                            </div>
                        </li>
                        <li class="flex gap-3 p-4">
                            <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-[13px] font-semibold text-slate-800">Attestation lisible au format ASACI</p>
                                <p class="text-xs text-slate-500 mt-0.5">Une photo nette et complète garantit une validation rapide.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            {{-- ÉTAPE 3 : DOCUMENTS --}}
            <section x-show="currentStep === 3" x-cloak class="p-6 md:p-8">
                <header class="grid grid-cols-1 lg:grid-cols-[1fr_minmax(0,360px)_1fr] items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Pièces justificatives</h2>
                        <p class="text-[13px] text-slate-500 mt-0.5">PDF, JPG ou PNG · 5 Mo maximum par fichier.</p>
                    </div>

                    {{-- N° de carte grise : lu automatiquement sur la carte grise --}}
                    <div class="w-full">
                        <div class="flex items-center gap-2">
                            <label for="immatriculation" class="text-xs font-semibold text-slate-600 whitespace-nowrap">N° de carte grise <span class="text-red-500">*</span></label>
                            <div class="relative flex-1 min-w-0">
                                <i id="cg_ai_icon" class="fa-solid fa-lock text-[11px] text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" name="immatriculation" id="immatriculation"
                                    value="{{ old('immatriculation', $contratExistant->immatriculation ?? '') }}"
                                    data-original="{{ $contratExistant->immatriculation ?? '' }}"
                                    readonly tabindex="-1" placeholder="Rempli automatiquement"
                                    class="w-full h-10 pl-9 pr-3 rounded-lg border border-slate-200 bg-slate-100 text-[13px] font-semibold font-mono uppercase text-slate-700 placeholder:font-sans placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 cursor-not-allowed outline-none">
                            </div>
                        </div>
                        <p id="cg_ai_status" class="text-xs mt-1.5 text-slate-400 lg:text-center">Le numéro est lu automatiquement sur la carte grise.</p>
                        @error('immatriculation') <p class="text-red-600 text-xs mt-1.5 lg:text-center">{{ $message }}</p> @enderror
                    </div>

                    <p id="docs_counter" class="text-xs font-medium text-slate-500 lg:text-right"></p>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    @foreach($documents as $doc)
                        <div class="doc-card" data-doc="{{ $doc['name'] }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[13px] font-semibold text-slate-700">
                                    {{ $doc['label'] }}
                                    @if($doc['required'])<span class="text-red-500">*</span>@else<span class="text-xs font-normal text-slate-400">(optionnel)</span>@endif
                                </span>
                                @if($doc['ai'])
                                    <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">Analysé par IA</span>
                                @endif
                            </div>

                            <input id="{{ $doc['name'] }}" name="{{ $doc['name'] }}" type="file"
                                accept="image/*,application/pdf" @required($doc['required'])
                                class="sr-only file-input-preview">

                            {{-- État vide --}}
                            <label for="{{ $doc['name'] }}" data-empty
                                class="flex flex-col items-center justify-center h-40 rounded-xl border border-dashed cursor-pointer transition text-center px-4
                                {{ $errors->has($doc['name']) ? 'border-red-300 bg-red-50/40' : 'border-slate-300 hover:border-blue-500 hover:bg-blue-50/40' }}">
                                <span class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center mb-2.5">
                                    <i class="fa-solid {{ $doc['icon'] }}"></i>
                                </span>
                                <span class="text-[13px] font-medium text-slate-700">
                                    <span class="text-blue-600">Choisir un fichier</span> ou glisser ici
                                </span>
                                <span class="text-xs text-slate-400 mt-0.5">{{ $doc['hint'] }}</span>
                            </label>

                            {{-- État rempli --}}
                            <div data-filled class="hidden h-40 rounded-xl border border-slate-200 overflow-hidden flex-col">
                                <div class="flex-1 bg-slate-50 flex items-center justify-center overflow-hidden" data-thumb></div>
                                <div class="flex items-center gap-2 px-3 py-2 border-t border-slate-200 bg-white">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                    <span class="text-xs font-medium text-slate-700 truncate flex-1" data-name></span>
                                    <label for="{{ $doc['name'] }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 cursor-pointer">Changer</label>
                                    <button type="button" data-remove class="text-slate-400 hover:text-red-600 transition" title="Retirer">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <p data-error class="text-red-600 text-xs mt-1.5 {{ $errors->has($doc['name']) ? '' : 'hidden' }}">{{ $errors->first($doc['name']) }}</p>
                            @if($doc['name'] === 'visite_technique')
                                <p id="vt_ai_status" class="text-xs mt-1.5 text-slate-400">L'IA vérifie que c'est bien une visite technique du véhicule.</p>
                            @endif
                            @if($doc['name'] === 'permis_conduire')
                                <p id="pc_ai_status" class="text-xs mt-1.5 text-slate-400">L'IA vérifie que c'est bien un permis de conduire.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Navigation --}}
            <footer class="flex items-center justify-between gap-3 px-6 md:px-8 py-4 border-t border-slate-200 bg-slate-50/60 rounded-b-2xl">
                <button type="button" x-show="currentStep > 1" @click="prevStep()"
                    class="h-10 px-4 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-[13px] font-semibold transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Précédent
                </button>
                <span x-show="currentStep === 1" class="text-xs text-slate-400">Étape <span x-text="currentStep"></span> sur 3</span>

                <div class="flex items-center gap-3 ml-auto">
                    <span x-show="currentStep > 1" class="hidden sm:inline text-xs text-slate-400">Étape <span x-text="currentStep"></span> sur 3</span>

                    <button type="button" x-show="currentStep < 3" @click="nextStep()"
                        class="h-10 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition inline-flex items-center gap-2">
                        Continuer
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>

                    <button type="submit" id="submitBtn" x-show="currentStep === 3" x-cloak
                        class="h-10 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition inline-flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-check text-xs"></i>
                        {{ $isRenew ? 'Valider le renouvellement' : "Enregistrer l'assurance" }}
                    </button>
                </div>
            </footer>
        </form>
    </div>

    @push('styles')
        <style>[x-cloak] { display: none !important; }</style>
    @endpush

    @push('scripts')
        <script>
            const CSRF_TOKEN = '{{ csrf_token() }}';
            const MAX_FILE_SIZE = 5 * 1024 * 1024;

            /** Affiche un message d'état (icône + texte) dans un élément. */
            function setStatus(el, tone, icon, text) {
                const tones = { info: 'text-blue-600', success: 'text-emerald-600', error: 'text-red-600' };
                el.className = el.dataset.baseClass + ' flex items-start gap-2 ' + (tones[tone] || '');
                el.textContent = '';
                const i = document.createElement('i');
                i.className = icon + ' mt-0.5';
                el.appendChild(i);
                const span = document.createElement('span');
                span.textContent = text;
                el.appendChild(span);
            }

            /** Attache un fichier à un input de l'étape 3 et rafraîchit l'aperçu. */
            function attachFileTo(inputId, file) {
                const input = document.getElementById(inputId);
                if (!input) return;
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function selectTypeVehicule(value) {
                const select = document.getElementById('type_vehicule');
                if (!select || !value) return false;
                for (const opt of select.options) {
                    if (opt.value && opt.value.toLowerCase() === value.toLowerCase()) {
                        opt.selected = true;
                        return true;
                    }
                }
                return false;
            }

            function postScan(url, field, file, extra = {}) {
                const formData = new FormData();
                formData.append(field, file);
                Object.entries(extra).forEach(([k, v]) => formData.append(k, v));
                formData.append('_token', CSRF_TOKEN);
                return fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: formData
                }).then(r => r.json());
            }

            /* ---------- Scan IA de la carte grise (étape 3) ---------- */
            const VEHICLE_FIELDS = [
                ['plaque', "la plaque d'immatriculation"],
                ['marque', 'la marque'],
                ['modele', 'le modèle'],
                ['type_vehicule', 'le type de véhicule'],
            ];
            const CG_DEFAULT_HINT = 'Le numéro est lu automatiquement sur la carte grise.';
            let cgScanToken = 0;        // ignore les réponses d'un fichier remplacé entre-temps
            let cgScanPending = false;
            let cgSilentClear = false;  // vidage après refus : conserver le message d'erreur

            function clearCarteGriseFile(input) {
                cgSilentClear = true;
                input.value = '';
                input.dispatchEvent(new Event('change'));
            }

            function scanCarteGrise(input) {
                const file = input.files && input.files[0];
                const field = document.getElementById('immatriculation');
                const icon = document.getElementById('cg_ai_icon');
                const status = document.getElementById('cg_ai_status');
                status.dataset.baseClass = 'text-xs mt-1.5';
                const token = ++cgScanToken;

                // Toujours repartir du numéro d'origine (vide, ou celui du contrat en renouvellement)
                field.value = field.dataset.original || '';
                icon.className = 'fa-solid fa-lock text-[11px] text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2';
                cgScanPending = false;

                if (!file) {
                    if (!cgSilentClear) {
                        status.className = 'text-xs mt-1.5 text-slate-400';
                        status.textContent = CG_DEFAULT_HINT;
                    }
                    cgSilentClear = false;
                    return;
                }

                const missing = VEHICLE_FIELDS.filter(([id]) => !document.getElementById(id).value.trim());
                if (missing.length) {
                    setStatus(status, 'error', 'fa-solid fa-circle-info',
                        "Renseignez d'abord " + missing.map(([, label]) => label).join(', ') + " à l'étape 1.");
                    clearCarteGriseFile(input);
                    return;
                }

                const plaqueSaisie = document.getElementById('plaque').value.trim();
                cgScanPending = true;
                icon.className = 'fa-solid fa-circle-notch animate-spin text-[11px] text-blue-600 absolute left-3.5 top-1/2 -translate-y-1/2';
                setStatus(status, 'info', 'fa-solid fa-robot', "Lecture de la carte grise et vérification de l'immatriculation…");

                postScan('{{ route("assure.contrats.scan-carte-grise-ai") }}', 'carte_grise', file, { plaque: plaqueSaisie })
                    .then(res => {
                        if (token !== cgScanToken) return;
                        if (res.success && res.data && res.data.numero_carte_grise) {
                            field.value = res.data.numero_carte_grise;
                            icon.className = 'fa-solid fa-circle-check text-[11px] text-emerald-500 absolute left-3.5 top-1/2 -translate-y-1/2';
                            setStatus(status, 'success', 'fa-solid fa-circle-check',
                                'Immatriculation ' + (res.data.plaque || plaqueSaisie) + ' confirmée.');
                        } else {
                            const msg = res.message || "Le document téléversé n'est pas une carte grise.";
                            setStatus(status, 'error', 'fa-solid fa-circle-xmark', msg);
                            clearCarteGriseFile(input);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: res.invalid || res.mismatch ? 'error' : 'warning',
                                    title: res.mismatch ? 'Immatriculation différente' : (res.invalid ? "Ce n'est pas une carte grise" : 'Lecture impossible'),
                                    text: msg,
                                    confirmButtonText: 'Compris',
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        }
                    })
                    .catch(err => {
                        if (token !== cgScanToken) return;
                        console.error('Erreur IA carte grise :', err);
                        setStatus(status, 'error', 'fa-solid fa-circle-exclamation', 'Analyse indisponible pour le moment. Réessayez dans quelques instants.');
                        clearCarteGriseFile(input);
                    })
                    .finally(() => {
                        if (token !== cgScanToken) return;
                        cgScanPending = false;
                    });
            }

            /* ---------- Scan IA de la visite technique (étape 3) ---------- */
            const VT_DEFAULT_HINT = "L'IA vérifie que c'est bien une visite technique du véhicule.";
            let vtScanToken = 0;
            let vtScanPending = false;
            let vtSilentClear = false;

            function clearVisiteTechniqueFile(input) {
                vtSilentClear = true;
                input.value = '';
                input.dispatchEvent(new Event('change'));
            }

            function scanVisiteTechnique(input) {
                const file = input.files && input.files[0];
                const status = document.getElementById('vt_ai_status');
                status.dataset.baseClass = 'text-xs mt-1.5';
                const token = ++vtScanToken;
                vtScanPending = false;

                if (!file) {
                    if (!vtSilentClear) {
                        status.className = 'text-xs mt-1.5 text-slate-400';
                        status.textContent = VT_DEFAULT_HINT;
                    }
                    vtSilentClear = false;
                    return;
                }

                const missing = VEHICLE_FIELDS.filter(([id]) => !document.getElementById(id).value.trim());
                if (missing.length) {
                    setStatus(status, 'error', 'fa-solid fa-circle-info',
                        "Renseignez d'abord " + missing.map(([, label]) => label).join(', ') + " à l'étape 1.");
                    clearVisiteTechniqueFile(input);
                    return;
                }

                const plaqueSaisie = document.getElementById('plaque').value.trim();
                vtScanPending = true;
                setStatus(status, 'info', 'fa-solid fa-circle-notch animate-spin', 'Vérification de la visite technique…');

                postScan('{{ route("assure.contrats.scan-visite-technique-ai") }}', 'visite_technique', file, { plaque: plaqueSaisie })
                    .then(res => {
                        if (token !== vtScanToken) return;
                        if (res.success && res.data) {
                            const d = res.data;
                            const fin = d.date_expiration ? new Date(d.date_expiration + 'T00:00:00').toLocaleDateString('fr-FR') : null;
                            if (d.expiree) {
                                status.dataset.baseClass = 'text-xs mt-1.5 !text-amber-600';
                                setStatus(status, 'info', 'fa-solid fa-triangle-exclamation',
                                    'Visite technique vérifiée, mais expirée' + (fin ? ' depuis le ' + fin : '') + '.');
                            } else {
                                setStatus(status, 'success', 'fa-solid fa-circle-check',
                                    'Visite technique vérifiée' + (fin ? ' · valide jusqu\'au ' + fin : '') + '.');
                            }
                        } else {
                            const msg = res.message || "Le document téléversé n'est pas une visite technique.";
                            setStatus(status, 'error', 'fa-solid fa-circle-xmark', msg);
                            clearVisiteTechniqueFile(input);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: res.invalid || res.mismatch ? 'error' : 'warning',
                                    title: res.mismatch ? 'Immatriculation différente' : (res.invalid ? "Ce n'est pas une visite technique" : 'Analyse impossible'),
                                    text: msg,
                                    confirmButtonText: 'Compris',
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        }
                    })
                    .catch(err => {
                        if (token !== vtScanToken) return;
                        console.error('Erreur IA visite technique :', err);
                        setStatus(status, 'error', 'fa-solid fa-circle-exclamation', 'Analyse indisponible pour le moment. Réessayez dans quelques instants.');
                        clearVisiteTechniqueFile(input);
                    })
                    .finally(() => {
                        if (token === vtScanToken) vtScanPending = false;
                    });
            }

            /* ---------- Scan IA du permis de conduire (étape 3) ---------- */
            const PC_DEFAULT_HINT = "L'IA vérifie que c'est bien un permis de conduire.";
            let pcScanToken = 0;
            let pcScanPending = false;
            let pcSilentClear = false;

            function clearPermisFile(input) {
                pcSilentClear = true;
                input.value = '';
                input.dispatchEvent(new Event('change'));
            }

            function scanPermis(input) {
                const file = input.files && input.files[0];
                const status = document.getElementById('pc_ai_status');
                status.dataset.baseClass = 'text-xs mt-1.5';
                const token = ++pcScanToken;
                pcScanPending = false;

                if (!file) {
                    if (!pcSilentClear) {
                        status.className = 'text-xs mt-1.5 text-slate-400';
                        status.textContent = PC_DEFAULT_HINT;
                    }
                    pcSilentClear = false;
                    return;
                }

                pcScanPending = true;
                setStatus(status, 'info', 'fa-solid fa-circle-notch animate-spin', 'Vérification du permis de conduire…');

                postScan('{{ route("assure.contrats.scan-permis-ai") }}', 'permis_conduire', file)
                    .then(res => {
                        if (token !== pcScanToken) return;
                        if (res.success && res.data) {
                            const d = res.data;
                            const fin = d.date_expiration ? new Date(d.date_expiration + 'T00:00:00').toLocaleDateString('fr-FR') : null;
                            const cat = d.categories ? ' · catégorie(s) ' + d.categories : '';
                            if (d.expiree) {
                                status.dataset.baseClass = 'text-xs mt-1.5 !text-amber-600';
                                setStatus(status, 'info', 'fa-solid fa-triangle-exclamation',
                                    'Permis vérifié' + cat + ', mais expiré' + (fin ? ' depuis le ' + fin : '') + '.');
                            } else {
                                setStatus(status, 'success', 'fa-solid fa-circle-check',
                                    'Permis de conduire vérifié' + cat + (fin ? ' · valide jusqu\'au ' + fin : '') + '.');
                            }
                        } else {
                            const msg = res.message || "Le document téléversé n'est pas un permis de conduire.";
                            setStatus(status, 'error', 'fa-solid fa-circle-xmark', msg);
                            clearPermisFile(input);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: res.invalid ? 'error' : 'warning',
                                    title: res.invalid ? "Ce n'est pas un permis de conduire" : 'Analyse impossible',
                                    text: msg,
                                    confirmButtonText: 'Compris',
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        }
                    })
                    .catch(err => {
                        if (token !== pcScanToken) return;
                        console.error('Erreur IA permis :', err);
                        setStatus(status, 'error', 'fa-solid fa-circle-exclamation', 'Analyse indisponible pour le moment. Réessayez dans quelques instants.');
                        clearPermisFile(input);
                    })
                    .finally(() => {
                        if (token === pcScanToken) pcScanPending = false;
                    });
            }

            /* ---------- Scan IA de l'attestation (pré-remplissage) ---------- */
            function handleAIAttestationUpload(input) {
                if (!input.files || !input.files[0]) return;

                const file = input.files[0];
                const status = document.getElementById('ai_scan_status');
                const icon = document.getElementById('ai_uploader_icon');
                const btnText = document.getElementById('ai_uploader_btn_text');
                status.dataset.baseClass = 'mt-4 pt-4 border-t border-slate-100 text-[13px]';

                if (file.size > MAX_FILE_SIZE) {
                    setStatus(status, 'error', 'fa-solid fa-circle-exclamation', 'Fichier trop volumineux (5 Mo maximum).');
                    input.value = '';
                    return;
                }

                icon.className = 'fa-solid fa-circle-notch animate-spin text-xs';
                btnText.textContent = 'Analyse en cours…';
                setStatus(status, 'info', 'fa-solid fa-robot', "Analyse de votre attestation par l'IA…");

                postScan('{{ route("assure.contrats.scan-attestation-ai") }}', 'attestation', file)
                    .then(res => {
                        if (res.success && res.data) {
                            const d = res.data;
                            const filled = [];
                            const fields = [
                                ['plaque', d.plaque, 'plaque'],
                                ['marque', d.marque, 'marque'],
                                ['modele', d.modele, 'modèle'],
                                ['numero_contrat', d.numero_contrat, 'n° de police'],
                            ];
                            fields.forEach(([id, value, label]) => {
                                const el = document.getElementById(id);
                                if (el && value && !el.readOnly) { el.value = value; filled.push(label); }
                            });
                            if (selectTypeVehicule(d.type_vehicule)) filled.push('type');

                            attachFileTo('attestation_assurance', file);

                            setStatus(status, 'success', 'fa-solid fa-circle-check',
                                'Attestation analysée · ' + (filled.length ? filled.join(', ') + ' rempli(s)' : 'aucun champ détecté') + '. Document joint aux justificatifs.');
                            btnText.textContent = "Changer l'attestation";

                            if (res.existing_contrat_id && !{{ $isRenew ? 'true' : 'false' }} && typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Assurance déjà enregistrée',
                                    html: 'Une assurance existe déjà pour le véhicule <b>' + (res.existing_plaque || d.plaque || '') + '</b>.<br>Voulez-vous la renouveler ?',
                                    showCancelButton: true,
                                    confirmButtonText: 'Renouveler',
                                    cancelButtonText: 'Continuer',
                                    confirmButtonColor: '#2563eb',
                                    cancelButtonColor: '#64748b'
                                }).then(result => {
                                    const contratId = parseInt(res.existing_contrat_id, 10);
                                    if (result.isConfirmed && !isNaN(contratId)) {
                                        window.location.href = "{{ route('assure.contrats.create') }}?renouveler_id=" + contratId;
                                    }
                                });
                            }
                        } else {
                            const msg = res.message || "Le document téléversé n'est pas une attestation d'assurance automobile.";
                            setStatus(status, 'error', 'fa-solid fa-circle-xmark', 'Document rejeté : ' + msg);
                            btnText.textContent = "Téléverser l'attestation";
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'error', title: 'Document invalide', text: msg, confirmButtonText: 'Compris', confirmButtonColor: '#2563eb' });
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Erreur IA attestation :', err);
                        setStatus(status, 'error', 'fa-solid fa-circle-exclamation', "Analyse indisponible. Vous pouvez remplir le formulaire manuellement.");
                        btnText.textContent = 'Réessayer';
                    })
                    .finally(() => {
                        icon.className = 'fa-solid fa-arrow-up-from-bracket text-xs';
                        input.value = '';
                    });
            }

            /* ---------- Zones de dépôt des justificatifs ---------- */
            document.addEventListener('DOMContentLoaded', function () {
                const cards = document.querySelectorAll('.doc-card');
                const counter = document.getElementById('docs_counter');

                const updateCounter = () => {
                    const total = cards.length;
                    const done = [...cards].filter(c => c.querySelector('input[type=file]').files.length).length;
                    counter.textContent = done + ' / ' + total + ' document(s) ajouté(s)';
                };

                cards.forEach(card => {
                    const input = card.querySelector('input[type=file]');
                    const empty = card.querySelector('[data-empty]');
                    const filled = card.querySelector('[data-filled]');
                    const thumb = card.querySelector('[data-thumb]');
                    const name = card.querySelector('[data-name]');
                    const error = card.querySelector('[data-error]');

                    const render = () => {
                        const file = input.files && input.files[0];
                        thumb.textContent = '';
                        if (!file) {
                            empty.classList.remove('hidden');
                            filled.classList.add('hidden');
                            filled.classList.remove('flex');
                            updateCounter();
                            return;
                        }
                        if (file.size > MAX_FILE_SIZE) {
                            input.value = '';
                            error.textContent = 'Fichier trop volumineux (5 Mo maximum).';
                            error.classList.remove('hidden');
                            render();
                            return;
                        }
                        error.classList.add('hidden');
                        name.textContent = file.name;

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.className = 'w-full h-full object-cover';
                            img.src = URL.createObjectURL(file);
                            thumb.appendChild(img);
                        } else {
                            const i = document.createElement('i');
                            i.className = 'fa-regular fa-file-pdf text-3xl text-red-500';
                            thumb.appendChild(i);
                        }
                        empty.classList.add('hidden');
                        filled.classList.remove('hidden');
                        filled.classList.add('flex');
                        updateCounter();
                    };

                    input.addEventListener('change', render);
                    if (input.id === 'carte_grise') {
                        input.addEventListener('change', () => scanCarteGrise(input));
                    }
                    if (input.id === 'visite_technique') {
                        input.addEventListener('change', () => scanVisiteTechnique(input));
                    }
                    if (input.id === 'permis_conduire') {
                        input.addEventListener('change', () => scanPermis(input));
                    }

                    card.querySelector('[data-remove]').addEventListener('click', () => {
                        input.value = '';
                        input.dispatchEvent(new Event('change'));
                    });

                    // Glisser-déposer
                    ['dragenter', 'dragover'].forEach(evt => empty.addEventListener(evt, e => {
                        e.preventDefault();
                        empty.classList.add('border-blue-500', 'bg-blue-50/40');
                    }));
                    ['dragleave', 'drop'].forEach(evt => empty.addEventListener(evt, e => {
                        e.preventDefault();
                        empty.classList.remove('border-blue-500', 'bg-blue-50/40');
                    }));
                    empty.addEventListener('drop', e => {
                        if (e.dataTransfer.files.length) attachFileTo(input.id, e.dataTransfer.files[0]);
                    });
                });

                updateCounter();

                /* ---------- Soumission ---------- */
                const form = document.getElementById('contractForm');
                const submitBtn = document.getElementById('submitBtn');

                form.addEventListener('submit', function (e) {
                    const alpine = Alpine.$data(form.parentElement);
                    if (!alpine.validateStep(1) || !alpine.validateStep(2)) {
                        e.preventDefault();
                        return;
                    }

                    // Justificatifs obligatoires manquants
                    const missing = [...cards].filter(c => {
                        const input = c.querySelector('input[type=file]');
                        return input.required && !input.files.length;
                    });
                    if (missing.length) {
                        e.preventDefault();
                        alpine.currentStep = 3;
                        missing.forEach(c => {
                            const err = c.querySelector('[data-error]');
                            err.textContent = 'Ce document est obligatoire.';
                            err.classList.remove('hidden');
                        });
                        missing[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    // N° de carte grise : lu par l'IA, obligatoire
                    const cgCard = document.querySelector('.doc-card[data-doc="carte_grise"]');
                    if (cgScanPending || vtScanPending || pcScanPending) {
                        e.preventDefault();
                        alpine.currentStep = 3;
                        Swal.fire({ icon: 'info', title: 'Vérification en cours', text: "Patientez pendant l'analyse de vos documents par l'IA.", confirmButtonColor: '#2563eb' });
                        return;
                    }
                    if (!document.getElementById('immatriculation').value.trim()) {
                        e.preventDefault();
                        alpine.currentStep = 3;
                        const err = cgCard.querySelector('[data-error]');
                        err.textContent = "Ajoutez une carte grise valide : son numéro n'a pas pu être lu.";
                        err.classList.remove('hidden');
                        cgCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Analyse en cours…',
                            html: "Nous vérifions votre attestation et extrayons les informations.<br>Merci de patienter quelques instants.",
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    }
                    submitBtn.disabled = true;
                });
            });
        </script>
    @endpush
@endsection
