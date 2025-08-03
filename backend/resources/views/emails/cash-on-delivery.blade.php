<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement à la livraison - Commande confirmée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header h2 {
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .success-banner {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            padding: 25px;
            border-radius: 10px;
            border-left: 5px solid #ff6b35;
            margin: 25px 0;
            text-align: center;
        }
        .payment-highlight {
            background: linear-gradient(135deg, #ffe8d1, #ffd89b);
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #ff6b35;
            margin: 25px 0;
            text-align: center;
            position: relative;
        }
        .payment-highlight::before {
            content: "💰";
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff6b35;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .delivery-instructions {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
        .important-notes {
            background-color: #fff3e0;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #ff9800;
            margin: 20px 0;
        }
        .order-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #e9ecef;
        }
        .order-items {
            margin-top: 15px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-details {
            flex: 1;
        }
        .item-price {
            font-weight: bold;
            color: #ff6b35;
            font-size: 16px;
        }
        .total-amount {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .contact-info {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .footer a {
            color: #ff6b35;
            text-decoration: none;
        }
        .checklist {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .checklist-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 8px;
            background-color: white;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }
        .checklist-item::before {
            content: "✓";
            background-color: #28a745;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .highlight-text {
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🛍️ SunuBoutique</h1>
            <h2>💰 Paiement à la livraison</h2>
        </div>

        <div class="content">
            <div class="success-banner">
                <h2 style="margin: 0 0 10px 0; color: #856404; font-size: 22px;">
                    ✅ Commande confirmée !
                </h2>
                <p style="margin: 0; font-size: 16px; color: #856404;">
                    Bonjour <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong>,<br>
                    Votre commande sera payée à la livraison
                </p>
            </div>

            <p style="font-size: 16px; text-align: center;">
                Votre commande <strong style="color: #ff6b35;">#{{ $order->order_number }}</strong> a été confirmée avec succès !
            </p>

            <div class="payment-highlight">
                <h3 style="margin: 20px 0 15px 0; color: #d63031; font-size: 20px;">
                    💳 Montant à préparer
                </h3>
                <div style="font-size: 32px; font-weight: bold; color: #d63031; margin: 15px 0;">
                    {{ number_format($order->total, 0, ',', ' ') }} FCFA
                </div>
                <p style="margin: 10px 0 0 0; color: #856404; font-style: italic;">
                    Montant exact à payer lors de la réception
                </p>
            </div>

            <div class="delivery-instructions">
                <h3 style="margin: 0 0 15px 0; color: #1976d2;">
                    🚚 Instructions de livraison
                </h3>
                <p style="margin: 0 0 10px 0;">
                    📦 <strong>Votre colis sera bientôt en route</strong> vers votre adresse de livraison.
                </p>
                <p style="margin: 0 0 10px 0;">
                    📞 <strong>Notre livreur vous appellera au <span class="highlight-text">{{ $order->shipping_phone }}</span></strong> pour convenir d'un créneau de livraison.
                </p>
                <p style="margin: 0; font-style: italic; color: #666;">
                    ⚠️ Gardez votre téléphone à portée de main pour ne pas manquer l'appel !
                </p>
            </div>

            <div class="checklist">
                <h3 style="margin: 0 0 15px 0; color: #333;">
                    📋 Checklist avant la livraison
                </h3>
                <div class="checklist-item">
                    <span>Préparez le montant exact : <strong>{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong></span>
                </div>
                <div class="checklist-item">
                    <span>Gardez votre téléphone <strong>{{ $order->shipping_phone }}</strong> allumé</span>
                </div>
                <div class="checklist-item">
                    <span>Vérifiez que quelqu'un sera présent à l'adresse de livraison</span>
                </div>
                <div class="checklist-item">
                    <span>Préparez une pièce d'identité si nécessaire</span>
                </div>
            </div>

            <div class="important-notes">
                <h3 style="margin: 0 0 15px 0; color: #e65100;">
                    ⚠️ Informations importantes
                </h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><strong>Paiement uniquement en espèces</strong> lors de la livraison</li>
                    <li><strong>Montant exact requis</strong> - notre livreur n'a pas toujours la monnaie</li>
                    <li><strong>V��rifiez votre commande</strong> avant de payer</li>
                    <li><strong>Demandez un reçu</strong> après le paiement</li>
                    <li><strong>En cas d'absence</strong>, le livreur vous recontactera</li>
                </ul>
            </div>

            <div class="order-summary">
                <h3 style="margin: 0 0 15px 0;">📦 Résumé de votre commande</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <p style="margin: 0 0 5px 0; font-weight: bold;">Numéro :</p>
                        <p style="margin: 0; color: #666;">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; font-weight: bold;">Date :</p>
                        <p style="margin: 0; color: #666;">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <p style="margin: 0 0 5px 0; font-weight: bold;">Adresse de livraison :</p>
                    <p style="margin: 0; color: #666;">
                        {{ $order->shipping_address_line_1 }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                        {{ $order->shipping_country }}
                        @if($order->notes)
                            <br><em style="color: #ff6b35;">Note : {{ $order->notes }}</em>
                        @endif
                    </p>
                </div>

                @if($order->items && $order->items->count() > 0)
                    <div class="order-items">
                        <h4 style="margin: 15px 0 10px 0;">Articles commandés :</h4>
                        @foreach($order->items as $item)
                            <div class="order-item">
                                <div class="item-details">
                                    <strong>{{ $item->product_name }}</strong><br>
                                    <span style="color: #666; font-size: 14px;">
                                        Quantité : {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                                <div class="item-price">
                                    {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="total-amount">
                💰 TOTAL À PAYER : {{ number_format($order->total, 0, ',', ' ') }} FCFA
            </div>

            <div class="warning-box">
                <p style="margin: 0; font-weight: bold; color: #856404;">
                    🔒 <strong>Sécurité :</strong>
                </p>
                <p style="margin: 5px 0 0 0; color: #856404; font-size: 14px;">
                    Notre livreur portera un badge SunuBoutique et pourra vous présenter une pièce d'identité. 
                    En cas de doute, n'hésitez pas à nous appeler pour vérifier.
                </p>
            </div>

            <div class="contact-info">
                <h3 style="margin: 0 0 15px 0; color: #155724;">
                    📞 Besoin d'aide ?
                </h3>
                <p style="margin: 0 0 10px 0;">
                    Pour toute question ou modification de dernière minute :
                </p>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>📧 Email : <strong>comedie442@gmail.com</strong></li>
                    <li>📱 Téléphone : <strong>+221 XX XXX XX XX</strong></li>
                    <li>⏰ Disponible : Lundi - Samedi, 8h - 20h</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p style="font-size: 18px; color: #ff6b35; margin: 0; font-weight: bold;">
                    🙏 Merci de votre confiance !
                </p>
                <p style="margin: 10px 0 0 0; color: #666;">
                    Nous nous réjouissons de vous livrer vos articles dans les meilleurs délais.
                </p>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0; font-size: 18px; font-weight: bold;">
                🛍️ SunuBoutique
            </p>
            <p style="margin: 0 0 15px 0; font-size: 14px;">
                Votre boutique en ligne de confiance au Sénégal
            </p>
            <p style="margin: 0; font-size: 12px; opacity: 0.8;">
                📧 <a href="mailto:comedie442@gmail.com">comedie442@gmail.com</a> | 
                📱 +221 XX XXX XX XX<br>
                Cet email a été envoyé automatiquement - Ne pas répondre directement
            </p>
        </div>
    </div>
</body>
</html>