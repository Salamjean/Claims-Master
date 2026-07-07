@extends('gendarmerie.layouts.template')
@section('title', 'Faire le constat')
@section('page-title', 'Constat')

@section('content')
    <div class="mx-auto space-y-5" style="max-width:1800px;">

        {{-- En-tête --}}
        <div class="flex items-center gap-4 flex-wrap">
            <a href="{{ route('gendarmerie.sinistres.en_attente') }}"
                class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">
                    {{ $isAccident ? "Constat d'accident" : "Constat d'incident" }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Sinistre #{{ $sinistre->id }} &mdash;
                    <span class="font-semibold text-slate-700">{{ $sinistre->assure->name ?? '' }}
                        {{ $sinistre->assure->prenom ?? '' }}</span>
                    &mdash; {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                </p>
            </div>
        </div>

        <form action="{{ route('gendarmerie.sinistres.constat.store', $sinistre->id) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <i class="fa-solid fa-file-signature text-blue-600"></i>
                    <h3 class="font-bold text-slate-800">Informations générales</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Lieu des faits</label>
                            <input type="text" name="lieu" class="input" placeholder="Lieu précis de l'incident..." required>
                        </div>
                        <div>
                            <label class="label">Date et Heure</label>
                            <input type="datetime-local" name="date_heure" class="input" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Véhicule A --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-blue-600 text-white flex items-center gap-3">
                        <i class="fa-solid fa-car"></i>
                        <h3 class="font-bold">1° - VÉHICULE A</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Marque</label>
                                <input type="text" name="veh_a_marque" class="input" placeholder="Ex: SUZUKI">
                            </div>
                            <div>
                                <label class="label">Type</label>
                                <input type="text" name="veh_a_type" class="input" placeholder="Ex: RFL61 (TPV)">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Etat général</label>
                                <select name="veh_a_etat_general" class="input">
                                    <option value="">Sélectionner...</option>
                                    <option value="neuf">Neuf</option>
                                    <option value="tres_bon">Très bon</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="passable">Passable</option>
                                    <option value="mediocre">Médiocre</option>
                                    <option value="mauvais">Mauvais</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Pneumatiques</label>
                                <select name="veh_a_pneumatiques" class="input">
                                    <option value="">Sélectionner...</option>
                                    <option value="gravures_apparentes">Gravures très apparentes</option>
                                    <option value="etat_moyen">État moyen</option>
                                    <option value="pneus_lisses">Pneus lisses</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest">Conducteur</h4>
                            <div>
                                <label class="label">Nom et Prénoms</label>
                                <input type="text" name="veh_a_conducteur_nom" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Né(e) le</label>
                                    <input type="date" name="veh_a_conducteur_date_naissance" class="input">
                                </div>
                                <div>
                                    <label class="label">À (Lieu)</label>
                                    <input type="text" name="veh_a_conducteur_lieu_naissance" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Fils/Fille de (Père)</label>
                                    <input type="text" name="veh_a_conducteur_pere" class="input">
                                </div>
                                <div>
                                    <label class="label">Et de (Mère)</label>
                                    <input type="text" name="veh_a_conducteur_mere" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Nationalité</label>
                                    <input type="text" name="veh_a_conducteur_nationalite" class="input">
                                </div>
                                <div>
                                    <label class="label">Tél</label>
                                    <input type="text" name="veh_a_conducteur_tel" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Profession</label>
                                    <input type="text" name="veh_a_conducteur_profession" class="input">
                                </div>
                                <div>
                                    <label class="label">Domicile</label>
                                    <input type="text" name="veh_a_conducteur_domicile" class="input">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest">Permis de conduire</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">N° de permis</label>
                                    <input type="text" name="veh_a_permis_numero" class="input">
                                </div>
                                <div>
                                    <label class="label">Délivré le</label>
                                    <input type="date" name="veh_a_permis_date" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">À (Lieu)</label>
                                    <input type="text" name="veh_a_permis_lieu" class="input">
                                </div>
                                <div>
                                    <label class="label">Catégories</label>
                                    <input type="text" name="veh_a_permis_categories" class="input" placeholder="Ex: ABCDE">
                                </div>
                            </div>
                            <div>
                                <label class="label">Valable jusqu'au</label>
                                <input type="date" name="veh_a_permis_validite" class="input">
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest">Propriétaire & Assurance</h4>
                            <div>
                                <label class="label">Propriétaire (Nom/Société)</label>
                                <input type="text" name="veh_a_proprietaire_nom" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Boîte Postale</label>
                                    <input type="text" name="veh_a_proprietaire_bp" class="input">
                                </div>
                                <div>
                                    <label class="label">Tél</label>
                                    <input type="text" name="veh_a_proprietaire_tel" class="input">
                                </div>
                            </div>
                            <div>
                                <label class="label">Assurance (Cie)</label>
                                <input type="text" name="veh_a_assurance_nom" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Valable du</label>
                                    <input type="date" name="veh_a_assurance_debut" class="input">
                                </div>
                                <div>
                                    <label class="label">Au</label>
                                    <input type="date" name="veh_a_assurance_fin" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Police N°</label>
                                    <input type="text" name="veh_a_police_numero" class="input">
                                </div>
                                <div>
                                    <label class="label">Attestation N°</label>
                                    <input type="text" name="veh_a_attestation_numero" class="input">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest">Dégâts</h4>
                            <div>
                                <label class="label">Dégâts apparents</label>
                                <textarea name="veh_a_degats_materiels" rows="2" class="input" placeholder="Ex: Pare chocs avant, aile droite..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Véhicule B --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-rose-600 text-white flex items-center gap-3">
                        <i class="fa-solid fa-car"></i>
                        <h3 class="font-bold">2° - VÉHICULE B</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Marque</label>
                                <input type="text" name="veh_b_marque" class="input" placeholder="Ex: TOYOTA">
                            </div>
                            <div>
                                <label class="label">Type</label>
                                <input type="text" name="veh_b_type" class="input" placeholder="Ex: EZ28EX (VP)">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Etat général</label>
                                <select name="veh_b_etat_general" class="input">
                                    <option value="">Sélectionner...</option>
                                    <option value="neuf">Neuf</option>
                                    <option value="tres_bon">Très bon</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="passable">Passable</option>
                                    <option value="mediocre">Médiocre</option>
                                    <option value="mauvais">Mauvais</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Pneumatiques</label>
                                <select name="veh_b_pneumatiques" class="input">
                                    <option value="">Sélectionner...</option>
                                    <option value="gravures_apparentes">Gravures très apparentes</option>
                                    <option value="etat_moyen">État moyen</option>
                                    <option value="pneus_lisses">Pneus lisses</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-rose-600 uppercase tracking-widest">Conducteur</h4>
                            <div>
                                <label class="label">Nom et Prénoms</label>
                                <input type="text" name="veh_b_conducteur_nom" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label">Né(e) le</label>
                                    <input type="date" name="veh_b_conducteur_date_naissance" class="input">
                                </div>
                                <div class="space-y-2">
                                    <label class="label">À (Lieu)</label>
                                    <input type="text" name="veh_b_conducteur_lieu_naissance" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label">Fils/Fille de (Père)</label>
                                    <input type="text" name="veh_b_conducteur_pere" class="input">
                                </div>
                                <div class="space-y-2">
                                    <label class="label">Et de (Mère)</label>
                                    <input type="text" name="veh_b_conducteur_mere" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label">Nationalité</label>
                                    <input type="text" name="veh_b_conducteur_nationalite" class="input">
                                </div>
                                <div class="space-y-2">
                                    <label class="label">Tél</label>
                                    <input type="text" name="veh_b_conducteur_tel" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="label">Profession</label>
                                    <input type="text" name="veh_b_conducteur_profession" class="input">
                                </div>
                                <div class="space-y-2">
                                    <label class="label">Domicile</label>
                                    <input type="text" name="veh_b_conducteur_domicile" class="input">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-rose-600 uppercase tracking-widest">Permis de conduire</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">N° de permis</label>
                                    <input type="text" name="veh_b_permis_numero" class="input">
                                </div>
                                <div>
                                    <label class="label">Délivré le</label>
                                    <input type="date" name="veh_b_permis_date" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">À (Lieu)</label>
                                    <input type="text" name="veh_b_permis_lieu" class="input">
                                </div>
                                <div>
                                    <label class="label">Catégories</label>
                                    <input type="text" name="veh_b_permis_categories" class="input" placeholder="Ex: B">
                                </div>
                            </div>
                            <div>
                                <label class="label">Valable jusqu'au</label>
                                <input type="date" name="veh_b_permis_validite" class="input">
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-rose-600 uppercase tracking-widest">Propriétaire & Assurance</h4>
                            <div>
                                <label class="label">Propriétaire (Nom/Société)</label>
                                <input type="text" name="veh_b_proprietaire_nom" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Boîte Postale</label>
                                    <input type="text" name="veh_b_proprietaire_bp" class="input">
                                </div>
                                <div>
                                    <label class="label">Tél</label>
                                    <input type="text" name="veh_b_proprietaire_tel" class="input">
                                </div>
                            </div>
                            <div>
                                <label class="label">Assurance (Cie)</label>
                                <input type="text" name="veh_b_assurance_nom" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Valable du</label>
                                    <input type="date" name="veh_b_assurance_debut" class="input">
                                </div>
                                <div>
                                    <label class="label">Au</label>
                                    <input type="date" name="veh_b_assurance_fin" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Police N°</label>
                                    <input type="text" name="veh_b_police_numero" class="input">
                                </div>
                                <div>
                                    <label class="label">Attestation N°</label>
                                    <input type="text" name="veh_b_attestation_numero" class="input">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-50">
                            <h4 class="text-xs font-black text-rose-600 uppercase tracking-widest">Dégâts</h4>
                            <div>
                                <label class="label">Dégâts apparents</label>
                                <textarea name="veh_b_degats_materiels" rows="2" class="input" placeholder="Ex: Pare chocs avant, aile gauche..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Victime --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-800 text-white flex items-center gap-3">
                    <i class="fa-solid fa-person-falling-burst"></i>
                    <h3 class="font-bold text-sm">VICTIME (S'il y a lieu)</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="label">Nom et Prénoms</label>
                            <input type="text" name="victime_nom" class="input">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="label">Né(e) le</label>
                                <input type="date" name="victime_date_naissance" class="input">
                            </div>
                            <div class="space-y-2">
                                <label class="label">À (Lieu)</label>
                                <input type="text" name="victime_lieu_naissance" class="input">
                            </div>
                        </div>
                        <div>
                            <label class="label">Nationalité</label>
                            <input type="text" name="victime_nationalite" class="input">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="label">Fils/Fille de</label>
                                <input type="text" name="victime_pere" class="input">
                            </div>
                            <div class="space-y-2">
                                <label class="label">Et de</label>
                                <input type="text" name="victime_mere" class="input">
                            </div>
                        </div>
                        <div>
                            <label class="label">Profession</label>
                            <input type="text" name="victime_profession" class="input">
                        </div>
                        <div>
                            <label class="label">Domicile</label>
                            <input type="text" name="victime_domicile" class="input">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="label">Nature des blessures</label>
                            <textarea name="victime_blessures" rows="2" class="input" placeholder="Décrivez les blessures..."></textarea>
                        </div>
                        <div>
                            <label class="label">Passager du véhicule n°</label>
                            <select name="victime_passager_vehicule" class="input">
                                <option value="">Non précisé</option>
                                <option value="vehicule_a">Véhicule A</option>
                                <option value="vehicule_b">Véhicule B</option>
                                <option value="non_passager">Non passager (Piéton, etc.)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Description & Témoins --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-align-left text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Nature des faits & Témoins</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Description / Nature des faits</label>
                            <textarea name="description_faits" rows="4" class="input" placeholder="Décrivez les faits constatés..." required></textarea>
                        </div>
                        <div>
                            <label class="label">Témoins (Nom, nationalité, parents, profession, domicile, tél)</label>
                            <textarea name="temoins" rows="4" class="input" placeholder="Informations complètes sur les témoins..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Dommages & Observations --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-eye text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Dommages & Mesures prises</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Dommages / Dégâts constatés (Général)</label>
                            <textarea name="dommages" rows="4" class="input" placeholder="Inventaire des dommages..." required></textarea>
                        </div>
                        <div>
                            <label class="label">Observations / Mesures prises</label>
                            <textarea name="observations" rows="4" class="input" placeholder="Mesures de sécurité, verbalisations, etc..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Croquis --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-pen-nib text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Croquis (Dessiner ou Photo)</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="space-y-4">
                            <canvas id="croquis-canvas" width="800" height="400" 
                                class="border-2 border-dashed border-slate-200 rounded-xl w-full bg-slate-50 cursor-crosshair"></canvas>
                            <input type="hidden" name="croquis_data" id="croquis-data">
                            
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="w-full sm:w-1/2">
                                    <label class="label text-[10px]">Ou importer une photo du croquis</label>
                                    <input type="file" name="croquis_file" class="input text-xs" accept="image/*">
                                </div>
                                <button type="button" onclick="clearCanvas()" class="text-xs text-rose-600 font-bold hover:underline">Effacer le dessin</button>
                            </div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-tight text-center sm:text-left">Priorité à la photo si les deux sont fournis</p>
                        </div>
                    </div>
                </div>

                {{-- Pièces jointes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-camera text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Photos & Annexes</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Assurance Partie A</label>
                                <input type="file" name="ass1_photo" class="input text-xs" accept="image/*">
                            </div>
                            <div>
                                <label class="label">Assurance Partie B</label>
                                <input type="file" name="ass2_photo" class="input text-xs" accept="image/*">
                            </div>
                        </div>
                        <hr class="border-slate-100">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="label">Autres justificatifs / Photos du lieu</label>
                                <button type="button" onclick="addPhotoField()" 
                                    class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition-colors">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                            <div id="photos-container" class="space-y-2">
                                <input type="file" name="photos_plus[]" class="input text-xs" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fonctionnaire Constatateur --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <i class="fa-solid fa-user-tie text-blue-600"></i>
                    <h3 class="font-bold text-slate-800">Fonctionnaire Constatateur</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="label">Nom et Prénoms</label>
                        <input type="text" name="agent_nom" class="input" value="{{ auth('user')->user()->name }} {{ auth('user')->user()->prenom }}" required>
                    </div>
                    <div>
                        <label class="label">Grade</label>
                        <input type="text" name="agent_grade" class="input" value="{{ auth('user')->user()->grade }}" placeholder="Votre grade...">
                    </div>
                    <div>
                        <label class="label">Matricule</label>
                        <input type="text" name="agent_matricule" class="input" value="{{ auth('user')->user()->matricule }}" placeholder="Votre matricule...">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pb-8">
                <a href="{{ route('gendarmerie.sinistres.en_attente') }}" 
                    class="px-6 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Annuler</a>
                <button type="submit" 
                    class="px-10 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-extrabold transition-all shadow-lg active:scale-95">
                    Clôturer le constat (Gendarmerie)
                </button>
            </div>
        </form>
    </div>

    <style>
        .label { display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .05em; }
        .input { display: block; width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 13px; color: #1e293b; background: #fff; outline: none; transition: all .2s; }
        .input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, .1); }
    </style>

    <script>
        function addPhotoField() {
            const container = document.getElementById('photos-container');
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 mt-2';
            div.innerHTML = `
                <input type="file" name="photos_plus[]" class="input text-xs flex-1" accept="image/*">
                <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-100 transition-colors">
                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                </button>
            `;
            container.appendChild(div);
        }

        const canvas = document.getElementById('croquis-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let drawing = false, lastX = 0, lastY = 0;
            ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 3; ctx.lineCap = 'round';

            function getPos(e) {
                const r = canvas.getBoundingClientRect(), src = e.touches ? e.touches[0] : e;
                return { x: (src.clientX - r.left) * canvas.width / r.width, y: (src.clientY - r.top) * canvas.height / r.height };
            }
            
            canvas.addEventListener('mousedown', e => { drawing = true; const p = getPos(e); lastX = p.x; lastY = p.y; });
            canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = getPos(e); ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y); ctx.stroke(); lastX = p.x; lastY = p.y; });
            canvas.addEventListener('mouseup', () => { drawing = false; save(); });
            canvas.addEventListener('mouseleave', () => drawing = false);

            // Touch events
            canvas.addEventListener('touchstart', e => { e.preventDefault(); const p = getPos(e); lastX = p.x; lastY = p.y; drawing = true; }, { passive: false });
            canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y); ctx.stroke(); lastX = p.x; lastY = p.y; }, { passive: false });
            canvas.addEventListener('touchend', () => { drawing = false; save(); });

            function save() { 
                const dataInput = document.getElementById('croquis-data');
                if (dataInput) dataInput.value = canvas.toDataURL(); 
            }
        }

        function clearCanvas() { 
            const canvas = document.getElementById('croquis-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height); 
                document.getElementById('croquis-data').value = ''; 
            }
        }
    </script>
@endsection