{{-- Shared: Card 6 — Courses Without IED Mapping (col 2, rows 3-5)
     Requires: $data['courses_without_ied'], $iedEmptyMessage (string) --}}
<div class="row-span-3 row-start-3 col-start-2">
    <div class="h-full border border-slate-200 rounded-lg bg-white overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center gap-2 shrink-0">
            <i class="bx bx-error-circle text-slate-600"></i>
            <h3 class="font-semibold text-slate-700">Courses Without IED Mapping</h3>
        </div>
        <div class="p-4 flex-1 overflow-y-auto">
            @if (empty($data['courses_without_ied']))
                <x-feedback-status.empty-state icon="bx-check-circle" title="All courses mapped"
                    :message="$iedEmptyMessage" />
            @else
                <div class="space-y-2">
                    @foreach ($data['courses_without_ied'] as $course)
                        <div class="flex items-center justify-between gap-2 p-3 rounded-lg bg-[#FEF2F2] border border-[#FECACA]">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#991B1B] truncate">
                                    {{ $course['course_code'] }} — {{ $course['title'] }}
                                </p>
                                <p class="text-xs text-[#7F1D1D] mt-0.5">{{ $course['program'] ?? 'Unknown program' }}</p>
                            </div>
                            <x-ui.button href="{{ route('courses.edit', ['course' => $course['id']]) }}" variant="table-danger" size="sm">
                                <i class="bx bx-edit text-xs leading-none"></i> Fix
                            </x-ui.button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
