@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-[#FAF7E8] overflow-x-hidden">
    <div class="max-w-6xl mx-auto px-6 py-6 space-y-6">

        {{-- HEADER --}}
        <div class="bg-[#FFF8E1] rounded-[2.5rem] px-10 py-8 flex items-center">
            <div class="flex-1">
                <h1 class="text-4xl md:text-5xl font-bold italic font-serif
                           text-[#FFAB40] leading-tight tracking-wider"
                    style="text-shadow:2px 2px 0 #fff">
                    HAVE A<br>GOOD<br>DAY
                </h1>
            </div>
            <div class="flex-1 flex justify-center items-center">
                <img src="/images/wanita.png"
                     class="h-64 md:h-72 object-contain">
            </div>
        </div>

        {{-- OVERVIEW --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#FFF9C4] rounded-2xl p-5">
                <h3 class="text-xs font-bold font-mono uppercase tracking-widest text-[#8D6E63] mb-2">
                    Pendapatan
                </h3>
                <p class="text-2xl font-bold text-[#3E2723]">
                    Rp {{ number_format($pendapatanHariIni,0,',','.') }}
                </p>
            </div>

            <div class="bg-[#FFF9C4] rounded-2xl p-5">
                <h3 class="text-xs font-bold font-mono uppercase tracking-widest text-[#8D6E63] mb-2">
                    Transaksi
                </h3>
                <p class="text-2xl font-bold text-[#3E2723]">
                    {{ $transaksiHariIni }}
                </p>
            </div>

            <div class="bg-[#FFF9C4] rounded-2xl p-5">
                <h3 class="text-xs font-bold font-mono uppercase tracking-widest text-[#8D6E63] mb-2">
                    Terjual
                </h3>
                <p class="text-2xl font-bold text-[#3E2723]">
                    {{ $menuTerjual }} <span class="text-sm font-normal">Pcs</span>
                </p>
            </div>

            <div class="bg-[#FFAB40] rounded-2xl p-5 text-white">
                <h3 class="text-xs font-bold font-mono uppercase tracking-widest mb-2 opacity-90">
                    Stok Kritis
                </h3>
                <p class="text-2xl font-bold">
                    {{ $stokKritis }} Produk
                </p>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-[#FFC107] rounded-3xl p-6 h-64">
                <h3 class="text-sm font-bold font-mono uppercase tracking-wider text-[#3E2723] mb-3">
                    Grafik Pendapatan Mingguan
                </h3>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="bg-[#FFF9C4] rounded-3xl p-6">
                <h3 class="text-sm font-bold font-mono uppercase tracking-wider text-[#3E2723] mb-3">
                    Produk Terlaris
                </h3>

                @forelse($itemTerlaris as $item)
                    <div class="flex justify-between text-sm text-[#3E2723] mb-2">
                        <span class="truncate">
                            {{ $item->product->name ?? 'Dihapus' }}
                        </span>
                        <span class="font-bold">
                            {{ $item->total_terjual }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs italic text-[#5D4037]">
                        Tidak ada produk terlaris hari ini
                    </p>
                @endforelse
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Pendapatan Harian',
                data: {!! json_encode($data) !!},
                borderColor: '#3E2723',
                backgroundColor: 'rgba(62, 39, 35, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#FFAB40',
                pointBorderColor: '#3E2723',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        color: '#3E2723',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(62, 39, 35, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#3E2723',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(62, 39, 35, 0.1)'
                    }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
