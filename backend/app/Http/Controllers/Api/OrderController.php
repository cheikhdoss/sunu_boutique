<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\InvoiceService;
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
            ->with(['items', 'deliveryAddress'])
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
        // Normaliser le paymentMethod
        $requestData = $request->all();
        if (isset($requestData['paymentMethod'])) {
            $requestData['paymentMethod'] = strtolower($requestData['paymentMethod']);
        }
        
        $validator = Validator::make($requestData, [
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

            // Créer la commande avec la structure correcte de la table
            $customerInfo = $data['customerInfo'];
            $deliveryAddress = $data['deliveryAddress'];
            
            $order = Order::create([
                'user_id' => $user ? $user->id : null, // Null pour les commandes invités
                'order_number' => 'CMD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_method' => $data['paymentMethod'],
                'payment_status' => 'pending',
                'total' => $data['totalAmount'],
                'subtotal' => $data['totalAmount'],
                
                // Informations de livraison
                'shipping_first_name' => $customerInfo['firstName'],
                'shipping_last_name' => $customerInfo['lastName'],
                'shipping_address_line_1' => $deliveryAddress['street'],
                'shipping_city' => $deliveryAddress['city'],
                'shipping_postal_code' => $deliveryAddress['postalCode'],
                'shipping_country' => $deliveryAddress['country'],
                'shipping_phone' => $customerInfo['phone'],
                
                // Informations de facturation (même que livraison pour simplifier)
                'billing_first_name' => $customerInfo['firstName'],
                'billing_last_name' => $customerInfo['lastName'],
                'billing_address_line_1' => $deliveryAddress['street'],
                'billing_city' => $deliveryAddress['city'],
                'billing_postal_code' => $deliveryAddress['postalCode'],
                'billing_country' => $deliveryAddress['country'],
                'billing_phone' => $customerInfo['phone'],
                
                // Informations client en JSON pour l'OrderObserver
                'customer_info' => json_encode([
                    'firstName' => $customerInfo['firstName'],
                    'lastName' => $customerInfo['lastName'],
                    'email' => $customerInfo['email'],
                    'phone' => $customerInfo['phone']
                ]),
                
                // Notes additionnelles
                'notes' => $deliveryAddress['additionalInfo'] ?? null,
            ]);

            // Ajouter les articles de la commande
            foreach ($data['items'] as $item) {
                // Récupérer les informations du produit
                $product = Product::find($item['productId']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['productId'],
                    'product_name' => $product ? $product->name : 'Produit inconnu',
                    'product_description' => $product ? $product->description : null,
                    'product_sku' => $product ? $product->sku : null,
                    'product_image' => $product ? $product->image : null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
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

    /**
     * Récupérer les détails d'une commande (accès public pour les pages de paiement)
     */
    public function getOrderDetails($orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            $formattedOrder = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'invoice_url' => $order->invoice_url,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedOrder
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching order details', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable'
            ], 404);
        }
    }

    /**
     * Générer une facture pour une commande
     */
    public function generateInvoice($orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);
            $invoiceService = new InvoiceService();

            // Vérifier que la facture peut être générée
            if (!$invoiceService->canGenerateInvoice($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La facture ne peut être générée que pour les commandes payées ou confirmées'
                ], 400);
            }

            $result = $invoiceService->generateInvoice($order);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'invoice_url' => $result['url'],
                    'filename' => $result['filename'],
                    'message' => $result['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error generating invoice', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération de la facture'
            ], 500);
        }
    }

    /**
     * Envoyer l'email de confirmation de commande
     */
    public function sendConfirmationEmail($orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            // Envoyer l'email de confirmation spécifique
            $this->sendOrderConfirmationEmail($order);

            // Pour les commandes à la livraison, on confirme directement la commande
            if ($order->payment_method === 'cash_on_delivery') {
                $order->update(['status' => 'confirmed']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Email de confirmation envoyé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending confirmation email', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email de confirmation'
            ], 500);
        }
    }

    /**
     * Confirmer le paiement à la livraison
     */
    public function confirmCashOnDeliveryPayment($orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            // Vérifier que c'est bien une commande à la livraison
            if ($order->payment_method !== 'cash_on_delivery') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette commande n\'est pas un paiement à la livraison'
                ], 400);
            }

            // Vérifier que la commande n'est pas déjà payée
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette commande est déjà marquée comme payée'
                ], 400);
            }

            // Marquer la commande comme payée
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'delivered' // Marquer comme livrée puisque le paiement est reçu
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paiement à la livraison confirmé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error confirming cash on delivery payment', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation du paiement'
            ], 500);
        }
    }

    /**
     * Envoyer l'email de confirmation de commande
     */
    private function sendOrderConfirmationEmail(Order $order): void
    {
        try {
            // Déterminer l'email du destinataire
            $recipientEmail = $this->getRecipientEmail($order);
            $recipientName = $this->getRecipientName($order);

            if (!$recipientEmail) {
                \Log::warning('No recipient email found for order confirmation', ['order_id' => $order->id]);
                return;
            }

            // Choisir le template selon le mode de paiement
            if ($order->payment_method === 'cash_on_delivery') {
                // Template spécifique pour paiement à la livraison
                \Mail::to($recipientEmail, $recipientName)
                    ->send(new \App\Mail\CashOnDeliveryMail($order));
                
                \Log::info('Cash on delivery email sent', [
                    'order_id' => $order->id,
                    'recipient' => $recipientEmail
                ]);
            } else {
                // Template général pour autres modes de paiement
                \Mail::to($recipientEmail, $recipientName)
                    ->send(new \App\Mail\OrderConfirmationMail($order));
                
                \Log::info('Order confirmation email sent', [
                    'order_id' => $order->id,
                    'recipient' => $recipientEmail
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtenir l'email du destinataire
     */
    private function getRecipientEmail(Order $order): ?string
    {
        // Priorité 1: Email de l'utilisateur connecté
        if ($order->user && $order->user->email) {
            return $order->user->email;
        }

        // Priorité 2: Email dans les informations client (JSON)
        if ($order->customer_info) {
            $customerInfo = is_array($order->customer_info) ? $order->customer_info : json_decode($order->customer_info, true);
            if ($customerInfo && isset($customerInfo['email'])) {
                return $customerInfo['email'];
            }
        }

        return null;
    }

    /**
     * Obtenir le nom du destinataire
     */
    private function getRecipientName(Order $order): string
    {
        // Priorité 1: Nom de l'utilisateur connecté
        if ($order->user && $order->user->name) {
            return $order->user->name;
        }

        // Priorité 2: Nom de livraison
        if ($order->shipping_first_name && $order->shipping_last_name) {
            return $order->shipping_first_name . ' ' . $order->shipping_last_name;
        }

        // Priorité 3: Nom dans les informations client (JSON)
        if ($order->customer_info) {
            $customerInfo = is_array($order->customer_info) ? $order->customer_info : json_decode($order->customer_info, true);
            if ($customerInfo && isset($customerInfo['firstName'], $customerInfo['lastName'])) {
                return $customerInfo['firstName'] . ' ' . $customerInfo['lastName'];
            }
        }

        return 'Client';
    }
}