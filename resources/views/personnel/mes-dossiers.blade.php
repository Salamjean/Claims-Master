@extends('personnel.layouts.template')

@section('title', 'Mes Dossiers')
@section('page-title', 'Mes Dossiers')

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

            {{-- Header drawer --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100"
                style="background: linear-gradient(135deg,#1d3557,#152840)">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-white text-sm"></i>
                    <span class="text-sm font-bold text-white">Filtres de recherche</span>
                </div>
                <button @click="filterOpen = false" class="text-white/60 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Corps drawer --}}
            <form method="GET" action="{{ route('personnel.mes-dossiers') }}"
                class="flex-1 flex flex-col overflow-y-auto">
                <div class="flex-1 px-5 py-5 space-y-5">

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-user text-[9px] mr-1 text-[#1d3557]"></i>Nom de l'assuré
                        </label>
                        <input type="text" name="f_assure" value="{{ $fAssure ?? '' }}" placeholder="Ex : Diallo, Koné…"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-car-burst text-[9px] mr-1 text-[#1d3557]"></i>Type de sinistre
                        </label>
                        <input type="text" name="f_type" value="{{ $fType ?? '' }}" placeholder="Ex : accident, vol…"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <i class="fa-solid fa-hashtag text-[9px] mr-1 text-[#1d3557]"></i>Numéro de sinistre
                        </label>
                        <input type="text" name="f_numero" value="{{ $fNumero ?? '' }}" placeholder="Ex : SIN-2024-001"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-[#1d3557]/50 focus:ring-2 focus:ring-[#1d3557]/10 transition-all">
                    </div>

                </div>

                {{-- Footer drawer --}}
                <div class="px-5 py-4 border-t border-slate-100 space-y-2">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-[#1d3557] text-white hover:bg-[#152840] transition-colors shadow-sm">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Appliquer les filtres
                    </button>
                    @if ($hasFilter)
                        <a href="{{ route('personnel.mes-dossiers') }}"
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
                <h1 class="text-xl font-bold text-slate-800">Mes Dossiers</h1>
                <p class="text-sm text-slate-400">Dossiers que vous avez récupérés pour traitement</p>
            </div>
            <button @click="filterOpen = true"
                class="relative inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all
                    {{ $hasFilter ? 'bg-[#1d3557] text-white border-[#1d3557] shadow-md' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                <i class="fa-solid fa-sliders text-sm"></i>
                Filtres
                @if ($hasFilter)
                    @php $activeCount = (int)!empty($fAssure) + (int)!empty($fType) + (int)!empty($fNumero); @endphp
                    <span
                        class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-white text-[#1d3557] text-[10px] font-black">{{ $activeCount }}</span>
                @endif
            </button>
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
                <a href="{{ route('personnel.mes-dossiers') }}"
                    class="text-xs text-red-500 hover:text-red-700 font-semibold ml-1">Tout effacer</a>
            </div>
        @endif

        {{-- Tableau --}}
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
                        <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Avis
                            Claims AI</th>
                        <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date
                        </th>
                        <th class="text-center px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mesDossiers as $sinistre)
                        @php
                            $totalDocs = $sinistre->documentsAttendus->count();
                            $fournisDocs = $sinistre->documentsAttendus->where('status_client', 'uploaded')->count();
                            $manquantsDocs = $sinistre->documentsAttendus->where('status_client', 'pending')->count();
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 text-center">
                                <div class="font-semibold text-slate-800">{{ $sinistre->type_sinistre }}</div>
                                @if ($sinistre->numero_sinistre)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $sinistre->numero_sinistre }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="font-medium text-slate-700">{{ $sinistre->assure->name . ' ' . $sinistre->assure->prenom }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $step = $sinistre->workflow_step ?? '';
                                    $status = $sinistre->status ?? '';
                                @endphp
                                @if (str_starts_with($step, 'closed_validated'))
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Validé &amp; Clôturé
                                    </span>
                                @elseif(str_starts_with($step, 'closed_rejected') || $step === 'rejected_no_warranty')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rejeté &amp; Clôturé
                                    </span>
                                @elseif($step === 'manager_review')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <i class="fa-solid fa-clock text-[10px]"></i> En révision
                                    </span>
                                @elseif($step === 'docs_pending')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100">
                                        <i class="fa-solid fa-file-circle-exclamation text-[10px]"></i> Compléments requis
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
                                @elseif($status === 'en_attente')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> En attente
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-500 border border-slate-100">
                                        {{ str_replace('_', ' ', $step ?: $status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center"
                                style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
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
                                    <button type="button" onclick="showDocs({{ $sinistre->id }}, {{ $docsJson }})"
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
                                                style="width: {{ $totalDocs > 0 ? round(($fournisDocs / $totalDocs) * 100) : 0 }}%">
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
                            <td class="px-5 py-4 text-center">
                                @if (is_array($sinistre->ai_analysis_report) && isset($sinistre->ai_analysis_report['gravity']))
                                    @php $grav = strtolower($sinistre->ai_analysis_report['gravity']); @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                                        {{ $grav === 'high' ? 'bg-red-50 text-red-600 border border-red-100' : ($grav === 'medium' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100') }}">
                                        Gravité : {{ ucfirst($grav) }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Pas d'analyse</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs text-center">
                                {{ $sinistre->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('personnel.sinistres.review', $sinistre) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#1d3557]/10 text-[#1d3557] hover:bg-[#1d3557]/20 transition-all">
                                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                        Examiner
                                    </a>
                                    <form action="{{ route('personnel.sinistres.release', $sinistre) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-all border border-red-100"
                                            onclick="return confirm('Remettre ce dossier dans le pool général ?')">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                            Libérer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 block opacity-30"></i>
                                Vous n'avez récupéré aucun dossier pour le moment.<br>
                                <a href="{{ route('personnel.dashboard') }}"
                                    class="text-[#457b9d] font-semibold hover:underline mt-2 inline-block text-xs">
                                    ← Aller au tableau de bord pour récupérer un dossier
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($mesDossiers->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $mesDossiers->links() }}
                </div>
            @endif
        </div>
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
                    const icon = isUploaded ?
                        '<span style="color:#16a34a;font-size:13px;">&#x2714;</span>' :
                        '<span style="color:#dc2626;font-size:13px;">&#x2716;</span>';
                    const badge = isUploaded ?
                        '<span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">Fourni</span>' :
                        '<span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">Manquant</span>';
                    const requis = doc.requis ?
                        '<span style="color:#f97316;font-size:10px;font-weight:700;margin-left:4px;">● Requis</span>' :
                        '';

                    return `<tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:10px 12px;text-align:left;">
                ${icon}
                <span style="margin-left:8px;font-size:13px;color:#1e293b;font-weight:600;">${doc.nom}</span>
                ${requis}
            </td>
            <td style="padding:10px 12px;text-align:right;">${badge}</td>
        </tr>`;
                }).join('');

                const fournis = docs.filter(d => d.statut === 'uploaded').length;
                const total = docs.length;
                const pct = Math.round((fournis / total) * 100);
                const barColor = fournis === total ? '#16a34a' : '#1d3557';

                Swal.fire({
                    title: '<span style="font-size:16px;font-weight:800;color:#1d3557;">📋 Documents du dossier</span>',
                    html: `
            <div style="margin-bottom:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:#64748b;font-weight:600;">${fournis} / ${total} documents fournis</span>
                    <span style="font-size:12px;font-weight:700;color:${barColor};">${pct}%</span>
                </div>
                <div style="height:6px;background:#e2e8f0;border-radius:9999px;overflow:hidden;">
                    <div style="height:100%;width:${pct}%;background:${barColor};border-radius:9999px;transition:width 0.4s;"></div>
                </div>
            </div>
            <table style="width:100%;border-collapse:collapse;">
                <tbody>${rows}</tbody>
            </table>`,
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
