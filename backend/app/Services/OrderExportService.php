<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderExportService
{
    public function exportOrders(Collection $orders): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->setCellValue('A1', 'Numéro de commande');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Client');
        $sheet->setCellValue('D1', 'Statut');
        $sheet->setCellValue('E1', 'Méthode de paiement');
        $sheet->setCellValue('F1', 'Statut du paiement');
        $sheet->setCellValue('G1', 'Total');
        $sheet->setCellValue('H1', 'Adresse de livraison');

        $row = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $order->order_number);
            $sheet->setCellValue('B' . $row, $order->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $row, $order->user->name);
            $sheet->setCellValue('D' . $row, $order->status);
            $sheet->setCellValue('E' . $row, $order->payment_method);
            $sheet->setCellValue('F' . $row, $order->payment_status);
            $sheet->setCellValue('G' . $row, $order->total);
            $sheet->setCellValue('H' . $row, $order->shipping_address);
            $row++;
        }

        // Ajuster la largeur des colonnes
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Créer le fichier
        $writer = new Xlsx($spreadsheet);
        $filename = 'commandes_' . now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/public/exports/' . $filename);
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        $writer->save($path);
        
        return $filename;
    }
}