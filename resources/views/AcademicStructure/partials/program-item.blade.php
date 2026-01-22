<div class="ml-6 mt-3">

    {{-- ADD PROGRAM --}}
    @include('AcademicStructure.partials.program-form', ['dept' => $dept])

    <ul class="space-y-2 mt-2">
        @foreach ($dept->programs as $program)
            <li class="flex justify-between items-start bg-gray-50 p-3 rounded">
                <div class="flex gap-2">
                    <i class="bx bx-book-open mt-1"></i>
                    <div>
                        <strong>{{ $program->name }}</strong>
                        <div class="text-sm text-gray-600">
                            BOR No: {{ $program->bor_approval_no }}<br>
                            {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('F d, Y') }}
                        </div>
                    </div>
                </div>

                <button onclick="toggle('editProgram{{ $program->id }}')">
                    <i class="bx bx-edit"></i>
                </button>
            </li>

            {{-- EDIT PROGRAM --}}
            <div id="editProgram{{ $program->id }}"
                class="hidden bg-gray-100 p-3 rounded mt-1">

                <form method="POST"
                    action="{{ route('program.update', $program) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden"
                            name="department_id"
                            value="{{ $dept->id }}">
                    <input name="name"
                            value="{{ $program->name }}"
                            class="border p-2 rounded w-full mb-1"
                            required>
                    <input name="bor_approval_no"
                            value="{{ $program->bor_approval_no }}"
                            class="border p-2 rounded w-full mb-1"
                            required>
                    <input type="date"
                            name="bor_approval_date"
                            value="{{ $program->bor_approval_date }}"
                            class="border p-2 rounded w-full"
                            required>
                    <div class="flex gap-2 mt-2">
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
                        <button type="button"
                                onclick="toggle('editProgram{{ $program->id }}')"
                                class="bg-gray-500 text-white px-3 py-1 rounded">
                            Cancel
                        </button>
                    </div>
                </form>

            </div>
        @endforeach
    </ul>
</div>
