@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}">Back to Account Approval</a>

    <div x-data="{
        openModal: false,
        action: '',
        userId: null,
        selectedRole: []
    }">

        <p class="text-lg font-semibold mb-4">Accounts Lists</p>

        <table class="border-collapse w-full mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-3 py-2">ID</th>
                    <th class="border px-3 py-2">Name</th>
                    <th class="border px-3 py-2">Email</th>
                    <th class="border px-3 py-2">Account Status</th>
                    <th class="border px-3 py-2">Role</th>
                    <th class="border px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="border px-3 py-2">{{ $user->id }}</td>
                        <td class="border px-3 py-2">{{ $user->name }}</td>
                        <td class="border px-3 py-2">{{ $user->email }}</td>
                        <td class="border px-3 py-2">{{ $user->account_status }}</td>
                        <td class="border px-3 py-2">
                            {{ $user->roles->pluck('name')->join(', ') ?: 'No Role Assigned' }}
                        </td>
                        <td class="border px-3 py-2 space-x-2">
                            @if ($user->account_status === 'pending')
                                <button @click="openModal=true; action='approve'; userId={{ $user->id }}"
                                    class="bg-green-600 text-white px-3 py-1 rounded">
                                    Active
                                </button>

                                <button @click="openModal=true; action='reject'; userId={{ $user->id }}"
                                    class="bg-red-600 text-white px-3 py-1 rounded">
                                    Reject
                                </button>
                            @elseif ($user->account_status === 'disabled')
                                <button @click="openModal=true; action='approve'; userId={{ $user->id }}"
                                    class="bg-green-600 text-white px-3 py-1 rounded">
                                    Active
                                </button>
                            @elseif ($user->account_status === 'rejected')
                                <button @click="openModal=true; action='restore'; userId={{ $user->id }}"
                                    class="bg-blue-600 text-white px-3 py-1 rounded">
                                    Restore
                                </button>
                            @elseif ($user->account_status === 'active')
                                <button @click="openModal=true; action='disable'; userId={{ $user->id }}"
                                    class="bg-blue-600 text-white px-3 py-1 rounded">
                                    Disable
                                </button>
                            @endif

                            <button
                                @click="
                                    openModal = true;
                                    action = 'assignRole';
                                    userId = {{ $user->id }};
                                    selectedRole = @js($user->roles->pluck('name'));
                        "
                                class="bg-yellow-500 text-white px-3 py-1 rounded">
                                Assign Role
                            </button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <x-modal id="approvalModal" title="Confirm Action" size="md" x-show="openModal">
            <!-- Approval / Status Actions -->
            <template x-if="action !== 'assignRole'">
                @include('AccountApproval.approvalModal')
            </template>

            <!-- Assign Role Modal -->
            <template x-if="action === 'assignRole'">
                @include('AccountApproval.assignRolesModal')
            </template>
        </x-modal>


    </div>
@endsection
