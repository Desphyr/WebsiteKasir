<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\SalesExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default filter: hari ini
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Ubah ke format timestamp untuk query
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();

        $transactions = Transaction::with(['user', 'details.product']) // Eager load
            ->whereBetween('transaction_time', [$startDateTime, $endDateTime])
            ->paginate(20);

        // Tampilan: resources/views/admin/laporan/index.blade.php
        return view('admin.laporan.index', compact('transactions', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Validasi tanggal
        if (!$startDate || !$endDate) {
            return redirect()->route('admin.laporan.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi.');
        }

        try {
            // Cek apakah ada transaksi dalam rentang tanggal
            $hasTransactions = Transaction::whereBetween('transaction_time', [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay()
            ])->exists();

            if (!$hasTransactions) {
                return redirect()->route('admin.laporan.index')
                    ->with('warning', 'Tidak ada transaksi untuk rentang tanggal yang dipilih.');
            }

            // Generate nama file dengan format tanggal
            $fileName = 'laporan-penjualan-' . $startDate . '-to-' . $endDate . '.xlsx';

            // Gunakan SalesExport untuk mendapatkan spreadsheet
            $salesExport = new SalesExport($startDate, $endDate);
            $spreadsheet = $salesExport->generate();

            // Download file
            $writer = new Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error exporting sales report: ' . $e->getMessage());
            
            return redirect()->route('admin.laporan.index')
                ->with('error', 'Terjadi kesalahan saat mengekspor laporan. Silakan coba lagi.');
        }
    }
}
