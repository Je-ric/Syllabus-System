<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-slate-900">Academic Calendar</h3>
        <p class="text-sm text-slate-600">Choose the academic year and semester for this syllabus.</p>
    </div>

    <div class="max-w-md bg-white border border-slate-200 rounded-xl p-4">
        <x-form.label class="block mb-2">
            Academic Calendar <span class="text-red-600">*</span>
        </x-form.label>
        <x-form.select wire:model.blur="academic_calendar_id"
            class="@error('academic_calendar_id') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
            required>
            <option value="">-- Choose Academic Calendar --</option>
            @foreach ($academicCalendars as $calendar)
                <option value="{{ $calendar->id }}">
                    {{ $calendar->academic_year }} - {{ $calendar->getFormattedSemester() }}
                </option>
            @endforeach
        </x-form.select>
        @error('academic_calendar_id')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>
