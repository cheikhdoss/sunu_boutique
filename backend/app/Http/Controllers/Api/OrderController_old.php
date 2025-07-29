<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Get all orders for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'deliveryAddress'])
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'date' => $order->created_at,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total' => $order->total,
                'invoice_url' => $order->invoice_url,
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ];
                }),
                'delivery_address' => $order->deliveryAddress ? [
                    'id' => $order->deliveryAddress->id,
                    'label' => $order->deliveryAddress->label,
                    'first_name' => $order->deliveryAddress->first_name,
                    'last_name' => $order->deliveryAddress->last_name,
                    'address' => $order->deliveryAddress->address,
                    'city' => $order->deliveryAddress->city,
                    'postal_code' => $order->deliveryAddress->postal_code,
                    'country' => $order->deliveryAddress->country,
                    'phone' => $order->deliveryAddress->phone,
                ] : null
            ];
        });

        return response()->json([
            'orders' => $formattedOrders
        ]);
    }

    /**
     * Create a new order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customerInfo.firstName' => 'required|string|max:255',
            'customerInfo.lastName' => 'required|string|max:255',
            'customerInfo.email' => 'required|email|max:255',
            'customerInfo.phone' => 'required|string|max:20',
            'deliveryAddress.street' => 'required|string|max:255',
            'deliveryAddress.city' => 'required|string|max:255',
            'deliveryAddress.postalCode' => 'required|string|max:10',
            'deliveryAddress.country' => 'required|string|max:255',
            'deliveryAddress.additionalInfo' => 'nullable|string|max:500',
            'paymentMethod' => 'required|in:online,cash_on_delivery',
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'totalAmount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $user = $request->user();

            // Créer la commande
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'order_number' => 'CMD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_method' => $data['paymentMethod'],
                'payment_status' => 'pending',
                'total' => $data['totalAmount'],
                'customer_info' => $data['customerInfo'],
                'delivery_address' => $data['deliveryAddress'],
            ]);

            // Ajouter les articles de la commande
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);
            }

            // Charger les relations pour la réponse
            $order->load('items');

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Order creation error', [
                'error' => $e->getMessage(),
                'user_id' => $user ? $user->id : null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande'
            ], 500);
        }
    }

    /**
     * Get a specific order
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Vérifier que la commande appartient à l'utilisateur connecté
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $order->load(['items.product', 'deliveryAddress']);

        $formattedOrder = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'date' => $order->created_at,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'total' => $order->total,
            'invoice_url' => $order->invoice_url,
            'notes' => $order->notes,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ];
            }),
            'delivery_address' => $order->deliveryAddress ? [
                'id' => $order->deliveryAddress->id,
                'label' => $order->deliveryAddress->label,
                'first_name' => $order->deliveryAddress->first_name,
                'last_name' => $order->deliveryAddress->last_name,
                'address' => $order->deliveryAddress->address,
                'city' => $order->deliveryAddress->city,
                'postal_code' => $order->deliveryAddress->postal_code,
                'country' => $order->deliveryAddress->country,
                'phone' => $order->deliveryAddress->phone,
            ] : null
        ];

        return response()->json([
            'order' => $formattedOrder
        ]);
    }

    /**
     * Cancel an order (only if status is 'en_attente')
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        // Vérifier que la commande appartient à l'utilisateur connecté
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        if (!$order->canBeCancelled()) {
            return response()->json([
                'message' => 'Cette commande ne peut plus être annulée'
            ], 422);
        }

        $order->update([
            'status' => 'annulee',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'message' => 'Commande annulée avec succès'
        ]);
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Request $request, Order $order): JsonResponse
    {
        // Vérifier que la commande appartient à l'utilisateur connecté
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        if (!$order->invoice_url && !$order->isDelivered()) {
            return response()->json([
                'message' => 'Facture non disponible'
            ], 404);
        }

        // Dans une vraie application, vous généreriez et retourneriez le PDF
        // Pour l'instant, on retourne juste l'URL
        return response()->json([
            'invoice_url' => $order->invoice_url,
            'message' => 'Facture prête au téléchargement'
        ]);
    }

    /**
     * Récupérer les commandes de l'utilisateur connecté (méthode alternative)
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