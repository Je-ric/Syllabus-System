<div class="ml-6 mt-4">

    {{-- ADD DEPARTMENT --}}
    @include('AcademicStructure.partials.department-form', ['college' => $college])

    @foreach ($departments->where('college_id', $college->id) as $dept)
        <details class="border-l pl-4 mt-3">
            <summary class="flex justify-between items-center cursor-pointer">
                <span class="flex items-center gap-2">
                    <i class="bx bx-building"></i> {{ $dept->name }}
                </span>
                <button onclick="toggle('editDept{{ $dept->id }}')">
                    <i class="bx bx-edit"></i>
                </button>
            </summary>

            {{-- EDIT DEPARTMENT --}}
            <div id="editDept{{ $dept->id }}"
                class="hidden mt-2 bg-gray-100 p-3 rounded">

                <form method="POST" action="{{ route('department.update', $dept) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden"
                            name="college_id"
                            value="{{ $college->id }}">
                    <input type="text"
                            name="name"
                            value="{{ $dept->name }}"
                            class="border p-2 rounded w-1/2"
                            required>
                    <div class="flex gap-2 mt-2">
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
                        <button type="button"
                                onclick="toggle('editDept{{ $dept->id }}')"
                                class="bg-gray-500 text-white px-3 py-1 rounded">
                            Cancel
                        </button>
                    </div>
                </form>
                
            </div>

            {{-- PROGRAMS --}}
            @include('AcademicStructure.partials.program-item', ['dept' => $dept])
        </details>
    @endforeach
</div>
