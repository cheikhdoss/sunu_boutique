<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre commande</title>
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
            background: linear-gradient(135deg, #28a745, #20c997);
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            color: white;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e9ecef;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            color: #6c757d;
        }
        .success-banner {
            background: linear-gradient(135deg, #e8f5e8, #d4edda);
            padding: 25px;
            border-radius: 10px;
            border-left: 5px solid #28a745;
            margin: 25px 0;
            text-align: center;
        }
        .delivery-info {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
        .payment-info {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        .order-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-items {
            margin-top: 20px;
        }
        .order-item {
            border-bottom: 1px solid #e9ecef;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-details {
            flex: 1;
        }
        .item-price {
            font-weight: bold;
            color: #28a745;
        }
        .total-section {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: right;
        }
        .contact-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 28px;">🛍️ SunuBoutique</h1>
        <h2 style="margin: 10px 0 0 0; font-size: 20px; font-weight: normal;">Confirmation de commande</h2>
    </div>

    <div class="content">
        <div class="success-banner">
            <h2 style="margin: 0 0 15px 0; color: #155724; font-size: 24px;">
                ✅ Commande confirmée avec succès !
            </h2>
            <p style="margin: 0; font-size: 16px; color: #155724;">
                Merci <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong> pour votre confiance !
            </p>
        </div>

        <p style="font-size: 16px;">
            Votre commande <strong>#{{ $order->order_number }}</strong> a été confirmée et est maintenant en cours de traitement.
        </p>

        <div class="delivery-info">
            <h3 style="margin: 0 0 15px 0; color: #1976d2;">
                🚚 Informations de livraison
            </h3>
            <p style="margin: 0 0 10px 0;">
                📦 <strong>Votre colis sera bientôt en route</strong> vers votre adresse de livraison.
            </p>
            <p style="margin: 0 0 10px 0;">
                📞 <strong>Notre livreur vous appellera prochainement</strong> au numéro <span class="highlight">{{ $order->shipping_phone }}</span> pour convenir d'un créneau de livraison qui vous convient.
            </p>
            <p style="margin: 0; font-style: italic; color: #666;">
                ⚠️ Merci de garder votre téléphone à portée de main pour ne pas manquer l'appel de notre livreur.
            </p>
        </div>

        @if($order->payment_method === 'cash_on_delivery')
            <div class="payment-info">
                <h3 style="margin: 0 0 15px 0; color: #856404;">
                    💰 Paiement à la livraison
                </h3>
                <p style="margin: 0 0 10px 0; color: #856404;">
                    <strong>Montant à préparer :</strong> <span style="font-size: 18px; font-weight: bold;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </p>
                <p style="margin: 0; color: #856404;">
                    💡 Préparez le montant exact pour faciliter la transaction lors de la réception de votre commande.
                </p>
            </div>
        @endif

        <div class="order-details">
            <h3 style="margin: 0 0 20px 0;">📋 Détails de la commande</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <p style="margin: 0 0 5px 0;"><strong>Numéro de commande :</strong></p>
                    <p style="margin: 0; color: #666;">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 5px 0;"><strong>Date de commande :</strong></p>
                    <p style="margin: 0; color: #666;">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 5px 0;"><strong>Adresse de livraison :</strong></p>
                <p style="margin: 0; color: #666;">
                    {{ $order->shipping_address_line_1 }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                    @if($order->notes)
                        <br><em>Note : {{ $order->notes }}</em>
                    @endif
                </p>
            </div>

            <div>
                <p style="margin: 0 0 5px 0;"><strong>Téléphone de contact :</strong></p>
                <p style="margin: 0; color: #666;">{{ $order->shipping_phone }}</p>
            </div>
        </div>

        @if($order->items && $order->items->count() > 0)
            <div class="order-details">
                <h3 style="margin: 0 0 20px 0;">🛒 Articles commandés</h3>
                <div class="order-items">
                    @foreach($order->items as $item)
                        <div class="order-item">
                            <div class="item-details">
                                <strong>{{ $item->product_name }}</strong><br>
                                <span style="color: #666;">Quantité : {{ $item->quantity }}</span>
                            </div>
                            <div class="item-price">
                                {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="total-section">
                <h3 style="margin: 0 0 10px 0; color: #28a745;">
                    💳 Total de la commande : {{ number_format($order->total, 0, ',', ' ') }} FCFA
                </h3>
                <p style="margin: 0; font-size: 14px; color: #666;">
                    @if($order->payment_method === 'cash_on_delivery')
                        À payer à la livraison
                    @else
                        Paiement en ligne
                    @endif
                </p>
            </div>
        @endif

        <div class="contact-section">
            <h3 style="margin: 0 0 15px 0; color: #17a2b8;">
                📞 Besoin d'aide ou de modifications ?
            </h3>
            <p style="margin: 0 0 10px 0;">
                Si vous souhaitez modifier votre adresse de livraison ou si vous avez des questions concernant votre commande, contactez-nous <strong>rapidement</strong> :
            </p>
            <ul style="margin: 0; padding-left: 20px;">
                <li>📧 Email : <strong>comedie442@gmail.com</strong></li>
                <li>📱 Téléphone : <strong>+221 XX XXX XX XX</strong></li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 18px; color: #28a745; margin: 0;">
                🙏 Merci de votre confiance !
            </p>
            <p style="margin: 10px 0 0 0; color: #666;">
                Nous nous réjouissons de vous livrer vos articles dans les meilleurs délais.
            </p>
        </div>

        <p style="text-align: center; font-weight: bold; color: #333;">
            L'équipe SunuBoutique 🛍️
        </p>
    </div>

    <div class="footer">
        <p>
            <strong>SunuBoutique</strong><br>
            Email: comedie442@gmail.com<br>
            Téléphone: +221 XX XXX XX XX<br>
        </p>
        <p>
            <small>
                Cet email a été envoyé automatiquement suite à votre commande.<br>
                Pour toute question, contactez-nous directement par téléphone ou email.
            </small>
        </p>
    </div>
</body>
</html>