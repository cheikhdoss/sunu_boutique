<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PayDunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $order = Order::with('items.product')->findOrFail($request->order_id);

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
                'amount' => $this->payDunyaService->formatAmount($order->total_amount),
                'description' => "Commande #{$order->id} - " . config('app.name'),
                'customer_email' => $order->customer_info['email'] ?? null,
                'customer_phone' => $order->customer_info['phone'] ?? null,
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price,
                        'total_price' => $item->price * $item->quantity,
                        'description' => $item->product->description ?? '',
                    ];
                })->toArray()
            ];

            // Créer la facture PayDunya
            $result = $this->payDunyaService->createInvoice($invoiceData);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            $invoiceResponse = $result['data'];

            // Sauvegarder les informations de paiement
            $order->update([
                'payment_reference' => $invoiceResponse['token'],
                'paydunya_invoice_token' => $invoiceResponse['token'],
                'payment_status' => 'processing'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_url' => $this->payDunyaService->getPaymentUrl($invoiceResponse['token']),
                    'invoice_token' => $invoiceResponse['token'],
                    'response_code' => $invoiceResponse['response_code'],
                    'description' => $invoiceResponse['description'] ?? 'Facture créée avec succès'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PayDunya payment initiation error', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage()
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
            $order->update($updates);

            Log::info('Order status updated from PayDunya', [
                'order_id' => $order->id,
                'updates' => $updates,
                'paydunya_status' => $payDunyaData['status']
            ]);
        }
    }
}