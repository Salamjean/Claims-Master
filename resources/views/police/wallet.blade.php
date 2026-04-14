@extends('police.layouts.template')

@section('title', 'Portefeuille Unité')

@section('content')
<div class="space-y-8 mx-auto" style="width: 100%;">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Portefeuille Unité</h1>
            <p class="text-sm text-slate-500 font-medium">Suivi centralisé des paiements générés par vos agents.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-[11px] font-black text-slate-600 uppercase">Commissariat Actif</span>
            </div>
        </div>
    </div>

    {{-- Wallet Card --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 md:p-12 text-white shadow-2xl shadow-blue-900/20"
         style="background: linear-gradient(135deg, #1d3557 0%, #152840 100%);">
        {{-- Decorative Elements --}}
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-10">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 backdrop-blur-md rounded-full border border-white/10">
                    <i class="fa-solid fa-building-columns text-blue-300 text-[10px]"></i>
                    <span class="text-[10px] font-black text-white/70 uppercase tracking-widest">Trésorerie Unité</span>
                </div>
                
                <div class="flex items-baseline gap-4">
                    <span class="text-5xl md:text-7xl font-black tracking-tighter">{{ number_format($user->wallet_balance, 0, ',', ' ') }}</span>
                    <span class="text-2xl md:text-3xl font-black text-blue-300">FCFA</span>
                </div>

                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-400/20 flex items-center justify-center text-blue-300 border border-blue-400/20">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-white/30 uppercase tracking-wider">Cumul National</p>
                            <p class="text-sm font-bold">{{ number_format($transactions->where('type', 'credit')->sum('amount'), 0, ',', ' ') }} F</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shrink-0">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 w-full md:w-80 space-y-6">
                    <p class="text-[11px] font-black text-white/40 uppercase tracking-[0.2em] text-center">Gestion des Retraits</p>
                    <div class="grid grid-cols-1 gap-3">
                        <button class="w-full py-4 bg-emerald-500 text-white rounded-2xl font-black text-[11px] uppercase tracking-wider hover:bg-emerald-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                            <i class="fa-solid fa-download"></i>
                            Décaisser les fonds
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-sm">
                    <i class="fa-solid fa-receipt text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-tight">Flux financiers des agents</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Détails des gains collectés</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Opération</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Agent impliqué</th>
                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Montant</th>
                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Horodatage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-blue-50/20 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-100">
                                        <i class="fa-solid fa-plus"></i>
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-black text-slate-700 leading-none mb-1">
                                            {{ $transaction->sinistre ? 'Règlement Sinistre #' . $transaction->sinistre->numero_sinistre : $transaction->description }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                            {{ $transaction->sinistre->assure->name ?? '' }} {{ $transaction->sinistre->assure->prenom ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @if($transaction->sinistre && $transaction->sinistre->assignedAgent)
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-200">
                                            {{ strtoupper(substr($transaction->sinistre->assignedAgent->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-700 leading-none mb-0.5">{{ $transaction->sinistre->assignedAgent->name }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $transaction->sinistre->assignedAgent->prenom }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 font-bold italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-sm font-black text-emerald-600">
                                    + {{ number_format($transaction->amount, 0, ',', ' ') }} F
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded-full border border-blue-100">
                                    <i class="fa-solid fa-check-double text-[8px]"></i>
                                    ENCAISSÉ
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <p class="text-[11px] font-black text-slate-500 leading-none mb-1">{{ $transaction->created_at->format('d/m/Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-bold">{{ $transaction->created_at->format('H:i') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                    <i class="fa-solid fa-building-columns text-2xl text-slate-200"></i>
                                </div>
                                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucune transaction centralisée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>

<style>
    .pagination { display: flex; gap: 0.5rem; justify-content: center; }
    .page-item .page-link {
        width: 36px; h: 36px; display: flex; align-items: center; justify-content: center;
        border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid #e2e8f0;
        color: #1e293b; background: white; transition: all 0.2s;
    }
    .page-item.active .page-link { background: #1e3a8a; color: white; border-color: #1e3a8a; }
</style>
@endsection
