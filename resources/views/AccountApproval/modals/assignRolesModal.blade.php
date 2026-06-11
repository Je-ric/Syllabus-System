@php
    $avatarColors = match($user->account_status) {
        'active' => 'bg-[#dcfce7] text-[#166534]',
        default  => 'bg-[#f1f5f9] text-[#475569]',
    };
@endphp

<x-modal.dialog :id="$modalId" maxWidth="max-w-lg" width="w-11/12"
    onclose="this.querySelector('form')?.reset()">
    <form method="POST" action="{{ route('account-approval.assign-role') }}"
            class="flex flex-col h-full"
            onsubmit="
                const dean  = this.querySelector('input[name=&quot;roles[]&quot;][value=&quot;dean&quot;]')?.checked;
                const chair = this.querySelector('input[name=&quot;roles[]&quot;][value=&quot;chair&quot;]')?.checked;
                if (dean && chair) { alert('A user cannot hold both Dean and Chair roles simultaneously.'); return false; }
                return true;
            ">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId" variant="roles">
            Assign Roles
        </x-modal.header>

        {{-- Body: fixed max-height so footer is always visible --}}
        <div class="flex-1 min-h-0 overflow-y-auto p-5 space-y-4">

            {{-- Identity strip --}}
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                <span class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full border font-bold text-sm {{ $avatarColors }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-semibold text-[#0f172a] truncate">{{ $user->name }}</p>
                    <p class="text-[12px] text-[#94a3b8] truncate">{{ $user->email }}</p>
                </div>
                <x-feedback-status.status-indicator :status="$user->account_status" />
            </div>

            {{-- Role grid --}}
            @php
                $roles = [
                    ['name' => 'admin', 'label' => 'Admin',            'desc' => 'Full system access',              'icon' => 'bx-crown',    'iconBg' => 'bg-violet-100', 'iconColor' => 'text-violet-700', 'ring' => 'hover:border-violet-300 hover:bg-violet-50/60'],
                    ['name' => 'dean',  'label' => 'College Dean',     'desc' => 'College-level management',        'icon' => 'bx-medal',    'iconBg' => 'bg-blue-100',   'iconColor' => 'text-blue-700',   'ring' => 'hover:border-blue-300 hover:bg-blue-50/60'],
                    ['name' => 'chair', 'label' => 'Dept. Chair',      'desc' => 'Department management',           'icon' => 'bx-user-pin', 'iconBg' => 'bg-sky-100',    'iconColor' => 'text-sky-700',    'ring' => 'hover:border-sky-300 hover:bg-sky-50/60'],
                    ['name' => 'faculty','label'=> 'Faculty',          'desc' => 'Default — always assigned',       'icon' => 'bx-user',     'iconBg' => 'bg-[#dcfce7]',  'iconColor' => 'text-[#16a34a]',  'ring' => '', 'locked' => true],
                ];
            @endphp

            <div class="grid grid-cols-2 gap-2">
                @foreach($roles as $role)
                    @if(!empty($role['locked']))
                        {{-- Faculty: locked card --}}
                        <label class="relative flex flex-col gap-2 p-3 rounded-xl border border-[#e2e8f0]
                                      bg-[#f8fafc] opacity-60 cursor-not-allowed select-none">
                            <input type="hidden" name="roles[]" value="faculty">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg shrink-0 {{ $role['iconBg'] }} {{ $role['iconColor'] }}">
                                    <i class="bx {{ $role['icon'] }} text-sm leading-none"></i>
                                </span>
                                <input type="checkbox" checked disabled
                                    class="ml-auto w-4 h-4 rounded border-[#e2e8f0] cursor-not-allowed">
                            </div>
                            <div>
                                <p class="text-[13px] font-semibold text-[#0f172a] leading-tight">{{ $role['label'] }}</p>
                                <p class="text-[11px] text-[#94a3b8] mt-0.5 leading-tight">{{ $role['desc'] }}</p>
                            </div>
                            <span class="absolute top-2 right-2 text-[10px] font-bold uppercase tracking-wide
                                         text-[#16a34a] bg-[#dcfce7] px-1.5 py-0.5 rounded-full">
                                Always
                            </span>
                        </label>
                    @else
                        {{-- Assignable role card --}}
                        <label class="relative flex flex-col gap-2 p-3 rounded-xl border border-[#e2e8f0]
                                      bg-white {{ $role['ring'] }} transition-colors cursor-pointer
                                      has-checked:border-[#16a34a] has-checked:bg-[#f0fdf4]">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg shrink-0 {{ $role['iconBg'] }} {{ $role['iconColor'] }}">
                                    <i class="bx {{ $role['icon'] }} text-sm leading-none"></i>
                                </span>
                                <input type="checkbox" name="roles[]" value="{{ $role['name'] }}"
                                    class="ml-auto w-4 h-4 rounded border-[#e2e8f0] text-[#16a34a] focus:ring-[#bbf7d0] cursor-pointer"
                                    {{ $user->roles->contains('name', $role['name']) ? 'checked' : '' }}>
                            </div>
                            <div>
                                <p class="text-[13px] font-semibold text-[#0f172a] leading-tight">{{ $role['label'] }}</p>
                                <p class="text-[11px] text-[#94a3b8] mt-0.5 leading-tight">{{ $role['desc'] }}</p>
                            </div>
                        </label>
                    @endif
                @endforeach
            </div>

            {{-- Single combined notice --}}
            <div class="rounded-xl border border-[#fcd34d] bg-[#fffbeb] px-3 py-2.5 flex items-start gap-2">
                <i class="bx bx-error text-[#f59e0b] text-base shrink-0 mt-0.5"></i>
                <p class="text-[12px] text-[#92400e] leading-relaxed">
                    <strong>Dean</strong> and <strong>Chair</strong> cannot be assigned simultaneously.
                    Changes take effect immediately and the user will be notified.
                </p>
            </div>

        </div>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save leading-none"></i> Save Roles
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
