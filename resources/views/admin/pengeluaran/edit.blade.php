@extends('layouts.app')

@section('title', 'Edit Catatan Pengeluaran')

@section('content')
<div class="container p-4 mx-auto">
    <h1 class="mb-6 text-3xl font-bold">Edit Catatan Pengeluaran</h1>

    <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-md">
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <ul class="pl-5 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pengeluaran.update', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Pengeluaran</label>
                    <textarea name="description" id="description" rows="3" required
                              class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $expense->description) }}</textarea>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}" required min="1"
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700">Tanggal Pengeluaran</label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="flex justify-end mt-6 space-x-4">
                <a href="{{ route('admin.pengeluaran.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    Update Catatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
