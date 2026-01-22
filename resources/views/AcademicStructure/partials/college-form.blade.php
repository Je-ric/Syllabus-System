<button class="bg-black text-white px-4 py-2 rounded"
        onclick="toggle('addCollegeForm')">
    <i class="bx bx-plus"></i> Add College
</button>

<div id="addCollegeForm" class="hidden mt-4 bg-gray-100 p-4 rounded">
    
    <form method="POST" action="{{ route('college.store') }}">
        @csrf
        <input type="text"
                name="name"
                placeholder="College Name"
                class="border p-2 rounded w-1/3"
                required>

        <div class="mt-2 space-x-2">
            <button class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
            <button type="button"
                    onclick="toggle('addCollegeForm')"
                    class="bg-gray-500 text-white px-3 py-1 rounded">
                    Cancel
            </button>
        </div>
    </form>

</div>
