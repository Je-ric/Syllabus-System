<div class="mb-4">
    <h3 class="text-xs uppercase tracking-[0.25em] text-slate-500 mb-3">
        Program Educational Objectives (PEOs)
    </h3>

    <div class="space-y-2">
        @forelse ($peos as $peo)
            <div class="text-sm text-slate-700 flex gap-2 rounded-lg border border-slate-200 bg-white/80 px-3 py-2 shadow-sm">
                <span class="font-semibold text-emerald-700 whitespace-nowrap">
                    {{-- {{ $peo['peo_code'] }}: --}}
                    PEO{{ $loop->iteration }}
                </span>
                <span>
                    {{ $peo['peo_text'] }}
                </span>
            </div>
        @empty
            <p class="text-sm text-slate-400 italic">No PEOs yet.</p>
        @endforelse
    </div>
</div>
