<div @if($liveRefresh) wire:poll.10s="refresh" @endif>

    {{-- ── Filter panel ──────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 mb-4" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

        <div class="flex items-center justify-between mb-3">
            <span class="text-[13px] font-semibold text-[#475569] flex items-center gap-1.5">
                <i class="bx bx-filter-alt"></i> Filters
            </span>
            <button wire:click="clearFilters" type="button"
                class="text-[13px] text-[#94a3b8] hover:text-rose-500 transition underline">
                Clear all
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">User</label>
                <x-form.select wire:model.live="userId">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">Module</label>
                <x-form.select wire:model.live="module">
                    <option value="">All Modules</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod }}">{{ $mod }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">Action</label>
                <x-form.select wire:model.live="action">
                    <option value="">All Actions</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">Ref ID</label>
                <x-form.input type="number" wire:model.live.debounce.500ms="referenceId" min="1" placeholder="e.g. 42" />
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">From</label>
                <x-form.input type="date" wire:model.live="dateFrom" />
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">To</label>
                <x-form.input type="date" wire:model.live="dateTo" />
            </div>

            <div class="col-span-2 xl:col-span-1">
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">Keyword</label>
                <x-form.input type="text" wire:model.live.debounce.400ms="keyword" placeholder="Search…" />
            </div>

        </div>
    </div>

    {{-- ── Toolbar: count + live toggle ─────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
        <p class="text-[13px] text-[#94a3b8]">
            <span class="font-semibold text-[#0f172a]">{{ number_format($this->logs->total()) }}</span> result{{ $this->logs->total() !== 1 ? 's' : '' }}
            <span class="mx-1.5 text-[#e2e8f0]">·</span>
            Updated <span class="font-medium text-[#475569]">{{ $lastRefreshed }}</span>
        </p>

        <button wire:click="$toggle('liveRefresh')" type="button"
            class="inline-flex items-center gap-1.5 text-[13px] px-2.5 py-1 rounded-full border transition
                {{ $liveRefresh
                    ? 'bg-[#f0fdf4] text-[#166534] border-[#bbf7d0] hover:bg-[#dcfce7]'
                    : 'bg-[#f8fafc] text-[#475569] border-[#e2e8f0] hover:bg-[#f0fdf4]' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $liveRefresh ? 'bg-[#16a34a] animate-pulse' : 'bg-[#94a3b8]' }}"></span>
            {{ $liveRefresh ? 'Live' : 'Paused' }}
        </button>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────────────── --}}
    <x-table.container>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th class="px-4 py-2.5 w-36">When</x-table.th>
                    <x-table.th class="px-4 py-2.5">User</x-table.th>
                    <x-table.th class="px-4 py-2.5">Module</x-table.th>
                    <x-table.th class="px-4 py-2.5 w-24">Action</x-table.th>
                    <x-table.th class="px-4 py-2.5 w-20">Ref</x-table.th>
                    <x-table.th class="px-4 py-2.5">Description</x-table.th>
                </x-table.row>
            </x-table.head>

            <x-table.body>
                @forelse ($this->logs as $log)
                    <x-table.row striped hover>

                        {{-- When: human-readable + exact on hover --}}
                        <x-table.td class="whitespace-nowrap">
                            <p class="text-[13px] font-medium text-[#0f172a]"
                                title="{{ optional($log->timestamp)->format('M d, Y H:i:s') }}">
                                {{ optional($log->timestamp)->diffForHumans() }}
                            </p>
                            <p class="text-[11px] text-[#94a3b8] mt-0.5">
                                {{ optional($log->timestamp)->format('M d, H:i') }}
                            </p>
                        </x-table.td>

                        {{-- User --}}
                        <x-table.td>
                            <span class="text-[13px] font-medium text-[#0f172a]">
                                {{ $log->user?->name ?? '—' }}
                            </span>
                        </x-table.td>

                        {{-- Module --}}
                        <x-table.td>
                            <span class="inline-flex items-center rounded-lg bg-[#f8fafc] px-2 py-0.5 text-[13px] font-medium text-[#475569] ring-1 ring-[#e2e8f0] whitespace-nowrap">
                                {{ $log->module }}
                            </span>
                        </x-table.td>

                        {{-- Action — icon + colour --}}
                        <x-table.td>
                            @php
                                $badge = match ($log->action) {
                                    'created'  => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-plus-circle'],
                                    'updated'  => ['bg-[#eff6ff] text-[#1e40af] ring-[#bfdbfe]',   'bx-edit'],
                                    'deleted'  => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-trash'],
                                    'login'    => ['bg-[#faf5ff] text-[#581c87] ring-[#d8b4fe]',   'bx-log-in'],
                                    'logout'   => ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-log-out'],
                                    'approved' => ['bg-[#f0fdf4] text-[#166534] ring-[#bbf7d0]',   'bx-check-circle'],
                                    'denied'   => ['bg-[#fff1f2] text-[#9f1239] ring-[#fda4af]',   'bx-x-circle'],
                                    default    => ['bg-[#f8fafc] text-[#475569] ring-[#e2e8f0]',   'bx-circle'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 rounded-lg px-2 py-0.5 text-[13px] font-medium ring-1 whitespace-nowrap {{ $badge[0] }}">
                                <i class="bx {{ $badge[1] }} text-[11px] leading-none"></i>
                                {{ ucfirst($log->action) }}
                            </span>
                        </x-table.td>

                        {{-- Ref ID --}}
                        <x-table.td class="text-[13px] text-[#94a3b8] font-mono">
                            {{ $log->reference_id ?? '—' }}
                        </x-table.td>

                        {{-- Description — truncated, expand on click --}}
                        <x-table.td class="text-[#475569] max-w-sm">
                            @if ($log->description)
                                <span x-data="{ open: false }">
                                    <span x-show="!open" class="line-clamp-2 cursor-pointer hover:text-slate-800"
                                        @click="open = true"
                                        title="Click to expand">{{ $log->description }}</span>
                                    <span x-show="open" class="cursor-pointer hover:text-slate-800"
                                        @click="open = false">{{ $log->description }}</span>
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </x-table.td>

                    </x-table.row>
                @empty
                    <x-table.empty :colspan="6" message="No audit logs match the selected filters." class="py-10" />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-table.container>

    <div class="mt-4">
        {{ $this->logs->links() }}
    </div>

</div>
