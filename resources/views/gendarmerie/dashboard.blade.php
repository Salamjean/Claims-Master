@extends('gendarmerie.layouts.template')

@section('title', 'Tableau de bord')

@section('content')
    <div class="space-y-6 mx-auto" style="max-width: 1800px;">

        {{-- En-tête --}}
        <div class="relative rounded-2xl overflow-hidden text-white px-8 py-7"
            style="background: linear-gradient(135deg, #1a3a2a 0%, #122b1e 100%);">
            <div
                style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.07),transparent);pointer-events:none;">
            </div>
            <div class="relative flex flex-wrap items-center justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs font-semibold text-green-300 uppercase tracking-wider">Brigade active</span>
                    </div>
                    <h1 class="text-2xl font-extrabold">Bonjour, <span class="text-emerald-300">{{ $user->name }}</span> 🎖️
                    </h1>
                    <p class="text-sm text-white/50 mt-1">Brigade de Gendarmerie &mdash;
                        {{ $user->contact ?? 'Contact non renseigné' }}</p>
                </div>
                <div class="text-right bg-white/10 border border-white/20 rounded-xl px-6 py-4">
                    <p class="text-xs text-white/50 uppercase tracking-wider mb-1">Sinistres assignés</p>
                    <p class="text-3xl font-extrabold">{{ $total }}</p>
                </div>
            </div>
        </div>

        {{-- Compteurs --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
                <p class="text-3xl font-extrabold text-amber-500">{{ $enAttente }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-widest">En attente</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
                <p class="text-3xl font-extrabold text-blue-500">{{ $enCours }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-widest">En cours</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
                <p class="text-3xl font-extrabold text-emerald-500">{{ $cloture }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-widest">Clôturés</p>
            </div>
            <div class="bg-slate-900 rounded-2xl border border-white/10 shadow-lg p-5 text-center relative overflow-hidden group cursor-pointer" onclick="window.location='{{ route('gendarmerie.wallet') }}'">
                <div class="absolute -right-4 -top-4 w-12 h-12 bg-emerald-500/10 rounded-full blur-xl"></div>
                <p class="text-2xl font-black text-emerald-400 relative z-10">{{ number_format($user->wallet_balance ?? 0, 0, ',', ' ') }} F</p>
                <p class="text-[10px] font-black text-white/40 mt-1 uppercase tracking-[0.2em] relative z-10">Portefeuille</p>
            </div>
        </div>

        {{-- Table des sinistres avec auto-refresh --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fa-solid fa-list-check text-emerald-600 text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Sinistres assignés à votre brigade</h2>
                    </div>
                </div>
                <div id="refresh-indicator" class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span id="countdown" class="font-mono font-bold text-slate-600">30</span>s
                    <div class="w-6 h-6 rounded-full border-2 border-slate-200 border-t-emerald-500 animate-spin hidden"
                        id="spinner"></div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-center px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Assuré
                            </th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Type
                            </th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Photos
                            </th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Statut
                            </th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Date
                            </th>
                        </tr>
                    </thead>
                    <tbody id="sinistres-body" class="divide-y divide-slate-50">
                        @forelse($sinistres as $index => $sinistre)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5" style="display: flex; justify-content: center; align-items: center;">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $sinistre->assure->name ?? '—' }}
                                            {{ $sinistre->assure->prenom ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-slate-600" style="display: flex; justify-content: center; align-items: center;">
                                    {{ str_replace('_', ' ', $sinistre->type_sinistre) }}</td>
                                <td class="px-5 py-3.5" style="display: flex; justify-content: center; align-items: center;">
                                    @if($sinistre->photos && count($sinistre->photos) > 0)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg">
                                            <i class="fa-solid fa-images"></i> {{ count($sinistre->photos) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5" style="display: flex; justify-content: center; align-items: center;">
                                    @php
                                        $s = match ($sinistre->status) {
                                            'en_attente' => ['En attente', 'bg-amber-100 text-amber-700'],
                                            'en_cours' => ['En cours', 'bg-blue-100 text-blue-700'],
                                            'traite' => ['Traité (Gendarme)', 'bg-emerald-100 text-emerald-700'],
                                            'cloture' => ['Clôturé (Assurance)', 'bg-emerald-600 text-white'],
                                            default => [$sinistre->status, 'bg-slate-100 text-slate-700'],
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 {{ $s[1] }} text-xs font-bold rounded-full">
                                        <i class="fa-solid fa-circle text-[6px]"></i> {{ $s[0] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500" style="display: flex; justify-content: center; align-items: center;">{{ $sinistre->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <i class="fa-solid fa-inbox text-slate-300 text-3xl mb-3 block"></i>
                                    <p class="text-sm text-slate-400 font-medium">Aucun sinistre assigné à votre brigade pour le
                                        moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const AJAX_URL = '{{ route("gendarmerie.sinistres.json") }}';
        let countdown = 30;
        const countEl = document.getElementById('countdown');
        const spinner = document.getElementById('spinner');
        const tbody = document.getElementById('sinistres-body');

        function statusBadge(status) {
            const map = {
                'en_attente': ['En attente', 'bg-amber-100 text-amber-700'],
                'en_cours': ['En cours', 'bg-blue-100 text-blue-700'],
                'traite': ['Traité (Gendarme)', 'bg-emerald-100 text-emerald-700'],
                'cloture': ['Clôturé (Assurance)', 'bg-emerald-600 text-white'],
            };
            const [label, cls] = map[status] ?? [status, 'bg-slate-100 text-slate-700'];
            return `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 ${cls} text-xs font-bold rounded-full">
                        <i class="fa-solid fa-circle text-[6px]"></i> ${label}
                    </span>`;
        }

        function refreshTable() {
            spinner.classList.remove('hidden');
            fetch(AJAX_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    spinner.classList.add('hidden');
                    countdown = 30;
                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-inbox text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-sm text-slate-400 font-medium">Aucun sinistre assigné à votre brigade.</p>
                        </td></tr>`;
                        return;
                    }
                    tbody.innerHTML = data.map((s, i) => `
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5" style="display: flex; justify-content: center; align-items: center;">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                        ${s.assure.charAt(0).toUpperCase()}
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700">${s.assure}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600 text-center">${s.type_sinistre}</td>
                            <td class="px-5 py-3.5 text-center">
                                ${s.photos_count > 0
                            ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg"><i class="fa-solid fa-images"></i> ${s.photos_count}</span>`
                            : `<span class="text-xs text-slate-400">—</span>`}
                            </td>
                            <td class="px-5 py-3.5 text-center">${statusBadge(s.status)}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 text-center">${s.created_at}</td>
                        </tr>
                    `).join('');
                })
                .catch(() => { spinner.classList.add('hidden'); });
        }

        setInterval(() => {
            countdown--;
            countEl.textContent = countdown;
            if (countdown <= 0) {
                refreshTable();
            }
        }, 1000);
    </script>
@endsection