@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak
         class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak
         class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
        {{ session('error') }}
    </div>
@endif

@if (session('info'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak
         class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg" role="alert">
        {{ session('info') }}
    </div>
@endif
