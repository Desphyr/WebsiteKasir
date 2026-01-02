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
                   class="w-full px-4 py-2 font-medium text-center text-white rounded-md md:w-auto hover:bg-green-700 {{ $transactions->isEmpty() ? 'bg-gray-400 cursor-not-allowed pointer-events-none' : 'bg-green-600' }}"
                   {{ $transactions->isEmpty() ? 'tabindex="-1"' : '' }}>
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
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction->user->full_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <ul class="list-disc list-inside">
                                    @foreach($transaction->details as $detail)
                                    <li>{{ $detail->product->name ?? 'Menu Dihapus' }} ({{ $detail->quantity }}x)</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 whitespace-nowrap">
                                @if(isset($startDate) && isset($endDate) && $startDate && $endDate)
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">Tidak ada transaksi pada rentang tanggal ini</p>
                                        <p class="text-sm">Coba ubah filter tanggal pencarian</p>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-lg font-medium">Belum ada data transaksi</p>
                                        <p class="text-sm">Transaksi akan muncul di sini setelah kasir melakukan penjualan</p>
                                    </div>
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

