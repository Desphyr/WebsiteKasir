@extends('layouts.app')

@section('title', 'Catat Pengeluaran')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Catat Pengeluaran</h1>

    <!-- Notifikasi Sukses/Error -->
    @include('layouts.partials.notifications')

    <div class="p-6 bg-white rounded-lg shadow-md">
        <!-- Aksi: Tambah & Filter Tanggal -->
        <div class="flex flex-col items-center justify-between mb-4 space-y-3 md:flex-row md:space-y-0">
            <a href="{{ route('admin.pengeluaran.create') }}" class="w-full px-4 py-2 font-medium text-center text-white bg-indigo-600 rounded-md md:w-auto hover:bg-indigo-700">
                + Catat Pengeluaran Baru
            </a>

            <form method="GET" action="{{ route('admin.pengeluaran.index') }}" class="flex items-center w-full space-x-2 md:w-auto">
                <label for="date" class="text-sm font-medium text-gray-700">Tampilkan Tanggal:</label>
                <input type="date" name="date" id="date" value="{{ $searchDate ?? '' }}"
                       class="px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    Filter
                </button>
            </form>
        </div>

        <!-- Tabel Pengeluaran -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">No</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Dicatat Oleh</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($expenses as $index => $expense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $expenses->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $expense->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $expense->user->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-red-600">- Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <a href="{{ route('admin.pengeluaran.edit', $expense->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.pengeluaran.destroy', $expense->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin menghapus catatan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 whitespace-nowrap">Tidak ada pengeluaran pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="mt-4">
            {{ $expenses->appends(['date' => $searchDate ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection
