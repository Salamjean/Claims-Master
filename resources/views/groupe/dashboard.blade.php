@extends('groupe.layouts.app')

@section('title', 'Tableau de Bord Groupe')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Bienvenue, {{ auth('user')->user()->name }}</h1>
        <p class="text-slate-500 mt-1">Gérez vos accès et suivez les interventions de votre groupe.</p>
    </div>

    <!-- Statistiques / Cartes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-truck-medical"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Urgences en attente</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $urgencesEnAttente }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">En attente de prise en charge</div>
        </div>

        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">En cours</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $interventionsEnCours }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Interventions actuelles</div>
        </div>

        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Total Alertes</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalAlerts }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Alertes reçues</div>
        </div>
    </div>

    <!-- Contenu Principal -->
    <div class="card p-6 border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Dernières déclarations (Interventions)</h2>
        
        @if($sinistres->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                            <th class="py-3 px-4 font-medium rounded-tl-lg">Date</th>
                            <th class="py-3 px-4 font-medium">Référence</th>
                            <th class="py-3 px-4 font-medium">Lieu</th>
                            <th class="py-3 px-4 font-medium">Statut</th>
                            <th class="py-3 px-4 font-medium text-right rounded-tr-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-50">
                        @foreach($sinistres as $sinistre)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">{{ $sinistre->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $sinistre->reference }}</td>
                            <td class="py-3 px-4">{{ $sinistre->lieu ?? 'Non précisé' }}</td>
                            <td class="py-3 px-4">
                                @if($sinistre->hospital_status === 'en_attente')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">En attente</span>
                                @elseif($sinistre->hospital_status === 'ambulance_en_route')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">En route</span>
                                @elseif($sinistre->hospital_status === 'arrive')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Arrivé</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ ucfirst($sinistre->hospital_status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <!-- Actions adaptées au groupe -->
                                @if($sinistre->hospital_status === 'en_attente')
                                    <form action="{{ route('groupe.sinistres.dispatch', $sinistre) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-rose-600 text-white text-xs font-medium rounded hover:bg-rose-700 transition-colors">
                                            Dépêcher équipe
                                        </button>
                                    </form>
                                @elseif($sinistre->hospital_status === 'ambulance_en_route')
                                    <form action="{{ route('groupe.sinistres.arrive', $sinistre) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                            Signaler Arrivée
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i class="fa-solid fa-inbox text-2xl"></i>
                </div>
                <h3 class="text-slate-700 font-medium mb-1">Aucune intervention</h3>
                <p class="text-slate-400 text-sm">Vous n'avez pas encore d'intervention assignée à votre groupe.</p>
            </div>
        @endif
    </div>
</div>
@endsection
