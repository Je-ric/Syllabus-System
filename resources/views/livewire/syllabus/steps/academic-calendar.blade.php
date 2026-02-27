<div>
    {{-- Header --}}
    <div class="mb-5">
        <h3 class="text-xl font-semibold text-slate-900">Academic Calendar</h3>
        <p class="text-sm text-slate-500 mt-0.5">Choose the academic year and semester for this syllabus.</p>
    </div>

    <div class="max-w-sm">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <x-form.label isRequired class="block mb-2">
                Academic Calendar
            </x-form.label>

            <x-form.select
                wire:model.defer="academic_calendar_id"
                class="@error('academic_calendar_id') border-red-400 focus:border-red-400 focus:ring-red-200 @enderror"
                required>
                <option value="">— Choose Academic Calendar —</option>
                @foreach ($academicCalendars as $calendar)
                    <option value="{{ $calendar->id }}">
                        {{ $calendar->academic_year }} – {{ $calendar->getFormattedSemester() }}
                    </option>
                @endforeach
            </x-form.select>

            @error('academic_calendar_id')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror

            {{-- <p class="mt-3 text-xs text-slate-400">
                <i class="bx bx-info-circle"></i>
                Each course can only have one syllabus per academic calendar.
            </p> --}}
        </div>
    </div>
</div>
