<button class="text-sm bg-black text-white px-3 py-1 rounded"
        onclick="toggle('addDept{{ $college->id }}')">
    <i class="bx bx-plus"></i> Add Department
</button>

<div id="addDept{{ $college->id }}"
    class="hidden mt-2 bg-gray-100 p-3 rounded">

    <form method="POST"
        action="{{ route('department.store') }}">
        @csrf

        <input type="hidden"
                name="college_id"
                value="{{ $college->id }}">
        <input type="text"
                name="name"
                placeholder="Department Name"
                class="border p-2 rounded w-1/2" required>
        <button class="bg-green-600 text-white px-3 py-1 rounded ml-2">Save</button>
        <button type="button"
                onclick="toggle('addDept{{ $college->id }}')"
                class="bg-gray-500 text-white px-3 py-1 rounded">
                Cancel
        </button>
    </form>

</div>
