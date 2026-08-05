<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'État des Lieux — {{ $sinistre->numero_sinistre ?? $sinistre->reference ?? ('#' . $sinistre->id) }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* Filigrane discret */
        .watermark {
            position: fixed;
            top: 32%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 34px;
            font-weight: 900;
            color: #f1f5f9;
            transform: rotate(-30deg);
            text-transform: uppercase;
            letter-spacing: 5px;
            z-index: -1000;
            line-height: 1.5;
        }

        /* Header Principal */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #be123c;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 17px;
            font-weight: 800;
            color: #881337;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
            font-weight: 600;
        }
        .header-badge {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        /* Synthèse Métriques / KPI Bar */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-table td {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px;
            text-align: center;
        }
        .kpi-lbl {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kpi-val {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Bannières de Sections (Numérotées de 1 à 11) */
        .sec-banner {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        .sec-banner.red { background-color: #9f1239; }
        .sec-banner.purple { background-color: #6b21a8; }
        .sec-banner.indigo { background-color: #3730a3; }
        .sec-banner.orange { background-color: #c2410c; }
        .sec-banner.teal { background-color: #0f766e; }
        .sec-banner.slate { background-color: #334155; }

        /* Tableaux de données structurés */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }
        .grid-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
        }
        .grid-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            background-color: #ffffff;
            word-wrap: break-word;
        }
        .grid-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        /* Labels et Valeurs */
        .lbl {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .val {
            font-size: 9.5px;
            color: #0f172a;
            font-weight: 500;
        }
        .val-bold {
            font-size: 9.5px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Badges / Chips */
        .chip {
            display: inline-block;
            font-size: 8px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid transparent;
        }
        .chip-red { background-color: #ffe4e6; color: #9f1239; border-color: #fecdd3; }
        .chip-green { background-color: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .chip-blue { background-color: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .chip-amber { background-color: #fef3c7; color: #92400e; border-color: #fde68a; }
        .chip-slate { background-color: #f1f5f9; color: #334155; border-color: #cbd5e1; }

        /* Zone de Signature */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
        }
        .signature-box {
            border: 1px dashed #cbd5e1;
            background-color: #fafafa;
            border-radius: 4px;
            padding: 6px 8px;
            height: 46px;
        }

        .footer-line {
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Filigrane officiel -->
    <div class="watermark">
        RAPPORT OFFICIEL D'INTERVENTION<br>
        <span style="font-size: 22px; color: #cbd5e1;">SAPEURS-POMPIERS & SECOURS</span>
    </div>

    <!-- En-tête -->
    <table class="header-table">
        <tr>
            <td width="65%">
                <div class="header-title">Rapport d'Intervention</div>
                <div class="header-subtitle">DOCUMENT OFFICIEL D'INTERVENTION SAPEURS-POMPIERS</div>
            </td>
            <td width="35%" style="text-align: right;">
                <div class="header-badge">RÉF : {{ $sinistre->numero_sinistre ?? $sinistre->reference ?? ('#' . $sinistre->id) }}</div>
                <div style="font-size: 8px; color: #64748b; margin-top: 3px;">Date d'émission : {{ date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- Synthèse KPI -->
    <table class="kpi-table">
        <tr>
            <td width="25%">
                <div class="kpi-lbl">Nature de l'intervention</div>
                <div class="kpi-val" style="color: #9f1239;">{{ $etatDesLieux->nature_intervention ?? 'Secours' }}</div>
            </td>
            <td width="25%">
                <div class="kpi-lbl">Niveau de Gravité</div>
                <div class="kpi-val">
                    @php $g = $etatDesLieux->niveau_gravite ?? 'Faible'; @endphp
                    <span class="chip @if($g === 'Critique' || $g === 'Élevé') chip-red @elseif($g === 'Moyen') chip-amber @else chip-green @endif">
                        {{ $g }}
                    </span>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-lbl">Nombre de Victimes</div>
                <div class="kpi-val" style="color: #6b21a8;">
                    {{ is_array($etatDesLieux->victimes) ? count($etatDesLieux->victimes) : 0 }} personne(s)
                </div>
            </td>
            <td width="25%">
                <div class="kpi-lbl">Statut du Rapport</div>
                <div class="kpi-val">
                    <span class="chip chip-green">VALIDÉ & ARCHIVÉ</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 1 : INFORMATIONS GÉNÉRALES -->
    <div class="sec-banner red">1. Informations Générales</div>
    <table class="grid-table">
        <tr>
            <td width="25%"><span class="lbl">N° Intervention</span><div class="val-bold">{{ $etatDesLieux->numero_intervention ?? '—' }}</div></td>
            <td width="25%"><span class="lbl">Date & Heure Alerte</span><div class="val">{{ optional($etatDesLieux->date_heure_alerte)->format('d/m/Y H:i') ?? '—' }}</div></td>
            <td width="25%"><span class="lbl">Départ Caserne</span><div class="val">{{ $etatDesLieux->heure_depart_caserne ?? '—' }}</div></td>
            <td width="25%"><span class="lbl">Arrivée sur les Lieux</span><div class="val">{{ $etatDesLieux->heure_arrivee_lieux ?? '—' }}</div></td>
        </tr>
        <tr>
            <td><span class="lbl">Fin d'Intervention</span><div class="val-bold" style="color: #065f46;">{{ $etatDesLieux->heure_fin_intervention ?? '—' }}</div></td>
            <td colspan="3"><span class="lbl">Lieu Exact (Adresse / Coordonnées GPS)</span><div class="val-bold">{{ $etatDesLieux->lieu_exact ?? $sinistre->lieu ?? '—' }}</div></td>
        </tr>
    </table>

    <!-- SECTION 2 : INFORMATIONS SUR LE SINISTRE -->
    <div class="sec-banner red">2. Informations sur le Sinistre</div>
    <table class="grid-table">
        <tr>
            <td width="33%"><span class="lbl">Nature de l'Intervention</span><div class="val-bold">{{ $etatDesLieux->nature_intervention ?? 'Non précisée' }}</div></td>
            <td width="33%"><span class="lbl">Niveau de Gravité</span><div class="val">{{ $etatDesLieux->niveau_gravite ?? 'Faible' }}</div></td>
            <td width="34%"><span class="lbl">Conditions Météorologiques</span><div class="val">{{ $etatDesLieux->conditions_meteo ?? 'Serein' }}</div></td>
        </tr>
        <tr>
            <td colspan="3"><span class="lbl">Cause Présumée</span><div class="val">{{ $etatDesLieux->cause_presumee ?? 'Enquête en cours / Inconnue' }}</div></td>
        </tr>
        @if($etatDesLieux->description_situation)
        <tr>
            <td colspan="3">
                <span class="lbl">Description Globale de la Situation</span>
                <div class="val" style="white-space: pre-line;">{{ $etatDesLieux->description_situation }}</div>
            </td>
        </tr>
        @endif
    </table>

    <!-- SECTION 3 : VICTIMES -->
    <div class="sec-banner purple">3. Victimes & Bilan Humain</div>
    @if(is_array($etatDesLieux->victimes) && count($etatDesLieux->victimes) > 0)
        <table class="grid-table">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="22%">Nom & Prénom</th>
                    <th width="12%">Sexe / Âge</th>
                    <th width="14%">Conscience</th>
                    <th width="10%">Statut</th>
                    <th width="18%">Blessures Observées</th>
                    <th width="20%">Évacuation & Hôpital</th>
                </tr>
            </thead>
            <tbody>
                @foreach($etatDesLieux->victimes as $idx => $v)
                <tr>
                    <td><strong>{{ $idx + 1 }}</strong></td>
                    <td><div class="val-bold">{{ $v['nom'] ?: 'Victime Non Identifiée' }}</div></td>
                    <td>{{ $v['sexe'] ?? '—' }} {{ !empty($v['age']) ? '('.$v['age'].' ans)' : '' }}</td>
                    <td>{{ $v['niveau_conscience'] ?? '—' }}</td>
                    <td>
                        <span class="chip @if(($v['decedee'] ?? '') === 'Oui') chip-red @else chip-green @endif">
                            {{ ($v['decedee'] ?? '') === 'Oui' ? 'DÉCÈS' : 'Vivant' }}
                        </span>
                    </td>
                    <td>
                        @if(!empty($v['blessures']))
                            <span class="chip chip-red">{{ $v['blessures'] }}</span>
                        @else
                            <span style="color: #94a3b8;">Aucune blessure</span>
                        @endif
                    </td>
                    <td>
                        <div class="val-bold">{{ $v['evacuation_hopital'] ?? 'Sur place' }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">Transport : {{ $v['moyen_transport'] ?? 'Non évacué' }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="background-color: #fafafa; border: 1px solid #e2e8f0; padding: 6px 8px; border-radius: 4px; color: #64748b; font-style: italic; margin-bottom: 8px;">
            Aucune victime répertoriée lors de l'intervention.
        </div>
    @endif

    <!-- SECTION 4 : VÉHICULES IMPLIQUÉS -->
    <div class="sec-banner indigo">4. Véhicules Impliqués</div>
    @if(is_array($etatDesLieux->vehicules_impliques) && count($etatDesLieux->vehicules_impliques) > 0)
        <table class="grid-table">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="16%">Type</th>
                    <th width="18%">Marque & Couleur</th>
                    <th width="16%">Immatriculation</th>
                    <th width="18%">Conducteur</th>
                    <th width="10%">Passagers</th>
                    <th width="18%">État du Véhicule</th>
                </tr>
            </thead>
            <tbody>
                @foreach($etatDesLieux->vehicules_impliques as $idx => $veh)
                <tr>
                    <td><strong>{{ $idx + 1 }}</strong></td>
                    <td>{{ $veh['type_vehicule'] ?? 'Véhicule' }}</td>
                    <td>{{ $veh['marque'] ?? '—' }} ({{ $veh['couleur'] ?? '—' }})</td>
                    <td><span class="val-bold">{{ $veh['immatriculation'] ?? 'Inconnue' }}</span></td>
                    <td>{{ $veh['conducteur_identifie'] ?? 'Non identifié' }}</td>
                    <td>{{ $veh['nombre_passagers'] ?? '0' }}</td>
                    <td>{{ $veh['etat_vehicule'] ?? 'Accidenté' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="background-color: #fafafa; border: 1px solid #e2e8f0; padding: 6px 8px; border-radius: 4px; color: #64748b; font-style: italic; margin-bottom: 8px;">
            Aucun véhicule impliqué répertorié.
        </div>
    @endif

    <!-- SECTION 5 & 6 : DÉGÂTS MATÉRIELS & MOYENS ENGAGÉS -->
    <div class="sec-banner orange">5 & 6. Dégâts Matériels & Moyens Engagés</div>
    <table class="grid-table">
        <tr>
            <td width="50%">
                <span class="lbl">Biens Endommagés</span><div class="val">{{ $etatDesLieux->biens_endommages ?? '—' }}</div><br>
                <span class="lbl">Bâtiments Touchés</span><div class="val">{{ $etatDesLieux->batiments_touches ?? '—' }}</div><br>
                <span class="lbl">Surface Brûlée / Biens Sauvés</span>
                <div class="val">Surface : <strong>{{ $etatDesLieux->surface_brulee ?? 'N/A' }}</strong> | Sauvés : <strong>{{ $etatDesLieux->biens_sauves ?? 'N/A' }}</strong></div>
            </td>
            <td width="50%">
                <span class="lbl">Groupe d'Intervention Mobilisé</span><div class="val-bold" style="color: #c2410c;">{{ $etatDesLieux->casernes_mobilisees ?? $sinistre->assignedGroupe->name ?? 'Groupe Sapeurs-Pompiers' }}</div><br>
                <span class="lbl">Effectifs Pompiers / Extincteurs</span><div class="val">Pompiers : <strong>{{ $etatDesLieux->nombre_pompiers ?? '—' }}</strong> | Extincteurs : <strong>{{ $etatDesLieux->produits_extincteurs_utilises ?? '—' }}</strong></div><br>
                <span class="lbl">Matériel Spécialisé Utilisé & Quantités</span><div class="val-bold">{{ $etatDesLieux->materiel_utilise ?? 'Matériel standard' }}</div>
            </td>
        </tr>
    </table>

    <!-- SECTION 7 & 8 : ACTIONS RÉALISÉES & AUTORITÉS PRÉSENTES -->
    <div class="sec-banner teal">7 & 8. Actions Réalisées & Autorités Présentes</div>
    <table class="grid-table">
        <tr>
            <td width="50%">
                <span class="lbl">Actions & Opérations Effectuées</span>
                <div style="margin-top: 3px;">
                    @if(is_array($etatDesLieux->actions_realisees) && count($etatDesLieux->actions_realisees) > 0)
                        @foreach($etatDesLieux->actions_realisees as $act)
                            <span class="chip chip-green" style="margin-right: 2px; margin-bottom: 2px;">✓ {{ $act }}</span>
                        @endforeach
                    @else
                        <span class="val">Aucune action spécifique enregistrée</span>
                    @endif
                </div>
            </td>
            <td width="50%">
                <span class="lbl">Services & Autorités Présents</span>
                <div style="margin-top: 3px;">
                    @if(is_array($etatDesLieux->autorites_presentes) && count($etatDesLieux->autorites_presentes) > 0)
                        @foreach($etatDesLieux->autorites_presentes as $aut)
                            <span class="chip chip-blue" style="margin-right: 2px; margin-bottom: 2px;">🛡️ {{ $aut }}</span>
                        @endforeach
                    @else
                        <span class="val">Aucune autorité enregistrée</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 9, 10 & 11 : TÉMOINS, CHRONOLOGIE & CONCLUSION -->
    <div class="sec-banner slate">9, 10 & 11. Témoins, Chronologie & Bilan Final</div>
    <table class="grid-table">
        <tr>
            <td width="50%">
                <span class="lbl">Déclarations des Témoins</span>
                @if(is_array($etatDesLieux->temoins) && count($etatDesLieux->temoins) > 0)
                    @foreach($etatDesLieux->temoins as $t)
                        <div style="margin-top: 2px; border-bottom: 1px border-dashed #e2e8f0; padding-bottom: 2px;">
                            <strong>{{ $t['nom'] ?? 'Témoin' }}</strong> <span style="color: #64748b;">({{ $t['contact'] ?? 'N/A' }})</span> : {{ $t['declaration'] ?? '' }}
                        </div>
                    @endforeach
                @else
                    <div class="val" style="font-style: italic; color: #94a3b8;">Aucun témoin enregistré</div>
                @endif
            </td>
            <td width="50%">
                <span class="lbl">Chronologie des Actions</span>
                @if(is_array($etatDesLieux->chronologie) && count($etatDesLieux->chronologie) > 0)
                    @foreach($etatDesLieux->chronologie as $c)
                        <div style="margin-top: 2px;">
                            <span class="chip chip-slate">[{{ $c['heure'] ?? '--:--' }}]</span> <strong>{{ $c['evenement'] ?? '' }}</strong> - {{ $c['description'] ?? '' }}
                        </div>
                    @endforeach
                @else
                    <div class="val" style="font-style: italic; color: #94a3b8;">Aucune étape chronologique spécifiée</div>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="border: none; padding: 0 4px;" width="33%">
                            <span class="lbl">Situation Maîtrisée</span>
                            <span class="chip @if(($etatDesLieux->situation_maitrisee ?? '') === 'Oui') chip-green @else chip-amber @endif">
                                {{ ($etatDesLieux->situation_maitrisee ?? '') === 'Oui' ? 'OUI (MAÎTRISÉE)' : 'NON (EN COURS)' }}
                            </span>
                        </td>
                        <td style="border: none; padding: 0 4px;" width="33%">
                            <span class="lbl">Cause Probable Retenue</span>
                            <div class="val-bold">{{ $etatDesLieux->cause_probable ?? 'Enquête en cours' }}</div>
                        </td>
                        <td style="border: none; padding: 0 4px;" width="33%">
                            <span class="lbl">Suites à Donner</span>
                            <div class="val">{{ $etatDesLieux->suites_a_donner ?? 'Transmission du dossier' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td width="50%" style="border: none;">
                <span class="lbl">Authentification Officielle</span>
                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                    Validé électroniquement par le Commandant / Chef de Garde du <strong>{{ $etatDesLieux->casernes_mobilisees ?? $sinistre->assignedGroupe->name ?? 'Groupe Sapeurs-Pompiers' }}</strong>.
                </div>
            </td>
            <td width="50%" style="border: none;">
                <div class="signature-box">
                    <span class="lbl">Signature & Cachet du Chef d'Intervention</span>
                    <div style="text-align: right; margin-top: 14px; font-weight: 800; color: #881337; font-size: 8.5px;">
                        [ SIGNÉ ET VALIDÉ ÉLECTRONIQUEMENT ]
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-line">
        Claims Master — Document officiel d'État des Lieux généré le {{ date('d/m/Y H:i:s') }} | Réf: {{ $sinistre->numero_sinistre ?? $sinistre->reference ?? $sinistre->id }}
    </div>

</body>
</html>
