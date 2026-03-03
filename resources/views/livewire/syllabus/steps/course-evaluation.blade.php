<div>

    {{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
    <x-wizard.step-header
        title="Course Evaluation"
        description="Set the weight (%) for each assessment task. Only weeks with an assessment task entered in Weekly Coverage appear here. Exam weeks are auto-detected from locked calendar events.">
        @if (count($rows) > 0)
            <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="shrink-0 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold
                       rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700
                       hover:bg-emerald-100 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                <span wire:loading.remove wire:target="save">
                    <i class="bx bx-save"></i> Save Evaluation
                </span>
                <span wire:loading wire:target="save">
                    <i class="bx bx-loader-alt bx-spin"></i> Saving…
                </span>
            </button>
        @endif
    </x-wizard.step-header>

    {{-- ══ Empty state ══════════════════════════════════════════════════════════ --}}
    @if (count($rows) === 0)
        <x-empty-state
            icon="bx-notepad"
            title="No assessment tasks yet"
            message="Enter assessment tasks in the Weekly Coverage step first. Exam weeks are auto-detected from calendar events." />

    @else

        {{--
            ══ Evaluation table ════════════════════════════════════════════════

            Table structure (course with LEC + LAB):

            ┌──────┬──────────────────────────┬──────────────────────────┬─────────┐
            │      │     LECTURE (LEC)         │     LABORATORY (LAB)     │ Passing │
            │  CO  ├─────────────────┬─────────┼─────────────────┬────────┤         │
            │      │  Assessment Task│ Weight  │  Assessment Task│ Weight │         │
            ├──────┼─────────────────┼─────────┼─────────────────┼────────┼─────────┤
            │ MVGO │ Activity 1      │ [ 4 ]%  │ Activity 1      │ [ 4 ]% │         │
            │ CO1  │ 1st Term Exam   │ [ 10 ]% │ 1st Prac Exam   │ [ 10]% │  60%    │
            ├──────┼─────────────────┼─────────┼─────────────────┼────────┼─────────┤
            │Total │                 │  sum%   │                 │  sum%  │ Min 60% │
            └──────┴─────────────────┴─────────┴─────────────────┴────────┴─────────┘

            When LEC has no task for a row → LEC columns show greyed "—" with disabled input.
            When LAB has no task for a row → LAB columns show greyed "—" with disabled input.
            This handles the real-world case where LEC and LAB have different task counts.

            LEC-only courses: LAB column group hidden entirely.

            Column count:
              1  CO
              2  LEC Assessment Task
              3  LEC Weight
             [4  LAB Assessment Task]  ← hidden if no LAB
             [5  LAB Weight]           ← hidden if no LAB
              6  Passing Standard
        --}}

        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
            <table class="w-full text-sm border-collapse">

                <thead>
                    <tr class="border-b border-slate-200">

                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 bg-slate-50 border-r border-slate-200 w-24 align-middle">
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
                                text-slate-500 bg-slate-50 w-28 align-middle">
                            Passing
                        </th>
                    </tr>

                    <tr class="border-b-2 border-slate-300 bg-slate-100">
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-45">
                            Assessment Task
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32
                                {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            Weight (%)
                        </th>

                        @if ($courseHasLab)
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-45">
                                Assessment Task
                            </th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32 border-r border-slate-200">
                                Weight (%)
                            </th>
                        @endif

                    </tr>
                </thead>

                {{-- ── Body ──────────────────────────────────────────────────── --}}
                <tbody class="divide-y divide-slate-100">

                    @foreach ($rows as $rowIndex => $row)
                        @php
                            // Exam rows get a warm amber tint; alternating stripes otherwise
                            $rowBg = $row['is_exam']
                                ? 'bg-amber-50/70 border-t-2 border-amber-200'
                                : ($rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/50');

                            // Grab IDs and labels from the pre-built row arrays
                            $lecId     = $row['lec']['week_content_id'] ?? null;
                            $lecCo     = $row['lec']['co_code'] ?? null;
                            $lecLabel  = $row['lec']['task_label'] ?? '';

                            $labId     = $row['lab']['week_content_id'] ?? null;
                            $labCo     = $row['lab']['co_code'] ?? null;
                            $labLabel  = $row['lab']['task_label'] ?? '';

                            // CO to display: prefer LEC CO; fall back to LAB CO; fall back to outcome_label input
                            $displayCo = $lecCo ?? $labCo ?? null;

                            // The input ID we bind outcome_label to (LEC preferred; LAB as fallback)
                            $outcomeInputId = $lecId ?? $labId;
                        @endphp

                        <tr class="{{ $rowBg }}">

                            {{-- ── CO column ────────────────────────────────── --}}
                            <td class="px-4 py-3 border-r border-slate-200 align-middle">
                                @if ($displayCo)
                                    {{-- CO already mapped from Weekly Coverage --}}
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold
                                                font-mono bg-slate-100 text-slate-700">
                                        {{ $displayCo }}
                                    </span>
                                @elseif ($outcomeInputId)
                                    {{-- No CO mapped — let faculty type a label (e.g. MVGO) --}}
                                    <input type="text"
                                        wire:model.lazy="inputs.{{ $outcomeInputId }}.outcome_label"
                                        placeholder="e.g. MVGO"
                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                            px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                            focus:ring-emerald-300 focus:outline-none
                                            placeholder:text-slate-300" />
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>

                            {{-- ── LEC Task column ──────────────────────────── --}}
                            @if ($lecId)
                                {{-- LEC has a task → show label + active weight input --}}
                                <td class="px-4 py-3 align-middle
                                           {{ $row['is_exam'] ? 'font-semibold text-amber-800' : 'text-slate-700' }}">
                                    <div class="flex items-center gap-2">
                                        @if ($row['is_exam'])
                                            <i class="bx bx-clipboard text-amber-500 shrink-0 text-base"></i>
                                        @endif
                                        <span>{{ $lecLabel }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.lazy="inputs.{{ $lecId }}.weight"
                                            min="0" max="100" step="1"
                                            placeholder="0"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-300 bg-white
                                                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                                   focus:ring-emerald-300 focus:outline-none
                                                   placeholder:text-slate-300" />
                                        <span class="text-xs text-slate-400">%</span>
                                    </div>
                                </td>
                            @else
                                {{-- LEC has NO task this week → greyed-out disabled placeholder --}}
                                <td class="px-4 py-3 align-middle bg-slate-50/70">
                                    <span class="text-slate-300 text-xs italic">No LEC task</span>
                                </td>
                                <td class="px-4 py-3 align-middle bg-slate-50/70
                                           {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            disabled
                                            placeholder="—"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-200
                                                   bg-slate-100 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                        <span class="text-xs text-slate-300">%</span>
                                    </div>
                                </td>
                            @endif

                            {{-- ── LAB columns (only for LEC+LAB courses) ──── --}}
                            @if ($courseHasLab)
                                @if ($labId)
                                    {{-- LAB has a task → show label + active weight input --}}
                                    <td class="px-4 py-3 align-middle
                                               {{ $row['is_exam'] ? 'font-semibold text-blue-800' : 'text-slate-700' }}">
                                        <div class="flex items-center gap-2">
                                            @if ($row['is_exam'])
                                                <i class="bx bx-clipboard text-blue-400 shrink-0 text-base"></i>
                                            @endif
                                            <span>{{ $labLabel }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle border-r border-slate-200">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number"
                                                wire:model.lazy="inputs.{{ $labId }}.weight"
                                                min="0" max="100" step="1"
                                                placeholder="0"
                                                class="w-20 text-sm text-right rounded-lg border border-slate-300 bg-white
                                                       px-2 py-1.5 focus:border-blue-400 focus:ring-1
                                                       focus:ring-blue-300 focus:outline-none
                                                       placeholder:text-slate-300" />
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>
                                    </td>
                                @else
                                    {{-- LAB has NO task this week → greyed-out disabled placeholder --}}
                                    <td class="px-4 py-3 align-middle bg-slate-50/70">
                                        <span class="text-slate-300 text-xs italic">No LAB task</span>
                                    </td>
                                    <td class="px-4 py-3 align-middle bg-slate-50/70 border-r border-slate-200">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number"
                                                disabled
                                                placeholder="—"
                                                class="w-20 text-sm text-right rounded-lg border border-slate-200
                                                       bg-slate-100 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                            <span class="text-xs text-slate-300">%</span>
                                        </div>
                                    </td>
                                @endif
                            @endif

                            {{-- ── Passing standard ─────────────────────────── --}}
                            {{-- Shown only on exam rows because that is where a term ends --}}
                            <td class="px-4 py-3 text-center align-middle">
                                @if ($row['is_exam'])
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold
                                                 bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        60%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    {{-- ── Totals row ────────────────────────────────────────── --}}
                    @php
                        $lecTotal = 0;
                        $labTotal = 0;
                        foreach ($rows as $row) {
                            if (isset($row['lec']['week_content_id'])) {
                                $lecTotal += (int) ($inputs[$row['lec']['week_content_id']]['weight'] ?? 0);
                            }
                            if ($courseHasLab && isset($row['lab']['week_content_id'])) {
                                $labTotal += (int) ($inputs[$row['lab']['week_content_id']]['weight'] ?? 0);
                            }
                        }
                        // Warn if weights don't match the expected 67/33 split
                        $lecWarning = $courseHasLab && $lecTotal > 0 && $lecTotal !== 67;
                        $labWarning = $courseHasLab && $labTotal > 0 && $labTotal !== 33;
                    @endphp
                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-semibold text-sm">

                        <td class="px-4 py-3 text-slate-600 border-r border-slate-200 text-xs uppercase tracking-wide">
                            Total
                        </td>

                        {{-- LEC totals --}}
                        <td class="px-4 py-3 text-slate-500 text-xs"></td>
                        <td class="px-4 py-3 {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <span class="text-sm {{ $lecWarning ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ $lecTotal }}%
                            </span>
                            @if ($lecWarning)
                                <span class="text-xs text-rose-500 block font-normal">Expected 67%</span>
                            @endif
                        </td>

                        @if ($courseHasLab)
                            {{-- LAB totals --}}
                            <td class="px-4 py-3 text-slate-500 text-xs"></td>
                            <td class="px-4 py-3 border-r border-slate-200">
                                <span class="text-sm {{ $labWarning ? 'text-rose-600' : 'text-blue-700' }}">
                                    {{ $labTotal }}%
                                </span>
                                @if ($labWarning)
                                    <span class="text-xs text-rose-500 block font-normal">Expected 33%</span>
                                @endif
                            </td>
                        @endif

                        <td class="px-4 py-3 text-center text-slate-500 text-xs font-normal">
                            Min. 60%
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        {{-- Helper notes --}}
        <div class="mt-4 space-y-1.5">
            <p class="text-xs text-slate-400">
                <i class="bx bx-info-circle"></i>
                Rows come from <strong>Weekly Coverage</strong>. Only weeks with an assessment task appear here.
                Greyed columns mean that component has no task entered for that week.
            </p>
            @if ($courseHasLab)
                <p class="text-xs text-slate-400">
                    <i class="bx bx-info-circle"></i>
                    Standard split: <strong class="text-emerald-600">LEC 67%</strong> +
                    <strong class="text-blue-600">LAB 33%</strong> = 100%.
                    Minimum passing per term: <strong>60%</strong>.
                </p>
            @endif
        </div>

    @endif

</div>
