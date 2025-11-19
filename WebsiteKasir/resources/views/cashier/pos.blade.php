@extends('layouts.app')

@section('title', 'Halaman Kasir')

@section('content')
<div class="flex flex-col h-full lg:flex-row" x-data="posSystem()">
    
    <!-- Area Menu (Sebelah Kiri) -->
    <div class="w-full lg:w-3/5 xl:w-2/3">
        <div class="p-4 bg-white rounded-lg shadow">
            <!-- Header: Search & Filter -->
            <div class="flex flex-col gap-4 mb-4 md:flex-row">
                <!-- Search Bar -->
                <div class="relative grow">
                    <input type="text" x-model="search" @input.debounce.500ms="filterProducts" placeholder="Cari menu..."
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                </div>
                <!-- Filter Kategori -->
                <select x-model="category" @change="filterProducts" class="border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Notifikasi Error/Sukses -->
            @include('layouts.partials.notifications')

            <!-- Grid Menu -->
            <div class="grid grid-cols-2 gap-4 overflow-y-auto sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 h-[calc(100vh-220px)] no-scrollbar p-1">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer hover:shadow-md" 
                         @click="product.stock > 0 ? addToCart(product) : alert('Stok produk habis!')">
                        <div class="relative">
                            <img :src="product.image_url || 'https://placehold.co/300x300/e2e8f0/adb5bd?text=Menu'" :alt="product.name" class="object-cover w-full h-24 md:h-32">
                            <template x-if="product.stock <= 0">
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                    <span class="px-2 py-1 text-xs font-bold text-white bg-red-600 rounded">STOK HABIS</span>
                                </div>
                            </template>
                        </div>
                        <div class="p-3">
                            <h5 class="font-semibold text-gray-800 text-md" x-text="product.name"></h5>
                            <p class="text-sm text-gray-600" x-text="formatCurrency(product.price)"></p>
                            <span class="text-xs font-medium" :class="product.stock > 5 ? 'text-green-600' : (product.stock > 0 ? 'text-yellow-600' : 'text-red-600')" x-text="`Stok: ${product.stock}`"></span>
                        </div>
                    </div>
                </template>
                <template x-if="filteredProducts.length === 0">
                    <p class="col-span-full text-center text-gray-500 mt-10">Tidak ada menu yang ditemukan.</p>
                </template>
            </div>
        </div>
    </div>

    <!-- Area Keranjang (Sebelah Kanan) -->
    <div class="w-full lg:w-2/5 xl:w-1/3">
        <form action="{{ route('kasir.pos.bayar') }}" method="POST" class="flex flex-col h-full p-4">
            @csrf
            <div class="flex flex-col grow p-4 bg-white rounded-lg shadow">
                <h2 class="pb-3 text-xl font-bold border-b border-gray-200">Pesanan</h2>
                
                <!-- Daftar Item Keranjang -->
                <div class="grow overflow-y-auto no-scrollbar h-[calc(100vh-450px)] lg:h-auto lg:grow">
                    <template x-if="cart.length === 0">
                        <p class="mt-10 text-center text-gray-500">Keranjang masih kosong.</p>
                    </template>
                    
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="grow pr-4">
                                <h4 class="font-semibold text-gray-800" x-text="item.name"></h4>
                                <p class="text-sm text-gray-600" x-text="formatCurrency(item.price)"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" @click="updateQuantity(item.id, 'dec')" class="flex items-center justify-center w-6 h-6 text-white bg-red-500 rounded-full hover:bg-red-600">-</button>
                                <span class="w-8 text-center" x-text="item.quantity"></span>
                                <button type="button" @click="updateQuantity(item.id, 'inc')" class="flex items-center justify-center w-6 h-6 text-white bg-green-500 rounded-full hover:bg-green-600">+</button>
                            </div>
                            <p class="w-20 ml-4 font-semibold text-right" x-text="formatCurrency(item.price * item.quantity)"></p>
                        </div>
                    </template>
                </div>
                
                <!-- Total & Pembayaran -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-700">Total Item:</span>
                        <span class="font-bold text-gray-900" x-text="totalItems"></span>
                    </div>
                    <div class="flex justify-between mb-4 text-xl">
                        <span class="font-semibold text-gray-800">Total Harga:</span>
                        <span class="font-bold text-indigo-600" x-text="formatCurrency(totalPrice)"></span>
                    </div>

                    <!-- Tipe Pembayaran -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tipe Pembayaran</label>
                        <select name="payment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Cash">Cash</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <!-- Hidden Inputs untuk Form -->
                    <input type="hidden" name="cart" :value="JSON.stringify(cart)">
                    <input type="hidden" name="total" :value="totalPrice">

                    <!-- Tombol Bayar -->
                    <button type="submit" 
                            :disabled="cart.length === 0"
                            :class="cart.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                            class="w-full px-4 py-3 font-bold text-white rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Bayar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function posSystem() {
        return {
            products: @json($products), // Ambil data produk dari Blade
            filteredProducts: [],
            cart: [],
            search: '',
            category: '',

            init() {
                this.filteredProducts = this.products;
                // NF-01: Waktu muat halaman < 3 detik (data sudah di-load)
            },

            filterProducts() {
                this.filteredProducts = this.products.filter(product => {
                    const nameMatch = product.name.toLowerCase().includes(this.search.toLowerCase());
                    const categoryMatch = this.category === '' || product.category_id == this.category;
                    return nameMatch && categoryMatch;
                });
            },

            addToCart(product) {
                // NF-01: Penambahan item < 500ms
                if (product.stock <= 0) {
                    // Seharusnya tidak terjadi karena tombol dinonaktifkan, tapi ini pengaman ganda
                    return;
                }

                const existingItem = this.cart.find(item => item.id === product.id);

                if (existingItem) {
                    if (existingItem.quantity < product.stock) {
                        existingItem.quantity++;
                    } else {
                        // Anda bisa mengganti alert() dengan notifikasi kustom jika mau
                        alert('Stok tidak mencukupi.');
                    }
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price), // Pastikan ini adalah angka
                        quantity: 1,
                        stock: product.stock
                    });
                }
            },

            updateQuantity(id, type) {
                const item = this.cart.find(item => item.id === id);
                if (!item) return;

                if (type === 'inc') {
                    if (item.quantity < item.stock) { // Validasi stok
                        item.quantity++;
                    } else {
                        alert('Stok tidak mencukupi.');
                    }
                } else if (type === 'dec') {
                    item.quantity--;
                    if (item.quantity <= 0) { // Validasi tidak boleh 0
                        this.cart = this.cart.filter(item => item.id !== id);
                    }
                }
            },

            get totalItems() {
                return this.cart.reduce((total, item) => total + item.quantity, 0);
            },

            get totalPrice() {
                return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }
        }
    }
</script>
@endpush
@endsection
