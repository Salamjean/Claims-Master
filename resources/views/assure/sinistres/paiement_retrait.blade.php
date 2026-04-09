@extends('assure.layouts.template')
@section('title', 'Réglage des Frais')

@section('content')
<div class="mx-auto max-w-4xl space-y-8 animate-fade-in" style="width:100%;">

    {{-- En-tête --}}
    <div class="flex items-center gap-6">
        <a href="{{ route('assure.constats.prets') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-all shadow-sm group active:scale-95">
            <i class="fa-solid fa-arrow-left text-slate-400 group-hover:text-violet-600 transition-colors"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Réglage des Frais</h1>
            <p class="text-sm text-slate-500 font-bold uppercase tracking-widest mt-1">Sinistre #{{ $sinistre->numero_sinistre }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        
        {{-- Formulaire de Paiement --}}
        <div class="lg:col-span-3 space-y-8">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-violet-500/5 blur-[80px]"></div>
                
                <form action="{{ route('assure.constats.paiement.store', $sinistre->id) }}" method="POST" id="payment-form">
                    @csrf

                    <div class="space-y-10">
                        {{-- Section Montant --}}
                        <div class="p-8 bg-violet-600 rounded-3xl text-white shadow-2xl shadow-violet-200 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60">Montant du Document</p>
                                <h2 class="text-4xl font-black mt-1">{{ number_format($sinistre->constat->montant_a_payer, 0, ',', ' ') }} <span class="text-xl opacity-60">FCFA</span></h2>
                            </div>
                            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md">
                                <i class="fa-solid fa-receipt text-3xl"></i>
                            </div>
                        </div>

                        {{-- Section Opérateurs --}}
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-wallet text-violet-500 text-lg"></i>
                                    <h3 class="text-slate-800 font-black text-sm uppercase tracking-wider">Moyen de Paiement</h3>
                                </div>
                                <span class="px-3 py-1 bg-sky-100 text-sky-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-sky-200">
                                    <i class="fa-solid fa-star mr-1"></i> Partenaire Officiel
                                </span>
                            </div>

                            <div class="grid grid-cols-1">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="payment_method" value="wave" class="hidden peer" checked required>
                                    <div class="h-28 flex flex-col items-center justify-center border-2 border-violet-200 rounded-3xl bg-violet-50/30 peer-checked:border-violet-500 peer-checked:bg-white peer-checked:shadow-2xl peer-checked:shadow-violet-100 transition-all duration-300 relative overflow-hidden group">
                                        <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fa-solid fa-circle-check text-violet-500 text-xl"></i>
                                        </div>
                                        <img src="{{ asset('assets/images/wave.png') }}" class="h-16 w-16 object-contain mb-2 transform group-hover:scale-110 transition-transform" alt="Wave">
                                        <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Payer avec Wave</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Bouton --}}
                        <button type="submit" class="w-full py-5 bg-violet-600 hover:bg-violet-700 text-white font-black text-base uppercase tracking-widest rounded-3xl shadow-2xl shadow-violet-200 transition-all active:scale-[0.98] flex items-center justify-center gap-4 group">
                            Confirmer le Paiement
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Infos & Sécurité --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-900 rounded-[40px] p-8 text-white shadow-2xl">
                <i class="fa-solid fa-shield-halved text-emerald-400 text-3xl mb-6"></i>
                <h3 class="text-xl font-black mb-4">Paiement Sécurisé</h3>
                <p class="text-sm text-slate-400 leading-relaxed font-medium">
                    Une fois le paiement effectué, votre constat sera immédiatement débloqué. Vous pourrez le consulter en ligne ou le télécharger au format PDF.
                </p>
                
                <div class="mt-8 space-y-4">
                    <div class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl border border-white/5">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span class="text-xs font-bold">Accès immédiat</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl border border-white/5">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span class="text-xs font-bold">Facture disponible</span>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-sky-50 rounded-[40px] border border-sky-100">
                <p class="text-xs font-black text-sky-800 uppercase tracking-widest mb-2 italic">Besoin d'aide ?</p>
                <p class="text-xs text-sky-600 font-bold leading-relaxed">
                    Si vous rencontrez un problème lors du paiement, contactez notre support 24/7.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>
@endsection
