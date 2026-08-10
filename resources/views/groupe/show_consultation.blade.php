@extends('groupe.layouts.app')

@section('title', 'Consultation Sinistre ' . ($sinistre->numero_sinistre ?? $sinistre->reference ?? '#' . $sinistre->id))
@section('page-title', 'Consultation du dossier')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- En-tête / Barre d'actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('groupe.historique') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dossier n° {{ $sinistre->numero_sinistre ?? $sinistre->reference ?? ('#' . $sinistre->id) }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Clôturé le {{ $sinistre->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($etatDesLieux && $etatDesLieux->status === 'valide')
                {{-- Rapport validé : bouton Consulter avec cadenas --}}
                <a href="{{ route('groupe.sinistres.etat_des_lieux', $sinistre) }}" class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-extrabold rounded-xl transition-colors inline-flex items-center gap-2 shadow-sm border border-emerald-300">
                    <i class="fa-solid fa-lock text-emerald-600"></i> Consulter l'état des lieux (Verrouillé)
                </a>
            @else
                {{-- Rapport modifiable --}}
                <a href="{{ route('groupe.sinistres.etat_des_lieux', $sinistre) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-colors inline-flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-pen-to-square"></i> Modifier l'état des lieux
                </a>
            @endif

            @if($etatDesLieux)
                <a href="{{ route('groupe.sinistres.etat_des_lieux.pdf', $sinistre) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors inline-flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-pdf"></i> Télécharger PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Rapport 12 Sections -->
    @if($etatDesLieux)
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h2 class="font-bold text-slate-800 text-lg">Rapport Officiel d'État des Lieux</h2>
                </div>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-200">
                    Clôturé & Enregistré
                </span>
            </div>

            <!-- 1. Informations générales -->
            <div class="space-y-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                    <i class="fa-solid fa-info-circle"></i> 1. Informations Générales
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div><span class="text-slate-400 block">N° Intervention</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->numero_intervention ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block">Alerte</span><span class="font-semibold text-slate-700">{{ optional($etatDesLieux->date_heure_alerte)->format('d/m/Y H:i') ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block">Départ caserne</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->heure_depart_caserne ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block">Arrivée lieux</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->heure_arrivee_lieux ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block">Fin intervention</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->heure_fin_intervention ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block">Lieu exact</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->lieu_exact ?? $sinistre->lieu ?? '—' }}</span></div>
                </div>
            </div>

            <!-- 2 & 3. Nature & Sinistre -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-fire text-amber-500"></i> 2. Nature de l'Intervention
                    </h3>
                    <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100 text-xs font-bold text-amber-800">
                        {{ $etatDesLieux->nature_intervention ?? 'Non précisée' }}
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-triangle-exclamation"></i> 3. Gravité & Risques
                    </h3>
                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div><span class="text-slate-400 block">Gravité</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->niveau_gravite ?? '—' }}</span></div>
                        <div><span class="text-slate-400 block">Météo</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->conditions_meteo ?? '—' }}</span></div>
                        <div class="col-span-2"><span class="text-slate-400 block">Cause présumée</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->cause_presumee ?? '—' }}</span></div>
                    </div>
                </div>
            </div>

            @if($etatDesLieux->description_situation)
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs space-y-1">
                    <span class="text-slate-400 font-bold uppercase">Description de la situation</span>
                    <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $etatDesLieux->description_situation }}</p>
                </div>
            @endif

            <!-- 4. Victimes -->
            <div class="space-y-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                    <i class="fa-solid fa-user-injured"></i> 4. Victimes ({{ is_array($etatDesLieux->victimes) ? count($etatDesLieux->victimes) : 0 }})
                </h3>
                @if(is_array($etatDesLieux->victimes) && count($etatDesLieux->victimes) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($etatDesLieux->victimes as $idx => $v)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1">
                                <div class="flex justify-between font-bold text-slate-800 border-b border-slate-200 pb-1">
                                    <span>Victime #{{ $idx + 1 }} : {{ $v['nom'] ?? 'Anonyme' }}</span>
                                    <span class="text-rose-600">{{ $v['decedee'] === 'Oui' ? 'Décédée' : 'Vivante' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <div><span class="text-slate-400">Sexe / Âge:</span> {{ $v['sexe'] ?? '—' }} / {{ $v['age'] ?? '—' }}</div>
                                    <div><span class="text-slate-400">Conscience:</span> {{ $v['niveau_conscience'] ?? '—' }}</div>
                                    <div class="col-span-2"><span class="text-slate-400">Blessures:</span> {{ $v['blessures'] ?? '—' }}</div>
                                    <div class="col-span-2"><span class="text-slate-400">Évacuation:</span> {{ $v['evacuation_hopital'] ?? 'Non évacué' }} ({{ $v['moyen_transport'] ?? '—' }})</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-slate-50 text-slate-400 text-xs rounded-xl italic">Aucune victime répertoriée.</div>
                @endif
            </div>

            <!-- 5. Véhicules impliqués -->
            <div class="space-y-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                    <i class="fa-solid fa-car-burst"></i> 5. Véhicules Impliqués ({{ is_array($etatDesLieux->vehicules_impliques) ? count($etatDesLieux->vehicules_impliques) : 0 }})
                </h3>
                @if(is_array($etatDesLieux->vehicules_impliques) && count($etatDesLieux->vehicules_impliques) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($etatDesLieux->vehicules_impliques as $idx => $veh)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1">
                                <div class="flex justify-between font-bold text-slate-800 border-b border-slate-200 pb-1">
                                    <span>Véhicule #{{ $idx + 1 }} : {{ $veh['marque'] ?? '' }} ({{ $veh['immatriculation'] ?? 'Sans plaque' }})</span>
                                    <span class="text-slate-500">{{ $veh['type_vehicule'] ?? '' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <div><span class="text-slate-400">Couleur:</span> {{ $veh['couleur'] ?? '—' }}</div>
                                    <div><span class="text-slate-400">Passagers:</span> {{ $veh['nombre_passagers'] ?? '0' }}</div>
                                    <div><span class="text-slate-400">Conducteur:</span> {{ $veh['conducteur_identifie'] ?? '—' }}</div>
                                    <div><span class="text-slate-400">État:</span> {{ $veh['etat_vehicule'] ?? '—' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-slate-50 text-slate-400 text-xs rounded-xl italic">Aucun véhicule répertorié.</div>
                @endif
            </div>

            <!-- 6 & 7. Dégâts matériels & Moyens engagés -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-house-crack"></i> 6. Dégâts Matériels
                    </h3>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-2">
                        <div><span class="text-slate-400 block font-bold">Biens endommagés:</span> {{ $etatDesLieux->biens_endommages ?? '—' }}</div>
                        <div><span class="text-slate-400 block font-bold">Bâtiments touchés:</span> {{ $etatDesLieux->batiments_touches ?? '—' }}</div>
                        <div><span class="text-slate-400 block font-bold">Surface brûlée:</span> {{ $etatDesLieux->surface_brulee ?? '—' }}</div>
                        <div><span class="text-slate-400 block font-bold">Biens sauvés:</span> {{ $etatDesLieux->biens_sauves ?? '—' }}</div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-truck-medical"></i> 7. Moyens Engagés
                    </h3>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-2">
                        <div><span class="text-slate-400 block font-bold">Casernes:</span> {{ $etatDesLieux->casernes_mobilisees ?? '—' }}</div>
                        <div><span class="text-slate-400 block font-bold">Effectifs pompiers:</span> {{ $etatDesLieux->nombre_pompiers ?? '—' }}</div>
                        <div><span class="text-slate-400 block font-bold">Quantité d'eau / extincteurs:</span> {{ $etatDesLieux->quantite_eau_utilisee ?? '—' }} / {{ $etatDesLieux->produits_extincteurs_utilises ?? '—' }}</div>
                        <div><span class="text-slate-400 block font-bold">Matériel spécialisé:</span> {{ $etatDesLieux->materiel_utilise ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <!-- 8 & 9. Actions & Autorités -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-list-check"></i> 8. Actions Réalisées
                    </h3>
                    <div class="flex flex-wrap gap-1.5">
                        @if(is_array($etatDesLieux->actions_realisees) && count($etatDesLieux->actions_realisees) > 0)
                            @foreach($etatDesLieux->actions_realisees as $act)
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-lg">
                                    {{ $act }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-xs text-slate-400 italic">Aucune action spécifique</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-building-shield"></i> 9. Autorités Présentes
                    </h3>
                    <div class="flex flex-wrap gap-1.5">
                        @if(is_array($etatDesLieux->autorites_presentes) && count($etatDesLieux->autorites_presentes) > 0)
                            @foreach($etatDesLieux->autorites_presentes as $aut)
                                <span class="px-2.5 py-1 bg-sky-50 text-sky-700 border border-sky-200 text-xs font-semibold rounded-lg">
                                    {{ $aut }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-xs text-slate-400 italic">Aucune autorité répertoriée</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 10 & 11. Témoins & Chronologie -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-users"></i> 10. Témoins
                    </h3>
                    @if(is_array($etatDesLieux->temoins) && count($etatDesLieux->temoins) > 0)
                        @foreach($etatDesLieux->temoins as $t)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                                <span class="font-bold text-slate-800">{{ $t['nom'] ?? 'Témoin' }}</span> - {{ $t['contact'] ?? 'Pas de contact' }}
                                <p class="text-slate-600 mt-1 italic">"{{ $t['declaration'] ?? '' }}"</p>
                            </div>
                        @endforeach
                    @else
                        <div class="text-xs text-slate-400 italic">Aucun témoin renseigné.</div>
                    @endif
                </div>

                <div class="space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                        <i class="fa-solid fa-clock-rotate-left"></i> 11. Chronologie
                    </h3>
                    @if(is_array($etatDesLieux->chronologie) && count($etatDesLieux->chronologie) > 0)
                        <div class="space-y-2">
                            @foreach($etatDesLieux->chronologie as $c)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs flex gap-3 items-center">
                                    <span class="px-2 py-1 bg-slate-200 font-bold rounded-lg text-slate-700">{{ $c['heure'] ?? '--:--' }}</span>
                                    <div>
                                        <span class="font-bold text-slate-800">{{ $c['evenement'] ?? '' }}</span>
                                        <span class="text-slate-500 block">{{ $c['description'] ?? '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs text-slate-400 italic">Aucune étape renseignée.</div>
                    @endif
                </div>
            </div>

            <!-- 12. Conclusion -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 text-rose-700">
                    <i class="fa-solid fa-flag-checkered"></i> 12. Conclusion
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div><span class="text-slate-400 block font-bold">Situation maîtrisée ?</span><span class="font-bold text-slate-800">{{ $etatDesLieux->situation_maitrisee ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block font-bold">Cause probable:</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->cause_probable ?? '—' }}</span></div>
                    <div><span class="text-slate-400 block font-bold">Suites à donner:</span><span class="font-semibold text-slate-700">{{ $etatDesLieux->suites_a_donner ?? '—' }}</span></div>
                    @if($etatDesLieux->recommandations)
                        <div class="md:col-span-3"><span class="text-slate-400 block font-bold">Recommandations:</span><p class="text-slate-700 whitespace-pre-line">{{ $etatDesLieux->recommandations }}</p></div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-white p-12 rounded-3xl border border-slate-100 text-center space-y-4 shadow-sm">
            <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto text-rose-500">
                <i class="fa-solid fa-file-circle-xmark text-3xl"></i>
            </div>
            <div>
                <h3 class="text-slate-800 font-bold text-lg">Aucun état des lieux rédigé</h3>
                <p class="text-slate-500 text-xs mt-1">L'état des lieux n'a pas encore été renseigné pour cette intervention.</p>
            </div>
            <a href="{{ route('groupe.sinistres.etat_des_lieux', $sinistre) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-md">
                <i class="fa-solid fa-pen-to-square"></i> Remplir l'état des lieux maintenant
            </a>
        </div>
    @endif
</div>
@endsection
