<div>
    <x-wizard.step-header
        title="Academic Calendar"
        description="Choose the academic year and semester for this syllabus." />

    <div class="max-w-sm">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <x-form.label isRequired class="block mb-2">Academic Calendar</x-form.label>

            <x-form.select
                wire:model="academic_calendar_id"
                wire:change="updatedAcademicCalendarId"
                class="@error('academic_calendar_id') border-red-400 focus:border-red-400 focus:ring-red-200 @enderror"
                required>
                <option value="">&mdash; Choose Academic Calendar &mdash;</option>
                @foreach ($academicCalendars as $calendar)
                    <option value="{{ $calendar['id'] }}">
                        {{ $calendar['academic_year'] }} &ndash; {{ $calendar['formatted_semester'] }}
                    </option>
                @endforeach
            </x-form.select>

            @error('academic_calendar_id')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</div>
