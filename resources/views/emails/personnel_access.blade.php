<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLAIMS MASTER - Activation de votre compte Personnel</title>
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
                            style="background: linear-gradient(135deg, #1d3557 0%, #152840 100%); padding: 36px 40px; text-align:center;">
                            <h1 style="color:#ffffff; font-size:24px; margin:0; letter-spacing:1px;">CLAIMS MASTER</h1>
                            <p style="color:rgba(255,255,255,0.75); font-size:13px; margin:6px 0 0;">Plateforme de
                                gestion des sinistres</p>
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="padding: 40px 40px 20px;">
                            <p style="font-size:15px; color:#374151; margin:0 0 16px;">Bonjour
                                <strong>{{ trim($name) }}</strong>,</p>
                            <p style="font-size:15px; color:#374151; margin:0 0 24px;">
                                Votre compte <strong>Personnel</strong> a été créé par
                                <strong>{{ $assuranceName }}</strong> sur la plateforme <strong>CLAIMS MASTER</strong>.
                            </p>
                            <p style="font-size:15px; color:#374151; margin:0 0 24px;">
                                Pour activer votre accès et définir votre mot de passe, utilisez le code ci-dessous puis
                                cliquez sur le bouton d'activation.
                            </p>

                            {{-- Code d'activation --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f0f9ff; border:2px dashed #3b82f6; border-radius:14px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:24px; text-align:center;">
                                        <p
                                            style="margin:0 0 8px; font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:1px; font-weight:600;">
                                            Votre code d'activation
                                        </p>
                                        <p
                                            style="margin:0; font-size:36px; font-weight:800; letter-spacing:10px; color:#1d4ed8; font-family:monospace;">
                                            {{ $code }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px; color:#374151; text-align:center; margin:0 0 20px;">
                                Cliquez sur le bouton ci-dessous pour accéder à la page d'activation :
                            </p>

                            <div style="text-align:center; margin-bottom:36px;">
                                <a href="{{ $actionUrl }}"
                                    style="display:inline-block; padding:16px 36px; background:#1d4ed8; color:#ffffff; text-decoration:none; border-radius:12px; font-weight:700; font-size:15px; letter-spacing:0.5px; box-shadow:0 4px 12px rgba(29,78,216,0.35);">
                                    Activer mon compte
                                </a>
                            </div>

                            <p
                                style="font-size:13px; color:#94a3b8; margin-top:20px; border-top:1px solid #f1f5f9; padding-top:20px;">
                                Si vous n'êtes pas à l'origine de cette invitation, vous pouvez ignorer cet email en
                                toute sécurité.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="background:#f8fafc; padding:20px; text-align:center; font-size:12px; color:#64748b; border-top:1px solid #e2e8f0;">
                            &copy; {{ date('Y') }} Claims Master &mdash; Tous droits réservés.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
