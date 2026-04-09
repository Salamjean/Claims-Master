@extends('admin.layouts.template')

@section('title', 'Service de constats')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Service de constats</h1>
                <p class="text-slate-500 text-sm mt-1">Gérez les commissariats de police et les brigades de gendarmerie partenaires.</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold flex items-center shadow-lg shadow-primary-600/20 transition-all">
                <i class="fa-solid fa-plus mr-2"></i> Ajouter un service
            </a>
        </div>

        {{-- Filtres & Recherche --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Rechercher un service..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all pl-10">
            </div>
            <div class="flex gap-2">
                <select class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:border-primary-600 transition-all">
                    <option value="">Tous les types</option>
                    <option value="police">Police</option>
                    <option value="gendarmerie">Gendarmerie</option>
                </select>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Localisation</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($services as $service)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                            {{ $service->role === 'police' ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600' }}">
                                            <i class="fa-solid {{ $service->role === 'police' ? 'fa-building-shield' : 'fa-building-columns' }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $service->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $service->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($service->role === 'police')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Police
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Gendarmerie
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700 font-medium">{{ $service->contact }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700">{{ $service->commune ?? 'Non renseigné' }}</p>
                                    <p class="text-xs text-slate-500 truncate max-w-[150px]">{{ $service->adresse }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Modifier">
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-300">
                                        <i class="fa-solid fa-building-shield text-2xl"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucun service de constat partenaire n'est enregistré.</p>
                                    <p class="text-slate-400 text-sm mt-1">Cliquez sur « Ajouter un service » pour commencer.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($services->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
