@extends('hopital.layouts.template')

@section('title', 'Tableau de bord Médical')

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
                        <span class="text-xs font-semibold text-green-300 uppercase tracking-wider">Établissement Actif</span>
                    </div>
                    <h1 class="text-2xl font-extrabold">Bonjour, <span class="text-rose-200">{{ $user->name }}</span> 🏥
                    </h1>
                    <p class="text-sm text-white/70 mt-1">SAMU / Hôpital &mdash;
                        {{ $user->adresse }} &mdash; {{ $user->contact ?? 'Contact non renseigné' }}</p>
                </div>
                <div class="text-right bg-white/10 border border-white/20 rounded-xl px-6 py-4">
                    <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Ambulance disponible</p>
                    <p class="text-xl font-bold flex items-center justify-end gap-1.5">
                        <i class="fa-solid fa-truck-medical text-rose-300"></i>
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
                <p class="text-[10px] text-slate-400 mt-1">Hôpital le plus proche désigné</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center flex flex-col justify-center">
                <p class="text-3xl font-extrabold text-emerald-600">{{ $totalHospitalises }}</p>
                <p class="text-xs font-bold text-slate-500 mt-1.5 uppercase tracking-widest">Blessés Pris en Charge</p>
                <p class="text-[10px] text-slate-400 mt-1">Évacués vers vos urgences</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center flex flex-col justify-center">
                <p class="text-3xl font-extrabold text-amber-500">{{ $urgencesEnAttente }}</p>
                <p class="text-xs font-bold text-slate-500 mt-1.5 uppercase tracking-widest">Urgences Actives</p>
                <p class="text-[10px] text-slate-400 mt-1">Sinistres en attente d'intervention</p>
            </div>
        </div>

        {{-- Table des alertes actives --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ openModal: false, completeUrl: '' }">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 animate-pulse">
                        <i class="fa-solid fa-heart-pulse text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 font-sans">Urgences actives nécessitant une prise en charge</h2>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Patient / Assuré</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Lieu & GPS</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Statut Médical</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions Urgences</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sinistres as $sinistre)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $sinistre->assure->name ?? '—' }} {{ $sinistre->assure->prenom ?? '' }}</p>
                                            <p class="text-xs text-slate-500">{{ $sinistre->assure->contact ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-slate-700 font-medium truncate max-w-[200px]" title="{{ $sinistre->lieu }}">{{ $sinistre->lieu ?? 'Lieu non renseigné' }}</p>
                                    @if($sinistre->latitude && $sinistre->longitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $sinistre->latitude }},{{ $sinistre->longitude }}" target="_blank"
                                            class="text-[10px] text-rose-600 font-bold hover:underline inline-flex items-center gap-1 mt-0.5">
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
                                            <i class="fa-solid fa-truck-medical text-[10px] animate-bounce"></i> Ambulance en route
                                        </span>
                                    @elseif ($sinistre->hospital_status === 'arrive')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-bed text-[10px]"></i> Patient aux urgences
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                            Clôturé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if ($sinistre->hospital_status === 'en_attente')
                                            <form action="{{ route('hopital.sinistres.dispatch', $sinistre->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md shadow-rose-600/10 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-truck-medical text-[10px]"></i> Dépêcher ambulance
                                                </button>
                                            </form>
                                        @elseif ($sinistre->hospital_status === 'ambulance_en_route')
                                            <form action="{{ route('hopital.sinistres.arrive', $sinistre->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-600/10 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-hospital-user text-[10px]"></i> Confirmer arrivée
                                                </button>
                                            </form>
                                        @elseif ($sinistre->hospital_status === 'arrive')
                                            <button @click="openModal = true; completeUrl = '{{ route('hopital.sinistres.complete', $sinistre->id) }}'" 
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/10 transition-all flex items-center gap-1.5">
                                                <i class="fa-solid fa-notes-medical text-[10px]"></i> Saisir Bilan & Clôturer
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center mx-auto mb-3 text-slate-300">
                                        <i class="fa-solid fa-house-medical text-lg"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucune urgence active enregistrée actuellement.</p>
                                    <p class="text-xs text-slate-400 mt-1">Les alertes géolocalisées des assurés s'afficheront ici en temps réel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Saisie Bilan Médical --}}
            <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" x-cloak>
                <div class="bg-white rounded-3xl border border-slate-100 max-w-lg w-full p-6 shadow-2xl transform transition-all" @click.away="openModal = false">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-file-medical text-rose-600 text-lg animate-pulse"></i>
                            Rapport Bilan Médical Initial
                        </h3>
                        <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form :action="completeUrl" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Gravité estimée des victimes <span class="text-red-500">*</span></label>
                            <select name="hospital_severity" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm" required>
                                <option value="">Sélectionner...</option>
                                <option value="leger">Léger (Premiers soins uniquement)</option>
                                <option value="grave">Grave (Hospitalisation requise)</option>
                                <option value="deces">Décès constaté</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Observations / Notes médicales</label>
                            <textarea name="hospital_notes" rows="4" placeholder="Ex: Traumatisme crânien léger, victime consciente..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm resize-none"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                            <button type="button" @click="openModal = false" class="px-5 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-50 border border-slate-200 text-xs">
                                Annuler
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/10">
                                Valider & Clôturer la prise en charge
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
