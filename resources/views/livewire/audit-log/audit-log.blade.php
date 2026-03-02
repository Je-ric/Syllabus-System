{{--
    livewire/audit-log.blade.php
    ─────────────────────────────────────────────────────────────────────
    Livewire component view for the Audit Logs page.

    Real-time: wire:poll.5s fires refresh() every 5 seconds, which
    re-renders the component and updates the "Last updated" timestamp.
    The user can pause polling with the Live / Paused toggle.

    All filters use wire:model.live — any change instantly re-queries.
    Debounce is used on text inputs to avoid firing on every keystroke.
--}}
<div @if($liveRefresh) wire:poll.5s="refresh" @endif>

    {{-- ── Filter panel ─────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-5 shadow-sm mb-5">

        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 flex items-center gap-2">
                <i class="bx bx-filter-alt text-slate-400"></i> Filters
            </p>
            <button wire:click="clearFilters" type="button"
                class="text-xs text-slate-400 hover:text-rose-500 transition underline">
                Clear all
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">User</label>
                <x-form.select wire:model.live="userId">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Module</label>
                <x-form.select wire:model.live="module">
                    <option value="">All Modules</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod }}">{{ $mod }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Action</label>
                <x-form.select wire:model.live="action">
                    <option value="">All Actions</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reference ID</label>
                <x-form.input
                    type="number"
                    wire:model.live.debounce.500ms="referenceId"
                    min="1"
                    placeholder="e.g. 42" />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">From</label>
                <x-form.input type="date" wire:model.live="dateFrom" />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">To</label>
                <x-form.input type="date" wire:model.live="dateTo" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Keyword</label>
                <x-form.input
                    type="text"
                    wire:model.live.debounce.400ms="keyword"
                    placeholder="Search description, module, action…" />
            </div>

        </div>
    </div>

    {{-- ── Results count + Live toggle ──────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
        <p class="text-xs text-slate-400">
            <span class="font-semibold text-slate-600">{{ number_format($logs->total()) }}</span> result(s)
            <span class="mx-1.5 text-slate-300">·</span>
            Updated: <span class="font-medium text-slate-500">{{ $lastRefreshed }}</span>
        </p>
    </div>

    {{-- ── Audit log table ─────────────────────────────────────────────── --}}
    <x-table.container>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th class="px-4 py-2.5 whitespace-nowrap">Time</x-table.th>
                    <x-table.th class="px-4 py-2.5">User</x-table.th>
                    <x-table.th class="px-4 py-2.5">Module</x-table.th>
                    <x-table.th class="px-4 py-2.5">Action</x-table.th>
                    <x-table.th class="px-4 py-2.5">Ref ID</x-table.th>
                    <x-table.th class="px-4 py-2.5">Description</x-table.th>
                </x-table.row>
            </x-table.head>

            <x-table.body>
                @forelse ($logs as $log)
                    <x-table.row striped hover>

                        {{-- Time — split date and time for readability --}}
                        <x-table.td class="px-4 py-2.5 whitespace-nowrap font-mono text-xs">
                            <span class="text-slate-700">{{ optional($log->timestamp)->format('Y-m-d') }}</span>
                            <span class="block text-slate-400">{{ optional($log->timestamp)->format('H:i:s') }}</span>
                        </x-table.td>

                        <x-table.td class="px-4 py-2.5 text-sm font-medium text-slate-700">
                            {{ $log->user?->name ?? '—' }}
                        </x-table.td>

                        {{-- Module badge --}}
                        <x-table.td class="px-4 py-2.5">
                            <span class="inline-flex items-center rounded-full
                                        bg-slate-100 px-2.5 py-0.5
                                        text-xs font-semibold text-slate-600
                                        ring-1 ring-slate-200 whitespace-nowrap">
                                {{ $log->module }}
                            </span>
                        </x-table.td>

                        {{-- Action badge — colour-coded --}}
                        <x-table.td class="px-4 py-2.5">
                            @php
                                $actionBadge = match ($log->action) {
                                    'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                    'updated' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                    'deleted' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                    default   => 'bg-slate-50 text-slate-600 ring-slate-200',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full
                                        px-2.5 py-0.5 text-xs font-semibold
                                        ring-1 {{ $actionBadge }} whitespace-nowrap">
                                {{ ucfirst($log->action) }}
                            </span>
                        </x-table.td>

                        <x-table.td class="px-4 py-2.5 font-mono text-xs text-slate-500">
                            {{ $log->reference_id ?? '—' }}
                        </x-table.td>

                        <x-table.td class="px-4 py-2.5 text-sm text-slate-600 max-w-xs">
                            {{ $log->description ?? '—' }}
                        </x-table.td>

                    </x-table.row>
                @empty
                    <x-table.empty :colspan="6" message="No audit logs match the selected filters." class="py-8" />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-table.container>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

</div>
