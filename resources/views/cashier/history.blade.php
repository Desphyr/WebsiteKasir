@extends('layouts.app')

@section('title', 'History Order')

@section('content')
<div class="flex flex-col h-full" x-data="historySystem()">
    
    <!-- Header: Navigation -->
    <div class="flex items-center justify-between p-4 mb-4 bg-white rounded-lg shadow">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">History Order</h1>
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button @click="window.location.href='{{ route('kasir.pos') }}'" 
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-md">
                    POS
                </button>
                <button @click="window.location.href='{{ route('kasir.pos.history') }}'" 
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md">
                    History Order
                </button>
            </div>
        </div>
        <div class="text-sm text-gray-600">
            {{ Auth::user()->name }} - {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Filter Section -->
    <div class="p-4 mb-4 bg-white rounded-lg shadow">
        <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-gray-700">Periode</label>
                <select name="period" @change="submitForm()" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Periode</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('kasir.pos.history') }}" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Notifikasi Error/Sukses -->
    @include('layouts.partials.notifications')

    <!-- History Table -->
    <div class="flex-1 p-4 bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kembalian</th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->transaction_time->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $transaction->payment_type === 'Cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $transaction->payment_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($transaction->payment_type === 'Cash' && $transaction->change_amount > 0)
                                <span class="text-green-600 font-semibold">{{ 'Rp ' . number_format($transaction->change_amount, 0, ',', '.') }}</span>
                            @elseif($transaction->payment_type === 'Cash')
                                <span class="text-gray-400">-</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('kasir.pos.history.show', $transaction->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada transaksi ditemukan</p>
                                <p class="text-sm">Coba ubah filter atau tanggal pencarian</p>
                            </div>
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
