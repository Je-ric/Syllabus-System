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
        <div class="rounded-xl border border-[#e2e8f0] bg-gradient-to-br from-[#f0fdf4] to-white p-5"
             style="box-shadow: 0 2px 12px rgba(0,0,0,.06);">
            <div class="flex items-center gap-2 mb-3">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-[#dcfce7] text-[#16a34a]">
                    <i class="bx bx-info-circle text-base leading-none"></i>
                </span>
                <p class="text-sm font-bold text-[#166534]">Why this matters</p>
            </div>
            <ul class="space-y-2">
                <li class="flex items-start gap-2.5 text-sm text-slate-600">
                    <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#16a34a] shrink-0"></span>
                    Weeks are auto-generated from the selected calendar's start and end dates.
                </li>
                <li class="flex items-start gap-2.5 text-sm text-slate-600">
                    <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#16a34a] shrink-0"></span>
                    Exam and non-teaching events will automatically lock the corresponding weeks.
                </li>
                <li class="flex items-start gap-2.5 text-sm text-amber-700 bg-amber-50 rounded-lg px-3 py-2 border border-amber-200">
                    <i class="bx bx-error-circle text-amber-500 shrink-0 mt-0.5"></i>
                    <span>Changing the calendar later will require regenerating weeks (content may be lost).</span>
                </li>
            </ul>
        </div>

    </div>
</div>
