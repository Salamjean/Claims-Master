@extends('assure.layouts.template')

@section('title', isset($contratExistant) && $contratExistant ? 'Renouveler une assurance' : 'Ajouter une assurance')
@section('page-title', isset($contratExistant) && $contratExistant ? 'Renouvellement Assurance' : 'Nouvelle Assurance')

@section('content')
    <div class="mx-auto pb-16" style="width: 90%;"
        x-data="{
            currentStep: 1,
            totalSteps: 3,
            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    this.currentStep = Math.min(this.currentStep + 1, this.totalSteps);
                    window.scrollTo({ top: 100, behavior: 'smooth' });
                }
            },
            prevStep() {
                this.currentStep = Math.max(this.currentStep - 1, 1);
                window.scrollTo({ top: 100, behavior: 'smooth' });
            },
            validateStep(step) {
                if (step === 1) {
                    const plaque = document.getElementById('plaque');
                    const marque = document.getElementById('marque');
                    const modele = document.getElementById('modele');
                    const immat = document.getElementById('immatriculation');
                    const type = document.getElementById('type_vehicule');

                    if (!plaque.checkValidity() || !marque.checkValidity() || !modele.checkValidity() || !immat.checkValidity() || !type.checkValidity()) {
                        plaque.reportValidity() || marque.reportValidity() || modele.reportValidity() || immat.reportValidity() || type.reportValidity();
                        return false;
                    }
                } else if (step === 2) {
                    const num = document.getElementById('numero_contrat');
                    if (!num.checkValidity()) {
                        num.reportValidity();
                        return false;
                    }
                }
                return true;
            },
            init() {
                @if($errors->has('attestation_assurance') || $errors->has('document_pdf') || $errors->has('carte_grise') || $errors->has('visite_technique') || $errors->has('permis_conduire'))
                    this.currentStep = 3;
                @elseif($errors->has('numero_contrat'))
                    this.currentStep = 2;
                @endif
            }
        }">

        {{-- Fil d'ariane & Bouton Retour --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('assure.contrats.index') }}"
                class="group inline-flex items-center gap-2.5 px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 hover:text-blue-600 rounded-xl text-xs font-bold border border-slate-200/80 shadow-sm transition-all duration-200">
                <i class="fa-solid fa-arrow-left text-xs group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Retour à Mes Assurances</span>
            </a>

            <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-400">
                <i class="fa-solid fa-shield-halved text-blue-500"></i>
                <span>Sécurisé & Vérifié par IA</span>
            </div>
        </div>

        {{-- Hero Card Header --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 p-8 md:p-10 text-white shadow-2xl mb-8 border border-white/10">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start md:items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-0.5 shadow-lg shadow-blue-500/30 shrink-0">
                        <div class="w-full h-full bg-slate-950/40 backdrop-blur-md rounded-[14px] flex items-center justify-center text-white">
                            <i class="fa-solid {{ isset($contratExistant) && $contratExistant ? 'fa-arrows-rotate text-2xl animate-spin-slow text-amber-400' : 'fa-car-side text-2xl text-blue-400' }}"></i>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-bold uppercase tracking-wider border border-blue-400/20">
                                {{ isset($contratExistant) && $contratExistant ? 'Mode Renouvellement' : 'Enregistrement Pas à Pas' }}
                            </span>
                            <span class="text-slate-400 text-xs">• Format ASACI</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">
                            {{ isset($contratExistant) && $contratExistant ? 'Renouveler l\'Assurance Automobile' : 'Enregistrer une nouvelle assurance' }}
                        </h1>
                        <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-2xl leading-relaxed">
                            {{ isset($contratExistant) && $contratExistant ? 'Mettez à jour votre attestation pour réactiver le véhicule ' . $contratExistant->plaque : 'Complétez les étapes ci-dessous et téléversez vos justificatifs pour l\'analyse automatique.' }}
                        </p>
                    </div>
                </div>

                @if(isset($contratExistant) && $contratExistant)
                    <div class="shrink-0 bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-[10px] text-blue-200 uppercase tracking-widest font-bold">Véhicule Cible</p>
                            <p class="text-sm font-extrabold text-white font-mono">{{ $contratExistant->plaque }}</p>
                            <p class="text-xs text-slate-300">{{ $contratExistant->marque }} {{ $contratExistant->modele }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- STEPPER NAVIGATION INDICATOR --}}
        <div class="mb-8 bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <div class="flex items-center justify-between relative max-w-3xl mx-auto">
                {{-- Ligne de progression --}}
                <div class="absolute top-6 left-12 right-12 h-1 bg-slate-100 rounded-full -z-0">
                    <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full transition-all duration-500"
                        :style="'width: ' + ((currentStep - 1) / (totalSteps - 1) * 100) + '%'"></div>
                </div>

                {{-- Étape 1 --}}
                <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer" @click="currentStep = 1">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm transition-all duration-300 shadow-md"
                        :class="currentStep === 1 ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-blue-500/30 scale-110 ring-4 ring-blue-500/20' : (currentStep > 1 ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-slate-100 text-slate-400')">
                        <template x-if="currentStep > 1">
                            <i class="fa-solid fa-check"></i>
                        </template>
                        <template x-if="currentStep <= 1">
                            <i class="fa-solid fa-car"></i>
                        </template>
                    </div>
                    <span class="text-xs font-extrabold transition-colors"
                        :class="currentStep === 1 ? 'text-blue-600' : (currentStep > 1 ? 'text-emerald-600' : 'text-slate-400')">Étape 1 : Véhicule</span>
                </div>

                {{-- Étape 2 --}}
                <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer" @click="if (validateStep(1)) currentStep = 2">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm transition-all duration-300 shadow-md"
                        :class="currentStep === 2 ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-blue-500/30 scale-110 ring-4 ring-blue-500/20' : (currentStep > 2 ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-slate-100 text-slate-400')">
                        <template x-if="currentStep > 2">
                            <i class="fa-solid fa-check"></i>
                        </template>
                        <template x-if="currentStep <= 2">
                            <i class="fa-solid fa-file-contract"></i>
                        </template>
                    </div>
                    <span class="text-xs font-extrabold transition-colors"
                        :class="currentStep === 2 ? 'text-blue-600' : (currentStep > 2 ? 'text-emerald-600' : 'text-slate-400')">Étape 2 : Contrat</span>
                </div>

                {{-- Étape 3 --}}
                <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer" @click="if (validateStep(1) && validateStep(2)) currentStep = 3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm transition-all duration-300 shadow-md"
                        :class="currentStep === 3 ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-blue-500/30 scale-110 ring-4 ring-blue-500/20' : 'bg-slate-100 text-slate-400'">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <span class="text-xs font-extrabold transition-colors"
                        :class="currentStep === 3 ? 'text-blue-600' : 'text-slate-400'">Étape 3 : Documents</span>
                </div>
            </div>
        </div>

        {{-- Formulaire Principal --}}
        <form action="{{ route('assure.contrats.store') }}" method="POST" enctype="multipart/form-data" id="contractForm" class="space-y-8">
            @csrf

            @if(isset($contratExistant) && $contratExistant)
                <input type="hidden" name="renouveler_id" value="{{ $contratExistant->id }}">
            @endif

            {{-- ÉTAPE 1: VÉHICULE --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-3xl p-6 md:p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-500 to-cyan-500"></div>

                <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold shadow-sm">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-800">Étape 1 : Caractéristiques du Véhicule</h2>
                        <p class="text-xs text-slate-400">Renseignez la plaque, la marque, le modèle et le type de véhicule</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Plaque --}}
                    <div class="space-y-2 md:col-span-2">
                        <label for="plaque" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Plaque d'immatriculation <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-rectangle-ad text-base"></i>
                            </div>
                            <input type="text" name="plaque" id="plaque"
                                value="{{ old('plaque', $contratExistant->plaque ?? '') }}"
                                placeholder="Ex: 1234AB01 ou 1234 AB 01"
                                required
                                class="w-full pl-12 pr-4 py-4 bg-slate-50/70 border border-slate-200 rounded-2xl text-base font-extrabold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all uppercase tracking-wider outline-none">
                        </div>
                        @error('plaque') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- Marque --}}
                    <div class="space-y-2">
                        <label for="marque" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Marque <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-copyright text-sm"></i>
                            </div>
                            <input type="text" name="marque" id="marque"
                                value="{{ old('marque', $contratExistant->marque ?? '') }}"
                                placeholder="Ex: Toyota, Hyundai..."
                                required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                        </div>
                        @error('marque') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- Modèle --}}
                    <div class="space-y-2">
                        <label for="modele" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Modèle <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-tag text-sm"></i>
                            </div>
                            <input type="text" name="modele" id="modele"
                                value="{{ old('modele', $contratExistant->modele ?? '') }}"
                                placeholder="Ex: Corolla, Tucson..."
                                required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                        </div>
                        @error('modele') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- N° Carte Grise --}}
                    <div class="space-y-2">
                        <label for="immatriculation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            N° de Châssis / Carte Grise <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-id-card text-sm"></i>
                            </div>
                            <input type="text" name="immatriculation" id="immatriculation"
                                value="{{ old('immatriculation', $contratExistant->immatriculation ?? '') }}"
                                placeholder="Ex: CH299253 ou VF1..."
                                required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all uppercase outline-none">
                        </div>
                        @error('immatriculation') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- Type de véhicule --}}
                    <div class="space-y-2">
                        <label for="type_vehicule" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Type de véhicule <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-truck-pickup text-sm"></i>
                            </div>
                            <select name="type_vehicule" id="type_vehicule" required
                                class="w-full pl-11 pr-10 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none appearance-none cursor-pointer">
                                <option value="" disabled selected>Sélectionnez la catégorie</option>
                                <option value="Berline" {{ old('type_vehicule', $contratExistant->type_vehicule ?? '') == 'Berline' ? 'selected' : '' }}>Berline</option>
                                <option value="SUV" {{ old('type_vehicule', $contratExistant->type_vehicule ?? '') == 'SUV' ? 'selected' : '' }}>SUV / 4x4</option>
                                <option value="Citadine" {{ old('type_vehicule', $contratExistant->type_vehicule ?? '') == 'Citadine' ? 'selected' : '' }}>Citadine</option>
                                <option value="Camionnette" {{ old('type_vehicule', $contratExistant->type_vehicule ?? '') == 'Camionnette' ? 'selected' : '' }}>Camionnette / Utilitaire</option>
                                <option value="Coupé" {{ old('type_vehicule', $contratExistant->type_vehicule ?? '') == 'Coupé' ? 'selected' : '' }}>Coupé / Cabriolet</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        @error('type_vehicule') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ÉTAPE 2: CONTRAT & IA --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-3xl p-6 md:p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

                <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold shadow-sm">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-800">Étape 2 : Numéro de Police & Audit IA</h2>
                        <p class="text-xs text-slate-400">Saisissez votre numéro de contrat et découvrez l'automatisation IA</p>
                    </div>
                </div>

                <div class="space-y-6 max-w-2xl mx-auto">
                    {{-- Numéro de contrat --}}
                    <div class="space-y-2">
                        <label for="numero_contrat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Numéro de Police / Contrat d'assurance <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-hashtag text-base"></i>
                            </div>
                            <input type="text" name="numero_contrat" id="numero_contrat"
                                value="{{ old('numero_contrat', $contratExistant->numero_contrat ?? '') }}"
                                placeholder="Ex: POL-998877 ou N° attestation"
                                required
                                class="w-full pl-12 pr-4 py-4 bg-slate-50/70 border border-slate-200 rounded-2xl text-base font-extrabold text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all uppercase outline-none">
                        </div>
                        @error('numero_contrat') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- Banner IA --}}
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-blue-950 text-white shadow-xl relative overflow-hidden border border-white/10">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-start gap-4 relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 text-indigo-300 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base font-black text-white uppercase tracking-wider">Audit & Extraction Automatique par Gemini IA</h4>
                                    <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-bold rounded-md border border-emerald-400/20">Active</span>
                                </div>
                                <p class="text-xs text-slate-300 leading-relaxed">
                                    Inutile de sélectionner manuellement votre compagnie d'assurance ou la date d'échéance. Lors de la validation à l'étape suivante, notre système IA scannera votre attestation ASACI, extraira la date d'expiration exacte et effectuera le rattachement automatique.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Remarque format ASACI --}}
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-center gap-3 text-amber-900">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg shrink-0"></i>
                        <p class="text-xs font-medium leading-tight">
                            Assurez-vous de transmettre une attestation d'assurance lisible et au format officiel ASACI à l'étape suivante pour garantir la validation rapide.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ÉTAPE 3: DOCUMENTS JUSTIFICATIFS --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-3xl p-6 md:p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-500"></div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold shadow-sm">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-800">Étape 3 : Documents & Pièces Justificatives</h2>
                            <p class="text-xs text-slate-400">Téléversez vos justificatifs pour valider et analyser votre assurance</p>
                        </div>
                    </div>
                    <span class="px-3.5 py-1.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold self-start sm:self-auto">
                        <i class="fa-solid fa-paperclip mr-1.5 text-slate-400"></i> Max 5 Mo par fichier
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- 1. Attestation d'Assurance (Mise en valeur IA) --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-id-card text-emerald-600"></i>
                                Attestation d'Assurance <span class="text-red-500">*</span>
                            </label>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[9px] font-extrabold border border-emerald-200">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>Scanné par IA
                            </span>
                        </div>

                        <div id="container_attestation_assurance" class="relative group">
                            <label for="attestation_assurance"
                                class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-emerald-300 hover:border-emerald-500 rounded-2xl cursor-pointer transition-all bg-emerald-50/30 hover:bg-emerald-50/70 text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Attestation (Format ASACI)</p>
                                <p class="text-[10px] text-slate-500 mt-1">Cliquez ou déposez votre fichier ici</p>
                                <input id="attestation_assurance" name="attestation_assurance" type="file" required accept="image/*,application/pdf"
                                    class="hidden file-input-preview" data-preview="preview_attestation_assurance" />
                            </label>

                            <div id="preview_attestation_assurance" class="hidden w-full h-44 border border-emerald-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                <img src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity p-4 text-center">
                                    <span class="text-white text-xs font-extrabold mb-1">Attestation sélectionnée</span>
                                    <span class="text-[10px] text-emerald-300 font-mono file-name-display truncate max-w-full"></span>
                                </div>
                            </div>
                        </div>
                        @error('attestation_assurance') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- 2. Copie du contrat --}}
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-file-pdf text-blue-600"></i>
                            Copie du Contrat {{ isset($contratExistant) && $contratExistant ? '(Optionnel)' : '*' }}
                        </label>

                        <div id="container_document_pdf" class="relative group">
                            <label for="document_pdf"
                                class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-100/50 text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform shadow-sm">
                                    <i class="fa-solid fa-file-arrow-up"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Document du contrat</p>
                                <p class="text-[10px] text-slate-500 mt-1">Cliquez ou déposez votre contrat PDF/Image</p>
                                <input id="document_pdf" name="document_pdf" type="file" {{ isset($contratExistant) && $contratExistant ? '' : 'required' }} accept="image/*,application/pdf"
                                    class="hidden file-input-preview" data-preview="preview_document_pdf" />
                            </label>

                            <div id="preview_document_pdf" class="hidden w-full h-44 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                <img src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity p-4 text-center">
                                    <span class="text-white text-xs font-extrabold mb-1">Contrat sélectionné</span>
                                    <span class="text-[10px] text-blue-300 font-mono file-name-display truncate max-w-full"></span>
                                </div>
                            </div>
                        </div>
                        @error('document_pdf') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- 3. Carte Grise --}}
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-file-invoice text-teal-600"></i>
                            Carte Grise {{ isset($contratExistant) && $contratExistant ? '(Optionnel)' : '*' }}
                        </label>

                        <div id="container_carte_grise" class="relative group">
                            <label for="carte_grise"
                                class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-slate-200 hover:border-teal-400 rounded-2xl cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-100/50 text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform shadow-sm">
                                    <i class="fa-solid fa-address-card"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Scan Carte Grise</p>
                                <p class="text-[10px] text-slate-500 mt-1">Cliquez ou déposez la carte grise</p>
                                <input id="carte_grise" name="carte_grise" type="file" {{ isset($contratExistant) && $contratExistant ? '' : 'required' }} accept="image/*,application/pdf"
                                    class="hidden file-input-preview" data-preview="preview_carte_grise" />
                            </label>

                            <div id="preview_carte_grise" class="hidden w-full h-44 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                <img src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity p-4 text-center">
                                    <span class="text-white text-xs font-extrabold mb-1">Carte Grise sélectionnée</span>
                                    <span class="text-[10px] text-teal-300 font-mono file-name-display truncate max-w-full"></span>
                                </div>
                            </div>
                        </div>
                        @error('carte_grise') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- 4. Visite Technique --}}
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-clipboard-check text-amber-600"></i>
                            Visite Technique {{ isset($contratExistant) && $contratExistant ? '(Optionnel)' : '*' }}
                        </label>

                        <div id="container_visite_technique" class="relative group">
                            <label for="visite_technique"
                                class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-2xl cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-100/50 text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform shadow-sm">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Visite Technique</p>
                                <p class="text-[10px] text-slate-500 mt-1">Cliquez ou déposez l'attestation de visite</p>
                                <input id="visite_technique" name="visite_technique" type="file" {{ isset($contratExistant) && $contratExistant ? '' : 'required' }} accept="image/*,application/pdf"
                                    class="hidden file-input-preview" data-preview="preview_visite_technique" />
                            </label>

                            <div id="preview_visite_technique" class="hidden w-full h-44 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                <img src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity p-4 text-center">
                                    <span class="text-white text-xs font-extrabold mb-1">Visite Technique sélectionnée</span>
                                    <span class="text-[10px] text-amber-300 font-mono file-name-display truncate max-w-full"></span>
                                </div>
                            </div>
                        </div>
                        @error('visite_technique') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- 5. Permis de Conduire --}}
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-id-badge text-purple-600"></i>
                            Permis de Conduire {{ isset($contratExistant) && $contratExistant ? '(Optionnel)' : '*' }}
                        </label>

                        <div id="container_permis_conduire" class="relative group">
                            <label for="permis_conduire"
                                class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-slate-200 hover:border-purple-400 rounded-2xl cursor-pointer transition-all bg-slate-50/50 hover:bg-slate-100/50 text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition-transform shadow-sm">
                                    <i class="fa-solid fa-address-card"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Permis de Conduire</p>
                                <p class="text-[10px] text-slate-500 mt-1">Cliquez ou déposez le permis</p>
                                <input id="permis_conduire" name="permis_conduire" type="file" {{ isset($contratExistant) && $contratExistant ? '' : 'required' }} accept="image/*,application/pdf"
                                    class="hidden file-input-preview" data-preview="preview_permis_conduire" />
                            </label>

                            <div id="preview_permis_conduire" class="hidden w-full h-44 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                <img src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity p-4 text-center">
                                    <span class="text-white text-xs font-extrabold mb-1">Permis sélectionné</span>
                                    <span class="text-[10px] text-purple-300 font-mono file-name-display truncate max-w-full"></span>
                                </div>
                            </div>
                        </div>
                        @error('permis_conduire') <p class="text-red-500 text-xs font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            {{-- BARRE DE NAVIGATION PAS À PAS --}}
            <div class="flex items-center justify-between pt-6 border-t border-slate-200">
                <div>
                    <button type="button" x-show="currentStep > 1" @click="prevStep()"
                        class="px-6 py-3.5 rounded-2xl border border-slate-200 text-slate-700 font-extrabold text-xs hover:bg-slate-100 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Précédent</span>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" x-show="currentStep < 3" @click="nextStep()"
                        class="px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all flex items-center gap-2">
                        <span>Étape Suivante</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <button type="submit" id="submitBtn" x-show="currentStep === 3"
                        class="px-10 py-4 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-3">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                        <span>{{ isset($contratExistant) && $contratExistant ? 'Valider le renouvellement' : 'Enregistrer & Analyser avec l\'IA' }}</span>
                        <i class="fa-solid fa-check text-xs"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- GESTION DES APERÇUS & DROPZONES ---
                const fileInputs = document.querySelectorAll('.file-input-preview');

                fileInputs.forEach(input => {
                    input.addEventListener('change', function () {
                        const previewId = this.dataset.preview;
                        const previewDiv = document.getElementById(previewId);
                        const labelElem = this.closest('label');
                        const img = previewDiv.querySelector('img');
                        const fileNameSpan = previewDiv.querySelector('.file-name-display');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            if (fileNameSpan) fileNameSpan.textContent = file.name;

                            const reader = new FileReader();

                            reader.onload = function (e) {
                                if (file.type === 'application/pdf') {
                                    img.src = 'https://cdn-icons-png.flaticon.com/512/337/337946.png'; // Icône PDF
                                    img.classList.add('p-8', 'object-contain');
                                    img.classList.remove('object-cover');
                                } else {
                                    img.src = e.target.result;
                                    img.classList.remove('p-8', 'object-contain');
                                    img.classList.add('object-cover');
                                }
                                labelElem.classList.add('hidden');
                                previewDiv.classList.remove('hidden');
                            }

                            reader.readAsDataURL(file);
                        }
                    });
                });

                // --- GESTION DU LOADING IA ---
                const form = document.getElementById('contractForm');
                const submitBtn = document.getElementById('submitBtn');

                if (form) {
                    form.addEventListener('submit', function (e) {
                        Swal.fire({
                            title: 'Analyse IA en cours...',
                            html: 'Nous vérifions la conformité de votre attestation (Format ASACI) et extrayons les informations.<br><br><b>Veuillez patienter quelques instants...</b>',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            customClass: {
                                popup: 'rounded-3xl'
                            }
                        });

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection