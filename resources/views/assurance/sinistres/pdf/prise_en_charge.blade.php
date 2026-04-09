<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bon de Prise en Charge</title>
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
            color: #005f99;
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

    <div class="title">BON DE PRISE EN CHARGE - RÉPARATIONS</div>

    <p style="text-align:right;">Date d'émission :
        {{ $sinistre->date_bon_prise_charge ? $sinistre->date_bon_prise_charge->format('d/m/Y') : date('d/m/Y') }}</p>

    <p>Nous autorisons par la présente le garage désigné ci-dessous à effectuer les réparations sur le véhicule
        identifié, conformément au rapport de l'expert mandaté.</p>

    <table>
        <tr>
            <th>Dossier Sinistre N°</th>
            <td>{{ $sinistre->numero_sinistre ?? $sinistre->id }}</td>
        </tr>
        <tr>
            <th>Assuré(e)</th>
            <td>{{ $sinistre->assure->name }} {{ $sinistre->assure->prenom }}</td>
        </tr>
        <tr>
            <th>Véhicule (Immatriculation)</th>
            <td>{{ $sinistre->constat->v1_immatriculation ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Garage Affecté</th>
            <td>{{ $sinistre->garage->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Expert Mandaté</th>
            <td>{{ $sinistre->expert->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <p style="margin-top: 30px;">
        <strong>Important :</strong> Ce bon est délivré sous réserve de garantie. Les travaux supplémentaires non prévus
        dans le devis validé par l'expert devront faire l'objet d'un accord préalable.
    </p>

    <div class="signature-box">
        <div>
            <p><strong>L'Assureur</strong></p>
            <p><em>(Signature et Cachet)</em></p>
        </div>
        <div>
            <p><strong>Pour Accord (Garage)</strong></p>
        </div>
    </div>

</body>

</html>