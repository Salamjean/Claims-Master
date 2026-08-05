@extends('assure.layouts.template')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@push('styles')
<style>
    /* ─── MODERN KPI CARDS ─── */
    .kpi-card-v2 {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.03);
    }

    .kpi-card-v2:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.08);
    }

    .kpi-glow-orb {
        position: absolute;
        top: -24px;
        right: -24px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        filter: blur(28px);
        opacity: 0.15;
        transition: opacity 0.3s, transform 0.3s;
        pointer-events: none;
    }
    .kpi-card-v2:hover .kpi-glow-orb {
        opacity: 0.28;
        transform: scale(1.15);
    }

    .kpi-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: transform 0.3s ease;
    }
    .kpi-card-v2:hover .kpi-icon-box {
        transform: scale(1.08) rotate(-3deg);
    }

    /* ─── TRACKER STEPS ─── */
    .tracker-line { position: absolute; top: 21px; left: 0; right: 0; height: 3px; background: #f1f5f9; border-radius: 3px; }
    .tracker-fill { position: absolute; top: 21px; left: 0; height: 3px; background: linear-gradient(90deg, #6366f1, #8b5cf6); transition: width 0.8s ease; border-radius: 3px; box-shadow: 0 0 10px rgba(99,102,241,0.5); }
    .step-dot {
        width: 44px; height: 44px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; z-index: 2;
    }
    .step-dot.done { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 6px 16px rgba(99,102,241,0.35); }
    .step-dot.current { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 6px 20px rgba(99,102,241,0.45); outline: 4px solid #eef2ff; animation: pulseRing 2s infinite; }
    .step-dot.pending { background: #fff; color: #cbd5e1; border: 2px solid #e2e8f0; }

    @keyframes pulseRing {
        0% { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
        70% { box-shadow: 0 0 0 10px rgba(99,102,241,0); }
        100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
    }

    /* ─── ACTIVITY FEED ─── */
    .activity-line { position: absolute; left: 19px; top: 38px; bottom: 0; width: 1.5px; background: #f1f5f9; }
    .activity-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; transition: all 0.2s; }

    /* ─── HERO BANNER ─── */
    .hero-banner {
        background: linear-gradient(135deg, #3730a3 0%, #4f46e5 40%, #7c3aed 100%);
        border-radius: 24px;
        padding: 30px 34px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 32px -8px rgba(79, 70, 229, 0.3);
    }
    .hero-banner::before {
        content: '';
        position: absolute;
        top: -60px; right: -40px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -80px; left: 10%;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }

    /* ─── ALERT BANNER ─── */
    .alert-banner {
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border: 1px solid #fed7aa;
        border-radius: 20px;
        padding: 20px 24px;
        box-shadow: 0 4px 16px -4px rgba(245, 158, 11, 0.1);
    }

    /* ─── SECTION CARD ─── */
    .section-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        overflow: hidden;
        box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.03);
        transition: box-shadow 0.3s, border-color 0.3s;
    }
    .section-card:hover {
        box-shadow: 0 8px 30px -4px rgba(15, 23, 42, 0.06);
        border-color: rgba(203, 213, 225, 0.9);
    }
    .section-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
    }
    .section-card-body { padding: 24px; }

    /* ─── EMPTY STATE ─── */
    .empty-state { text-align: center; padding: 48px 24px; }
    .empty-icon { width: 76px; height: 76px; background: linear-gradient(135deg, #eef2ff, #e0e7ff); border-radius: 22px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 30px; color: #6366f1; box-shadow: 0 8px 20px -4px rgba(99,102,241,0.2); }

    /* ─── ANIMATE ─── */
    @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .au { animation: fadeUp 0.45s ease-out both; }
    .au-1 { animation-delay: 0.05s; } .au-2 { animation-delay: 0.1s; }
    .au-3 { animation-delay: 0.15s; } .au-4 { animation-delay: 0.2s; }
    .au-5 { animation-delay: 0.25s; } .au-6 { animation-delay: 0.3s; }

    /* ─── STATUS BADGE ─── */
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════ --}}
<div class="hero-banner mb-6 au au-1">
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400" style="box-shadow: 0 0 8px #34d399;"></span>
                <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-widest">Compte actif</span>
            </div>
            <h2 class="text-2xl font-black text-white leading-tight mb-1">
                Bonjour, <span class="text-violet-200">{{ $user->prenom ?? $user->name }} 👋</span>
            </h2>
            <p class="text-white/60 text-sm font-medium">Voici un aperçu de vos dossiers et activités récentes.</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            {{-- Code Assuré --}}
            <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-4 py-3 text-center">
                <p class="text-[9px] font-black text-white/50 uppercase tracking-widest mb-1">Code Assuré</p>
                <div class="flex items-center gap-2">
                    <span class="font-black text-white font-mono text-lg tracking-tight">{{ $user->code_user }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $user->code_user }}').then(()=>Swal.fire({toast:true,position:'top-end',icon:'success',title:'Copié !',showConfirmButton:false,timer:1500}))"
                        class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white/60 hover:text-white transition-all">
                        <i class="fa-regular fa-copy text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- CTA Déclarer --}}
            <a href="{{ route('assure.sinistres.create') }}"
                class="flex items-center gap-2 px-5 py-3 bg-white text-indigo-700 font-black text-sm rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Nouveau sinistre</span>
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     ALERTE CONSTATS NON RÉGLÉS
══════════════════════════════════════════ --}}
@if(isset($countConstatsNonRegles) && $countConstatsNonRegles > 0)
<div class="alert-banner mb-6 au au-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-500 text-base shrink-0">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div>
            <p class="font-black text-orange-900 text-sm">{{ $countConstatsNonRegles }} constat{{ $countConstatsNonRegles > 1 ? 's' : '' }} en attente de règlement</p>
            <p class="text-orange-600/70 text-xs font-medium mt-0.5">Procédez au paiement pour débloquer vos documents officiels.</p>
        </div>
    </div>
    <a href="{{ route('assure.constats.prets') }}"
        class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-black rounded-xl transition-all whitespace-nowrap shadow-sm">
        Débloquer maintenant →
    </a>
</div>
@endif

{{-- ══════════════════════════════════════════
     KPI CARDS — Nouveau Design Moderne
══════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Sinistres --}}
    <div class="kpi-card-v2 indigo au au-2 group">
        <div class="kpi-glow-orb bg-indigo-600"></div>
        <div class="flex items-start justify-between relative z-10">
            <div class="kpi-icon-box bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/25">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                Total
            </span>
        </div>
        <div class="relative z-10">
            <div class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-1 group-hover:text-indigo-600 transition-colors">
                {{ $total ?? 0 }}
            </div>
            <div class="text-xs font-bold text-slate-400">Sinistres déclarés</div>
        </div>
    </div>

    {{-- En Attente --}}
    <div class="kpi-card-v2 amber au au-3 group">
        <div class="kpi-glow-orb bg-amber-500"></div>
        <div class="flex items-start justify-between relative z-10">
            <div class="kpi-icon-box bg-gradient-to-br from-amber-400 to-amber-500 text-white shadow-md shadow-amber-500/25">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-100">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                En attente
            </span>
        </div>
        <div class="relative z-10">
            <div class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-1 group-hover:text-amber-600 transition-colors">
                {{ $enAttente ?? 0 }}
            </div>
            <div class="text-xs font-bold text-slate-400">Dossiers en attente</div>
        </div>
    </div>

    {{-- En Cours --}}
    <div class="kpi-card-v2 blue au au-4 group">
        <div class="kpi-glow-orb bg-blue-500"></div>
        <div class="flex items-start justify-between relative z-10">
            <div class="kpi-icon-box bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-500/25">
                <i class="fa-solid fa-bolt-lightning"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                Actif
            </span>
        </div>
        <div class="relative z-10">
            <div class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-1 group-hover:text-blue-600 transition-colors">
                {{ $enCours ?? 0 }}
            </div>
            <div class="text-xs font-bold text-slate-400">Traitement en cours</div>
        </div>
    </div>

    {{-- Clôturés --}}
    <div class="kpi-card-v2 emerald au au-5 group">
        <div class="kpi-glow-orb bg-emerald-500"></div>
        <div class="flex items-start justify-between relative z-10">
            <div class="kpi-icon-box bg-gradient-to-br from-emerald-400 to-teal-600 text-white shadow-md shadow-emerald-500/25">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Terminés
            </span>
        </div>
        <div class="relative z-10">
            <div class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-1 group-hover:text-emerald-600 transition-colors">
                {{ $cloture ?? 0 }}
            </div>
            <div class="text-xs font-bold text-slate-400">Sinistres clôturés</div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     GRILLE PRINCIPALE — Suivi + Activités
══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- ─── COL GAUCHE (2/3) : Tracker + Chart ─── --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Suivi dossier actif --}}
        <div class="section-card au au-3">
            <div class="section-card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Suivi du dossier actif</h3>
                        @if($dernierSinistre)
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Mis à jour {{ $dernierSinistre->updated_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>
                @if($dernierSinistre)
                    <div class="flex items-center gap-2">
                        <span class="status-pill bg-indigo-50 text-indigo-600 border border-indigo-100">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            N° {{ $dernierSinistre->numero_sinistre }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="section-card-body">
                @if($dernierSinistre)
                    @php
                        $isAmiable = ($dernierSinistre->constat && $dernierSinistre->constat->methode_redaction === 'Amiable');
                        if ($isAmiable) {
                            $steps = [
                                ['label' => 'Déclaration', 'icon' => 'fa-paper-plane'],
                                ['label' => 'Analyse IA', 'icon' => 'fa-robot'],
                                ['label' => 'Constat rédigé', 'icon' => 'fa-file-signature'],
                                ['label' => 'Révision', 'icon' => 'fa-shield-halved'],
                                ['label' => 'Clôture', 'icon' => 'fa-check-double'],
                            ];
                        } else {
                            $steps = [
                                ['label' => 'Déclaration', 'icon' => 'fa-paper-plane'],
                                ['label' => 'Agent assigné', 'icon' => 'fa-user-shield'],
                                ['label' => 'Constat terrain', 'icon' => 'fa-clipboard-check'],
                                ['label' => 'Constat rédigé', 'icon' => 'fa-file-invoice'],
                                ['label' => 'Révision', 'icon' => 'fa-shield-halved'],
                                ['label' => 'Clôture', 'icon' => 'fa-check-double'],
                            ];
                        }
                        $status = $dernierSinistre->status;
                        $isAssigned = !empty($dernierSinistre->assigned_agent_id);
                        $isConstatTerrainDone = in_array($status, ['constat_terrain_ok', 'traite', 'cloture']);
                        $isRedactionDone = in_array($status, ['traite', 'cloture']);
                        $isReviewStarted = ($dernierSinistre->workflow_step === 'manager_review' || $status === 'cloture');
                        $isClosed = ($status === 'cloture');

                        if ($isAmiable) {
                            $stepStates = [true, true, true, $isReviewStarted, $isClosed];
                            $currentStepIndex = 2;
                            if ($isClosed) $currentStepIndex = 4;
                            elseif ($isReviewStarted) $currentStepIndex = 3;
                        } else {
                            $stepStates = [true, $isAssigned, $isConstatTerrainDone, $isRedactionDone, $isReviewStarted, $isClosed];
                            $currentStepIndex = 0;
                            if ($isClosed) $currentStepIndex = 5;
                            elseif ($isReviewStarted) $currentStepIndex = 4;
                            elseif ($isRedactionDone) $currentStepIndex = 3;
                            elseif ($isConstatTerrainDone) $currentStepIndex = 2;
                            elseif ($isAssigned) $currentStepIndex = 1;
                        }
                        $totalSteps = count($steps);
                        $fillPct = $totalSteps > 1 ? round(($currentStepIndex / ($totalSteps - 1)) * 100) : 0;
                    @endphp

                    {{-- Progress bar --}}
                    <div class="relative mb-8 mt-3">
                        <div class="tracker-line"></div>
                        <div class="tracker-fill" style="width: {{ $fillPct }}%;"></div>

                        <div class="relative z-10 flex justify-between">
                            @foreach($steps as $i => $step)
                                @php
                                    $isDone = $stepStates[$i] ?? false;
                                    $isCurr = $i === $currentStepIndex;
                                    $cls = $isDone ? ($isCurr ? 'current' : 'done') : 'pending';
                                @endphp
                                <div class="flex flex-col items-center">
                                    <div class="step-dot {{ $cls }}">
                                        <i class="fa-solid {{ $step['icon'] }} text-sm"></i>
                                    </div>
                                    <span class="mt-3 text-[10px] font-bold text-center max-w-[64px] leading-tight
                                        {{ $isDone ? 'text-indigo-600 font-extrabold' : 'text-slate-400' }}">
                                        {{ $step['label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info bar --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 bg-gradient-to-r from-slate-50 via-indigo-50/20 to-slate-50 rounded-2xl border border-slate-200/70">
                        @if(!$isAmiable && $dernierSinistre->assignedAgent)
                            <div class="flex items-center gap-3 sm:border-r sm:border-slate-200 sm:pr-4">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center font-black text-white text-sm shadow-sm">
                                    {{ strtoupper(substr($dernierSinistre->assignedAgent->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Agent Assigné</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $dernierSinistre->assignedAgent->name }}</p>
                                </div>
                            </div>
                        @elseif($isAmiable)
                            <div class="flex items-center gap-3 sm:border-r sm:border-slate-200 sm:pr-4">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-base shadow-sm">
                                    <i class="fa-solid fa-bolt-lightning"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Mode</p>
                                    <p class="text-sm font-bold text-slate-800">Constat Amiable</p>
                                </div>
                            </div>
                        @endif

                        <p class="flex-1 text-xs font-semibold text-slate-600 leading-relaxed">
                            @if($currentStepIndex == 0) Votre demande est en attente d'affectation d'un agent.
                            @elseif($currentStepIndex == 1) Un agent a été assigné et se rend sur les lieux.
                            @elseif($currentStepIndex == 2) Constat terrain effectué. Rédaction du document en cours.
                            @elseif($currentStepIndex == 3 || $currentStepIndex == 4)
                                Dossier transmis pour révision par l'assurance.
                            @else Votre dossier est <strong class="text-emerald-600">clôturé</strong>. Merci de votre confiance.
                            @endif
                        </p>

                        <div class="flex gap-2 shrink-0 flex-wrap">
                            @if($dernierSinistre->constat && $dernierSinistre->constat->methode_redaction === 'Amiable')
                                <a href="{{ route('assure.sinistres.constat.download', $dernierSinistre->id) }}"
                                    class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                    <i class="fa-solid fa-file-pdf"></i> Constat
                                </a>
                            @endif
                            <a href="{{ route('assure.sinistres.show', $dernierSinistre->id) }}"
                                class="flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all">
                                Voir détails <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>

                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-shield-cat"></i></div>
                        <h4 class="text-base font-black text-slate-800 mb-2">Aucun sinistre en cours</h4>
                        <p class="text-xs text-slate-400 font-medium mb-5">Tout est calme. En cas d'incident, nous sommes là pour vous accompagner 24h/7j.</p>
                        <a href="{{ route('assure.sinistres.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-500/20 transition-all">
                            <i class="fa-solid fa-plus text-xs"></i> Déclarer un sinistre
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Graphique tendances + Profil rapide --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Chart --}}
            <div class="section-card au au-4">
                <div class="section-card-header">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-indigo-500 text-xs"></i>
                        <h3 class="text-sm font-black text-slate-800">Tendances</h3>
                    </div>
                    <span class="text-[10px] font-extrabold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">6 mois</span>
                </div>
                <div class="section-card-body">
                    <div class="h-36 relative">
                        <canvas id="claimsChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Profil rapide --}}
            <div class="section-card au au-4 flex flex-col justify-between">
                <div class="section-card-header">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-indigo-500 text-xs"></i>
                        <h3 class="text-sm font-black text-slate-800">Mon profil</h3>
                    </div>
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-xs shadow-emerald-400"></div>
                </div>
                <div class="section-card-body flex-1 flex flex-col justify-between">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-base shadow-md shadow-indigo-500/20 overflow-hidden ring-2 ring-indigo-500/10">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-black text-slate-800 text-sm truncate">{{ $user->name }} {{ $user->prenom }}</p>
                            <span class="inline-block text-[10px] font-bold text-indigo-600 font-mono bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100/60 mt-0.5">{{ $user->code_user }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                            <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs shrink-0">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        @if($user->contact)
                        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                            <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <span>{{ $user->contact }}</span>
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('assure.profile') }}"
                        class="w-full flex items-center justify-center gap-2 py-2.5 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 hover:border-indigo-200 transition-all shadow-2xs">
                        <i class="fa-solid fa-user-pen"></i> Gérer mon profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── COL DROITE (1/3) : Activités ─── --}}
    <div class="section-card au au-4 flex flex-col" style="max-height: 660px;">
        <div class="section-card-header shrink-0">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-500 text-xs"></i>
                <h3 class="text-sm font-black text-slate-800">Activités récentes</h3>
            </div>
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
        </div>

        <div class="flex-1 overflow-y-auto" style="padding: 20px 24px; scrollbar-width: none;">
            <div class="space-y-5">
                @forelse($recentActivities as $activity)
                    <div class="flex gap-3.5 relative group">
                        @if(!$loop->last)
                            <div class="activity-line"></div>
                        @endif
                        <div class="activity-icon shrink-0 bg-slate-50 border border-slate-200/80 group-hover:bg-indigo-50 group-hover:border-indigo-200 transition-all">
                            <i class="fa-solid {{ $activity['icon'] }} text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                        </div>
                        <div class="flex flex-col justify-center min-w-0">
                            <p class="text-[13px] font-bold text-slate-700 leading-snug group-hover:text-indigo-900 transition-colors">{{ $activity['title'] }}</p>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                                {{ \Illuminate\Support\Carbon::parse($activity['date'])->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <i class="fa-regular fa-clock-rotate-left text-lg"></i>
                        </div>
                        <p class="text-xs text-slate-400 font-bold">Aucune activité récente</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Support card --}}
        <div class="shrink-0" style="padding: 16px 20px; border-top: 1px solid #f1f5f9; background: #fafafa;">
            <a href="{{ route('assure.support') }}"
                class="flex items-center gap-3 p-4 bg-gradient-to-r from-indigo-500 to-violet-600 text-white rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-xl hover:scale-[1.02] transition-all group">
                <div class="w-9 h-9 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white shrink-0">
                    <i class="fa-solid fa-headset text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-black text-white leading-none mb-1">Besoin d'aide ?</p>
                    <p class="text-[10px] text-indigo-100 font-medium">Support disponible 24h/7j</p>
                </div>
                <i class="fa-solid fa-arrow-right text-xs text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
            </a>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('claimsChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 150);
    grad.addColorStop(0, 'rgba(99,102,241,0.18)');
    grad.addColorStop(1, 'rgba(99,102,241,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                data: {!! json_encode($chartData) !!},
                borderColor: '#6366f1',
                borderWidth: 2.5,
                backgroundColor: grad,
                fill: true,
                tension: 0.45,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#6366f1',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false }, tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#94a3b8',
                bodyColor: '#fff',
                bodyFont: { weight: 700 },
                padding: 10,
                cornerRadius: 10,
                displayColors: false
            }},
            scales: {
                y: { display: false, beginAtZero: true },
                x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8' } }
            }
        }
    });
});
</script>
@endpush