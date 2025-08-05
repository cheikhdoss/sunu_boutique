<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Générer et télécharger la facture d'une commande
     */
    public function downloadInvoice(Order $order)
    {
        try {
            // Vérifier que la commande existe et est payée
            if (!$order || $order->payment_status !== 'paid') {
                abort(404, 'Facture non disponible pour cette commande');
            }

            // Préparer les données pour la facture
            $data = $this->prepareInvoiceData($order);

            // Générer le PDF
            $pdf = PDF::loadView('admin.invoices.invoice-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => false,
                    'isHtml5ParserEnabled' => true,
                ]);

            $filename = "facture-{$order->order_number}.pdf";

            return $pdf->download($filename);
        } catch (Exception $e) {
            Log::error('Erreur génération facture: ' . $e->getMessage());
            abort(500, 'Erreur lors de la génération de la facture');
        }
    }

    /**
     * Voir la facture dans le navigateur
     */
    public function viewInvoice(Order $order)
    {
        try {
            if (!$order || $order->payment_status !== 'paid') {
                abort(404, 'Facture non disponible pour cette commande');
            }

            $data = $this->prepareInvoiceData($order);

            $pdf = PDF::loadView('admin.invoices.invoice-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => false,
                    'isHtml5ParserEnabled' => true,
                ]);

            return $pdf->stream("facture-{$order->order_number}.pdf");
        } catch (Exception $e) {
            Log::error('Erreur affichage facture: ' . $e->getMessage());
            abort(500, 'Erreur lors de l\'affichage de la facture');
        }
    }

    /**
     * Préparer les données pour la facture
     */
    private function prepareInvoiceData(Order $order): array
    {
        // Charger les relations nécessaires
        $order->load(['user', 'items.product']);

        // Calculer les totaux
        $subtotal = $order->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $taxRate = 0.18; // 18% TVA (ajustez selon vos besoins)
        $taxAmount = $subtotal * $taxRate;
        $total = $subtotal + $taxAmount;

        // Informations de l'entreprise
        $company = [
            'name' => 'SUNU BOUTIQUE',
            'address' => 'Dakar, Sénégal',
            'phone' => '+221 77 123 45 67',
            'email' => 'contact@sunuboutique.com',
            'website' => 'www.sunuboutique.com',
            'ninea' => '123456789', // Numéro NINEA
        ];

        // Informations client
        $customer = [
            'name' => $order->user ? $order->user->name : $order->customer_name,
            'email' => $order->user ? $order->user->email : $order->customer_email,
            'phone' => $order->user ? $order->user->phone : $order->customer_phone,
            'address' => $order->shipping_address ?? 'Non spécifiée',
            'city' => $order->shipping_city ?? '',
        ];

        return [
            'order' => $order,
            'company' => $company,
            'customer' => $customer,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate * 100,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Générer une facture pour toutes les commandes payées (action en lot)
     */
    public function bulkDownloadInvoices(Request $request)
    {
        try {
            $orderIds = $request->input('orders', []);
            
            if (empty($orderIds)) {
                return response()->json(['error' => 'Aucune commande sélectionnée'], 400);
            }

            $orders = Order::whereIn('id', $orderIds)
                ->where('payment_status', 'paid')
                ->with(['user', 'items.product'])
                ->get();

            if ($orders->isEmpty()) {
                return response()->json(['error' => 'Aucune commande payée trouvée'], 404);
            }

            // Créer un ZIP avec toutes les factures
            $zip = new \ZipArchive();
            $zipFileName = 'factures-' . Carbon::now()->format('Y-m-d-H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            // Créer le dossier temp s'il n'existe pas
            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                foreach ($orders as $order) {
                    $data = $this->prepareInvoiceData($order);
                    
                    $pdf = PDF::loadView('admin.invoices.invoice-pdf', $data)
                        ->setPaper('a4', 'portrait');
                    
                    $pdfContent = $pdf->output();
                    $zip->addFromString("facture-{$order->order_number}.pdf", $pdfContent);
                }
                $zip->close();

                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            } else {
                throw new Exception('Impossible de créer le fichier ZIP');
            }
        } catch (Exception $e) {
            Log::error('Erreur génération factures en lot: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération des factures'], 500);
        }
    }
}