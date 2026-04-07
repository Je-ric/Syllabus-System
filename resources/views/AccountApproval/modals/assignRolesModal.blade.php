<x-modal.dialog :id="$modalId" maxWidth="max-w-md" width="w-11/12"
    onclose="this.querySelector('form')?.reset()">
    <form method="POST" action="{{ route('account-approval.assign-role') }}" class="flex flex-col"
        onsubmit="
            const dean = this.querySelector('input[name=&quot;roles[]&quot;][value=&quot;dean&quot;]')?.checked;
            const chair = this.querySelector('input[name=&quot;roles[]&quot;][value=&quot;chair&quot;]')?.checked;
            if (dean && chair) { alert('A user cannot hold both Dean and Chair roles simultaneously.'); return false; }
            return true;
        ">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#e2e8f0] text-[#475569] shrink-0">
                    <i class="bx bx-shield text-base leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-[#0f172a]">Assign Roles</p>
                    <p class="text-[13px] text-[#94a3b8] truncate">{{ $user->name }}</p>
                </div>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-2">
                @php
                    $roles = [
                        ['name' => 'admin', 'label' => 'Admin',            'desc' => 'Full system access',              'icon' => 'bx-crown',    'color' => 'text-[#581c87]', 'bg' => 'bg-[#faf5ff]'],
                        ['name' => 'dean',  'label' => 'College Dean',     'desc' => 'Manage college-level operations', 'icon' => 'bx-medal',    'color' => 'text-[#3730a3]', 'bg' => 'bg-[#eef2ff]'],
                        ['name' => 'chair', 'label' => 'Department Chair', 'desc' => 'Manage department operations',    'icon' => 'bx-user-pin', 'color' => 'text-[#1e40af]', 'bg' => 'bg-[#eff6ff]'],
                    ];
                @endphp

                @foreach ($roles as $role)
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[#e2e8f0] bg-white
                                  hover:border-[#bbf7d0] hover:bg-[#f0fdf4] transition-colors cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role['name'] }}"
                            class="mt-0.5 w-4 h-4 rounded text-[#16a34a] border-[#e2e8f0] focus:ring-[#bbf7d0]"
                            {{ $user->roles->contains('name', $role['name']) ? 'checked' : '' }}>
                        <div class="flex items-center gap-2.5 flex-1 min-w-0">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg shrink-0 {{ $role['bg'] }} {{ $role['color'] }}">
                                <i class="bx {{ $role['icon'] }} text-sm leading-none"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold text-[#0f172a]">{{ $role['label'] }}</p>
                                <p class="text-[11px] text-[#94a3b8]">{{ $role['desc'] }}</p>
                            </div>
                        </div>
                    </label>
                @endforeach

                {{-- Faculty — always assigned --}}
                <div class="flex items-start gap-3 p-3.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] opacity-60 cursor-not-allowed">
                    <input type="checkbox" checked disabled class="mt-0.5 w-4 h-4 rounded">
                    <input type="hidden" name="roles[]" value="faculty">
                    <div class="flex items-center gap-2.5 flex-1 min-w-0">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg shrink-0 bg-[#f0fdf4] text-[#16a34a]">
                            <i class="bx bx-user text-sm leading-none"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-semibold text-[#0f172a]">Faculty</p>
                            <p class="text-[11px] text-[#94a3b8]">Default role — always assigned</p>
                        </div>
                    </div>
                </div>

                <x-feedback-status.alert type="warning" :showTitle="false">Dean and Chair roles cannot be assigned at the same time.</x-feedback-status.alert>
                <x-feedback-status.alert type="info" :showTitle="false">Role changes will be notified to the user via email.</x-feedback-status.alert>
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
