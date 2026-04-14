@extends('assure.layouts.template')

@section('title', 'Tableau de bord personnel')
@section('page-title', 'Tableau de bord')

@section('content')
    <div class="mx-auto" style="width: 100%;">

        {{-- ── SECTION 1 : HERO DYNAMIQUE (GLASSMORPHISM) ── --}}
        <div class="relative overflow-hidden rounded-[2.5rem] mb-10 shadow-2xl animate-in" style="--delay: 0.1s">
            <!-- Fond dégradé animé -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#213685] via-[#1a2b6d] to-[#152252]"></div>

            <!-- Éléments décoratifs -->
            <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-blue-500/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-[-20%] left-[10%] w-80 h-80 bg-emerald-500/10 rounded-full blur-[80px]"></div>

            <div
                class="relative z-10 px-8 py-6 md:px-12 md:py-6 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-center md:text-left">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_#34d399]"></span>
                        <span class="text-xs font-bold text-emerald-100 uppercase tracking-widest">Compte vérifié</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-white mb-1 leading-tight">
                        Ravi de vous revoir, <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-emerald-300">{{ $user->prenom ?? $user->name }}</span>
                    </h1>
                    <p class="text-blue-100/50 text-sm font-medium max-w-lg">
                        Votre sécurité est notre priorité. Suivez vos dossiers en temps réel.
                    </p>
                </div>

                <div class="shrink-0">
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4 text-center">
                        <p class="text-blue-200/40 text-[9px] font-black uppercase tracking-[0.2em] mb-1">Code Assuré</p>
                        <div class="flex items-center gap-2 justify-center mb-3">
                            <span
                                class="text-xl font-black text-white font-mono tracking-tighter">{{ $user->code_user }}</span>
                            <button
                                onclick="navigator.clipboard.writeText('{{ $user->code_user }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Copié !', showConfirmButton:false, timer:1500})"
                                class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-all text-white/60 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                        <a href="{{ route('assure.sinistres.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-black rounded-xl transition-all shadow-lg shadow-red-500/20 text-xs">
                            <i class="fa-solid fa-triangle-exclamation text-lg animate-pulse"></i>
                            <span>DÉCLARER SINISTRE</span>
                            <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($countConstatsNonRegles) && $countConstatsNonRegles > 0)
            <div class="mb-10 animate-in" style="--delay: 0.15s">
                <div
                    class="bg-gradient-to-r from-orange-400 to-amber-500 rounded-[2rem] p-6 text-white shadow-xl shadow-orange-200/40 border border-orange-300/30">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                                <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black">Règlement en attente</h2>
                                <p class="text-orange-50/80 text-sm font-medium">Vous avez <span
                                        class="font-black underline">{{ $countConstatsNonRegles }}</span>
                                    {{ $countConstatsNonRegles > 1 ? 'constats rédigés' : 'constat rédigé' }} qui
                                    {{ $countConstatsNonRegles > 1 ? 'attendent' : 'attend' }} d'être débloqué(s).</p>
                            </div>
                        </div>
                        <a href="{{ route('assure.constats.prets') }}"
                            class="px-8 py-3 bg-white text-orange-600 font-black rounded-2xl shadow-lg shadow-black/5 hover:scale-105 transition-all active:scale-95 whitespace-nowrap">
                            DÉBLOQUER MAINTENANT
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── SECTION 2 : BENTO GRID DISPOSITION ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

            {{-- COL GAUCHE : Suivi dernier sinistre (2/3) --}}
            <div class="lg:col-span-2 space-y-8">

                @if($dernierSinistre)
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden animate-in"
                        style="--delay: 0.2s">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-10">
                                <div>
                                    <h2 class="text-xl font-black text-slate-800">Suivi de votre dossier en cours</h2>
                                    <p class="text-sm text-slate-400 font-medium mt-1">Dernière mise à jour :
                                        {{ $dernierSinistre->updated_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-widest border border-blue-100 mb-2">
                                        {{ $dernierSinistre->numero_sinistre }}
                                    </span>
                                    <span
                                        class="text-sm font-bold text-slate-700">{{ str_replace('_', ' ', $dernierSinistre->type_sinistre) }}</span>
                                </div>
                            </div>

                            {{-- Tracker Graphique --}}
                            <div class="relative py-4">
                                <!-- Ligne de fond -->
                                <div class="absolute top-[21px] left-0 right-0 h-1 bg-slate-100 rounded-full"></div>

                                @php
                                    $isAmiable = ($dernierSinistre->constat && $dernierSinistre->constat->methode_redaction === 'Amiable');
                                    
                                    if ($isAmiable) {
                                        $steps = [
                                            ['id' => 'declaration', 'label' => 'Déclaration', 'icon' => 'fa-paper-plane'],
                                            ['id' => 'ai_analysis', 'label' => 'Analyse IA', 'icon' => 'fa-robot'],
                                            ['id' => 'redaction', 'label' => 'Constat rédigé', 'icon' => 'fa-file-signature'],
                                            ['id' => 'review', 'label' => 'Révision Assurance', 'icon' => 'fa-shield-halved'],
                                            ['id' => 'cloture', 'label' => 'Clôture', 'icon' => 'fa-check-double']
                                        ];
                                    } else {
                                        $steps = [
                                            ['id' => 'declaration', 'label' => 'Déclaration', 'icon' => 'fa-paper-plane'],
                                            ['id' => 'agent', 'label' => 'Agent assigné', 'icon' => 'fa-user-shield'],
                                            ['id' => 'constat', 'label' => 'Constat terrain', 'icon' => 'fa-clipboard-check'],
                                            ['id' => 'redaction', 'label' => 'Constat rédigé', 'icon' => 'fa-file-invoice'],
                                            ['id' => 'review', 'label' => 'Révision Assurance', 'icon' => 'fa-shield-halved'],
                                            ['id' => 'cloture', 'label' => 'Clôture', 'icon' => 'fa-check-double']
                                        ];
                                    }

                                    // Calcul de l'avancement précis
                                    $status = $dernierSinistre->status;
                                    $isAssigned = !empty($dernierSinistre->assigned_agent_id);
                                    $isConstatTerrainDone = in_array($status, ['constat_terrain_ok', 'traite', 'cloture']);
                                    $isRedactionDone = in_array($status, ['traite', 'cloture']);
                                    $isDocsComplete = $dernierSinistre->documentsAttendus()->where('is_mandatory', true)->where('status_client', 'pending')->count() === 0;
                                    $isReviewStarted = ($dernierSinistre->workflow_step === 'manager_review' || $status === 'cloture');
                                    $isClosed = ($status === 'cloture');

                                    // État de chaque étape pour le style
                                    if ($isAmiable) {
                                        $stepStates = [
                                            0 => true, // Déclaration
                                            1 => true, // Analyse IA (Automatique en mode amiable)
                                            2 => true, // Constat rédigé (Immédiat)
                                            3 => $isReviewStarted,
                                            4 => $isClosed
                                        ];
                                        
                                        $currentStepIndex = 2; // Par défaut au moins rédigé en amiable
                                        if ($isClosed) $currentStepIndex = 4;
                                        elseif ($isReviewStarted) $currentStepIndex = 3;
                                    } else {
                                        $stepStates = [
                                            0 => true,
                                            1 => $isAssigned,
                                            2 => $isConstatTerrainDone,
                                            3 => $isRedactionDone,
                                            4 => $isReviewStarted,
                                            5 => $isClosed
                                        ];

                                        $currentStepIndex = 0;
                                        if ($isClosed) $currentStepIndex = 5;
                                        elseif ($isReviewStarted) $currentStepIndex = 4;
                                        elseif ($isRedactionDone) $currentStepIndex = 3;
                                        elseif ($isConstatTerrainDone) $currentStepIndex = 2;
                                        elseif ($isAssigned) $currentStepIndex = 1;
                                    }
                                @endphp

                                <div class="flex justify-between items-start relative z-10">
                                    @foreach($steps as $index => $step)
                                        @php
                                            $isActive = $stepStates[$index] ?? false;
                                            $isCurrent = $index === $currentStepIndex;
                                        @endphp
                                        <div class="flex flex-col items-center group">
                                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center transition-all duration-500
                                                        {{ $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40 text-lg' : 'bg-white text-slate-300 border-2 border-slate-100' }}
                                                        {{ $isCurrent ? 'ring-4 ring-blue-50 scale-110' : '' }}">
                                                <i class="fa-solid {{ $step['icon'] }}"></i>
                                            </div>
                                            <span class="mt-4 text-[11px] font-black uppercase tracking-tight text-center max-w-[80px]
                                                        {{ $isActive ? 'text-blue-600' : 'text-slate-400' }}">
                                                {{ $step['label'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div
                                class="mt-12 flex flex-col md:flex-row items-center gap-6 p-6 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                                @if(!$isAmiable && $dernierSinistre->assignedAgent)
                                    <div class="flex items-center gap-4 border-r border-slate-200 pr-10">
                                        <div
                                            class="w-12 h-12 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center font-black text-blue-600 shadow-sm">
                                            {{ substr($dernierSinistre->assignedAgent->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Agent sur
                                                place</p>
                                            <p class="text-sm font-bold text-slate-800">{{ $dernierSinistre->assignedAgent->name }}
                                            </p>
                                        </div>
                                    </div>
                                @elseif($isAmiable)
                                    <div class="flex items-center gap-4 border-r border-slate-200 pr-10">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
                                            <i class="fa-solid fa-bolt-lightning"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Mode</p>
                                            <p class="text-sm font-bold text-slate-800">Constat Amiable</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-1 text-center md:text-left">
                                    <p class="text-sm font-bold text-slate-600">
                                        @if($isAmiable)
                                            @if($isClosed) 
                                                Votre dossier est clôturé. Merci de votre confiance.
                                            @elseif($isReviewStarted)
                                                Votre constat est en cours d'analyse finale par les experts de l'assurance.
                                            @else
                                                Le constat amiable a été généré avec succès. <span class="text-blue-600">Dossier transmis pour révision.</span>
                                            @endif
                                        @else
                                            @if($currentStepIndex == 0)
                                                Votre demande est en attente d'affectation d'un agent.
                                            @elseif($currentStepIndex == 1)
                                                @if($isDocsComplete)
                                                    Documents reçus. <span class="text-blue-600">L'agent est en route pour réaliser le
                                                        constat terrain.</span>
                                                @else
                                                    L'agent est en route. <span class="text-amber-600">N'oubliez pas de soumettre vos
                                                        documents requis.</span>
                                                @endif
                                            @elseif($currentStepIndex == 2)
                                                Constat terrain effectué. <span class="text-blue-600">L'agent procède actuellement à la
                                                    rédaction officielle de votre document.</span>
                                            @elseif($currentStepIndex == 3)
                                                @if(!$isDocsComplete)
                                                    Le constat est rédigé. <span class="text-red-500">Action requise : Veuillez fournir vos
                                                        documents pour validation par l'assurance.</span>
                                                @else
                                                    Constat rédigé et documents reçus. <span class="text-blue-600">Dossier transmis pour
                                                        révision.</span>
                                                @endif
                                            @elseif($currentStepIndex == 4)
                                                Vos documents sont en cours d'analyse par les experts de l'assurance.
                                            @else
                                                Votre dossier est clôturé. Merci de votre confiance.
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-3 shrink-0">
                                    @if($dernierSinistre->constat && $dernierSinistre->constat->methode_redaction === 'Amiable')
                                        <a href="{{ route('assure.sinistres.constat.download', $dernierSinistre->id) }}"
                                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-xl shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span>CONSTAT</span>
                                        </a>
                                    @endif
                                    @if($dernierSinistre->assigned_agent_id && $dernierSinistre->status === 'en_cours')
                                        <a href="{{ route('assure.sinistres.tracking', $dernierSinistre->id) }}"
                                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black text-sm rounded-xl shadow-lg shadow-blue-500/20 flex items-center gap-2 group pulse-animation">
                                            <i class="fa-solid fa-location-crosshairs animate-spin-slow"></i>
                                            <span>SUIVRE L'AGENT</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('assure.sinistres.show', $dernierSinistre->id) }}"
                                        class="px-6 py-3 bg-white hover:bg-slate-800 hover:text-white transition-all text-slate-800 font-bold text-sm rounded-xl border border-slate-200 shadow-sm flex items-center gap-2 group">
                                        <span>Voir détails</span>
                                        <i
                                            class="fa-solid fa-chevron-right text-[10px] opacity-40 group-hover:opacity-100 transition-all"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- État vide si aucun sinistre --}}
                    <div class="bg-white rounded-[2rem] border border-slate-100 p-12 text-center animate-in"
                        style="--delay: 0.2s">
                        <div
                            class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 text-blue-300 text-3xl">
                            <i class="fa-solid fa-shield-cat"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 mb-3">Aucun sinistre en cours</h2>
                        <p class="text-slate-400 font-medium max-w-sm mx-auto mb-8">Tout est calme sur votre route. En cas de
                            problème, notre assistance est là pour vous 24h/24.</p>
                        <a href="{{ route('assure.sinistres.create') }}"
                            class="px-8 py-3.5 bg-blue-600 text-white font-black rounded-2xl shadow-lg shadow-blue-50/40 hover:bg-blue-700 transition-all">Déclarer
                            maintenant</a>
                    </div>
                @endif

                {{-- Stat Cards --}}
                {{-- SECTION : ANALYSE & DÉTAILS (Déplacé ici) ── --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Graphique Sinistres -->
                    <div class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-xl shadow-slate-200/50 animate-in"
                        style="--delay: 0.3s">
                        <h3 class="text-xs font-black text-slate-400 mb-6 uppercase tracking-[0.2em]">Tendances mensuelles
                        </h3>
                        <div class="h-48 relative">
                            <canvas id="claimsChart"></canvas>
                        </div>
                    </div>

                    <!-- Profil Shortcut (Déplacé ici) -->
                    <div class="bg-[#1a1b24] rounded-[2rem] p-8 text-white relative overflow-hidden group animate-in flex flex-col justify-between"
                        style="--delay: 0.4s">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-[80px]"></div>
                        <div class="flex items-center gap-4 relative z-10 mb-6">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center overflow-hidden">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-lg font-black">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-base font-black">{{ $user->name }}</p>
                                <p class="text-[10px] text-blue-300/60 font-bold font-mono tracking-widest">
                                    {{ $user->code_user }}</p>
                            </div>
                        </div>

                        <a href="{{ route('assure.profile') }}"
                            class="w-full py-3 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center gap-3 font-black text-xs hover:bg-white hover:text-slate-900 transition-all group relative z-10">
                            <span>GÉRER MON COMPTE</span>
                            <i
                                class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- COL DROITE : Activités et Charts --}}
            <div class="space-y-8 h-full">

                {{-- Activités Récentes --}}
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 flex flex-col h-full animate-in"
                    style="--delay: 0.5s">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Activités Récentes</h3>
                        <i class="fa-solid fa-bell text-slate-200"></i>
                    </div>

                    <div class="space-y-6 flex-1">
                        @forelse($recentActivities as $activity)
                            <div class="flex gap-4 relative group">
                                @if(!$loop->last)
                                    <div class="absolute left-6 top-12 bottom-[-24px] w-0.5 bg-slate-50"></div>
                                @endif
                                <div
                                    class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 group-hover:bg-{{ $activity['color'] }}-50 group-hover:border-{{ $activity['color'] }}-100 transition-colors">
                                    <i
                                        class="fa-solid {{ $activity['icon'] }} text-slate-400 group-hover:text-{{ $activity['color'] }}-500 transition-colors"></i>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <p
                                        class="text-sm font-bold text-slate-700 leading-tight group-hover:text-slate-900 transition-colors">
                                        {{ $activity['title'] }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-1">
                                        {{ \Illuminate\Support\Carbon::parse($activity['date'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-xs text-slate-400 font-bold italic">Aucune activité récente.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-10 pt-6 border-t border-slate-50">
                        <div
                            class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100/50 flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-500">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-blue-900 uppercase leading-none mb-1">Besoin d'aide ?</p>
                                <p class="text-[10px] text-blue-600 font-bold">Support 24/7 disponible</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: var(--delay, 0s);
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Chart Config Global
                Chart.defaults.font.family = "'Outfit', sans-serif";
                Chart.defaults.color = '#94a3b8';

                // 1. Line Chart : Sinistres
                const ctx = document.getElementById('claimsChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Sinistres',
                            data: {!! json_encode($chartData) !!},
                            borderColor: '#3b82f6',
                            borderWidth: 4,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#3b82f6',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { display: false },
                            x: { grid: { display: false }, border: { display: false } }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection