@extends('agent.layouts.template')
@section('title', 'Faire le constat')
@section('page-title', 'Constat Terrain')

@section('content')
<!-- Fabric.js Library pour les objets déplaçables -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<div class="w-full space-y-6 pb-12" x-data="agentConstatUniqueLayout()">

    <!-- Header Banner -->
    <div class="w-full bg-slate-900 text-white p-6 md:p-8 rounded-3xl shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-bold border border-indigo-500/30">
                <i class="fa-solid fa-user-gear"></i> Espace Agent & Expert Terrain
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-white mt-2">
                {{ $isAccident ? "Constat Terrain d'Accident" : "Constat Terrain d'Incident" }}
            </h1>
            <p class="text-xs md:text-sm text-slate-400 mt-1">Saisie guidée du rapport d'expertise terrain et relevé contradictoire.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-4 py-2.5 bg-slate-800 rounded-2xl border border-slate-700 text-xs">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">N° Sinistre</span>
                <span class="font-extrabold text-indigo-400 text-sm">#{{ $sinistre->numero_sinistre ?? $sinistre->id }}</span>
            </div>
            <a href="{{ route('agent.sinistres.en_attente') }}" class="px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs border border-slate-700 shadow-md">
                <i class="fa-solid fa-arrow-left"></i> Quitter
            </a>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="w-full rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm shadow-sm">
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

    <!-- Disposition Unique : Colonne Gauche (Menu Navigation & Synthèse) + Colonne Droite (Formulaire) -->
    <form action="{{ route('agent.sinistres.constat.store', $sinistre->id) }}" method="POST" enctype="multipart/form-data" onsubmit="saveAgentFabricSketchData()" novalidate class="w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-start agent-constat-form">
        @csrf

        <!-- COLONNE GAUCHE (4 Cols) : Deck de Navigation & Fiche Synthèse -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-2 mb-1">Étapes d'Expertise</h3>

                <button type="button" @click="setStep(1)" :class="step === 1 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold scale-[1.02]' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" class="w-full p-4 rounded-2xl text-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0" :class="step === 1 ? 'bg-white text-indigo-600' : 'bg-slate-200 text-slate-700'">1</div>
                        <div class="text-left">
                            <div class="font-bold text-sm">Lieu & Faits</div>
                            <div class="text-[10px] opacity-75">Circonstances & faits</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>

                <button type="button" @click="setStep(2)" :class="step === 2 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold scale-[1.02]' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" class="w-full p-4 rounded-2xl text-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0" :class="step === 2 ? 'bg-white text-indigo-600' : 'bg-slate-200 text-slate-700'">2</div>
                        <div class="text-left">
                            <div class="font-bold text-sm">Véhicule A (Assuré)</div>
                            <div class="text-[10px] opacity-75">Caractéristiques A</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>

                <button type="button" @click="setStep(3)" :class="step === 3 ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30 font-bold scale-[1.02]' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" class="w-full p-4 rounded-2xl text-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0" :class="step === 3 ? 'bg-white text-rose-600' : 'bg-slate-200 text-slate-700'">3</div>
                        <div class="text-left">
                            <div class="font-bold text-sm">Véhicule B & Victimes</div>
                            <div class="text-[10px] opacity-75">Partie adverse & santé</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>

                <button type="button" @click="setStep(4)" :class="step === 4 ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 font-bold scale-[1.02]' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" class="w-full p-4 rounded-2xl text-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0" :class="step === 4 ? 'bg-white text-emerald-600' : 'bg-slate-200 text-slate-700'">4</div>
                        <div class="text-left">
                            <div class="font-bold text-sm">Croquis & Signature</div>
                            <div class="text-[10px] opacity-75">Dessin, photos & clôture</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-check text-xs"></i>
                </button>
            </div>

            <!-- Fiche Synthèse du Sinistre -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 rounded-3xl border border-slate-700 shadow-md space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-indigo-400 uppercase tracking-wider">
                    <i class="fa-solid fa-id-card-clip"></i> Synthèse Dossier Agent
                </div>
                <div class="text-xs space-y-2 border-t border-slate-700 pt-3">
                    <div class="flex justify-between"><span class="text-slate-400">Assuré :</span> <span class="font-bold text-white">{{ $sinistre->assure->name ?? '' }} {{ $sinistre->assure->prenom ?? '' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Type Sinistre :</span> <span class="font-semibold text-slate-200">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Lieu déclaré :</span> <span class="font-semibold text-slate-200">{{ $sinistre->lieu ?? 'Non précisé' }}</span></div>
                </div>
            </div>
        </div>

        <!-- COLONNE DROITE (8 Cols) : Espace de travail de l'Étape Active -->
        <div class="lg:col-span-8 space-y-6">

            <!-- ÉTAPE 1 : CONTEXTE & FAITS -->
            <div x-show="step === 1" x-transition.opacity class="space-y-6">
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-indigo-600 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg font-black">1</div>
                            <div>
                                <h2 class="text-xl font-extrabold text-slate-800">Localisation & Horaires des Constatations</h2>
                                <p class="text-xs text-slate-500">Précisez le lieu exact de l'incident et l'horodatage des constatations.</p>
                            </div>
                        </div>
                        <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-black border border-indigo-200 uppercase tracking-wider">Étape 01</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Lieu précis des faits <span class="text-rose-500">*</span></label>
                            <input type="text" name="lieu" value="{{ old('lieu', $constat->lieu ?? $sinistre->lieu ?? $sinistre->lieu_sinistre ?? '') }}" class="w-full text-xs font-bold rounded-2xl border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 bg-slate-50 py-3 text-slate-800" placeholder="Adresse complète, axe routier, repère GPS..." />
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Date & Heure du constat <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" name="date_heure" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full text-xs font-bold rounded-2xl border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 bg-slate-50 py-3 text-slate-800" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-indigo-600 space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <i class="fa-solid fa-align-left text-indigo-600 text-base"></i>
                            <h3 class="font-extrabold text-slate-800 text-base">Description & Récit des Faits <span class="text-rose-500">*</span></h3>
                        </div>
                        <div>
                            <textarea name="description_faits" rows="6" class="w-full text-xs font-semibold rounded-2xl border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 bg-slate-50 p-4 text-slate-800 leading-relaxed" placeholder="Récit complet des événements et constatations établies sur le terrain par l'expert..."></textarea>
                        </div>
                    </div>

                    <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-rose-600 space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                            <i class="fa-solid fa-car-burst text-rose-600 text-base"></i>
                            <h3 class="font-extrabold text-slate-800 text-base">Dommages & Dégâts Matériels <span class="text-rose-500">*</span></h3>
                        </div>
                        <div>
                            <textarea name="dommages" rows="6" class="w-full text-xs font-semibold rounded-2xl border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 bg-slate-50 p-4 text-slate-800 leading-relaxed" placeholder="Inventaire exhaustif des déformations mécaniques, chocs et dégâts matériels..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-slate-700 space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-users-viewfinder text-slate-700 text-base"></i>
                        <h3 class="font-extrabold text-slate-800 text-base">Témoins & Remarques d'Expertise</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Témoins présents</label>
                            <textarea name="temoins" rows="4" class="w-full text-xs font-semibold rounded-2xl border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 bg-slate-50 p-4 text-slate-800" placeholder="Identités et coordonnées des témoins..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Observations & Réservations</label>
                            <textarea name="observations" rows="4" class="w-full text-xs font-semibold rounded-2xl border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 bg-slate-50 p-4 text-slate-800" placeholder="Remarques et détails d'expertise..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÉTAPE 2 : VÉHICULE A (ASSURÉ) -->
            <div x-show="step === 2" x-transition.opacity class="space-y-6">
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-indigo-600 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl font-black">A</div>
                            <div>
                                <h2 class="text-xl font-extrabold text-slate-800">VÉHICULE A (Véhicule de l'Assuré)</h2>
                                <p class="text-xs text-slate-500">Caractéristiques, conducteur, permis et police d'assurance A.</p>
                            </div>
                        </div>
                        <span class="px-4 py-1.5 bg-indigo-100 text-indigo-800 rounded-full text-xs font-black uppercase tracking-wider">Partie A</span>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-car"></i> Caractéristiques du Véhicule A
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Marque</label>
                                <input type="text" name="veh_a_marque" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Ex: PEUGEOT..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Type / Modèle</label>
                                <input type="text" name="veh_a_type" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Ex: 308..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">État Général</label>
                                <select name="veh_a_etat_general" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5">
                                    <option value="">Sélectionner l'état...</option>
                                    <option value="neuf">Neuf</option>
                                    <option value="tres_bon">Très bon</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="passable">Passable</option>
                                    <option value="mauvais">Mauvais</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pneumatiques</label>
                                <select name="veh_a_pneumatiques" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5">
                                    <option value="">Pneus...</option>
                                    <option value="gravures_apparentes">Gravures très apparentes</option>
                                    <option value="etat_moyen">État moyen</option>
                                    <option value="pneus_lisses">Pneus lisses</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-slate-100">
                        <h3 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-id-card"></i> Conducteur A
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nom & Prénoms</label>
                                <input type="text" name="veh_a_conducteur_nom" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Conducteur A..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Date de Naissance</label>
                                <input type="date" name="veh_a_conducteur_date_naissance" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Téléphone</label>
                                <input type="text" name="veh_a_conducteur_tel" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Téléphone..." />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-slate-100">
                        <h3 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-file-contract"></i> Permis, Assurance & Dégâts A
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Permis N°</label>
                                <input type="text" name="veh_a_permis_numero" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="N° Permis..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Cie d'Assurance</label>
                                <input type="text" name="veh_a_assurance_nom" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Assurance..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Police N°</label>
                                <input type="text" name="veh_a_police_numero" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="N° Police..." />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Dégâts Apparents Véhicule A</label>
                            <textarea name="veh_a_degats_materiels" rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-3" placeholder="Dégâts observés sur le véhicule A..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÉTAPE 3 : VÉHICULE B & VICTIMES -->
            <div x-show="step === 3" x-transition.opacity class="space-y-6">
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-rose-600 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-xl font-black">B</div>
                            <div>
                                <h2 class="text-xl font-extrabold text-slate-800">VÉHICULE B (Tiers / Partie Adverse)</h2>
                                <p class="text-xs text-slate-500">Renseignez le second véhicule impliqué.</p>
                            </div>
                        </div>
                        <span class="px-4 py-1.5 bg-rose-100 text-rose-800 rounded-full text-xs font-black uppercase tracking-wider">Partie B</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Marque B</label>
                            <input type="text" name="veh_b_marque" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Ex: TOYOTA..." />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Conducteur B</label>
                            <input type="text" name="veh_b_conducteur_nom" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Nom conducteur B..." />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Assurance B</label>
                            <input type="text" name="veh_b_assurance_nom" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Assurance B..." />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Police B N°</label>
                            <input type="text" name="veh_b_police_numero" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Police B..." />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Dégâts Apparents Véhicule B</label>
                        <textarea name="veh_b_degats_materiels" rows="2" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-3" placeholder="Dégâts constatés sur le véhicule B..."></textarea>
                    </div>
                </div>

                <!-- Victimes -->
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-purple-600 space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl font-black">
                            <i class="fa-solid fa-user-injured"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-800">Victimes & Blessés</h2>
                            <p class="text-xs text-slate-500">Prise en charge sanitaire et évacuation hospitalière.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nom de la Victime</label>
                            <input type="text" name="victime_nom" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Nom..." />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Blessures Observées</label>
                            <input type="text" name="victime_blessures" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" placeholder="Blessures..." />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Évacuation Hospitalière</label>
                            <select name="hospital_id" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5">
                                <option value="">Aucune (Non évacué)</option>
                                @foreach($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÉTAPE 4 : CROQUIS INTERACTIF FABRIC.JS & CLÔTURE AGENT -->
            <div x-show="step === 4" x-transition.opacity class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Croquis Canvas Fabric.js -->
                    <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-indigo-600 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-pen-ruler text-indigo-600 text-base"></i>
                                <h3 class="font-extrabold text-slate-800 text-base">Croquis Interactif Agent</h3>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="setAgentSketchMode('draw')" id="agent-sketch-draw-btn" class="text-xs px-2.5 py-1 rounded-xl bg-indigo-600 text-white font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-pen"></i> Dessin
                                </button>
                                <button type="button" onclick="setAgentSketchMode('select')" id="agent-sketch-move-btn" class="text-xs px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-bold transition-all hover:bg-slate-200">
                                    <i class="fa-solid fa-up-down-left-right"></i> Déplacer
                                </button>
                                <button type="button" onclick="deleteAgentSelectedObject()" class="text-xs px-2.5 py-1 rounded-xl bg-rose-50 text-rose-600 font-bold transition-all hover:bg-rose-100">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                <button type="button" onclick="clearAgentFabricSketch()" class="text-xs px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-bold transition-all hover:bg-slate-200">
                                    <i class="fa-solid fa-eraser"></i> Effacer
                                </button>
                            </div>
                        </div>

                        <!-- Éléments déplaçables (Stamps / Symboles) -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Éléments à insérer sur le croquis :</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="addAgentSketchSymbol('auto')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    🚗 Auto
                                </button>
                                <button type="button" onclick="addAgentSketchSymbol('deux_roues')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    🏍️ 2 Roues
                                </button>
                                <button type="button" onclick="addAgentSketchSymbol('camion')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    🚚 Camion
                                </button>
                                <button type="button" onclick="addAgentSketchSymbol('pieton')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    🚶 Piéton
                                </button>
                                <button type="button" onclick="addAgentSketchSymbol('feu')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    🚦 Feu
                                </button>
                                <button type="button" onclick="addAgentSketchSymbol('stop')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    🛑 STOP
                                </button>
                                <button type="button" onclick="addAgentSketchSymbol('balise')" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition-all">
                                    ⚠️ Balise
                                </button>
                            </div>
                        </div>

                        <!-- Canvas Fabric.js Wrapper - Full Visible Dimension -->
                        <div id="agent-sketch-wrapper" class="relative w-full h-80 border-2 border-dashed border-slate-300 rounded-2xl bg-white overflow-hidden shadow-inner">
                            <canvas id="agent-sketch-pad" class="w-full h-full cursor-crosshair"></canvas>
                        </div>
                        <input type="hidden" name="croquis_data" id="agent-croquis-data">

                        <div class="pt-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Ou Importer un fichier photo du croquis</label>
                            <input type="file" name="croquis_file" accept="image/*" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 py-2" />
                        </div>
                    </div>

                    <!-- Photos & Pièces Jointes -->
                    <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-indigo-600 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-camera text-indigo-600 text-base"></i>
                                <h3 class="font-extrabold text-slate-800 text-base">Photos & Pièces Jointes</h3>
                            </div>
                            <button type="button" onclick="addAgentPhotoField()" class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-colors border border-indigo-200">
                                <i class="fa-solid fa-plus"></i> Ajouter photo
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Attestation Partie A</label>
                                <input type="file" name="ass1_photo" accept="image/*" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 py-2" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Attestation Partie B</label>
                                <input type="file" name="ass2_photo" accept="image/*" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 py-2" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Photos supplémentaires</label>
                            <div id="agent-photos-container" class="space-y-2">
                                <input type="file" name="photos_plus[]" accept="image/*" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 py-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agent Constatateur -->
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm border-t-4 border-t-emerald-600 space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-user-check text-emerald-600 text-base"></i>
                        <h3 class="font-extrabold text-slate-800 text-base">Agent Expert (Validation & Signature)</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nom & Prénoms de l'Agent <span class="text-rose-500">*</span></label>
                            <input type="text" name="agent_nom" value="{{ auth('user')->user()->name }} {{ auth('user')->user()->prenom }}" required class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-100 py-2.5 text-slate-800" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Grade / Qualification</label>
                            <input type="text" name="agent_grade" value="{{ auth('user')->user()->grade }}" placeholder="Agent Expert..." class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Matricule</label>
                            <input type="text" name="agent_matricule" value="{{ auth('user')->user()->matricule }}" placeholder="Matricule Agent..." class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2.5" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Action Bar -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <button type="button" @click="prevStep()" x-show="step > 1" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-2xl text-xs transition-all flex items-center gap-2">
                    <i class="fa-solid fa-chevron-left"></i> Étape précédente
                </button>
                <div x-show="step === 1"></div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="nextStep()" x-show="step < 4" class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl text-xs shadow-lg transition-all flex items-center gap-2">
                        Suivant <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <button type="submit" x-show="step === 4" class="px-10 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-xs shadow-xl transition-all flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-check-circle text-base"></i> Clôturer & Valider le Constat Agent
                    </button>
                </div>
            </div>

        </div>

    </form>
</div>

<!-- Fabric.js Agent Croquis Interactive Scripts avec synchronisation des dimensions -->
<script>
    let agentFabricSketch = null;

    const AGENT_SKETCH_SYMBOLS = {
        auto: {
            scale: 0.8,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 64" width="40" height="64"><rect x="5" y="12" width="30" height="40" rx="6" fill="#4f46e5" stroke="#1e293b" stroke-width="1.5"/><rect x="9" y="15" width="22" height="10" rx="3" fill="#c7d2fe"/><rect x="9" y="40" width="22" height="8" rx="3" fill="#c7d2fe"/><rect x="1" y="15" width="5" height="9" rx="2" fill="#1e293b"/><rect x="34" y="15" width="5" height="9" rx="2" fill="#1e293b"/><rect x="1" y="38" width="5" height="9" rx="2" fill="#1e293b"/><rect x="34" y="38" width="5" height="9" rx="2" fill="#1e293b"/></svg>`
        },
        deux_roues: {
            scale: 0.8,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 64" width="22" height="64"><rect x="5" y="6" width="12" height="52" rx="5" fill="#10b981" stroke="#1e293b" stroke-width="1.5"/><rect x="1" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="16" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="1" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/><rect x="16" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/></svg>`
        },
        camion: {
            scale: 0.7,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 90" width="50" height="90"><rect x="4" y="4" width="42" height="20" rx="4" fill="#64748b" stroke="#1e293b" stroke-width="1.5"/><rect x="8" y="7" width="34" height="11" rx="3" fill="#c7d2fe"/><rect x="3" y="28" width="44" height="58" rx="3" fill="#94a3b8" stroke="#1e293b" stroke-width="1.5"/><rect x="0" y="8" width="5" height="12" rx="2" fill="#1e293b"/><rect x="45" y="8" width="5" height="12" rx="2" fill="#1e293b"/></svg>`
        },
        pieton: {
            scale: 0.9,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 46" width="30" height="46"><circle cx="15" cy="7" r="6" fill="#e2e8f0" stroke="#1e293b" stroke-width="1.5"/><line x1="15" y1="13" x2="15" y2="30" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="21" x2="25" y2="21" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="30" x2="7" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="30" x2="23" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/></svg>`
        },
        feu: {
            scale: 0.9,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 58" width="24" height="58"><rect x="2" y="2" width="20" height="40" rx="4" fill="#1e293b" stroke="#374151" stroke-width="1"/><circle cx="12" cy="10" r="5" fill="#ef4444"/><circle cx="12" cy="22" r="5" fill="#f59e0b"/><circle cx="12" cy="34" r="5" fill="#22c55e"/><rect x="10" y="42" width="4" height="12" fill="#374151"/></svg>`
        },
        stop: {
            scale: 0.8,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="50" height="64"><polygon points="15,2 35,2 48,15 48,35 35,48 15,48 2,35 2,15" fill="#dc2626" stroke="#1e293b" stroke-width="1.5"/><text x="25" y="31" text-anchor="middle" fill="white" font-size="12" font-family="Arial" font-weight="bold">STOP</text></svg>`
        },
        balise: {
            scale: 0.8,
            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="50" height="64"><polygon points="25,3 47,46 3,46" fill="white" stroke="#1e293b" stroke-width="2"/><polygon points="25,11 41,43 9,43" fill="none" stroke="#e11d48" stroke-width="1.5"/></svg>`
        }
    };

    function initAgentFabricSketch() {
        const wrapper = document.getElementById('agent-sketch-wrapper');
        const canvasEl = document.getElementById('agent-sketch-pad');
        if (!wrapper || !canvasEl) return;

        const width = wrapper.clientWidth || wrapper.offsetWidth || 700;
        const height = wrapper.clientHeight || wrapper.offsetHeight || 320;

        if (agentFabricSketch) {
            agentFabricSketch.setWidth(width);
            agentFabricSketch.setHeight(height);
            agentFabricSketch.calcOffset();
            agentFabricSketch.renderAll();
            return;
        }

        canvasEl.width = width;
        canvasEl.height = height;

        agentFabricSketch = new fabric.Canvas('agent-sketch-pad', {
            width: width,
            height: height,
            isDrawingMode: true,
            backgroundColor: '#ffffff',
            selection: true,
        });
        agentFabricSketch.freeDrawingBrush.color = '#0f172a';
        agentFabricSketch.freeDrawingBrush.width = 3;
        agentFabricSketch.calcOffset();
    }

    function addAgentSketchSymbol(type) {
        if (!agentFabricSketch) initAgentFabricSketch();
        const sym = AGENT_SKETCH_SYMBOLS[type];
        if (!sym || !agentFabricSketch) return;

        fabric.loadSVGFromString(sym.svg, function(objects, options) {
            const group = fabric.util.groupSVGElements(objects, options);
            group.set({
                left: (agentFabricSketch.width / 2) - ((group.width * sym.scale) / 2),
                top: (agentFabricSketch.height / 2) - ((group.height * sym.scale) / 2),
                scaleX: sym.scale,
                scaleY: sym.scale,
                hasControls: true,
                hasBorders: true,
                cornerSize: 10,
                cornerColor: '#4f46e5',
                borderColor: '#4f46e5',
            });
            agentFabricSketch.add(group);
            agentFabricSketch.setActiveObject(group);
            setAgentSketchMode('select');
            agentFabricSketch.renderAll();
        });
    }

    function setAgentSketchMode(mode) {
        if (!agentFabricSketch) initAgentFabricSketch();
        if (!agentFabricSketch) return;
        const drawBtn = document.getElementById('agent-sketch-draw-btn');
        const moveBtn = document.getElementById('agent-sketch-move-btn');

        if (mode === 'draw') {
            agentFabricSketch.isDrawingMode = true;
            if (drawBtn) drawBtn.className = 'text-xs px-2.5 py-1 rounded-xl bg-indigo-600 text-white font-bold transition-all shadow-sm';
            if (moveBtn) moveBtn.className = 'text-xs px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-bold transition-all hover:bg-slate-200';
        } else {
            agentFabricSketch.isDrawingMode = false;
            if (drawBtn) drawBtn.className = 'text-xs px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-bold transition-all hover:bg-slate-200';
            if (moveBtn) moveBtn.className = 'text-xs px-2.5 py-1 rounded-xl bg-indigo-600 text-white font-bold transition-all shadow-sm';
        }
    }

    function deleteAgentSelectedObject() {
        if (!agentFabricSketch) return;
        const activeObj = agentFabricSketch.getActiveObject();
        if (activeObj) {
            agentFabricSketch.remove(activeObj);
            agentFabricSketch.discardActiveObject();
            agentFabricSketch.renderAll();
        }
    }

    function clearAgentFabricSketch() {
        if (agentFabricSketch) {
            agentFabricSketch.clear();
            agentFabricSketch.setBackgroundColor('#ffffff', agentFabricSketch.renderAll.bind(agentFabricSketch));
        }
    }

    function saveAgentFabricSketchData() {
        if (agentFabricSketch) {
            const dataInput = document.getElementById('agent-croquis-data');
            if (dataInput) dataInput.value = agentFabricSketch.toDataURL({ format: 'png' });
        }
    }

    function agentConstatUniqueLayout() {
        return {
            step: 1,
            setStep(s) {
                this.step = s;
                if (s === 4) {
                    setTimeout(() => {
                        initAgentFabricSketch();
                    }, 150);
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            nextStep() {
                if (this.step < 4) {
                    this.step++;
                    if (this.step === 4) {
                        setTimeout(() => {
                            initAgentFabricSketch();
                        }, 150);
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
            prevStep() {
                if (this.step > 1) {
                    this.step--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    }

    function addAgentPhotoField() {
        const container = document.getElementById('agent-photos-container');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mt-2';

        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'photos_plus[]';
        input.className = 'w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 py-2 flex-1';
        input.accept = 'image/*';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors shrink-0';
        btn.onclick = function() { div.remove(); };

        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-trash-can text-xs';
        btn.appendChild(icon);

        div.appendChild(input);
        div.appendChild(btn);
        container.appendChild(div);
    }
</script>
@endsection