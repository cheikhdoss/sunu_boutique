<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Générer une facture PDF pour une commande
     */
    public function generateInvoice(Order $order)
    {
        try {
            // Charger les relations nécessaires
            $order->load(['items', 'user']);

            // Générer le nom du fichier
            $filename = 'facture-' . $order->order_number . '.pdf';
            $filepath = 'invoices/' . $filename;

            // Vérifier si la facture existe déjà
            if (Storage::disk('public')->exists($filepath)) {
                return [
                    'success' => true,
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'url' => Storage::disk('public')->url($filepath),
                    'message' => 'Facture déjà existante'
                ];
            }

            // Générer le PDF
            $pdf = Pdf::loadView('invoices.template', compact('order'));
            
            // Configurer le PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

            // Sauvegarder le PDF
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($filepath, $pdfContent);

            // Mettre à jour l'URL de la facture dans la commande
            $order->update([
                'invoice_url' => Storage::disk('public')->url($filepath),
                'invoice_generated_at' => now()
            ]);

            Log::info('Invoice generated successfully', [
                'order_id' => $order->id,
                'filename' => $filename,
                'filepath' => $filepath
            ]);

            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'url' => Storage::disk('public')->url($filepath),
                'message' => 'Facture générée avec succès'
            ];

        } catch (\Exception $e) {
            Log::error('Invoice generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la génération de la facture: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Télécharger une facture existante
     */
    public function downloadInvoice(Order $order)
    {
        try {
            $filename = 'facture-' . $order->order_number . '.pdf';
            $filepath = 'invoices/' . $filename;

            // Vérifier si la facture existe
            if (!Storage::disk('public')->exists($filepath)) {
                // Générer la facture si elle n'existe pas
                $result = $this->generateInvoice($order);
                if (!$result['success']) {
                    return $result;
                }
                $filepath = $result['filepath'];
            }

            return [
                'success' => true,
                'filepath' => $filepath,
                'filename' => $filename,
                'url' => Storage::disk('public')->url($filepath),
                'content' => Storage::disk('public')->get($filepath)
            ];

        } catch (\Exception $e) {
            Log::error('Invoice download failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors du téléchargement de la facture'
            ];
        }
    }

    /**
     * Vérifier si une facture peut être générée
     */
    public function canGenerateInvoice(Order $order)
    {
        // La facture peut être générée si la commande est payée ou confirmée
        return in_array($order->payment_status, ['paid', 'processing']) || 
               in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']);
    }

    /**
     * Obtenir l'URL de la facture
     */
    public function getInvoiceUrl(Order $order)
    {
        if ($order->invoice_url) {
            return $order->invoice_url;
        }

        $filename = 'facture-' . $order->order_number . '.pdf';
        $filepath = 'invoices/' . $filename;

        if (Storage::disk('public')->exists($filepath)) {
            $url = Storage::disk('public')->url($filepath);
            
            // Mettre à jour l'URL dans la base de données
            $order->update(['invoice_url' => $url]);
            
            return $url;
        }

        return null;
    }

    /**
     * Supprimer une facture
     */
    public function deleteInvoice(Order $order)
    {
        try {
            $filename = 'facture-' . $order->order_number . '.pdf';
            $filepath = 'invoices/' . $filename;

            if (Storage::disk('public')->exists($filepath)) {
                Storage::disk('public')->delete($filepath);
                
                // Supprimer l'URL de la base de données
                $order->update([
                    'invoice_url' => null,
                    'invoice_generated_at' => null
                ]);

                return [
                    'success' => true,
                    'message' => 'Facture supprimée avec succès'
                ];
            }

            return [
                'success' => false,
                'error' => 'Facture introuvable'
            ];

        } catch (\Exception $e) {
            Log::error('Invoice deletion failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression de la facture'
            ];
        }
    }

    /**
     * Régénérer une facture
     */
    public function regenerateInvoice(Order $order)
    {
        // Supprimer l'ancienne facture
        $this->deleteInvoice($order);
        
        // Générer une nouvelle facture
        return $this->generateInvoice($order);
    }

    /**
     * Obtenir les statistiques des factures
     */
    public function getInvoiceStats()
    {
        $totalInvoices = Order::whereNotNull('invoice_url')->count();
        $paidInvoices = Order::where('payment_status', 'paid')
                            ->whereNotNull('invoice_url')
                            ->count();
        
        $totalAmount = Order::where('payment_status', 'paid')
                           ->whereNotNull('invoice_url')
                           ->sum('total');

        return [
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $totalInvoices - $paidInvoices,
            'total_amount' => $totalAmount
        ];
    }
}