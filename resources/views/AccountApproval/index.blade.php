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

        <div class="mb-4">
            <p class="text-lg font-semibold text-slate-800">Accounts</p>
            <p class="text-sm text-slate-500">
                Manage user access, statuses, and role assignments
            </p>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full border-collapse">
                <thead class="sticky top-0 z-10 bg-slate-50">
                    <tr class="text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">
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
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-4 py-3 border border-slate-200 align-middle">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 border border-slate-200 font-medium text-slate-800">{{ $user->name }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-slate-600 text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-3 border border-slate-200 align-middle">
                                <x-feedback-status.status-indicator status="{{ $user->account_status }}" />
                            </td>

                            <td class="border px-3 py-2">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        <x-feedback-status.status-indicator
                                            :status="$role->name"
                                            :label="ucfirst($role->name)"
                                        />
                                    @empty
                                        <x-feedback-status.status-indicator
                                            status="neutral"
                                            label="No Role Assigned"
                                        />
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3 border-b border-slate-200 align-middle space-x-2">
                                @if ($user->account_status === 'pending')
                                    <x-button variant='active'
                                            @click="openModal=true;
                                            action='approve';
                                            userId={{ $user->id }}">
                                            Active
                                    </x-button>

                                    <x-button variant='reject'
                                            @click="openModal=true;
                                            action='reject';
                                            userId={{ $user->id }}">
                                            Reject
                                    </x-button>
                                @elseif ($user->account_status === 'disabled')
                                    <x-button variant='active'
                                            @click="openModal=true;
                                            action='approve';
                                            userId={{ $user->id }}">
                                            Active
                                    </x-button>
                                @elseif ($user->account_status === 'rejected')
                                    <x-button variant='restore'
                                            @click="openModal=true;
                                            action='restore';
                                            userId={{ $user->id }}">
                                            Restore
                                    </x-button>
                                @elseif ($user->account_status === 'active')
                                    <x-button variant='disable'
                                            @click="openModal=true;
                                            action='disable';
                                            userId={{ $user->id }}">
                                            Disable
                                    </x-button>
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
        </div>

        <x-modal id="approvalModal" title="Confirm Action" size="md" x-show="openModal">
            {{-- Alpine.js note:
                x-if only works on <template> elements (not normal HTML tags).
                This ensures the approval modal content is only rendered
                when the action is NOT "assignRole".
            --}}

            <template x-if="action !== 'assignRole'"> {{-- Needs to be true --}}
                @include('AccountApproval.modals.approvalModal')
            </template>

            {{-- Displayed when the selected action is "assignRole" --}}
            <template x-if="action === 'assignRole'">
                @include('AccountApproval.modals.assignRolesModal')
            </template>
        </x-modal>

    </div>
@endsection
