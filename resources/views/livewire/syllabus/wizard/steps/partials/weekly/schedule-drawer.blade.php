{{-- weekly-partials/schedule-drawer.blade.php --}}
<x-layout.offcanvas title="Class Schedule" subtitle="Lecture & Laboratory schedule" icon="bx-time" open="scheduleOpen">

    @php
        $lec = $courseComponents['LEC'] ?? null;
        $lab = $courseComponents['LAB'] ?? null;
    @endphp

    @if ($lec || $lab)
        <div class="space-y-4">

            @if ($lec)
                <div class="rounded-[14px] border border-[#d1fae5] bg-white overflow-hidden">
                    <div class="px-4 py-2.5 bg-[#f0fdf4] border-b border-[#d1fae5] flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#16a34a] shrink-0"></span>
                        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#166534]">Lecture · LEC</p>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        <div class="flex items-center justify-between text-[12px] text-[#71717a] mb-1">
                            <span>Class Hours</span>
                            <span class="font-mono text-[#18181b] font-semibold">{{ $lec['class_hours'] ?? '—' }} hrs/wk</span>
                        </div>
                        @forelse ($lec['schedules'] ?? [] as $s)
                            <div class="flex items-center justify-between py-1.5 border-b border-[#f4f4f5] last:border-0">
                                <span class="text-[12px] font-medium text-[#52525b]">{{ $s['day'] }}</span>
                                <span class="text-[13px] font-semibold text-[#09090b]">{{ $s['time'] }}</span>
                            </div>
                        @empty
                            <p class="text-[12px] italic text-[#a1a1aa]">No schedule set.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if ($lab)
                <div class="rounded-[14px] border border-[#bfdbfe] bg-white overflow-hidden">
                    <div class="px-4 py-2.5 bg-[#eff6ff] border-b border-[#bfdbfe] flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#2563eb] shrink-0"></span>
                        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#1e40af]">Laboratory · LAB</p>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        <div class="flex items-center justify-between text-[12px] text-[#71717a] mb-1">
                            <span>Class Hours</span>
                            <span class="font-mono text-[#18181b] font-semibold">{{ $lab['class_hours'] ?? '—' }} hrs/wk</span>
                        </div>
                        @forelse ($lab['schedules'] ?? [] as $s)
                            <div class="flex items-center justify-between py-1.5 border-b border-[#f4f4f5] last:border-0">
                                <span class="text-[12px] font-medium text-[#52525b]">{{ $s['day'] }}</span>
                                <span class="text-[13px] font-semibold text-[#09090b]">{{ $s['time'] }}</span>
                            </div>
                        @empty
                            <p class="text-[12px] italic text-[#a1a1aa]">No schedule set.</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    @else
        <x-feedback-status.empty-state icon="bx-time" title="No schedule" message="Complete Course Components first to see schedule info here." />
    @endif

</x-layout.offcanvas>
