<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Academic Calendar</h3>
        <p class="text-sm text-slate-600">Choose the academic year and semester for this syllabus.</p>
    </div>

    <div class="max-w-md bg-white border border-slate-200 rounded-xl p-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Academic Calendar <span class="text-red-600">*</span>
        </label>
        <select wire:model.blur="academic_calendar_id"
                class="w-full border rounded-lg px-4 py-2 @error('academic_calendar_id') border-red-500 @enderror"
                required>
            <option value="">-- Choose Academic Calendar --</option>
            @foreach ($academicCalendars as $calendar)
                <option value="{{ $calendar->id }}">
                    {{ $calendar->academic_year }} - {{ $calendar->getFormattedSemester() }}
                </option>
            @endforeach
        </select>
        @error('academic_calendar_id')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>
