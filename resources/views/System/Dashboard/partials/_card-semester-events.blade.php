{{-- Shared: Card 4 — Semester Events (col 4, rows 1-5) --}}
<div class="row-span-5 col-start-4 row-start-1">
    <div class="h-full border border-slate-200 rounded-lg bg-white overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center gap-2 shrink-0">
            <i class="bx bx-calendar-event text-slate-600"></i>
            <h3 class="font-semibold text-slate-700">Semester Events</h3>
        </div>
        <div class="p-4 flex-1 overflow-y-auto">
            @if (empty($data['upcoming_events']))
                <x-feedback-status.empty-state icon="bx-calendar-event" title="No upcoming events"
                    message="No academic events in the next 30 days." />
            @else
                <div class="space-y-2">
                    @foreach ($data['upcoming_events'] as $event)
                        <div class="flex items-start justify-between gap-2 p-3 rounded-lg
                            {{ isset($event['is_past']) && $event['is_past'] ? 'bg-slate-100 border border-slate-200 opacity-60' : 'bg-slate-50 border border-slate-200' }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#394056] truncate">{{ $event['name'] }}</p>
                                <p class="text-xs text-[#72809E] mt-0.5">
                                    {{ $event['date'] }}
                                    @if (isset($event['days_until']) && !($event['is_past'] ?? false))
                                        <span class="ml-1 font-semibold {{ $event['days_until'] <= 3 ? 'text-red-600' : 'text-emerald-600' }}">
                                            · {{ $event['days_until'] == 0 ? 'Today' : ($event['days_until'] == 1 ? 'Tomorrow' : $event['days_until'] . 'd') }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full
                                {{ match($event['type'] ?? 'other') {
                                    'holiday'      => 'bg-blue-100 text-blue-700',
                                    'exam'         => 'bg-red-100 text-red-700',
                                    'break'        => 'bg-purple-100 text-purple-700',
                                    'non_teaching' => 'bg-orange-100 text-orange-700',
                                    default        => 'bg-gray-100 text-gray-700',
                                } }}">
                                {{ ucfirst(str_replace('_', ' ', $event['type'] ?? 'other')) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
