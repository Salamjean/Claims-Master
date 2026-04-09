@extends('assure.layouts.template')
@section('title', 'Mes Constats Prêts')
@section('page-title', 'Mes Constats Prêts')

@section('content')
<div class="mx-auto space-y-6" style="width:100%;">

    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-violet-600 flex items-center justify-center shadow-lg shadow-violet-200">
            <i class="fa-solid fa-file-invoice text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Mes Constats Prêts</h1>
            <p class="text-sm text-slate-500 mt-1">Liste des rapports officiels disponibles pour retrait ou livraison.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-700 font-bold shadow-sm animate-fade-in-down">
        <i class="fa-solid fa-circle-check text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sinistres as $sinistre)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden group">
            <div class="p-6">
                {{-- Badge Statut --}}
                <div class="flex justify-between items-start mb-4">
                    <span class="px-3 py-1 bg-violet-100 text-violet-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-violet-200">
                        Prêt
                    </span>
                    @if($sinistre->constat->statut_paiement === 'success')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-200">
                            Payé
                        </span>
                    @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-amber-200">
                            {{ number_format($sinistre->constat->montant_a_payer, 0, ',', ' ') }} FCFA
                        </span>
                    @endif
                </div>

                <h3 class="text-lg font-black text-slate-800 mb-1">Sinistre #{{ $sinistre->numero_sinistre }}</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-4">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</p>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-slate-600">
                        <i class="fa-solid fa-building-columns text-violet-400 w-4"></i>
                        <span class="text-sm font-bold">{{ $sinistre->service->name ?? 'Service de Police' }}</span>
                    </div>
                </div>

                @if($sinistre->constat->statut_paiement === 'success')
                    <div class="flex flex-col gap-3">
                        @if($sinistre->constat->redaction_pdf)
                            <a href="{{ Storage::url($sinistre->constat->redaction_pdf) }}" target="_blank"
                                class="flex items-center justify-center gap-3 w-full py-4 bg-red-600 hover:bg-red-700 text-white font-black text-sm rounded-2xl shadow-lg shadow-red-200 transition-all active:scale-95">
                                <i class="fa-solid fa-file-pdf"></i> Télécharger le Constat
                            </a>
                        @elseif($sinistre->constat->redaction_contenu)
                            <button type="button" 
                                onclick="alert('Contenu : {{ addslashes($sinistre->constat->redaction_contenu) }}')"
                                class="flex items-center justify-center gap-3 w-full py-4 bg-violet-600 hover:bg-violet-700 text-white font-black text-sm rounded-2xl shadow-lg shadow-violet-200 transition-all active:scale-95">
                                <i class="fa-solid fa-eye"></i> Voir le Constat
                            </button>
                        @endif
                        
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center gap-2 justify-center">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Document débloqué</span>
                        </div>
                    </div>
                @else
                    <a href="{{ route('assure.constats.paiement', $sinistre->id) }}"
                        class="block w-full py-4 bg-violet-600 hover:bg-violet-700 text-white text-center font-black text-sm rounded-2xl shadow-lg shadow-violet-200 transition-all active:scale-95">
                        <i class="fa-solid fa-credit-card mr-2"></i> Régler les frais ({{ number_format($sinistre->constat->montant_a_payer, 0, ',', ' ') }} FCFA)
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center opacity-40">
            <i class="fa-solid fa-file-circle-exclamation text-6xl mb-4"></i>
            <p class="text-xl font-bold">Aucun constat prêt pour le moment</p>
            <p class="text-sm font-medium">Vous recevrez une notification quand votre constat sera rédigé.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
