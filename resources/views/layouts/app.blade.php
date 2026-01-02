<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi POS')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js (Penting untuk POS) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js (Untuk Dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- IBM Plex Mono Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* IBM Plex Mono untuk sidebar */
        .sidebar-menu {
            font-family: 'IBM Plex Mono', monospace;
        }
        
        /* Sembunyikan scrollbar untuk chrome, safari, opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Sembunyikan scrollbar untuk IE, Edge, Firefox */
        .no-scrollbar {
            -ms-overflow-style: none; /* IE dan Edge */
            scrollbar-width: none; /* Firefox */
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#FFFBF0] font-sans antialiased">
    
    <div x-data="{ sidebarOpen: false }" class="flex flex-col h-screen bg-[#FFFBF0]">
        @auth
        <!-- Header dengan design retro -->
        @include('layouts.partials.header')
        
        <!-- Header Mobile Toggle -->
        <header class="flex items-center justify-between p-4 bg-white border-b lg:hidden">
            <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
            <h1 class="text-xl font-semibold">@yield('title')</h1>
            <div></div> <!-- Spacer -->
        </header>
        @endauth
        
        <!-- Sidebar dan Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar (Hanya tampil jika user login) -->
            @auth
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 transform bg-yellow-100 text-gray-900 overflow-y-auto transition duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-lg">

                <!-- Ikon Atas -->
                <div class="p-6 border-b-2 border-yellow-300">
                    <div class="flex justify-center">
                        @php
                            $currentRoute = Route::currentRouteName();
                            $sidebarIcon = match(true) {
                                str_contains($currentRoute, 'admin.dashboard') => 'sidebar-dashboard.png',
                                str_contains($currentRoute, 'admin.menu') => 'sidebar-menu.png',
                                str_contains($currentRoute, 'admin.kategori') => 'list-icon.svg',
                                str_contains($currentRoute, 'admin.laporan') => 'sidebar-laporan.png',
                                str_contains($currentRoute, 'admin.pengeluaran') => 'sidebar-pembukuan.png',
                                str_contains($currentRoute, 'admin.staf') => 'sidebar-staf.png',
                                str_contains($currentRoute, 'kasir.pos') => 'sidebar-kasir.png',
                                default => 'sidebar-icon.png',
                            };
                        @endphp
                        <img src="{{ asset('images/' . $sidebarIcon) }}" alt="Sidebar Icon" class="h-16 w-16 object-cover rounded-lg border-2 border-yellow-400">
                    </div>
                </div>

                <!-- Daftar Menu -->
                <nav class="mt-6 px-4">
                    @if(Auth::user()->role === 'admin')
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            Dashboard
                        </a>
                        
                        <!-- Kelola Menu -->
                        <a href="{{ route('admin.menu.index') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.menu*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            Kelola menu
                        </a>
                        
                        <!-- Kelola Kategori -->
                        <a href="{{ route('admin.kategori.index') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.kategori*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            Kelola kategori
                        </a>
                        
                        <!-- Laporan Penjualan -->
                        <a href="{{ route('admin.laporan.index') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.laporan*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            Laporan Penjualan
                        </a>
                        
                        <!-- Kelola Staf -->
                        <a href="{{ route('admin.staf.index') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.staf*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            Kelola staf
                        </a>
                        
                        <!-- Pembukuan -->
                        <a href="{{ route('admin.pengeluaran.index') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.pengeluaran*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            <span class="lowercase">pembukuan</span>
                        </a>
                    
                    @elseif(Auth::user()->role === 'kasir')
                        <!-- Halaman Kasir (POS) -->
                        <a href="{{ route('kasir.pos') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('kasir.pos') && !request()->routeIs('kasir.pos.history*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            POS
                        </a>
                        
                        <!-- Riwayat Transaksi -->
                        <a href="{{ route('kasir.pos.history') }}" class="flex items-center px-4 py-3 mb-3 text-gray-800 font-mono italic rounded-lg transition-colors duration-200 {{ request()->routeIs('kasir.pos.history*') ? 'bg-orange-400 font-bold shadow-md' : 'hover:bg-yellow-200' }}">
                            History
                        </a>
                    @endif
                </nav>
            </aside>
            @endauth
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#FFFBF0] p-4 lg:p-6 min-h-0">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts') <!-- Untuk script tambahan per halaman -->
</body>
</html>

