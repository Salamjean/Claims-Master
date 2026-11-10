@extends('assure.layouts.template')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@php
    // ─── État des assurances ───
    $today = now()->startOfDay();
    $contratsInfos = ($contrats ?? collect())->map(function ($c) use ($today) {
        $fin = $c->date_fin ? \Illuminate\Support\Carbon::parse($c->date_fin)->startOfDay() : null;
        if (in_array($c->statut, ['expire', 'inactif']) || ($fin && $fin->lt($today))) {
            $etat = 'expire';
        } elseif ($fin && $fin->lte($today->copy()->addDays(30))) {
            $etat = 'bientot';
        } else {
            $etat = 'actif';
        }
        return ['contrat' => $c, 'fin' => $fin, 'etat' => $etat];
    });
    $expiredCount = $contratsInfos->where('etat', 'expire')->count();
    $soonCount = $contratsInfos->where('etat', 'bientot')->count();
    // Les contrats à traiter en premier
    $contratsAffiches = $contratsInfos->sortBy(fn ($i) => ['expire' => 0, 'bientot' => 1, 'actif' => 2][$i['etat']])->take(4);

    $etatStyles = [
        'expire'  => ['label' => 'Expirée',        'pill' => 'bg-red-50 text-red-700',         'icon' => 'bg-red-50 text-red-600'],
        'bientot' => ['label' => 'Expire bientôt', 'pill' => 'bg-amber-50 text-amber-700',     'icon' => 'bg-amber-50 text-amber-600'],
        'actif'   => ['label' => 'Active',         'pill' => 'bg-emerald-50 text-emerald-700', 'icon' => 'bg-slate-100 text-slate-600'],
    ];

    $kpis = [
        ['value' => $total ?? 0,     'label' => 'Sinistres déclarés', 'icon' => 'fa-folder-open',       'tone' => 'bg-slate-100 text-slate-700',   'route' => route('assure.sinistres.historique')],
        ['value' => $enAttente ?? 0, 'label' => 'En attente',         'icon' => 'fa-hourglass-half',    'tone' => 'bg-amber-50 text-amber-600',    'route' => route('assure.sinistres.en_attente')],
        ['value' => $enCours ?? 0,   'label' => 'En cours',           'icon' => 'fa-arrows-rotate',     'tone' => 'bg-blue-50 text-blue-600',      'route' => route('assure.sinistres.en_cours')],
        ['value' => $cloture ?? 0,   'label' => 'Clôturés',           'icon' => 'fa-circle-check',      'tone' => 'bg-emerald-50 text-emerald-600','route' => route('assure.sinistres.historique')],
    ];

    $hour = now()->hour;
    $greeting = $hour < 18 ? 'Bonjour' : 'Bonsoir';
@endphp

@section('content')
<div class="w-full space-y-6 pb-10">

    {{-- ═════════ EN-TÊTE ═════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 fu">
        <div>
            <p class="text-[13px] text-slate-500 first-letter:uppercase">{{ now()->translatedFormat('l j F Y') }}</p>
            <h2 class="text-2xl md:text-[28px] font-bold tracking-tight text-slate-900 mt-1">
                {{ $greeting }}, {{ $user->prenom ?? $user->name }}
            </h2>
            <p class="text-sm text-slate-500 mt-1">Voici l'essentiel de vos dossiers et de vos assurances.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($user->code_user)
                <button type="button" id="copyCodeBtn"
                    onclick="navigator.clipboard.writeText(@js($user->code_user)).then(() => Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Code copié', showConfirmButton: false, timer: 1500 }))"
                    class="h-10 inline-flex items-center gap-2.5 px-3.5 rounded-lg bg-white border border-slate-200 hover:border-slate-300 transition text-left"
                    title="Copier mon code assuré">
                    <span class="text-xs text-slate-500">Code assuré</span>
                    <span class="font-mono text-[13px] font-semibold text-slate-900">{{ $user->code_user }}</span>
                    <i class="fa-regular fa-copy text-xs text-slate-400"></i>
                </button>
            @endif
            <a href="{{ route('assure.contrats.create') }}"
                class="h-10 inline-flex items-center gap-2 px-4 rounded-lg bg-white border border-slate-200 hover:border-slate-300 text-slate-700 text-[13px] font-semibold transition">
                <i class="fa-solid fa-file-shield text-xs text-slate-400"></i>
                Ajouter une assurance
            </a>
            <a href="{{ route('assure.sinistres.create') }}"
                class="h-10 inline-flex items-center gap-2 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Déclarer un sinistre
            </a>
        </div>
    </div>

    {{-- ═════════ ACTIONS REQUISES ═════════ --}}
    @if(($countConstatsNonRegles ?? 0) > 0 || ($pendingDocumentsCount ?? 0) > 0 || $expiredCount > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 fu d1">
            @if(($countConstatsNonRegles ?? 0) > 0)
                <a href="{{ route('assure.constats.prets') }}" class="group flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-200 hover:border-amber-300 transition">
                    <span class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-[13px] font-semibold text-slate-900">{{ $countConstatsNonRegles }} constat{{ $countConstatsNonRegles > 1 ? 's' : '' }} à régler</span>
                        <span class="block text-xs text-slate-500">Payez pour débloquer vos documents officiels</span>
                    </span>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-300 group-hover:text-amber-600 group-hover:translate-x-0.5 transition"></i>
                </a>
            @endif
            @if(($pendingDocumentsCount ?? 0) > 0)
                <a href="{{ route('assure.sinistres.documents') }}" class="group flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-200 hover:border-blue-300 transition">
                    <span class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-file-arrow-up"></i></span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-[13px] font-semibold text-slate-900">{{ $pendingDocumentsCount }} document{{ $pendingDocumentsCount > 1 ? 's' : '' }} à fournir</span>
                        <span class="block text-xs text-slate-500">Nécessaire pour faire avancer vos dossiers</span>
                    </span>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-300 group-hover:text-blue-600 group-hover:translate-x-0.5 transition"></i>
                </a>
            @endif
            @if($expiredCount > 0)
                <a href="{{ route('assure.contrats.index') }}" class="group flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-200 hover:border-red-300 transition">
                    <span class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-[13px] font-semibold text-slate-900">{{ $expiredCount }} assurance{{ $expiredCount > 1 ? 's' : '' }} expirée{{ $expiredCount > 1 ? 's' : '' }}</span>
                        <span class="block text-xs text-slate-500">Renouvelez pour rester couvert</span>
                    </span>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-300 group-hover:text-red-600 group-hover:translate-x-0.5 transition"></i>
                </a>
            @endif
        </div>
    @endif

    {{-- ═════════ KPI ═════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 fu d2">
        @foreach($kpis as $kpi)
            <a href="{{ $kpi['route'] }}" class="group p-5 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 transition">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-lg {{ $kpi['tone'] }} flex items-center justify-center text-sm"><i class="fa-solid {{ $kpi['icon'] }}"></i></span>
                    <i class="fa-solid fa-chevron-right text-[11px] text-slate-300 group-hover:text-slate-500 transition"></i>
                </div>
                <p class="text-3xl font-bold tracking-tight text-slate-900 mt-4 leading-none">{{ $kpi['value'] }}</p>
                <p class="text-[13px] text-slate-500 mt-1.5">{{ $kpi['label'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- ═════════ GRILLE PRINCIPALE ═════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ─── Colonne principale ─── --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Suivi du dossier --}}
            <section class="rounded-2xl bg-white border border-slate-200 fu d3">
                <header class="flex items-center justify-between gap-3 px-6 pt-5 pb-4">
                    <div>
                        <h3 class="text-[15px] font-semibold text-slate-900">Suivi de votre dernier dossier</h3>
                        @if($dernierSinistre)
                            <p class="text-xs text-slate-500 mt-0.5">Mis à jour {{ $dernierSinistre->updated_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    @if($dernierSinistre)
                        <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md">N° {{ $dernierSinistre->numero_sinistre }}</span>
                    @endif
                </header>

                <div class="px-6 pb-6">
                    @if($dernierSinistre)
                        @php
                            $isAmiable = ($dernierSinistre->constat && $dernierSinistre->constat->methode_redaction === 'Amiable');
                            $steps = $isAmiable
                                ? ['Déclaration', 'Analyse IA', 'Constat rédigé', 'Révision', 'Clôture']
                                : ['Déclaration', 'Agent assigné', 'Constat terrain', 'Constat rédigé', 'Révision', 'Clôture'];

                            $status = $dernierSinistre->status;
                            $isAssigned = !empty($dernierSinistre->assigned_agent_id);
                            $isConstatTerrainDone = in_array($status, ['constat_terrain_ok', 'traite', 'cloture']);
                            $isRedactionDone = in_array($status, ['traite', 'cloture']);
                            $isReviewStarted = ($dernierSinistre->workflow_step === 'manager_review' || $status === 'cloture');
                            $isClosed = ($status === 'cloture');

                            if ($isAmiable) {
                                $stepStates = [true, true, true, $isReviewStarted, $isClosed];
                                $currentStepIndex = $isClosed ? 4 : ($isReviewStarted ? 3 : 2);
                            } else {
                                $stepStates = [true, $isAssigned, $isConstatTerrainDone, $isRedactionDone, $isReviewStarted, $isClosed];
                                $currentStepIndex = 0;
                                if ($isClosed) $currentStepIndex = 5;
                                elseif ($isReviewStarted) $currentStepIndex = 4;
                                elseif ($isRedactionDone) $currentStepIndex = 3;
                                elseif ($isConstatTerrainDone) $currentStepIndex = 2;
                                elseif ($isAssigned) $currentStepIndex = 1;
                            }

                            if ($isClosed) {
                                $message = 'Votre dossier est clôturé. Merci de votre confiance.';
                            } elseif ($isAmiable) {
                                $message = $isReviewStarted ? "Dossier transmis à votre assurance pour révision." : "Constat amiable rédigé. En attente de transmission à l'assurance.";
                            } else {
                                $message = [
                                    0 => "Votre déclaration est enregistrée. Un agent va bientôt être assigné.",
                                    1 => "Un agent a été assigné et se rend sur les lieux.",
                                    2 => "Constat terrain effectué. Rédaction du document en cours.",
                                    3 => "Constat rédigé. Transmission à votre assurance en cours.",
                                    4 => "Dossier transmis à votre assurance pour révision.",
                                ][$currentStepIndex] ?? '';
                            }
                            $progress = round(($currentStepIndex + ($isClosed ? 1 : 0.5)) / count($steps) * 100);
                        @endphp

                        {{-- Résumé --}}
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-car-burst text-sm"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 truncate first-letter:uppercase">{{ str_replace('_', ' ', $dernierSinistre->type_sinistre) }}</p>
                                <p class="text-xs text-slate-500">Déclaré le {{ $dernierSinistre->created_at->format('d/m/Y') }} · {{ $isAmiable ? 'Constat amiable' : 'Constat avec agent' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-slate-900">{{ $progress }}%</span>
                        </div>

                        {{-- Étapes --}}
                        <div class="overflow-x-auto -mx-1 px-1">
                            <ol class="grid gap-2 min-w-[480px]" style="grid-template-columns: repeat({{ count($steps) }}, minmax(0, 1fr));">
                                @foreach($steps as $i => $label)
                                    @php
                                        $done = ($stepStates[$i] ?? false) && ($i < $currentStepIndex || $isClosed);
                                        $current = !$isClosed && $i === $currentStepIndex;
                                    @endphp
                                    <li>
                                        <div class="h-1.5 rounded-full {{ $done ? 'bg-blue-600' : ($current ? 'bg-blue-200' : 'bg-slate-100') }} overflow-hidden">
                                            @if($current)<div class="h-full w-1/2 bg-blue-600 rounded-full"></div>@endif
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-2.5">
                                            @if($done)
                                                <i class="fa-solid fa-circle-check text-[11px] text-blue-600"></i>
                                            @elseif($current)
                                                <span class="relative flex w-2.5 h-2.5">
                                                    <span class="absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-60 animate-ping"></span>
                                                    <span class="relative inline-flex w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                                </span>
                                            @else
                                                <span class="w-2.5 h-2.5 rounded-full border border-slate-300"></span>
                                            @endif
                                            <span class="text-xs {{ $done || $current ? 'font-semibold text-slate-900' : 'text-slate-400' }} truncate">{{ $label }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        {{-- Statut + actions --}}
                        <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col md:flex-row md:items-center gap-4">
                            @if(!$isAmiable && $dernierSinistre->assignedAgent)
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <span class="w-9 h-9 rounded-full bg-blue-600 text-white text-[13px] font-semibold flex items-center justify-center">
                                        {{ strtoupper(mb_substr($dernierSinistre->assignedAgent->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-[11px] text-slate-500">Agent assigné</p>
                                        <p class="text-[13px] font-semibold text-slate-900">{{ $dernierSinistre->assignedAgent->name }}</p>
                                    </div>
                                </div>
                                <span class="hidden md:block w-px h-8 bg-slate-200"></span>
                            @endif

                            <p class="flex-1 text-[13px] text-slate-600">{{ $message }}</p>

                            <div class="flex flex-wrap gap-2 shrink-0">
                                @if($dernierSinistre->assignedAgent && $dernierSinistre->status === 'en_cours')
                                    <button type="button"
                                        onclick="openAgentMapModal({{ $dernierSinistre->id }}, @js($dernierSinistre->assignedAgent->name), {{ $dernierSinistre->latitude ?? 0 }}, {{ $dernierSinistre->longitude ?? 0 }})"
                                        class="h-9 inline-flex items-center gap-2 px-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition">
                                        <i class="fa-solid fa-location-dot text-xs"></i> Suivre l'agent
                                    </button>
                                @endif
                                @if($isAmiable)
                                    <a href="{{ route('assure.sinistres.constat.download', $dernierSinistre->id) }}"
                                        class="h-9 inline-flex items-center gap-2 px-3.5 rounded-lg border border-slate-200 hover:border-slate-300 text-slate-700 text-[13px] font-semibold transition">
                                        <i class="fa-regular fa-file-pdf text-xs text-red-500"></i> Constat
                                    </a>
                                @endif
                                <a href="{{ route('assure.sinistres.show', $dernierSinistre->id) }}"
                                    class="h-9 inline-flex items-center gap-2 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-[13px] font-semibold transition">
                                    Voir le dossier <i class="fa-solid fa-arrow-right text-[11px]"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center text-center py-10">
                            <span class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center mb-4">
                                <i class="fa-solid fa-shield-halved text-lg"></i>
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Aucun sinistre déclaré</p>
                            <p class="text-[13px] text-slate-500 mt-1 max-w-sm">Tout est calme. En cas d'incident, déclarez-le ici : nous vous accompagnons 24h/24.</p>
                            <a href="{{ route('assure.sinistres.create') }}"
                                class="mt-5 h-9 inline-flex items-center gap-2 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold transition">
                                <i class="fa-solid fa-plus text-xs"></i> Déclarer un sinistre
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Activité sur 6 mois --}}
            <section class="rounded-2xl bg-white border border-slate-200 fu d4">
                <header class="flex items-center justify-between px-6 pt-5 pb-2">
                    <div>
                        <h3 class="text-[15px] font-semibold text-slate-900">Sinistres déclarés</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sur les 6 derniers mois</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ array_sum($chartData ?? []) }}</p>
                </header>
                <div class="px-4 pb-4 h-56">
                    <canvas id="sinistresChart" aria-label="Sinistres déclarés par mois" role="img"></canvas>
                </div>
            </section>
        </div>

        {{-- ─── Colonne latérale ─── --}}
        <div class="space-y-6">

            {{-- Mes assurances --}}
            <section class="rounded-2xl bg-white border border-slate-200 fu d3">
                <header class="flex items-center justify-between px-5 pt-5 pb-3">
                    <div>
                        <h3 class="text-[15px] font-semibold text-slate-900">Mes assurances</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $contratsInfos->count() }} véhicule{{ $contratsInfos->count() > 1 ? 's' : '' }}
                            @if($soonCount) · <span class="text-amber-600">{{ $soonCount }} à renouveler bientôt</span>@endif
                        </p>
                    </div>
                    <a href="{{ route('assure.contrats.index') }}" class="text-[13px] font-semibold text-blue-600 hover:text-blue-700">Tout voir</a>
                </header>

                @if($contratsAffiches->isNotEmpty())
                    <ul class="px-2 pb-2">
                        @foreach($contratsAffiches as $info)
                            @php $c = $info['contrat']; $style = $etatStyles[$info['etat']]; @endphp
                            <li class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-slate-50 transition">
                                <span class="w-9 h-9 rounded-lg {{ $style['icon'] }} flex items-center justify-center shrink-0"><i class="fa-solid fa-car text-sm"></i></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[13px] font-semibold text-slate-900 truncate">{{ $c->marque }} {{ $c->modele }}</p>
                                    <p class="text-xs text-slate-500 truncate">
                                        <span class="font-mono">{{ $c->plaque }}</span>
                                        @if($info['fin']) · {{ $info['etat'] === 'expire' ? 'Expirée le' : "Jusqu'au" }} {{ $info['fin']->format('d/m/Y') }}@endif
                                    </p>
                                </div>
                                @if($info['etat'] === 'actif')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md {{ $style['pill'] }}">{{ $style['label'] }}</span>
                                @else
                                    <a href="{{ route('assure.contrats.create', ['renouveler_id' => $c->id]) }}"
                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-md {{ $style['pill'] }} hover:underline" title="Renouveler">
                                        {{ $style['label'] }} · Renouveler
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-5 pb-6 pt-2 text-center">
                        <p class="text-[13px] text-slate-500">Aucune assurance enregistrée.</p>
                        <a href="{{ route('assure.contrats.create') }}" class="inline-flex items-center gap-1.5 mt-3 text-[13px] font-semibold text-blue-600 hover:text-blue-700">
                            <i class="fa-solid fa-plus text-xs"></i> Ajouter une assurance
                        </a>
                    </div>
                @endif
            </section>

            {{-- Activité récente --}}
            <section class="rounded-2xl bg-white border border-slate-200 fu d4">
                <header class="px-5 pt-5 pb-3">
                    <h3 class="text-[15px] font-semibold text-slate-900">Activité récente</h3>
                </header>
                <ol class="px-5 pb-5">
                    @forelse($recentActivities as $activity)
                        <li class="relative flex gap-3 {{ $loop->last ? '' : 'pb-5' }}">
                            @unless($loop->last)
                                <span class="absolute left-[15px] top-8 bottom-0 w-px bg-slate-200"></span>
                            @endunless
                            <span class="w-8 h-8 rounded-full {{ $activity['type'] === 'sinistre' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $activity['icon'] }} text-xs"></i>
                            </span>
                            <div class="min-w-0 pt-1">
                                <p class="text-[13px] font-medium text-slate-800 leading-snug first-letter:uppercase">{{ $activity['title'] }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ \Illuminate\Support\Carbon::parse($activity['date'])->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-center py-6 text-[13px] text-slate-500">Aucune activité récente.</li>
                    @endforelse
                </ol>
            </section>

            {{-- Support --}}
            <a href="{{ route('assure.support') }}" class="group flex items-center gap-4 p-5 rounded-2xl bg-slate-900 hover:bg-slate-800 transition fu d4">
                <span class="w-10 h-10 rounded-lg bg-white/10 text-white flex items-center justify-center shrink-0"><i class="fa-solid fa-headset"></i></span>
                <span class="flex-1 min-w-0">
                    <span class="block text-sm font-semibold text-white">Besoin d'aide ?</span>
                    <span class="block text-xs text-slate-400">Notre équipe vous répond 24h/24, 7j/7</span>
                </span>
                <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-white group-hover:translate-x-0.5 transition"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('sinistresChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const data = @json(array_values($chartData ?? []));
        const maxValue = Math.max(...data, 0);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json(array_values($chartLabels ?? [])),
                datasets: [{
                    data: data,
                    backgroundColor: data.map((_, i) => i === data.length - 1 ? '#2563eb' : '#dbeafe'),
                    hoverBackgroundColor: '#2563eb',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: { label: ctx => ctx.parsed.y + ' sinistre' + (ctx.parsed.y > 1 ? 's' : '') }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(maxValue + 1, 4),
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 12 }, precision: 0, maxTicksLimit: 5 }
                    }
                }
            }
        });
    });
</script>
@endpush
