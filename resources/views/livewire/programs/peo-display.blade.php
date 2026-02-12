<div class="mb-4">
    <h3 class="text-xs uppercase tracking-[0.25em] text-slate-500 mb-3">
        Program Educational Objectives (PEOs)
    </h3>

    <div class="space-y-2">
        @forelse ($peos as $peo)
            <x-text-block>
                <span class="font-semibold text-emerald-700">
                    {{-- {{ $peo['peo_code'] }}: --}}
                    {{ 'PEO' . $loop->iteration }}:
                </span>
                <span>
                    {{ $peo['peo_text'] }}
                </span>
            </x-text-block>
        @empty
            <p class="text-sm text-slate-400 italic">No PEOs yet.</p>
        @endforelse
    </div>
</div>
