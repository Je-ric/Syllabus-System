<x-button variant="add-button"
        onclick="toggle('addCollegeForm')">
    <i class="bx bx-plus"></i> Add College
</x-button>

<div id="addCollegeForm"
    class="hidden mb-3 bg-green-50 border border-green-200 p-4 rounded-lg">

    <form method="POST" action="{{ route('college.store') }}">
        @csrf
        <input type="text"
                name="name"
                placeholder="College Name"
                class="border p-2 rounded w-1/3 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                required>

        <div class="mt-2 space-x-2">
            <x-button variant="save">Save</x-button>
            <x-button type="button"
                    onclick="toggle('addCollegeForm')"
                    variant="cancel">
                    Cancel
            </x-button>
        </div>
    </form>

</div>
