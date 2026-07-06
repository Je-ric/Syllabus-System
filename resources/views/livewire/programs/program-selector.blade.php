<div>
    @if ($locked ?? false)
        @php
            $lockedCollege = collect($colleges)->firstWhere('id', (int)$collegeId);
            $lockedDept    = collect($departments)->firstWhere('id', (int)$departmentId);
            $lockedProgram = collect($programs)->firstWhere('id', (int)$programId);
        @endphp

        <div class="flex items-center gap-2 mb-4 text-xs text-[#a1a1aa] select-none">
            @foreach ([['College', 1], ['Department', 2], ['Program', 3]] as [$step, $num])
                <span class="flex items-center gap-1.5 font-semibold text-[#16a34a]">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold bg-[#16a34a] text-white">{{ $num }}</span>
                    {{ $step }}
                </span>
                @if ($num < 3)<i class="bx bx-chevron-right text-[#d4d4d8]"></i>@endif
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach ([
                ['bx-buildings',     $lockedCollege?->name ?? '—', 'College'],
                ['bx-sitemap',       $lockedDept?->name    ?? '—', 'Department'],
                ['bx-network-chart', $lockedProgram?->name ?? '—', 'Program'],
            ] as [$icon, $value, $hint])
                <div class="space-y-1.5">
                    <label class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a]">
                        <i class="bx {{ $icon }} text-[#16a34a]"></i>
                        {{ $hint }}
                    </label>
                    <div class="relative">
                        <div class="w-full rounded-[14px] border border-[#e4e4e7] bg-[#f4f4f5] px-3 py-2 pr-9 text-[13px] text-[#a1a1aa] cursor-not-allowed truncate">
                            {{ $value }}
                        </div>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="bx bx-lock-alt text-[#d4d4d8] text-base"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Step breadcrumb --}}
        <div class="flex items-center gap-2 mb-4 text-xs text-[#a1a1aa] select-none">
            <span @class([
                'flex items-center gap-1.5 font-semibold transition-colors',
                'text-[#16a34a]' => $collegeId,
                'text-[#a1a1aa]' => !$collegeId,
            ])>
                <span @class([
                    'inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold transition-colors',
                    'bg-[#16a34a] text-white'    => $collegeId,
                    'bg-[#e4e4e7] text-[#71717a]' => !$collegeId,
                ])>1</span>
                College
            </span>
            <i class="bx bx-chevron-right text-[#d4d4d8]"></i>
            <span @class([
                'flex items-center gap-1.5 font-semibold transition-colors',
                'text-[#16a34a]' => $departmentId,
                'text-[#a1a1aa]' => !$departmentId,
            ])>
                <span @class([
                    'inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold transition-colors',
                    'bg-[#16a34a] text-white'    => $departmentId,
                    'bg-[#e4e4e7] text-[#71717a]' => !$departmentId,
                ])>2</span>
                Department
            </span>
            <i class="bx bx-chevron-right text-[#d4d4d8]"></i>
            <span @class([
                'flex items-center gap-1.5 font-semibold transition-colors',
                'text-[#16a34a]' => $programId,
                'text-[#a1a1aa]' => !$programId,
            ])>
                <span @class([
                    'inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold transition-colors',
                    'bg-[#16a34a] text-white'    => $programId,
                    'bg-[#e4e4e7] text-[#71717a]' => !$programId,
                ])>3</span>
                Program
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            {{-- College --}}
            <div class="space-y-1.5">
                <label class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a]">
                    <i class="bx bx-buildings text-[#16a34a]"></i> College
                </label>
                <div class="relative">
                    <select wire:model.live="collegeId"
                        class="w-full appearance-none rounded-[14px] border border-[#d4d4d8] bg-white
                               px-3 py-2 pr-9 text-[13px] text-[#09090b]
                               hover:border-[#a1a1aa]
                               focus:border-[#16a34a] focus:outline-none transition-colors"
                        style="box-shadow:none;"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.15)'"
                        onblur="this.style.boxShadow='none'">
                        <option value="">Select college…</option>
                        @foreach ($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#a1a1aa]">
                        <span wire:loading.remove wire:target="collegeId">
                            <i class="bx bx-chevron-down text-base"></i>
                        </span>
                        <span wire:loading wire:target="collegeId">
                            <i class="bx bx-loader-alt bx-spin text-[#16a34a] text-base"></i>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Department --}}
            <div class="space-y-1.5">
                <label class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a]">
                    <i class="bx bx-sitemap text-[#16a34a]"></i> Department
                </label>
                <div class="relative">
                    <select wire:model.live="departmentId" @disabled(!$collegeId)
                        @class([
                            'w-full appearance-none rounded-[14px] border px-3 py-2 pr-9 text-[13px] transition-colors focus:outline-none',
                            'border-[#d4d4d8] bg-white text-[#09090b] hover:border-[#a1a1aa] focus:border-[#16a34a]' => $collegeId,
                            'border-[#e4e4e7] bg-[#f4f4f5] text-[#a1a1aa] cursor-not-allowed' => !$collegeId,
                        ])
                        style="box-shadow:none;"
                        onfocus="if(this.disabled) return; this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.15)'"
                        onblur="this.style.boxShadow='none'">
                        <option value="">{{ !$collegeId ? '← Select college first' : 'Select department…' }}</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#a1a1aa]">
                        <span wire:loading.remove wire:target="collegeId,departmentId">
                            <i @class(['bx text-base', 'bx-chevron-down' => $collegeId, 'bx-lock-alt text-[#d4d4d8]' => !$collegeId])></i>
                        </span>
                        <span wire:loading wire:target="collegeId,departmentId">
                            <i class="bx bx-loader-alt bx-spin text-[#16a34a] text-base"></i>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Program --}}
            <div class="space-y-1.5">
                <label class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.1em] text-[#71717a]">
                    <i class="bx bx-network-chart text-[#16a34a]"></i> Program
                </label>
                <div class="relative">
                    <select wire:model.live="programId" @disabled(!$departmentId)
                        @class([
                            'w-full appearance-none rounded-[14px] border px-3 py-2 pr-9 text-[13px] transition-colors focus:outline-none',
                            'border-[#d4d4d8] bg-white text-[#09090b] hover:border-[#a1a1aa] focus:border-[#16a34a]' => $departmentId,
                            'border-[#e4e4e7] bg-[#f4f4f5] text-[#a1a1aa] cursor-not-allowed' => !$departmentId,
                        ])
                        style="box-shadow:none;"
                        onfocus="if(this.disabled) return; this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.15)'"
                        onblur="this.style.boxShadow='none'">
                        <option value="">{{ !$departmentId ? '← Select department first' : 'Select program…' }}</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#a1a1aa]">
                        <span wire:loading.remove wire:target="departmentId,programId">
                            <i @class(['bx text-base', 'bx-chevron-down' => $departmentId, 'bx-lock-alt text-[#d4d4d8]' => !$departmentId])></i>
                        </span>
                        <span wire:loading wire:target="departmentId,programId">
                            <i class="bx bx-loader-alt bx-spin text-[#16a34a] text-base"></i>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Selected program chip --}}
        @if ($programId && count($programs))
            @php $selectedProgram = collect($programs)->firstWhere('id', (int)$programId); @endphp
            @if ($selectedProgram)
                <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-[#86efac]
                            bg-[#f0fdf4] px-3 py-1 text-[12px] font-semibold text-[#166534]">
                    <i class="bx bx-check-circle text-[#16a34a] text-sm"></i>
                    {{ $selectedProgram->name }}
                </div>
            @endif
        @endif
    @endif
</div>
