@extends('personnel.layouts.template')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
    @php
        $statusClasses = [
            'soumis' => 'bg-amber-50 text-amber-600 border-amber-200',
            'en_cours' => 'bg-blue-50 text-blue-600 border-blue-200',
            'documents_soumis' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
            'cloture' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
            'rejete' => 'bg-red-50 text-red-600 border-red-200',
        ];
    @endphp
    <div class="space-y-6 animate-in">

        {{-- En-tête --}}
        <div class="relative rounded-2xl overflow-hidden text-white px-6 py-6 shadow-xl"
            style="background: linear-gradient(135deg, #1d3557 0%, #152840 100%);">
            <div
                style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.06),transparent);pointer-events:none;">
            </div>
            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-[#a8dadc] animate-pulse"></span>
                        <span class="text-[10px] font-black text-[#a8dadc] uppercase tracking-[0.2em]">Personnel en
                            service</span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-black">Bonjour, <span
                            class="text-[#a8dadc]">{{ $personnel->name }}</span> 👋</h1>
                    <p class="text-white/40 text-xs font-bold uppercase tracking-wider mt-1">{{ $personnel->code_user }}</p>
                </div>
                <div
                    class="flex items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl px-5 py-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-[#a8dadc]">
                        <i class="fa-solid fa-inbox text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-white/30 font-black uppercase tracking-widest mb-0.5">Non Récupérés</p>
                        <p class="text-2xl font-black leading-none">{{ $totalNonRecuperes }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Compteurs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="card-stat flex items-center gap-4 group hover:border-slate-200 border border-transparent">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ $totalNonRecuperes }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Non Récupérés</p>
                </div>
            </div>

            <div class="card-stat flex items-center gap-4 group hover:border-amber-100 border border-transparent">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ $sinistresEnAttente }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">En Attente</p>
                </div>
            </div>

            <div class="card-stat flex items-center gap-4 group hover:border-indigo-100 border border-transparent">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ $sinistresEnCours }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">En Cours</p>
                </div>
            </div>

            <div class="card-stat flex items-center gap-4 group hover:border-emerald-100 border border-transparent">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform shrink-0">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ $sinistresCloturer }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Clôturés</p>
                </div>
            </div>
        </div>

        {{-- Pool général (sinistres non récupérés) --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fa-solid fa-inbox text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800">Pool Général</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Dossiers disponibles à
                            récupérer</p>
                    </div>
                </div>
                <span class="text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 px-2.5 py-1 rounded-full">
                    {{ $pool->count() }} dossier(s)
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60">
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                N° Sinistre</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Assuré</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Type</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Statut</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Date</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($pool as $sinistre)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('personnel.sinistres.show', $sinistre) }}"
                                        class="font-mono text-xs font-bold text-[#1d3557] hover:underline">
                                        {{ $sinistre->numero_sinistre }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <p class="font-semibold text-slate-700 text-xs">{{ $sinistre->assure->name }}
                                        {{ $sinistre->assure->prenom }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $sinistre->assure->code_user }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 capitalize text-center">
                                    {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @php
                                        $cls =
                                            $statusClasses[$sinistre->status] ??
                                            'bg-slate-50 text-slate-500 border-slate-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $cls }} capitalize">
                                        {{ str_replace('_', ' ', $sinistre->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-400 text-center">
                                    {{ $sinistre->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <form action="{{ route('personnel.sinistres.claim', $sinistre) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1d3557] hover:bg-[#152840] text-white text-xs font-bold rounded-lg transition-colors">
                                            <i class="fa-solid fa-hand-holding-hand text-[11px]"></i>
                                            Récupérer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-sm">
                                    <i class="fa-solid fa-circle-check text-3xl text-emerald-200 mb-3 block"></i>
                                    Tous les dossiers ont été récupérés.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
