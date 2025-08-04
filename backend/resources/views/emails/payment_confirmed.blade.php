<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre paiement</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #f8fafc;
            color: #718096;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .content {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background-color: #3490dc;
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .success-icon {
            color: #10b981;
            font-size: 48px;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="header">
                <img src="{{ $logoUrl ?? 'https://via.placeholder.com/150' }}" alt="Logo de votre entreprise">
            </div>

            <div style="text-align: center; margin-bottom: 20px;">
                <span class="success-icon">✔</span>
            </div>

            <h1>Paiement confirmé !</h1>

            <p>Bonjour {{ $recipientName }},</p>

            <p>Nous avons bien reçu le paiement pour votre commande n°<strong>{{ $order->order_number }}</strong> d'un montant de <strong>{{ number_format($order->total, 2, ',', ' ') }} XOF</strong>.</p>

            <p>Votre commande est maintenant en cours de préparation. Vous recevrez un autre e-mail dès qu'elle sera expédiée.</p>

            <p style="text-align: center;">
                <a href="{{ $actionUrl }}" class="button">Voir le récapitulatif de ma commande</a>
            </p>

            <p>Merci pour votre achat.</p>
            <p>L'équipe de {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </div>
    </div>
</body>
</html>
