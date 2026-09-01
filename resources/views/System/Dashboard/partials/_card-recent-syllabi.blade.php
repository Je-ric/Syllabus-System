{{-- Shared: Card 7 — Recent Syllabi Activity (col 3, rows 3-5)
     Requires: $data['recent_syllabi'], $syllabiEmptyMessage (string) --}}
<div class="row-span-3 row-start-3 col-start-3">
    <div class="h-full border border-slate-200 rounded-lg bg-white overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center gap-2 shrink-0">
            <i class="bx bx-time text-slate-600"></i>
            <h3 class="font-semibold text-slate-700">Recent Syllabi Activity</h3>
        </div>
        <div class="p-4 flex-1 overflow-y-auto">
            @if (empty($data['recent_syllabi']))
                <x-feedback-status.empty-state icon="bx-time" title="No recent activity"
                    :message="$syllabiEmptyMessage" />
            @else
                <div class="space-y-2">
                    @foreach ($data['recent_syllabi'] as $syllabus)
                        <div class="flex items-center justify-between gap-2 p-3 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#394056] truncate">
                                    {{ $syllabus['course_code'] ?? '—' }} — {{ $syllabus['title'] ?? 'Untitled' }}
                                </p>
                                <p class="text-xs text-[#72809E] mt-0.5">
                                    <span class="font-semibold">{{ $syllabus['status_label'] ?? 'Unknown' }}</span>
                                    @if (isset($syllabus['updated_at']))
                                        · {{ $syllabus['updated_at'] }}
                                    @endif
                                </p>
                            </div>
                            <x-feedback-status.status-indicator :status="$syllabus['status'] ?? 'neutral'" size="sm" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
