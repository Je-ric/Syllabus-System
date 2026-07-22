{{--
    peo-display.blade.php  (read-only)
    Shown at the top of the PO tab as a reference panel.
    determine if being used, delete if not.
--}}
<div>
    <div class="flex items-center gap-2 mb-3">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md
                    bg-emerald-100 text-emerald-600">
            <i class="bx bx-graduation text-sm leading-none"></i>
        </span>
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
            PEOs for Reference
            <span class="ml-1 font-normal normal-case tracking-normal text-slate-400">
                — map your POs to these objectives below
            </span>
        </p>
    </div>

    @if (count($peos) > 0)
        <div class="grid gap-2">
            @foreach ($peos as $index => $peo)
                <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3
                            hover:border-emerald-200 hover:bg-emerald-50/30 transition-colors">
                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700
                                text-xs font-bold ring-1 ring-emerald-200">
                        {{ strtoupper($peo['peo_code']) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-0.5">
                            PEO {{ $index + 1 }}
                        </p>
                        <p class="text-sm text-slate-700 leading-relaxed">
                            {{ $peo['peo_text'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border-2 border-dashed border-slate-200 bg-white py-8 text-center">
            <i class="bx bx-error-circle text-3xl text-slate-300"></i>
            <p class="mt-2 text-sm font-semibold text-slate-500">No PEOs defined yet</p>
            <p class="text-xs text-slate-400 mt-0.5">
                Go to the <strong class="text-emerald-600">PEOs tab</strong> and add them first before mapping POs.
            </p>
        </div>
    @endif
</div>
