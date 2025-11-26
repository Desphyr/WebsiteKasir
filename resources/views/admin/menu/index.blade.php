@extends('layouts.app')

@section('title', 'Manajemen Menu')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Manajemen Menu</h1>

    @include('layouts.partials.notifications')

    <div class="p-6 bg-white rounded-lg shadow-md">
        <!-- Header: Tombol Tambah & Search -->
        <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
            <!-- Tombol Tambah -->
            <a href="{{ route('admin.menu.create') }}" class="px-6 py-2.5 font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-md text-center">
                + Tambah Menu Baru
            </a>

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('admin.menu.index') }}" class="flex w-full md:w-auto gap-2">
                <input type="text" name="search" placeholder="Cari menu..." value="{{ $search ?? '' }}"
                       class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                
                <!-- TOMBOL CARI -->
                <button type="submit" class="px-4 py-2 font-semibold text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Menu -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase">No</th>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase">Gambar</th>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase">Nama Menu</th>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase">Stok</th>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-500 uppercase">Harga</th>
                        <th scope="col" class="px-6 py-3 text-xs font-bold tracking-wider text-right text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $index => $product)
                        <tr class="hover:bg-gray-50 transition menu-row" data-id="{{ $product->id }}">
                            <!-- Nomor -->
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                                {{ $products->firstItem() + $index }}
                            </td>
                            
                            <!-- Gambar (FIXED) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->image_url)
                                    <!-- Menggunakan asset() untuk memanggil file dari folder public/storage -->
                                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" 
                                         class="w-16 h-16 rounded-lg object-cover shadow-sm border border-gray-200">
                                @else
                                    <!-- Placeholder jika gambar kosong -->
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs font-bold border border-gray-200">
                                        No IMG
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Nama Menu -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                            </td>
                            
                            <!-- Kategori -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            
                            <!-- Stok -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($product->stock < 5)
                                    <span class="text-red-600 font-bold flex items-center">
                                        {{ $product->stock }}
                                        <span class="ml-1 text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Kritis</span>
                                    </span>
                                @else
                                    <span class="text-gray-900">{{ $product->stock }}</span>
                                @endif
                            </td>
                            
                            <!-- Harga -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.menu.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold transition">Edit</a>
                                    
                                    <form action="{{ route('admin.menu.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus menu ini? Data yang dihapus tidak bisa dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-900 font-bold transition btn-delete-menu" data-id="{{ $product->id }}">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    <p class="text-lg font-medium">Tidak ada menu ditemukan.</p>
                                    <p class="text-sm text-gray-400">Coba kata kunci lain atau tambahkan menu baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                    </tbody>
                </table>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.btn-delete-menu').forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            if (!confirm('Yakin ingin menghapus menu ini? Data yang dihapus tidak bisa dikembalikan.')) return;
                            const id = btn.getAttribute('data-id');
                            const row = document.querySelector('.menu-row[data-id="' + id + '"]');
                            fetch(btn.closest('form').getAttribute('action'), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': btn.closest('form').querySelector('[name=_token]').value,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: new URLSearchParams({
                                    _method: 'DELETE',
                                })
                            })
                            .then(res => {
                                if (res.ok) {
                                    row.style.transition = 'opacity 0.5s';
                                    row.style.opacity = 0;
                                    setTimeout(() => row.remove(), 500);
                                } else {
                                    res.json().then(data => alert(data.message || 'Gagal menghapus menu'));
                                }
                            })
                            .catch(() => alert('Gagal menghapus menu'));
                        });
                    });
                });
                </script>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="mt-6">
            {{ $products->appends(['search' => $search ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection