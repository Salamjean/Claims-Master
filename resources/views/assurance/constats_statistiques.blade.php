@extends('assurance.layouts.template')
@section('title', 'Statistiques Constats')
@section('page-title', 'Statistiques Constats')

@section('content')
    <div class="mx-auto space-y-6" style="width:100%;">

        {{-- En-tête --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-green-700 flex items-center justify-center shadow-lg shadow-green-200">
                <i class="fa-solid fa-chart-pie text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Statistiques des Constats</h1>
                <p class="text-sm text-slate-500 mt-1">Vue globale des paiements en ligne et des déblocages en espèces — tous
                    agents confondus.</p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-lines text-violet-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-500 font-semibold">Total constats</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-credit-card text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $stats['online_count'] }}</p>
                    <p class="text-xs text-slate-500 font-semibold">Paiements en ligne</p>
                    <p class="text-[11px] text-emerald-600 font-bold mt-0.5">
                        {{ number_format($stats['online_montant'], 0, ',', ' ') }} FCFA collectés
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-unlock text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $stats['deblocage_count'] }}</p>
                    <p class="text-xs text-slate-500 font-semibold">Déblocages agents</p>
                    <p class="text-[11px] text-blue-500 font-bold mt-0.5">Paiements en espèces</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-clock text-amber-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $stats['pending_count'] }}</p>
                    <p class="text-xs text-slate-500 font-semibold">En attente</p>
                    <p class="text-[11px] text-amber-500 font-bold mt-0.5">Non encore payés</p>
                </div>
            </div>
        </div>

        {{-- Cartes totaux encaissés --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div
                class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-200 shadow-sm p-6 flex items-center gap-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-emerald-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                    <i class="fa-solid fa-credit-card text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Total encaissé en ligne
                    </p>
                    <p class="text-3xl font-extrabold text-emerald-700 leading-none">
                        {{ number_format($stats['online_montant'], 0, ',', ' ') }}
                        <span class="text-base font-bold text-emerald-500 ml-1">FCFA</span>
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">{{ $stats['online_count'] }} paiement(s) Wave /
                        mobile money</p>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-blue-200 shadow-sm p-6 flex items-center gap-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center shrink-0 shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-money-bill-wave text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Total encaissé en espèces
                    </p>
                    <p class="text-3xl font-extrabold text-blue-700 leading-none">
                        {{ number_format($stats['deblocage_montant'], 0, ',', ' ') }}
                        <span class="text-base font-bold text-blue-400 ml-1">FCFA</span>
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">{{ $stats['deblocage_count'] }} déblocage(s)
                        agents directs</p>
                </div>
            </div>

        </div>

        {{-- Barre de répartition --}}
        @if ($stats['total'] > 0)
            @php
                $pctOnline = round(($stats['online_count'] / $stats['total']) * 100);
                $pctDeblocage = round(($stats['deblocage_count'] / $stats['total']) * 100);
                $pctPending = 100 - $pctOnline - $pctDeblocage;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-green-600"></i> Répartition des paiements
                </h3>
                <div class="flex h-6 rounded-full overflow-hidden gap-px bg-slate-100">
                    @if ($pctOnline > 0)
                        <div class="bg-emerald-500 h-full" style="width:{{ $pctOnline }}%"
                            title="En ligne — {{ $pctOnline }}%"></div>
                    @endif
                    @if ($pctDeblocage > 0)
                        <div class="bg-blue-500 h-full" style="width:{{ $pctDeblocage }}%"
                            title="Espèces — {{ $pctDeblocage }}%"></div>
                    @endif
                    @if ($pctPending > 0)
                        <div class="bg-amber-300 h-full" style="width:{{ $pctPending }}%"
                            title="En attente — {{ $pctPending }}%"></div>
                    @endif
                </div>
                <div class="flex flex-wrap gap-6 mt-4">
                    <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                        Paiement en ligne — <strong>{{ $pctOnline }}%</strong>
                        <span class="text-slate-400">({{ $stats['online_count'] }} constat(s))</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                        <span class="w-3 h-3 rounded-full bg-blue-500 inline-block shrink-0"></span>
                        Espèces (déblocage) — <strong>{{ $pctDeblocage }}%</strong>
                        <span class="text-slate-400">({{ $stats['deblocage_count'] }} constat(s))</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                        <span class="w-3 h-3 rounded-full bg-amber-300 inline-block shrink-0"></span>
                        En attente — <strong>{{ $pctPending }}%</strong>
                        <span class="text-slate-400">({{ $stats['pending_count'] }} constat(s))</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Top agents --}}
        @if ($topAgents->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-ranking-star text-amber-500"></i> Top agents — paiements en ligne
                </h3>
                <div class="space-y-3">
                    @foreach ($topAgents as $agentName => $data)
                        @php $maxMontant = $topAgents->max('montant'); @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-36 shrink-0 text-xs font-semibold text-slate-700 truncate">{{ $agentName }}
                            </div>
                            <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full"
                                    style="width: {{ $maxMontant > 0 ? round(($data['montant'] / $maxMontant) * 100) : 0 }}%">
                                </div>
                            </div>
                            <div class="w-36 text-right text-xs font-bold text-slate-700 shrink-0">
                                {{ number_format($data['montant'], 0, ',', ' ') }} FCFA
                                <span class="text-slate-400 font-normal">({{ $data['count'] }})</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Historique des paiements --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-clock-rotate-left text-green-600"></i>
                    <h3 class="text-sm font-bold text-slate-700">Historique des paiements</h3>
                </div>

                {{-- Filtre par agent --}}
                @php
                    $agentsList = $history
                        ->map(fn($i) => $i->sinistre->assignedAgent?->name ?? 'Inconnu')
                        ->unique()
                        ->sort()
                        ->values();
                @endphp
                @if ($agentsList->count() > 1)
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                        <i class="fa-solid fa-user-tie text-slate-400 text-xs"></i>
                        <select id="filter-agent"
                            class="text-sm text-slate-700 bg-transparent outline-none font-semibold cursor-pointer">
                            <option value="">Tous les agents</option>
                            @foreach ($agentsList as $agentName)
                                <option value="{{ strtolower($agentName) }}">{{ $agentName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Recherche texte --}}
                <div
                    class="flex items-center gap-2 flex-1 min-w-[200px] bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                    <input id="search-history" type="text" placeholder="Rechercher par n° sinistre, assuré..."
                        class="flex-1 text-sm text-slate-700 placeholder-slate-400 bg-transparent outline-none font-medium">
                    <button id="search-history-clear" onclick="clearHistorySearch()"
                        class="hidden text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
                <span id="search-history-count" class="text-xs text-slate-400 font-semibold hidden"></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[750px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Sinistre</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Assuré</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Agent
                            </th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Type paiement</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Montant</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody" class="divide-y divide-slate-50">
                        @forelse($history as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors"
                                data-searchable="{{ strtolower(($item->sinistre->numero_sinistre ?? 'SI-' . $item->sinistre_id) . ' ' . ($item->sinistre->assure->name ?? '') . ' ' . ($item->sinistre->assure->contact ?? '') . ' ' . ($item->sinistre->assignedAgent->name ?? '') . ' ' . ($item->agent_unlocked ? 'especes agent' : 'en ligne')) }}"
                                data-agent="{{ strtolower($item->sinistre->assignedAgent?->name ?? 'inconnu') }}">
                                <td class="px-5 py-3 text-center">
                                    <span class="text-xs font-bold text-violet-600 bg-violet-50 px-2 py-1 rounded-lg">
                                        {{ $item->sinistre->numero_sinistre ?? 'SI-' . $item->sinistre_id }}
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        {{ str_replace('_', ' ', $item->sinistre->type_sinistre ?? '') }}
                                    </p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $item->sinistre->assure->name ?? '—' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $item->sinistre->assure->contact ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $item->sinistre->assignedAgent->name ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($item->agent_unlocked)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-extrabold rounded-full">
                                            <i class="fa-solid fa-unlock text-[10px]"></i> Espèces (agent)
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-full">
                                            <i class="fa-solid fa-credit-card text-[10px]"></i> En ligne
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center font-black text-sm">
                                    <span class="{{ $item->agent_unlocked ? 'text-blue-700' : 'text-emerald-700' }}">
                                        {{ number_format($item->montant_a_payer ?? 0, 0, ',', ' ') }}
                                        <span class="text-[10px] font-normal">FCFA</span>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center text-xs text-slate-500">
                                    {{ $item->agent_unlocked
                                        ? $item->agent_unlocked_at?->format('d/m/Y H:i')
                                        : $item->redaction_validee_at?->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center text-slate-400 text-sm">
                                    <i class="fa-solid fa-clock-rotate-left text-4xl mb-3 block opacity-20"></i>
                                    Aucun paiement enregistré pour l'instant.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('search-history');
            var agentSel = document.getElementById('filter-agent');
            var countEl = document.getElementById('search-history-count');
            var clearBtn = document.getElementById('search-history-clear');
            var rows = document.querySelectorAll('#history-tbody tr[data-searchable]');

            function applyFilters() {
                var q = input ? input.value.trim().toLowerCase() : '';
                var agent = agentSel ? agentSel.value.toLowerCase() : '';
                var visible = 0;
                rows.forEach(function(row) {
                    var matchText = !q || row.getAttribute('data-searchable').includes(q);
                    var matchAgent = !agent || row.getAttribute('data-agent') === agent;
                    var show = matchText && matchAgent;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                var active = q || agent;
                if (active) {
                    countEl.textContent = visible + ' résultat(s)';
                    countEl.classList.remove('hidden');
                    if (q) clearBtn.classList.remove('hidden');
                    else clearBtn.classList.add('hidden');
                } else {
                    countEl.classList.add('hidden');
                    clearBtn.classList.add('hidden');
                }
            }

            if (input) input.addEventListener('input', applyFilters);
            if (agentSel) agentSel.addEventListener('change', applyFilters);
        });

        function clearHistorySearch() {
            var input = document.getElementById('search-history');
            input.value = '';
            input.dispatchEvent(new Event('input'));
            input.focus();
        }
    </script>
@endpush
