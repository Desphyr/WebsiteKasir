<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Category::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(10);
        // Tampilan untuk ini perlu dibuat di:
        // resources/views/admin/kategori/index.blade.php
        return view('admin.kategori.index', compact('categories', 'search'));
    }

    public function create()
    {
        // Tampilan untuk ini perlu dibuat di:
        // resources/views/admin/kategori/create.blade.php
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name'),
            ],
        ], [
            'name.unique' => 'nama kategori sudah ada',
        ]);

        Category::create($request->all());

        return redirect()->route('admin.kategori.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function edit(Category $kategori) // Route model binding
    {
        // Tampilan untuk ini perlu dibuat di:
        // resources/views/admin/kategori/edit.blade.php
        return view('admin.kategori.edit', ['category' => $kategori]);
    }

    public function update(Request $request, Category $kategori)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->ignore($kategori->id),
            ],
        ]);

        $kategori->update($request->all());

        return redirect()->route('admin.kategori.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $kategori)
    {
        // NF-04: Keandalan data. Cek jika kategori masih dipakai.
        // Migrasi products (onDelete('cascade')) sebenarnya akan menghapus
        // semua produk, tapi itu berbahaya. Lebih baik kita cegah.
        if ($kategori->products()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                             ->with('error', 'Tidak dapat menghapus kategori karena masih memiliki menu terkait.');
        }

        $kategori->delete();
        return redirect()->route('admin.kategori.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}

