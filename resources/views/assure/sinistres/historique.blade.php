@extends('assure.layouts.template')

@section('title', 'Historique des sinistres')
@section('page-title', 'Historique')

@section('content')
    <div style="width:95%;" class="mx-auto pb-12">

        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4 animate-in" style="--delay:0.1s">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-500">
                        <i class="fa-solid fa-clock-rotate-left text-base"></i>
                    </div>
                    Historique des sinistres
                </h2>
                <p class="text-sm text-slate-500 mt-1">L'ensemble de vos déclarations de sinistres.</p>
            </div>
            <a href="{{ route('assure.sinistres.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm shadow-red-500/30">
                <i class="fa-solid fa-plus"></i> Nouvelle déclaration
            </a>
        </div>

        @if(isset($tousSinistres))
            {{-- Statistiques rapides globales --}}
            @php
                $enAttente = $tousSinistres->where('status', 'en_attente')->count();
                $enCours = $tousSinistres->where('status', 'en_cours')->count();
                $cloture = $tousSinistres->whereIn('status', ['traite', 'cloture'])->count();
            @endphp
            <div class="grid grid-cols-3 gap-4 mb-6 animate-in" style="--delay:0.15s">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
                    <p class="text-2xl font-extrabold text-amber-500">{{ $enAttente }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">En attente</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
                    <p class="text-2xl font-extrabold text-blue-500">{{ $enCours }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">En cours</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
                    <p class="text-2xl font-extrabold text-emerald-500">{{ $cloture }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Dossiers Traités/Clôturés</p>
                </div>
            </div>
        @endif

        @if($sinistres->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center animate-in"
                style="--delay:0.2s">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-folder-open text-blue-300 text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Aucun sinistre traité</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Vous n'avez actuellement aucun sinistre traité ou clôturé.</p>
                <a href="{{ route('assure.sinistres.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors text-sm shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i> Déclarer un sinistre
                </a>
            </div>
        @else

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in" style="--delay:0.2s">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Type de
                                sinistre</th>
                            <th
                                class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">
                                Description</th>
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Service
                                assigné</th>
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
                            @php
                                if ($sinistre->status === 'cloture') {
                                    if ($sinistre->workflow_step === 'closed_validated') {
                                        $statusConfig = ['label' => 'Validé (Assurance)', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'];
                                    } elseif ($sinistre->workflow_step === 'closed_rejected') {
                                        $statusConfig = ['label' => 'Rejeté (Assurance)', 'bg' => 'bg-red-100', 'text' => 'text-red-700'];
                                    } else {
                                        $statusConfig = ['label' => 'Clôturé (Service)', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'];
                                    }
                                } else {
                                    $statusConfig = match ($sinistre->status) {
                                        'en_attente' => ['label' => 'En attente', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                                        'en_cours' => ['label' => 'En cours', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                        'traite' => ['label' => 'Constat terminé', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                                        default => ['label' => $sinistre->status, 'bg' => 'bg-slate-100', 'text' => 'text-slate-700'],
                                    };
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-car-burst text-slate-400 text-xs"></i>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-slate-700">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <p class="text-sm text-slate-500 truncate max-w-[180px]">{{ $sinistre->description ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @if($sinistre->service)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-building-shield text-blue-400 text-xs"></i>
                                            <span class="text-sm text-slate-600 font-medium">{{ $sinistre->service->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400 italic">Non assigné</span>
                                    @endif
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
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} text-xs font-bold rounded-full">
                                        <i class="fa-solid fa-circle text-[6px]"></i> {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <span class="text-xs text-slate-500">{{ $sinistre->created_at->format('d/m/Y à H:i') }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($sinistre->documentsAttendus()->count() > 0)
                                            <a href="{{ route('assure.sinistres.upload-docs', $sinistre->id) }}"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm shadow-blue-600/20">
                                                <i class="fa-solid fa-file-invoice text-xs"></i> Docs
                                                @if($sinistre->documentsAttendus()->where('status_client', 'pending')->count() > 0)
                                                    <span
                                                        class="inline-flex items-center justify-center w-4 h-4 bg-red-400 text-white text-[10px] rounded-full ml-1">
                                                        {{ $sinistre->documentsAttendus()->where('status_client', 'pending')->count() }}
                                                    </span>
                                                @endif
                                            </a>
                                        @endif
                                        <a href="{{ route('assure.sinistres.show', $sinistre->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                            <i class="fa-solid fa-eye text-xs"></i> Détails
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