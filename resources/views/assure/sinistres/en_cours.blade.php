@extends('assure.layouts.template')

@section('title', 'Sinistres en cours')
@section('page-title', 'Dossiers en cours')

@section('content')
    <div style="width:95%;" class="mx-auto pb-12">

        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4 animate-in" style="--delay:0.1s">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-500">
                        <i class="fa-solid fa-person-running text-base"></i>
                    </div>
                    Dossiers pris en charge
                </h2>
                <p class="text-sm text-slate-500 mt-1">Vos sinistres actuellement gérés par un agent sur le terrain.
                </p>
            </div>
            <a href="{{ route('assure.sinistres.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm shadow-red-500/30">
                <i class="fa-solid fa-plus"></i> Nouvelle déclaration
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3 animate-in"
                style="--delay:0.15s">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-lg shrink-0"></i>
                <p class="text-sm font-medium text-emerald-700">{{ session('success') }}</p>
            </div>
        @endif

        @if($sinistres->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center animate-in"
                style="--delay:0.2s">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-person-running text-blue-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Aucun dossier en cours</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Aucun agent n'est actuellement en route pour l'une de vos déclarations.</p>
                <a href="{{ route('assure.sinistres.en_attente') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl transition-colors text-sm shadow-sm">
                    <i class="fa-solid fa-hourglass-half"></i> Voir mes attentes
                </a>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in" style="--delay:0.2s">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Référence</th>
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Type de
                                sinistre</th>
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Agent en charge</th>
                            <th
                                class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">
                                Photos</th>
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Statut
                            </th>
                            <th
                                class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">
                                Date</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($sinistres as $index => $sinistre)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-car-burst text-blue-500 text-xs"></i>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-slate-700">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-[10px] font-bold text-emerald-600 uppercase">
                                            {{ substr($sinistre->assignedAgent->name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 leading-tight">{{ $sinistre->assignedAgent->name ?? 'Agent assigné' }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium italic">
                                                {{ $sinistre->status === 'traite' ? 'Constat effectué' : 'En route pour le constat' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden sm:table-cell">
                                    @if($sinistre->photos && count($sinistre->photos) > 0)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-lg">
                                            <i class="fa-solid fa-images"></i> {{ count($sinistre->photos) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">Aucune</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($sinistre->status === 'traite')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                            <i class="fa-solid fa-file-circle-check text-[10px]"></i> Constat terminé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full animate-pulse">
                                            <i class="fa-solid fa-person-running text-[10px]"></i> En route
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <span class="text-xs text-slate-500">{{ $sinistre->created_at->format('d/m/Y à H:i') }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($sinistre->constat && $sinistre->constat->methode_redaction === 'Amiable')
                                            <a href="{{ route('assure.sinistres.constat.download', $sinistre->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl transition-all"
                                                title="Télécharger le constat">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('assure.sinistres.show', $sinistre->id) }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                                            <i class="fa-solid fa-eye text-xs"></i> 
                                            {{ $sinistre->status === 'traite' ? 'Voir détails' : 'Suivre l\'agent' }}
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

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .animate-in {
            opacity: 0;
            animation: fadeUp .5s cubic-bezier(.16, 1, .3, 1) forwards;
            animation-delay: var(--delay, 0s);
        }
    </style>

@endsection
