<div>
    <x-wizard.step-header
        title="Academic Calendar"
        icon="calendar"
        description="Choose the academic year and semester for this syllabus." />

    <x-wizard.section title="Select Academic Calendar" icon="calendar" color="slate" class="max-w-sm">
        <x-form.label isRequired class="block mb-2">
            <i class="bx bx-calendar"></i>Academic Calendar
        </x-form.label>
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
    </x-wizard.section>
</div>
