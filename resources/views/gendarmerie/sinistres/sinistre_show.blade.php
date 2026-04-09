@extends('gendarmerie.layouts.template')
@section('title', 'Détails du sinistre')
@section('page-title', 'Détails du sinistre')

@section('content')
    <div class="mx-auto space-y-5" style="max-width:1800px;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}"
                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
                </a>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-emerald-500 text-base"></i>
                        Sinistre #{{ $sinistre->id }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Déclaré le {{ $sinistre->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
            @php
                $s = match ($sinistre->status) {
                    'en_attente' => ['En attente', 'bg-amber-100 text-amber-700'],
                    'en_cours' => ['En cours', 'bg-blue-100 text-blue-700'],
                    'cloture' => ['Clôturé', 'bg-emerald-100 text-emerald-700'],
                    default => [$sinistre->status, 'bg-slate-100 text-slate-700'],
                };
            @endphp
            <span class="inline-flex items-center gap-2 px-4 py-2 {{ $s[1] }} text-sm font-bold rounded-xl">
                <i class="fa-solid fa-circle text-[8px]"></i> {{ $s[0] }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Colonne Gauche --}}
            <div class="space-y-5">

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-emerald-50/60 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-user text-emerald-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Assuré</h3>
                    </div>
                    <div class="p-5 grid grid-cols-2 gap-4">
                        <x-detail label="Nom" :value="$sinistre->assure->name ?? null" />
                        <x-detail label="Prénom" :value="$sinistre->assure->prenom ?? null" />
                        <x-detail label="Contact" :value="$sinistre->assure->contact ?? null" />
                        <x-detail label="Email" :value="$sinistre->assure->email ?? null" />
                        <div class="col-span-2"><x-detail label="Adresse" :value="$sinistre->assure->adresse ?? null" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-orange-50/60 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-triangle-exclamation text-orange-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Type d'incident</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <x-detail label="Type de sinistre" :value="str_replace('_', ' ', $sinistre->type_sinistre)" />
                        <x-detail label="Description" :value="$sinistre->description" />
                    </div>
                </div>

                @if($sinistre->latitude || $sinistre->longitude)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-amber-50/60 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-xs"><i
                                    class="fa-solid fa-map-pin text-amber-600"></i></div>
                            <h3 class="font-bold text-slate-800 text-sm">Localisation GPS</h3>
                        </div>
                        <div class="p-5 space-y-3">
                            <div class="flex gap-4">
                                <x-detail label="Latitude" :value="$sinistre->latitude" />
                                <x-detail label="Longitude" :value="$sinistre->longitude" />
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

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-violet-50/60 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center text-xs"><i
                                class="fa-solid fa-file-lines text-violet-600"></i></div>
                        <h3 class="font-bold text-slate-800 text-sm">Constat associé</h3>
                    </div>
                    <div class="p-5">
                        @if($sinistre->constat)
                            <div class="flex items-center gap-3 p-3 bg-violet-50 rounded-xl border border-violet-100">
                                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file-check text-violet-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-800">Constat
                                        {{ $sinistre->constat->type_constat === 'accident' ? "d'accident" : "d'incident" }}</p>
                                    <p class="text-xs text-slate-500">Établi le
                                        {{ $sinistre->constat->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <a href="{{ route('gendarmerie.sinistres.constat.show', $sinistre->id) }}"
                                    class="px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-lg transition-colors">
                                    Voir
                                </a>
                            </div>
                        @else
                            <div
                                class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file-circle-question text-slate-400"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-600">Aucun constat établi</p>
                                    <p class="text-xs text-slate-400">Le constat n'a pas encore été rédigé.</p>
                                </div>
                                @if($sinistre->status === 'en_attente')
                                    <a href="{{ route('gendarmerie.sinistres.constat.create', $sinistre->id) }}"
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors">
                                        Créer

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
        class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4 cursor-zoom-out">
        <img id="img-modal-src" src="" alt="Plein écran" class="max-h-[90vh] max-w-full rounded-2xl shadow-2xl">
    </div>

    <script>
        function openModal(src) {
            document.getElementById('img-modal-src').src = src;
            document.getElementById('img-modal').classList.remove('hidden');
        }
    </script>
@endsection