<div @if($liveRefresh) wire:poll.30s="refresh" @endif>

    {{-- ── Filter panel ──────────────────────────────────────────────────── --}}
    <x-layout.card-section
        title="Filters"
        icon="bx-filter-alt"
        class="mb-4">

        <x-slot:actions>
            <button wire:click="clearFilters"
                class="text-xs text-[#94a3b8] hover:text-rose-500 transition flex items-center gap-1">
                <i class="bx bx-reset text-sm leading-none"></i> Clear all
            </button>
        </x-slot:actions>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">

            <div class="col-span-2 xl:col-span-1">
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    Keyword
                </label>

                <div class="xl:col-span-2 relative">
                    <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                        <x-form.input
                            type="text"
                            wire:model.live.debounce.400ms="keyword"
                            placeholder="Search…"
                            class="pl-9"/>
                    </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    User
                </label>
                <x-form.select wire:model.lazy="userId">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    Module
                </label>
                <x-form.select wire:model.lazy="module">
                    <option value="">All Modules</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod }}">{{ $mod }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    Action
                </label>
                <x-form.select wire:model.lazy="action">
                    <option value="">All Actions</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    Ref ID
                </label>
                <x-form.input
                    type="number"
                    wire:model.live.debounce.150ms="referenceId"
                    min="1"
                    placeholder="e.g. 42" />
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    From
                </label>
                <x-form.input type="date" wire:model.lazy="dateFrom" />
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1">
                    To
                </label>
                <x-form.input type="date" wire:model.lazy="dateTo" />
            </div>

        </div>

    </x-layout.card-section>

    {{-- ── Toolbar ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">

        <button wire:click="openPurgeModal" type="button"
            class="inline-flex items-center gap-1.5 text-[13px] px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 transition font-medium">
            <i class="bx bx-trash text-sm leading-none"></i>
            Purge Old Logs
        </button>

        <button wire:click="$toggle('liveRefresh')" type="button"
            class="inline-flex items-center gap-1.5 text-[13px] px-2.5 py-1 rounded-full border transition
                {{ $liveRefresh
                    ? 'bg-[#f0fdf4] text-[#166534] border-[#bbf7d0] hover:bg-[#dcfce7]'
                    : 'bg-[#f8fafc] text-[#475569] border-[#e2e8f0] hover:bg-[#f0fdf4]' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $liveRefresh ? 'bg-[#16a34a] animate-pulse' : 'bg-[#94a3b8]' }}"></span>
            {{ $liveRefresh ? 'Live' : 'Paused' }}
        </button>

    </div>

    {{-- ── Purge modal ─────────────────────────────────────────────────── --}}
    @include('livewire.audit-log.modals.purge-modal', [
        'confirmingPurge' => $confirmingPurge,
        'purgeMonths' => $purgeMonths,
        'protectSyllabusLogs' => $protectSyllabusLogs,
        'purgePreviewCount' => $purgePreviewCount,
    ])

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
                @if ($isLoading)
                    <x-table.row>
                        <x-table.td colspan="6" class="text-center py-8">
                            <div class="flex items-center justify-center gap-2 text-[#94a3b8]">
                                <i class="bx bx-loader-alt animate-spin text-lg"></i>
                                <span class="text-[13px]">Loading audit logs...</span>
                            </div>
                        </x-table.td>
                    </x-table.row>
                @else
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
                                <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-[13px] font-medium text-[#475569] whitespace-nowrap">
                                    {{ $log->module }}
                                </span>
                            </x-table.td>

                            {{-- Action — icon + colour --}}
                            <x-table.td>
                                @include('livewire.audit-log.modals.action-badge', ['action' => $log->action])
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
                @endif
            </x-table.body>
        </x-table.table>
    </x-table.container>

    <x-pagination.custom :paginator="$this->logs" />

</div>
