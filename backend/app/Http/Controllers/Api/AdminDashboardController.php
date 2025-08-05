<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Obtenir les statistiques générales du tableau de bord
     */
    public function getStats(): JsonResponse
    {
        // Période actuelle (ce mois)
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Statistiques générales
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $currentMonthRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $currentMonth)
            ->sum('total');
        $previousMonthRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->sum('total');
        
        $totalOrders = Order::count();
        $currentMonthOrders = Order::where('created_at', '>=', $currentMonth)->count();
        $previousMonthOrders = Order::whereBetween('created_at', [$previousMonth, $currentMonth])->count();
        
        $totalCustomers = User::where('is_admin', false)->count();
        $currentMonthCustomers = User::where('is_admin', false)
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $previousMonthCustomers = User::where('is_admin', false)
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->count();
        
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_visible', true)->count();
        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();
        
        // Calcul des pourcentages de croissance
        $revenueGrowth = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 0;
        
        $ordersGrowth = $previousMonthOrders > 0 
            ? (($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders) * 100 
            : 0;
        
        $customersGrowth = $previousMonthCustomers > 0 
            ? (($currentMonthCustomers - $previousMonthCustomers) / $previousMonthCustomers) * 100 
            : 0;

        // Commandes par statut
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Commandes par méthode de paiement
        $ordersByPaymentMethod = Order::select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->pluck('count', 'payment_method');

        return response()->json([
            'revenue' => [
                'total' => round($totalRevenue, 2),
                'current_month' => round($currentMonthRevenue, 2),
                'previous_month' => round($previousMonthRevenue, 2),
                'growth_percentage' => round($revenueGrowth, 2)
            ],
            'orders' => [
                'total' => $totalOrders,
                'current_month' => $currentMonthOrders,
                'previous_month' => $previousMonthOrders,
                'growth_percentage' => round($ordersGrowth, 2),
                'by_status' => $ordersByStatus,
                'by_payment_method' => $ordersByPaymentMethod
            ],
            'customers' => [
                'total' => $totalCustomers,
                'current_month' => $currentMonthCustomers,
                'previous_month' => $previousMonthCustomers,
                'growth_percentage' => round($customersGrowth, 2)
            ],
            'products' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
                'out_of_stock' => $outOfStockProducts,
                'categories_count' => Category::count()
            ]
        ]);
    }

    /**
     * Obtenir les produits les plus vendus
     */
    public function getTopProducts(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $period = $request->get('period', 'all'); // all, month, week

        $query = OrderItem::select(
            'product_id',
            'products.name',
            'products.price',
            'products.image',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
        )
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.payment_status', 'paid');

        switch ($period) {
            case 'month':
                $query->where('orders.created_at', '>=', Carbon::now()->startOfMonth());
                break;
            case 'week':
                $query->where('orders.created_at', '>=', Carbon::now()->startOfWeek());
                break;
        }

        $topProducts = $query->groupBy('product_id', 'products.name', 'products.price', 'products.image')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'period' => $period,
            'products' => $topProducts->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'price' => round($item->price, 2),
                    'image' => $item->image,
                    'total_sold' => $item->total_sold,
                    'total_revenue' => round($item->total_revenue, 2)
                ];
            })
        ]);
    }
}