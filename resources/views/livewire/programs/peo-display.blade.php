<div class="mb-4">
    <h3 class="text-sm font-medium text-gray-700 mb-2">
        Program Educational Objectives (PEOs)
    </h3>

    <div class="space-y-1">
        @forelse ($peos as $peo)
            <div class="text-sm text-gray-800 flex gap-2">
                <span class="font-semibold whitespace-nowrap">
                    {{ $peo['peo_code'] }}:
                </span>
                <span>
                    {{ $peo['peo_text'] }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400 italic">No PEOs yet.</p>
        @endforelse
    </div>
</div>
