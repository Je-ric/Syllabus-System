<div>
    <x-wizard.step-header
        title="Academic Calendar"
        description="Choose the academic year and semester for this syllabus. This determines the weeks and locked dates." />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Selector card --}}
        <x-wizard.section title="Select Academic Calendar" icon="calendar" color="emerald">
            <x-form.label isRequired class="block mb-2">Academic Calendar</x-form.label>
            <x-form.select
                wire:model="academic_calendar_id"
                class="@error('academic_calendar_id') border-rose-400 @enderror"
                required>
                <option value="">&mdash; Choose Academic Calendar &mdash;</option>
                @foreach ($academicCalendars as $calendar)
                    <option value="{{ $calendar['id'] }}">
                        {{ $calendar['academic_year'] }} &ndash; {{ $calendar['formatted_semester'] }}
                    </option>
                @endforeach
            </x-form.select>

            @error('academic_calendar_id')
                <p class="mt-1.5 text-sm text-rose-600 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror
        </x-wizard.section>

        {{-- Info card --}}
        <x-wizard.info-card title="Why this matters" icon="info-circle" color="emerald">
            <ul class="space-y-2">
                <li class="flex items-start gap-2.5 text-sm text-slate-600">
                    <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#16a34a] shrink-0"></span>
                    Weeks are auto-generated from the selected calendar's start and end dates.
                </li>
                <li class="flex items-start gap-2.5 text-sm text-slate-600">
                    <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#16a34a] shrink-0"></span>
                    Exam and non-teaching events will automatically lock the corresponding weeks.
                </li>
                <li class="mt-1">
                    <x-feedback-status.alert type="warning" :showTitle="false">
                        Changing the calendar later will require regenerating weeks (content may be lost).
                    </x-feedback-status.alert>
                </li>
            </ul>
        </x-wizard.info-card>

    </div>
</div>
