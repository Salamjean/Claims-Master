@extends('personnel.layouts.template')

@section('title', 'Dossiers Sinistres')
@section('page-title', 'Dossiers Sinistres')

@section('content')
    <div class="space-y-5 animate-in">

        {{-- En-tête --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Dossiers Sinistres</h1>
                <p class="text-sm text-slate-400">Consultez et suivez tous les dossiers de votre assurance</p>
            </div>
        </div>

        {{-- Filtres --}}
        <form method="GET" action="{{ route('personnel.sinistres.index') }}"
            class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-wrap gap-3 items-end">

            <div class="flex-1 min-w-[180px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Recherche</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center text-slate-300 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="N° sinistre, assuré..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition">
                </div>
            </div>

            <div class="min-w-[160px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Statut</label>
                <select name="status"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 transition">
                    <option value="">Tous les statuts</option>
                    <option value="soumis" {{ request('status') === 'soumis' ? 'selected' : '' }}>En attente</option>
                    <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="documents_soumis" {{ request('status') === 'documents_soumis' ? 'selected' : '' }}>
                        Documents soumis</option>
                    <option value="expertise_en_cours" {{ request('status') === 'expertise_en_cours' ? 'selected' : '' }}>
                        Expertise en cours</option>
                    <option value="cloture" {{ request('status') === 'cloture' ? 'selected' : '' }}>Clôturé</option>
                    <option value="rejete" {{ request('status') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-[#1d3557] text-white text-sm font-semibold hover:bg-[#152840] transition active:scale-95">
                    <i class="fa-solid fa-filter mr-1.5 text-xs"></i>Filtrer
                </button>
                @if (request('search') || request('status'))
                    <a href="{{ route('personnel.sinistres.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">N°
                            Sinistre</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Assuré
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date
                        </th>
                        <th class="text-right px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sinistres as $sinistre)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('personnel.sinistres.show', $sinistre) }}"
                                    class="font-mono text-xs font-bold text-[#1d3557] hover:underline">
                                    {{ $sinistre->numero_sinistre }}
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-[#1d3557]/10 flex items-center justify-center text-[#1d3557] text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-xs">{{ $sinistre->assure->name }}
                                            {{ $sinistre->assure->prenom }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $sinistre->assure->code_user }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500 capitalize">
                                {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $statusClasses = [
                                        'soumis' => 'bg-amber-50 text-amber-600 border-amber-200',
                                        'en_cours' => 'bg-blue-50 text-blue-600 border-blue-200',
                                        'documents_soumis' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                        'expertise_en_cours' => 'bg-purple-50 text-purple-600 border-purple-200',
                                        'cloture' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                        'rejete' => 'bg-red-50 text-red-600 border-red-200',
                                    ];
                                    $cls =
                                        $statusClasses[$sinistre->status] ??
                                        'bg-slate-50 text-slate-500 border-slate-200';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $cls }} capitalize">
                                    {{ str_replace('_', ' ', $sinistre->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-400">
                                {{ $sinistre->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('personnel.sinistres.show', $sinistre) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <i class="fa-solid fa-folder-open text-4xl text-slate-200"></i>
                                    <p class="text-sm font-medium">Aucun sinistre trouvé</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($sinistres->hasPages())
            <div>{{ $sinistres->links() }}</div>
        @endif

    </div>
@endsection
