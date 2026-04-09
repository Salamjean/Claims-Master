@extends('agent.layouts.template')
@section('title', 'Constats Rédigés')
@section('page-title', 'Constats Rédigés')

@section('content')
<div class="mx-auto space-y-6" style="width:100%;">

    {{-- En-tête --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-violet-600 flex items-center justify-center shadow-lg shadow-violet-200">
                <i class="fa-solid fa-file-lines text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Constats Rédigés</h1>
                <p class="text-sm text-slate-500 mt-1">Documents officiels validés et transmis aux assurés.</p>
            </div>
        </div>
        <span class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm">
            {{ $constats->count() }} constat(s)
        </span>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-left">Sinistre</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-left">Assuré</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Type</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Rédaction</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Validé le</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Montant</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Statut Paiement</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($constats as $constat)
                    <tr class="hover:bg-slate-50/70 transition-colors">

                        {{-- Numéro sinistre --}}
                        <td class="px-5 py-4">
                            <span class="text-xs font-bold text-violet-600 bg-violet-50 px-2 py-1 rounded-lg">
                                {{ $constat->sinistre->numero_sinistre ?? 'SI-'.$constat->sinistre_id }}
                            </span>
                            <p class="text-[10px] text-slate-400 mt-1 font-medium">
                                {{ str_replace('_',' ', $constat->sinistre->type_sinistre ?? '—') }}
                            </p>
                        </td>

                        {{-- Assuré --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-200">
                                    {{ strtoupper(substr($constat->sinistre->assure->name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">{{ $constat->sinistre->assure->name ?? '—' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $constat->sinistre->assure->contact ?? '—' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Type de constat --}}
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-extrabold rounded-full uppercase tracking-wider
                                {{ $constat->type_constat === 'accident' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                <i class="fa-solid fa-circle text-[5px]"></i>
                                {{ $constat->type_constat === 'accident' ? 'Accident' : 'Général' }}
                            </span>
                        </td>

                        {{-- Type de rédaction (texte ou PDF) --}}
                        <td class="px-5 py-4 text-center">
                            @if($constat->redaction_pdf)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-full">
                                    <i class="fa-solid fa-file-pdf text-[10px]"></i> PDF joint
                                </span>
                            @elseif($constat->redaction_contenu)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-extrabold rounded-full">
                                    <i class="fa-solid fa-pen text-[10px]"></i> Texte rédigé
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Date de validation --}}
                        <td class="px-5 py-4 text-center text-xs text-slate-500 font-medium">
                            {{ $constat->redaction_validee_at?->format('d/m/Y') }}<br>
                            <span class="text-slate-400 text-[10px]">{{ $constat->redaction_validee_at?->format('H:i') }}</span>
                        </td>

                        {{-- Montant fixé --}}
                        <td class="px-5 py-4 text-center font-black text-violet-700 text-sm">
                            {{ number_format($constat->montant_a_payer ?? 0, 0, ',', ' ') }} <span class="text-[10px]">FCFA</span>
                        </td>

                        {{-- Statut Paiement --}}
                        <td class="px-5 py-4 text-center">
                            @if($constat->statut_paiement === 'success')
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-full">
                                        <i class="fa-solid fa-check-double text-[10px]"></i> Payé
                                    </span>
                                </div>
                            @elseif($constat->statut_paiement === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-extrabold rounded-full">
                                    <i class="fa-solid fa-clock text-[10px]"></i> En attente
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-extrabold rounded-full">
                                    <i class="fa-solid fa-hourglass-start text-[10px]"></i> Non payé
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('agent.sinistres.show', $constat->sinistre_id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i> Dossier
                                </a>

                                @if($constat->redaction_pdf)
                                <a href="{{ Storage::url($constat->redaction_pdf) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                                    <i class="fa-solid fa-file-pdf text-xs"></i> Voir PDF
                                </a>
                                @elseif($constat->redaction_contenu)
                                <button type="button"
                                    onclick="showRedactionModal('{{ addslashes(nl2br(htmlspecialchars($constat->redaction_contenu))) }}', '{{ $constat->sinistre->numero_sinistre ?? $constat->sinistre_id }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                                    <i class="fa-solid fa-eye text-xs"></i> Lire
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-20">
                                <i class="fa-solid fa-file-lines text-6xl mb-4"></i>
                                <p class="text-xl font-bold">Aucun constat rédigé</p>
                                <p class="text-sm">Les constats validés apparaîtront ici.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal lecture texte --}}
<div id="redaction-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" onclick="closeModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-file-lines text-violet-500"></i>
                Constat officiel — <span id="modal-numero" class="text-violet-600"></span>
            </h3>
            <button onclick="document.getElementById('redaction-modal').classList.add('hidden')"
                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-slate-600 text-sm"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <div id="modal-content" class="text-sm text-slate-700 font-mono leading-relaxed whitespace-pre-wrap"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showRedactionModal(content, numero) {
        document.getElementById('modal-numero').textContent = '#' + numero;
        document.getElementById('modal-content').innerHTML = content;
        document.getElementById('redaction-modal').classList.remove('hidden');
    }
    function closeModal(e) {
        document.getElementById('redaction-modal').classList.add('hidden');
    }
</script>
@endpush
