<template x-if="action === 'assignRole'">
    <div>
        <p class="text-gray-700 mb-4 font-semibold">Assign roles to this user:</p>

        <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4">
            <p class="text-sm text-blue-800">
                <span class="font-semibold">Currently assigned roles:</span>
                <span x-text="selectedRole.length > 0 ? selectedRole.join(', ') : 'None'"></span>
            </p>
        </div>

        <form method="POST" action="{{ route('account-approval.assign-role') }}">
            @csrf
            <input type="hidden" name="user_id" :value="userId">

            <div class="flex flex-col space-y-2 mb-4">

                <label class="inline-flex items-center">
                    <input type="checkbox" name="roles[]" value="admin" x-model="selectedRole">
                    <span class="ml-2">Admin</span>
                    <span x-show="selectedRole.includes('admin')" class="ml-2 text-xs text-green-600 font-semibold">(Already assigned)</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="roles[]" value="dean" x-model="selectedRole">
                    <span class="ml-2">College Dean</span>
                    <span x-show="selectedRole.includes('dean')" class="ml-2 text-xs text-green-600 font-semibold">(Already assigned)</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="roles[]" value="chair" x-model="selectedRole">
                    <span class="ml-2">Department Chair</span>
                    <span x-show="selectedRole.includes('chair')" class="ml-2 text-xs text-green-600 font-semibold">(Already assigned)</span>
                </label>

                <!-- Faculty always assigned -->
                <label class="inline-flex items-center opacity-70">
                    <input type="checkbox" checked disabled>
                    <input type="hidden" name="roles[]" value="faculty">
                    <span class="ml-2">Faculty <span class="text-xs text-gray-500">(Always assigned)</span></span>
                </label>

            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="openModal=false" class="border px-4 py-2 rounded">
                    Cancel
                </button>

                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    Save Roles
                </button>
            </div>
        </form>
    </div>
</template>
