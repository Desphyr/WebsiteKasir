<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // Import ini penting

class SalesExport
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
    }

    /**
     * Generate file Excel
     * @return Spreadsheet
     */
    public function generate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = [
            'No',
            'Waktu Transaksi',
            'Tipe Pembayaran',
            'Nama Kasir',
            'Detail Pesanan',
            'Total (Rp)'
        ];

        // Write headers
        foreach ($headers as $colIndex => $header) {
            // Ubah index 0, 1, 2 menjadi A, B, C
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Terapkan style (bisa langsung range A1 sampai F1)
        // Kita gunakan huruf kolom terakhir berdasarkan jumlah header
        $lastColLetter = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColLetter . '1')->applyFromArray($headerStyle);

        // Get transactions
        $transactions = Transaction::with(['user', 'details.product'])
            ->whereBetween('transaction_time', [$this->startDate, $this->endDate])
            ->orderBy('transaction_time', 'desc')
            ->get();

        // Write data
        $row = 2;
        foreach ($transactions as $index => $transaction) {
            // Format detail pesanan
            $orderDetails = [];
            foreach ($transaction->details as $detail) {
                $productName = $detail->product->name ?? 'Menu Dihapus';
                $orderDetails[] = "{$productName} ({$detail->quantity}x)";
            }
            $orderDetailsText = implode(', ', $orderDetails);

            // Gunakan Huruf Kolom secara eksplisit (A, B, C...) + Row
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $transaction->transaction_time->format('d M Y, H:i'));
            $sheet->setCellValue('C' . $row, $transaction->payment_type);
            $sheet->setCellValue('D' . $row, $transaction->user->full_name);
            $sheet->setCellValue('E' . $row, $orderDetailsText);
            $sheet->setCellValue('F' . $row, $transaction->total_amount);

            $row++;
        }

        // Set column widths (Ganti angka kolom dengan Huruf)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);

        return $spreadsheet;
    }
}