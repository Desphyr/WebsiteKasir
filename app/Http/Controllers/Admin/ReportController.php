<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
// Untuk Excel, pastikan Anda sudah: composer require maatwebsite/excel
use Maatwebsite\Excel\Facades\Excel;
// Anda perlu membuat App\Exports\SalesExport
// php artisan make:export SalesExport

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

        // Logika untuk ekspor Excel. Anda perlu membuat class SalesExport.
        // return Excel::download(new \App\Exports\SalesExport($startDate, $endDate), 'laporan-penjualan.xlsx');

        // Placeholder
        return redirect()->route('admin.laporan.index')
            ->with('info', 'Fitur ekspor sedang dalam pengembangan. Anda perlu membuat App\Exports\SalesExport.');
    }
}
