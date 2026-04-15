@extends('personnel.layouts.template')

@section('title', 'Recherche')

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg,#1d3557,#152840)">
                <i class="fa-solid fa-magnifying-glass text-white text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Recherche de dossier</h1>
                <p class="text-sm text-slate-400">Retrouvez un sinistre par assuré, type ou numéro</p>
            </div>
        </div>

        {{-- Formulaire de recherche --}}
        <form method="GET" action="{{ route('personnel.search') }}"
            class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-7">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                        <i class="fa-solid fa-user text-[9px] mr-1 text-[#1d3557]"></i>Nom de l'assuré
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input type="text" name="f_assure" value="{{ $fAssure }}" placeholder="Ex : Diallo, Koné…"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                        <i class="fa-solid fa-car-burst text-[9px] mr-1 text-[#1d3557]"></i>Type de sinistre
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-car-burst text-xs"></i>
                        </span>
                        <input type="text" name="f_type" value="{{ $fType }}"
                            placeholder="Ex : accident, vol, incendie…"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                        <i class="fa-solid fa-hashtag text-[9px] mr-1 text-[#1d3557]"></i>Numéro de sinistre
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-hashtag text-xs"></i>
                        </span>
                        <input type="text" name="f_numero" value="{{ $fNumero }}" placeholder="Ex : SIN-2024-001"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-100">
                @if ($hasFilter)
                    <a href="{{ route('personnel.search') }}"
                        class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-semibold transition-colors">
                        <i class="fa-solid fa-xmark"></i> Effacer la recherche
                    </a>
                @else
                    <span></span>
                @endif
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold bg-[#1d3557] text-white hover:bg-[#152840] transition-colors shadow-sm">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Lancer la recherche
                </button>
            </div>
        </form>

        {{-- Résultats --}}
        @if (!$hasFilter)
            {{-- État vide : invitation --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 opacity-20"
                    style="background: linear-gradient(135deg,#1d3557,#152840)">
                    <i class="fa-solid fa-magnifying-glass text-white text-2xl"></i>
                </div>
                <p class="text-slate-400 text-sm font-medium">Entrez au moins un critère pour lancer la recherche.</p>
            </div>
        @elseif ($resultats && $resultats->isEmpty())
            {{-- Aucun résultat --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                    style="background: rgba(29,53,87,0.08)">
                    <i class="fa-solid fa-folder-open text-[#1d3557] text-2xl opacity-40"></i>
                </div>
                <p class="text-slate-500 font-semibold text-sm mb-1">Aucun dossier trouvé</p>
                <p class="text-slate-400 text-xs">Aucun sinistre ne correspond à vos critères de recherche.</p>
            </div>
        @else
            {{-- Bandeau résultats --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500">
                        {{ $resultats->total() }} résultat{{ $resultats->total() > 1 ? 's' : '' }}
                    </span>
                    @if ($fAssure)
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                            <i class="fa-solid fa-user text-[9px]"></i> {{ $fAssure }}
                        </span>
                    @endif
                    @if ($fType)
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                            <i class="fa-solid fa-car-burst text-[9px]"></i> {{ $fType }}
                        </span>
                    @endif
                    @if ($fNumero)
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                            <i class="fa-solid fa-hashtag text-[9px]"></i> {{ $fNumero }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Tableau résultats --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100" style="background: rgba(36,58,143,0.04)">
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Sinistre</th>
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Assuré</th>
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Statut</th>
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Documents</th>
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Agent</th>
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Date</th>
                            <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($resultats as $sinistre)
                            @php
                                $totalDocs = $sinistre->documentsAttendus->count();
                                $fournisDocs = $sinistre->documentsAttendus
                                    ->where('status_client', 'uploaded')
                                    ->count();
                                $manquantsDocs = $sinistre->documentsAttendus
                                    ->where('status_client', 'pending')
                                    ->count();
                                $step = $sinistre->workflow_step ?? '';
                                $status = $sinistre->status ?? '';
                                $isMyDossier = $sinistre->assigned_personnel_id === $personnel->id;
                                $isInPool = $sinistre->assigned_personnel_id === null;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">

                                {{-- Sinistre --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="font-semibold text-slate-800">{{ $sinistre->type_sinistre }}</div>
                                    @if ($sinistre->numero_sinistre)
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $sinistre->numero_sinistre }}</div>
                                    @endif
                                </td>

                                {{-- Assuré --}}
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="font-medium text-slate-700">{{ $sinistre->assure->name . ' ' . $sinistre->assure->prenom }}</span>
                                </td>

                                {{-- Statut --}}
                                <td class="px-5 py-4 text-center">
                                    @if (str_starts_with($step, 'closed_validated'))
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Validé & Clôturé
                                        </span>
                                    @elseif(str_starts_with($step, 'closed_rejected') || $step === 'rejected_no_warranty')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rejeté & Clôturé
                                        </span>
                                    @elseif($step === 'manager_review')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                            <i class="fa-solid fa-clock text-[10px]"></i> En révision
                                        </span>
                                    @elseif($step === 'docs_pending')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100">
                                            <i class="fa-solid fa-file-circle-exclamation text-[10px]"></i> Compléments
                                            requis
                                        </span>
                                    @elseif($step === 'expert_garage_assigned')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                            <i class="fa-solid fa-user-tie text-[10px]"></i> Expert/Garage assigné
                                        </span>
                                    @elseif($step === 'warranty_verified_pending_assignment')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <i class="fa-solid fa-shield-check text-[10px]"></i> Garantie vérifiée
                                        </span>
                                    @elseif($status === 'traite')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <i class="fa-solid fa-file-circle-check text-[10px]"></i> Constat terminé
                                        </span>
                                    @elseif($status === 'en_cours')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100">
                                            <i class="fa-solid fa-spinner text-[10px]"></i> En cours
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-500 border border-slate-100">
                                            {{ str_replace('_', ' ', $step ?: $status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Documents --}}
                                <td class="px-5 py-4 text-center"
                                    style="display:flex;justify-content:center;flex-direction:column;align-items:center;">
                                    @if ($totalDocs > 0)
                                        @php
                                            $docsJson = $sinistre->documentsAttendus
                                                ->map(
                                                    fn($d) => [
                                                        'nom' => $d->nom_document,
                                                        'statut' => $d->status_client,
                                                        'requis' => $d->is_mandatory,
                                                    ],
                                                )
                                                ->toJson();
                                        @endphp
                                        <button type="button"
                                            onclick="showDocs({{ $sinistre->id }}, {{ $docsJson }})"
                                            class="flex flex-col items-center gap-1.5">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="flex items-center gap-1 text-xs font-semibold {{ $fournisDocs === $totalDocs ? 'text-emerald-600' : 'text-slate-700' }}">
                                                    <i class="fa-solid fa-file-lines text-[10px]"></i>
                                                    {{ $fournisDocs }} / {{ $totalDocs }}
                                                </div>
                                                @if ($manquantsDocs > 0)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-red-50 text-red-600 border border-red-100">
                                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                                        {{ $manquantsDocs }} manquant{{ $manquantsDocs > 1 ? 's' : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $fournisDocs === $totalDocs ? 'bg-emerald-500' : 'bg-[#1d3557]' }}"
                                                    style="width:{{ $totalDocs > 0 ? round(($fournisDocs / $totalDocs) * 100) : 0 }}%">
                                                </div>
                                            </div>
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#1d3557]/10 text-[#1d3557] hover:bg-[#1d3557]/20 transition-colors border border-[#1d3557]/15">
                                                <i class="fa-solid fa-eye text-[9px]"></i> Voir les documents
                                            </span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Aucun requis</span>
                                    @endif
                                </td>

                                {{-- Agent assigné --}}
                                <td class="px-5 py-4 text-center">
                                    @if ($sinistre->assignedPersonnel)
                                        @if ($isMyDossier)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#1d3557]/10 text-[#1d3557] border border-[#1d3557]/15">
                                                <i class="fa-solid fa-circle-user text-[9px]"></i> Moi
                                            </span>
                                        @else
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="text-xs font-semibold text-slate-700">{{ $sinistre->assignedPersonnel->name }}
                                                    {{ $sinistre->assignedPersonnel->prenom }}</span>
                                                <span
                                                    class="text-[10px] text-slate-400">{{ $sinistre->assignedPersonnel->code_user }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-600 border border-amber-100">
                                            <i class="fa-solid fa-inbox text-[9px]"></i> Pool
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-5 py-4 text-slate-400 text-xs text-center">
                                    {{ $sinistre->created_at->format('d/m/Y H:i') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        @if ($isMyDossier)
                                            <a href="{{ route('personnel.sinistres.review', $sinistre) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#1d3557]/10 text-[#1d3557] hover:bg-[#1d3557]/20 transition-all">
                                                <i class="fa-solid fa-magnifying-glass text-xs"></i> Examiner
                                            </a>
                                        @elseif ($isInPool)
                                            <form action="{{ route('personnel.sinistres.claim', $sinistre) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all border border-emerald-100">
                                                    <i class="fa-solid fa-hand text-xs"></i> Récupérer
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Assigné</span>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($resultats->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100">
                        {{ $resultats->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function showDocs(sinistreId, docs) {
                if (!docs || docs.length === 0) {
                    Swal.fire({
                        title: 'Documents',
                        text: 'Aucun document requis.',
                        icon: 'info',
                        confirmButtonColor: '#1d3557',
                        scrollbarPadding: false,
                        heightAuto: false
                    });
                    return;
                }
                const rows = docs.map(doc => {
                    const isUploaded = doc.statut === 'uploaded';
                    const icon = isUploaded ? '<span style="color:#16a34a;font-size:13px;">&#x2714;</span>' :
                        '<span style="color:#dc2626;font-size:13px;">&#x2716;</span>';
                    const badge = isUploaded ?
                        '<span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">Fourni</span>' :
                        '<span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">Manquant</span>';
                    const requis = doc.requis ?
                        '<span style="color:#f97316;font-size:10px;font-weight:700;margin-left:4px;">● Requis</span>' :
                        '';
                    return `<tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 12px;text-align:left;">${icon}<span style="margin-left:8px;font-size:13px;color:#1e293b;font-weight:600;">${doc.nom}</span>${requis}</td>
                        <td style="padding:10px 12px;text-align:right;">${badge}</td>
                    </tr>`;
                }).join('');
                const fournis = docs.filter(d => d.statut === 'uploaded').length;
                const total = docs.length;
                const pct = Math.round((fournis / total) * 100);
                const barColor = fournis === total ? '#16a34a' : '#1d3557';
                Swal.fire({
                    title: '<span style="font-size:16px;font-weight:800;color:#1d3557;">📋 Documents du dossier</span>',
                    html: `<div style="margin-bottom:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-size:12px;color:#64748b;font-weight:600;">${fournis} / ${total} documents fournis</span>
                            <span style="font-size:12px;font-weight:700;color:${barColor};">${pct}%</span>
                        </div>
                        <div style="height:6px;background:#e2e8f0;border-radius:9999px;overflow:hidden;">
                            <div style="height:100%;width:${pct}%;background:${barColor};border-radius:9999px;transition:width 0.4s;"></div>
                        </div>
                    </div>
                    <table style="width:100%;border-collapse:collapse;"><tbody>${rows}</tbody></table>`,
                    confirmButtonText: 'Fermer',
                    confirmButtonColor: '#1d3557',
                    width: 520,
                    scrollbarPadding: false,
                    heightAuto: false,
                    customClass: {
                        popup: 'text-left'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                });
            }
        </script>
    @endpush
@endsection
