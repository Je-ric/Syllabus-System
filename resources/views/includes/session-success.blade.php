@if (session('success'))
    <div class="text-green-600 mb-3 text-sm">
        {{ session('success') }}
    </div>
@endif
