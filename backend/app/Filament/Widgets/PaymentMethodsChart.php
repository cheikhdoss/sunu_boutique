<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentMethodsChart extends ChartWidget
{
    protected static ?string $heading = 'Méthodes de Paiement Utilisées';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $paymentMethods = Order::where('payment_status', 'paid')
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($paymentMethods as $method => $count) {
            $labels[] = $this->getPaymentMethodLabel($method);
            $data[] = $count;
            $colors[] = $this->getPaymentMethodColor($method);
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    private function getPaymentMethodLabel(?string $method): string
    {
        return match($method) {
            'paydunya' => 'PayDunya',
            'cash_on_delivery' => 'Paiement à la livraison',
            'bank_transfer' => 'Virement bancaire',
            'mobile_money' => 'Mobile Money',
            'credit_card' => 'Carte de crédit',
            null => 'Non défini',
            default => ucfirst($method ?? 'Inconnu'),
        };
    }

    private function getPaymentMethodColor(?string $method): string
    {
        return match($method) {
            'paydunya' => 'rgba(59, 130, 246, 0.8)', // Bleu
            'cash_on_delivery' => 'rgba(16, 185, 129, 0.8)', // Vert
            'bank_transfer' => 'rgba(139, 92, 246, 0.8)', // Violet
            'mobile_money' => 'rgba(245, 158, 11, 0.8)', // Orange
            'credit_card' => 'rgba(236, 72, 153, 0.8)', // Rose
            null => 'rgba(156, 163, 175, 0.8)', // Gris
            default => 'rgba(99, 102, 241, 0.8)', // Indigo
        };
    }
}