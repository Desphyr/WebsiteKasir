<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $searchDate = $request->query('date', now()->format('Y-m-d'));
        
        $expenses = Expense::with('user') // Eager load user (admin)
            ->whereDate('expense_date', $searchDate)
            ->paginate(10);

        // Tampilan: resources/views/admin/pengeluaran/index.blade.php
        return view('admin.pengeluaran.index', compact('expenses', 'searchDate'));
    }

    public function create()
    {
        // Tampilan: resources/views/admin/pengeluaran/create.blade.php
        return view('admin.pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'user_id' => Auth::id(), // Admin yang login
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
        ]);

        return redirect()->route('admin.pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(Expense $pengeluaran) // Nama variabel $pengeluaran harus cocok dengan di routes
    {
        // Tampilan: resources/views/admin/pengeluaran/edit.blade.php
        return view('admin.pengeluaran.edit', ['expense' => $pengeluaran]);
    }

    public function update(Request $request, Expense $pengeluaran)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $pengeluaran->update($request->all());

        return redirect()->route('admin.pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $pengeluaran)
    {
        $pengeluaran->delete();
        return redirect()->route('admin.pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}