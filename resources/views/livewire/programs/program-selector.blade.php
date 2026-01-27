<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- College --}}
    <div>
        <label class="text-sm font-medium">College</label>
        <select wire:model.live="collegeId" class="w-full border rounded px-3 py-2">
            <option value="">Select College</option>
            @foreach ($colleges as $college)
                <option value="{{ $college->id }}">{{ $college->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Department --}}
    <div>
        <label class="text-sm font-medium">Department</label>
        <select wire:model.live="departmentId"
                class="w-full border rounded px-3 py-2"
                @disabled(!$collegeId)>
            <option value="">Select Department</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Program --}}
    <div>
        <label class="text-sm font-medium">Program</label>
        <select wire:model.live="programId"
                class="w-full border rounded px-3 py-2"
                @disabled(!$departmentId)>
            <option value="">Select Program</option>
            @foreach ($programs as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
            @endforeach
        </select>
    </div>

</div>
