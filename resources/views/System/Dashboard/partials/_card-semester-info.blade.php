{{-- Shared: Card 1 — Semester Information (cols 1-2, rows 1-2) --}}
<div class="col-span-2 row-span-2">
    <div class="h-full border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bx bx-calendar-event text-slate-600"></i>
                <h3 class="font-semibold text-slate-700">Semester Information</h3>
            </div>
        </div>
        <div class="p-4 h-full">
            @if ($data['active_semester'])
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">Active Semester</h4>
                            <p class="text-base font-bold text-clsu-cobra mt-0.5">{{ $data['active_semester']['label'] }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 bg-emerald-50 ring-1 ring-inset ring-emerald-200 rounded-full px-3 py-1.5 shrink-0">
                            <i class="bx bx-calendar-check text-clsu-green text-sm"></i>
                            <span class="text-sm font-bold text-clsu-cobra">Week {{ $data['active_semester']['current_week'] ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-200">
                            <h4 class="text-[10px] font-semibold text-emerald-700 uppercase tracking-wide">Current Date</h4>
                            <p class="text-sm font-bold text-emerald-800 mt-0.5">{{ $data['active_semester']['current_date'] ?? '-' }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                            <h4 class="text-[10px] font-semibold text-purple-700 uppercase tracking-wide">Days Left</h4>
                            <p class="text-sm font-bold text-purple-800 mt-0.5">{{ $data['active_semester']['days_remaining'] ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
                            <h4 class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide">Start Date</h4>
                            <p class="text-sm font-bold text-amber-800 mt-0.5">{{ $data['active_semester']['start_date'] ?? '-' }}</p>
                        </div>
                        <div class="bg-rose-50 rounded-lg p-3 border border-rose-200">
                            <h4 class="text-[10px] font-semibold text-rose-700 uppercase tracking-wide">End Date</h4>
                            <p class="text-sm font-bold text-rose-800 mt-0.5">{{ $data['active_semester']['end_date'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bx bx-warning text-amber-600"></i>
                        <h4 class="font-semibold text-amber-800">No active semester</h4>
                    </div>
                    <p class="text-sm text-amber-700">No academic calendar is marked as active. Set one under Academic Calendars.</p>
                </div>
            @endif
        </div>
    </div>
</div>
