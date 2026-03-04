<div>

    {{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
    <x-wizard.step-header
        title="Course Evaluation"
        icon="notepad"
        description="Set the weight (%) for each assessment task. Only weeks with an assessment task appear here. Exam weeks are auto-detected from locked calendar events.">

        @if (count($rows) > 0)
            <x-wizard.btn variant="sm-success"
                wire:click="save"
                wire:target="save"
                loading="Saving…">
                <i class="bx bx-save"></i> Save Evaluation
            </x-wizard.btn>
        @endif
    </x-wizard.step-header>

    {{-- ══ Empty state ════════════════════════════════════════════════════════ --}}
    @if (count($rows) === 0)
        <x-wizard.empty
            icon="notepad"
            title="No assessment tasks yet"
            message="Fill in assessment tasks in the Weekly Coverage step first. Exam weeks are auto-detected from calendar events." />

    @else

        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
            <table class="w-full text-sm border-collapse">
                <thead>
                    {{-- Row 1: Group headers --}}
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 border-r border-slate-200 w-24 align-middle">
                            CO
                        </th>
                        <th colspan="2"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-emerald-700 bg-emerald-50 {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Lecture (LEC)
                            </div>
                        </th>
                        @if ($courseHasLab)
                            <th colspan="2"
                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                       text-blue-700 bg-blue-50 border-r border-slate-200">
                                <div class="flex items-center justify-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Laboratory (LAB)
                                </div>
                            </th>
                        @endif
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 w-28 align-middle">
                            Passing
                        </th>
                    </tr>

                    {{-- Row 2: Column headers --}}
                    <tr class="bg-slate-100 border-b-2 border-slate-300">
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-[180px]">
                            Assessment Task
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32
                                   {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            Weight (%)
                        </th>
                        @if ($courseHasLab)
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-[180px]">
                                Assessment Task
                            </th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32 border-r border-slate-200">
                                Weight (%)
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($rows as $rowIndex => $row)
                        @php
                            $rowBg = $row['is_exam']
                                ? 'bg-amber-50/70 border-t-2 border-amber-200'
                                : ($rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/50');

                            $lecId        = $row['lec']['week_content_id'] ?? null;
                            $lecCo        = $row['lec']['co_code'] ?? null;
                            $lecTaskLabel = $row['lec']['task_label'] ?? '';

                            $labId        = $row['lab']['week_content_id'] ?? null;
                            $labCo        = $row['lab']['co_code'] ?? null;
                            $labTaskLabel = $row['lab']['task_label'] ?? '';

                            $displayCo      = $lecCo ?? $labCo ?? null;
                            $outcomeInputId = $lecId ?? $labId;
                        @endphp

                        <tr class="{{ $rowBg }}">

                            {{-- CO column --}}
                            <td class="px-4 py-3 border-r border-slate-200 align-middle">
                                @if ($displayCo)
                                    <x-wizard.badge variant="slate">{{ $displayCo }}</x-wizard.badge>
                                @elseif ($outcomeInputId)
                                    <input type="text"
                                        wire:model.lazy="inputs.{{ $outcomeInputId }}.outcome_label"
                                        placeholder="e.g. MVGO"
                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                               px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                               focus:ring-emerald-300 focus:outline-none placeholder:text-slate-300" />
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>

                            {{-- LEC Task --}}
                            @if ($lecId)
                                <td class="px-4 py-3 align-middle {{ $row['is_exam'] ? 'font-semibold text-amber-800' : 'text-slate-700' }}">
                                    <div class="flex items-center gap-2">
                                        @if ($row['is_exam'])
                                            <i class="bx bx-clipboard text-amber-500 shrink-0 text-base"></i>
                                        @endif
                                        {{ $lecTaskLabel }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.lazy="inputs.{{ $lecId }}.weight"
                                            min="0" max="100" step="1" placeholder="0"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-300 bg-white
                                                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                                   focus:ring-emerald-300 focus:outline-none placeholder:text-slate-300" />
                                        <span class="text-xs text-slate-400">%</span>
                                    </div>
                                </td>
                            @else
                                <td class="px-4 py-3 bg-slate-50/70 align-middle">
                                    <span class="text-slate-300 text-xs italic">No LEC task</span>
                                </td>
                                <td class="px-4 py-3 bg-slate-50/70 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number" disabled placeholder="—"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-200
                                                   bg-slate-100 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                        <span class="text-xs text-slate-300">%</span>
                                    </div>
                                </td>
                            @endif

                            {{-- LAB Task --}}
                            @if ($courseHasLab)
                                @if ($labId)
                                    <td class="px-4 py-3 align-middle {{ $row['is_exam'] ? 'font-semibold text-blue-800' : 'text-slate-700' }}">
                                        <div class="flex items-center gap-2">
                                            @if ($row['is_exam'])
                                                <i class="bx bx-clipboard text-blue-400 shrink-0 text-base"></i>
                                            @endif
                                            {{ $labTaskLabel }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle border-r border-slate-200">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number"
                                                wire:model.lazy="inputs.{{ $labId }}.weight"
                                                min="0" max="100" step="1" placeholder="0"
                                                class="w-20 text-sm text-right rounded-lg border border-slate-300 bg-white
                                                       px-2 py-1.5 focus:border-blue-400 focus:ring-1
                                                       focus:ring-blue-300 focus:outline-none placeholder:text-slate-300" />
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>
                                    </td>
                                @else
                                    <td class="px-4 py-3 bg-slate-50/70 align-middle">
                                        <span class="text-slate-300 text-xs italic">No LAB task</span>
                                    </td>
                                    <td class="px-4 py-3 bg-slate-50/70 align-middle border-r border-slate-200">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" disabled placeholder="—"
                                                class="w-20 text-sm text-right rounded-lg border border-slate-200
                                                       bg-slate-100 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                            <span class="text-xs text-slate-300">%</span>
                                        </div>
                                    </td>
                                @endif
                            @endif

                            {{-- Passing standard --}}
                            <td class="px-4 py-3 text-center align-middle">
                                @if ($row['is_exam'])
                                    <x-wizard.badge variant="emerald">60%</x-wizard.badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    {{-- Totals row --}}
                    @php
                        $lecTotal = 0; $labTotal = 0;
                        foreach ($rows as $row) {
                            if (isset($row['lec']['week_content_id']))
                                $lecTotal += (int) ($inputs[$row['lec']['week_content_id']]['weight'] ?? 0);
                            if ($courseHasLab && isset($row['lab']['week_content_id']))
                                $labTotal += (int) ($inputs[$row['lab']['week_content_id']]['weight'] ?? 0);
                        }
                        $lecWarn = $courseHasLab && $lecTotal > 0 && $lecTotal !== 67;
                        $labWarn = $courseHasLab && $labTotal > 0 && $labTotal !== 33;
                    @endphp
                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-semibold text-sm">
                        <td class="px-4 py-3 text-slate-600 border-r border-slate-200 text-xs uppercase tracking-wide">Total</td>
                        <td class="px-4 py-3 text-slate-500 text-xs"></td>
                        <td class="px-4 py-3 {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <span class="{{ $lecWarn ? 'text-rose-600' : 'text-emerald-700' }}">{{ $lecTotal }}%</span>
                            @if ($lecWarn) <span class="text-xs text-rose-500 block font-normal">Expected 67%</span> @endif
                        </td>
                        @if ($courseHasLab)
                            <td class="px-4 py-3 text-slate-500 text-xs"></td>
                            <td class="px-4 py-3 border-r border-slate-200">
                                <span class="{{ $labWarn ? 'text-rose-600' : 'text-blue-700' }}">{{ $labTotal }}%</span>
                                @if ($labWarn) <span class="text-xs text-rose-500 block font-normal">Expected 33%</span> @endif
                            </td>
                        @endif
                        <td class="px-4 py-3 text-center text-slate-500 text-xs font-normal">Min. 60%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 space-y-1.5">
            <p class="text-xs text-slate-400 flex items-center gap-1">
                <i class="bx bx-info-circle"></i>
                Rows come from <strong>Weekly Coverage</strong>. Only weeks with an assessment task appear here.
                Greyed columns mean that component has no task for that week.
            </p>
            @if ($courseHasLab)
                <p class="text-xs text-slate-400 flex items-center gap-1">
                    <i class="bx bx-info-circle"></i>
                    Standard split: <strong class="text-emerald-600">LEC 67%</strong> + <strong class="text-blue-600">LAB 33%</strong> = 100%.
                    Minimum passing per term: <strong>60%</strong>.
                </p>
            @endif
        </div>

    @endif
</div>
