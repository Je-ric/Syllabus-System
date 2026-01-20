@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
        ← Back
    </a>

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
                        <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                        <td class="border px-3 py-2">{{ $user->name }}</td>
                        <td class="border px-3 py-2">{{ $user->email }}</td>
                        <td class="border px-3 py-2">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'disabled' => 'bg-gray-200 text-gray-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];

                                $class = $statusColors[$user->account_status] ?? 'bg-slate-100 text-slate-800';
                            @endphp

                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $class }}">
                                {{ ucfirst($user->account_status) }}
                            </span>
                        </td>


                        <td class="border px-3 py-2">
                            {{ $user->roles->pluck('name')->join(', ') ?: 'No Role Assigned' }}
                        </td>
                        <td class="border px-3 py-2 space-x-2">
                            @if ($user->account_status === 'pending')
                                <button @click="openModal=true;
                                        action='approve';
                                        userId={{ $user->id }}"
                                        class="bg-green-600 text-white px-3 py-1 rounded">
                                        Active
                                </button>

                                <button @click="openModal=true;
                                        action='reject';
                                        userId={{ $user->id }}"
                                        class="bg-red-600 text-white px-3 py-1 rounded">
                                        Reject
                                </button>
                            @elseif ($user->account_status === 'disabled')
                                <button @click="openModal=true;
                                        action='approve';
                                        userId={{ $user->id }}"
                                        class="bg-green-600 text-white px-3 py-1 rounded">
                                        Active
                                </button>
                            @elseif ($user->account_status === 'rejected')
                                <button @click="openModal=true;
                                        action='restore';
                                        userId={{ $user->id }}"
                                        class="bg-blue-600 text-white px-3 py-1 rounded">
                                        Restore
                                </button>
                            @elseif ($user->account_status === 'active')
                                <button @click="openModal=true;
                                        action='disable';
                                        userId={{ $user->id }}"
                                        class="bg-blue-600 text-white px-3 py-1 rounded">
                                        Disable
                                </button>
                            @endif

                            @if ($user->account_status === 'active')
                                <button
                                    @click="
                                        openModal = true;
                                        action = 'assignRole';
                                        userId = {{ $user->id }};
                                        selectedRole = @js($user->roles->pluck('name'));"
                                        class="bg-yellow-500 text-white px-3 py-1 rounded">
                                        Assign Role
                                </button>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <x-modal id="approvalModal" title="Confirm Action" size="md" x-show="openModal">
            {{-- Alpine.js note:
                x-if only works on <template> elements (not normal HTML tags).
                This ensures the approval modal content is only rendered
                when the action is NOT "assignRole".
            --}}

            <template x-if="action !== 'assignRole'"> {{-- Needs to be true --}}
                @include('AccountApproval.approvalModal')
            </template>

            {{-- Displayed when the selected action is "assignRole" --}}
            <template x-if="action === 'assignRole'">
                @include('AccountApproval.assignRolesModal')
            </template>
        </x-modal>

    </div>
@endsection
