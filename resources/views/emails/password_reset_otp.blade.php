<!DOCTYPE html>
<html>

<head>
    <title>Réinitialisation de mot de passe - Prestataire AF</title>
</head>

<body>
    <table width="100%" cellpadding="0" cellspacing="0"
        style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <img src="{{ $logoUrl }}" alt="Logo Prestataire AF" width="150">
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; border: 1px solid #eee; border-radius: 8px;">
                <h1 style="color: #dc2626; font-size: 24px; margin-bottom: 20px;">Réinitialisation de mot de passe</h1>
                <p>Bonjour,</p>
                <p>Vous avez demandé la réinitialisation du mot de passe de votre compte <strong>{{ $email }}</strong>.
                </p>
                <p>Veuillez utiliser le code de vérification suivant pour définir un nouveau mot de passe :</p>
                <div
                    style="background-color: #f3f4f6; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
                    <span
                        style="font-size: 32px; font-weight: bold; letter-spacing: 10px; color: #111827;">{{ $code }}</span>
                </div>
                <p>Ce code est nécessaire pour accéder à la page de réinitialisation.</p>
                <p style="text-align: center; margin-top: 30px;">
                    <a href="{{ url('/reset-password/' . $email) }}"
                        style="background-color:#dc2626; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; border-radius: 8px; font-weight: bold;">Réinitialiser
                        mon mot de passe</a>
                </p>
                <p style="margin-top: 30px; font-size: 14px; color: #666;">Si vous n'êtes pas à l'origine de cette
                    demande, vous pouvez ignorer cet email en toute sécurité.</p>
                <p style="margin-top: 20px;">Merci,<br>L'équipe Prestataire AF</p>
            </td>
        </tr>
    </table>
</body>

</html>