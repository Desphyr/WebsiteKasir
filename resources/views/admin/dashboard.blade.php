@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Dashboard Admin</h1>

    <!-- Ringkasan Hari Ini -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Pendapatan Hari Ini -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-600">Pendapatan Hari Ini</h3>
            <p class="mt-2 text-3xl font-bold text-indigo-600">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-gray-500">(Reset pukul 00.00 WIB)</p>
        </div>
        <!-- Transaksi Hari Ini -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-600">Transaksi Hari Ini</h3>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $transaksiHariIni }}</p>
            <p class="mt-1 text-sm text-gray-500">Total transaksi</p>
        </div>
        <!-- Menu Terjual (Pcs) -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-600">Menu Terjual (Pcs)</h3>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $menuTerjual }}</p>
            <p class="mt-1 text-sm text-gray-500">Total item terjual</p>
        </div>
        <!-- Stok Kritis -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-600">Stok Kritis</h3>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ $stokKritis }}</p>
            <p class="mt-1 text-sm text-gray-500">Item dengan stok < 5</p>
        </div>
    </div>

    <!-- Grafik dan Item Terlaris -->
    <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-3">
        <!-- Grafik Pendapatan -->
        <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-2">
            <h3 class="mb-4 text-xl font-semibold">Grafik Pendapatan (7 Hari Terakhir)</h3>
            <!-- Container dengan tinggi tetap agar grafik muncul -->
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Item Terlaris Hari Ini -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h3 class="mb-4 text-xl font-semibold">Item Terlaris Hari Ini</h3>
            <div class="space-y-4 overflow-y-auto max-h-72">
                @forelse ($itemTerlaris as $item)
                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded transition">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->product->name ?? 'Menu Dihapus' }}</p>
                            <p class="text-sm text-gray-500">{{ $item->product->category->name ?? '' }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-bold text-indigo-800 bg-indigo-100 rounded-full">{{ $item->total_terjual }}x</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">Belum ada penjualan hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart');
        
        // Data dari controller (Menggunakan json_encode eksplisit untuk menghindari SyntaxError)
        const chartLabels = {!! json_encode($labels) !!};
        const chartData = {!! json_encode($data) !!};

        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'line', // UBAH KE LINE (GARIS)
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: chartData,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)', // Warna area bawah garis (lebih transparan)
                        borderColor: 'rgba(79, 70, 229, 1)', // Warna garis utama (Indigo)
                        borderWidth: 3, // Garis lebih tebal sedikit
                        tension: 0.4, // Membuat garis melengkung (smooth)
                        fill: true, // Mengisi area di bawah garis
                        pointBackgroundColor: '#ffffff', // Titik data putih
                        pointBorderColor: 'rgba(79, 70, 229, 1)', // Pinggiran titik ungu
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Penting agar mengikuti tinggi container
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 2],
                                color: '#e5e7eb'
                            },
                            ticks: {
                                // Format Rupiah Singkat (misal 1jt, 500rb)
                                callback: function(value, index, values) {
                                    return new Intl.NumberFormat('id-ID', { notation: "compact", compactDisplay: "short" }).format(value);
                                },
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection