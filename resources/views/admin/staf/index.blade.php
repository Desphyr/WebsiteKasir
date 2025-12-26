@extends('layouts.app')

@section('title', 'Manajemen Staf')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Kelola Staf</h1>

    <!-- Notifikasi Sukses/Error -->
    @include('layouts.partials.notifications')

    <div class="p-6 bg-white rounded-lg shadow-md">
        <!-- Aksi: Tambah & Search -->
        <div class="flex flex-col items-center justify-between mb-4 space-y-3 md:flex-row md:space-y-0">
            <a href="{{ route('admin.staf.create') }}" class="w-full px-4 py-2 font-medium text-center text-white bg-indigo-600 rounded-md md:w-auto hover:bg-indigo-700">
                + Tambah Staf Baru
            </a>

            <form method="GET" action="{{ route('admin.staf.index') }}" class="w-full md:w-1/3">
                <div class="relative">
                    <input type="text" name="search" placeholder="Cari nama atau username..." value="{{ $search ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="absolute inset-y-0 right-0 px-4 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Staf -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">No</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Username</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Role</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($staff as $index => $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $staff->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $user->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->username }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium capitalize rounded-full {{ $user->role == 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <a href="{{ route('admin.staf.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @if(Auth::id() != $user->id) {{-- Tidak bisa hapus diri sendiri --}}
                                <form action="{{ route('admin.staf.destroy', $user->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin menghapus staf ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 whitespace-nowrap">Tidak ada staf ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="mt-4">
            {{ $staff->appends(['search' => $search ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection
