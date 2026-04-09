<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Outfit', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #1d3557 0%, #152840 100%); padding: 40px 20px; text-align: center; color: #ffffff; }
        .content { padding: 40px; }
        .footer { background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
        .code-box { background: #f8fafc; border: 2px dashed #3b82f6; border-radius: 16px; padding: 20px; text-align: center; margin: 30px 0; }
        .code { font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #1d4ed8; font-family: monospace; }
        .button { display: inline-block; padding: 16px 32px; background: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 14px; font-weight: 700; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5); }
        .h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .p { margin: 16px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="h1">Bienvenue sur Claims Master</h1>
        </div>
        <div class="content">
            <p class="p">Bonjour <strong>{{ $name }}</strong>,</p>
            <p class="p">Votre compte Agent a été créé par le service <strong>{{ $serviceName }}</strong>.</p>
            <p class="p">Pour activer votre accès et définir votre mot de passe, veuillez utiliser le code de validation ci-dessous :</p>
            
            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>

            <p class="p text-center">Cliquez sur le bouton ci-dessous pour accéder à la page d'activation :</p>
            
            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="button">Activer mon compte</a>
            </div>

            <p class="p" style="font-size: 13px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Claims Master. Tous droits réservés.
        </div>
    </div>
</body>
</html>
