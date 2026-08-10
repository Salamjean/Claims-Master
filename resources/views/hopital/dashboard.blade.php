@extends('hopital.layouts.template')

@section('title', 'Tableau de bord Secours')

@section('content')
    <div class="space-y-6 mx-auto" style="width: 100%;">

        {{-- En-tête --}}
        <div class="relative rounded-2xl overflow-hidden text-white px-8 py-7"
            style="background: linear-gradient(135deg, #be123c 0%, #881337 100%);">
            <div
                style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.07),transparent);pointer-events:none;">
            </div>
            <div class="relative flex flex-wrap items-center justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs font-semibold text-green-300 uppercase tracking-wider">Caserne Active</span>
                    </div>
                    <h1 class="text-2xl font-extrabold">Bonjour, <span class="text-rose-200">{{ $user->name }}</span> 🚒
                    </h1>
                    <p class="text-sm text-white/70 mt-1">Sapeurs-Pompiers / GSPM &mdash;
                        {{ $user->adresse }} &mdash; {{ $user->contact ?? 'Contact non renseigné' }}</p>
                </div>
                <div class="text-right bg-white/10 border border-white/20 rounded-xl px-6 py-4">
                    <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Véhicule de secours disponible</p>
                    <p class="text-xl font-bold flex items-center justify-end gap-1.5">
                        <i class="fa-solid fa-truck-field text-rose-300"></i>
                        {{ $user->has_ambulance ? 'Oui' : 'Non' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Messages d'alerte / Actions success --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-base animate-bounce"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Compteurs --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center flex flex-col justify-center">
                <p class="text-3xl font-extrabold text-rose-600">{{ $totalAlerts }}</p>
                <p class="text-xs font-bold text-slate-500 mt-1.5 uppercase tracking-widest">Alertes Reçues</p>
                <p class="text-[10px] text-slate-400 mt-1">Caserne la plus proche désignée</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center flex flex-col justify-center">
                <p class="text-3xl font-extrabold text-emerald-600">{{ $totalHospitalises }}</p>
                <p class="text-xs font-bold text-slate-500 mt-1.5 uppercase tracking-widest">Blessés Secourus</p>
                <p class="text-[10px] text-slate-400 mt-1">Interventions de votre caserne</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center flex flex-col justify-center">
                <p class="text-3xl font-extrabold text-amber-500">{{ $urgencesEnAttente }}</p>
                <p class="text-xs font-bold text-slate-500 mt-1.5 uppercase tracking-widest">Alertes Actives</p>
                <p class="text-[10px] text-slate-400 mt-1">Sinistres en attente de secours</p>
            </div>
        </div>

        {{-- Table des alertes actives --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ openModal: false, completeUrl: '' }">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 animate-pulse">
                        <i class="fa-solid fa-truck-field text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 font-sans">Urgences actives nécessitant une intervention</h2>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-center">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Patient / Assuré</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Lieu & GPS</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Statut Secours</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Actions Intervention</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sinistres as $sinistre)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="w-8 h-8 rounded-xl {{ $sinistre->user_id ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($sinistre->declarant_nom ?? $sinistre->assure->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div class="text-left">
                                            @if(!$sinistre->user_id)
                                                {{-- Déclaration Témoin / Passant --}}
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 border border-amber-300 mb-0.5">
                                                    <i class="fa-solid fa-eye text-[9px]"></i> Alerte Témoin
                                                </span>
                                                <p class="text-sm font-bold text-slate-800">{{ $sinistre->declarant_nom ?? 'Passant Anonyme' }}</p>
                                                <p class="text-xs text-slate-500">{{ $sinistre->declarant_contact ?? 'Contact non fourni' }}</p>
                                            @else
                                                <p class="text-sm font-bold text-slate-800">{{ $sinistre->assure->name ?? '—' }} {{ $sinistre->assure->prenom ?? '' }}</p>
                                                <p class="text-xs text-slate-500">{{ $sinistre->assure->contact ?? 'N/A' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-slate-700 font-medium truncate max-w-[200px] mx-auto" title="{{ $sinistre->lieu }}">{{ $sinistre->lieu ?? 'Lieu non renseigné' }}</p>
                                    @if($sinistre->latitude && $sinistre->longitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $sinistre->latitude }},{{ $sinistre->longitude }}" target="_blank"
                                            class="text-[10px] text-rose-600 font-bold hover:underline inline-flex items-center justify-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-map-location-dot"></i> Voir itinéraire GPS
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($sinistre->hospital_status === 'en_attente')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Alerte reçue
                                        </span>
                                    @elseif ($sinistre->hospital_status === 'ambulance_en_route')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-truck-field text-[10px] animate-bounce"></i> Secours en route
                                        </span>
                                    @elseif ($sinistre->hospital_status === 'arrive')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-truck-field text-[10px]"></i> Blessés pris en charge
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                            Clôturé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        @if($sinistre->assigned_groupe_id)
                                            <span class="text-xs font-medium text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                                <i class="fa-solid fa-users text-slate-400 mr-1"></i>
                                                Équipe : {{ $sinistre->assignedGroupe->name ?? 'Équipe Inconnue' }}
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                                                <i class="fa-solid fa-clock text-amber-400 mr-1"></i> En attente de récupération par une équipe
                                            </span>
                                        @endif

                                        @if($sinistre->latitude && $sinistre->longitude)
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $sinistre->latitude }},{{ $sinistre->longitude }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors shadow-sm"
                                                title="Ouvrir la localisation dans Google Maps">
                                                <i class="fa-solid fa-map-location-dot"></i> Localisation
                                            </a>
                                        @elseif($sinistre->lieu)
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($sinistre->lieu) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors shadow-sm"
                                                title="Ouvrir le lieu dans Google Maps">
                                                <i class="fa-solid fa-map-location-dot"></i> Localisation
                                            </a>
                                        @else
                                            <button disabled class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed">
                                                <i class="fa-solid fa-map-location-dot"></i> Non localisé
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center mx-auto mb-3 text-slate-300">
                                        <i class="fa-solid fa-fire-extinguisher text-lg"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucune alerte de secours active enregistrée actuellement.</p>
                                    <p class="text-xs text-slate-400 mt-1">Les alertes géolocalisées des assurés s'afficheront ici en temps réel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Actualisation du tableau toutes les 10 secondes (sans recharger toute la page)
    setInterval(function() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Actualiser le tableau
                const newTable = doc.querySelector('.overflow-x-auto');
                const currentTable = document.querySelector('.overflow-x-auto');
                if (newTable && currentTable) {
                    currentTable.replaceWith(document.adoptNode(newTable));
                }
                
                // Actualiser les compteurs
                const newStats = doc.querySelectorAll('.grid-cols-1.md\\:grid-cols-3 > div');
                const currentStats = document.querySelectorAll('.grid-cols-1.md\\:grid-cols-3 > div');
                if (newStats.length === currentStats.length) {
                    for(let i=0; i<newStats.length; i++) {
                        currentStats[i].replaceWith(document.adoptNode(newStats[i]));
                    }
                }
            });
    }, 10000);
</script>
@endpush
