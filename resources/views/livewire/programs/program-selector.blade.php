<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="space-y-1">
        <label class="text-sm font-medium text-gray-700">College</label>

        <div class="relative">
            <select
                wire:model.live="collegeId"
                class="w-full border rounded px-3 py-2 pr-8">
                <option value="">Select College</option>
                @foreach ($colleges as $college)
                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                @endforeach
            </select>

            {{-- Loading indicator --}}
            <div wire:loading wire:target="collegeId"
                    class="absolute right-2 top-2.5 text-gray-400">
                <i class='bx bx-loader bx-spin'></i>
            </div>
        </div>
    </div>

    <div class="space-y-1">
        <label class="text-sm font-medium text-gray-700">Department</label>

        <div class="relative">
            <select
                wire:model.live="departmentId"
                class="w-full border rounded px-3 py-2 pr-8"
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
                    class="absolute right-2 top-2.5 text-gray-400">
                <i class='bx bx-loader bx-spin'></i>
            </div>
        </div>
    </div>


    <div class="space-y-1">
        <label class="text-sm font-medium text-gray-700">Program</label>

        <div class="relative">
            <select
                wire:model.live="programId"
                class="w-full border rounded px-3 py-2 pr-8"
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
                 class="absolute right-2 top-2.5 text-gray-400">
                <i class='bx bx-loader bx-spin'></i>
            </div>
        </div>
    </div>

</div>
