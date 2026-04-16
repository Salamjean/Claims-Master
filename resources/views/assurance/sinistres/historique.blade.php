@extends('assurance.layouts.template')

@section('title', 'Historique des dossiers clôturés')

@section('content')
    <div x-data="{ filterOpen: {{ $hasFilter ? 'true' : 'false' }} }">

        {{-- Backdrop --}}
        <div x-show="filterOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="filterOpen = false"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40" x-cloak></div>

        {{-- Drawer filtres --}}
        <div x-show="filterOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 flex flex-col" x-cloak>

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100"
                style="background: linear-gradient(135deg,#1d3557,#152840)">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-white text-sm"></i>
                    <span class="text-sm font-bold text-white">Filtres</span>
                </div>
                <button @click="filterOpen = false" class="text-white/60 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('assurance.sinistres.historique') }}"
                class="flex-1 flex flex-col overflow-y-auto">
                <div class="flex-1 px-5 py-5 space-y-5">

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-user text-[9px] mr-1 text-[#1d3557]"></i>Assuré
                        </label>
                        <input type="text" name="f_assure" value="{{ $fAssure }}" placeholder="Nom de l'assuré…"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-hashtag text-[9px] mr-1 text-[#1d3557]"></i>N° sinistre
                        </label>
                        <input type="text" name="f_numero" value="{{ $fNumero }}" placeholder="SIN-…"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-car-burst text-[9px] mr-1 text-[#1d3557]"></i>Type de sinistre
                        </label>
                        <input type="text" name="f_type" value="{{ $fType }}" placeholder="Accident, incendie…"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-flag text-[9px] mr-1 text-[#1d3557]"></i>Décision finale
                        </label>
                        <select name="f_decision"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all bg-white">
                            <option value="">Toutes</option>
                            <option value="closed_validated" {{ $fDecision === 'closed_validated' ? 'selected' : '' }}>
                                Validé &amp; clôturé</option>
                            <option value="closed_rejected" {{ $fDecision === 'closed_rejected' ? 'selected' : '' }}>Rejeté
                            </option>
                            <option value="rejected_no_warranty"
                                {{ $fDecision === 'rejected_no_warranty' ? 'selected' : '' }}>Rejeté (non couvert)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-user-tie text-[9px] mr-1 text-[#1d3557]"></i>Personnel traitant
                        </label>
                        <input type="text" name="f_personnel" value="{{ $fPersonnel }}"
                            placeholder="Nom du gestionnaire…"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-slate-100 space-y-2">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-colors"
                        style="background:#1d3557">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Appliquer
                    </button>
                    @if ($hasFilter)
                        <a href="{{ route('assurance.sinistres.historique') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-red-50 text-red-500 border border-red-100 hover:bg-red-100 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i> Effacer les filtres
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Historique des dossiers</h1>
                <p class="text-sm text-slate-400">Tous les sinistres clôturés de votre compagnie</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('assurance.sinistres.index') }}"
                    class="px-4 py-2 border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 rounded-xl text-sm font-semibold transition-all">
                    <i class="fa-solid fa-arrow-left mr-1.5 text-xs"></i> Dossiers actifs
                </a>
                <button @click="filterOpen = true"
                    class="relative inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all
                        {{ $hasFilter ? 'bg-[#1d3557] text-white border-[#1d3557] shadow-md' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-sliders text-sm"></i>
                    Filtres
                    @if ($hasFilter)
                        @php $activeCount = (int)!empty($fAssure) + (int)!empty($fType) + (int)!empty($fNumero) + (int)!empty($fDecision) + (int)!empty($fPersonnel); @endphp
                        <span
                            class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-white text-[#1d3557] text-[10px] font-black">{{ $activeCount }}</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Badges filtres actifs --}}
        @if ($hasFilter)
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs text-slate-400 font-medium">Filtres actifs :</span>
                @if ($fAssure)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                        <i class="fa-solid fa-user text-[9px]"></i> {{ $fAssure }}
                    </span>
                @endif
                @if ($fNumero)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                        <i class="fa-solid fa-hashtag text-[9px]"></i> {{ $fNumero }}
                    </span>
                @endif
                @if ($fType)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                        <i class="fa-solid fa-car-burst text-[9px]"></i> {{ $fType }}
                    </span>
                @endif
                @if ($fDecision)
                    @php
                        $decisionLabels = [
                            'closed_validated' => 'Validé & clôturé',
                            'closed_rejected' => 'Rejeté',
                            'rejected_no_warranty' => 'Non couvert',
                        ];
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                        <i class="fa-solid fa-flag text-[9px]"></i> {{ $decisionLabels[$fDecision] ?? $fDecision }}
                    </span>
                @endif
                @if ($fPersonnel)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                        <i class="fa-solid fa-user-tie text-[9px]"></i> {{ $fPersonnel }}
                    </span>
                @endif
            </div>
        @endif

        {{-- KPI résumé --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-folder-open text-slate-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800">{{ $sinistres->total() }}</p>
                    <p class="text-xs text-slate-400 font-semibold">Dossiers clôturés</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
                <div>
                    @php
                        $validatedCount = \App\Models\Sinistre::where('assurance_id', auth('user')->id())
                            ->where('status', 'cloture')
                            ->where('workflow_step', 'closed_validated')
                            ->count();
                    @endphp
                    <p class="text-2xl font-black text-emerald-700">{{ $validatedCount }}</p>
                    <p class="text-xs text-slate-400 font-semibold">Validés</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                </div>
                <div>
                    @php
                        $rejectedCount = \App\Models\Sinistre::where('assurance_id', auth('user')->id())
                            ->where('status', 'cloture')
                            ->whereIn('workflow_step', ['closed_rejected', 'rejected_no_warranty'])
                            ->count();
                    @endphp
                    <p class="text-2xl font-black text-red-600">{{ $rejectedCount }}</p>
                    <p class="text-xs text-slate-400 font-semibold">Rejetés</p>
                </div>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">N°
                                Sinistre</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Assuré</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Type
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Personnel</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Décision</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Clôturé le</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($sinistres as $s)
                            @php
                                $decision = $s->workflow_step;
                                $decisionLabel = match ($decision) {
                                    'closed_validated' => [
                                        'label' => 'Validé',
                                        'color' => 'bg-emerald-100 text-emerald-700',
                                    ],
                                    'closed_rejected' => ['label' => 'Rejeté', 'color' => 'bg-red-100 text-red-600'],
                                    'rejected_no_warranty' => [
                                        'label' => 'Non couvert',
                                        'color' => 'bg-orange-100 text-orange-600',
                                    ],
                                    default => ['label' => 'Clôturé', 'color' => 'bg-slate-100 text-slate-600'],
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 text-center">
                                    <span class="text-xs font-bold text-violet-600 bg-violet-50 px-2 py-1 rounded-lg">
                                        {{ $s->numero_sinistre ?? 'SI-' . $s->id }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $s->assure->name ?? '—' }}
                                        {{ $s->assure->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-slate-400">{{ $s->assure->contact ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="text-xs text-slate-600 font-medium">
                                        {{ str_replace('_', ' ', $s->type_sinistre ?? '—') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if ($s->assignedPersonnel)
                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ $s->assignedPersonnel->name }} {{ $s->assignedPersonnel->prenom ?? '' }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-700">{{ Auth::user()->name ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $decisionLabel['color'] }}">
                                        {{ $decisionLabel['label'] }}
                                    </span>
                                    @if ($s->motif_rejet)
                                        <p class="text-[10px] text-slate-400 mt-1 max-w-[160px] truncate"
                                            title="{{ $s->motif_rejet }}">
                                            {{ $s->motif_rejet }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <p class="text-xs font-semibold text-slate-600">{{ $s->updated_at->format('d/m/Y') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">{{ $s->updated_at->format('H:i') }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('assurance.sinistres.show', $s) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                                        <i class="fa-solid fa-eye text-xs"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-slate-400">
                                        <i class="fa-solid fa-folder-open text-4xl text-slate-200"></i>
                                        <p class="text-sm font-semibold">Aucun dossier clôturé trouvé</p>
                                        @if ($hasFilter)
                                            <a href="{{ route('assurance.sinistres.historique') }}"
                                                class="text-xs text-[#1d3557] font-bold hover:underline">
                                                Effacer les filtres
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sinistres->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $sinistres->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
