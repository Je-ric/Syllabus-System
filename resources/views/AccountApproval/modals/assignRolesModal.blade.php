<x-modal.dialog :id="$modalId" maxWidth="max-w-md" width="w-full">
    <x-modal.header>
        <span class="inline-flex items-center gap-2">
            <i class="bx bx-shield text-slate-600 text-lg leading-none"></i>
            Assign Roles
        </span>
        <x-modal.x-button :modalId="$modalId" />
    </x-modal.header>

    <form method="POST" action="{{ route('account-approval.assign-role') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.body>
            <div class="space-y-3">

                {{-- User info --}}
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 mb-1">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600 font-bold text-sm uppercase">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                    </div>
                </div>

                @php
                    $roles = [
                        ['name' => 'admin', 'label' => 'Admin',            'desc' => 'Full system access',              'icon' => 'bx-crown',    'color' => 'text-purple-600'],
                        ['name' => 'dean',  'label' => 'College Dean',     'desc' => 'Manage college-level operations', 'icon' => 'bx-medal',    'color' => 'text-indigo-600'],
                        ['name' => 'chair', 'label' => 'Department Chair', 'desc' => 'Manage department operations',    'icon' => 'bx-user-pin', 'color' => 'text-blue-600'],
                    ];
                @endphp

                @foreach ($roles as $role)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 cursor-pointer transition-colors">
                        <input type="checkbox" name="roles[]" value="{{ $role['name'] }}"
                            class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-400"
                            {{ $user->roles->contains('name', $role['name']) ? 'checked' : '' }}>
                        <i class="bx {{ $role['icon'] }} {{ $role['color'] }} text-lg leading-none shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $role['label'] }}</p>
                            <p class="text-xs text-slate-500">{{ $role['desc'] }}</p>
                        </div>
                    </label>
                @endforeach

                {{-- Faculty — always assigned --}}
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50 opacity-60 cursor-not-allowed">
                    <input type="checkbox" checked disabled class="w-4 h-4 rounded">
                    <input type="hidden" name="roles[]" value="faculty">
                    <i class="bx bx-user text-green-600 text-lg leading-none shrink-0"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700">Faculty</p>
                        <p class="text-xs text-slate-500">Default role — always assigned</p>
                    </div>
                </div>

                <x-feedback-status.alert type="info"
                    title="Role changes will be notified to the user via email." />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Roles
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
