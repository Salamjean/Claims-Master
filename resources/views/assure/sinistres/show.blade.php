@extends('assure.layouts.template')

@section('title', 'Détails du sinistre')
@section('page-title', 'Détail sinistre')

@section('content')
    <div style="width:95%;" class="mx-auto pb-12">

        {{-- En-tête / Breadcrumb --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4 animate-in" style="--delay:0.1s">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-400 mb-2">
                    <a href="{{ route('assure.sinistres.historique') }}"
                        class="hover:text-slate-600 transition-colors">Historique</a>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-slate-600 font-semibold">Sinistre {{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</span>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500">
                        <i class="fa-solid fa-car-burst text-base"></i>
                    </div>
                    {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                </h2>
            </div>

            <div class="flex items-center gap-3">
                {{-- Bouton Documents Requis --}}
                @if($sinistre->documentsAttendus()->count() > 0)
                    <a href="{{ route('assure.sinistres.upload-docs', $sinistre->id) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm shadow-blue-600/20">
                        <i class="fa-solid fa-file-invoice text-sm"></i> Documents 
                        @if($sinistre->documentsAttendus()->where('status_client', 'pending')->count() > 0)
                            <span class="inline-flex items-center justify-center w-5 h-5 bg-red-400 text-white text-xs rounded-full ml-1">
                                {{ $sinistre->documentsAttendus()->where('status_client', 'pending')->count() }}
                            </span>
                        @endif
                    </a>
                @endif

                {{-- Bouton Supprimer (seulement si en_attente) --}}
                @if($sinistre->status === 'en_attente')
                    <form action="{{ route('assure.sinistres.destroy', $sinistre->id) }}" method="POST" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl text-sm transition-colors border border-red-200">
                            <i class="fa-solid fa-trash-can text-sm"></i> Supprimer
                        </button>
                    </form>
                @endif

                {{-- Bouton Constat Amiable (Téléchargement) --}}
                @if($sinistre->constat && $sinistre->constat->methode_redaction === 'Amiable')
                    <a href="{{ route('assure.sinistres.constat.download', $sinistre->id) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm shadow-emerald-600/20">
                        <i class="fa-solid fa-file-pdf text-sm"></i> Télécharger le Constat
                    </a>
                @endif

                <a href="{{ route('assure.sinistres.historique') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-600 font-semibold rounded-xl text-sm transition-colors border border-slate-200">
                    <i class="fa-solid fa-arrow-left text-sm"></i> Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Infos principales --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in"
                    style="--delay:0.2s">
                    <div class="h-1.5 bg-gradient-to-r from-slate-400 to-slate-200"></div>
                    <div class="p-6 md:p-8">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5">Informations générales
                        </h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wide w-40 shrink-0">Type</dt>
                                <dd class="text-sm font-semibold text-slate-800">
                                    {{ str_replace('_', ' ', $sinistre->type_sinistre) }}</dd>
                            </div>
                            <div
                                class="border-t border-slate-50 pt-4 flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4">
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wide w-40 shrink-0">
                                    Description</dt>
                                <dd class="text-sm text-slate-700 leading-relaxed">
                                    {{ $sinistre->description ?? 'Aucune description fournie.' }}</dd>
                            </div>
                            <div
                                class="border-t border-slate-50 pt-4 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wide w-40 shrink-0">Date de
                                    déclaration</dt>
                                <dd class="text-sm text-slate-700">{{ $sinistre->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            <div
                                class="border-t border-slate-50 pt-4 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wide w-40 shrink-0">
                                    Lieu de l'incident</dt>
                                <dd class="text-sm text-slate-700">
                                    <span id="location-display" class="inline-flex items-center gap-2">
                                        <i class="fa-solid fa-spinner fa-spin text-slate-300"></i>
                                        <span class="text-slate-400 text-xs italic">Récupération de l'adresse...</span>
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Photos --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in"
                    style="--delay:0.3s">
                    <div class="h-1.5 bg-gradient-to-r from-emerald-400 to-emerald-200"></div>
                    <div class="p-6 md:p-8">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5">
                            <i class="fa-solid fa-images mr-1.5 text-emerald-400"></i>
                            Photos jointes
                            @if($sinistre->photos)
                                <span
                                    class="ml-2 inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-600 text-xs rounded-full font-bold">{{ count($sinistre->photos) }}</span>
                            @endif
                        </h3>
                        @if($sinistre->photos && count($sinistre->photos) > 0)
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                @foreach($sinistre->photos as $photo)
                                    <a href="{{ Storage::url($photo) }}" target="_blank"
                                        class="group block rounded-xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition-shadow" style="height:80px;">
                                        <img src="{{ Storage::url($photo) }}" alt="Photo sinistre"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center py-10 text-center rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200">
                                <i class="fa-regular fa-image text-3xl text-slate-300 mb-3"></i>
                                <p class="text-sm text-slate-400 font-medium">Aucune photo jointe à cette déclaration.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Sidebar droite --}}
            <div class="space-y-5">

                {{-- Statut --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in"
                    style="--delay:0.2s">
                    <div class="p-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Statut de la déclaration
                        </h3>
                        @php
                            if ($sinistre->status === 'cloture') {
                                if ($sinistre->workflow_step === 'closed_validated') {
                                    $statusConfig = ['label' => 'Validé (Assurance)', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500', 'desc' => 'Cette déclaration a été validée et clôturée par votre assurance.', 'icon' => 'fa-check-double'];
                                } elseif ($sinistre->workflow_step === 'closed_rejected') {
                                    $statusConfig = ['label' => 'Rejeté (Assurance)', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'desc' => 'Cette déclaration a été rejetée et clôturée par votre assurance.', 'icon' => 'fa-ban'];
                                } else {
                                    $statusConfig = ['label' => 'Clôturé (Service)', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => 'bg-purple-500', 'desc' => 'Le constat a été traité et clôturé par le service compétent.', 'icon' => 'fa-file-shield'];
                                }
                            } else {
                                $statusConfig = match ($sinistre->status) {
                                    'en_attente' => ['label' => 'En attente', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-400', 'desc' => 'Votre déclaration a été enregistrée et est en attente d\'être prise en charge.', 'icon' => 'fa-hourglass-half'],
                                    'en_cours' => [
                                        'label' => $sinistre->assigned_agent_id ? 'Agent en route' : 'En cours',
                                        'bg' => 'bg-blue-50', 
                                        'text' => 'text-blue-700', 
                                        'border' => 'border-blue-200', 
                                        'dot' => 'bg-blue-500', 
                                        'desc' => $sinistre->assigned_agent_id ? 'Un agent a récupéré votre dossier et est actuellement en route vers le lieu du sinistre.' : 'Votre déclaration est en cours de traitement par le service compétent.', 
                                        'icon' => 'fa-person-running'
                                    ],
                                    'traite' => [
                                        'label' => 'Constat terminé', 
                                        'bg' => 'bg-indigo-50', 
                                        'text' => 'text-indigo-700', 
                                        'border' => 'border-indigo-200', 
                                        'dot' => 'bg-indigo-500', 
                                        'desc' => $sinistre->workflow_step === 'manager_review' 
                                            ? 'Le constat est terminé et vos documents sont reçus. Votre dossier est en cours de révision par l\'assureur.' 
                                            : 'L\'agent a terminé le constat sur le terrain. Veuillez vous assurer d\'avoir transmis tous les documents requis pour la suite.', 
                                        'icon' => 'fa-file-circle-check'
                                    ],
                                    default => ['label' => $sinistre->status, 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'dot' => 'bg-slate-400', 'desc' => 'Statut inconnu.', 'icon' => 'fa-circle'],
                                };
                            }
                        @endphp
                        <div class="p-4 rounded-2xl border {{ $statusConfig['bg'] }} {{ $statusConfig['border'] }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $statusConfig['dot'] }} animate-pulse"></span>
                                    <span class="text-sm font-bold {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</span>
                                </div>
                                <i class="fa-solid {{ $statusConfig['icon'] ?? 'fa-circle' }} {{ $statusConfig['text'] }} opacity-50 text-sm"></i>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed font-medium">{{ $statusConfig['desc'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Service & Agent assigné --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in"
                    style="--delay:0.25s">
                    <div class="p-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Intervention personnel
                        </h3>
                        
                        {{-- Service --}}
                        <div class="flex items-center gap-3 {{ $sinistre->assignedAgent ? 'mb-4 pb-4 border-b border-slate-50' : '' }}">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-building-shield text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Service</p>
                                <p class="text-sm font-bold text-slate-800">{{ $sinistre->service->name ?? 'Non assigné' }}</p>
                            </div>
                        </div>

                        {{-- Agent --}}
                        @if($sinistre->assignedAgent)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-user-shield text-emerald-500 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Agent en charge</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $sinistre->assignedAgent->name }}</p>
                                    </div>
                                </div>
                                @if($sinistre->status === 'en_cours')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-black rounded-lg uppercase">En route</span>
                                @endif
                            </div>
                        @endif

                        @if(!$sinistre->service && !$sinistre->assignedAgent)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl">
                                <i class="fa-solid fa-circle-question text-slate-300 text-xl"></i>
                                <p class="text-sm text-slate-500">En attente d'affectation d'un agent.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Unités à proximité --}}
                @if($sinistre->nearby_units && count($sinistre->nearby_units) > 0)
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in"
                        style="--delay:0.3s">
                        <div class="p-6">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Unités à proximité (Alrtées)</h3>
                            <div class="space-y-3">
                                @foreach($sinistre->nearby_units as $unit)
                                    @php
                                        $icon = $unit['role'] === 'police' ? 'fa-building-shield' : ($unit['role'] === 'gendarmerie' ? 'fa-shield-halved' : 'fa-user-shield');
                                        $color = $unit['role'] === 'police' ? 'text-blue-500' : ($unit['role'] === 'gendarmerie' ? 'text-emerald-500' : 'text-indigo-500');
                                        $bgColor = $unit['role'] === 'police' ? 'bg-blue-50' : ($unit['role'] === 'gendarmerie' ? 'bg-emerald-50' : 'bg-indigo-50');
                                    @endphp
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 border border-slate-100">
                                        <div class="flex items-center gap-3 w-full overflow-hidden">
                                            <div class="w-8 h-8 rounded-lg {{ $bgColor }} {{ $color }} flex items-center justify-center text-xs shrink-0">
                                                <i class="fa-solid {{ $icon }}"></i>
                                            </div>
                                            <div class="overflow-hidden flex-1">
                                                <p class="text-[11px] font-bold text-slate-800 truncate">
                                                    {{ $unit['name'] }}
                                                </p>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-[8px] font-bold text-slate-400 uppercase">{{ $unit['role'] }}</span>
                                                    @if($unit['role'] === 'agent' && isset($unit['parent_service']))
                                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                        <span class="text-[8px] font-medium text-slate-400 truncate">{{ $unit['parent_service'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-[10px] font-black text-blue-600">{{ $unit['distance'] }} km</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-4 text-[9px] text-slate-400 leading-relaxed italic font-medium">
                                <i class="fa-solid fa-circle-info mr-1 text-blue-400"></i> Ces unités ont été notifiées lors de votre déclaration.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Danger zone (suppression) --}}
                @if($sinistre->status === 'en_attente')
                    <div class="bg-white rounded-3xl border border-red-100 shadow-sm overflow-hidden animate-in"
                        style="--delay:0.3s">
                        <div class="p-6">
                            <h3 class="text-xs font-bold text-red-400 uppercase tracking-wider mb-2">Zone dangereuse</h3>
                            <p class="text-xs text-slate-500 mb-4 leading-relaxed">Cette déclaration est encore en attente. Vous
                                pouvez l'annuler, ce qui la supprimera définitivement.</p>
                            <button onclick="confirmDelete()"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-sm transition-colors shadow-sm shadow-red-500/20">
                                <i class="fa-solid fa-trash-can"></i> Annuler ma déclaration
                            </button>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>

    @if($sinistre->status === 'en_attente')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete() {
                Swal.fire({
                    title: 'Confirmer la suppression ?',
                    text: 'Cette action est irréversible. Votre déclaration de sinistre sera définitivement supprimée.',
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
                        document.getElementById('delete-form').submit();
                    }
                });
            }
        </script>
    @endif

    {{-- Géocodage inversé : coordonnées → adresse --}}
    <script>
        (function() {
            const lat = {{ $sinistre->latitude }};
            const lng = {{ $sinistre->longitude }};
            const display = document.getElementById('location-display');

            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, {
                headers: { 'Accept-Language': 'fr' }
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.display_name) {
                    display.innerHTML = `
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-red-400 mt-0.5 shrink-0"></i>
                            <span class="text-sm text-slate-700 leading-snug">${data.display_name}</span>
                        </div>`;
                } else {
                    display.innerHTML = `<span class="text-slate-400 italic text-xs">Adresse introuvable (${lat}, ${lng})</span>`;
                }
            })
            .catch(() => {
                display.innerHTML = `<span class="text-slate-400 italic text-xs">Lat : ${lat}, Lng : ${lng}</span>`;
            });
        })();
    </script>

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