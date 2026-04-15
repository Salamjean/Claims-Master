@extends('personnel.layouts.template')

@section('title', 'Dossier #' . $sinistre->numero_sinistre)
@section('page-title', 'Détail Sinistre')

@section('content')
    <div class="max-w-5xl mx-auto space-y-5 animate-in">

        {{-- Breadcrumb + retour --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('personnel.sinistres.index') }}"
                class="w-9 h-9 rounded-xl flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all shrink-0">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Dossier {{ $sinistre->numero_sinistre }}</h1>
                <p class="text-xs text-slate-400">Déclaré le {{ $sinistre->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="ml-auto">
                @php
                    $statusClasses = [
                        'soumis' => 'bg-amber-50 text-amber-600 border-amber-200',
                        'en_cours' => 'bg-blue-50 text-blue-600 border-blue-200',
                        'documents_soumis' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                        'expertise_en_cours' => 'bg-purple-50 text-purple-600 border-purple-200',
                        'cloture' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                        'rejete' => 'bg-red-50 text-red-600 border-red-200',
                    ];
                    $cls = $statusClasses[$sinistre->status] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border {{ $cls }} capitalize">
                    {{ str_replace('_', ' ', $sinistre->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Infos sinistre --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-shield text-[#1d3557]"></i> Informations du sinistre
                    </h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Type</p>
                            <p class="font-semibold text-slate-700 capitalize">
                                {{ str_replace('_', ' ', $sinistre->type_sinistre) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Numéro</p>
                            <p class="font-mono font-bold text-[#1d3557]">{{ $sinistre->numero_sinistre }}</p>
                        </div>
                        @if ($sinistre->lieu_sinistre)
                            <div class="col-span-2">
                                <p class="text-xs text-slate-400 mb-0.5">Lieu</p>
                                <p class="font-semibold text-slate-700">{{ $sinistre->lieu_sinistre }}</p>
                            </div>
                        @endif
                        @if ($sinistre->description)
                            <div class="col-span-2">
                                <p class="text-xs text-slate-400 mb-0.5">Description</p>
                                <p class="text-slate-600 bg-slate-50 rounded-xl p-3 text-sm italic border border-slate-100">
                                    « {{ $sinistre->description }} »
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Photos --}}
                @if ($sinistre->photos && count($sinistre->photos) > 0)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-images text-[#1d3557]"></i> Photos ({{ count($sinistre->photos) }})
                        </h2>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($sinistre->photos as $photo)
                                <a href="{{ Storage::url($photo) }}" target="_blank"
                                    class="w-24 h-24 rounded-xl overflow-hidden border border-slate-200 hover:opacity-80 transition-opacity block">
                                    <img src="{{ Storage::url($photo) }}" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Documents soumis --}}
                @if ($sinistre->documents && $sinistre->documents->count())
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-paperclip text-[#1d3557]"></i> Documents soumis
                        </h2>
                        <div class="space-y-2">
                            @foreach ($sinistre->documents as $doc)
                                <div
                                    class="flex items-center justify-between px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-file-pdf text-red-400 text-sm"></i>
                                        <span
                                            class="text-sm font-medium text-slate-700">{{ $doc->nom_document ?? 'Document' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($doc->statut === 'valide')
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">Validé</span>
                                        @elseif($doc->statut === 'rejete')
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-red-50 text-red-600 text-xs font-bold border border-red-200">Rejeté</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-xs font-bold border border-amber-200">En
                                                attente</span>
                                        @endif
                                        @if ($doc->chemin_fichier)
                                            <a href="{{ Storage::url($doc->chemin_fichier) }}" target="_blank"
                                                class="text-xs text-[#457b9d] hover:underline font-semibold">
                                                <i class="fa-solid fa-eye mr-1"></i>Voir
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Colonne latérale --}}
            <div class="space-y-5">

                {{-- Infos assuré --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user text-[#1d3557]"></i> Assuré
                    </h2>
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-11 h-11 rounded-xl bg-[#1d3557]/10 flex items-center justify-center text-[#1d3557] font-black shrink-0">
                            {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $sinistre->assure->name }}
                                {{ $sinistre->assure->prenom }}</p>
                            <p class="text-xs font-mono text-[#457b9d]">{{ $sinistre->assure->code_user }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        @if ($sinistre->assure->email)
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fa-solid fa-envelope text-slate-300 w-4 text-center"></i>
                                {{ $sinistre->assure->email }}
                            </div>
                        @endif
                        @if ($sinistre->assure->contact)
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fa-solid fa-phone text-slate-300 w-4 text-center"></i>
                                {{ $sinistre->assure->contact }}
                            </div>
                        @endif
                        @if ($sinistre->assure->adresse)
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fa-solid fa-location-dot text-slate-300 w-4 text-center"></i>
                                {{ $sinistre->assure->adresse }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Expert assigné --}}
                @if ($sinistre->expert)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-user-tie text-[#1d3557]"></i> Expert assigné
                        </h2>
                        <p class="font-semibold text-slate-700 text-sm">{{ $sinistre->expert->name }}
                            {{ $sinistre->expert->prenom }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $sinistre->expert->contact }}</p>
                    </div>
                @endif

                {{-- Garage assigné --}}
                @if ($sinistre->garage)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-wrench text-[#1d3557]"></i> Garage assigné
                        </h2>
                        <p class="font-semibold text-slate-700 text-sm">{{ $sinistre->garage->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $sinistre->garage->contact }}</p>
                    </div>
                @endif

            </div>
        </div>

    </div>
@endsection
