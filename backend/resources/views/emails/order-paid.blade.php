<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2196f3;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .success-icon {
            font-size: 48px;
            color: #4caf50;
            text-align: center;
            margin-bottom: 20px;
        }
        .order-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        .order-items {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            color: #2196f3;
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #2196f3;
        }
        .button {
            display: inline-block;
            background-color: #2196f3;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }
        .next-steps {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .step {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .step-icon {
            background-color: #2196f3;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Paiement confirmé !</h1>
        <p>Votre commande a été payée avec succès</p>
    </div>

    <div class="content">
        <div class="success-icon">✅</div>
        
        <p>Bonjour <strong>{{ $customerName }}</strong>,</p>
        
        <p>Nous avons le plaisir de vous confirmer que votre paiement a été traité avec succès. Votre commande est maintenant confirmée et sera bientôt préparée pour l'expédition.</p>

        <div class="order-details">
            <h3>📋 Détails de votre commande</h3>
            <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
            <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Statut :</strong> <span style="color: #4caf50;">Confirmée et payée</span></p>
            <p><strong>Mode de paiement :</strong> PayDunya</p>
        </div>

        @if($order->items && $order->items->count() > 0)
        <div class="order-items">
            <h3>🛍️ Articles commandés</h3>
            @foreach($order->items as $item)
            <div class="item">
                <div>
                    <strong>{{ $item->product_name }}</strong><br>
                    <small>Quantité: {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</small>
                </div>
                <div>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</div>
            </div>
            @endforeach
            <div class="total">
                Total payé : {{ number_format($order->total, 0, ',', ' ') }} FCFA
            </div>
        </div>
        @endif

        <div class="next-steps">
            <h3>📦 Prochaines étapes</h3>
            <div class="step">
                <div class="step-icon">1</div>
                <div>Votre commande va être préparée par notre équipe</div>
            </div>
            <div class="step">
                <div class="step-icon">2</div>
                <div>Vous recevrez un email avec les informations de suivi</div>
            </div>
            <div class="step">
                <div class="step-icon">3</div>
                <div>Votre commande sera expédiée sous 24-48h</div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $paymentUrl }}" class="button">Voir les détails de ma commande</a>
        </div>

        <p><strong>Informations de livraison :</strong></p>
        <p>
            {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
            {{ $order->shipping_address_line_1 }}<br>
            @if($order->shipping_address_line_2)
                {{ $order->shipping_address_line_2 }}<br>
            @endif
            {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
            {{ $order->shipping_country }}<br>
            Tél: {{ $order->shipping_phone }}
        </p>

        @if($order->notes)
        <p><strong>Notes :</strong> {{ $order->notes }}</p>
        @endif

        <p>Si vous avez des questions concernant votre commande, n'hésitez pas à nous contacter.</p>
        
        <p>Merci pour votre confiance !</p>
        <p><strong>L'équipe SunuBoutique</strong></p>
    </div>

    <div class="footer">
        <p>
            <strong>SunuBoutique</strong><br>
            Email: comedie442@gmail.com<br>
            Téléphone: +221 XX XXX XX XX<br>
            <a href="{{ config('app.frontend_url') }}">www.sunuboutique.com</a>
        </p>
        <p style="font-size: 0.8em; color: #999;">
            Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
        </p>
    </div>
</body>
</html>