<div @if($liveRefresh) wire:poll.10s="refresh" @endif>

    {{-- ── Filter panel ──────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm mb-4">

        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                <i class="bx bx-filter-alt"></i> Filters
            </span>
            <button wire:click="clearFilters" type="button"
                class="text-xs text-slate-400 hover:text-rose-500 transition underline">
                Clear all
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">User</label>
                <x-form.select wire:model.live="userId">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Module</label>
                <x-form.select wire:model.live="module">
                    <option value="">All Modules</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod }}">{{ $mod }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Action</label>
                <x-form.select wire:model.live="action">
                    <option value="">All Actions</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Ref ID</label>
                <x-form.input type="number" wire:model.live.debounce.500ms="referenceId" min="1" placeholder="e.g. 42" />
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
                <x-form.input type="date" wire:model.live="dateFrom" />
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
                <x-form.input type="date" wire:model.live="dateTo" />
            </div>

            <div class="col-span-2 xl:col-span-1">
                <label class="block text-xs font-medium text-slate-500 mb-1">Keyword</label>
                <x-form.input type="text" wire:model.live.debounce.400ms="keyword" placeholder="Search…" />
            </div>

        </div>
    </div>

    {{-- ── Toolbar: count + live toggle ─────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
        <p class="text-xs text-slate-400">
            <span class="font-semibold text-slate-600">{{ number_format($this->logs->total()) }}</span> result{{ $this->logs->total() !== 1 ? 's' : '' }}
            <span class="mx-1.5 text-slate-300">·</span>
            Updated <span class="font-medium text-slate-500">{{ $lastRefreshed }}</span>
        </p>

        <button wire:click="$toggle('liveRefresh')" type="button"
            class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full border transition
                {{ $liveRefresh
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                    : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $liveRefresh ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
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
                        <x-table.td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-xs text-slate-700 font-medium"
                                title="{{ optional($log->timestamp)->format('M d, Y H:i:s') }}">
                                {{ optional($log->timestamp)->diffForHumans() }}
                            </span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">
                                {{ optional($log->timestamp)->format('M d, H:i') }}
                            </span>
                        </x-table.td>

                        {{-- User --}}
                        <x-table.td class="px-4 py-3">
                            <span class="text-sm font-medium text-slate-700">
                                {{ $log->user?->name ?? '—' }}
                            </span>
                        </x-table.td>

                        {{-- Module --}}
                        <x-table.td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200 whitespace-nowrap">
                                {{ $log->module }}
                            </span>
                        </x-table.td>

                        {{-- Action — icon + colour --}}
                        <x-table.td class="px-4 py-3">
                            @php
                                $badge = match ($log->action) {
                                    'created'  => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'bx-plus-circle'],
                                    'updated'  => ['bg-blue-50 text-blue-700 ring-blue-200',          'bx-edit'],
                                    'deleted'  => ['bg-rose-50 text-rose-700 ring-rose-200',          'bx-trash'],
                                    'login'    => ['bg-violet-50 text-violet-700 ring-violet-200',    'bx-log-in'],
                                    'logout'   => ['bg-slate-50 text-slate-600 ring-slate-200',       'bx-log-out'],
                                    'approved' => ['bg-teal-50 text-teal-700 ring-teal-200',          'bx-check-circle'],
                                    'denied'   => ['bg-orange-50 text-orange-700 ring-orange-200',    'bx-x-circle'],
                                    default    => ['bg-slate-50 text-slate-600 ring-slate-200',       'bx-circle'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 whitespace-nowrap {{ $badge[0] }}">
                                <i class="bx {{ $badge[1] }} text-[11px] leading-none"></i>
                                {{ ucfirst($log->action) }}
                            </span>
                        </x-table.td>

                        {{-- Ref ID --}}
                        <x-table.td class="px-4 py-3 text-xs text-slate-400 font-mono">
                            {{ $log->reference_id ?? '—' }}
                        </x-table.td>

                        {{-- Description — truncated, expand on click --}}
                        <x-table.td class="px-4 py-3 text-sm text-slate-600 max-w-sm">
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
