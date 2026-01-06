@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Laporan Penjualan</h1>

    <!-- Notifikasi Sukses/Error -->
    @include('layouts.partials.notifications')

    <div class="p-6 bg-[#FFF9C4] rounded-lg shadow-md">
        <!-- Filter Tanggal & Ekspor -->
        <form method="GET" action="{{ route('admin.laporan.index') }}">
            <div class="flex flex-col items-center justify-between mb-4 space-y-3 md:flex-row md:space-y-0">
                <div class="flex items-center space-x-2">
                    <label for="start_date" class="text-sm font-medium text-gray-700">Dari:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                           class="px-4 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
                    <label for="end_date" class="text-sm font-medium text-[#8D6E63]">Sampai:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                           class="px-4 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-400 rounded-md hover:bg-orange-500">
                        Filter
                    </button>
                </div>
                
                <a href="{{ route('admin.laporan.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="w-full px-4 py-2 font-medium text-center text-white bg-green-600 rounded-md md:w-auto hover:bg-green-700 {{ $transactions->total() === 0 ? 'pointer-events-none opacity-50 cursor-not-allowed' : '' }}">
                    Ekspor ke Excel
                </a>
            </div>
        </form>

        <!-- Tabel Laporan -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-[#FFF8E1]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">No</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Tipe Pembayaran</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Nama Kasir</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Detail Pesanan</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-[#8D6E63] uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-[#FFF9C4] divide-y divide-yellow-200">
                    @forelse ($transactions as $index => $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transactions->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction->transaction_time->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium capitalize rounded-full {{ $transaction->payment_type == 'Cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $transaction->payment_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction->user->full_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <ul class="list-disc list-inside">
                                    @foreach($transaction->details as $detail)
                                    <li>{{ $detail->product->name ?? 'Menu Dihapus' }} ({{ $detail->quantity }}x)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 whitespace-nowrap">
                                @if(request('start_date') || request('end_date'))
                                    Tidak ada transaksi pada rentang tanggal ini.
                                @else
                                    Belum ada data transaksi.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="mt-4">
            {{ $transactions->appends(['start_date' => $startDate, 'end_date' => $endDate])->links() }}
        </div>
    </div>
</div>
@endsection