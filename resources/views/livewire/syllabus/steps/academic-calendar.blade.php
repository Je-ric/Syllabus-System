<div>
    <h3 class="text-xl font-semibold mb-4">Select Academic Year & Semester</h3>

    <div class="max-w-md">
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
