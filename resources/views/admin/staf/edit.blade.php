@extends('layouts.app')

@section('title', 'Edit Staf')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Edit Staf: {{ $staff->full_name }}</h1>

    <div class="p-6 bg-white rounded-lg shadow-md">
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <ul class="pl-5 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.staf.update', $staff->id) }}" method="POST" id="stafForm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $staff->full_name) }}" required
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $staff->username) }}" required
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email (Opsional, untuk Lupa Password)</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}"
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required {{ Auth::id() == $staff->id ? 'disabled' : '' }}
                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ Auth::id() == $staff->id ? 'bg-gray-100' : '' }}">
                        <option value="kasir" {{ old('role', $staff->role) == 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role', $staff->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @if(Auth::id() == $staff->id)
                    <p class="mt-1 text-xs text-gray-500">Anda tidak dapat mengubah role Anda sendiri.</p>
                    <input type="hidden" name="role" value="{{ $staff->role }}">
                    @endif
                </div>
                <div class="md:col-span-2">
                    <hr>
                    <p class="mt-4 text-sm text-gray-600">Kosongkan password jika tidak ingin menggantinya.</p>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p id="password_error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p id="password_confirmation_error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
            </div>
            
            <div class="flex justify-end mt-6 space-x-4">
                <a href="{{ route('admin.staf.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700" id="submitBtn">
                    Update Staf
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('password_error');
    const passwordConfirmationError = document.getElementById('password_confirmation_error');
    const submitBtn = document.getElementById('submitBtn');

    function validatePassword() {
        const passwordValue = password.value;
        let isValid = true;

        // Validasi panjang password (hanya jika password diisi)
        if (passwordValue.length > 0 && passwordValue.length < 8) {
            passwordError.textContent = 'password harus berisi 8 karakter';
            passwordError.classList.remove('hidden');
            password.classList.add('border-red-500');
            isValid = false;
        } else {
            passwordError.classList.add('hidden');
            password.classList.remove('border-red-500');
        }

        // Validasi konfirmasi password (hanya jika konfirmasi diisi)
        if (passwordConfirmation.value.length > 0 && passwordValue !== passwordConfirmation.value) {
            passwordConfirmationError.textContent = 'password tidak cocok';
            passwordConfirmationError.classList.remove('hidden');
            passwordConfirmation.classList.add('border-red-500');
            isValid = false;
        } else {
            passwordConfirmationError.classList.add('hidden');
            passwordConfirmation.classList.remove('border-red-500');
        }

        submitBtn.disabled = !isValid;
        if (!isValid) {
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'shadow-none');
            submitBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        } else {
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'shadow-none');
            submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        }
    }

    password.addEventListener('input', validatePassword);
    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endpush
@endsection

