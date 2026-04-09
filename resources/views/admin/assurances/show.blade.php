@extends('admin.layouts.template')

@section('title', 'Détails — ' . $user->name)
@section('page-title', 'Détails assurance')

@section('content')

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shrink-0"
                style="background: linear-gradient(135deg, #243a8f, #7cb604);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $user->name }}</h1>
                <p class="text-sm text-slate-400 mt-0.5">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full"
                style="background:rgba(124,182,4,0.12); color:#5a8a03;">
                <i class="fa-solid fa-shield-halved mr-1"></i> Assurance
            </span>
            <a href="{{ route('admin.assurances.index') }}"
                class="flex items-center gap-2 text-sm text-slate-500 hover:text-primary border border-slate-200 hover:border-primary/30 px-4 py-2 rounded-xl transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- COLONNE PRINCIPALE --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Informations générales --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(36,58,143,0.1)">
                        <i class="fa-solid fa-building text-xs" style="color:#243a8f"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700">Informations générales</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Nom de la compagnie</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $user->name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Représentant / Prénom</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $user->prenom ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Adresse email</p>
                        <p class="text-sm font-semibold text-slate-800">
                            <a href="mailto:{{ $user->email }}" class="hover:text-primary transition-colors">
                                {{ $user->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Contact téléphonique</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $user->contact ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Commune</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $user->commune ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Adresse physique</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $user->adresse ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Inscrit le</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ $user->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Documents légaux --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(124,182,4,0.1)">
                        <i class="fa-solid fa-file-contract text-xs" style="color:#7cb604"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700">Documents légaux</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">

                    {{-- RCCM --}}
                    <div class="border border-slate-100 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                style="background:rgba(36,58,143,0.08)">
                                <i class="fa-solid fa-registered" style="color:#243a8f"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Registre du Commerce (RCCM)</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5">
                                    {{ $user->assuranceProfile->numero_rccm ?? '—' }}
                                </p>
                            </div>
                        </div>

                        @if($user->assuranceProfile && $user->assuranceProfile->path_rccm)
                            <a href="{{ Storage::url($user->assuranceProfile->path_rccm) }}" target="_blank" download
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                                style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                                <i class="fa-solid fa-download text-xs"></i>
                                Télécharger la fiche RCCM
                            </a>
                            <p class="text-xs text-center text-slate-300">
                                <i class="fa-solid fa-file-arrow-down mr-1"></i>
                                {{ basename($user->assuranceProfile->path_rccm) }}
                            </p>
                        @else
                            <div
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm text-slate-300 bg-slate-50 border border-dashed border-slate-200">
                                <i class="fa-solid fa-file-slash text-xs"></i>
                                Aucun fichier RCCM fourni
                            </div>
                        @endif
                    </div>

                    {{-- DFE --}}
                    <div class="border border-slate-100 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                style="background:rgba(124,182,4,0.08)">
                                <i class="fa-solid fa-file-invoice" style="color:#7cb604"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Dossier Fiscal de l'Entreprise (DFE)</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5">
                                    {{ $user->assuranceProfile->numero_dfe ?? '—' }}
                                </p>
                            </div>
                        </div>

                        @if($user->assuranceProfile && $user->assuranceProfile->path_dfe)
                            <a href="{{ Storage::url($user->assuranceProfile->path_dfe) }}" target="_blank" download
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                                style="background: linear-gradient(135deg, #7cb604, #5a8a03);">
                                <i class="fa-solid fa-download text-xs"></i>
                                Télécharger la fiche DFE
                            </a>
                            <p class="text-xs text-center text-slate-300">
                                <i class="fa-solid fa-file-arrow-down mr-1"></i>
                                {{ basename($user->assuranceProfile->path_dfe) }}
                            </p>
                        @else
                            <div
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm text-slate-300 bg-slate-50 border border-dashed border-slate-200">
                                <i class="fa-solid fa-file-slash text-xs"></i>
                                Aucun fichier DFE fourni
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- COLONNE DROITE --}}
        <div class="space-y-5">

            {{-- Statut du compte --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Statut du compte</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Rôle</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg"
                            style="background:rgba(36,58,143,0.1); color:#243a8f;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Email vérifié</span>
                        @if($user->email_verified_at)
                            <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                                <i class="fa-solid fa-check-circle"></i> Oui
                            </span>
                        @else
                            <span class="text-xs font-semibold text-amber-500 flex items-center gap-1">
                                <i class="fa-solid fa-clock"></i> En attente
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Inscription</span>
                        <span class="text-xs text-slate-600">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Actions</h2>

                <form action="{{ route('admin.assurances.destroy', $user) }}" method="POST"
                    onsubmit="return confirm('Supprimer définitivement cette assurance ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-medium text-red-500 bg-red-50 hover:bg-red-100 transition-all border border-red-100">
                        <i class="fa-solid fa-trash text-xs"></i>
                        Supprimer ce compte
                    </button>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('styles')
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeUp 0.4s ease-out both;
        }
    </style>
@endpush