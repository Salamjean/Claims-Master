@extends('agent.layouts.template')
@section('title', 'Sinistres Publics')
@section('page-title', 'Sinistres Publics')

@section('content')
    <div class="space-y-5 mx-auto" style="width: 100%;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center">
                        <i class="fa-solid fa-globe text-blue-500 text-sm"></i>
                    </div>
                    Sinistres Publics (À Récupérer)
                </h2>
                <p class="text-sm text-slate-500 mt-1">Déclarations assignées à votre service et en attente de traitement.</p>
            </div>
        </div>

        @if($sinistres->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
                <i class="fa-solid fa-inbox text-slate-300 text-4xl mb-4 block"></i>
                <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun sinistre en attente</h3>
                <p class="text-sm text-slate-400">Tous les sinistres assignés à votre service ont été traités.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Assuré</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Type</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell text-center">Description</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Photos</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Statut</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell text-center">Date</th>
                                <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($sinistres as $sinistre)
                                <tr class="hover:bg-amber-50/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                                {{ strtoupper(substr($sinistre->assure->name ?? 'A', 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-semibold text-slate-700">{{ $sinistre->assure->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-600 text-center uppercase">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</td>
                                    <td class="px-5 py-3.5 hidden md:table-cell text-center">
                                        <p class="text-sm text-slate-500 truncate max-w-[180px] mx-auto">{{ $sinistre->description ?? '—' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if($sinistre->photos && count($sinistre->photos) > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100">
                                                <i class="fa-solid fa-camera"></i> {{ count($sinistre->photos) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 font-bold">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                            <i class="fa-solid fa-circle text-[6px]"></i> En attente
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 hidden lg:table-cell text-xs text-slate-500 text-center">
                                        {{ $sinistre->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('agent.sinistres.show', $sinistre->id) }}"
                                                class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg transition-colors" title="Voir détails">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
    
                                            @if(!$sinistre->assigned_agent_id)
                                                <form action="{{ route('agent.sinistres.claim', $sinistre->id) }}" method="POST" class="inline claim-form">
                                                    @csrf
                                                    <input type="hidden" name="agent_lat" class="gps-lat" value="">
                                                    <input type="hidden" name="agent_lng" class="gps-lng" value="">
                                                    <button type="submit"
                                                        class="p-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors shadow-sm" title="Récupérer">
                                                        <i class="fa-solid fa-hand-holding-hand"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('agent.sinistres.constat.create', $sinistre->id) }}"
                                                    class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm" title="Faire le constat">
                                                    <i class="fa-solid fa-file-pen"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.querySelectorAll('.gps-lat').forEach(el => el.value = pos.coords.latitude);
            document.querySelectorAll('.gps-lng').forEach(el => el.value = pos.coords.longitude);
        }, null, { enableHighAccuracy: true, timeout: 10000 });
    }
</script>
@endpush
