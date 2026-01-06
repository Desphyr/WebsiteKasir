<!-- Header dengan design retro -->
<header class="bg-yellow-50 border-b-2 border-yellow-200 shadow-sm">
    <div class="max-w-full px-6 py-2">
        <div class="flex items-center justify-between">
            
            <!-- Bagian Kiri: Logo, Judul, dan Tanggal -->
            <div class="flex items-center gap-3">
                <!-- Logo -->
                <div class="shrink-0">
                    <img src="{{ asset('images/bakaran-logo.png') }}" alt="Bakaran Logo" class="h-12 w-12 object-cover rounded-full border-2 border-yellow-300">
                </div>
                
                <!-- Judul dan Tanggal -->
                <div>
                    <h1 class="text-2xl font-bold text-amber-900 leading-tight">
                        @if(Auth::user()->role === 'admin')
                            @switch(Route::currentRouteName())
                                @case('admin.dashboard')
                                    Dashboard
                                    @break
                                @case('admin.menu.index')
                                @case('admin.menu.create')
                                @case('admin.menu.edit')
                                @case('admin.menu.show')
                                    Manajemen Menu
                                    @break
                                @case('admin.kategori.index')
                                @case('admin.kategori.create')
                                @case('admin.kategori.edit')
                                @case('admin.kategori.show')
                                    Manajemen Kategori
                                    @break
                                @case('admin.laporan.index')
                                    Laporan Penjualan
                                    @break
                                @case('admin.pengeluaran.index')
                                @case('admin.pengeluaran.create')
                                @case('admin.pengeluaran.edit')
                                @case('admin.pengeluaran.show')
                                    Catat Pengeluaran
                                    @break
                                @case('admin.staf.index')
                                @case('admin.staf.create')
                                @case('admin.staf.edit')
                                @case('admin.staf.show')
                                    Manajemen Staf
                                    @break
                                @case('admin.profil.edit')
                                    Profil
                                    @break
                                @default
                                    Dashboard
                            @endswitch
                        @elseif(Auth::user()->role === 'kasir')
                            @switch(Route::currentRouteName())
                                @case('kasir.pos')
                                    Halaman Kasir
                                    @break
                                @case('kasir.pos.history')
                                @case('kasir.pos.history.show')
                                    Riwayat Pesanan
                                    @break
                                @default
                                    Point of Sale (POS)
                            @endswitch
                        @endif
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ now()->locale('id')->dayName }}, {{ now()->locale('id')->format('d F Y') }}
                    </p>
                </div>
            </div>
            
            <!-- Bagian Tengah: Dekorasi Awan -->
            <div class="flex-1 flex justify-center relative">
                <svg class="w-24 h-12 opacity-40" viewBox="0 0 100 50" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 30 Q10 20 20 20 Q25 10 35 10 Q45 10 50 20 Q60 20 60 30 Q60 40 50 40 L15 40 Q10 40 10 30" fill="#87CEEB" opacity="0.6"/>
                </svg>
            </div>
            
            <!-- Bagian Kanan: Tombol Logout -->
            <div class="shrink-0">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-6 py-2 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold rounded-l-full rounded-r-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                        <span class="italic">logout</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
