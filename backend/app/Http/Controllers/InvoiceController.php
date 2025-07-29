<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Générer une facture pour une commande
     */
    public function generate(Request $request, $orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            // Vérifier si la facture peut être générée
            if (!$this->invoiceService->canGenerateInvoice($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La facture ne peut être générée que pour les commandes payées ou confirmées'
                ], 400);
            }

            $result = $this->invoiceService->generateInvoice($order);

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
            Log::error('Invoice generation controller error', [
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
     * Télécharger une facture
     */
    public function download(Request $request, $orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            $result = $this->invoiceService->downloadInvoice($order);

            if ($result['success']) {
                return response($result['content'])
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Invoice download controller error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement de la facture'
            ], 500);
        }
    }

    /**
     * Voir une facture dans le navigateur
     */
    public function view(Request $request, $orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            $result = $this->invoiceService->downloadInvoice($order);

            if ($result['success']) {
                return response($result['content'])
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $result['filename'] . '"');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Invoice view controller error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'affichage de la facture'
            ], 500);
        }
    }

    /**
     * Obtenir l'URL d'une facture
     */
    public function getUrl(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $url = $this->invoiceService->getInvoiceUrl($order);

            if ($url) {
                return response()->json([
                    'success' => true,
                    'invoice_url' => $url
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Facture non disponible'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Invoice URL controller error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'URL de la facture'
            ], 500);
        }
    }

    /**
     * Régénérer une facture
     */
    public function regenerate(Request $request, $orderId)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($orderId);

            if (!$this->invoiceService->canGenerateInvoice($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La facture ne peut être générée que pour les commandes payées ou confirmées'
                ], 400);
            }

            $result = $this->invoiceService->regenerateInvoice($order);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'invoice_url' => $result['url'],
                    'filename' => $result['filename'],
                    'message' => 'Facture régénérée avec succès'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Invoice regeneration controller error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la régénération de la facture'
            ], 500);
        }
    }

    /**
     * Supprimer une facture
     */
    public function delete(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $result = $this->invoiceService->deleteInvoice($order);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Invoice deletion controller error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la facture'
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques des factures
     */
    public function stats()
    {
        try {
            $stats = $this->invoiceService->getInvoiceStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Invoice stats controller error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }
}