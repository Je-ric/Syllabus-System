{{-- weekly-partials/schedule-drawer.blade.php --}}
<x-offcanvas title="Class Schedule" subtitle="Lecture & Laboratory schedule" icon="bx-time" open="scheduleOpen">

    @php
        $lec = $courseComponents['LEC'] ?? null;
        $lab = $courseComponents['LAB'] ?? null;
    @endphp

    @if ($lec || $lab)
        <div class="space-y-4">

            @if ($lec)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 overflow-hidden">
                    <div class="px-4 py-2.5 bg-emerald-50 border-b border-emerald-100">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Lecture · LEC</p>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span>Class Hours</span>
                            <span class="font-mono text-slate-700">{{ $lec['class_hours'] ?? '—' }} hrs/wk</span>
                        </div>
                        @forelse ($lec['schedules'] ?? [] as $s)
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-600">{{ $s['day'] }}</span>
                                <span class="text-sm text-slate-800">{{ $s['time'] }}</span>
                            </div>
                        @empty
                            <p class="text-xs italic text-slate-400">No schedule set.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if ($lab)
                <div class="rounded-xl border border-blue-200 bg-blue-50/40 overflow-hidden">
                    <div class="px-4 py-2.5 bg-blue-50 border-b border-blue-100">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Laboratory · LAB</p>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span>Class Hours</span>
                            <span class="font-mono text-slate-700">{{ $lab['class_hours'] ?? '—' }} hrs/wk</span>
                        </div>
                        @forelse ($lab['schedules'] ?? [] as $s)
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-600">{{ $s['day'] }}</span>
                                <span class="text-sm text-slate-800">{{ $s['time'] }}</span>
                            </div>
                        @empty
                            <p class="text-xs italic text-slate-400">No schedule set.</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    @else
        <x-empty-state icon="bx-time" title="No schedule" message="Complete Course Components first to see schedule info here." />
    @endif

</x-offcanvas>
