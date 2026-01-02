@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="flex flex-col h-full" x-data="historySystem()">
    
    <!-- Header: Navigation -->
    <div class="flex items-center justify-between p-4 mb-4 bg-[#FFF9C4] rounded-lg shadow">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-[#3E2723]">History Order</h1>
        </div>
        <div class="text-sm text-[#8D6E63]">
            {{ Auth::user()->name }} - {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Filter Section -->
    <div class="p-4 mb-4 bg-[#FFF9C4] rounded-lg shadow">
        <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-[#8D6E63]">Periode</label>
                <select name="period" @change="submitForm()" class="px-3 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
                    <option value="">Semua Periode</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-[#8D6E63]">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="px-3 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-[#8D6E63]">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="px-3 py-2 border border-yellow-300 rounded-md focus:ring-yellow-400 focus:border-yellow-400">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-white bg-orange-400 rounded-md hover:bg-orange-500">
                    Filter
                </button>
                <a href="{{ route('kasir.pos.history') }}" class="px-4 py-2 text-[#8D6E63] bg-yellow-200 rounded-md hover:bg-yellow-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Notifikasi Error/Sukses -->
    @include('layouts.partials.notifications')

    <!-- History Table -->
    <div class="flex-1 p-4 bg-[#FFF9C4] rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-[#FFF8E1]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#8D6E63] uppercase tracking-wider">ID Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#8D6E63] uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#8D6E63] uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#8D6E63] uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#8D6E63] uppercase tracking-wider">Kembalian</th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-[#8D6E63] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-[#FFF9C4] divide-y divide-yellow-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-yellow-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#3E2723]">
                            #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#8D6E63]">
                            {{ $transaction->transaction_time->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-[#3E2723]">
                            {{ 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $transaction->payment_type === 'Cash' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $transaction->payment_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#8D6E63]">
                            @if($transaction->payment_type === 'Cash' && $transaction->change_amount > 0)
                                <span class="text-green-600 font-semibold">{{ 'Rp ' . number_format($transaction->change_amount, 0, ',', '.') }}</span>
                            @elseif($transaction->payment_type === 'Cash')
                                <span class="text-yellow-400">-</span>
                            @else
                                <span class="text-yellow-400">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('kasir.pos.history.show', $transaction->id) }}" 
                               class="text-orange-600 hover:text-orange-900">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 whitespace-nowrap">
                            @if(request('start_date') || request('end_date') || request('period'))
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada transaksi pada rentang tanggal ini</p>
                                    <p class="text-sm">Coba ubah filter atau tanggal pencarian</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada data transaksi</p>
                                    <p class="text-sm">Transaksi akan muncul di sini setelah Anda melakukan penjualan</p>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Menampilkan {{ $transactions->firstItem() ?? 0 }} hingga {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
            </div>
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function historySystem() {
        return {
            init() {
                // Auto submit form when period is changed
                const periodSelect = document.querySelector('select[name="period"]');
                if (periodSelect) {
                    periodSelect.addEventListener('change', () => {
                        this.submitForm();
                    });
                }
            },
            
            submitForm() {
                const form = event.target.closest('form');
                if (form) {
                    form.submit();
                }
            }
        }
    }
</script>
@endpush
@endsection

