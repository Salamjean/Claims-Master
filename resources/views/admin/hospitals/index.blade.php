@extends('admin.layouts.template')

@section('title', 'Sapeurs-pompiers')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Sapeurs-pompiers</h1>
                <p class="text-slate-500 text-sm mt-1">Gérez les casernes de sapeurs-pompiers pour la prise en charge et le secours des victimes.</p>
            </div>
            <a href="{{ route('admin.hospitals.create') }}" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold flex items-center shadow-lg shadow-rose-600/20 transition-all">
                <i class="fa-solid fa-plus mr-2"></i> Ajouter une caserne
            </a>
        </div>

        {{-- Filtres & Recherche --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Rechercher une caserne..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 transition-all pl-10">
            </div>
        </div>

        {{-- Messages d'alerte --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Caserne</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Localisation</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($hospitals as $hospital)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-rose-100 text-rose-600">
                                            <i class="fa-solid fa-fire-extinguisher"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $hospital->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $hospital->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700 font-medium">{{ $hospital->contact }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700">{{ $hospital->commune ?? 'Non renseigné' }}</p>
                                    <p class="text-xs text-slate-500 truncate max-w-[150px]">{{ $hospital->adresse }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Supprimer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-300">
                                        <i class="fa-solid fa-fire-extinguisher text-2xl"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucune caserne de sapeurs-pompiers n'est enregistrée.</p>
                                    <p class="text-slate-400 text-sm mt-1">Cliquez sur « Ajouter une caserne » pour commencer.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($hospitals->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $hospitals->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
