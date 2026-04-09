@extends('assure.layouts.template')

@section('title', 'Support 24/7')
@section('page-title', 'Support & Assistance')

@section('content')
    <div class="space-y-8 pb-12">
        {{-- ── HERO SECTION ── --}}
        <div class="relative rounded-[2rem] overflow-hidden bg-slate-900 px-8 py-12 text-center shadow-2xl shadow-blue-900/20 animate-in" style="--delay: 0.1s">
            <div class="absolute inset-0 opacity-40">
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600 blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600 blur-[120px] translate-x-1/2 translate-y-1/2"></div>
            </div>
            
            <div class="relative z-10 max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Support Disponible 24h/24 & 7j/7
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Comment pouvons-nous <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">vous aider ?</span></h1>
                <p class="text-slate-400 text-lg leading-relaxed">Nos experts sont mobilisés jour et nuit pour vous accompagner dans vos démarches et assurer votre tranquillité.</p>
            </div>
        </div>

        {{-- ── QUICK CONTACT CARDS ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-in" style="--delay: 0.2s">
            {{-- WhatsApp --}}
            <a href="https://wa.me/2250000000000" target="_blank" class="group relative p-8 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-brands fa-whatsapp text-8xl"></i>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-500">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">WhatsApp Direct</h3>
                <p class="text-slate-500 text-sm mb-6">Réponse instantanée pour vos urgences mineures.</p>
                <span class="inline-flex items-center gap-2 text-emerald-600 font-bold text-sm">
                    Discuter maintenant <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>

            {{-- Phone --}}
            <a href="tel:22501020304" class="group relative p-8 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-phone-volume text-8xl"></i>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-500">
                    <i class="fa-solid fa-phone-volume text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Centre d'Appel</h3>
                <p class="text-slate-500 text-sm mb-6">Assistance vocale prioritaire en cas de sinistre grave.</p>
                <span class="inline-flex items-center gap-2 text-blue-600 font-bold text-sm">
                    Appeler le 1313 <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>

            {{-- Email --}}
            <a href="mailto:support@claimsmaster.ci" class="group relative p-8 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-envelope-open-text text-8xl"></i>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-500">
                    <i class="fa-solid fa-envelope-open-text text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Support Email</h3>
                <p class="text-slate-500 text-sm mb-6">Pour vos demandes administratives et suivis complexes.</p>
                <span class="inline-flex items-center gap-2 text-indigo-600 font-bold text-sm">
                    Envoyer un mail <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- ── FAQ SECTION ── --}}
            <div class="animate-in" style="--delay: 0.3s">
                <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm transition-all hover:shadow-lg">
                    <h2 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-question text-lg"></i>
                        </span>
                        Foire Aux Questions
                    </h2>
                    
                    <div class="space-y-4" x-data="{ active: null }">
                        @php
                            $faqs = [
                                [
                                    'q' => 'Comment déclarer un sinistre rapidement ?',
                                    'a' => 'Cliquez sur le bouton "Déclarer un sinistre" depuis votre tableau de bord. Prenez des photos claires, décrivez l\'événement et validez. Un agent sera automatiquement dispatché.'
                                ],
                                [
                                    'q' => 'Combien de temps prend le traitement ?',
                                    'a' => 'Le constat par l\'agent est généralement effectué en moins de 30 minutes. Le traitement administratif par l\'assureur prend ensuite entre 24h et 48h selon la complexité.'
                                ],
                                [
                                    'q' => 'Quels documents dois-je préparer ?',
                                    'a' => 'Généralement, votre CNI, le permis de conduire, et la carte grise du véhicule. L\'IA vérifiera ces pièces lors de votre déclaration.'
                                ],
                                [
                                    'q' => 'Puis-je modifier une déclaration ?',
                                    'a' => 'Une fois validée, une déclaration ne peut être modifiée directement pour garantir l\'intégrité des preuves. Contactez le support via WhatsApp pour toute rectification urgente.'
                                ]
                            ];
                        @endphp

                        @foreach($faqs as $i => $faq)
                            <div class="border border-slate-50 rounded-2xl overflow-hidden transition-all duration-300" :class="active === {{ $i }} ? 'bg-slate-50/50 border-blue-100 shadow-inner' : ''">
                                <button @click="active = active === {{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between p-5 text-left transition-all">
                                    <span class="font-bold text-slate-700" :class="active === {{ $i }} ? 'text-blue-600' : ''">{{ $faq['q'] }}</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-300 transition-transform duration-300" :class="active === {{ $i }} ? 'rotate-180 text-blue-500' : ''"></i>
                                </button>
                                <div x-show="active === {{ $i }}" x-collapse x-cloak>
                                    <div class="px-5 pb-5 text-sm text-slate-500 leading-relaxed border-t border-slate-100/50 pt-3">
                                        {{ $faq['a'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── MESSAGE FORM ── --}}
            <div class="animate-in" style="--delay: 0.4s">
                <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm h-full flex flex-col transition-all hover:shadow-lg">
                    <h2 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-paper-plane text-lg"></i>
                        </span>
                        Envoyer un message
                    </h2>
                    
                    <form action="#" class="space-y-5 flex-1 flex flex-col">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-1">Sujet du message</label>
                            <select class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 transition-all outline-none font-bold text-slate-600 appearance-none">
                                <option>Aide sur une déclaration</option>
                                <option>Question sur mon contrat</option>
                                <option>Problème technique (App)</option>
                                <option>Autre demande</option>
                            </select>
                        </div>

                        <div class="space-y-1.5 flex-1 flex flex-col">
                            <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-1">Message détaillé</label>
                            <textarea placeholder="Comment pouvons-nous vous aider aujourd'hui ?" class="w-full flex-1 px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 transition-all outline-none font-medium text-slate-600 resize-none min-h-[150px]"></textarea>
                        </div>

                        <button type="button" onclick="Swal.fire({
                            icon: 'success',
                            title: 'Message envoyé !',
                            text: 'Notre équipe reviendra vers vous dans les plus brefs délais.',
                            confirmButtonText: 'Parfait',
                            confirmButtonColor: '#3b82f6',
                            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl' }
                        })" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-500/30 flex items-center justify-center gap-3 active:scale-[0.98]">
                            <i class="fa-solid fa-paper-plane text-sm"></i> Envoyer le message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            animation-delay: var(--delay);
        }
    </style>
@endsection
