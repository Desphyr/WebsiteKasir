@extends('layouts.app')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Kelola Kategori</h1>

    <!-- Notifikasi Sukses/Error -->
    @include('layouts.partials.notifications')

    <div class="p-6 bg-[#FFF9C4] rounded-lg shadow-md">
        <!-- Aksi: Tambah & Search -->
        <div class="flex flex-col items-center justify-between mb-4 space-y-3 md:flex-row md:space-y-0">
            <a href="{{ route('admin.kategori.create') }}" class="w-full px-4 py-2 font-medium text-center text-white bg-orange-400 rounded-md md:w-auto hover:bg-orange-500">
                + Tambah Kategori Baru
            </a>

            <form method="GET" action="{{ route('admin.kategori.index') }}" class="w-full md:w-1/3">
                <div class="relative">
                    <input type="text" name="search" placeholder="Cari kategori..." value="{{ $search ?? '' }}"
                           class="w-full px-4 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
                    <button type="submit" class="absolute inset-y-0 right-0 px-4 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Kategori -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-[#FFF8E1]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">No</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">ID Kategori</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Nama Kategori</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-[#8D6E63] uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-[#FFF9C4] divide-y divide-yellow-200">
                    @forelse ($categories as $index => $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-[#8D6E63]">{{ $categories->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-[#8D6E63]">{{ $category->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-[#3E2723]">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <a href="{{ route('admin.kategori.edit', $category->id) }}" class="text-orange-600 hover:text-orange-900">Edit</a>
                                <form action="{{ route('admin.kategori.destroy', $category->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 whitespace-nowrap">Tidak ada kategori ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="mt-4">
            {{ $categories->appends(['search' => $search ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection
