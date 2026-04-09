@extends('agent.layouts.template')
@section('title', 'Détails du sinistre')
@section('page-title', 'Détails du sinistre')

@section('content')
    <div class="mx-auto space-y-5" style="width: 100%;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}"
                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
                </a>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-blue-500 text-base"></i>
                        Sinistre {{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Déclaré le {{ $sinistre->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
            {{-- Statut --}}
            @php
                $s = match ($sinistre->status) {
                    'en_attente'          => ['En attente',             'bg-amber-100 text-amber-700'],
                    'en_cours'            => ['En cours',               'bg-blue-100 text-blue-700'],
                    'constat_terrain_ok'  => ['Constat terrain ✅',     'bg-violet-100 text-violet-700'],
                    'traite'              => ['Traité — En attente assurance', 'bg-emerald-100 text-emerald-700'],
                    'cloture'             => ['Clôturé',                'bg-slate-100 text-slate-600'],
                    default               => [$sinistre->status,        'bg-slate-100 text-slate-700'],
                };
            @endphp
            <span class="inline-flex items-center gap-2 px-4 py-2 {{ $s[1] }} text-sm font-bold rounded-xl">
                <i class="fa-solid fa-circle text-[8px]"></i> {{ $s[0] }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Colonne Gauche --}}
            <div class="space-y-5">

                {{-- Informations de l'assuré --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-blue-50/60 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-user text-blue-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Assuré</h3>
                    </div>
                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div class="detail-item">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Nom Complet</p>
                            <p class="text-sm font-bold text-slate-700">{{ $sinistre->assure->name ?? '—' }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Contact</p>
                            <p class="text-sm font-bold text-slate-700">{{ $sinistre->assure->contact ?? '—' }}</p>
                        </div>
                        <div class="col-span-2">
                             <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Adresse</p>
                            <p class="text-sm font-bold text-slate-700">{{ $sinistre->assure->adresse ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Informations du sinistre --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-orange-50/60 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-triangle-exclamation text-orange-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Type d'incident</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Nature du sinistre</p>
                            <p class="text-sm font-bold text-slate-700 uppercase">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Description</p>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $sinistre->description }}</p>
                        </div>
                    </div>
                </div>

                {{-- Localisation --}}
                @if($sinistre->latitude || $sinistre->longitude)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-amber-50/60 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-xs"><i
                                    class="fa-solid fa-map-pin text-amber-600"></i></div>
                            <h3 class="font-bold text-slate-800 text-sm">Localisation GPS</h3>
                        </div>
                        <div class="p-5 space-y-3 text-center">
                            <div class="flex justify-center gap-8">
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Latitude</p>
                                    <p class="text-sm font-mono font-bold text-slate-700">{{ $sinistre->latitude }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Longitude</p>
                                    <p class="text-sm font-mono font-bold text-slate-700">{{ $sinistre->longitude }}</p>
                                </div>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $sinistre->latitude }},{{ $sinistre->longitude }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-bold rounded-xl transition-colors border border-amber-200">
                                <i class="fa-solid fa-map-location-dot text-xs"></i> Voir sur Google Maps
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Colonne Droite --}}
            <div class="space-y-5">

                {{-- Photos du sinistre --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-slate-200 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-images text-slate-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">
                            Photos du sinistre
                            @if($sinistre->photos && count($sinistre->photos) > 0)
                                <span
                                    class="ml-2 px-2 py-0.5 bg-slate-200 text-slate-600 text-xs rounded-full">{{ count($sinistre->photos) }}</span>
                            @endif
                        </h3>
                    </div>
                    <div class="p-5">
                        @if($sinistre->photos && count($sinistre->photos) > 0)
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($sinistre->photos as $photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Photo sinistre"
                                        class="w-full h-36 object-cover rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity"
                                        onclick="openModal(this.src)">
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-slate-300">
                                <i class="fa-solid fa-image text-4xl mb-2"></i>
                                <p class="text-sm font-medium">Aucune photo jointe</p>
                            </div>
                        @endif
                    </div>
                </div>
                        {{-- Gestion du dossier (Récupération / Agent en charge) --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-center">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-slate-200 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-user-shield text-slate-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Prise en charge</h3>
                    </div>
                    <div class="p-5">
                        @if(!$sinistre->assigned_agent_id)
                            <div class="space-y-3">
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 mb-3">
                                    <p class="text-sm font-bold text-emerald-800">Dossier disponible</p>
                                    <p class="text-xs text-emerald-600">Ce sinistre n'est pas encore attribué.</p>
                                </div>
                                <form action="{{ route('agent.sinistres.claim', $sinistre->id) }}" method="POST" id="claim-form">
                                    @csrf
                                    <input type="hidden" name="agent_lat" id="gps-lat" value="">
                                    <input type="hidden" name="agent_lng" id="gps-lng" value="">
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-md text-sm">
                                        <i class="fa-solid fa-hand-holding-hand"></i>
                                        Récupérer le dossier
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-3 {{ $sinistre->assigned_agent_id === auth('user')->id() ? 'bg-blue-50 border-blue-100' : 'bg-slate-50 border-slate-100' }} rounded-xl border">
                                <div class="w-10 h-10 rounded-xl {{ $sinistre->assigned_agent_id === auth('user')->id() ? 'bg-blue-100 text-blue-600' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($sinistre->assignedAgent->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $sinistre->assigned_agent_id === auth('user')->id() ? 'Vous traitez ce dossier' : 'Traité par ' . ($sinistre->assignedAgent->name ?? 'un agent') }}
                                    </p>
                                    <p class="text-xs text-slate-500">Agent matricule #{{ $sinistre->assigned_agent_id }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Lien vers le constat --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-violet-50/60 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-file-lines text-violet-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Constat associé</h3>
                    </div>
                    <div class="p-5">
                        @if($sinistre->constat)
                            {{-- Constat terrain existant --}}
                            <div class="flex items-center gap-3 p-3 bg-violet-50 rounded-xl border border-violet-100 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file-check text-violet-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-800">Constat terrain
                                        {{ $sinistre->constat->type_constat === 'accident' ? "d'accident" : "général" }}
                                    </p>
                                    <p class="text-xs text-slate-500">Établi le {{ $sinistre->constat->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <a href="{{ route('agent.sinistres.constat.show', $sinistre->id) }}"
                                    class="px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-lg transition-colors">
                                    Voir
                                </a>
                            </div>

                            {{-- Bouton rédaction officielle --}}
                            @if($sinistre->constat->terrain_valide && !$sinistre->constat->redaction_validee && $sinistre->assigned_agent_id === auth('user')->id())
                            <a href="{{ route('agent.sinistres.redaction', $sinistre->id) }}"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-sm rounded-xl transition-all shadow-md shadow-violet-200 mt-2">
                                <i class="fa-solid fa-file-pen"></i> Rédiger le Constat Officiel
                            </a>
                            @elseif($sinistre->constat->redaction_validee)
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                <p class="text-xs font-black text-emerald-700">✅ Rédaction officielle validée</p>
                                <p class="text-xs text-emerald-600 mt-1">Le {{ $sinistre->constat->redaction_validee_at?->format('d/m/Y à H:i') }}</p>
                            </div>
                            @endif
                        @else
                            <div
                                class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file-circle-question text-slate-400"></i>
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-bold text-slate-600">Aucun constat établi</p>
                                    <p class="text-xs text-slate-400">Le constat n'a pas encore été rédigé.</p>
                                </div>
                                @if($sinistre->assigned_agent_id === auth('user')->id() && $sinistre->status !== 'cloture')
                                    <a href="{{ route('agent.sinistres.constat.create', $sinistre->id) }}"
                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                        <i class="fa-solid fa-file-pen mr-1"></i> Créer
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Modal image --}}
    <div id="img-modal" onclick="this.classList.add('hidden')"
        class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4 cursor-zoom-out text-center">
        <img id="img-modal-src" src="" alt="Plein écran" class="max-h-[90vh] max-w-full rounded-2xl shadow-2xl">
    </div>

    <script>
        function openModal(src) {
            document.getElementById('img-modal-src').src = src;
            document.getElementById('img-modal').classList.remove('hidden');
        }
    </script>
@endsection

@push('scripts')
<script>
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('gps-lat').value = pos.coords.latitude;
            document.getElementById('gps-lng').value = pos.coords.longitude;
        }, function(err) {
            console.warn('GPS Error:', err.message);
        }, { enableHighAccuracy: true, timeout: 10000 });
    }
</script>
@endpush
