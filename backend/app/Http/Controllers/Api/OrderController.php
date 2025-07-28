<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Récupérer les commandes de l'utilisateur connecté
     */
    public function getUserOrders(Request $request)
    {
        $user = $request->user();
        
        $orders = $user->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'orders' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Récupérer les détails d'une commande spécifique
     */
    public function getOrderDetails(Request $request, $id)
    {
        $user = $request->user();
        
        $order = $user->orders()
            ->with(['items.product.category'])
            ->findOrFail($id);

        return response()->json([
            'order' => $order,
            'items' => $order->items,
            'shipping_address' => [
                'full_name' => $order->shipping_full_name,
                'company' => $order->shipping_company,
                'address_line_1' => $order->shipping_address_line_1,
                'address_line_2' => $order->shipping_address_line_2,
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'postal_code' => $order->shipping_postal_code,
                'country' => $order->shipping_country,
                'phone' => $order->shipping_phone,
            ],
            'billing_address' => [
                'full_name' => $order->billing_full_name,
                'company' => $order->billing_company,
                'address_line_1' => $order->billing_address_line_1,
                'address_line_2' => $order->billing_address_line_2,
                'city' => $order->billing_city,
                'state' => $order->billing_state,
                'postal_code' => $order->billing_postal_code,
                'country' => $order->billing_country,
                'phone' => $order->billing_phone,
            ]
        ]);
    }

    /**
     * Annuler une commande (si possible)
     */
    public function cancelOrder(Request $request, $id)
    {
        $user = $request->user();
        
        $order = $user->orders()->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return response()->json([
                'message' => 'Cette commande ne peut plus être annulée'
            ], 400);
        }

        try {
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            // Remettre les produits en stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increaseStock($item->quantity);
                }
            }

            return response()->json([
                'message' => 'Commande annulée avec succès',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'annulation de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger la facture d'une commande (préparation)
     */
    public function downloadInvoice(Request $request, $id)
    {
        $user = $request->user();
        
        $order = $user->orders()->findOrFail($id);

        if (!$order->isDelivered()) {
            return response()->json([
                'message' => 'La facture n\'est disponible que pour les commandes livrées'
            ], 400);
        }

        // TODO: Implémenter la génération de PDF
        return response()->json([
            'message' => 'Génération de facture en cours de développement',
            'order_number' => $order->order_number,
            'download_url' => '/api/orders/' . $order->id . '/invoice.pdf'
        ]);
    }

    /**
     * Récupérer les commandes récentes (pour le dashboard)
     */
    public function getRecentOrders(Request $request)
    {
        $user = $request->user();
        
        $orders = $user->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($orders);
    }

    /**
     * Rechercher dans les commandes
     */
    public function searchOrders(Request $request)
    {
        $user = $request->user();
        $query = $request->get('q', '');
        $status = $request->get('status', '');
        
        $ordersQuery = $user->orders()->with('items.product');

        if (!empty($query)) {
            $ordersQuery->where(function($q) use ($query) {
                $q->where('order_number', 'like', '%' . $query . '%')
                  ->orWhereHas('items', function($itemQuery) use ($query) {
                      $itemQuery->where('product_name', 'like', '%' . $query . '%');
                  });
            });
        }

        if (!empty($status)) {
            $ordersQuery->where('status', $status);
        }

        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'orders' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }
}