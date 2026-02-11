<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="space-y-1">
        <x-form.label>College</x-form.label>

        <div class="relative">
            <select
                wire:model.live="collegeId"
                class="w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 pr-10 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                <option value="">Select College</option>
                @foreach ($colleges as $college)
                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                @endforeach
            </select>

            {{-- Loading indicator --}}
            <div wire:loading wire:target="collegeId"
                    class="absolute right-2 top-2 flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 shadow-sm">
                <i class='bx bx-loader-alt bx-spin'></i>
                Loading
            </div>
        </div>
    </div>

    <div class="space-y-1">
        <x-form.label>Department</x-form.label>

        <div class="relative">
            <select
                wire:model.live="departmentId"
                class="w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 pr-10 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition disabled:bg-slate-100"
                @disabled(!$collegeId)
            >
                <option value="">
                    {{ !$collegeId ? 'Select college first' : 'Select Department' }}
                </option>

                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>

            <div wire:loading wire:target="collegeId,departmentId"
                    class="absolute right-2 top-2 flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 shadow-sm">
                <i class='bx bx-loader-alt bx-spin'></i>
                Loading
            </div>
        </div>
    </div>


    <div class="space-y-1">
        <x-form.label>Program</x-form.label>
        <div class="relative">
            <select
                wire:model.live="programId"
                class="w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 pr-10 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition disabled:bg-slate-100"
                @disabled(!$departmentId)
            >
                <option value="">
                    {{ !$departmentId ? 'Select department first' : 'Select Program' }}
                </option>

                @foreach ($programs as $program)
                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                @endforeach
            </select>

            {{-- Loading --}}
            <div wire:loading wire:target="departmentId,programId"
                 class="absolute right-2 top-2 flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 shadow-sm">
                <i class='bx bx-loader-alt bx-spin'></i>
                Loading
            </div>
        </div>
    </div>

</div>
