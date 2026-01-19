@extends('layouts.app')

@section('content')
<div x-data="{ openModal: false, action: '', userId: null, selectedRole: '' }">

    <p class="text-lg font-semibold mb-4">Accounts Lists</p>

    <table class="border-collapse w-full mb-6">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2">ID</th>
                <th class="border px-3 py-2">Name</th>
                <th class="border px-3 py-2">Email</th>
                <th class="border px-3 py-2">Account Status</th>
                {{-- <th class="border px-3 py-2">Email Verified At</th> --}}
                <th class="border px-3 py-2">Role</th>
                <th class="border px-3 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="border px-3 py-2">{{ $user->id }}</td>
                <td class="border px-3 py-2">{{ $user->name }}</td>
                <td class="border px-3 py-2">{{ $user->email }}</td>
                <td class="border px-3 py-2">{{ $user->account_status }}</td>
                {{-- <td class="border px-3 py-2">{{ $user->email_verified_at }}</td> --}}
                <td class="border px-3 py-2">
                    {{ $user->roles->first()?->name ?? 'No Role Assigned' }}
                </td>
                <td class="border px-3 py-2 space-x-2">
                    <button @click="openModal=true; action='approve'; userId={{ $user->id }}"
                        class="bg-green-600 text-white px-3 py-1 rounded">
                        Approve
                    </button>

                    <button @click="openModal=true; action='reject'; userId={{ $user->id }}"
                        class="bg-red-600 text-white px-3 py-1 rounded">
                        Reject
                    </button>

                    <button @click="openModal=true; action='restore'; userId={{ $user->id }}"
                        class="bg-blue-600 text-white px-3 py-1 rounded">
                        Restore
                    </button>

                    <button @click="openModal=true; action='assignRole'; userId={{ $user->id }}; selectedRole='{{ $user->roles->first()?->name ?? '' }}'"
                        class="bg-yellow-500 text-white px-3 py-1 rounded">
                        Assign Role
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal Component -->
    <x-modal id="approvalModal" title="Confirm Action" size="md" x-show="openModal">
        <!-- Approval / Reject / Restore -->
        <template x-if="action === 'approve' || action === 'reject' || action === 'restore'">
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
                        <button type="button" @click="openModal=false"
                            class="border px-4 py-2 rounded">Cancel</button>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </template>

        <!-- Role Assignment -->
        <!-- Role Assignment -->
<template x-if="action === 'assignRole'">
    <div>
        <p class="text-gray-700 mb-4">Assign roles to this user:</p>

        <form method="POST" action="{{ route('account-approval.assign-role') }}">
            @csrf
            <input type="hidden" name="user_id" :value="userId">

            <div class="flex flex-col space-y-2 mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="roles[]" value="admin" 
                        :checked="selectedRole.includes('admin')" x-model="selectedRole">
                    <span class="ml-2">Admin</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="roles[]" value="dean"
                        :checked="selectedRole.includes('dean')" x-model="selectedRole">
                    <span class="ml-2">Dean</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" disabled name="roles[]" value="faculty"
                        :checked="selectedRole.includes('faculty')" x-model="selectedRole">
                    <span class="ml-2">Faculty</span>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="openModal=false" class="border px-4 py-2 rounded">Cancel</button>

                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">
                    Assign Roles
                </button>
            </div>
        </form>
    </div>
</template>

    </x-modal>

</div>
@endsection
