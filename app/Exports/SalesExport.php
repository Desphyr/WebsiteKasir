<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;

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
     * Query untuk mengambil data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function query()
    {
        return Transaction::with(['user', 'details.product'])
            ->whereBetween('transaction_time', [$this->startDate, $this->endDate])
            ->orderBy('transaction_time', 'desc')
            ->get();
    }

    /**
     * Header untuk file Excel
     * @return array
     */
    public function headings()
    {
        return [
            'No',
            'Waktu Transaksi',
            'Tipe Pembayaran',
            'Nama Kasir',
            'Detail Pesanan',
            'Total (Rp)'
        ];
    }

    /**
     * Format data untuk setiap baris
     * @param Transaction $transaction
     * @return array
     */
    public function map($transaction)
    {
        // Format detail pesanan
        $orderDetails = [];
        foreach ($transaction->details as $detail) {
            $productName = $detail->product->name ?? 'Menu Dihapus';
            $orderDetails[] = "{$productName} ({$detail->quantity}x)";
        }
        $orderDetailsText = implode(', ', $orderDetails);

        return [
            '', // No akan diisi otomatis oleh Excel
            $transaction->transaction_time->format('d M Y, H:i'),
            $transaction->payment_type,
            $transaction->user->full_name,
            $orderDetailsText,
            $transaction->total_amount
        ];
    }

    /**
     * Style untuk file Excel
     * @param \PHPExcel_Worksheet $sheet
     */
    public function excelStyles($sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFE6E6FA', // Light purple
                ],
            ],
        ]);

        // Auto width untuk semua kolom
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Format kolom Total menjadi currency
        $sheet->getStyle('F:F')->getNumberFormat()->setFormatCode('#,##0');
    }
}
