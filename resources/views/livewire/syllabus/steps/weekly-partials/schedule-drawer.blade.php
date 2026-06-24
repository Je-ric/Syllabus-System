{{-- weekly-partials/schedule-drawer.blade.php
     Requires: $courseComponents array
     Alpine state in parent: scheduleOpen
--}}
<x-offcanvas title="Class Schedule" subtitle="Lecture & Laboratory schedule" icon="bx-time" open="scheduleOpen">

    @if (!empty($courseComponents['LEC']) || !empty($courseComponents['LAB']))
        <div class="space-y-4">
            @if (!empty($courseComponents['LEC']))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 overflow-hidden">
                    <div class="px-4 py-2.5 bg-emerald-50 border-b border-emerald-100">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-emerald-700">Lecture · LEC</p>
                    </div>
                    <div class="px-4 py-3 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[12px] text-slate-500">Schedule</span>
                            <span class="text-[13px] font-medium text-slate-800">{{ $courseComponents['LEC']['schedule'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[12px] text-slate-500">Class Hours</span>
                            <span class="text-[13px] font-mono text-slate-700">{{ $courseComponents['LEC']['class_hours'] ?? '—' }} hrs/wk</span>
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($courseComponents['LAB']))
                <div class="rounded-xl border border-blue-200 bg-blue-50/40 overflow-hidden">
                    <div class="px-4 py-2.5 bg-blue-50 border-b border-blue-100">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-blue-700">Laboratory · LAB</p>
                    </div>
                    <div class="px-4 py-3 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[12px] text-slate-500">Schedule</span>
                            <span class="text-[13px] font-medium text-slate-800">{{ $courseComponents['LAB']['schedule'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[12px] text-slate-500">Class Hours</span>
                            <span class="text-[13px] font-mono text-slate-700">{{ $courseComponents['LAB']['class_hours'] ?? '—' }} hrs/wk</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <x-empty-state icon="bx-time" title="No schedule" message="Complete Course Components first to see schedule info here." />
    @endif

</x-offcanvas>
