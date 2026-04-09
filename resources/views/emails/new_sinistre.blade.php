<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Déclaration de Sinistre</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f9; color: #333; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e1e8ed; }
        .header { background: linear-gradient(135deg, #e63946 0%, #d62828 100%); color: #ffffff; padding: 40px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 30px; }
        .content h2 { color: #1d3557; font-size: 20px; margin-top: 0; border-bottom: 2px solid #f1faee; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
        .info-item { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #edf2f7; }
        .info-label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
        .info-value { font-size: 14px; color: #1e293b; font-weight: 600; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #edf2f7; }
        .btn { display: inline-block; padding: 12px 25px; background: #e63946; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 700; margin-top: 25px; transition: background 0.3s; }
        .btn:hover { background: #d62828; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CLAIMS MASTER</h1>
            <p style="margin-top: 10px; opacity: 0.9;">Nouvelle Alerte Sinistre</p>
        </div>
        <div class="content">
            <h2>Détails du sinistre #{{ $sinistre->id }}</h2>
            <p>Bonjour {{ $assurance->name }},</p>
            <p>Un nouvel incident a été déclaré par l'un de vos assurés. Voici les informations préliminaires :</p>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Assuré</div>
                    <div class="info-value">{{ $sinistre->assure->name }} {{ $sinistre->assure->prenom }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type de Sinistre</div>
                    <div class="info-value">{{ $sinistre->type_sinistre }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Véhicule</div>
                    <div class="info-value">{{ $sinistre->contrat->marque }} {{ $sinistre->contrat->modele }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Immatriculation</div>
                    <div class="info-value">{{ $sinistre->contrat->immatriculation }}</div>
                </div>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #fffcf0; border: 1px solid #feebc8; border-radius: 8px;">
                <div class="info-label">Description de l'assuré</div>
                <div class="info-value" style="font-style: italic; font-weight: 400;">"{{ $sinistre->description ?? 'Aucune description fournie' }}"</div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('portal.login') }}" class="btn">Accéder au Portail</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CLAIMS MASTER. Système de gestion de sinistres intelligent.
        </div>
    </div>
</body>
</html>
