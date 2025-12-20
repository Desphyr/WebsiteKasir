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

class POSController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::where('stock', '>', 0); // Hanya tampilkan yg ada stok

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
                    'price_per_item' => $item['price'], // Harga saat itu
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
            // NF-04: Pesan error jelas
            return back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $query = Transaction::with(['user', 'details.product'])
            ->where('user_id', Auth::id()); // Hanya tampilkan transaksi user ini

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
            ->paginate(15); // Pagination untuk performance

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
}
