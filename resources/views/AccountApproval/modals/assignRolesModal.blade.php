<x-modal.dialog :id="$modalId" maxWidth="max-w-xl" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h3 class="text-lg font-semibold text-slate-800">
            Assign Roles to {{ $user->name }}
        </h3>
    </x-modal.header>

    <form method="POST" action="{{ route('account-approval.assign-role') }}" class="flex flex-col h-full">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.body>
            <div class="space-y-3">
                @php
                    $roles = [
                        ['name'=>'admin','label'=>'Admin','desc'=>'Full system access'],
                        ['name'=>'dean','label'=>'College Dean','desc'=>'Manage college-level operations'],
                        ['name'=>'chair','label'=>'Department Chair','desc'=>'Manage department operations'],
                    ];
                @endphp

                @foreach ($roles as $role)
                    <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="roles[]" value="{{ $role['name'] }}" class="w-4 h-4 text-blue-600"
                            {{ $user->roles->contains('name', $role['name']) ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="font-medium text-slate-800">{{ $role['label'] }}</span>
                            <p class="text-xs text-slate-500">{{ $role['desc'] }}</p>
                        </div>
                    </label>
                @endforeach

                {{-- Faculty always assigned --}}
                <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg bg-slate-50 opacity-75">
                    <input type="checkbox" checked disabled class="w-4 h-4">
                    <input type="hidden" name="roles[]" value="faculty">
                    <div class="flex-1">
                        <span class="font-medium text-slate-600">Faculty</span>
                        <p class="text-xs text-slate-500">Default role (Always assigned)</p>
                    </div>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" variant="cancel" />
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                Save Roles
            </button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
