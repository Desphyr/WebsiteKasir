<?php
namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::query(); // Hapus filter 'stock > 0' agar menu stok 0 tetap muncul

        // Filter Kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('category_id', $request->kategori);
        }

        // Filter Search
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->orderBy('name', 'asc')->get();

        return view('cashier.pos', compact('products', 'categories'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:QRIS,Cash',
            'cart' => 'required|json',
            'total' => 'required|numeric|min:0',
            'cash_amount' => 'nullable|numeric|min:0'
        ]);

        $cart = json_decode($request->cart, true);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // Validasi cash amount jika pembayaran cash
        if ($request->payment_type === 'Cash') {
            if (!$request->has('cash_amount') || !$request->cash_amount) {
                return back()->with('error', 'Masukkan jumlah uang cash yang diberikan customer.');
            }
            if ($request->cash_amount < $request->total) {
                return back()->with('error', 'Jumlah uang cash tidak mencukupi untuk pembayaran.');
            }
        }

        // Gunakan DB Transaction (NF-04)
        try {
            DB::beginTransaction();

            $changeAmount = 0;
            if ($request->payment_type === 'Cash') {
                $changeAmount = $request->cash_amount - $request->total;
            }

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total_amount' => $request->total,
                'payment_type' => $request->payment_type,
                'cash_amount' => $request->cash_amount,
                'change_amount' => $changeAmount,
                'transaction_time' => now(),
            ]);

            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                
                // Validasi stok saat checkout
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi.");
                }

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price_per_item' => $item['price'],
                ]);

                // Kurangi stok
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();
            
            $message = 'Transaksi berhasil!';
            if ($request->payment_type === 'Cash' && $changeAmount > 0) {
                $message .= ' Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.');
            }
            
            return redirect()->route('kasir.pos')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $query = Transaction::with(['user', 'details.product'])
            ->where('user_id', Auth::id());

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('transaction_time', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('transaction_time', '<=', $request->end_date);
        }

        // Filter berdasarkan periode predefined
        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('transaction_time', today());
                    break;
                case 'week':
                    $query->whereBetween('transaction_time', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('transaction_time', now()->month)
                          ->whereYear('transaction_time', now()->year);
                    break;
            }
        }

        $transactions = $query->orderBy('transaction_time', 'desc')
            ->paginate(15);

        return view('cashier.history', compact('transactions'));
    }

    public function showTransactionDetail($id)
    {
        try {
            $transaction = Transaction::with([
                'user', 
                'details.product'
            ])->where('user_id', Auth::id())
              ->findOrFail($id);

            return view('cashier.history-detail', compact('transaction'));
        } catch (\Exception $e) {
            return redirect()->route('kasir.pos.history')
                ->with('error', 'Transaksi tidak ditemukan.');
        }
    }

    public function getRecentTransactions()
    {
        $recentTransactions = Transaction::with(['user', 'details.product'])
            ->where('user_id', Auth::id())
            ->orderBy('transaction_time', 'desc')
            ->limit(5)
            ->get();

        return response()->json($recentTransactions);
    }

    public function exportTransaction($id)
    {
        try {
            $transaction = Transaction::with([
                'user', 
                'details.product'
            ])->where('user_id', Auth::id())
              ->findOrFail($id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detail Transaksi');

            // Set lebar kolom
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);

            // Header Toko
            $sheet->setCellValue('A1', 'BAKARAN RESTO');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->setCellValue('A2', 'Detail Transaksi #' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT));
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

            // Informasi Transaksi
            $sheet->setCellValue('A4', 'ID Transaksi:');
            $sheet->setCellValue('B4', '#' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT));
            $sheet->setCellValue('A5', 'Waktu:');
            $sheet->setCellValue('B5', $transaction->transaction_time->format('d/m/Y H:i:s'));
            $sheet->setCellValue('A6', 'Kasir:');
            $sheet->setCellValue('B6', $transaction->user->name);
            $sheet->setCellValue('A7', 'Tipe Pembayaran:');
            $sheet->setCellValue('B7', $transaction->payment_type);

            // Header Tabel Item
            $sheet->setCellValue('A9', 'No');
            $sheet->setCellValue('B9', 'Nama Menu');
            $sheet->setCellValue('C9', 'Qty');
            $sheet->setCellValue('D9', 'Harga Satuan');
            $sheet->setCellValue('E9', 'Total');

            // Style header tabel
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ];
            $sheet->getStyle('A9:E9')->applyFromArray($headerStyle);

            // Data Item
            $row = 10;
            $no = 1;
            foreach ($transaction->details as $detail) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $detail->product->name);
                $sheet->setCellValue('C' . $row, $detail->quantity);
                $sheet->setCellValue('D' . $row, $detail->price_per_item);
                $sheet->setCellValue('E' . $row, $detail->quantity * $detail->price_per_item);

                // Format currency
                $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('Rp #,##0');
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('Rp #,##0');

                $row++;
                $no++;
            }

            // Summary
            $summaryRow = $row + 1;
            $sheet->setCellValue('D' . $summaryRow, 'Total:');
            $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
            $sheet->setCellValue('E' . $summaryRow, $transaction->total_amount);
            $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
            $sheet->getStyle('E' . $summaryRow)->getNumberFormat()->setFormatCode('Rp #,##0');

            // Jika pembayaran cash, tampilkan kembalian
            if ($transaction->payment_type === 'Cash') {
                $summaryRow++;
                $sheet->setCellValue('D' . $summaryRow, 'Uang Diberikan:');
                $sheet->setCellValue('E' . $summaryRow, $transaction->cash_amount);
                $sheet->getStyle('E' . $summaryRow)->getNumberFormat()->setFormatCode('Rp #,##0');

                $summaryRow++;
                $sheet->setCellValue('D' . $summaryRow, 'Kembalian:');
                $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true)->setColor(new Color('FF00B050'));
                $sheet->setCellValue('E' . $summaryRow, $transaction->change_amount);
                $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true)->setColor(new Color('FF00B050'));
                $sheet->getStyle('E' . $summaryRow)->getNumberFormat()->setFormatCode('Rp #,##0');
            }

            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = 'transaksi_' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT) . '_' . now()->format('YmdHis') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->route('kasir.pos.history')
                ->with('error', 'Gagal mengekspor transaksi: ' . $e->getMessage());
        }
    }
}

