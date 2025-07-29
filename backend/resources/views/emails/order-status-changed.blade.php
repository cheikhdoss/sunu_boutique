<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre commande</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
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
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-shipped {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-delivered {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .alert-success {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .alert-warning {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ SunuBoutique</h1>
        <h2>Mise à jour de votre commande</h2>
    </div>

    <div class="content">
        <p>Bonjour {{ $order->shipping_first_name }} {{ $order->shipping_last_name }},</p>

        <p>Nous vous informons que le statut de votre commande <strong>#{{ $order->order_number }}</strong> a été mis à jour.</p>

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Nouveau statut :</strong></p>
            <span class="status-badge status-{{ $newStatus }}">
                @switch($newStatus)
                    @case('confirmed')
                        Confirmée
                        @break
                    @case('shipped')
                        Expédiée
                        @break
                    @case('delivered')
                        Livrée
                        @break
                    @case('cancelled')
                        Annulée
                        @break
                    @default
                        {{ ucfirst($newStatus) }}
                @endswitch
            </span>
        </div>

        @if($newStatus === 'confirmed')
            <div class="alert-success">
                <p style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; color: #155724;">
                    ✅ Votre commande a été confirmée avec succès !
                </p>
                <p style="margin: 0 0 10px 0;">
                    📦 <strong>Votre colis sera bientôt en route</strong> vers votre adresse de livraison.
                </p>
                <p style="margin: 0 0 10px 0;">
                    📞 <strong>Notre livreur vous appellera prochainement</strong> au numéro <strong>{{ $order->shipping_phone }}</strong> pour convenir d'un créneau de livraison qui vous convient.
                </p>
                <p style="margin: 0; font-style: italic; color: #6c757d;">
                    Merci de garder votre téléphone à portée de main pour ne pas manquer l'appel de notre livreur.
                </p>
            </div>
            
            @if($order->payment_method === 'cash_on_delivery')
                <div class="alert-warning">
                    <p style="margin: 0; font-weight: bold; color: #856404;">
                        💰 <strong>Paiement à la livraison :</strong>
                    </p>
                    <p style="margin: 5px 0 0 0; color: #856404;">
                        Préparez le montant exact de <strong>{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong> pour le paiement lors de la réception de votre commande.
                    </p>
                </div>
            @endif
        @elseif($newStatus === 'shipped')
            <div style="background-color: #cce5ff; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff; margin: 20px 0;">
                <p style="margin: 0 0 10px 0; font-weight: bold; color: #004085;">
                    📦 Votre commande a été expédiée !
                </p>
                <p style="margin: 0 0 10px 0; color: #004085;">
                    Votre colis est maintenant en route vers votre adresse de livraison.
                </p>
                <p style="margin: 0; color: #004085;">
                    📞 Notre livreur vous contactera bientôt au <strong>{{ $order->shipping_phone }}</strong> pour finaliser la livraison.
                </p>
            </div>
        @elseif($newStatus === 'delivered')
            <div style="background-color: #d1ecf1; padding: 20px; border-radius: 8px; border-left: 4px solid #17a2b8; margin: 20px 0;">
                <p style="margin: 0; font-weight: bold; color: #0c5460;">
                    🎉 Votre commande a été livrée avec succès ! Nous espérons que vous êtes satisfait(e) de votre achat.
                </p>
            </div>
        @elseif($newStatus === 'cancelled')
            <div style="background-color: #f8d7da; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545; margin: 20px 0;">
                <p style="margin: 0; font-weight: bold; color: #721c24;">
                    ❌ Votre commande a été annulée. Si vous avez des questions, n'hésitez pas à nous contacter.
                </p>
            </div>
        @endif

        <div class="order-details">
            <h3>Détails de la commande</h3>
            <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
            <p><strong>Date de commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Montant total :</strong> {{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
            <p><strong>Mode de paiement :</strong> 
                @if($order->payment_method === 'cash_on_delivery')
                    Paiement à la livraison
                @else
                    Paiement en ligne
                @endif
            </p>
            <p><strong>Adresse de livraison :</strong><br>
                {{ $order->shipping_address_line_1 }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                {{ $order->shipping_country }}
            </p>
            <p><strong>Téléphone de contact :</strong> {{ $order->shipping_phone }}</p>

            @if($order->items && $order->items->count() > 0)
                <div class="order-items">
                    <h4>Articles commandés :</h4>
                    @foreach($order->items as $item)
                        <div class="order-item">
                            <strong>{{ $item->product_name }}</strong><br>
                            Quantité : {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                            = {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($newStatus === 'delivered' && $order->payment_method === 'cash_on_delivery')
            <p><strong>Note :</strong> Si vous avez payé à la livraison, assurez-vous que le paiement a bien été effectué.</p>
        @endif

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0; font-weight: bold;">
                📞 Besoin d'aide ?
            </p>
            <p style="margin: 0;">
                Si vous avez des questions concernant votre commande ou si vous souhaitez modifier votre adresse de livraison, n'hésitez pas à nous contacter rapidement.
            </p>
        </div>

        <p>Merci de votre confiance !</p>
        <p><strong>L'équipe SunuBoutique</strong></p>
    </div>

    <div class="footer">
        <p>
            <strong>SunuBoutique</strong><br>
            Email: comedie442@gmail.com<br>
            Téléphone: +221 XX XXX XX XX<br>
        </p>
        <p>
            <small>
                Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.<br>
                Pour toute question, contactez-nous directement par téléphone ou email.
            </small>
        </p>
    </div>
</body>
</html>