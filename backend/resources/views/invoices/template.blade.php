<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #2196f3;
            padding-bottom: 20px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2196f3;
            margin-bottom: 10px;
        }
        
        .company-info {
            color: #666;
            line-height: 1.6;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2196f3;
            margin-bottom: 10px;
        }
        
        .invoice-meta {
            color: #666;
        }
        
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .invoice-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2196f3;
            margin-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background-color: #2196f3;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .total-label {
            display: table-cell;
            padding: 8px 12px;
            font-weight: bold;
            text-align: right;
            border: 1px solid #e0e0e0;
            background-color: #f8f9fa;
        }
        
        .total-value {
            display: table-cell;
            padding: 8px 12px;
            text-align: right;
            border: 1px solid #e0e0e0;
            width: 120px;
        }
        
        .grand-total .total-label,
        .grand-total .total-value {
            background-color: #2196f3;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
        
        .payment-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        .status-paid {
            background-color: #4caf50;
            color: white;
        }
        
        .status-processing {
            background-color: #ff9800;
            color: white;
        }
        
        .status-pending {
            background-color: #2196f3;
            color: white;
        }
        
        .footer {
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .notes {
            background-color: #fff3e0;
            padding: 15px;
            border-left: 4px solid #ff9800;
            margin-bottom: 20px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #e65100;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="logo">SunuBoutique</div>
                <div class="company-info">
                    Votre boutique en ligne de confiance<br>
                    Dakar, Sénégal<br>
                    Tél: +221 XX XXX XX XX<br>
                    Email: birakanembodj01@gmail.com<br>
                    Web: www.sunuboutique.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-meta">
                    <strong>N° {{ $order->order_number }}</strong><br>
                    Date: {{ $order->created_at->format('d/m/Y') }}<br>
                    @if($order->paid_at)
                        Payée le: {{ $order->paid_at->format('d/m/Y à H:i') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="bill-to">
                <div class="section-title">FACTURER À</div>
                <div>
                    <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong><br>
                    @if($order->shipping_company)
                        {{ $order->shipping_company }}<br>
                    @endif
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2)
                        {{ $order->shipping_address_line_2 }}<br>
                    @endif
                    {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
                    {{ $order->shipping_country }}<br>
                    @if($order->shipping_phone)
                        Tél: {{ $order->shipping_phone }}
                    @endif
                </div>
            </div>
            <div class="invoice-info">
                <div class="section-title">INFORMATIONS FACTURE</div>
                <div class="info-row">
                    <span class="info-label">Commande:</span>
                    {{ $order->order_number }}
                </div>
                <div class="info-row">
                    <span class="info-label">Date commande:</span>
                    {{ $order->created_at->format('d/m/Y à H:i') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Statut:</span>
                    <span class="payment-status status-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mode paiement:</span>
                    {{ $order->payment_method === 'online' ? 'PayDunya' : 'Paiement à la livraison' }}
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        @if($order->payment_status === 'paid')
        <div class="payment-info">
            <strong>✅ Paiement confirmé</strong><br>
            Cette facture a été payée le {{ $order->paid_at ? $order->paid_at->format('d/m/Y à H:i') : $order->updated_at->format('d/m/Y à H:i') }}
            @if($order->payment_method === 'online')
                via PayDunya.
            @endif
        </div>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%">Article</th>
                    <th style="width: 15%" class="text-center">Quantité</th>
                    <th style="width: 17.5%" class="text-right">Prix unitaire</th>
                    <th style="width: 17.5%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product_description)
                            <br><small style="color: #666;">{{ $item->product_description }}</small>
                        @endif
                        @if($item->product_sku)
                            <br><small style="color: #999;">SKU: {{ $item->product_sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px; color: #666;">
                        Aucun article trouvé pour cette commande
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <div class="total-label">Sous-total:</div>
                <div class="total-value">{{ number_format($order->subtotal ?? $order->total, 0, ',', ' ') }} FCFA</div>
            </div>
            @if($order->tax_amount && $order->tax_amount > 0)
            <div class="total-row">
                <div class="total-label">TVA:</div>
                <div class="total-value">{{ number_format($order->tax_amount, 0, ',', ' ') }} FCFA</div>
            </div>
            @endif
            @if($order->shipping_amount && $order->shipping_amount > 0)
            <div class="total-row">
                <div class="total-label">Livraison:</div>
                <div class="total-value">{{ number_format($order->shipping_amount, 0, ',', ' ') }} FCFA</div>
            </div>
            @else
            <div class="total-row">
                <div class="total-label">Livraison:</div>
                <div class="total-value">Gratuite</div>
            </div>
            @endif
            @if($order->discount_amount && $order->discount_amount > 0)
            <div class="total-row">
                <div class="total-label">Remise:</div>
                <div class="total-value">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</div>
            </div>
            @endif
            <div class="total-row grand-total">
                <div class="total-label">TOTAL À PAYER:</div>
                <div class="total-value">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>

        <!-- Notes -->
        @if($order->notes)
        <div class="notes">
            <div class="notes-title">Notes de commande:</div>
            {{ $order->notes }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Merci pour votre confiance !</strong></p>
            <p>Cette facture a été générée automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
            <p>Pour toute question, contactez-nous à birakanembodj01@gmail.com ou +221 XX XXX XX XX</p>
            <br>
            <p style="font-size: 9px;">
                SunuBoutique - Votre boutique en ligne de confiance<br>
                Dakar, Sénégal - www.sunuboutique.com
            </p>
        </div>
    </div>
</body>
</html>