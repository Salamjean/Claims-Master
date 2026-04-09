@extends('gendarmerie.layouts.template')
@section('title', 'Détails du constat')
@section('page-title', 'Détails du constat')

@section('content')
    <div class="mx-auto space-y-5" style="max-width:1800px;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('gendarmerie.sinistres.historique') }}"
                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
                </a>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-contract text-emerald-500 text-base"></i>
                        Constat Officiel #{{ $constat->id }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Sinistre {{ $sinistre->numero_sinistre }} &mdash;
                        <span class="font-semibold text-slate-700">{{ $sinistre->assure->name ?? '' }} {{ $sinistre->assure->prenom ?? '' }}</span>
                        &mdash; <span class="text-slate-400">{{ $constat->created_at->format('d/m/Y à H:i') }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('gendarmerie.sinistres.constat.create', $sinistre->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-bold rounded-xl transition-colors border border-emerald-200">
                <i class="fa-solid fa-pen-to-square text-xs"></i> Modifier
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Colonne Gauche : Contenu du constat --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Informations générales --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-circle-info text-emerald-600 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Informations générales</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lieu des faits</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $constat->lieu }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date et Heure précises</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $constat->date_heure ? $constat->date_heure->format('d/m/Y à H:i') : '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Corps du constat --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-align-left text-emerald-600 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Rapport de l'incident</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nature des faits / Circonstances</p>
                            <div class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                                {{ $constat->description_faits }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-rose-500">Dommages et Dégâts constatés</p>
                            <div class="text-sm text-slate-700 font-medium bg-rose-50/30 p-4 rounded-xl border border-rose-100 whitespace-pre-line">
                                {{ $constat->dommages }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Témoins / Personnes impliquées</p>
                                <p class="text-sm text-slate-600 bg-slate-50/50 p-3 rounded-lg border border-slate-100 min-h-[50px]">
                                    {{ $constat->temoins ?? 'Aucun témoin mentionné' }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Observations / Mesures prises</p>
                                <p class="text-sm text-slate-600 bg-slate-50/50 p-3 rounded-lg border border-slate-100 min-h-[50px]">
                                    {{ $constat->observations ?? 'Aucune observation particulière' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Croquis --}}
                @if($constat->croquis)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-teal-50/50 flex items-center gap-3">
                            <i class="fa-solid fa-pen-nib text-teal-600 text-xs"></i>
                            <h3 class="font-bold text-slate-800 text-sm">Croquis de situation</h3>
                        </div>
                        <div class="p-6 bg-slate-50/50 flex justify-center">
                            @if(Str::startsWith($constat->croquis, 'data:image'))
                                <img src="{{ $constat->croquis }}" class="max-h-96 object-contain rounded-xl shadow-sm border border-slate-200 cursor-zoom-in" onclick="openModal(this.src)">
                            @else
                                <img src="{{ Storage::url($constat->croquis) }}" class="max-h-96 object-contain rounded-xl shadow-sm border border-slate-200 cursor-zoom-in" onclick="openModal(this.src)">
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Colonne Droite : Docs & Agent --}}
            <div class="space-y-6">
                
                {{-- Agent responsable --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-emerald-600 text-white flex items-center gap-3">
                        <i class="fa-solid fa-user-shield text-xs text-emerald-200"></i>
                        <h3 class="font-bold text-sm">Brigadier / Agent en charge</h3>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-extrabold border border-emerald-100">
                                {{ strtoupper(substr($sinistre->assignedAgent->name ?? $user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-slate-800">
                                    {{ $sinistre->assignedAgent ? ($sinistre->assignedAgent->name . ' ' . $sinistre->assignedAgent->prenom) : $user->name }}
                                </p>
                                <p class="text-xs text-emerald-600 font-bold mt-0.5">
                                    {{ $sinistre->assignedAgent ? $sinistre->assignedAgent->contact : $user->contact }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Photos d'assurance --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved text-slate-400 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Pièces d'assurance</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        @if($constat->ass1_photo)
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Assuré (Partie A)</p>
                                <img src="{{ Storage::url($constat->ass1_photo) }}" class="w-full h-32 object-cover rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity" onclick="openModal(this.src)">
                            </div>
                        @endif
                        @if($constat->ass2_photo)
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiers (Partie B)</p>
                                <img src="{{ Storage::url($constat->ass2_photo) }}" class="w-full h-32 object-cover rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity" onclick="openModal(this.src)">
                            </div>
                        @endif
                        @if(!$constat->ass1_photo && !$constat->ass2_photo)
                            <p class="text-xs text-slate-400 italic text-center py-4">Aucune photo d'assurance</p>
                        @endif
                    </div>
                </div>

                {{-- Photos supplémentaires --}}
                @if($constat->photos_plus && count($constat->photos_plus) > 0)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                            <i class="fa-solid fa-camera text-slate-400 text-xs"></i>
                            <h3 class="font-bold text-slate-800 text-sm">Annexes ({{ count($constat->photos_plus) }})</h3>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-2">
                            @foreach($constat->photos_plus as $photo)
                                <img src="{{ Storage::url($photo) }}" class="w-full h-24 object-cover rounded-lg border border-slate-200 cursor-pointer hover:scale-105 transition-transform" onclick="openModal(this.src)">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal image --}}
    <div id="img-modal" onclick="this.classList.add('hidden')"
        class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4 cursor-zoom-out">
        <img id="img-modal-src" src="" alt="Plein écran" class="max-h-[90vh] max-w-full rounded-2xl shadow-2xl">
    </div>

    <script>
        function openModal(src) {
            document.getElementById('img-modal-src').src = src;
            document.getElementById('img-modal').classList.remove('hidden');
        }
    </script>
@endsection