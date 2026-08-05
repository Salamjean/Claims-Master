@extends('groupe.layouts.app')

@section('title', 'Interventions en cours')
@section('page-title', 'Interventions en cours')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Interventions en cours</h1>
        <p class="text-slate-500 mt-1">Suivez les interventions que votre groupe a récupérées et gère activement.</p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="interventions-stats-container">
        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-truck-medical"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">En route</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $enRoute }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Équipes actuellement en route</div>
        </div>

        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Sur les lieux</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $arrive }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Équipes arrivées sur place</div>
        </div>

        <a href="{{ route('groupe.historique') }}" class="card p-6 border border-slate-100 hover:shadow-md hover:border-rose-200 transition-all group cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 group-hover:bg-rose-100 flex items-center justify-center text-xl transition-colors">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Historique</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalHistorique }}</p>
                </div>
            </div>
            <div class="text-sm text-rose-500 font-medium group-hover:underline flex items-center gap-1">
                <i class="fa-solid fa-arrow-right text-xs"></i> Voir l'historique
            </div>
        </a>
    </div>

    <!-- Tableau -->
    <div class="card p-6 border border-slate-100" id="interventions-container">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Mes interventions actives</h2>

        @include('groupe.partials.sinistres_table', [
            'collection' => $sinistres,
            'emptyMessage' => 'Aucune intervention en cours pour votre groupe'
        ])
    </div>
</div>
@endsection

@push('scripts')
<script>
    setInterval(function() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newContent = doc.querySelector('#interventions-container');
                const currentContent = document.querySelector('#interventions-container');
                if (newContent && currentContent) {
                    currentContent.replaceWith(document.adoptNode(newContent));
                }

                const newStats = doc.querySelector('#interventions-stats-container');
                const currentStats = document.querySelector('#interventions-stats-container');
                if (newStats && currentStats) {
                    currentStats.replaceWith(document.adoptNode(newStats));
                }
            })
            .catch(error => console.error('Erreur rafraîchissement automatique:', error));
    }, 10000);
</script>
@endpush
