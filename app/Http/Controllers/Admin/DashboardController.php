<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->today();

        // 1. Data Kartu Atas (Ringkasan Hari Ini)
        $pendapatanHariIni = Transaction::whereDate('transaction_time', $today)->sum('total_amount');
        $transaksiHariIni = Transaction::whereDate('transaction_time', $today)->count();
        $menuTerjual = TransactionDetail::whereHas('transaction', function ($q) use ($today) {
            $q->whereDate('transaction_time', $today);
        })->sum('quantity');
        $stokKritis = Product::where('stock', '<', 5)->count();

        // 2. Item Terlaris (Top 5)
        $itemTerlaris = TransactionDetail::with('product')
            ->whereHas('transaction', function ($q) use ($today) {
                $q->whereDate('transaction_time', $today);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_terjual'))
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
            
        // 3. PERBAIKAN DATA GRAFIK (7 Hari Terakhir)
        // Langkah A: Buat kerangka tanggal 7 hari ke belakang (isi 0 semua dulu)
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            // Key format Y-m-d untuk pencocokan database
            $dates->put(now()->subDays($i)->format('Y-m-d'), 0);
        }

        // Langkah B: Ambil data real dari Database
        $grafikData = Transaction::select(
                DB::raw('DATE(transaction_time) as tanggal'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('transaction_time', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('tanggal')
            ->get()
            ->pluck('total', 'tanggal'); // Pluck menghasilkan array [ '2023-11-19' => 500000 ]

        // Langkah C: Gabungkan kerangka kosong dengan data real
        // (Data real akan menimpa nilai 0 pada tanggal yang cocok)
        $finalGrafik = $dates->merge($grafikData);

        // Langkah D: Pisahkan jadi labels (sumbu X) dan data (sumbu Y) untuk ChartJS
        $labels = $finalGrafik->keys()->map(function($date) {
            return Carbon::parse($date)->format('d M'); // Contoh Tampilan: 19 Nov
        })->values();
        
        $data = $finalGrafik->values(); // Contoh Data: [0, 50000, 0, 120000...]

        return view('admin.dashboard', compact(
            'pendapatanHariIni',
            'transaksiHariIni',
            'menuTerjual',
            'stokKritis',
            'itemTerlaris',
            'labels',
            'data'
        ));
    }
}