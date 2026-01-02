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
<body class="bg-gray-100 font-sans antialiased">
    
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">
        <!-- Sidebar (Hanya tampil jika user login) -->
        @auth
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 transform bg-gray-900 text-white overflow-y-auto transition duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center p-4 bg-gray-800">
                <span class="text-2xl font-bold">Bakaran Dua Hati</span>
            </div>
            
            <nav class="mt-4 sidebar-menu">
                @if(Auth::user()->role === 'admin')
                    <!-- Menu Admin -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.dashboard*') ? 'bg-gray-700 font-bold' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.menu.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.menu*') ? 'bg-gray-700 font-bold' : '' }}">Manajemen Menu</a>
                    <a href="{{ route('admin.kategori.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.kategori*') ? 'bg-gray-700 font-bold' : '' }}">Manajemen Kategori</a>
                    <a href="{{ route('admin.laporan.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.laporan*') ? 'bg-gray-700 font-bold' : '' }}">Laporan Penjualan</a>
                    <a href="{{ route('admin.pengeluaran.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.pengeluaran*') ? 'bg-gray-700 font-bold' : '' }}">Catat Pengeluaran</a>
                    <a href="{{ route('admin.staf.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.staf*') ? 'bg-gray-700 font-bold' : '' }}">Manajemen Staf</a>
                    <a href="{{ route('admin.profil.edit') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.profil*') ? 'bg-gray-700 font-bold' : '' }}">Profil</a>
                
                @elseif(Auth::user()->role === 'kasir')
                    <!-- Menu Kasir -->
                    <a href="{{ route('kasir.pos') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('kasir.pos*') ? 'bg-gray-700 font-bold' : '' }}">Halaman Kasir</a>
                @endif
                
                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">Logout</button>
                </form>
            </nav>
        </aside>
        @endauth
        
        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col overflow-hidden">
            @auth
            <!-- Header -->
            <header class="flex items-center justify-between p-4 bg-white border-b lg:hidden">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <h1 class="text-xl font-semibold">@yield('title')</h1>
                <div></div> <!-- Spacer -->
            </header>
            @endauth

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts') <!-- Untuk script tambahan per halaman -->
</body>
</html>

