@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="flex flex-col h-full" x-data="transactionDetail()">
    
    <!-- Header: Navigation -->
    <div class="flex items-center justify-between p-4 mb-4 bg-white rounded-lg shadow">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kasir.pos.history.export', $transaction->id) }}" 
                    class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Ekspor ke Excel
            </a>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="flex-1 p-4 bg-white rounded-lg shadow print:shadow-none">
        
        <!-- Print Header -->
        <div class="hidden print:block mb-6 text-center">
            <h1 class="text-2xl font-bold">BAKARAN RESTO</h1>
            <p class="text-sm">Jl. Contoh No. 123, Jakarta</p>
            <p class="text-sm">Telp: 021-12345678</p>
            <hr class="my-4">
        </div>

        <!-- Transaction Info -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3 text-gray-900">Informasi Transaksi</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">ID Transaksi:</span>
                        <span class="font-mono font-bold">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Waktu:</span>
                        <span>{{ $transaction->transaction_time->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Kasir:</span>
                        <span>{{ $transaction->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Tipe Pembayaran:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $transaction->payment_type === 'Cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $transaction->payment_type }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3 text-gray-900">Ringkasan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Total Item:</span>
                        <span class="font-semibold">{{ $transaction->details->sum('quantity') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Total Harga:</span>
                        <span class="font-semibold">{{ 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->payment_type === 'Cash')
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">Uang Diberikan:</span>
                            <span class="font-semibold">{{ 'Rp ' . number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                            <span class="text-green-700">Kembalian:</span>
                            <span class="text-green-600">{{ 'Rp ' . number_format($transaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3 text-gray-900">Detail Pesanan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->details as $detail)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $detail->product->name }}</div>
                                        @if($detail->product->category)
                                        <div class="text-sm text-gray-500">{{ $detail->product->category->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $detail->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                {{ 'Rp ' . number_format($detail->price_per_item, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                {{ 'Rp ' . number_format($detail->price_per_item * $detail->quantity, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Back Button (Hidden in Print) -->
        <div class="flex justify-between items-center print:hidden">
            <a href="{{ route('kasir.pos.history') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke History
            </a>
            
            <div class="text-sm text-gray-600">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print\:block, .print\:block * {
            visibility: visible;
        }
        .print\:block {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:shadow-none {
            box-shadow: none !important;
        }
        .print\:hidden {
            display: none !important;
        }
    }
</style>

@push('scripts')
<script>
    function transactionDetail() {
        return {
            init() {
                // Auto focus ke print button jika ada parameter print=1
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('print') === '1') {
                    setTimeout(() => {
                        window.print();
                    }, 500);
                }
            }
        }
    }
</script>
@endpush
@endsection
