<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Répartition des Commandes par Statut';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($ordersByStatus as $status => $count) {
            $labels[] = $this->getStatusLabel($status);
            $data[] = $count;
            $colors[] = $this->getStatusColor($status);
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
        return 'doughnut';
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

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending', 'en_attente' => 'En attente',
            'processing', 'en_cours' => 'En cours',
            'shipped', 'expediee' => 'Expédiée',
            'delivered', 'livree' => 'Livrée',
            'cancelled', 'annulee' => 'Annulée',
            default => ucfirst($status),
        };
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'pending', 'en_attente' => 'rgba(245, 158, 11, 0.8)', // Orange
            'processing', 'en_cours' => 'rgba(59, 130, 246, 0.8)', // Bleu
            'shipped', 'expediee' => 'rgba(139, 92, 246, 0.8)', // Violet
            'delivered', 'livree' => 'rgba(16, 185, 129, 0.8)', // Vert
            'cancelled', 'annulee' => 'rgba(239, 68, 68, 0.8)', // Rouge
            default => 'rgba(156, 163, 175, 0.8)', // Gris
        };
    }
}