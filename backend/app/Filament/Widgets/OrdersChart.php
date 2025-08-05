<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Statistiques des commandes';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getOrdersData();

        return [
            'datasets' => [
                [
                    'label' => 'Commandes',
                    'data' => $data['totals'],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOrdersData(): array
    {
        $orders = Order::query()
            ->whereBetween('created_at', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay(),
            ])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $totals = [];

        foreach ($orders as $order) {
            $labels[] = Carbon::parse($order->date)->format('d M');
            $totals[] = $order->total;
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
        ];
    }
}