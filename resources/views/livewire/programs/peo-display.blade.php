{{--
    peo-display.blade.php  (read-only)
    Shown at the top of the PO tab as a reference panel.
    Livewire: PeoDisplay — refreshes when peosUpdated event fires.

    peo_code from DB is a lowercase letter (a, b, c …) — displayed uppercased.
--}}
<div>
    <div class="flex items-center gap-2 mb-3">
        <i class="bx bx-graduation text-emerald-500 text-base"></i>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
            PEOs for Reference
            <span class="ml-1 font-normal normal-case tracking-normal text-slate-400">
                — map your POs to these objectives below
            </span>
        </p>
    </div>

    @if (count($peos) > 0)
        <div class="grid gap-2">
            @foreach ($peos as $index => $peo)
                <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                    {{-- Letter badge — uppercase of peo_code stored in DB (a → A) --}}
                    <span class="mt-0.5 shrink-0 inline-flex items-center justify-center
                                 w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700
                                 text-xs font-bold uppercase ring-1 ring-emerald-200">
                        {{ strtoupper($peo['peo_code']) }}
                    </span>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $peo['peo_text'] }}
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-white py-6 text-center">
            <i class="bx bx-error-circle text-2xl text-slate-300"></i>
            <p class="mt-1.5 text-sm text-slate-400">
                No PEOs defined yet.
                <strong class="font-semibold">Go to the PEOs tab</strong> and add them first before mapping POs.
            </p>
        </div>
    @endif
</div>
