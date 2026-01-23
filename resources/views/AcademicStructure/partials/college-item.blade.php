{{-- College --}}
<details class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
    <summary class="flex items-center justify-between p-4 cursor-pointer select-none">
        <div class="flex items-center gap-3 flex-1">
            <i class="bx bx-chevron-down text-xl text-gray-500 chevron-icon"></i>
            <i class="bx bxs-school text-2xl text-green-500"></i>
            <span class="font-semibold text-lg text-gray-800">{{ $college->name }}</span>
        </div>
        <button type="button"
                onclick="event.stopPropagation(); toggle('editCollege{{ $college->id }}')"
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="bx bx-edit text-xl text-gray-600 hover:text-green-500"></i>
        </button>
    </summary>

    <div class="px-4 pb-4">
        {{-- EDIT COLLEGE --}}
        <div id="editCollege{{ $college->id }}"
            class="hidden mb-4 bg-green-50 border border-green-200 p-4 rounded-lg">

            <form method="POST"
                action="{{ route('college.update', $college) }}">
                @csrf
                @method('PUT')
                <div class="space-y-3">
                    <input type="text"
                            name="name"
                            value="{{ $college->name }}"
                            placeholder="College Name"
                            class="border border-gray-300 p-2 rounded-lg w-full focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">Save</button>
                        <button type="button"
                                onclick="toggle('editCollege{{ $college->id }}')"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                Cancel
                        </button>
                    </div>
                </div>
            </form>

        </div>

        {{-- DEPARTMENTS --}}
        @include('AcademicStructure.partials.department-item', [
            'college' => $college,
            'departments' => $departments
        ])
    </div>
</details>
