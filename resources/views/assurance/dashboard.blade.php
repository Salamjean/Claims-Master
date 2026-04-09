@extends('assurance.layouts.template')

@section('page-title', 'Tableau de bord')

@section('content')
    <div class="space-y-8 animate-in">
        {{-- Header / Welcome --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bienvenue, {{ Auth::user()->name }}</h1>
                <p class="text-slate-500 text-sm">Voici un aperçu de l'activité de votre plateforme aujourd'hui.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-xs font-bold text-slate-500 shadow-sm">
                    <i class="fa-solid fa-calendar-days mr-2 text-primary"></i>
                    {{ now()->translatedFormat('d F Y') }}
                </span>
                <a href="{{ route('assurance.assures.create') }}" 
                   class="px-5 py-2.5 bg-secondary hover:bg-secondary-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-secondary/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Nouvel assuré
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card Assurés --}}
            <div class="card-stat flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Assurés</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalAssures) }}</h3>
                </div>
            </div>

            {{-- Card Sinistres --}}
            <div class="card-stat flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <i class="fa-solid fa-folder-open text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Sinistres</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalSinistres) }}</h3>
                </div>
            </div>

            {{-- Card En Attente --}}
            <div class="card-stat flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 shrink-0">
                    <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">En Attente</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1 text-red-600">{{ number_format($sinistresEnAttente) }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Graphique --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-3">
                        <span class="w-2 h-7 bg-primary rounded-full"></span>
                        Activité des Sinistres
                    </h2>
                    <select class="bg-slate-50 border-none rounded-lg text-xs font-bold text-slate-500 px-3 py-1.5 focus:ring-0">
                        <option>6 derniers mois</option>
                    </select>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="claimsChart"></canvas>
                </div>
            </div>

            {{-- Actions Rapides / Liens --}}
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-base font-bold text-slate-800 mb-6">Plateforme</h2>
                    <div class="space-y-3">
                        <a href="{{ route('assurance.experts.index') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-primary">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Nos Experts</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-primary transition-all"></i>
                        </a>
                        <a href="{{ route('assurance.garages.index') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-secondary">
                                    <i class="fa-solid fa-wrench"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Nos Garages</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-secondary transition-all"></i>
                        </a>
                        <a href="{{ route('assurance.documents-requis.index') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-amber-500">
                                    <i class="fa-solid fa-file-shield"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Documents Requis</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-amber-500 transition-all"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-primary rounded-3xl p-6 text-white relative overflow-hidden shadow-xl shadow-primary/20">
                    <div class="relative z-10">
                        <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest mb-1">Support Premium</p>
                        <h3 class="text-lg font-bold mb-4">Besoin d'aide ?</h3>
                        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-xs font-bold transition-all backdrop-blur-md">
                            Contacter l'assistance
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <i class="fa-solid fa-headset absolute -bottom-4 -right-4 text-7xl text-white/10 rotate-12"></i>
                </div>
            </div>
        </div>

        {{-- Derniers Sinistres --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 pb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-3">
                    <span class="w-2 h-7 bg-secondary rounded-full"></span>
                    Sinistres Récents
                </h2>
                <a href="{{ route('assurance.sinistres.index') }}" class="text-xs font-bold text-primary hover:underline">Voir tout</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Référence</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Assuré</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Statut</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentSinistres as $sinistre)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-slate-700">#{{ $sinistre->numero_sinistre ?? 'SN-' . $sinistre->id }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-primary text-[10px] font-bold">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-600">{{ $sinistre->assure->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm text-slate-500">{{ ucfirst($sinistre->type_sinistre) }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm text-slate-500">{{ $sinistre->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex justify-center">
                                        @php
                                            $statusColors = [
                                                'déclaré' => 'bg-blue-50 text-blue-600',
                                                'en_attente' => 'bg-amber-50 text-amber-600',
                                                'validé' => 'bg-green-50 text-green-600',
                                                'terminé' => 'bg-slate-50 text-slate-600',
                                                'rejeté' => 'bg-red-50 text-red-600',
                                            ];
                                            $color = $statusColors[$sinistre->status] ?? 'bg-slate-50 text-slate-400';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $color }}">
                                            {{ $sinistre->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('assurance.sinistres.show', $sinistre) }}" 
                                       class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary/30 hover:shadow-sm transition-all shadow-none">
                                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-10 text-center text-slate-400 italic text-sm">
                                    Aucun sinistre récent pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('claimsChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(36, 58, 143, 0.1)');
            gradient.addColorStop(1, 'rgba(36, 58, 143, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json(collect($chartData)->pluck('month')),
                    datasets: [{
                        label: 'Sinistres déclarés',
                        data: @json(collect($chartData)->pluck('count')),
                        borderColor: '#243a8f',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#243a8f',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1c2e72',
                            padding: 12,
                            titleFont: { size: 13, family: 'Outfit', weight: 'bold' },
                            bodyFont: { size: 12, family: 'Outfit' },
                            cornerRadius: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: { font: { family: 'Outfit', size: 11 }, color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Outfit', size: 11 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        });
    </script>
@endpush