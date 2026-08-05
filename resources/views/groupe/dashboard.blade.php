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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8" id="stats-container">
        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-bell-ring"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Urgences en attente</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $urgencesEnAttente }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Non encore récupérées par un groupe</div>
        </div>

        <a href="{{ route('groupe.interventions') }}" class="card p-6 border border-slate-100 hover:shadow-md hover:border-amber-200 transition-all group cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 group-hover:bg-amber-100 flex items-center justify-center text-xl transition-colors">
                    <i class="fa-solid fa-truck-medical"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Mes interventions en cours</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $interventionsEnCours }}</p>
                </div>
            </div>
            <div class="text-sm text-amber-500 font-medium group-hover:underline flex items-center gap-1">
                <i class="fa-solid fa-arrow-right text-xs"></i> Voir mes interventions
            </div>
        </a>
    </div>

    <!-- Contenu Principal -->
    <div class="card p-6 border border-slate-100" id="table-declarations-en-attente">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Déclarations en attente de prise en charge</h2>

        @php
            $sinistresVueEnsemble = $sinistres->filter(function($s) {
                return is_null($s->assigned_groupe_id);
            });
        @endphp

        @include('groupe.partials.sinistres_table', [
            'collection' => $sinistresVueEnsemble,
            'emptyMessage' => 'Aucune déclaration en attente'
        ])
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Actualisation automatique du tableau et des cartes statistiques toutes les 10 secondes
    setInterval(function() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Actualiser le tableau des déclarations en attente
                const newTable = doc.querySelector('#table-declarations-en-attente');
                const currentTable = document.querySelector('#table-declarations-en-attente');
                if (newTable && currentTable) {
                    currentTable.replaceWith(document.adoptNode(newTable));
                }

                // Actualiser les compteurs / cartes statistiques
                const newStats = doc.querySelector('#stats-container');
                const currentStats = document.querySelector('#stats-container');
                if (newStats && currentStats) {
                    currentStats.replaceWith(document.adoptNode(newStats));
                }
            })
            .catch(error => console.error('Erreur actualisation automatique:', error));
    }, 10000);
</script>
@endpush

