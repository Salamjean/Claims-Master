@extends('groupe.layouts.app')

@section('title', 'Statistiques Groupe')
@section('page-title', 'Statistiques')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Statistiques de votre groupe</h1>
        <p class="text-slate-500 mt-1">Chiffres basés uniquement sur les déclarations récupérées par votre groupe.</p>
    </div>

    <!-- Cartes de résumé -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-hand-holding-medical"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Total récupérées</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalInterventions }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Déclarations prises en charge</div>
        </div>

        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Terminées</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $interventionsTerminees }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Prises en charge clôturées</div>
        </div>

        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">Récupérées (en attente dispatch)</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $interventionsEnAttente }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">En attente d'envoi d'équipe</div>
        </div>

        <div class="card p-6 border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h3 class="text-slate-500 text-sm font-medium">En intervention</h3>
                    <p class="text-2xl font-bold text-slate-800">{{ $interventionsEnCours }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">Équipes sur le terrain</div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card p-6 border border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Évolution des interventions (6 derniers mois)</h3>
            <div class="relative h-64 w-full">
                <canvas id="interventionsChart"></canvas>
            </div>
        </div>
        
        <div class="card p-6 border border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Répartition par statut</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique d'évolution
    const moisData = @json($sinistresMois->keys());
    const valeursData = @json($sinistresMois->values());

    const ctxEvol = document.getElementById('interventionsChart').getContext('2d');
    new Chart(ctxEvol, {
        type: 'bar',
        data: {
            labels: moisData,
            datasets: [{
                label: 'Nombre d\'interventions',
                data: valeursData,
                backgroundColor: 'rgba(190, 18, 60, 0.2)', // rose-700 avec opacité
                borderColor: '#be123c', // rose-700
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Données pour le graphique de répartition
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Terminées', 'En attente', 'En cours'],
            datasets: [{
                data: [
                    {{ $interventionsTerminees }}, 
                    {{ $interventionsEnAttente }}, 
                    {{ $interventionsEnCours }}
                ],
                backgroundColor: [
                    '#10b981', // emerald-500
                    '#e11d48', // rose-600
                    '#f59e0b', // amber-500
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
