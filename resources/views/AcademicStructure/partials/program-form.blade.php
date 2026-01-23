<x-button variant="add-button"
        onclick="toggle('addProgram{{ $dept->id }}')">
    <i class="bx bx-plus"></i> Add Program
</x-button>

<div id="addProgram{{ $dept->id }}"
    class="hidden mt-2 bg-gray-100 p-3 rounded">

    <form method="POST" action="{{ route('program.store') }}">
        @csrf
        <input type="hidden"
                name="department_id"
                value="{{ $dept->id }}">
        <input name="name"
                placeholder="Program Name"
                class="border p-2 rounded w-full mb-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <input name="bor_approval_no"
                placeholder="BOR Approval No"
                class="border p-2 rounded w-full mb-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <input type="date"
                name="bor_approval_date"
                class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button class="bg-green-600 text-white px-3 py-1 rounded mt-2">Save</button>
        <button type="button"
                    onclick="toggle('addProgram{{ $dept->id }}')"
                    class="bg-gray-500 text-white px-3 py-1 rounded">
                    Cancel
            </button>
    </form>

</div>
