@extends('assurance.layouts.template')

@section('title', 'Détail Assuré — ' . $user->name)

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-400 mb-1">
                    <a href="{{ route('assurance.assures.index') }}"
                        class="hover:text-slate-600 transition-colors">Assurés</a>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-slate-600 font-semibold">{{ $user->name }} {{ $user->prenom }}</span>
                </div>
                <h1 class="text-xl font-bold text-slate-800">Fiche Assuré</h1>
            </div>
            <a href="{{ route('assurance.assures.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i> Retour
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne gauche : Infos personnelles --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Carte identité --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 flex flex-col items-center text-center border-b border-slate-100"
                        style="background: linear-gradient(135deg, rgba(36,58,143,0.05), rgba(124,182,4,0.05))">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-3 shadow-md"
                            style="background: linear-gradient(135deg, #243a8f, #7cb604)">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">{{ $user->name }} {{ $user->prenom }}</h2>
                        <span
                            class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-lg text-xs font-mono font-semibold"
                            style="background:rgba(36,58,143,0.08); color:#243a8f">
                            <i class="fa-solid fa-id-badge text-[10px]"></i>
                            {{ $user->code_user ?? '—' }}
                        </span>
                    </div>

                    <div class="p-5 space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-envelope text-slate-300 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Email</p>
                                <p class="text-slate-700">{{ $user->email ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-phone text-slate-300 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Contact</p>
                                <p class="text-slate-700">{{ $user->contact ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-slate-300 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Adresse</p>
                                <p class="text-slate-700">{{ $user->adresse ?? '—' }}</p>
                                @if ($user->commune)
                                    <p class="text-xs text-slate-400">{{ $user->commune }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-calendar text-slate-300 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Inscrit le
                                </p>
                                <p class="text-slate-700">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistiques rapides --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-4">Statistiques</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-file-contract text-slate-300 w-4 text-center"></i> Contrats
                            </span>
                            <span class="text-sm font-bold text-slate-800">{{ $user->contrats->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-slate-300 w-4 text-center"></i> Sinistres
                            </span>
                            <span class="text-sm font-bold text-slate-800">{{ $sinistres->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-lock text-slate-300 w-4 text-center"></i> Clôturés
                            </span>
                            <span class="text-sm font-bold text-slate-800">
                                {{ $sinistres->where('status', 'cloture')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite : Contrats + Sinistres --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Contrats --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-file-contract text-slate-400"></i> Contrats
                        </h2>
                        <span class="text-xs font-bold text-slate-400">{{ $user->contrats->count() }} contrat(s)</span>
                    </div>

                    @forelse ($user->contrats as $contrat)
                        <div class="px-6 py-4 border-b border-slate-50 last:border-b-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-sm font-bold text-slate-800">
                                            {{ ucfirst($contrat->type_contrat) }}
                                        </span>
                                        <span
                                            class="text-xs font-mono text-slate-400">{{ $contrat->numero_contrat }}</span>
                                        @php
                                            $statutColor = match ($contrat->statut) {
                                                'actif' => 'bg-emerald-100 text-emerald-700',
                                                'suspendu' => 'bg-yellow-100 text-yellow-700',
                                                'resilie' => 'bg-red-100 text-red-700',
                                                'expire' => 'bg-slate-100 text-slate-500',
                                                default => 'bg-slate-100 text-slate-500',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold {{ $statutColor }}">
                                            {{ ucfirst($contrat->statut) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-slate-400 flex-wrap">
                                        <span>
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            Du {{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}
                                            au {{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}
                                        </span>
                                        @if ($contrat->prime)
                                            <span>
                                                <i class="fa-solid fa-coins mr-1"></i>
                                                Prime : {{ number_format($contrat->prime, 0, ',', ' ') }} FCFA
                                            </span>
                                        @endif
                                        @if ($contrat->plaque || $contrat->marque)
                                            <span>
                                                <i class="fa-solid fa-car mr-1"></i>
                                                {{ $contrat->marque }} {{ $contrat->modele }}
                                                @if ($contrat->plaque)
                                                    ({{ $contrat->plaque }})
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($contrat->document_pdf)
                                    <a href="{{ Storage::url($contrat->document_pdf) }}" target="_blank"
                                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                                        <i class="fa-regular fa-file-pdf text-red-400"></i> PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-slate-400 text-sm">
                            <i class="fa-solid fa-file-contract text-3xl mb-2 block opacity-20"></i>
                            Aucun contrat enregistré.
                        </div>
                    @endforelse
                </div>

                {{-- Sinistres --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-slate-400"></i> Sinistres déclarés
                        </h2>
                        <span class="text-xs font-bold text-slate-400">{{ $sinistres->count() }} sinistre(s)</span>
                    </div>

                    @forelse ($sinistres as $sinistre)
                        <div class="px-6 py-4 border-b border-slate-50 last:border-b-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-sm font-bold text-slate-800">
                                            {{ str_replace('_', ' ', ucfirst($sinistre->type_sinistre)) }}
                                        </span>
                                        @if ($sinistre->numero_sinistre)
                                            <span
                                                class="text-xs font-mono text-slate-400">{{ $sinistre->numero_sinistre }}</span>
                                        @endif
                                        @php
                                            $sColor = match ($sinistre->status) {
                                                'en_attente' => 'bg-yellow-100 text-yellow-700',
                                                'en_cours' => 'bg-blue-100 text-blue-700',
                                                'traite' => 'bg-indigo-100 text-indigo-700',
                                                'cloture' => match ($sinistre->workflow_step) {
                                                    'closed_validated' => 'bg-emerald-100 text-emerald-700',
                                                    'closed_rejected' => 'bg-red-100 text-red-700',
                                                    default => 'bg-slate-100 text-slate-500',
                                                },
                                                default => 'bg-slate-100 text-slate-500',
                                            };
                                            $sLabel = match ($sinistre->status) {
                                                'en_attente' => 'En attente',
                                                'en_cours' => 'En cours',
                                                'traite' => 'Traité',
                                                'cloture' => $sinistre->workflow_step === 'closed_validated'
                                                    ? 'Validé'
                                                    : ($sinistre->workflow_step === 'closed_rejected'
                                                        ? 'Rejeté'
                                                        : 'Clôturé'),
                                                default => ucfirst($sinistre->status),
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold {{ $sColor }}">
                                            {{ $sLabel }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-slate-400 flex-wrap">
                                        <span>
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            Déclaré le {{ $sinistre->created_at->format('d/m/Y') }}
                                        </span>
                                        @if ($sinistre->lieu)
                                            <span>
                                                <i class="fa-solid fa-location-dot mr-1"></i>
                                                {{ $sinistre->lieu }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('assurance.sinistres.show', $sinistre) }}"
                                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                                    <i class="fa-solid fa-eye text-xs"></i> Examiner
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-slate-400 text-sm">
                            <i class="fa-solid fa-triangle-exclamation text-3xl mb-2 block opacity-20"></i>
                            Aucun sinistre déclaré.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection
