<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->order_number }} - {{ $company['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .company-details {
            color: #666;
            line-height: 1.6;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .invoice-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
        }
        .billing-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .bill-to, .ship-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 5px;
        }
        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px 8px;
            text-align: left;
        }
        th {
            background: #e74c3c;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            width: 50%;
            margin-left: auto;
            margin-top: 20px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .total-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            width: 120px;
        }
        .grand-total {
            border-top: 2px solid #e74c3c;
            padding-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
        }
        .payment-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            border-left: 4px solid #28a745;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-details">
                {{ $company['address'] }}<br>
                Tél: {{ $company['phone'] }}<br>
                Email: {{ $company['email'] }}<br>
                Web: {{ $company['website'] }}<br>
                NINEA: {{ $company['ninea'] }}
            </div>
        </div>
        <div class="invoice-info">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-details">
                <div class="invoice-number">N° {{ $order->order_number }}</div>
                <div style="margin-top: 10px;">
                    <strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                    <strong>Échéance:</strong> {{ $order->created_at->addDays(30)->format('d/m/Y') }}<br>
                    <strong>Statut:</strong> 
                    <span class="status-badge status-{{ $order->payment_status === 'paid' ? 'paid' : 'pending' }}">
                        {{ $order->payment_status === 'paid' ? 'Payée' : 'En attente' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations client -->
    <div class="billing-section">
        <div class="bill-to">
            <div class="section-title">Facturé à</div>
            <div class="customer-info">
                <strong>{{ $customer['name'] }}</strong><br>
                {{ $customer['email'] }}<br>
                @if($customer['phone'])
                    Tél: {{ $customer['phone'] }}<br>
                @endif
                {{ $customer['address'] }}<br>
                {{ $customer['city'] }}
            </div>
        </div>
        <div class="ship-to">
            <div class="section-title">Détails de la commande</div>
            <div class="customer-info">
                <strong>Commande:</strong> {{ $order->order_number }}<br>
                <strong>Date:</strong> {{ $order->created_at->format('d/m/Y à H:i') }}<br>
                <strong>Paiement:</strong> 
                @php
                    $paymentLabel = match($order->payment_method) {
                        'paydunya' => 'PayDunya',
                        'cash_on_delivery' => 'Paiement à la livraison',
                        'bank_transfer' => 'Virement bancaire',
                        'mobile_money' => 'Mobile Money',
                        default => $order->payment_method ?? 'Non défini',
                    };
                @endphp
                {{ $paymentLabel }}<br>
                <strong>Statut:</strong> 
                @php
                    $statusLabel = match($order->status) {
                        'pending', 'en_attente' => 'En attente',
                        'processing', 'en_cours' => 'En cours',
                        'shipped', 'expediee' => 'Expédiée',
                        'delivered', 'livree' => 'Livrée',
                        'cancelled', 'annulee' => 'Annulée',
                        default => ucfirst($order->status),
                    };
                @endphp
                {{ $statusLabel }}
            </div>
        </div>
    </div>

    <!-- Articles -->
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Article</th>
                <th style="width: 15%;">Prix unitaire</th>
                <th style="width: 10%;">Quantité</th>
                <th style="width: 25%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product ? $item->product->name : $item->product_name }}</strong>
                    @if($item->product && $item->product->sku)
                        <br><small style="color: #666;">SKU: {{ $item->product->sku }}</small>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals-section">
        <div class="total-row">
            <div class="total-label">Sous-total:</div>
            <div class="total-value">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="total-row">
            <div class="total-label">TVA ({{ $tax_rate }}%):</div>
            <div class="total-value">{{ number_format($tax_amount, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="total-row grand-total">
            <div class="total-label">TOTAL:</div>
            <div class="total-value">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    <!-- Informations de paiement -->
    @if($order->payment_status === 'paid')
    <div class="payment-info">
        <strong>✓ Paiement reçu</strong><br>
        Méthode: {{ $paymentLabel }}<br>
        Date: {{ $order->updated_at->format('d/m/Y à H:i') }}
    </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - Merci pour votre confiance !</p>
        <p>Cette facture a été générée automatiquement le {{ $generated_at->format('d/m/Y à H:i') }}</p>
        <p>Pour toute question, contactez-nous à {{ $company['email'] }} ou {{ $company['phone'] }}</p>
    </div>
</body>
</html>