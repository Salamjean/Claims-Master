@extends('agent.layouts.template')
@section('title', 'Détails du constat')
@section('page-title', 'Détails du constat')

@section('content')
    <div class="mx-auto space-y-5" style="width: 100%;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('agent.sinistres.historique') }}"
                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
                </a>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice text-blue-500 text-base"></i>
                        Votre Constat #{{ $constat->id }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Sinistre {{ $sinistre->numero_sinistre }} &mdash;
                        <span class="font-semibold text-slate-700">{{ $sinistre->assure->name ?? '' }}</span>
                        &mdash; <span class="text-slate-400">{{ $constat->created_at->format('d/m/Y à H:i') }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('agent.sinistres.constat.create', $sinistre->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-bold rounded-xl transition-colors border border-blue-200">
                <i class="fa-solid fa-pen-to-square text-xs"></i> Modifier
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne Gauche : Contenu du constat --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informations générales --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-circle-info text-blue-600 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Informations générales</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lieu des faits</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $constat->lieu }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date et Heure</p>
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $constat->date_heure ? $constat->date_heure->format('d/m/Y à H:i') : '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Véhicule A --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-blue-600 text-white flex items-center gap-3">
                            <i class="fa-solid fa-car text-xs"></i>
                            <h3 class="font-bold text-sm">VÉHICULE A</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Marque & Type
                                    </p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $constat->veh_a_marque ?? '—' }}
                                        {{ $constat->veh_a_type ?? '' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">État & Pneus
                                    </p>
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ str_replace('_', ' ', $constat->veh_a_etat_general) ?? '—' }} /
                                        {{ str_replace('_', ' ', $constat->veh_a_pneumatiques) ?? '—' }}</p>
                                </div>
                            </div>
                            <hr class="border-slate-50">
                            <div class="space-y-3">
                                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Conducteur</p>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-800">{{ $constat->veh_a_conducteur_nom ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500">Né(e) le
                                        {{ $constat->veh_a_conducteur_date_naissance ? $constat->veh_a_conducteur_date_naissance->format('d/m/Y') : '—' }}
                                        à {{ $constat->veh_a_conducteur_lieu_naissance ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">Fils/Fille de
                                        {{ $constat->veh_a_conducteur_pere ?? '—' }} et
                                        {{ $constat->veh_a_conducteur_mere ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">Nat:
                                        {{ $constat->veh_a_conducteur_nationalite ?? '—' }} | Tél:
                                        {{ $constat->veh_a_conducteur_tel ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">Prof:
                                        {{ $constat->veh_a_conducteur_profession ?? '—' }} | Dom:
                                        {{ $constat->veh_a_conducteur_domicile ?? '—' }}</p>
                                </div>
                            </div>
                            <hr class="border-slate-50">
                            <div class="space-y-3">
                                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Permis & Assurance
                                </p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Permis N°
                                        </p>
                                        <p class="text-xs font-semibold text-slate-700">
                                            {{ $constat->veh_a_permis_numero ?? '—' }}
                                            ({{ $constat->veh_a_permis_categories ?? '—' }})</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Valable
                                            jusqu'au</p>
                                        <p class="text-xs font-semibold text-slate-700">
                                            {{ $constat->veh_a_permis_validite ? $constat->veh_a_permis_validite->format('d/m/Y') : '—' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Assurance &
                                        Police</p>
                                    <p class="text-xs font-semibold text-slate-700">
                                        {{ $constat->veh_a_assurance_nom ?? '—' }}</p>
                                    <p class="text-[10px] text-slate-500">Police: {{ $constat->veh_a_police_numero ?? '—' }}
                                        | Attest: {{ $constat->veh_a_attestation_numero ?? '—' }}</p>
                                </div>
                            </div>
                            @if($constat->veh_a_degats_materiels)
                                <div class="pt-2">
                                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Dégâts apparents</p>
                                    <p
                                        class="text-xs text-slate-600 bg-rose-50 p-2 rounded-lg border border-rose-100 mt-1 italic">
                                        {{ $constat->veh_a_degats_materiels }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Véhicule B --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-rose-600 text-white flex items-center gap-3">
                            <i class="fa-solid fa-car text-xs"></i>
                            <h3 class="font-bold text-sm">VÉHICULE B</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Marque & Type
                                    </p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $constat->veh_b_marque ?? '—' }}
                                        {{ $constat->veh_b_type ?? '' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">État & Pneus
                                    </p>
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ str_replace('_', ' ', $constat->veh_b_etat_general) ?? '—' }} /
                                        {{ str_replace('_', ' ', $constat->veh_b_pneumatiques) ?? '—' }}</p>
                                </div>
                            </div>
                            <hr class="border-slate-50">
                            <div class="space-y-3">
                                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest">Conducteur</p>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-800">{{ $constat->veh_b_conducteur_nom ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500">Né(e) le
                                        {{ $constat->veh_b_conducteur_date_naissance ? $constat->veh_b_conducteur_date_naissance->format('d/m/Y') : '—' }}
                                        à {{ $constat->veh_b_conducteur_lieu_naissance ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">Fils/Fille de
                                        {{ $constat->veh_b_conducteur_pere ?? '—' }} et
                                        {{ $constat->veh_b_conducteur_mere ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">Nat:
                                        {{ $constat->veh_b_conducteur_nationalite ?? '—' }} | Tél:
                                        {{ $constat->veh_b_conducteur_tel ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">Prof:
                                        {{ $constat->veh_b_conducteur_profession ?? '—' }} | Dom:
                                        {{ $constat->veh_b_conducteur_domicile ?? '—' }}</p>
                                </div>
                            </div>
                            <hr class="border-slate-50">
                            <div class="space-y-3">
                                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest">Permis & Assurance
                                </p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Permis N°
                                        </p>
                                        <p class="text-xs font-semibold text-slate-700">
                                            {{ $constat->veh_b_permis_numero ?? '—' }}
                                            ({{ $constat->veh_b_permis_categories ?? '—' }})</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Valable
                                            jusqu'au</p>
                                        <p class="text-xs font-semibold text-slate-700">
                                            {{ $constat->veh_b_permis_validite ? $constat->veh_b_permis_validite->format('d/m/Y') : '—' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Assurance &
                                        Police</p>
                                    <p class="text-xs font-semibold text-slate-700">
                                        {{ $constat->veh_b_assurance_nom ?? '—' }}</p>
                                    <p class="text-[10px] text-slate-500">Police: {{ $constat->veh_b_police_numero ?? '—' }}
                                        | Attest: {{ $constat->veh_b_attestation_numero ?? '—' }}</p>
                                </div>
                            </div>
                            @if($constat->veh_b_degats_materiels)
                                <div class="pt-2">
                                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Dégâts apparents</p>
                                    <p
                                        class="text-xs text-slate-600 bg-rose-50 p-2 rounded-lg border border-rose-100 mt-1 italic">
                                        {{ $constat->veh_b_degats_materiels }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Victime --}}
                @if($constat->victime_nom)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-800 text-white flex items-center gap-3">
                            <i class="fa-solid fa-person-falling-burst text-xs"></i>
                            <h3 class="font-bold text-sm">VICTIME IDENTIFIÉE</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Identité</p>
                                <p class="text-sm font-bold text-slate-800">{{ $constat->victime_nom }}</p>
                                <p class="text-xs text-slate-500">Né(e) le
                                    {{ $constat->victime_date_naissance ? $constat->victime_date_naissance->format('d/m/Y') : '—' }}
                                    à {{ $constat->victime_lieu_naissance ?? '—' }}</p>
                                <p class="text-xs text-slate-500">Nationalité: {{ $constat->victime_nationalite ?? '—' }}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Filiation & Profession
                                </p>
                                <p class="text-xs text-slate-500">Fils/Fille de {{ $constat->victime_pere ?? '—' }} et
                                    {{ $constat->victime_mere ?? '—' }}</p>
                                <p class="text-xs text-slate-500">Prof: {{ $constat->victime_profession ?? '—' }}</p>
                                <p class="text-xs text-slate-500">Dom: {{ $constat->victime_domicile ?? '—' }}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Blessures & Situation
                                </p>
                                <p class="text-xs text-slate-700 font-medium">{{ $constat->victime_blessures ?? 'Non précisé' }}
                                </p>
                                <p class="text-[10px] text-slate-500 mt-2 font-bold uppercase">Situation:
                                    {{ $constat->victime_passager_vehicule ? str_replace('_', ' ', $constat->victime_passager_vehicule) : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Corps du constat --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-align-left text-blue-600 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Rapport rédigé</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Description des faits
                            </p>
                            <div
                                class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                                {{ $constat->description_faits }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-rose-500">Dommages
                                et Dégâts</p>
                            <div
                                class="text-sm text-slate-700 font-medium bg-rose-50/30 p-4 rounded-xl border border-rose-100 whitespace-pre-line">
                                {{ $constat->dommages }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Témoins / Impliqués
                                </p>
                                <p
                                    class="text-sm text-slate-600 bg-slate-50/50 p-3 rounded-lg border border-slate-100 min-h-[50px]">
                                    {{ $constat->temoins ?? 'Aucun témoin' }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vos Observations
                                </p>
                                <p
                                    class="text-sm text-slate-600 bg-slate-50/50 p-3 rounded-lg border border-slate-100 min-h-[50px]">
                                    {{ $constat->observations ?? 'Aucune observation' }}
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
                            <h3 class="font-bold text-slate-800 text-sm">Croquis réalisé</h3>
                        </div>
                        <div class="p-6 bg-slate-50/50 flex justify-center">
                            @if(Str::startsWith($constat->croquis, 'data:image'))
                                <img src="{{ $constat->croquis }}"
                                    class="max-h-96 object-contain rounded-xl shadow-sm border border-slate-200 cursor-zoom-in"
                                    onclick="openModal(this.src)">
                            @else
                                <img src="{{ Storage::url($constat->croquis) }}"
                                    class="max-h-96 object-contain rounded-xl shadow-sm border border-slate-200 cursor-zoom-in"
                                    onclick="openModal(this.src)">
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Informations sur le Fonctionnaire Constatateur --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-user-tie text-blue-600 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Fonctionnaire Constatateur</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nom et Prénoms</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $constat->agent_nom ?? '—' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Grade</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $constat->agent_grade ?? '—' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Matricule</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $constat->agent_matricule ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne Droite : Docs --}}
            <div class="space-y-6">

                {{-- Photos d'assurance --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved text-slate-400 text-xs"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Pièces d'assurance</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        @if($constat->ass1_photo)
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Partie A</p>
                                <img src="{{ Storage::url($constat->ass1_photo) }}"
                                    class="w-full h-32 object-cover rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity"
                                    onclick="openModal(this.src)">
                            </div>
                        @endif
                        @if($constat->ass2_photo)
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Partie B</p>
                                <img src="{{ Storage::url($constat->ass2_photo) }}"
                                    class="w-full h-32 object-cover rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity"
                                    onclick="openModal(this.src)">
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
                            <h3 class="font-bold text-slate-800 text-sm">Photos jointes ({{ count($constat->photos_plus) }})
                            </h3>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-2">
                            @foreach($constat->photos_plus as $photo)
                                <img src="{{ Storage::url($photo) }}"
                                    class="w-full h-24 object-cover rounded-lg border border-slate-200 cursor-pointer hover:scale-105 transition-transform"
                                    onclick="openModal(this.src)">
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