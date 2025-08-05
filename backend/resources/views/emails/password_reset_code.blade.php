<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de réinitialisation - SunuBoutique</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 2px dashed #e74c3c;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #e74c3c;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SunuBoutique</div>
            <h2>Réinitialisation de votre mot de passe</h2>
        </div>

        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

        <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte SunuBoutique.</p>

        <p>Voici votre code de vérification :</p>

        <div class="code-container">
            <div class="code">{{ $code }}</div>
        </div>

        <div class="warning">
            <strong>⚠️ Important :</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Ce code expire dans <strong>15 minutes</strong></li>
                <li>Ne partagez jamais ce code avec personne</li>
                <li>Si vous n'avez pas demandé cette réinitialisation, ignorez ce message</li>
            </ul>
        </div>

        <p>Pour continuer la réinitialisation de votre mot de passe :</p>
        <ol>
            <li>Retournez sur l'application SunuBoutique</li>
            <li>Saisissez ce code de vérification</li>
            <li>Créez votre nouveau mot de passe</li>
        </ol>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p><strong>L'équipe SunuBoutique</strong></p>
            <p style="font-size: 12px; color: #999;">
                Si vous rencontrez des problèmes, contactez notre support client.
            </p>
        </div>
    </div>
</body>
</html>