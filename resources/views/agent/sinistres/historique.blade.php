@extends('agent.layouts.template')
@section('title', 'Historique des sinistres')
@section('page-title', 'Historique')

@section('content')
    <div class="space-y-5 mx-auto" style="width: 100%;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center">
                        <i class="fa-solid fa-history text-blue-500 text-sm"></i>
                    </div>
                    Historique des sinistres
                </h2>
                <p class="text-sm text-slate-500 mt-1">Tous les sinistres assignés à votre service.</p>
            </div>
        </div>

        @if($sinistres->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
                <i class="fa-solid fa-inbox text-slate-300 text-4xl mb-4 block"></i>
                <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun historique</h3>
                <p class="text-sm text-slate-400">Aucun dossier à afficher.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Réf</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Assuré</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Type</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Agent</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Statut</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Date</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($sinistres as $sinistre)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2" style="display:flex; justify-content: center;">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                                {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-semibold text-slate-700">{{ $sinistre->assure->name . ' ' . $sinistre->assure->prenom ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm font-bold text-slate-700 uppercase text-center">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</td>
                                    <td class="px-5 py-3.5">
                                        @if($sinistre->assignedAgent)
                                            <div class="flex items-center gap-2" style="display:flex; justify-content: center;">
                                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                                    {{ strtoupper(substr($sinistre->assignedAgent->name, 0, 1)) }}
                                                </div>
                                                <span class="text-xs text-slate-600 truncate max-w-[100px]">{{ $sinistre->assignedAgent->name . ' ' . $sinistre->assignedAgent->prenom }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php
                                            $s = match ($sinistre->status) {
                                                'en_attente' => ['En attente', 'bg-amber-100 text-amber-700'],
                                                'en_cours' => ['En cours', 'bg-blue-100 text-blue-700'],
                                                'cloture' => ['Clôturé', 'bg-emerald-100 text-emerald-700'],
                                                default => [$sinistre->status, 'bg-slate-100 text-slate-700'],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 {{ $s[1] }} text-xs font-bold rounded-full">
                                            <i class="fa-solid fa-circle text-[6px]"></i> {{ $s[0] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 text-center">
                                        {{ $sinistre->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('agent.sinistres.show', $sinistre->id) }}"
                                                class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg transition-colors" title="Détails">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if($sinistre->constat)
                                                <a href="{{ route('agent.sinistres.constat.show', $sinistre->id) }}"
                                                    class="p-2 bg-violet-50 hover:bg-violet-100 text-violet-600 rounded-lg transition-colors" title="Constat">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
