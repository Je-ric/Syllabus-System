<x-modal.dialog :id="$modalId" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]"
    onclose="this.querySelector('form')?.reset()">
    <form method="POST" action="{{ route('account-approval.assign-role') }}" class="flex flex-col"
        onsubmit="
            const dean = this.querySelector('input[name=&quot;roles[]&quot;][value=&quot;dean&quot;]')?.checked;
            const chair = this.querySelector('input[name=&quot;roles[]&quot;][value=&quot;chair&quot;]')?.checked;
            if (dean && chair) {
                alert('A user cannot hold both Dean and Chair roles simultaneously.');
                return false;
            }
            return true;
        ">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                    <span class="text-slate-600 font-bold text-sm uppercase">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-shield text-slate-500 text-2xl"></i>
                        Assign Roles
                    </h3>
                    <p class="text-sm text-gray-500">{{ $user->name }}</p>
                </div>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-3">

                @php
                    $roles = [
                        ['name' => 'admin', 'label' => 'Admin',            'desc' => 'Full system access',              'icon' => 'bx-crown',    'color' => 'text-purple-600'],
                        ['name' => 'dean',  'label' => 'College Dean',     'desc' => 'Manage college-level operations', 'icon' => 'bx-medal',    'color' => 'text-indigo-600'],
                        ['name' => 'chair', 'label' => 'Department Chair', 'desc' => 'Manage department operations',    'icon' => 'bx-user-pin', 'color' => 'text-blue-600'],
                    ];
                @endphp

                @foreach ($roles as $role)
                    <label class="relative flex items-start p-4 bg-white border border-gray-200 rounded-lg hover:border-slate-400 hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="roles[]" value="{{ $role['name'] }}"
                                class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-400"
                                {{ $user->roles->contains('name', $role['name']) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <i class="bx {{ $role['icon'] }} {{ $role['color'] }} text-lg leading-none"></i>
                                <span class="text-sm font-semibold text-gray-900">{{ $role['label'] }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $role['desc'] }}</p>
                        </div>
                    </label>
                @endforeach

                {{-- Faculty — always assigned --}}
                <div class="flex items-start p-4 bg-gray-50 border border-gray-200 rounded-lg opacity-60 cursor-not-allowed">
                    <div class="flex items-center h-5">
                        <input type="checkbox" checked disabled class="w-4 h-4 rounded">
                        <input type="hidden" name="roles[]" value="faculty">
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <i class="bx bx-user text-green-600 text-lg leading-none"></i>
                            <span class="text-sm font-semibold text-gray-700">Faculty</span>
                        </div>
                        <p class="mt-0.5 text-xs text-gray-500">Default role — always assigned</p>
                    </div>
                </div>

                <x-feedback-status.alert type="warning" title="Dean and Chair roles cannot be assigned at the same time." />
                <x-feedback-status.alert type="info" title="Role changes will be notified to the user via email." />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="$modalId" text="Cancel" variant="cancel" />
                <x-button type="submit" variant="save">
                    <i class="bx bx-save"></i> Save Roles
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
