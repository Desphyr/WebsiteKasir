<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Product::with('category'); 

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }
        
        $products = $query->paginate(10);
        return view('admin.menu.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
        ]);

        $data = $request->only(['name', 'category_id', 'price', 'stock', 'image_url']);

        Product::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Product $menu) 
    {
        $categories = Category::all();
        return view('admin.menu.edit', ['product' => $menu, 'categories' => $categories]);
    }

    public function update(Request $request, Product $menu)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('products')->ignore($menu->id),
            ],
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
        ]);

        $data = $request->only(['name', 'category_id', 'price', 'stock', 'image_url']);

        $menu->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $menu)
    {
        // Hapus gambar dari penyimpanan
        if ($menu->image_url) {
            $oldPath = str_replace('/storage/', '', $menu->image_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus.');
    }
}