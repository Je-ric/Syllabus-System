@props([
    'title',
    'description' => null,
])

<div class="mb-5 flex items-start justify-between gap-4">
    <div class="min-w-0">
        <h3 class="text-xl font-semibold text-slate-900">{{ $title }}</h3>
        @if ($description)
            <p class="mt-0.5 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="shrink-0 flex items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>

