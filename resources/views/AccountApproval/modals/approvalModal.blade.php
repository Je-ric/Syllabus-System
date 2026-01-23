<template x-if="action === 'approve' ||
                action === 'reject' ||
                action === 'restore' ||
                action === 'disable'"
                >
    <div>
        <p class="text-gray-700 mb-4">
            Are you sure you want to
            <span class="font-semibold capitalize" x-text="action"></span>
            this account?
        </p>

        <form method="POST" :action="`/account-approval/${action}`">
            @csrf
            <input type="hidden" name="user_id" :value="userId">

            <div class="flex justify-end gap-2">
                <button type="button" @click="openModal=false" class="border px-4 py-2 rounded">
                    Cancel
                </button>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</template>
