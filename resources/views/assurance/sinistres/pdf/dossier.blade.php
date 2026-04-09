<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dossier Sinistre</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }

        .header,
        .footer {
            text-align: center;
            font-weight: bold;
        }

        .title {
            text-align: center;
            color: green;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            color: #005f99;
            font-weight: bold;
            border-bottom: 1px solid #005f99;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table,
        th,
        td {
            border: 1px dashed black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .flex {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .col {
            flex: 1;
            padding: 0 10px;
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body onload="window.print()">

    <!-- RECTO -->
    <div class="header">
        <p>Nom de la société d'Assurance: {{ $sinistre->assurance->name ?? 'N/A' }}</p>
        <p>Siège social / Adresse : {{ $sinistre->assurance->adresse ?? 'N/A' }}</p>
        <p>Email: {{ $sinistre->assurance->email ?? 'N/A' }} | Téléphone: {{ $sinistre->assurance->contact ?? 'N/A' }}
        </p>
        @if($sinistre->assurance->assuranceProfile)
            <p>RCM: {{ $sinistre->assurance->assuranceProfile->numero_rccm ?? 'N/A' }} | N° Contribuable:
                {{ $sinistre->assurance->assuranceProfile->numero_dfe ?? 'N/A' }}</p>
        @endif
    </div>

    <div class="title">II - INFOS FIGURANT SUR LE DOSSIER SINISTRE</div>

    <h2 style="color: red; text-align:center;">PREMIÈRE PAGE (Recto)</h2>

    <p style="text-align:center; font-weight:bold; font-size:18px;">SINISTRE
        {{ \Str::upper(str_replace('_', ' ', $sinistre->type_sinistre)) }}<br>N°
        {{ $sinistre->numero_sinistre ?? 'N/A' }}</p>

    <div class="section-title">Section Informations de Gestion :</div>
    <ul>
        <li><strong>SURVENU LE :</strong>
            {{ $sinistre->date_survenance ? $sinistre->date_survenance->format('d/m/Y') : 'N/A' }}</li>
        <li><strong>DÉCLARÉ LE :</strong>
            {{ $sinistre->date_declaration ? $sinistre->date_declaration->format('d/m/Y') : ($sinistre->created_at ? $sinistre->created_at->format('d/m/Y') : 'N/A') }}
        </li>
        <li><strong>OUVERT LE :</strong>
            {{ $sinistre->date_ouverture ? $sinistre->date_ouverture->format('d/m/Y') : ($sinistre->created_at ? $sinistre->created_at->format('d/m/Y') : 'N/A') }}
        </li>
    </ul>

    <div class="flex">
        <div class="col">
            <div class="section-title">Section Identification (Assuré) :</div>
            <ul>
                <li>ASSURÉ : {{ $sinistre->assure->name }} {{ $sinistre->assure->prenom }}</li>
                @php $contrat = $sinistre->assure->contrats->first(); @endphp
                <li>N° POLICE : {{ $contrat ? $contrat->numero_contrat : 'N/A' }}</li>
                <li>EFFET : {{ $contrat ? $contrat->date_debut->format('d/m/Y') : 'N/A' }}</li>
                <li>EXPIRATION : {{ $contrat && $contrat->date_fin ? $contrat->date_fin->format('d/m/Y') : 'N/A' }}</li>
                <li>MARQUE : {{ $sinistre->constat->v1_marque ?? 'N/A' }}</li>
                <li>IMMATRICULATION : {{ $sinistre->constat->v1_immatriculation ?? 'N/A' }}</li>
                <li>CONDUCTEUR : {{ $sinistre->constat->c1_nom ?? 'N/A' }} {{ $sinistre->constat->c1_prenom ?? 'N/A' }}
                </li>
            </ul>
        </div>
        <div class="col">
            <div class="section-title">Section Identification (Tiers) :</div>
            <ul>
                <li>TIERS : {{ $sinistre->constat->c2_nom ?? 'N/A' }} {{ $sinistre->constat->c2_prenom ?? 'N/A' }}</li>
                <li>N° POLICE : {{ $sinistre->constat->ass2_numero_police ?? 'N/A' }}</li>
                <li>COMPAGNIE : {{ $sinistre->constat->ass2_compagnie ?? 'N/A' }}</li>
                <li>MARQUE : {{ $sinistre->constat->v2_marque ?? 'N/A' }}</li>
                <li>IMMATRICULATION : {{ $sinistre->constat->v2_immatriculation ?? 'N/A' }}</li>
            </ul>
        </div>
    </div>

    <div class="section-title">Section Financière :</div>
    <ul>
        <li>PRIME : {{ ($contrat && $contrat->prime_payee) ? '[X] PAYÉE' : '[ ] PAYÉE' }}
            {{ ($contrat && !$contrat->prime_payee) ? '[X] IMPAYÉE' : '[ ] IMPAYÉE' }}</li>
        <li>SOLDE DE LA POLICE : N/A</li>
        <li>SOLDE GLOBAL DU CLIENT : N/A</li>
    </ul>

    <div style="page-break-after: always;"></div>

    <!-- VERSO -->
    <h2 style="color: red; text-align:center;">DEUXIÈME PAGE (Verso)</h2>

    <div class="section-title">CIRCONSTANCES DU SINISTRE :</div>
    <p>{{ $sinistre->constat->circonstances ?? $sinistre->description ?? '..................................................................' }}
    </p>

    <div class="section-title">DÉGÂTS MATÉRIELS :</div>
    <p>{{ $sinistre->constat->v1_degats ?? '..................................................................' }}</p>

    <div class="section-title">VICTIMES :</div>
    <ul>
        <li>BLESSÉS (S) NOMBRE : ........</li>
        <li>DÉCÉDÉ (S) NOMBRE : ........</li>
    </ul>

    <p style="font-weight: bold;">Tableau de suivi des victimes :</p>
    <table>
        <thead>
            <tr>
                <th rowspan="2">VÉHICULE ASSURÉ<br>Nom & Prénoms</th>
                <th colspan="2">cases à cocher</th>
                <th rowspan="2">TIERS<br>Nom & Prénoms</th>
                <th colspan="2">cases à cocher</th>
            </tr>
            <tr>
                <th>BL (Blessé)</th>
                <th>DCD (Décédé)</th>
                <th>BL (Blessé)</th>
                <th>DCD (Décédé)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><br></td>
                <td></td>
                <td></td>
                <td><br></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><br></td>
                <td></td>
                <td></td>
                <td><br></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><br></td>
                <td></td>
                <td></td>
                <td><br></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Tableau : GARANTIES MISES EN CAUSE : (*)</div>
    @php
        $garantiesList = [
            'Responsabilité civile',
            'Corporel',
            'Dommage au véhicule',
            'Tierce collision',
            'Incendie et explosions',
            'Vol du véhicule',
            'Bris de glaces',
            'Vol à main armée',
            'Vol accessoires',
            'Défense Recours',
            'Remboursement anticipé',
            'Immobilisation',
            'ACP',
            'Honoraires Expert',
            'Assistance'
        ];
        $souscrites = $contrat && $contrat->garanties ? $contrat->garanties : [];
    @endphp
    <ul>
        @foreach($garantiesList as $index => $g)
            <li>{{ $index + 1 }}. {{ $g }} [{{ in_array($g, $souscrites) ? 'X' : ' ' }}]</li>
        @endforeach
    </ul>
    <p><em>(*) Cocher la case correspondante</em></p>

    <div class="section-title">Section Expertise et Suivi :</div>
    <ul>
        <li>FRANCHISE : {{ $contrat ? $contrat->franchise : '........' }}</li>
        <li>GARAGE / VITRIER : {{ $sinistre->garage->name ?? '........' }}</li>
        <li>EXPERT : {{ $sinistre->expert->name ?? '........' }}
            <ul>
                <li>Date de la mission :
                    {{ $sinistre->date_mandat_expert ? $sinistre->date_mandat_expert->format('d/m/Y') : '........' }}
                </li>
                <li>Date de la réception du rapport :
                    {{ $sinistre->date_rapport_expert ? $sinistre->date_rapport_expert->format('d/m/Y') : '........' }}
                </li>
            </ul>
        </li>
        <li>MÉDECIN : ........</li>
        <li>AVOCAT : ........</li>
    </ul>

    <div class="footer" style="margin-top: 50px;">
        <p>Généré le {{ date('d/m/Y H:i') }} par le système CLAIMS MASTER</p>
    </div>

</body>

</html>