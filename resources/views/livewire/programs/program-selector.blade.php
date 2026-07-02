{{--
    Program Selector — cascading College → Department → Program
    Consistent with app's CLSU green brand + slate neutrals.

    Props:
    - $colleges, $departments, $programs  (Livewire public)
    - $collegeId, $departmentId, $programId (Livewire wire:model.live)
--}}

<div>
    @if ($locked ?? false)
        @php
            $lockedCollege = collect($colleges)->firstWhere('id', (int)$collegeId);
            $lockedDept    = collect($departments)->firstWhere('id', (int)$departmentId);
            $lockedProgram = collect($programs)->firstWhere('id', (int)$programId);
        @endphp

        {{-- Same breadcrumb as unlocked, all steps filled --}}
        <div class="flex items-center gap-2 mb-4 text-xs text-slate-400 select-none">
            @foreach ([['College', 1], ['Department', 2], ['Program', 3]] as [$step, $num])
                <span class="flex items-center gap-1.5 font-semibold text-emerald-600">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold bg-emerald-500 text-white">{{ $num }}</span>
                    {{ $step }}
                </span>
                @if ($num < 3)<i class="bx bx-chevron-right text-slate-300"></i>@endif
            @endforeach
        </div>

        {{-- Same grid as unlocked, but read-only fields --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach ([
                ['bx-buildings',     $lockedCollege?->name ?? '—', 'College'],
                ['bx-sitemap',       $lockedDept?->name    ?? '—', 'Department'],
                ['bx-network-chart', $lockedProgram?->name ?? '—', 'Program'],
            ] as [$icon, $value, $hint])
                <div class="space-y-1.5">
                    <label class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">
                        <i class="bx {{ $icon }} text-emerald-500"></i>
                        {{ $hint }}
                    </label>
                    <div class="relative">
                        <div class="w-full rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 pr-9 text-sm text-slate-500 shadow-sm cursor-not-allowed truncate">
                            {{ $value }}
                        </div>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="bx bx-lock-alt text-slate-300 text-base"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
    {{-- Step breadcrumb: shows how far along the selection is --}}
    <div class="flex items-center gap-2 mb-4 text-xs text-slate-400 select-none">
        <span @class([
            'flex items-center gap-1.5 font-semibold transition-colors',
            'text-emerald-600' => $collegeId,
            'text-slate-400'   => !$collegeId,
        ])>
            <span @class([
                'inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold transition-colors',
                'bg-emerald-500 text-white'     => $collegeId,
                'bg-slate-200 text-slate-500'   => !$collegeId,
            ])>1</span>
            College
        </span>
        <i class="bx bx-chevron-right text-slate-300"></i>
        <span @class([
            'flex items-center gap-1.5 font-semibold transition-colors',
            'text-emerald-600' => $departmentId,
            'text-slate-400'   => !$departmentId,
        ])>
            <span @class([
                'inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold transition-colors',
                'bg-emerald-500 text-white'   => $departmentId,
                'bg-slate-200 text-slate-500' => !$departmentId,
            ])>2</span>
            Department
        </span>
        <i class="bx bx-chevron-right text-slate-300"></i>
        <span @class([
            'flex items-center gap-1.5 font-semibold transition-colors',
            'text-emerald-600' => $programId,
            'text-slate-400'   => !$programId,
        ])>
            <span @class([
                'inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold transition-colors',
                'bg-emerald-500 text-white'   => $programId,
                'bg-slate-200 text-slate-500' => !$programId,
            ])>3</span>
            Program
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

        {{-- ── College ─────────────────────────────────────────────────────── --}}
        <div class="space-y-1.5">
            <label class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">
                <i class="bx bx-buildings text-emerald-500"></i>
                College
            </label>
            <div class="relative">
                <select
                    wire:model.live="collegeId"
                    class="w-full appearance-none rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-9 text-sm
                           text-slate-700 shadow-sm ring-0 transition
                           focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 focus:outline-none
                           hover:border-slate-300">
                    <option value="">Select college…</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                    @endforeach
                </select>

                {{-- Custom chevron --}}
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <span wire:loading.remove wire:target="collegeId">
                        <i class="bx bx-chevron-down text-base"></i>
                    </span>
                    <span wire:loading wire:target="collegeId">
                        <i class="bx bx-loader-alt bx-spin text-emerald-500 text-base"></i>
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Department ──────────────────────────────────────────────────── --}}
        <div class="space-y-1.5">
            <label class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">
                <i class="bx bx-sitemap text-emerald-500"></i>
                Department
            </label>
            <div class="relative">
                <select
                    wire:model.live="departmentId"
                    @disabled(!$collegeId)
                    @class([
                        'w-full appearance-none rounded-xl border px-4 py-2.5 pr-9 text-sm shadow-sm ring-0 transition focus:outline-none',
                        'border-slate-200 bg-white text-slate-700 hover:border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100' => $collegeId,
                        'border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed' => !$collegeId,
                    ])>
                    <option value="">
                        {{ !$collegeId ? '← Select college first' : 'Select department…' }}
                    </option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>

                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <span wire:loading.remove wire:target="collegeId,departmentId">
                        <i @class([
                            'bx text-base',
                            'bx-chevron-down'     => $collegeId,
                            'bx-lock-alt text-slate-300' => !$collegeId,
                        ])></i>
                    </span>
                    <span wire:loading wire:target="collegeId,departmentId">
                        <i class="bx bx-loader-alt bx-spin text-emerald-500 text-base"></i>
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Program ──────────────────────────────────────────────────────── --}}
        <div class="space-y-1.5">
            <label class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">
                <i class="bx bx-network-chart text-emerald-500"></i>
                Program
            </label>
            <div class="relative">
                <select
                    wire:model.live="programId"
                    @disabled(!$departmentId)
                    @class([
                        'w-full appearance-none rounded-xl border px-4 py-2.5 pr-9 text-sm shadow-sm ring-0 transition focus:outline-none',
                        'border-slate-200 bg-white text-slate-700 hover:border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100' => $departmentId,
                        'border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed' => !$departmentId,
                    ])>
                    <option value="">
                        {{ !$departmentId ? '← Select department first' : 'Select program…' }}
                    </option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>

                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <span wire:loading.remove wire:target="departmentId,programId">
                        <i @class([
                            'bx text-base',
                            'bx-chevron-down'            => $departmentId,
                            'bx-lock-alt text-slate-300' => !$departmentId,
                        ])></i>
                    </span>
                    <span wire:loading wire:target="departmentId,programId">
                        <i class="bx bx-loader-alt bx-spin text-emerald-500 text-base"></i>
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Selected program confirmation chip --}}
    @if ($programId && count($programs))
        @php $selectedProgram = collect($programs)->firstWhere('id', (int)$programId); @endphp
        @if ($selectedProgram)
            <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-emerald-200
                        bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <i class="bx bx-check-circle text-emerald-500 text-sm"></i>
                {{ $selectedProgram->name }}
            </div>
        @endif
    @endif
    @endif {{-- end @else (not locked) --}}
</div>
