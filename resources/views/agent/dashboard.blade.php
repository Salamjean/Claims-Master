@extends('agent.layouts.template')

@section('title', 'Tableau de bord')

@section('content')
    <div class="space-y-6 mx-auto" style="width: 100%;">

        {{-- En-tête --}}
        <div class="relative rounded-2xl overflow-hidden text-white px-6 py-6 md:px-8 md:py-7 shadow-xl"
            style="background: linear-gradient(135deg, #1d3557 0%, #152840 100%);">
            <div
                style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.07),transparent);pointer-events:none;">
            </div>
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.5)]"></span>
                        <span class="text-[10px] font-black text-green-300 uppercase tracking-[0.2em]">Agent en Service</span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-black">Bonjour, <span class="text-blue-300">{{ $agent->name }}</span> 👮</h1>
                    <p class="text-[11px] text-white/40 font-bold uppercase tracking-wider mt-1 opacity-60">
                        {{ $agent->service->name ?? 'Service de Constat National' }}
                    </p>
                </div>
                <div class="flex items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl px-5 py-4 md:px-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-300">
                        <i class="fa-solid fa-rss text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-white/30 font-black uppercase tracking-widest mb-0.5">Pool Général</p>
                        <p class="text-2xl font-black leading-none">{{ $totalPublic ?? 0 }} <span class="text-[10px] text-white/20 font-bold ml-1">Sinistres</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Compteurs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5 group hover:border-amber-200 transition-all">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $enAttente }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">En attente</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5 group hover:border-blue-200 transition-all">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $enCours }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dossiers en cours</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5 group hover:border-emerald-200 transition-all sm:col-span-2 lg:col-span-1">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $cloture }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sinistres clôturés</p>
                </div>
            </div>
        </div>

        {{-- Table des sinistres avec auto-refresh --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-globe text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-tight">Pool Général</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Sinistres disponibles</p>
                    </div>
                </div>
                <div id="refresh-indicator" class="flex items-center self-end sm:self-auto gap-2.5 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Actualisation</span>
                    <div class="flex items-center gap-1.5">
                        <span id="countdown" class="text-xs font-black text-blue-600 w-5 text-center">30</span>
                        <div class="w-4 h-4 rounded-full border-2 border-slate-200 border-t-blue-500 animate-spin hidden" id="spinner"></div>
                    </div>
                </div>
            </div>

            <div id="sinistres-table-wrapper" class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px] lg:min-w-full" id="sinistres-table">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Réf.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Assuré</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Type</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Médias</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Statut</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center hidden md:table-cell">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sinistres-body" class="divide-y divide-slate-50">
                        @forelse($sinistres as $sinistre)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[11px] font-black text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg border border-blue-100">{{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</span>
                                </td>
                                <td class="px-6 py-4" style="display:flex; justify-content: center;">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-xs font-black text-slate-500 border border-slate-200">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-700 leading-none mb-1">{{ $sinistre->assure->name . ' ' . $sinistre->assure->prenom ?? '—' }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $sinistre->assure->code_user ?? 'Client' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[11px] font-black text-slate-600 bg-slate-50 px-2 py-1 rounded-md uppercase tracking-tight">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sinistre->photos && count($sinistre->photos) > 0)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg border border-emerald-100">
                                            <i class="fa-solid fa-camera"></i> {{ count($sinistre->photos) }}
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-300 font-bold">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-600 text-[10px] font-black rounded-full border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        EN ATTENTE
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[11px] text-slate-400 font-bold text-center hidden md:table-cell">{{ $sinistre->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('agent.sinistres.claim', $sinistre->id) }}" method="POST" class="inline claim-form">
                                            @csrf
                                            <input type="hidden" name="agent_lat" class="gps-lat" value="">
                                            <input type="hidden" name="agent_lng" class="gps-lng" value="">
                                            <button type="submit"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95 uppercase tracking-wider">
                                                <i class="fa-solid fa-plus text-[10px]"></i>
                                                Récupérer
                                            </button>
                                        </form>
                                        <a href="{{ route('agent.sinistres.show', $sinistre->id) }}"
                                            class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="Voir détails">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <i class="fa-solid fa-inbox text-2xl text-slate-200"></i>
                                    </div>
                                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucun nouveau sinistre disponible</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const AJAX_URL = '{{ route("agent.sinistres.json") }}';
        let countdown = 30;
        const countEl = document.getElementById('countdown');
        const spinner = document.getElementById('spinner');
        const tbody = document.getElementById('sinistres-body');

        function statusBadge(status) {
            const map = {
                'en_attente': ['EN ATTENTE', 'bg-amber-50 text-amber-600 border-amber-100', 'bg-amber-500 animate-pulse'],
                'en_cours': ['EN COURS', 'bg-blue-50 text-blue-600 border-blue-100', 'bg-blue-500'],
                'cloture': ['CLÔTURÉ', 'bg-emerald-50 text-emerald-600 border-emerald-100', 'bg-emerald-500'],
            };
            const [label, cls, dotCls] = map[status] ?? [status.toUpperCase(), 'bg-slate-50 text-slate-600 border-slate-100', 'bg-slate-500'];
            return `<span class="inline-flex items-center gap-2 px-3 py-1.5 ${cls} text-[10px] font-black rounded-full border">
                        <span class="w-1.5 h-1.5 rounded-full ${dotCls}"></span>
                        ${label}
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
                        tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-16 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i class="fa-solid fa-inbox text-2xl text-slate-200"></i>
                            </div>
                            <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Aucun nouveau sinistre disponible</p>
                        </td></tr>`;
                        return;
                    }
                    tbody.innerHTML = data.map(s => `
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <span class="text-[11px] font-black text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg border border-blue-100">${s.numero_sinistre || 'SI-'+s.id}</span>
                            </td>
                            <td class="px-6 py-4" style="display:flex; justify-content: center;">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-xs font-black text-slate-500 border border-slate-200">
                                        ${(s.assure || 'A').charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700 leading-none mb-1">${s.assure + ' ' + s.prenom || '—'}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">${s.code_user || '—'}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[11px] font-black text-slate-600 bg-slate-50 px-2 py-1 rounded-md uppercase tracking-tight">${s.type_sinistre}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                ${s.photos_count > 0 ? `
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg border border-emerald-100">
                                        <i class="fa-solid fa-camera"></i> ${s.photos_count}
                                    </div>
                                ` : '<span class="text-[10px] text-slate-300 font-bold">—</span>'}
                            </td>
                            <td class="px-6 py-4 text-center">
                                ${statusBadge(s.status)}
                            </td>
                            <td class="px-6 py-4 text-[11px] text-slate-400 font-bold text-center hidden md:table-cell">${s.created_at}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="/agent/sinistres/${s.id}/claim" method="POST" class="inline claim-form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="agent_lat" class="gps-lat" value="${window._agentLat || ''}">
                                        <input type="hidden" name="agent_lng" class="gps-lng" value="${window._agentLng || ''}">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95 uppercase tracking-wider">
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                            Récupérer
                                        </button>
                                    </form>
                                    <a href="/agent/sinistres/${s.id}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="Voir détails">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(() => { spinner.classList.add('hidden'); });
        }

        setInterval(() => {
            countdown--;
            countEl.textContent = countdown;
            if (countdown <= 0) refreshTable();
        }, 1000);
    </script>
    <script>
        // Capture GPS automatique au chargement de la page
        window._agentLat = '';
        window._agentLng = '';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                window._agentLat = pos.coords.latitude;
                window._agentLng = pos.coords.longitude;
                // Remplir les champs cachés dans les formulaires statiques
                document.querySelectorAll('.gps-lat').forEach(el => el.value = window._agentLat);
                document.querySelectorAll('.gps-lng').forEach(el => el.value = window._agentLng);
            }, function(err) {
                console.warn('GPS non disponible:', err.message);
            }, { enableHighAccuracy: true, timeout: 10000 });
        }
    </script>
@endsection
