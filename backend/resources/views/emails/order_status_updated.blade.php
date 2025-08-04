<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre commande</title>
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
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
        }
        .status-pending { background-color: #6b7280; }
        .status-processing { background-color: #f59e0b; }
        .status-shipped { background-color: #3b82f6; }
        .status-delivered { background-color: #10b981; }
        .status-cancelled { background-color: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="header">
                {{-- Remplacez par l'URL de votre logo --}}
                <img src="{{ $logoUrl ?? 'https://via.placeholder.com/150' }}" alt="Logo de votre entreprise">
            </div>

            <h1>Mise à jour de votre commande</h1>

            <p>Bonjour {{ $recipientName }},</p>

            <p>Le statut de votre commande n°<strong>{{ $order->order_number }}</strong> a été mis à jour.</p>

            <p>Nouveau statut :
                @php
                    $statusTranslations = [
                        'pending' => 'En attente',
                        'processing' => 'En cours de traitement',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                    ];
                    $statusClasses = [
                        'pending' => 'status-pending',
                        'processing' => 'status-processing',
                        'shipped' => 'status-shipped',
                        'delivered' => 'status-delivered',
                        'cancelled' => 'status-cancelled',
                    ];
                    $statusText = $statusTranslations[$order->status] ?? ucfirst($order->status);
                    $statusClass = $statusClasses[$order->status] ?? 'status-pending';
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
            </p>

            <p>Vous pouvez consulter les détails de votre commande en cliquant sur le bouton ci-dessous :</p>

            <p style="text-align: center;">
                <a href="{{ $actionUrl }}" class="button">Voir ma commande</a>
            </p>

            <p>Merci de votre confiance.</p>
            <p>L'équipe de {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </div>
    </div>
</body>
</html>
