@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-900">Lupa Password</h2>
        <p class="text-center text-gray-600">Masukkan username Anda. Jika ada email yang terdaftar, kami akan mengirimkan link reset password.</p>

        @if (session('status'))
            <div class="p-3 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <ul class="pl-5 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.username') }}" class="space-y-6">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <button type="submit"
                        class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Kirim Link Reset
                </button>
            </div>
            <div class="text-sm text-center">
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
