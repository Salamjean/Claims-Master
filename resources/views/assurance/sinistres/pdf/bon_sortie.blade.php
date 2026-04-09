<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bon de Sortie</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 40px;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .title {
            text-align: center;
            color: #d9534f;
            font-weight: bold;
            font-size: 22px;
            text-decoration: underline;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .details-box {
            border: 1px solid #333;
            padding: 15px;
            margin-top: 20px;
        }

        .signature-box {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h1>{{ $sinistre->assurance->name ?? 'ASSURANCE' }}</h1>
        <p>{{ $sinistre->assurance->adresse ?? '' }}</p>
    </div>

    <div class="title">BON DE SORTIE - VÉHICULE RÉPARÉ</div>

    <p style="text-align:right;">Date de restitution : {{ date('d/m/Y') }}</p>

    <p>Ce document atteste de la bonne exécution des travaux de réparation sur le véhicule, validés par l'expert et de
        sa restitution au propriétaire.</p>

    <table>
        <tr>
            <th>Dossier Sinistre N°</th>
            <td>{{ $sinistre->numero_sinistre ?? $sinistre->id }}</td>
        </tr>
        <tr>
            <th>Assuré(e) / Bénéficiaire</th>
            <td>{{ $sinistre->assure->name }} {{ $sinistre->assure->prenom }}</td>
        </tr>
        <tr>
            <th>Véhicule (Immatriculation)</th>
            <td>{{ $sinistre->constat->v1_immatriculation ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Garage ayant effectué les travaux</th>
            <td>{{ $sinistre->garage->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="details-box">
        <h3>Détails Financiers</h3>
        <p>Montant total du préjudice / Travaux : <strong>À compléter par l'assureur</strong></p>
        <p>Franchise (à la charge de l'assuré) : <strong>{{ $sinistre->assure->contrats->first()->franchise ?? '...' }}
                FCFA</strong></p>
        <p>Règle proportionnelle / Pénalités : <strong>À compléter si applicable</strong></p>
    </div>

    <p style="margin-top: 20px;">
        Le garage libère le véhicule sur présentation de ce bon de sortie. La facture finale devra être jointe au
        présent bon et transmise à l'assureur pour paiement dans un délai de 30 jours maximum.
    </p>

    <div class="signature-box">
        <div>
            <p><strong>L'Assureur</strong></p>
            <p><em>(Signature et Cachet)</em></p>
        </div>
        <div>
            <p><strong>L'Assuré(e)</strong></p>
            <p><em>Pour réception du véhicule satisfait(e)</em></p>
        </div>
        <div>
            <p><strong>Le Garage</strong></p>
        </div>
    </div>

</body>

</html>