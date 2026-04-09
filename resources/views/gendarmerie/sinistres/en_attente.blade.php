@extends('gendarmerie.layouts.template')
@section('title', 'Sinistres en attente')
@section('page-title', 'En attente')

@section('content')
    <div class="space-y-5 mx-auto" style="max-width:1800px;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half text-amber-500 text-sm"></i>
                    </div>
                    Sinistres en attente
                </h2>
                <p class="text-sm text-slate-500 mt-1">Déclarations assignées à votre brigade et en attente de traitement.
                </p>
            </div>
        </div>

        @if($sinistres->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
                <i class="fa-solid fa-inbox text-slate-300 text-4xl mb-4 block"></i>
                <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun sinistre en attente</h3>
                <p class="text-sm text-slate-400">Tous les sinistres assignés à votre brigade ont été traités.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Assuré
                            </th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Type</th>
                            <th
                                class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">
                                Description</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Photos
                            </th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Statut
                            </th>
                            <th
                                class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">
                                Date</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($sinistres as $index => $sinistre)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $sinistre->assure->name ?? '—' }}
                                            {{ $sinistre->assure->prenom ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-slate-600">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                                </td>
                                <td class="px-5 py-3.5 hidden md:table-cell">
                                    <p class="text-sm text-slate-500 truncate max-w-[180px]">{{ $sinistre->description ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($sinistre->photos && count($sinistre->photos) > 0)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg">
                                            <i class="fa-solid fa-images"></i> {{ count($sinistre->photos) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                        <i class="fa-solid fa-circle text-[6px]"></i> En attente
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 hidden lg:table-cell text-xs text-slate-500">
                                    {{ $sinistre->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('gendarmerie.sinistres.show', $sinistre->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                                            <i class="fa-solid fa-folder-open text-xs"></i> Sinistre
                                        </a>
                                        @if($sinistre->constat)
                                            <a href="{{ route('gendarmerie.sinistres.constat.show', $sinistre->id) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 hover:bg-violet-200 text-violet-700 text-xs font-bold rounded-lg transition-colors">
                                                <i class="fa-solid fa-file-lines text-xs"></i> Constat
                                            </a>
                                        @endif
                                        <a href="{{ route('gendarmerie.sinistres.constat.create', $sinistre->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                            <i class="fa-solid fa-file-pen text-xs"></i>
                                            {{ $sinistre->constat ? 'Modifier' : 'Faire le constat' }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection