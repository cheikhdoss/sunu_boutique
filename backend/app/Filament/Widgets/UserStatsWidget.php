<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Période actuelle (ce mois)
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Statistiques des utilisateurs
        $totalUsers = User::count();
        $totalClients = User::where('is_admin', false)->count();
        $totalAdmins = User::where('is_admin', true)->count();
        
        // Nouveaux utilisateurs ce mois
        $newUsersThisMonth = User::where('created_at', '>=', $currentMonth)->count();
        $newUsersLastMonth = User::whereBetween('created_at', [$previousMonth, $currentMonth])->count();
        
        $userGrowth = $newUsersLastMonth > 0 
            ? (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 
            : 0;

        // Utilisateurs actifs (avec au moins une commande)
        $activeUsers = User::has('orders')->count();
        $activeUsersRate = $totalClients > 0 ? ($activeUsers / $totalClients) * 100 : 0;

        // Utilisateurs avec email vérifié
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $verificationRate = $totalUsers > 0 ? ($verifiedUsers / $totalUsers) * 100 : 0;

        // Top clients (par nombre de commandes)
        $topClientByOrders = User::withCount('orders')
            ->where('is_admin', false)
            ->orderBy('orders_count', 'desc')
            ->first();

        // Top client (par montant dépensé)
        $topClientByAmount = User::select('users.*')
            ->selectRaw('COALESCE(SUM(orders.total), 0) as total_spent')
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->where('users.is_admin', false)
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id')
            ->orderBy('total_spent', 'desc')
            ->first();

        // Utilisateurs récents (7 derniers jours)
        $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        return [
            Stat::make('Total Utilisateurs', $totalUsers)
                ->description($totalClients . ' clients, ' . $totalAdmins . ' admins')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart($this->getUsersChart()),

            Stat::make('Nouveaux ce mois', $newUsersThisMonth)
                ->description(($userGrowth >= 0 ? '+' : '') . number_format($userGrowth, 1) . '% vs mois dernier')
                ->descriptionIcon($userGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($userGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Utilisateurs Actifs', $activeUsers)
                ->description(number_format($activeUsersRate, 1) . '% des clients')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Emails Vérifiés', $verifiedUsers)
                ->description(number_format($verificationRate, 1) . '% de vérification')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($verificationRate >= 80 ? 'success' : ($verificationRate >= 50 ? 'warning' : 'danger')),

            Stat::make('Top Client (Commandes)', $topClientByOrders ? $topClientByOrders->name : 'Aucun')
                ->description($topClientByOrders ? $topClientByOrders->orders_count . ' commandes' : 'Pas de commandes')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make('Top Client (Montant)', $topClientByAmount ? $topClientByAmount->name : 'Aucun')
                ->description($topClientByAmount ? number_format($topClientByAmount->total_spent, 0, ',', ' ') . ' FCFA' : '0 FCFA')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Nouveaux (7 jours)', $recentUsers)
                ->description('Cette semaine')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Taux de Conversion', number_format($activeUsersRate, 1) . '%')
                ->description('Clients qui ont commandé')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($activeUsersRate >= 30 ? 'success' : ($activeUsersRate >= 15 ? 'warning' : 'danger')),
        ];
    }

    private function getUsersChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $users = User::whereDate('created_at', $date)->count();
            $data[] = $users;
        }
        return $data;
    }
}