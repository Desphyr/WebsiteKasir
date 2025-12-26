@extends('layouts.app')

@section('title', 'Point of Sale (POS)')

@section('content')
<div class="flex h-[calc(100vh-(--spacing(16)))] bg-gray-50" x-data="posSystem()">
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-extrabold text-gray-800">
                    Kasir
                </h1>
            </div>
            <div class="text-sm font-medium text-gray-500 bg-gray-50 px-3 py-1 rounded-full border border-gray-200 flex items-center gap-2">
                <span>👤 {{ Auth::user()->name }}</span>
                <span class="text-gray-300">|</span>
                <span>🕒 {{ now()->format('H:i') }}</span>
            </div>
        </header>

        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 shrink-0">
            <div class="md:col-span-2 relative">
                <input type="text" x-model="search" @input.debounce.500ms="filterProducts" placeholder="Cari menu (e.g. Nasi Goreng)..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select x-model="category" @change="filterProducts" class="w-full py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <main class="flex-1 overflow-y-auto p-6 pt-0 scroll-smooth custom-scrollbar">
            @include('layouts.partials.notifications')

            <!-- Menu Grid Section (with right padding for fixed payment) -->
            <div class="pr-96 pb-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-indigo-400 transition-all duration-200 cursor-pointer overflow-hidden flex flex-col"
                             @click="product.stock > 0 ? addToCart(product) : null">
                            
                            <div class="relative aspect-square overflow-hidden bg-gray-100">
                                <img :src="product.image_url || 'https://placehold.co/400x400/f1f5f9/94a3b8?text=Menu'" 
                                     :alt="product.name" 
                                     class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                                
                                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>

                                <template x-if="product.stock <= 0">
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-[1px] z-20">
                                        <span class="bg-red-600 text-white text-sm font-bold px-4 py-1 rounded-full uppercase tracking-wider shadow-lg transform -rotate-12">Habis</span>
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 text-center bg-white flex flex-col items-center justify-center gap-2 h-auto min-h-[90px]">
                                <h3 class="font-bold text-gray-800 text-base leading-tight line-clamp-2 group-hover:text-indigo-600 transition-colors" x-text="product.name"></h3>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full border"
                                      :class="product.stock > 5 ? 'text-gray-500 border-gray-200 bg-gray-50' : (product.stock > 0 ? 'text-yellow-600 border-yellow-200 bg-yellow-50' : 'text-red-500 border-red-200 bg-red-50')"
                                      x-text="`Stok: ${product.stock}`">
                                </span>
                            </div>
                        </div>
                    </template>

                    <template x-if="filteredProducts.length === 0">
                        <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                            <div class="bg-gray-100 p-4 rounded-full mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <p class="text-gray-500 font-medium">Menu tidak ditemukan.</p>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </div>

    <!-- Fixed Payment Bar -->
    <div class="w-96 bg-white border-l border-gray-200 flex flex-col shadow-2xl z-20 fixed right-0 top-[calc(4rem+1px)] h-[calc(100vh-4rem)]">
        
        <div class="p-5 bg-linear-to-r from-indigo-600 to-blue-500 text-white shadow-md shrink-0 rounded-bl-xl">
            <h2 class="text-lg font-bold flex items-center justify-between">
                <span>🛒 Pesanan</span>
                <span class="text-xs font-bold text-indigo-600 bg-white px-2.5 py-1 rounded-full shadow-sm" x-text="`${totalItems} Item`"></span>
            </h2>
        </div>

        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-gray-50/50">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center opacity-60">
                    <img src="https://img.icons8.com/ios/100/cbd5e1/shopping-cart.png" class="w-16 h-16 mb-2 opacity-50">
                    <p class="text-gray-400 text-sm">Keranjang kosong</p>
                </div>
            </template>
            
            <div class="space-y-3">
                <template x-for="item in cart" :key="item.id">
                    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex flex-col gap-2 relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                        
                        <div class="pl-2 flex justify-between items-start">
                            <h4 class="font-bold text-gray-800 text-sm leading-tight w-2/3" x-text="item.name"></h4>
                            <span class="font-bold text-indigo-600 text-sm" x-text="formatCurrency(item.price * item.quantity)"></span>
                        </div>
                        
                        <div class="pl-2 flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                            <div class="text-xs font-medium text-gray-400" x-text="formatCurrency(item.price)"></div>

                            <div class="flex items-center gap-2">
                                <button @click="updateQuantity(item.id, 'dec')" 
                                        class="w-7 h-7 flex items-center justify-center bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded-full transition-all shadow-sm">
                                    <span class="font-bold text-lg leading-none mb-0.5">-</span>
                                </button>
                                <span class="w-6 text-center text-sm font-bold text-gray-800" x-text="item.quantity"></span>
                                <button @click="updateQuantity(item.id, 'inc')" 
                                        class="w-7 h-7 flex items-center justify-center bg-green-100 hover:bg-green-500 text-green-600 hover:text-white rounded-full transition-all shadow-sm">
                                    <span class="font-bold text-lg leading-none mb-0.5">+</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="p-5 bg-white border-t border-gray-200 shrink-0 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] rounded-tl-xl z-20">
            <form action="{{ route('kasir.pos.bayar') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-4">
                    <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 flex justify-between items-center">
                        <span class="text-indigo-800 font-medium text-sm">Total Tagihan</span>
                        <span class="text-xl font-extrabold text-indigo-700" x-text="formatCurrency(totalPrice)"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="paymentType = 'Cash'; cashAmount = 0" 
                                class="px-3 py-2.5 text-sm font-bold rounded-lg border transition-all duration-200 flex items-center justify-center gap-2"
                                :class="paymentType === 'Cash' ? 'border-indigo-600 bg-indigo-600 text-white shadow-md' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            Tunai
                        </button>
                        <button type="button" @click="paymentType = 'QRIS'; cashAmount = 0"
                                class="px-3 py-2.5 text-sm font-bold rounded-lg border transition-all duration-200 flex items-center justify-center gap-2"
                                :class="paymentType === 'QRIS' ? 'border-indigo-600 bg-indigo-600 text-white shadow-md' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            QRIS
                        </button>
                    </div>
                    <input type="hidden" name="payment_type" x-model="paymentType">

                    <div x-show="paymentType === 'Cash'" x-transition class="space-y-2 pt-1">
                        <div class="relative group">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="cash_amount" x-model="cashAmount" placeholder="0"
                                   class="w-full pl-10 pr-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-right font-bold text-lg text-gray-800 focus:ring-0 focus:border-indigo-600 transition-colors">
                        </div>

                        <div class="flex justify-between items-center px-1">
                             <span class="text-xs text-gray-500 font-medium">Uang Diterima:</span>
                             <span class="text-sm font-bold text-gray-800" x-text="cashAmount ? formatCurrency(cashAmount) : 'Rp 0'"></span>
                        </div>

                        <div class="border-b border-dashed border-gray-300 my-2"></div>

                        <div class="flex justify-between items-center px-1 bg-gray-50 p-2 rounded">
                            <span class="text-sm font-bold text-gray-600">Kembalian:</span>
                            <span class="font-extrabold text-lg" 
                                  :class="(cashAmount - totalPrice) >= 0 ? 'text-green-600' : 'text-red-500'"
                                  x-text="formatCurrency(Math.max(0, cashAmount - totalPrice))"></span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="cart" :value="JSON.stringify(cart)">
                <input type="hidden" name="total" :value="totalPrice">

                <button type="submit" 
                        :disabled="cart.length === 0 || (paymentType === 'Cash' && cashAmount < totalPrice)"
                        class="w-full py-3.5 text-white font-bold text-lg rounded-xl shadow-lg transition-all transform active:scale-[0.98] flex items-center justify-center gap-2"
                        :class="cart.length === 0 || (paymentType === 'Cash' && cashAmount < totalPrice) 
                            ? 'bg-gray-400 cursor-not-allowed shadow-none' 
                            : 'bg-green-600 hover:bg-green-700 shadow-green-200'">
                    
                    <span>KONFIRMASI PEMBAYARAN</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function posSystem() {
        return {
            products: @json($products),
            filteredProducts: [],
            cart: [],
            search: '',
            category: '',
            paymentType: 'Cash',
            cashAmount: '', 
            
            init() {
                this.filteredProducts = this.products;
            },

            filterProducts() {
                this.filteredProducts = this.products.filter(product => {
                    const nameMatch = product.name.toLowerCase().includes(this.search.toLowerCase());
                    const categoryMatch = this.category === '' || product.category_id == this.category;
                    return nameMatch && categoryMatch;
                });
            },

            addToCart(product) {
                const existingItem = this.cart.find(item => item.id === product.id);
                if (existingItem) {
                    if (existingItem.quantity < product.stock) {
                        existingItem.quantity++;
                    } else {
                        // Toast here
                    }
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1,
                        stock: product.stock
                    });
                }
            },

            updateQuantity(id, type) {
                const item = this.cart.find(item => item.id === id);
                if (!item) return;

                if (type === 'inc') {
                    if (item.quantity < item.stock) item.quantity++;
                } else if (type === 'dec') {
                    item.quantity--;
                    if (item.quantity <= 0) {
                        this.cart = this.cart.filter(i => i.id !== id);
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
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            }
        }
    }
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>
@endpush
@endsection

