<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>CLAIMS MASTER - Vos accès</title>
</head>

<body style="margin:0; padding:0; background-color:#f1f5f9; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.07);">

                    {{-- Header --}}
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #243a8f 0%, #1c2e72 100%); padding: 36px 40px; text-align:center;">
                            <h1 style="color:#ffffff; font-size:24px; margin:0; letter-spacing:1px;">CLAIMS MASTER</h1>
                            <p style="color:rgba(255,255,255,0.75); font-size:13px; margin:6px 0 0;">Plateforme de
                                gestion des sinistres</p>
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="padding: 40px 40px 20px;">
                            <p style="font-size:15px; color:#374151; margin:0 0 16px;">Bonjour
                                <strong>{{ $nom }}</strong>,</p>
                            <p style="font-size:15px; color:#374151; margin:0 0 24px;">
                                Votre compte assuré a été créé avec succès sur la plateforme <strong>CLAIMS
                                    MASTER</strong>.
                                Voici vos informations d'accès :
                            </p>

                            {{-- Encadré accès --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f8faff; border:1px solid #dce3f5; border-radius:12px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:24px 28px;">
                                        <p
                                            style="margin:0 0 16px; font-size:13px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; font-weight:600;">
                                            Vos identifiants</p>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:10px 0; border-bottom:1px solid #e5e7eb;">
                                                    <span style="font-size:13px; color:#6b7280;">Code assuré</span><br>
                                                    <strong
                                                        style="font-size:18px; color:#243a8f; font-family:monospace; letter-spacing:1px;">{{ $codeUser }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:10px 0;">
                                                    <span style="font-size:13px; color:#6b7280;">Mot de passe
                                                        temporaire</span><br>
                                                    <strong
                                                        style="font-size:18px; color:#1c2e72; font-family:monospace; letter-spacing:2px;">{{ $plainPassword }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Avertissement --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0; font-size:13px; color:#92400e;">
                                            ⚠️ <strong>Important :</strong> Conservez ces informations en lieu sûr. Ne
                                            les partagez avec personne.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px; color:#6b7280; margin:0 0 20px;">
                                Pour toute question, contactez votre compagnie d'assurance.
                            </p>

                            {{-- Bouton connexion --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/login') }}"
                                           style="display:inline-block; background:linear-gradient(135deg,#243a8f,#1c2e72); color:#ffffff; text-decoration:none; font-size:15px; font-weight:600; padding:14px 36px; border-radius:10px; letter-spacing:0.5px;">
                                            🔐 Accéder à mon espace
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:10px;">
                                        <span style="font-size:12px; color:#9ca3af;">Ou copiez ce lien : </span>
                                        <a href="{{ url('/login') }}" style="font-size:12px; color:#243a8f;">{{ url('/login') }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="background:#f8faff; border-top:1px solid #e5e7eb; padding:24px 40px; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#9ca3af;">
                                &copy; {{ date('Y') }} CLAIMS MASTER — Tous droits réservés
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>