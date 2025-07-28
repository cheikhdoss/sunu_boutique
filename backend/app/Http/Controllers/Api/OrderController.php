<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
                'delivery_address' => [
                    'id' => $order->deliveryAddress->id,
                    'label' => $order->deliveryAddress->label,
                    'first_name' => $order->deliveryAddress->first_name,
                    'last_name' => $order->deliveryAddress->last_name,
                    'address' => $order->deliveryAddress->address,
                    'city' => $order->deliveryAddress->city,
                    'postal_code' => $order->deliveryAddress->postal_code,
                    'country' => $order->deliveryAddress->country,
                    'phone' => $order->deliveryAddress->phone,
                ]
            ];
        });

        return response()->json([
            'orders' => $formattedOrders
        ]);
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
            'delivery_address' => [
                'id' => $order->deliveryAddress->id,
                'label' => $order->deliveryAddress->label,
                'first_name' => $order->deliveryAddress->first_name,
                'last_name' => $order->deliveryAddress->last_name,
                'address' => $order->deliveryAddress->address,
                'city' => $order->deliveryAddress->city,
                'postal_code' => $order->deliveryAddress->postal_code,
                'country' => $order->deliveryAddress->country,
                'phone' => $order->deliveryAddress->phone,
            ]
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

        if ($order->status !== 'en_attente') {
            return response()->json([
                'message' => 'Cette commande ne peut plus être annulée'
            ], 422);
        }

        $order->update(['status' => 'annulee']);

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

        if (!$order->invoice_url) {
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
}