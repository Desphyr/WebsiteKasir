@extends('layouts.app')

@section('title', 'Catat Pengeluaran')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Pembukuan</h1>

    <!-- Notifikasi Sukses/Error -->
    @include('layouts.partials.notifications')

    <div class="p-6 bg-[#FFF9C4] rounded-lg shadow-md">
        <!-- Aksi: Tambah & Filter Tanggal -->
        <div class="flex flex-col items-center justify-between mb-4 space-y-3 md:flex-row md:space-y-0">
            <a href="{{ route('admin.pengeluaran.create') }}" class="w-full px-4 py-2 font-medium text-center text-white bg-orange-400 rounded-md md:w-auto hover:bg-orange-500">
                + Catat Pengeluaran Baru
            </a>

            <form method="GET" action="{{ route('admin.pengeluaran.index') }}" class="flex items-center w-full space-x-2 md:w-auto">
                <label for="date" class="text-sm font-medium text-[#8D6E63]">Tampilkan Tanggal:</label>
                <input type="date" name="date" id="date" value="{{ $searchDate ?? '' }}"
                       class="px-4 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-400 rounded-md hover:bg-orange-500">
                    Filter
                </button>
            </form>
        </div>

        <!-- Tabel Pengeluaran -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-[#FFF8E1]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">No</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Dicatat Oleh</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-[#8D6E63] uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-[#FFF9C4] divide-y divide-yellow-200">
                    @forelse ($expenses as $index => $expense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-[#8D6E63]">{{ $expenses->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-[#8D6E63]">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-[#3E2723]">{{ $expense->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-[#8D6E63]">{{ $expense->user->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-red-600">- Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <a href="{{ route('admin.pengeluaran.edit', $expense->id) }}" class="text-orange-600 hover:text-orange-900">Edit</a>
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
