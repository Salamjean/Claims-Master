<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Constat Terrain - {{ $sinistre->numero_sinistre ?? 'SI-' . $sinistre->id }}</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
        }

        /* Header section with accent colors */
        .header {
            border-bottom: 3px solid #1e40af;
            padding-bottom: 12px;
            margin-bottom: 20px;
            position: relative;
        }

        .header-title {
            font-size: 22px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            color: #64748b;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 3px;
            letter-spacing: 1px;
        }

        .header-ref {
            position: absolute;
            right: 0;
            top: 0;
            text-align: right;
        }

        .header-ref-label {
            font-size: 8px;
            color: #94a3b8;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-ref-value {
            font-size: 16px;
            font-weight: 900;
            color: #dc2626;
        }

        /* Information Grid */
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .info-cell:first-child {
            border-radius: 8px 0 0 8px;
        }

        .info-cell:last-child {
            border-radius: 0 8px 8px 0;
        }

        .label-tiny {
            font-size: 7px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 900;
            display: block;
            margin-bottom: 2px;
        }

        .value-text {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Section Headings */
        .section-title {
            background: #1e293b;
            color: white;
            padding: 6px 12px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 10px;
            border-radius: 6px;
            margin: 15px 0 10px 0;
            letter-spacing: 0.5px;
        }

        /* Grid Layout for vehicles */
        .grid {
            width: 100%;
            clear: both;
        }

        .col-6 {
            float: left;
            width: 48.5%;
        }

        .col-right {
            float: right;
            width: 48.5%;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Cards for details */
        .card {
            border: 1.5px solid #f1f5f9;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            background: #ffffff;
        }

        .card-header {
            font-weight: 900;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 2px solid #f8fafc;
            padding-bottom: 5px;
        }

        .accent-a {
            color: #2563eb;
            border-color: #dbeafe;
        }

        .accent-b {
            color: #e11d48;
            border-color: #ffe4e6;
        }

        .bg-accent-a {
            background: #eff6ff;
        }

        .bg-accent-b {
            background: #fff1f2;
        }

        .data-group {
            margin-bottom: 8px;
        }

        .data-label {
            font-size: 7px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 1px;
        }

        .data-value {
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        .data-value.none {
            font-style: italic;
            color: #cbd5e1;
            font-weight: 500;
        }

        /* Sketch area */
        .sketch-box {
            border: 2px dashed #e2e8f0;
            border-radius: 15px;
            height: 280px;
            text-align: center;
            background: #fafafa;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        .sketch-image {
            max-width: 90%;
            max-height: 90%;
            margin-top: 2%;
            object-fit: contain;
        }

        .sketch-placeholder {
            padding-top: 130px;
            color: #94a3b8;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.5;
        }

        /* Footer */
        .footer {
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
            margin-top: 30px;
            text-align: center;
        }

        .footer-brand {
            font-size: 10px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .footer-text {
            font-size: 8px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .page-break {
            page-break-after: always;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .badge-a {
            background: #2563eb;
            color: white;
        }

        .badge-b {
            background: #e11d48;
            color: white;
        }

        /* Sub-sections within cards */
        .sub-header {
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            color: #64748b;
            margin: 10px 0 5px 0;
            border-left: 3px solid #cbd5e1;
            padding-left: 6px;
        }
    </style>
</head>

<body>
    @php
        function displayVal($val)
        {
            return !empty($val) ? $val : '<span class="none">Néant</span>';
        }
    @endphp

    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1 class="header-title">Procès-Verbal de Constat Terrain</h1>
            <div class="header-subtitle">Document de constatation technique d'accident automobile</div>
            <div class="header-ref">
                <div class="header-ref-label">Référence Dossier</div>
                <div class="header-ref-value">{{ $sinistre->numero_sinistre ?? 'SI-' . $sinistre->id }}</div>
            </div>
        </div>

        <!-- MAIN INFO BAR -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <span class="label-tiny">Lieu de l'accident</span>
                    <span class="value-text">{!! displayVal($constat->lieu ?? $sinistre->lieu) !!}</span>
                </div>
                <div class="info-cell">
                    <span class="label-tiny">Date et Heure précises</span>
                    <span
                        class="value-text">{{ $constat->date_heure ? $constat->date_heure->format('d/m/Y à H:i') : '—' }}</span>
                </div>
                <div class="info-cell">
                    <span class="label-tiny">Unité d'Intervention</span>
                    <span class="value-text">{!! displayVal($sinistre->service->name) !!}</span>
                </div>
            </div>
        </div>

        <div class="grid clearfix">
            <!-- VEHICULE A -->
            <div class="col-6">
                <div class="card accent-a">
                    <div class="card-header"><span class="badge badge-a">A</span> VÉHICULE A</div>

                    <div class="data-group">
                        <div class="data-label">Marque & Type de véhicule</div>
                        <div class="data-value">
                            {!! displayVal(($constat->veh_a_marque ?? '') . ' ' . ($constat->veh_a_type ?? '')) !!}
                        </div>
                    </div>

                    <div class="sub-header">Identité du Conducteur</div>
                    <div class="data-group">
                        <div class="data-label">Nom et Prénoms</div>
                        <div class="data-value">{!! displayVal($constat->veh_a_conducteur_nom) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Filiation (Père / Mère)</div>
                        <div class="data-value">Fils/Fille de {!! displayVal($constat->veh_a_conducteur_pere) !!} et
                            {!! displayVal($constat->veh_a_conducteur_mere) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Né(e) le / À</div>
                        <div class="data-value">
                            {{ $constat->veh_a_conducteur_date_naissance ? $constat->veh_a_conducteur_date_naissance->format('d/m/Y') : '—' }}
                            à {!! displayVal($constat->veh_a_conducteur_lieu_naissance) !!}</div>
                    </div>

                    <div class="sub-header">Permis & Assurance</div>
                    <div class="data-group">
                        <div class="data-label">Permis N° (Catégories)</div>
                        <div class="data-value">{!! displayVal($constat->veh_a_permis_numero) !!}
                            ({!! displayVal($constat->veh_a_permis_categories) !!})</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Compagnie d'Assurance</div>
                        <div class="data-value">{!! displayVal($constat->veh_a_assurance_nom) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Police N° / Attestation</div>
                        <div class="data-value">{!! displayVal($constat->veh_a_police_numero) !!} /
                            {!! displayVal($constat->veh_a_attestation_numero) !!}</div>
                    </div>

                    <div class="sub-header bg-accent-a" style="padding: 4px; border-radius: 4px;">Dégâts Apparents</div>
                    <div class="data-value" style="color: #2563eb; min-height: 20px;">
                        {!! displayVal($constat->veh_a_degats_materiels) !!}</div>
                </div>
            </div>

            <!-- VEHICULE B -->
            <div class="col-right">
                <div class="card accent-b">
                    <div class="card-header"><span class="badge badge-b">B</span> VÉHICULE B</div>

                    <div class="data-group">
                        <div class="data-label">Marque & Type de véhicule</div>
                        <div class="data-value">
                            {!! displayVal(($constat->veh_b_marque ?? '') . ' ' . ($constat->veh_b_type ?? '')) !!}
                        </div>
                    </div>

                    <div class="sub-header">Identité du Conducteur</div>
                    <div class="data-group">
                        <div class="data-label">Nom et Prénoms</div>
                        <div class="data-value">{!! displayVal($constat->veh_b_conducteur_nom) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Filiation (Père / Mère)</div>
                        <div class="data-value">Fils/Fille de {!! displayVal($constat->veh_b_conducteur_pere) !!} et
                            {!! displayVal($constat->veh_b_conducteur_mere) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Né(e) le / À</div>
                        <div class="data-value">
                            {{ $constat->veh_b_conducteur_date_naissance ? $constat->veh_b_conducteur_date_naissance->format('d/m/Y') : '—' }}
                            à {!! displayVal($constat->veh_b_conducteur_lieu_naissance) !!}</div>
                    </div>

                    <div class="sub-header">Permis & Assurance</div>
                    <div class="data-group">
                        <div class="data-label">Permis N° (Catégories)</div>
                        <div class="data-value">{!! displayVal($constat->veh_b_permis_numero) !!}
                            ({!! displayVal($constat->veh_b_permis_categories) !!})</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Compagnie d'Assurance</div>
                        <div class="data-value">{!! displayVal($constat->veh_b_assurance_nom) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Police N° / Attestation</div>
                        <div class="data-value">{!! displayVal($constat->veh_b_police_numero) !!} /
                            {!! displayVal($constat->veh_b_attestation_numero) !!}</div>
                    </div>

                    <div class="sub-header bg-accent-b" style="padding: 4px; border-radius: 4px;">Dégâts Apparents</div>
                    <div class="data-value" style="color: #e11d48; min-height: 20px;">
                        {!! displayVal($constat->veh_b_degats_materiels) !!}</div>
                </div>
            </div>
        </div>

        @if($constat->victime_nom)
            <div class="section-title">Informations sur la Victime</div>
            <div class="card" style="border-color: #cbd5e1; border-left: 5px solid #1e293b;">
                <div class="grid clearfix">
                    <div class="col-6">
                        <div class="data-group">
                            <div class="data-label">Nom et Prénoms</div>
                            <div class="data-value">{!! displayVal($constat->victime_nom) !!}</div>
                        </div>
                        <div class="data-group">
                            <div class="data-label">Né(e) le / À</div>
                            <div class="data-value">
                                {{ $constat->victime_date_naissance ? $constat->victime_date_naissance->format('d/m/Y') : '—' }}
                                à {!! displayVal($constat->victime_lieu_naissance) !!}</div>
                        </div>
                        <div class="data-group">
                            <div class="data-label">Nationalité / Profession</div>
                            <div class="data-value">{!! displayVal($constat->victime_nationalite) !!} /
                                {!! displayVal($constat->victime_profession) !!}</div>
                        </div>
                    </div>
                    <div class="col-right">
                        <div class="data-group">
                            <div class="data-label">Nature des blessures</div>
                            <div class="data-value" style="color: #dc2626;">{!! displayVal($constat->victime_blessures) !!}
                            </div>
                        </div>
                        <div class="data-group">
                            <div class="data-label">Situation au moment du choc</div>
                            <div class="data-value">
                                {!! displayVal($constat->victime_passager_vehicule ? str_replace('_', ' ', $constat->victime_passager_vehicule) : '') !!}
                            </div>
                        </div>
                        <div class="data-group">
                            <div class="data-label">Domicile</div>
                            <div class="data-value">{!! displayVal($constat->victime_domicile) !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="section-title">Corps du Constat & Observations</div>
        <div class="card" style="background: #f8fafc;">
            <div class="data-group">
                <div class="data-label">Nature des faits / Description</div>
                <div class="data-value" style="font-weight: 500; font-size: 10px;">
                    {!! displayVal($constat->description_faits) !!}</div>
            </div>
            <div class="data-group" style="margin-top: 10px;">
                <div class="data-label">Dommages / Dégâts constatés (Général)</div>
                <div class="data-value" style="font-weight: 500; font-size: 10px;">
                    {!! displayVal($constat->dommages) !!}</div>
            </div>
            <div class="data-group" style="margin-top: 10px;">
                <div class="data-label">Témoins identifiés</div>
                <div class="data-value" style="font-weight: 500; font-size: 10px;">{!! displayVal($constat->temoins) !!}
                </div>
            </div>
        </div>

        <div class="page-break"></div>

        <div class="section-title">Croquis Technique de l'Accident</div>
        <div class="sketch-box">
            @if($constat->croquis)
                <img src="{{ public_path('storage/' . $constat->croquis) }}" class="sketch-image">
            @else
                <div class="sketch-placeholder">Aucun croquis technique n'a été produit</div>
            @endif
        </div>

        <div class="section-title">Autorité de Police / Gendarmerie</div>
        <div class="card" style="border: 1px solid #1e293b; background: #fafafa;">
            <div class="grid clearfix">
                <div class="col-6">
                    <div class="data-group">
                        <div class="data-label">Fonctionnaire Constatateur</div>
                        <div class="data-value" style="font-size: 12px; color: #1e3a8a;">
                            {!! displayVal($constat->agent_nom) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Grade / Qualité</div>
                        <div class="data-value">{!! displayVal($constat->agent_grade) !!}</div>
                    </div>
                </div>
                <div class="col-right">
                    <div class="data-group">
                        <div class="data-label">Numéro Matricule</div>
                        <div class="data-value" style="font-size: 12px; font-family: monospace;">
                            {!! displayVal($constat->agent_matricule) !!}</div>
                    </div>
                    <div class="data-group">
                        <div class="data-label">Signature & Cachet de l'Unité</div>
                        <div style="height: 40px; margin-top: 5px; border-bottom: 1px dotted #cbd5e1;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-brand">Claims Master - Système de Gestion Numérique des Sinistres</div>
            <div class="footer-text">
                Ce document est généré numériquement et constitue un rapport de constatation technique.<br>
                Généré le {{ date('d/m/Y à H:i') }} | Plateforme Sécurisée Salamean Group.
            </div>
        </div>
    </div>
</body>

</html>