@extends('layouts.app')

@section('title', 'Login - Bakaran Dua Hati')

@section('content')
    <!-- 
        TRIK FULL SCREEN:
        Class 'fixed inset-0 z-50' digunakan untuk memaksa elemen ini 
        keluar dari pembungkus layout.app dan menutupi satu layar penuh.
    -->
    <div class="fixed inset-0 z-50 flex h-screen w-screen bg-[#FFDE68] font-[Poppins]">
        
        <!-- Load Font Poppins Manual & CSS Hack -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap');
            
            /* Hack Autofill Chrome agar tetap kuning */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus, 
            input:-webkit-autofill:active {
                -webkit-box-shadow: 0 0 0 30px #FFDE68 inset !important;
                -webkit-text-fill-color: #000 !important;
                transition: background-color 5000s ease-in-out 0s;
            }
        </style>

        <!-- ========================================== -->
        <!-- BAGIAN KIRI: GAMBAR ILUSTRASI BEBEK        -->
        <!-- ========================================== -->
        <div class="hidden lg:block w-1/2 relative h-full">
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ asset('images/duck-pattern.jpg') }}');">
            </div>
            <div class="absolute inset-0 bg-black/5"></div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN KANAN: FORM LOGIN                   -->
        <!-- ========================================== -->
        <div class="w-full lg:w-1/2 relative flex flex-col justify-center items-center bg-[#FFDE68] h-full">
            
            <!-- Dekorasi Gelombang (SVG) -->
            <div class="absolute bottom-0 left-0 w-full z-0 leading-none">
                <svg class="block w-full h-[120px] md:h-[180px]" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                    <path fill="#FF9E4F" fill-opacity="1" d="M0,224L60,213.3C120,203,240,181,360,186.7C480,192,600,224,720,218.7C840,213,960,171,1080,160C1200,149,1320,171,1380,181.3L1440,192L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
                </svg>
            </div>

            <!-- Container Konten -->
            <div class="relative z-10 w-full max-w-md px-10">
                
                <!-- LOGO -->
                <div class="flex justify-center mb-12">
                    <div class="w-40 h-40 bg-white rounded-full flex items-center justify-center border-[6px] border-[#E13B23] shadow-xl transform hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/bakaran-logo.png') }}" alt="Logo Bakaran" class="w-28 h-28 object-contain">
                    </div>
                </div>

                <!-- ALERT ERROR -->
                @if ($errors->any())
                    <div class="mb-6 p-3 bg-red-100 border border-[#E13B23] text-[#E13B23] rounded-lg text-sm font-medium shadow-sm">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('status'))
                    <div class="mb-6 p-3 bg-green-100 border border-green-600 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- FORM LOGIN -->
                <form method="POST" action="{{ route('login') }}" class="space-y-8">
                    @csrf

                    <!-- Input Username -->
                    <div class="group">
                        <label for="username" class="block text-sm font-bold italic text-gray-900 mb-1 ml-1">User Nama</label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                               class="w-full bg-transparent border-0 border-b-[2.5px] border-gray-800 text-gray-900 text-base py-2 px-1 focus:ring-0 focus:border-[#E13B23] transition-colors placeholder-gray-600/50 font-medium"
                               placeholder="">
                    </div>

                    <!-- Input Password dengan Mata (Alpine.js) -->
                    <div class="group" x-data="{ show: false }">
                        <label for="password" class="block text-sm font-bold italic text-gray-900 mb-1 ml-1">Password</label>
                        
                        <div class="relative">
                            <!-- Input: type berubah dinamis antara 'text' dan 'password' -->
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                   class="w-full bg-transparent border-0 border-b-[2.5px] border-gray-800 text-gray-900 text-base py-2 px-1 pr-10 focus:ring-0 focus:border-[#E13B23] transition-colors placeholder-gray-600/50 font-medium"
                                   placeholder="">
                            
                            <!-- Tombol Toggle Mata -->
                            <button type="button" @click="show = !show" 
                                    class="absolute right-0 top-1/2 transform -translate-y-1/2 text-gray-800 hover:text-[#E13B23] focus:outline-none p-1 transition-colors">
                                
                                <!-- Icon Mata Terbuka (Show Password) -->
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                
                                <!-- Icon Mata Tertutup (Hide Password) -->
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.592m1.828-1.828A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-3.264 5.406M7 7l3 3m2 6l1.5 1.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('password.request') }}" class="text-sm font-bold italic text-gray-900 hover:text-[#E13B23] transition-colors">
                            Forget password?
                        </a>

                        <button type="submit" 
                                class="bg-[#1a1a1a] text-white px-10 py-3 rounded-xl font-bold text-lg shadow-lg hover:bg-black hover:shadow-xl hover:-translate-y-1 transition-all duration-200">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection