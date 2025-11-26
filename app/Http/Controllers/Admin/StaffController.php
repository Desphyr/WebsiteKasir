<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = User::query(); // User model adalah staf kita

        if ($search) {
            $query->where('full_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
        }

        $staff = $query->paginate(10);
        // Tampilan: resources/views/admin/staf/index.blade.php
        return view('admin.staf.index', compact('staff', 'search'));
    }

    public function create()
    {
        // Tampilan: resources/views/admin/staf/create.blade.php
        return view('admin.staf.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'role' => 'required|in:admin,kasir',
            'password' => ['required', 'confirmed', Password::min(8)],
            'email' => 'nullable|string|email|max:255|unique:users',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password), // NF-02: Hash password
        ]);

        return redirect()->route('admin.staf.index')
                         ->with('success', 'Staf baru berhasil ditambahkan.');
    }

    public function edit(User $staf) // Route model binding
    {
        // Tampilan: resources/views/admin/staf/edit.blade.php
        return view('admin.staf.edit', ['staff' => $staf]);
    }

    public function update(Request $request, User $staf)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('users')->ignore($staf->id),
            ],
            'role' => 'required|in:admin,kasir',
            'email' => [
                'nullable', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($staf->id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)], // Password opsional
        ]);

        $data = $request->only('full_name', 'username', 'email', 'role');
        
        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staf->update($data);

        return redirect()->route('admin.staf.index')
                         ->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(User $staf)
    {
        // NF-04: Admin tidak bisa menghapus diri sendiri
        if ($staf->id === Auth::id()) {
            return redirect()->route('admin.staf.index')
                             ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staf->delete();
        return redirect()->route('admin.staf.index')
                         ->with('success', 'Staf berhasil dihapus.');
    }
}
