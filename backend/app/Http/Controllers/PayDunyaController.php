<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PayDunyaService;
use App\Mail\OrderPaidNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayDunyaController extends Controller
{
    protected $payDunyaService;

    public function __construct(PayDunyaService $payDunyaService)
    {
        $this->payDunyaService = $payDunyaService;
    }

    /**
     * Initier un paiement PayDunya
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $order = Order::with('items')->findOrFail($request->order_id);

            // Vérifier que la commande est en attente de paiement
            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette commande ne peut pas être payée'
                ], 400);
            }

            // Préparer les données pour PayDunya
            $invoiceData = [
                'order_id' => $order->id,
                'amount' => $this->payDunyaService->formatAmount($order->total),
                'description' => "Commande #{$order->order_number} - " . config('app.name'),
                'customer_phone' => $order->shipping_phone,
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->product_name ?? 'Produit',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'description' => $item->product_description ?? '',
                    ];
                })->toArray()
            ];

            // Ajouter l'email du client s'il existe
            if ($order->user && $order->user->email) {
                $invoiceData['customer_email'] = $order->user->email;
            }

            Log::info('PayDunya invoice data', $invoiceData);

            // Créer la facture PayDunya
            $result = $this->payDunyaService->createInvoice($invoiceData);

            Log::info('PayDunya result', $result);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            $invoiceResponse = $result['data'];

            // Vérifier que le token existe
            if (!isset($invoiceResponse['token'])) {
                Log::error('PayDunya token missing', [
                    'response' => $invoiceResponse
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur PayDunya: Token manquant'
                ], 400);
            }

            // Sauvegarder les informations de paiement avec une requête SQL directe
            try {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'paydunya_invoice_token' => $invoiceResponse['token'],
                        'payment_status' => 'processing',
                        'updated_at' => now()
                    ]);
                
                Log::info('Order updated successfully', [
                    'order_id' => $order->id,
                    'token' => $invoiceResponse['token']
                ]);
            } catch (\Exception $updateException) {
                Log::error('Failed to update order', [
                    'order_id' => $order->id,
                    'error' => $updateException->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la sauvegarde des informations de paiement'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_url' => $this->payDunyaService->getPaymentUrl($invoiceResponse['token'], $invoiceResponse['response_text'] ?? null),
                    'invoice_token' => $invoiceResponse['token'],
                    'response_code' => $invoiceResponse['response_code'],
                    'description' => $invoiceResponse['description'] ?? 'Facture créée avec succès'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PayDunya payment initiation error', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initiation du paiement'
            ], 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            if (!$order->paydunya_invoice_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune facture PayDunya trouvée pour cette commande'
                ], 404);
            }

            // Vérifier le statut auprès de PayDunya
            $result = $this->payDunyaService->checkInvoiceStatus($order->paydunya_invoice_token);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            $invoiceData = $result['data'];

            // Mettre à jour le statut de la commande
            $this->updateOrderFromPayDunyaStatus($order, $invoiceData);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_status' => $order->fresh()->status,
                    'payment_status' => $order->fresh()->payment_status,
                    'paydunya_status' => $invoiceData['status'],
                    'invoice_data' => $invoiceData
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PayDunya status check error', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut'
            ], 500);
        }
    }

    /**
     * Gérer les notifications IPN de PayDunya
     */
    public function handleIPN(Request $request)
    {
        Log::info('PayDunya IPN received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        try {
            // Valider la notification IPN
            $validation = $this->payDunyaService->validateIPN($request->all());

            if (!$validation['success']) {
                Log::warning('PayDunya IPN validation failed', [
                    'error' => $validation['error'],
                    'payload' => $request->all()
                ]);

                return response('Invalid IPN', 400);
            }

            $invoiceData = $validation['data'];
            $invoiceToken = $request->input('invoice_token');

            // Trouver la commande correspondante
            $order = Order::where('paydunya_invoice_token', $invoiceToken)->first();

            if (!$order) {
                Log::warning('PayDunya IPN: Order not found', [
                    'invoice_token' => $invoiceToken
                ]);

                return response('Order not found', 404);
            }

            // Mettre à jour le statut de la commande
            $this->updateOrderFromPayDunyaStatus($order, $invoiceData);

            Log::info('PayDunya IPN processed successfully', [
                'order_id' => $order->id,
                'status' => $invoiceData['status'],
                'invoice_token' => $invoiceToken
            ]);

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('PayDunya IPN processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response('Internal Server Error', 500);
        }
    }

    /**
     * Callback de succès de paiement
     */
    public function paymentSuccess(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $provider = $request->query('provider');

            if (!$orderId || $provider !== 'paydunya') {
                return redirect(config('app.frontend_url') . '/payment/error?message=Paramètres manquants');
            }

            $order = Order::find($orderId);
            if (!$order) {
                return redirect(config('app.frontend_url') . '/payment/error?message=Commande introuvable');
            }

            // Vérifier le statut réel auprès de PayDunya
            if ($order->paydunya_invoice_token) {
                $result = $this->payDunyaService->checkInvoiceStatus($order->paydunya_invoice_token);
                if ($result['success']) {
                    $this->updateOrderFromPayDunyaStatus($order, $result['data']);
                }
            }

            return redirect(config('app.frontend_url') . '/payment/success?order_id=' . $orderId);

        } catch (\Exception $e) {
            Log::error('PayDunya success callback error', [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return redirect(config('app.frontend_url') . '/payment/error?message=Erreur technique');
        }
    }

    /**
     * Callback d'erreur de paiement
     */
    public function paymentError(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $provider = $request->query('provider');

            if ($orderId && $provider === 'paydunya') {
                $order = Order::find($orderId);
                if ($order && $order->paydunya_invoice_token) {
                    // Vérifier le statut réel
                    $result = $this->payDunyaService->checkInvoiceStatus($order->paydunya_invoice_token);
                    if ($result['success']) {
                        $this->updateOrderFromPayDunyaStatus($order, $result['data']);
                    }
                }
            }

            return redirect(config('app.frontend_url') . '/payment/error?order_id=' . $orderId);

        } catch (\Exception $e) {
            Log::error('PayDunya error callback error', [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return redirect(config('app.frontend_url') . '/payment/error?message=Erreur technique');
        }
    }

    /**
     * Mettre à jour le statut de la commande selon PayDunya
     */
    private function updateOrderFromPayDunyaStatus(Order $order, array $payDunyaData)
    {
        $updates = [];

        // Mapper les statuts PayDunya vers nos statuts
        switch ($payDunyaData['status']) {
            case 'completed':
                $updates['payment_status'] = 'paid';
                $updates['status'] = 'confirmed';
                $updates['paid_at'] = now();
                break;
            case 'cancelled':
                $updates['payment_status'] = 'failed';
                break;
            case 'pending':
                $updates['payment_status'] = 'processing';
                break;
        }

        // Ajouter les informations de transaction PayDunya
        if (isset($payDunyaData['receipt_url'])) {
            $updates['paydunya_receipt_url'] = $payDunyaData['receipt_url'];
        }

        if (isset($payDunyaData['customer'])) {
            $updates['paydunya_customer_info'] = json_encode($payDunyaData['customer']);
        }

        if (!empty($updates)) {
            try {
                $updates['updated_at'] = now();
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update($updates);

                Log::info('Order status updated from PayDunya', [
                    'order_id' => $order->id,
                    'updates' => $updates,
                    'paydunya_status' => $payDunyaData['status']
                ]);

                // Envoyer une notification par email si le paiement est confirmé
                if ($payDunyaData['status'] === 'completed' && isset($updates['payment_status']) && $updates['payment_status'] === 'paid') {
                    $this->sendPaymentConfirmationEmail($order);
                }
            } catch (\Exception $e) {
                Log::error('Failed to update order status from PayDunya', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'updates' => $updates
                ]);
            }
        }
    }

    /**
     * Envoyer un email de confirmation de paiement
     */
    private function sendPaymentConfirmationEmail(Order $order)
    {
        try {
            // Charger les relations nécessaires
            $order->load(['items', 'user']);

            // Déterminer l'email du destinataire
            $recipientEmail = $this->getRecipientEmail($order);
            $recipientName = $this->getRecipientName($order);

            if ($recipientEmail) {
                Mail::to($recipientEmail, $recipientName)->send(new OrderPaidNotification($order));
                
                Log::info('Payment confirmation email sent', [
                    'order_id' => $order->id,
                    'recipient' => $recipientEmail
                ]);
            } else {
                Log::warning('No customer email found for payment confirmation', [
                    'order_id' => $order->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
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

        // Priorité 2: Email de facturation
        if ($order->billing_email) {
            return $order->billing_email;
        }

        // Priorité 3: Email dans les informations client (JSON)
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

        // Priorité 2: Nom de facturation
        if ($order->billing_first_name && $order->billing_last_name) {
            return $order->billing_first_name . ' ' . $order->billing_last_name;
        }

        // Priorité 3: Nom de livraison
        if ($order->shipping_first_name && $order->shipping_last_name) {
            return $order->shipping_first_name . ' ' . $order->shipping_last_name;
        }

        // Priorité 4: Nom dans les informations client (JSON)
        if ($order->customer_info) {
            $customerInfo = is_array($order->customer_info) ? $order->customer_info : json_decode($order->customer_info, true);
            if ($customerInfo && isset($customerInfo['firstName'], $customerInfo['lastName'])) {
                return $customerInfo['firstName'] . ' ' . $customerInfo['lastName'];
            }
        }

        return 'Client';
    }
}