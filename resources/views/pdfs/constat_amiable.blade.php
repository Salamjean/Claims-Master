<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Constat Amiable - {{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; }
        
        /* Main Container */
        .container { width: 100%; }
        
        /* Header */
        .header { border-bottom: 2px solid #213685; padding-bottom: 15px; margin-bottom: 20px; position: relative; }
        .header-title { font-size: 20px; font-weight: 900; color: #213685; text-transform: uppercase; margin: 0; letter-spacing: -0.5px; }
        .header-subtitle { color: #64748b; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 2px; }
        .header-ref { position: absolute; right: 0; top: 0; text-align: right; }
        .header-ref-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; font-weight: bold; }
        .header-ref-value { font-size: 14px; font-weight: 900; color: #ef4444; }

        /* Document Info Bar */
        .info-bar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-bottom: 20px; }
        .info-item { display: inline-block; width: 32%; vertical-align: top; }
        .info-label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .info-value { font-size: 11px; font-weight: bold; color: #1e293b; }

        /* Layout Grid */
        .row { width: 100%; clear: both; }
        .col-6 { float: left; width: 49%; }
        .col-right { float: right; width: 49%; }

        /* Vehicle Sections */
        .section-header { padding: 6px 12px; border-radius: 6px 6px 0 0; font-weight: 900; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: white; }
        .header-a { background: #213685; }
        .header-b { background: #ea580c; }
        
        .section-content { border: 1px solid #e2e8f0; border-top: none; padding: 10px; border-radius: 0 0 8px 8px; min-height: 150px; background: white; margin-bottom: 15px; }
        
        .data-row { margin-bottom: 6px; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px; }
        .data-row:last-child { border-bottom: none; }
        .data-label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .data-value { font-size: 10px; font-weight: bold; color: #1e293b; }

        /* Impact Area */
        .impact-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; font-size: 10px; font-weight: 900; margin-top: 4px; }
        .impact-a { background: #dbeafe; color: #1e40af; }
        .impact-b { background: #ffedd5; color: #9a3412; }

        /* Circumstances Table */
        .circumstances-box { margin-top: 10px; }
        .circ-table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .circ-th { background: #f8fafc; font-size: 8px; text-transform: uppercase; color: #64748b; padding: 6px; border: 1px solid #e2e8f0; }
        .circ-td { font-size: 9px; padding: 4px 8px; border: 1px solid #e2e8f0; }
        .circ-check { display: inline-block; width: 10px; height: 10px; border: 1px solid #cbd5e1; background: #f1f5f9; text-align: center; line-height: 10px; font-weight: bold; margin-right: 4px; }
        .circ-checked-a { background: #213685; border-color: #213685; color: white; }
        .circ-checked-b { background: #ea580c; border-color: #ea580c; color: white; }
        .circ-label-active { font-weight: bold; color: #1e293b; }

        /* Sketch Section */
        .sketch-box { border: 2px dashed #e2e8f0; border-radius: 12px; height: 260px; text-align: center; margin-top: 15px; position: relative; background: #fafafa; }
        .sketch-title { position: absolute; top: 10px; left: 15px; font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase; }
        .sketch-image { max-width: 90%; max-height: 90%; margin-top: 5%; object-fit: contain; }

        /* Signatures */
        .signatures-row { margin-top: 20px; }
        .signature-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center; background: white; }
        .signature-title { font-size: 9px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; }
        .signature-img { max-width: 140px; max-height: 80px; filter: contrast(150%); }

        /* Footer */
        .footer { padding-top: 15px; border-top: 1px solid #e2e8f0; margin-top: 30px; text-align: center; }
        .footer-text { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

        .clearfix::after { content: ""; clear: both; display: table; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1 class="header-title">Constat Amiable d'Accident</h1>
            <div class="header-subtitle">Document Officiel de Déclaration de Sinistre Automobile</div>
            <div class="header-ref">
                <div class="header-ref-label">Référence Dossier</div>
                <div class="header-ref-value">{{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</div>
            </div>
        </div>

        <!-- INFO BAR -->
        <div class="info-bar row">
            <div class="info-item">
                <div class="info-label">1. Date & Heure</div>
                <div class="info-value">{{ $sinistre->created_at->format('d/m/Y à H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">2. Localisation (Lieu)</div>
                <div class="info-value">{{ $sinistre->lieu ?? 'Coordonnées GPS: '.$sinistre->latitude.', '.$sinistre->longitude }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">3. Témoins</div>
                <div class="info-value">{{ !empty($data['temoins']) ? $data['temoins'] : 'Néant' }}</div>
            </div>
        </div>

        <!-- VEHICLES DATA -->
        <div class="row clearfix">
            <!-- VEHICLE A -->
            <div class="col-6">
                <div class="section-header header-a">Véhicule A</div>
                <div class="section-content">
                    <div class="data-row">
                        <div class="data-label">Assuré (Nom / Prénom)</div>
                        <div class="data-value">{{ $sinistre->assure->name }} {{ $sinistre->assure->prenom }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Véhicule (Marque / Modèle / Plaque)</div>
                        <div class="data-value">{{ $sinistre->contrat->marque }} {{ $sinistre->contrat->modele }} - <span style="color: #213685;">{{ $sinistre->contrat->immatriculation }}</span></div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Conducteur</div>
                        <div class="data-value">{{ $data['partie_a']['conducteur'] ?? 'L\'assuré lui-même' }}</div>
                    </div>
                    <div class="data-row" style="border-bottom: none;">
                        <div class="data-label">Point de choc initial</div>
                        <div class="impact-badge impact-a">{{ str_replace('-', ' ', $data['partie_a']['point_choc'] ?? 'Non indiqué') }}</div>
                    </div>
                </div>
            </div>

            <!-- VEHICLE B -->
            <div class="col-right">
                <div class="section-header header-b">Véhicule B</div>
                <div class="section-content">
                    <div class="data-row">
                        <div class="data-label">Propriétaire / Conducteur</div>
                        <div class="data-value">{{ $data['partie_b']['nom'] ?? 'Inconnu' }} {{ $data['partie_b']['prenom'] ?? '' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Contact (Tél / Email)</div>
                        <div class="data-value">{{ $data['partie_b']['contact'] ?? 'Non fourni' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Véhicule (Identifiant / Détails)</div>
                        <div class="data-value">{{ $data['partie_b']['vehicule'] ?? 'Non précisé' }}</div>
                    </div>
                    <div class="data-row" style="border-bottom: none;">
                        <div class="data-label">Point de choc initial</div>
                        <div class="impact-badge impact-b">{{ str_replace('-', ' ', $data['partie_b']['point_choc'] ?? 'Non indiqué') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CIRCUMSTANCES -->
        <div class="circumstances-box">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px;">12. Circonstances de l'accident</div>
            <table class="circ-table">
                <thead>
                    <tr>
                        <th class="circ-th" width="10%" style="text-align: center;">A</th>
                        <th class="circ-th">Description des faits</th>
                        <th class="circ-th" width="10%" style="text-align: center;">B</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $circonstances = [
                            1 => "En stationnement / à l'arrêt",
                            2 => "Quittait un stationnement / ouvrait une portière",
                            3 => "Prenait un stationnement",
                            4 => "Sortait d'un parking, lieu privé, chemin de terre",
                            5 => "S’engageait dans un parking, lieu privé, chemin de terre",
                            6 => "S’engageait sur une place à sens giratoire",
                            7 => "Roulait sur une place à sens giratoire",
                            8 => "Heurtait à l’arrière (même sens, même file)",
                            9 => "Roulait dans le même sens (file différente)",
                            10 => "Changeait de file",
                            11 => "Doublait",
                            12 => "Virait à droite",
                            13 => "Virait à gauche",
                            14 => "Reculait",
                            15 => "Empiétait sur une voie réservée",
                            16 => "Venait de droite (dans un carrefour)",
                            17 => "N’avait pas observé un signal de priorité"
                        ];
                    @endphp
                    @foreach($circonstances as $num => $label)
                        @php
                            $activeA = in_array($num, $data['partie_a']['circonstances'] ?? []);
                            $activeB = in_array($num, $data['partie_b']['circonstances'] ?? []);
                        @endphp
                        <tr>
                            <td class="circ-td" style="text-align: center;">
                                <div class="circ-check {{ $activeA ? 'circ-checked-a' : '' }}">{{ $activeA ? 'X' : '' }}</div>
                            </td>
                            <td class="circ-td {{ ($activeA || $activeB) ? 'circ-label-active' : '' }}" style="color: {{ ($activeA || $activeB) ? '#1e293b' : '#94a3b8' }};">
                                {{ $num }}. {{ $label }}
                            </td>
                            <td class="circ-td" style="text-align: center;">
                                <div class="circ-check {{ $activeB ? 'circ-checked-b' : '' }}">{{ $activeB ? 'X' : '' }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="page-break"></div>

        <!-- SKETCH -->
        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px;">13. Croquis de l'accident</div>
        <div class="sketch-box">
            <div class="sketch-title">Position des véhicules au moment du choc</div>
            @if($constat->croquis)
                <img src="{{ public_path('storage/' . $constat->croquis) }}" class="sketch-image">
            @else
                <div style="padding-top: 110px; color: #cbd5e1; font-weight: bold; font-size: 14px;">Aucun croquis fourni</div>
            @endif
        </div>

        <!-- SIGNATURES -->
        <div class="signatures-row row clearfix">
            <div class="col-6">
                <div class="signature-card">
                    <div class="signature-title" style="color: #213685;">Signature Conducteur A</div>
                    @if($constat->ass1_photo)
                        <img src="{{ public_path('storage/' . $constat->ass1_photo) }}" class="signature-img">
                    @else
                        <div style="height: 60px; line-height: 60px; color: #cbd5e1; font-style: italic;">Absente</div>
                    @endif
                </div>
            </div>
            <div class="col-right">
                <div class="signature-card">
                    <div class="signature-title" style="color: #ea580c;">Signature Conducteur B</div>
                    @if($constat->ass2_photo)
                        <img src="{{ public_path('storage/' . $constat->ass2_photo) }}" class="signature-img">
                    @else
                        <div style="height: 60px; line-height: 60px; color: #cbd5e1; font-style: italic;">Absente</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-text">Généré via Claims Master App le {{ date('d/m/Y à H:i') }}</div>
            <div class="footer-text" style="margin-top: 4px; font-weight: 900; color: #cbd5e1;">© Salamean Group - Tous droits réservés</div>
        </div>
    </div>
</body>
</html>
