@extends('agent.layouts.template')

@section('title', 'Flux des Sinistres')

@section('content')
    <div class="space-y-6 mx-auto" style="width: 100%;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-briefcase text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Flux des Sinistres</h1>
                    <p class="text-sm text-slate-500 mt-1">Vos dossiers récupérés en cours de traitement.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm">
                    {{ $sinistres->count() }} Dossiers
                </span>
            </div>
        </div>

        {{-- Table des sinistres --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Réf</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Assuré</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Type</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Statut</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Date Récup.</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($sinistres as $sinistre)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 text-center">
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">{{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-200">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="text-left">
                                            <p class="text-sm font-bold text-slate-700">{{ $sinistre->assure->name . ' ' . $sinistre->assure->prenom ?? '—' }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium">{{ $sinistre->assure->contact ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-xs font-bold text-slate-600 uppercase">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $s = match ($sinistre->status) {
                                            'en_attente'         => ['Pris en charge',       'bg-amber-100 text-amber-700'],
                                            'en_cours'           => ['En cours',              'bg-blue-100 text-blue-700'],
                                            'constat_terrain_ok' => ['Terrain \u2705 — \u00c0 r\u00e9diger', 'bg-violet-100 text-violet-700'],
                                            'traite'             => ['Trait\u00e9',                'bg-emerald-100 text-emerald-700'],
                                            'cloture'            => ['Cl\u00f4tur\u00e9',              'bg-slate-100 text-slate-600'],
                                            default              => [$sinistre->status,        'bg-slate-100 text-slate-700'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $s[1] }} text-[10px] font-extrabold rounded-full uppercase tracking-wider">
                                        <i class="fa-solid fa-circle text-[6px]"></i> {{ $s[0] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500 text-center font-medium">
                                    {{ $sinistre->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('agent.sinistres.show', $sinistre->id) }}"
                                            class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                                            <i class="fa-solid fa-eye text-xs"></i> Détails
                                        </a>

                                        @if($sinistre->status === 'en_cours')
                                            {{-- Pas encore de constat terrain : proposer de le faire --}}
                                            <a href="{{ route('agent.sinistres.constat.create', $sinistre->id) }}"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-100">
                                                <i class="fa-solid fa-map-location-dot text-xs"></i>
                                                Constat terrain
                                            </a>

                                        @elseif($sinistre->status === 'constat_terrain_ok' && !($sinistre->constat?->redaction_validee))
                                            {{-- Terrain fait, rédaction à faire --}}
                                            <a href="{{ route('agent.sinistres.redaction', $sinistre->id) }}"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-violet-100 animate-pulse">
                                                <i class="fa-solid fa-file-pen text-xs"></i>
                                                Rédiger le constat
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-20">
                                        <i class="fa-solid fa-folder-open text-6xl mb-4"></i>
                                        <p class="text-xl font-bold">Aucun dossier en cours</p>
                                        <p class="text-sm">Récupérez des sinistres depuis le tableau de bord pour les traiter ici.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
