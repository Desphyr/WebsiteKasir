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
            'total' => 'required|numeric|min:0'
        ]);

        $cart = json_decode($request->cart, true);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // Gunakan DB Transaction (NF-04)
        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total_amount' => $request->total,
                'payment_type' => $request->payment_type,
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
            return redirect()->route('kasir.pos')->with('success', 'Transaksi berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            // NF-04: Pesan error jelas
            return back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }
}
