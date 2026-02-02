<div class="ml-8 space-y-2">

    {{-- ADD PROGRAM --}}
    <x-button variant="add-button" onclick="openAddProgramModal({{ $dept->id }})">
        <i class="bx bx-plus"></i> Add Program
    </x-button>


    <div class="space-y-2">
        @foreach ($dept->programs as $program)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex items-start justify-between p-3">
                    <div class="flex gap-3 flex-1">
                        <i class="bx bx-book-open text-lg text-blue-500 mt-1"></i>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $program->name }}</div>
                            <div class="text-sm text-gray-600 mt-1">
                                <span class="inline-block mr-3"><b>BOR No: </b>{{ $program->bor_approval_no }}</span><br>
                                <span class="inline-block"><b>BOR Date Approval: </b> {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('F d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            onclick="toggle('editProgram{{ $program->id }}')"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                        <i class="bx bx-edit text-lg text-gray-600 hover:text-blue-500"></i>
                    </button>
                </div>

                {{-- EDIT PROGRAM --}}
                <div id="editProgram{{ $program->id }}"
                    class="hidden border-t border-gray-200 bg-blue-50 p-4">

                    <form method="POST"
                        action="{{ route('program.update', $program) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden"
                                name="department_id"
                                value="{{ $dept->id }}">
                        <div class="space-y-3">
                            <input name="name"
                                    value="{{ $program->name }}"
                                    placeholder="Program Name"
                                    class="border border-gray-300 p-2 rounded-lg w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                            <input name="bor_approval_no"
                                    value="{{ $program->bor_approval_no }}"
                                    placeholder="BOR Approval No"
                                    class="border border-gray-300 p-2 rounded-lg w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                            <input type="date"
                                    name="bor_approval_date"
                                    value="{{ $program->bor_approval_date }}"
                                    class="border border-gray-300 p-2 rounded-lg w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">Save</button>
                                <button type="button"
                                        onclick="toggle('editProgram{{ $program->id }}')"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        @endforeach
    </div>
</div>
