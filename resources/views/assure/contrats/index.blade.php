@extends('assure.layouts.template')

@section('title', 'Mes Assurances')
@section('page-title', 'Mes Assurances')

@php
    $today = now()->startOfDay();

    // Calcul de l'état de chaque contrat (actif / expire bientôt / expiré)
    $items = $contrats->values()->map(function ($c) use ($today) {
        $fin = $c->date_fin ? \Carbon\Carbon::parse($c->date_fin)->startOfDay() : null;
        $jours = $fin ? (int) $today->diffInDays($fin, false) : null;

        if ($fin && $jours < 0) {
            $etat = 'expired';
        } elseif ($fin && $jours <= 30) {
            $etat = 'soon';
        } else {
            $etat = 'active';
        }

        $assureur = $c->assureur->name ?? $c->nom_assureur ?? null;

        return [
            'contrat'  => $c,
            'fin'      => $fin,
            'jours'    => $jours,
            'etat'     => $etat,
            'assureur' => $assureur,
            'terms'    => mb_strtolower(implode(' ', [$c->marque, $c->modele, $c->plaque, $c->numero_contrat, $assureur, $c->type_vehicule])),
            'docs'     => [
                ['short' => 'Attest.',  'label' => "Attestation d'assurance", 'icon' => 'fa-file-shield',     'path' => $c->attestation_assurance],
                ['short' => 'C. grise', 'label' => 'Carte grise',             'icon' => 'fa-id-card',         'path' => $c->carte_grise],
                ['short' => 'Visite',   'label' => 'Visite technique',        'icon' => 'fa-clipboard-check', 'path' => $c->visite_technique],
                ['short' => 'Permis',   'label' => 'Permis de conduire',      'icon' => 'fa-id-badge',        'path' => $c->permis_conduire],
            ],
        ];
    });

    $counts = [
        'all'     => $items->count(),
        'active'  => $items->where('etat', 'active')->count(),
        'soon'    => $items->where('etat', 'soon')->count(),
        'expired' => $items->where('etat', 'expired')->count(),
    ];

    $etatStyles = [
        'active'  => ['label' => 'Active',         'pill' => 'bg-emerald-50 text-emerald-700', 'dot' => 'bg-emerald-500'],
        'soon'    => ['label' => 'Expire bientôt', 'pill' => 'bg-amber-50 text-amber-700',     'dot' => 'bg-amber-500'],
        'expired' => ['label' => 'Expirée',        'pill' => 'bg-red-50 text-red-700',         'dot' => 'bg-red-500'],
    ];

@endphp

@section('content')
<div x-data="{
        search: '',
        filter: 'all',
        items: @js($items->map(fn ($i) => ['etat' => $i['etat'], 'terms' => $i['terms']])->values()),
        showRenewModal: false,
        renewUrl: '',
        renewVehicleName: '',
        fileName: '',
        matches(index) {
            const item = this.items[index];
            const q = this.search.toLowerCase().trim();
            return (this.filter === 'all' || item.etat === this.filter) && (!q || item.terms.includes(q));
        },
        get visibleCount() {
            return this.items.filter((_, i) => this.matches(i)).length;
        },
        openRenewModal(url, name) {
            this.renewUrl = url;
            this.renewVehicleName = name;
            this.fileName = '';
            this.showRenewModal = true;
        }
    }" class="space-y-6 pb-12">

    {{-- ═════════ EN-TÊTE ═════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_minmax(0,480px)_1fr] items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-[28px] font-bold tracking-tight text-slate-900">Mes assurances</h2>
            <p class="text-sm text-slate-500 mt-1">Vos véhicules, leurs contrats et leurs justificatifs.</p>
        </div>

        @if($items->isNotEmpty())
            <div class="relative w-full order-last lg:order-none">
                <i class="fa-solid fa-magnifying-glass text-xs text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="search" x-model="search" placeholder="Rechercher une plaque, une marque, un assureur…"
                    class="w-full h-10 pl-9 pr-3 rounded-lg border border-slate-200 bg-white text-[13px] text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10">
            </div>
        @else
            <div class="hidden lg:block"></div>
        @endif

        <div class="lg:justify-self-end">
            <a href="{{ route('assure.contrats.create') }}"
                class="h-10 inline-flex items-center justify-center gap-2 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Ajouter une assurance
            </a>
        </div>
    </div>

    @if($items->isEmpty())
        {{-- ═════════ AUCUNE ASSURANCE ═════════ --}}
        <div class="rounded-2xl bg-white border border-slate-200 px-6 py-16 flex flex-col items-center text-center">
            <span class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mb-5">
                <i class="fa-solid fa-car-side text-xl"></i>
            </span>
            <h3 class="text-base font-semibold text-slate-900">Aucune assurance enregistrée</h3>
            <p class="text-[13px] text-slate-500 mt-1.5 max-w-md">
                Ajoutez votre premier véhicule : l'IA lit vos documents pour vous éviter la saisie et accélérer vos déclarations de sinistre.
            </p>
            <a href="{{ route('assure.contrats.create') }}"
                class="mt-6 h-10 inline-flex items-center gap-2 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Ajouter ma première assurance
            </a>
        </div>
    @else
        {{-- ═════════ RÉSUMÉ ═════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach([
                ['key' => 'all',     'label' => 'Véhicules assurés', 'icon' => 'fa-car',                 'tone' => 'bg-slate-100 text-slate-700'],
                ['key' => 'active',  'label' => 'Actives',           'icon' => 'fa-circle-check',        'tone' => 'bg-emerald-50 text-emerald-600'],
                ['key' => 'soon',    'label' => 'Expirent sous 30 j','icon' => 'fa-hourglass-half',      'tone' => 'bg-amber-50 text-amber-600'],
                ['key' => 'expired', 'label' => 'Expirées',          'icon' => 'fa-triangle-exclamation','tone' => 'bg-red-50 text-red-600'],
            ] as $stat)
                <button type="button" @click="filter = '{{ $stat['key'] }}'"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-white border text-left transition"
                    :class="filter === '{{ $stat['key'] }}' ? 'border-blue-600 ring-2 ring-blue-600/10' : 'border-slate-200 hover:border-slate-300'">
                    <span class="w-8 h-8 rounded-lg {{ $stat['tone'] }} flex items-center justify-center text-xs shrink-0"><i class="fa-solid {{ $stat['icon'] }}"></i></span>
                    <span class="min-w-0">
                        <span class="block text-lg font-bold text-slate-900 leading-none">{{ $counts[$stat['key']] }}</span>
                        <span class="block text-xs text-slate-500 mt-0.5 truncate">{{ $stat['label'] }}</span>
                    </span>
                </button>
            @endforeach
        </div>

        {{-- ═════════ LISTE DES VÉHICULES (cartes compactes) ═════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3">
            @foreach($items as $index => $item)
                @php
                    $c = $item['contrat'];
                    $style = $etatStyles[$item['etat']];
                    $jours = $item['jours'];
                @endphp

                <article x-show="matches({{ $index }})" class="flex flex-col rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition">

                    <div class="p-3.5 flex-1">
                        {{-- Plaque + statut --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-stretch rounded border-[1.5px] border-slate-900 overflow-hidden">
                                <span class="bg-slate-900 text-white text-[8px] font-bold px-1 flex items-center">CI</span>
                                <span class="px-1.5 py-0.5 font-mono text-[11px] font-bold tracking-wider text-slate-900 uppercase bg-white">{{ $c->plaque }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $style['pill'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                {{ $style['label'] }}
                            </span>
                        </div>

                        {{-- Véhicule --}}
                        <div class="mt-2.5 flex items-center gap-1.5 min-w-0">
                            <h3 class="text-[13px] font-semibold text-slate-900 truncate">{{ $c->marque }} {{ $c->modele }}</h3>
                            @if($c->attestation_ai_status === 'valid')
                                <i class="fa-solid fa-circle-check text-[10px] text-blue-600 shrink-0" title="Attestation vérifiée par l'IA"></i>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 truncate">{{ $c->type_vehicule ?: 'Véhicule' }}</p>

                        {{-- Contrat --}}
                        <dl class="mt-2.5 space-y-1 text-[11px]">
                            <div class="flex justify-between gap-2">
                                <dt class="text-slate-500 shrink-0">Police</dt>
                                <dd class="font-mono font-medium text-slate-800 truncate">{{ $c->numero_contrat ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt class="text-slate-500 shrink-0">Assureur</dt>
                                <dd class="font-medium text-slate-800 truncate" title="{{ $item['assureur'] }}">{{ $item['assureur'] ?? 'Non spécifié' }}</dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt class="text-slate-500 shrink-0">Échéance</dt>
                                <dd class="font-medium truncate {{ $item['etat'] === 'expired' ? 'text-red-600' : ($item['etat'] === 'soon' ? 'text-amber-600' : 'text-slate-800') }}">
                                    @if($item['fin'])
                                        {{ $item['fin']->format('d/m/Y') }}
                                        @if($jours < 0)
                                            <span class="font-normal">· -{{ abs($jours) }} j</span>
                                        @elseif($jours <= 30)
                                            <span class="font-normal">· {{ $jours }} j</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        {{-- Documents : 4 justificatifs, espacés régulièrement --}}
                        <div class="mt-3 grid grid-cols-4 gap-1.5">
                            @foreach($item['docs'] as $doc)
                                @if($doc['path'])
                                    <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" rel="noopener" title="Voir : {{ $doc['label'] }}"
                                        class="flex items-center justify-center h-7 rounded-md border border-slate-200 text-slate-600 hover:text-blue-700 hover:border-blue-200 hover:bg-blue-50 transition">
                                        <span class="text-[10px] font-medium leading-none whitespace-nowrap">{{ $doc['short'] }}</span>
                                    </a>
                                @else
                                    <span title="{{ $doc['label'] }} : non fourni"
                                        class="flex items-center justify-center h-7 rounded-md border border-dashed border-slate-200 text-slate-300 cursor-default">
                                        <span class="text-[10px] font-medium leading-none whitespace-nowrap">{{ $doc['short'] }}</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-3.5 py-2.5 border-t border-slate-100 flex items-center gap-1.5">
                        @if($item['etat'] === 'expired')
                            <button type="button"
                                @click="openRenewModal(@js(route('assure.contrats.renew', $c)), @js($c->plaque . ' · ' . $c->marque . ' ' . $c->modele))"
                                class="flex-1 h-7 inline-flex items-center justify-center gap-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-semibold transition">
                                <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Renouveler
                            </button>
                        @else
                            <a href="{{ route('assure.sinistres.create', ['contrat_id' => $c->id]) }}"
                                class="flex-1 h-7 inline-flex items-center justify-center gap-1.5 rounded-md bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-semibold transition">
                                <i class="fa-solid fa-car-burst text-[10px]"></i> Déclarer un sinistre
                            </a>
                            @if($item['etat'] === 'soon')
                                <button type="button"
                                    @click="openRenewModal(@js(route('assure.contrats.renew', $c)), @js($c->plaque . ' · ' . $c->marque . ' ' . $c->modele))"
                                    class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-700 transition"
                                    title="Renouveler cette assurance">
                                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                </button>
                            @endif
                        @endif

                        <form action="{{ route('assure.contrats.destroy', $c) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete(this)"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition"
                                title="Supprimer cette assurance">
                                <i class="fa-regular fa-trash-can text-[11px]"></i>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Aucun résultat --}}
        <div x-show="visibleCount === 0" x-cloak class="rounded-2xl bg-white border border-dashed border-slate-300 px-6 py-12 text-center">
            <p class="text-sm font-semibold text-slate-900">Aucune assurance ne correspond</p>
            <p class="text-[13px] text-slate-500 mt-1">Modifiez votre recherche ou le compteur sélectionné.</p>
            <button type="button" @click="search = ''; filter = 'all'" class="mt-4 text-[13px] font-semibold text-blue-600 hover:text-blue-700">
                Réinitialiser les filtres
            </button>
        </div>
    @endif

    {{-- ═════════ MODALE DE RENOUVELLEMENT ═════════ --}}
    <div x-show="showRenewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="renew-title"
        @keydown.escape.window="showRenewModal = false">
        <div x-show="showRenewModal" x-transition.opacity @click="showRenewModal = false" class="absolute inset-0 bg-slate-900/50"></div>

        <div x-show="showRenewModal"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="relative w-full max-w-lg rounded-2xl bg-white border border-slate-200 shadow-xl">

            <form :action="renewUrl" method="POST" enctype="multipart/form-data" id="pop-renew-form">
                @csrf
                <div class="flex items-start justify-between gap-4 p-6 pb-4">
                    <div>
                        <h3 id="renew-title" class="text-base font-semibold text-slate-900">Renouveler l'assurance</h3>
                        <p class="text-[13px] text-slate-500 mt-0.5" x-text="renewVehicleName"></p>
                    </div>
                    <button type="button" @click="showRenewModal = false" class="w-8 h-8 -mr-2 -mt-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition" title="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="px-6 space-y-4">
                    <p class="text-[13px] text-slate-600">
                        Déposez la <span class="font-semibold text-slate-900">nouvelle attestation d'assurance</span> (format ASACI). L'IA vérifie l'immatriculation et met à jour l'échéance.
                    </p>

                    <label class="relative flex flex-col items-center justify-center gap-2 h-36 rounded-xl border border-dashed cursor-pointer transition text-center px-4"
                        :class="fileName ? 'border-blue-600 bg-blue-50/40' : 'border-slate-300 hover:border-blue-500 hover:bg-blue-50/40'">
                        <input type="file" name="attestation_assurance" id="pop_attestation_assurance" required accept="image/*,application/pdf"
                            class="sr-only" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                        <span class="w-10 h-10 rounded-lg flex items-center justify-center" :class="fileName ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500'">
                            <i class="fa-solid" :class="fileName ? 'fa-check' : 'fa-arrow-up-from-bracket'"></i>
                        </span>
                        <span class="text-[13px] font-medium text-slate-700 max-w-full truncate" x-text="fileName || 'Choisir un fichier'"></span>
                        <span class="text-xs text-slate-400">PDF, JPG ou PNG · 5 Mo maximum</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 p-6 pt-5">
                    <button type="button" @click="showRenewModal = false"
                        class="h-10 px-4 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-[13px] font-semibold transition">
                        Annuler
                    </button>
                    <button type="submit" :disabled="!fileName"
                        class="h-10 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        Analyser et mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Supprimer cette assurance ?',
                text: "Cette action est irréversible : le contrat et ses documents seront définitivement supprimés.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const popForm = document.getElementById('pop-renew-form');
            if (popForm) {
                popForm.addEventListener('submit', function () {
                    Swal.fire({
                        title: 'Analyse en cours…',
                        html: "Nous lisons la nouvelle attestation et mettons à jour l'échéance.<br>Merci de patienter quelques instants.",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                });
            }
        });
    </script>
@endpush
