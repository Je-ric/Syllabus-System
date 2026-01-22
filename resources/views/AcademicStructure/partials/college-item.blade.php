{{-- Accordions --}}
<details class="border rounded p-4">
    <summary class="flex justify-between items-center cursor-pointer font-semibold text-lg">
        <span class="flex items-center gap-2">
            <i class="bx bxs-school"></i> {{ $college->name }}
        </span>
        <button onclick="toggle('editCollege{{ $college->id }}')">
            <i class="bx bx-edit"></i>
        </button>
    </summary>

    {{-- EDIT COLLEGE --}}
    <div id="editCollege{{ $college->id }}"
        class="hidden mt-3 bg-gray-100 p-3 rounded">

        <form method="POST"
            action="{{ route('college.update', $college) }}">
            @csrf
            @method('PUT')
            <input type="text"
                    name="name"
                    value="{{ $college->name }}"
                    class="border p-2 rounded w-1/3" required>
            <button class="bg-green-600 text-white px-3 py-1 rounded ml-2">Save</button>
        </form>

    </div>

    {{-- DEPARTMENTS --}}
    @include('AcademicStructure.partials.department-item', [
        'college' => $college,
        'departments' => $departments
    ])
</details>
