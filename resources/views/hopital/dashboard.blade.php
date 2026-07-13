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

        {{-- Table des alertes --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                        <i class="fa-solid fa-bell text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Déclarations et blessés à proximité</h2>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Patient / Assuré</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Type de Sinistre</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Prise en charge</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Statut Sinistre</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Date & Heure</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sinistres as $sinistre)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $sinistre->assure->name ?? '—' }} {{ $sinistre->assure->prenom ?? '' }}</p>
                                            <p class="text-xs text-slate-500">{{ $sinistre->assure->contact ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        <i class="fa-solid fa-car-burst text-[10px]"></i>
                                        {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sinistre->constat && $sinistre->constat->hospital_id == $user->id)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Évacué vers vos urgences
                                        </span>
                                    @elseif($sinistre->nearest_hospital_id == $user->id)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-location-crosshairs text-[10px]"></i>
                                            Plus proche désigné
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($sinistre->status === 'en_attente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            En attente
                                        </span>
                                    @elseif ($sinistre->status === 'en_cours')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            En cours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Traité
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ $sinistre->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center mx-auto mb-3 text-slate-300">
                                        <i class="fa-solid fa-house-medical text-lg"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucune déclaration d'accident enregistrée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
