<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total des commandes', Order::count())
                ->description('Nombre total de commandes')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart($this->getOrderTrend())
                ->color('success'),

            Stat::make('Commandes en attente', Order::where('status', 'pending')->count())
                ->description('Commandes à traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Chiffre d\'affaires', number_format(Order::where('payment_status', 'paid')->sum('total'), 0, ',', ' ') . ' XOF')
                ->description('Total des ventes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart($this->getRevenueTrend())
                ->color('success'),
        ];
    }

    protected function getOrderTrend(): array
    {
        return Order::query()
            ->whereBetween('created_at', [
                now()->subDays(7)->startOfDay(),
                now()->endOfDay(),
            ])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    protected function getRevenueTrend(): array
    {
        return Order::query()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [
                now()->subDays(7)->startOfDay(),
                now()->endOfDay(),
            ])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }
}