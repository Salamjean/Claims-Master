@extends('admin.layouts.template')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')

    {{-- PAGE HEADER --}}
    <div class="mb-6 animate-in" style="animation-delay:0s">
        <h1 class="text-xl font-bold text-slate-800">Bonjour, {{ auth('user')->user()->name ?? 'Admin' }} 👋</h1>
        <p class="text-slate-400 text-sm mt-0.5">Voici un résumé de l'activité de votre plateforme.</p>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="card-stat animate-in" style="animation-delay:0.05s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Utilisateurs</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">—</p>
                    <p class="text-xs text-emerald-500 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up"></i> Actifs ce mois
                    </p>
                </div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(36,58,143,0.1)">
                    <i class="fa-solid fa-users" style="color:#243a8f"></i>
                </div>
            </div>
        </div>

        <div class="card-stat animate-in" style="animation-delay:0.1s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Interventions</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">—</p>
                    <p class="text-xs text-blue-500 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-clock"></i> En attente
                    </p>
                </div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(124,182,4,0.1)">
                    <i class="fa-solid fa-clipboard-list" style="color:#7cb604"></i>
                </div>
            </div>
        </div>

        <div class="card-stat animate-in" style="animation-delay:0.15s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Équipements</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">—</p>
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Enregistrés
                    </p>
                </div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                    style="background:rgba(100,116,139,0.1)">
                    <i class="fa-solid fa-toolbox text-slate-500"></i>
                </div>
            </div>
        </div>

        <div class="card-stat animate-in" style="animation-delay:0.2s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Paiements</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">—</p>
                    <p class="text-xs text-emerald-500 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-check-double"></i> Validés
                    </p>
                </div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(16,185,129,0.1)">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-500"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- CHARTS + RECENT --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">

        {{-- Chart --}}
        <div class="xl:col-span-2 card-stat animate-in" style="animation-delay:0.25s">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-700">Activité des interventions</h2>
                    <p class="text-xs text-slate-400">6 derniers mois</p>
                </div>
                <span class="text-xs bg-primary/10 text-primary font-medium px-3 py-1 rounded-full">Mensuel</span>
            </div>
            <canvas id="activityChart" height="110"></canvas>
        </div>

        {{-- Statuts --}}
        <div class="card-stat animate-in" style="animation-delay:0.3s">
            <h2 class="text-sm font-semibold text-slate-700 mb-1">Répartition des statuts</h2>
            <p class="text-xs text-slate-400 mb-4">Toutes interventions confondues</p>

            {{-- Donut + label central --}}
            <div class="relative flex justify-center items-center" style="height:250px;">
                <canvas id="statusChart"></canvas>
                <div class="absolute flex flex-col items-center pointer-events-none">
                    <span class="text-2xl font-bold text-slate-700" id="donut-total">3</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Total</span>
                </div>
            </div>

            {{-- Légende stylisée --}}
            <div class="mt-5 space-y-2">
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                    <span class="flex items-center gap-2 text-xs text-slate-600">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background:#243a8f"></span>
                        Validées
                    </span>
                    <span class="text-xs font-semibold text-slate-800 bg-white px-2 py-0.5 rounded-lg shadow-sm">—</span>
                </div>
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                    <span class="flex items-center gap-2 text-xs text-slate-600">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background:#7cb604"></span>
                        Payées
                    </span>
                    <span class="text-xs font-semibold text-slate-800 bg-white px-2 py-0.5 rounded-lg shadow-sm">—</span>
                </div>
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                    <span class="flex items-center gap-2 text-xs text-slate-600">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background:#f59e0b"></span>
                        En attente
                    </span>
                    <span class="text-xs font-semibold text-slate-800 bg-white px-2 py-0.5 rounded-lg shadow-sm">—</span>
                </div>
            </div>
        </div>

    </div>

    {{-- RECENT INTERVENTIONS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 animate-in" style="animation-delay:0.35s">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-semibold text-slate-700">Dernières interventions</h2>
                <p class="text-xs text-slate-400">Activité récente</p>
            </div>
            <a href="#"
                class="text-xs font-medium px-4 py-1.5 rounded-xl text-primary border border-primary/30 hover:bg-primary/5 transition-all">
                Voir tout
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Référence</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Site</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Prestataire</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Statut</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    {{-- Quand les données seront disponibles, utiliser @forelse $interventions --}}
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-300 text-sm">
                            <i class="fa-solid fa-inbox text-3xl mb-3 block"></i>
                            Aucune intervention pour le moment
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Chart activité mensuelle
        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sep', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév'],
                datasets: [{
                    label: 'Interventions',
                    data: [0, 0, 0, 0, 0, 0],
                    borderColor: '#243a8f',
                    backgroundColor: 'rgba(36, 58, 143, 0.07)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#243a8f',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 }, beginAtZero: true }
                }
            }
        });

        // Donut statuts
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Validées', 'Payées', 'En attente'],
                datasets: [{
                    data: [1, 1, 1],
                    backgroundColor: ['#243a8f', '#7cb604', '#f59e0b'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8,
                    hoverBorderColor: '#fff',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                animation: { animateRotate: true, duration: 900, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label} : ${ctx.parsed}`
                        },
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                    }
                }
            }
        });
    </script>
@endpush