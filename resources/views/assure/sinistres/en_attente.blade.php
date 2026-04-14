@extends('assure.layouts.template')

@section('title', 'Sinistres en attente')
@section('page-title', 'Sinistres en attente')

@section('content')
    <div style="width:95%;" class="mx-auto pb-12">

        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4 animate-in" style="--delay:0.1s">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-500">
                        <i class="fa-solid fa-eye text-base"></i>
                    </div>
                    Suivi de mes dossiers
                </h2>
                <p class="text-sm text-slate-500 mt-1">L'état d'avancement de toutes vos déclarations actives.</p>
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
                <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-inbox text-amber-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Aucun dossier actif</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Vous n'avez aucune déclaration en cours de traitement pour le moment.</p>
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
                            <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Référence</th>
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
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded inline-block w-fit">
                                            {{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}
                                        </span>
                                        @if($sinistre->constat && $sinistre->constat->methode_redaction === 'Amiable')
                                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 uppercase tracking-tighter w-fit">
                                                <i class="fa-solid fa-bolt-lightning text-[8px]"></i> Amiable
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-car-burst text-amber-500 text-xs"></i>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-slate-700">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    @if($sinistre->lieu)
                                        <div class="flex items-start gap-1.5 max-w-[180px]">
                                            <i class="fa-solid fa-location-dot text-red-400 text-[10px] mt-1 shrink-0"></i>
                                            <p class="text-[11px] font-medium text-slate-500 italic leading-tight">{{ $sinistre->lieu }}</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 truncate max-w-[180px]">{{ $sinistre->description ?? '—' }}</p>
                                    @endif
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
                                    @php
                                        $statusConfig = match($sinistre->status) {
                                            'en_attente' => ['label' => 'En attente', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-hourglass-half'],
                                            'en_cours' => ['label' => 'En cours', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-person-running'],
                                            'traite' => ['label' => 'Constat terminé', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'icon' => 'fa-file-circle-check'],
                                            default => ['label' => $sinistre->status, 'bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => 'fa-circle'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} text-xs font-bold rounded-full">
                                        <i class="fa-solid {{ $statusConfig['icon'] }} text-[10px]"></i> {{ $statusConfig['label'] }}
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
                                        @if($sinistre->constat && $sinistre->constat->methode_redaction === 'Amiable')
                                            <a href="{{ route('assure.sinistres.constat.download', $sinistre->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-all"
                                                title="Télécharger le constat">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('assure.sinistres.show', $sinistre->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                            <i class="fa-solid fa-eye text-xs"></i> Détails
                                        </a>
                                        <!-- @if($sinistre->constat && $sinistre->constat->methode_redaction === 'Amiable')
                                            <a href="{{ route('assure.sinistres.constat.download', $sinistre->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-all"
                                                title="Télécharger le constat">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        @endif -->
                                        <button type="button" onclick="confirmDelete({{ $sinistre->id }})"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg transition-colors border border-red-200">
                                            <i class="fa-solid fa-trash-can text-xs"></i> Supprimer
                                        </button>
                                        {{-- Formulaire de suppression caché --}}
                                        <form id="delete-form-{{ $sinistre->id }}"
                                            action="{{ route('assure.sinistres.destroy', $sinistre->id) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Confirmer la suppression ?',
                text: 'Cette action est irréversible. Votre déclaration sera définitivement supprimée.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

@endsection