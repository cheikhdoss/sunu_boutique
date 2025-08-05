<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Période actuelle (ce mois)
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Chiffre d'affaires
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $currentMonthRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $currentMonth)
            ->sum('total');
        $previousMonthRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->sum('total');
        
        $revenueGrowth = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 0;

        // Commandes
        $totalOrders = Order::count();
        $currentMonthOrders = Order::where('created_at', '>=', $currentMonth)->count();
        $previousMonthOrders = Order::whereBetween('created_at', [$previousMonth, $currentMonth])->count();
        
        $ordersGrowth = $previousMonthOrders > 0 
            ? (($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders) * 100 
            : 0;

        // Clients
        $totalCustomers = User::where('is_admin', false)->count();
        $currentMonthCustomers = User::where('is_admin', false)
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $previousMonthCustomers = User::where('is_admin', false)
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->count();
        
        $customersGrowth = $previousMonthCustomers > 0 
            ? (($currentMonthCustomers - $previousMonthCustomers) / $previousMonthCustomers) * 100 
            : 0;

        // Produits
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_visible', true)->count();
        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();

        // Commandes en attente
        $pendingOrders = Order::whereIn('status', ['pending', 'en_attente', 'processing'])->count();

        // Panier moyen
        $averageOrderValue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->avg('total');

        return [
            Stat::make('Chiffre d\'affaires total', number_format($totalRevenue, 0, ',', ' ') . ' FCFA')
                ->description('Ce mois: ' . number_format($currentMonthRevenue, 0, ',', ' ') . ' FCFA')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueChart()),

            Stat::make('Total Commandes', $totalOrders)
                ->description(($ordersGrowth >= 0 ? '+' : '') . number_format($ordersGrowth, 1) . '% ce mois')
                ->descriptionIcon($ordersGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersGrowth >= 0 ? 'success' : 'danger')
                ->chart($this->getOrdersChart()),

            Stat::make('Clients', $totalCustomers)
                ->description(($customersGrowth >= 0 ? '+' : '') . number_format($customersGrowth, 1) . '% ce mois')
                ->descriptionIcon($customersGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customersGrowth >= 0 ? 'success' : 'danger')
                ->chart($this->getCustomersChart()),

            Stat::make('Produits Actifs', $activeProducts)
                ->description($outOfStockProducts . ' en rupture de stock')
                ->descriptionIcon($outOfStockProducts > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($outOfStockProducts > 0 ? 'warning' : 'success'),

            Stat::make('Commandes en Attente', $pendingOrders)
                ->description('À traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Panier Moyen', number_format($averageOrderValue, 0, ',', ' ') . ' FCFA')
                ->description('30 derniers jours')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }

    private function getRevenueChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total');
            $data[] = $revenue;
        }
        return $data;
    }

    private function getOrdersChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $orders = Order::whereDate('created_at', $date)->count();
            $data[] = $orders;
        }
        return $data;
    }

    private function getCustomersChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $customers = User::where('is_admin', false)
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $customers;
        }
        return $data;
    }
}