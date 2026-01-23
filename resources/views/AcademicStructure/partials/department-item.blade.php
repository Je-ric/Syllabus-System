<div class="ml-8 space-y-3">

    {{-- ADD DEPARTMENT --}}
    @include('AcademicStructure.partials.department-form', ['college' => $college])

    @foreach ($departments->where('college_id', $college->id) as $dept)
        <details class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
            <summary class="flex items-center justify-between p-3 cursor-pointer select-none">
                <div class="flex items-center gap-3 flex-1">
                    <i class="bx bx-chevron-down text-lg text-gray-500 chevron-icon"></i>
                    <i class="bx bx-building text-xl text-red-500"></i>
                    <span class="font-medium text-gray-800">{{ $dept->name }}</span>
                </div>
                <button type="button"
                        onclick="event.stopPropagation(); toggle('editDept{{ $dept->id }}')"
                        class="p-2 hover:bg-gray-200 rounded-lg transition-colors">
                    <i class="bx bx-edit text-lg text-gray-600 hover:text-red-500"></i>
                </button>
            </summary>

            <div class="px-3 pb-3">
                {{-- EDIT DEPARTMENT --}}
                <div id="editDept{{ $dept->id }}"
                    class="hidden mb-3 bg-red-50 border border-red-200 p-4 rounded-lg">

                    <form method="POST" action="{{ route('department.update', $dept) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden"
                                name="college_id"
                                value="{{ $college->id }}">
                        <div class="space-y-3">
                            <input type="text"
                                    name="name"
                                    value="{{ $dept->name }}"
                                    placeholder="Department Name"
                                    class="border border-gray-300 p-2 rounded-lg w-full focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">Save</button>
                                <button type="button"
                                        onclick="toggle('editDept{{ $dept->id }}')"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

                {{-- PROGRAMS --}}
                @include('AcademicStructure.partials.program-item', ['dept' => $dept])
            </div>
        </details>
    @endforeach
</div>
