@extends('assure.layouts.template')

@section('title', 'Mes Assurances')
@section('page-title', 'Mes Assurances')

@section('content')
@php
    $totalCount = $contrats->count();
    $activeCount = 0;
    $expiredCount = 0;
    $aiVerifiedCount = 0;

    foreach ($contrats as $c) {
        $isExp = $c->date_fin && \Carbon\Carbon::parse($c->date_fin)->isPast() && !\Carbon\Carbon::parse($c->date_fin)->isToday();
        if ($isExp) {
            $expiredCount++;
        } else {
            $activeCount++;
        }
        if ($c->attestation_ai_status === 'valid') {
            $aiVerifiedCount++;
        }
    }
@endphp

<div x-data="{ 
    search: '', 
    filter: 'all', 
    showRenewModal: false, 
    renewUrl: '', 
    renewVehicleName: '', 
    fileName: '', 
    openRenewModal(url, name) { 
        this.renewUrl = url; 
        this.renewVehicleName = name; 
        this.fileName = ''; 
        this.showRenewModal = true; 
    } 
}" class="space-y-8 pb-12">

    {{-- EN-TÊTE PAGE --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight font-['Space_Grotesk']">
                Mes Assurances Automobile
            </h1>
            <p class="text-slate-500 text-sm mt-1">Gérez vos véhicules et vos contrats d'assurance</p>
        </div>
        <a href="{{ route('assure.contrats.create') }}"
            class="inline-flex items-center justify-center gap-2.5 px-5 py-3 bg-gradient-to-r from-blue-900 to-indigo-900 hover:from-blue-800 hover:to-indigo-800 text-white font-extrabold text-sm rounded-xl transition-all shadow-lg shadow-blue-900/20 hover:scale-[1.02] active:scale-[0.98]">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Ajouter une assurance</span>
        </a>
    </div>

    @if($contrats->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm max-w-2xl mx-auto">
            <div class="w-24 h-24 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner border border-blue-100/50">
                <i class="fa-solid fa-car-shield text-4xl animate-bounce"></i>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight font-['Space_Grotesk']">Aucune assurance enregistrée</h3>
            <p class="text-slate-500 text-sm max-w-md mx-auto mt-2 mb-8 leading-relaxed">
                Vous n'avez pas encore ajouté de véhicule ou de contrat d'assurance automobile. Enregistrez votre premier contrat pour bénéficier du scan automatique par IA et faciliter vos déclarations de sinistres.
            </p>
            <a href="{{ route('assure.contrats.create') }}"
                class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-blue-600/25 transition-all">
                <i class="fa-solid fa-shield-plus"></i>
                <span>Ajouter ma première assurance</span>
            </a>
        </div>
    @else

        {{-- BARRE DE RECHERCHE ET FILTRES --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            {{-- Input de recherche --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" x-model="search" placeholder="Rechercher par plaque, marque, modèle, contrat..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <button x-show="search.length > 0" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                </button>
            </div>

            {{-- Boutons de filtrage --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                    class="px-4 py-2 rounded-xl text-xs transition-all whitespace-nowrap">
                    Toutes ({{ $totalCount }})
                </button>
                <button @click="filter = 'active'"
                    :class="filter === 'active' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                    class="px-4 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Actives ({{ $activeCount }})
                </button>
                <button @click="filter = 'expired'"
                    :class="filter === 'expired' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                    class="px-4 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    Expirées ({{ $expiredCount }})
                </button>
            </div>
        </div>

        {{-- GRILLE DES CARTES CONTRAT (COMPACTE) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($contrats as $contrat)
                @php
                    $isExpired = $contrat->date_fin && \Carbon\Carbon::parse($contrat->date_fin)->isPast() && !\Carbon\Carbon::parse($contrat->date_fin)->isToday();
                    $dateFinFormatted = $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : null;
                    $searchTerms = strtolower($contrat->marque . ' ' . $contrat->modele . ' ' . $contrat->plaque . ' ' . $contrat->numero_contrat . ' ' . ($contrat->assureur->name ?? $contrat->nom_assureur ?? ''));
                @endphp

                <div x-show="(filter === 'all' || (filter === 'active' && !{{ $isExpired ? 'true' : 'false' }}) || (filter === 'expired' && {{ $isExpired ? 'true' : 'false' }})) && ('{{ $searchTerms }}'.includes(search.toLowerCase().trim()))"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg hover:border-blue-300/80 transition-all duration-200 flex flex-col overflow-hidden group">
                    
                    {{-- EN-TÊTE DE CARTE COMPACT --}}
                    <div class="p-4 border-b border-slate-100 relative bg-gradient-to-b from-slate-50/70 to-white">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            {{-- Plaque d'immatriculation --}}
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-900 text-white rounded-lg shadow-inner border border-slate-800 font-mono tracking-wider text-[11px] font-bold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                <span>{{ $contrat->plaque }}</span>
                            </div>

                            {{-- Badges de statut --}}
                            <div class="flex items-center gap-1 flex-wrap justify-end">
                                @if($contrat->attestation_ai_status === 'valid')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/80 text-[9px] font-extrabold" title="IA Certifié">
                                        <i class="fa-solid fa-robot text-indigo-500"></i>
                                        <span>IA</span>
                                    </span>
                                @endif

                                @if($isExpired)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200/80 text-[9px] font-extrabold">
                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                        <span>Expirée</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[9px] font-extrabold">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Actif</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Marque & Modèle du véhicule --}}
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-sm shadow-sm shadow-blue-500/20 shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-car"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-slate-800 truncate leading-tight">
                                    {{ $contrat->marque }} {{ $contrat->modele }}
                                </h3>
                                <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">
                                    {{ $contrat->type_vehicule }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- CORPS DE CARTE COMPACT --}}
                    <div class="p-4 space-y-2 flex-1 bg-white">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Police N°</span>
                            <span class="font-bold text-slate-700 font-mono bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">
                                {{ $contrat->numero_contrat }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Assureur</span>
                            <span class="font-bold text-blue-900 truncate max-w-[130px]">
                                {{ $contrat->assureur->name ?? $contrat->nom_assureur ?? 'Non spécifié' }}
                            </span>
                        </div>

                        {{-- Date d'Expiration / Échéance --}}
                        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-100">
                            <span class="text-slate-400 font-medium">Échéance</span>
                            @if($dateFinFormatted)
                                @if($isExpired)
                                    <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200/60 text-[10px]">
                                        {{ $dateFinFormatted }}
                                    </span>
                                @else
                                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60 text-[10px]">
                                        {{ $dateFinFormatted }}
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-400 italic text-[10px]">Non renseignée</span>
                            @endif
                        </div>

                        {{-- DOCUMENTS JUSTIFICATIFS --}}
                        <div class="pt-2">
                            <div class="flex flex-wrap gap-1">
                                @if($contrat->document_pdf)
                                    <a href="{{ asset('storage/' . $contrat->document_pdf) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-lg border border-slate-200 transition-colors">
                                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                                        <span>Contrat</span>
                                    </a>
                                @endif

                                @if($contrat->attestation_assurance)
                                    <a href="{{ asset('storage/' . $contrat->attestation_assurance) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-lg border border-slate-200 transition-colors">
                                        <i class="fa-solid fa-id-card text-blue-600"></i>
                                        <span>Attestation</span>
                                    </a>
                                @endif

                                @if($contrat->carte_grise)
                                    <a href="{{ asset('storage/' . $contrat->carte_grise) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-lg border border-slate-200 transition-colors">
                                        <i class="fa-solid fa-file-invoice text-teal-600"></i>
                                        <span>C. Grise</span>
                                    </a>
                                @endif

                                @if($contrat->visite_technique)
                                    <a href="{{ asset('storage/' . $contrat->visite_technique) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-lg border border-slate-200 transition-colors">
                                        <i class="fa-solid fa-clipboard-check text-orange-500"></i>
                                        <span>Visite</span>
                                    </a>
                                @endif

                                @if($contrat->permis_conduire)
                                    <a href="{{ asset('storage/' . $contrat->permis_conduire) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-lg border border-slate-200 transition-colors">
                                        <i class="fa-solid fa-address-card text-purple-600"></i>
                                        <span>Permis</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- PIED DE CARTE / ACTIONS COMPACT --}}
                    <div class="p-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2">
                        @if(!$isExpired)
                            <a href="{{ route('assure.sinistres.create', ['contrat_id' => $contrat->id]) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-[11px] rounded-lg shadow-sm shadow-orange-500/20 transition-all">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                <span>Déclarer sinistre</span>
                            </a>
                        @else
                            <button type="button"
                                @click="openRenewModal('{{ route('assure.contrats.renew', $contrat) }}', '{{ $contrat->plaque }} ({{ $contrat->marque }} {{ $contrat->modele }})')"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-[11px] rounded-lg shadow-sm shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                <span>Renouveler avec l'IA</span>
                            </button>
                        @endif

                        <form action="{{ route('assure.contrats.destroy', $contrat) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete(this)"
                                class="w-7 h-7 inline-flex items-center justify-center bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 rounded-lg transition-all shadow-sm"
                                title="Supprimer ce contrat">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- MODAL POP-UP DE RENOUVELLEMENT IA --}}
    <div x-show="showRenewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRenewModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="showRenewModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showRenewModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 p-6 md:p-8">
                
                <form :action="renewUrl" method="POST" enctype="multipart/form-data" id="pop-renew-form">
                    @csrf
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-sm">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-800">Renouvellement d'assurance</h3>
                                <p class="text-xs text-indigo-600 font-bold" x-text="renewVehicleName"></p>
                            </div>
                        </div>
                        <button type="button" @click="showRenewModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div class="p-4 bg-indigo-50/70 border border-indigo-100 rounded-2xl mb-6">
                        <p class="text-xs text-indigo-900 leading-relaxed font-medium">
                            <i class="fa-solid fa-circle-info mr-1 text-indigo-600"></i>
                            Fournissez uniquement la nouvelle <b>Attestation d'Assurance</b>. Notre IA va scanner le document, vérifier l'immatriculation et mettre à jour la nouvelle date d'échéance.
                        </p>
                    </div>

                    <div class="space-y-3 mb-6">
                        <label class="block text-sm font-bold text-slate-700">Nouvelle Attestation d'Assurance (Format ASACI) <span class="text-red-500">*</span></label>
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-2xl p-6 text-center cursor-pointer transition-colors bg-slate-50/50 group">
                            <input type="file" name="attestation_assurance" id="pop_attestation_assurance" required accept="image/*,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-12 h-12 rounded-2xl bg-white text-indigo-600 shadow-md border border-slate-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700 max-w-xs truncate" x-text="fileName || 'Cliquez ou déposez la nouvelle attestation'"></p>
                                <p class="text-[10px] text-slate-400">PDF, JPG, PNG jusqu'à 5 Mo</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showRenewModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span>Analyser & Mettre à jour</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Supprimer cette assurance ?',
                text: "Cette action est irréversible et supprimera définitivement le contrat et les documents associés.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const popForm = document.getElementById('pop-renew-form');
            if (popForm) {
                popForm.addEventListener('submit', function () {
                    Swal.fire({
                        title: 'Analyse IA en cours...',
                        html: 'Nous numérisons la nouvelle attestation (Format ASACI) et mettons à jour la date d\'échéance.<br><br><b>Veuillez patienter quelques instants...</b>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            }
        });
    </script>
@endpush