<div>

    {{-- ══ Header ═══════════════════════════════════════════════════════════════ --}}
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Course Evaluation</h3>
            <p class="mt-0.5 text-sm text-slate-500">
                Set the weight (%) for each assessment task. Activities come from the
                Weekly Coverage step; exams are auto-detected from locked exam weeks.
            </p>
        </div>

        {{-- Save button --}}
        @if (count($rows) > 0)
            <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="shrink-0 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold
                       rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700
                       hover:bg-emerald-100 disabled:opacity-60 disabled:cursor-not-allowed
                       transition-colors">
                <span wire:loading.remove wire:target="save">
                    <i class="bx bx-save"></i> Save Evaluation
                </span>
                <span wire:loading wire:target="save">
                    <i class="bx bx-loader-alt bx-spin"></i> Saving…
                </span>
            </button>
        @endif
    </div>

    {{-- ══ Empty state ════════════════════════════════════════════════════════════ --}}
    @if (count($rows) === 0)
        <x-empty-state
            icon="bx-notepad"
            title="No assessment tasks yet"
            message="Fill in assessment tasks in the Weekly Coverage step first. Exam weeks are auto-detected." />

    @else

        {{--
            ══ Evaluation table ═══════════════════════════════════════════════════
            Layout mirrors the document:

            ┌─────────┬─────────────────────────────┬───────────────────────────┬──────────┐
            │         │    LECTURE (67%)             │    LABORATORY (33%)       │ Passing  │
            │  CO     ├──────────────┬───────────────┼──────────────┬────────────┤ Standard │
            │         │ Task         │ Weight (%)    │ Task         │ Weight (%) │          │
            ├─────────┼──────────────┼───────────────┼──────────────┼────────────┼──────────┤
            │ MVGO    │ Activity 1   │ [  4  ]       │ Activity 1   │ [  4  ]   │          │
            │ CO1     │ Activity 2   │ [  4  ]       │ Activity 2   │ [  4  ]   │  60%     │
            │ CO1     │ 1st Term Exam│ [ 10  ]       │ 1st Prac Exam│ [ 10  ]  │          │
            └─────────┴──────────────┴───────────────┴──────────────┴────────────┴──────────┘

            If course has no LAB, the LAB columns are hidden.
        --}}

        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
            <table class="w-full text-sm border-collapse">

                {{-- Table header --}}
                <thead>
                    {{-- Row 1: Group headers (LECTURE / LABORATORY) --}}
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 border-r border-slate-200 w-24">
                            CO
                        </th>

                        {{-- LECTURE columns group --}}
                        <th colspan="3"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-emerald-700 bg-emerald-50 border-r border-slate-200">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Lecture (LEC)
                            </div>
                        </th>

                        {{-- LABORATORY columns group — only if course has LAB --}}
                        @if ($courseHasLab)
                            <th colspan="3"
                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                       text-blue-700 bg-blue-50 border-r border-slate-200">
                                <div class="flex items-center justify-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Laboratory (LAB)
                                </div>
                            </th>
                        @endif

                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 w-28">
                            Passing
                        </th>
                    </tr>

                    {{-- Row 2: Column headers --}}
                    <tr class="bg-slate-100 border-b-2 border-slate-300">
                        {{-- CO --}}
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 border-r border-slate-200 w-24">
                            CO
                        </th>
                        {{-- LEC: Task label + Weight input --}}
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-45">
                            Assessment Task
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32">
                            Kind
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32
                                    {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            Weight (%)
                        </th>
                        {{-- LAB columns only if course has LAB --}}
                        @if ($courseHasLab)
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-45">
                                Assessment Task
                            </th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32">
                                Kind
                            </th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-32 border-r border-slate-200">
                                Weight (%)
                            </th>
                        @endif
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-600 w-28">
                            Passing
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    {{--
                        We track whether to show the passing standard (60%) on exam rows
                        because that's when a "term" ends in the document.
                    --}}

                    @foreach ($rows as $rowIndex => $row)

                        @php
                            // Is this the last row before an exam, or an exam row itself?
                            // Show "60%" passing standard on exam rows (term boundary).
                            $showPassing = $row['is_exam'];

                            // Determine row background based on whether it's an exam
                            $rowBg = $row['is_exam']
                                ? 'bg-amber-50/60'
                                : ($rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/50');

                            // Get LEC data for this row
                            $lecId        = $row['lec']['week_content_id'] ?? null;
                            $lecCoCode    = $row['lec']['co_code'] ?? null;
                            $lecTaskLabel = $row['lec']['task_label'] ?? '';

                            // Get LAB data for this row
                            $labId        = $row['lab']['week_content_id'] ?? null;
                            $labCoCode    = $row['lab']['co_code'] ?? null;
                            $labTaskLabel = $row['lab']['task_label'] ?? '';

                            // The CO code to display — use LEC CO, or LAB CO, or the outcome_label input
                            // (for tasks not mapped to a specific CO, like MVGO tasks)
                            $displayCo = $lecCoCode ?? $labCoCode ?? null;
                        @endphp

                        <tr class="{{ $rowBg }} {{ $row['is_exam'] ? 'border-t-2 border-amber-200' : '' }}">

                            {{-- CO column --}}
                            <td class="px-4 py-3 border-r border-slate-200 align-middle">
                                @if ($displayCo)
                                    {{-- CO is already mapped from Weekly Coverage --}}
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold
                                                 bg-slate-100 text-slate-700 font-mono">
                                        {{ $displayCo }}
                                    </span>
                                @else
                                    {{--
                                        No CO mapped — let faculty type a label (e.g. "MVGO").
                                        We bind to inputs[lec_id].outcome_label or inputs[lab_id].outcome_label.
                                    --}}
                                    @php $outcomeInputId = $lecId ?? $labId; @endphp
                                    @if ($outcomeInputId)
                                        <input type="text"
                                            wire:model.lazy="inputs.{{ $outcomeInputId }}.outcome_label"
                                            placeholder="e.g. MVGO"
                                            class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                                   focus:ring-emerald-300 focus:outline-none
                                                   placeholder:text-slate-300" />
                                    @endif
                                @endif
                            </td>

                            {{-- LEC: Task label + Weight input --}}
                            @if ($lecId)
                                <td class="px-4 py-3 align-middle {{ $row['is_exam'] ? 'font-semibold text-amber-800' : 'text-slate-700' }}">
                                    <div class="flex items-center gap-2">
                                        @if ($row['is_exam'])
                                            <i class="bx bx-notepad text-amber-500 shrink-0"></i>
                                        @endif
                                        <span class="text-sm">{{ $lecTaskLabel }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    @if ($row['is_exam'])
                                        <select disabled
                                            class="w-full text-xs rounded-lg border border-slate-300 bg-slate-100
                                                   px-2 py-1.5 text-slate-500">
                                            <option selected>Exam (Auto)</option>
                                        </select>
                                    @else
                                        <select wire:model.lazy="inputs.{{ $lecId }}.kind"
                                            class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                                   focus:ring-emerald-300 focus:outline-none">
                                            <option value="activity">Activity</option>
                                            <option value="quiz">Quiz</option>
                                        </select>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle border-r border-slate-200">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.lazy="inputs.{{ $lecId }}.weight"
                                            min="0"
                                            max="100"
                                            step="1"
                                            placeholder="0"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-300 bg-white
                                                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                                   focus:ring-emerald-300 focus:outline-none
                                                   placeholder:text-slate-300" />
                                        <span class="text-xs text-slate-400">%</span>
                                    </div>
                                </td>
                            @else
                                <td colspan="3" class="px-4 py-3 border-r border-slate-200 text-xs text-slate-400 italic">
                                    No LEC task
                                </td>
                            @endif

                            {{-- LAB: Task label + Weight input (only if course has LAB) --}}
                            @if ($courseHasLab)
                                @if ($labId)
                                    <td class="px-4 py-3 align-middle {{ $row['is_exam'] ? 'font-semibold text-blue-800' : 'text-slate-700' }}">
                                        <div class="flex items-center gap-2">
                                            @if ($row['is_exam'])
                                                <i class="bx bx-notepad text-blue-400 shrink-0"></i>
                                            @endif
                                            <span class="text-sm">{{ $labTaskLabel }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if ($row['is_exam'])
                                            <select disabled
                                                class="w-full text-xs rounded-lg border border-slate-300 bg-slate-100
                                                       px-2 py-1.5 text-slate-500">
                                                <option selected>Exam (Auto)</option>
                                            </select>
                                        @else
                                            <select wire:model.lazy="inputs.{{ $labId }}.kind"
                                                class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                                       px-2 py-1.5 focus:border-blue-400 focus:ring-1
                                                       focus:ring-blue-300 focus:outline-none">
                                                <option value="activity">Activity</option>
                                                <option value="quiz">Quiz</option>
                                            </select>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle border-r border-slate-200">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number"
                                                wire:model.lazy="inputs.{{ $labId }}.weight"
                                                min="0"
                                                max="100"
                                                step="1"
                                                placeholder="0"
                                                class="w-20 text-sm text-right rounded-lg border border-slate-300 bg-white
                                                       px-2 py-1.5 focus:border-blue-400 focus:ring-1
                                                       focus:ring-blue-300 focus:outline-none
                                                       placeholder:text-slate-300" />
                                            <span class="text-xs text-slate-400">%</span>
                                        </div>
                                    </td>
                                @else
                                    <td colspan="3" class="px-4 py-3 border-r border-slate-200 text-xs text-slate-400 italic">
                                        No LAB task
                                    </td>
                                @endif
                            @endif

                            {{-- Passing standard (shown on exam rows = term boundaries) --}}
                            <td class="px-4 py-3 text-center align-middle">
                                @if ($showPassing)
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold
                                                 bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        60%
                                    </span>
                                @endif
                            </td>
                        </tr>

                    @endforeach

                    {{-- Totals row --}}
                    @php
                        // Calculate total weights entered so far
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
                        // Determine if totals look valid (warning if not 67/33 split or 100 total)
                        $lecWarning = $lecTotal > 0 && $courseHasLab && $lecTotal !== 67;
                        $labWarning = $labTotal > 0 && $courseHasLab && $labTotal !== 33;
                    @endphp

                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-semibold text-sm">
                        <td class="px-4 py-3 text-slate-600 border-r border-slate-200">Total</td>

                        {{-- LEC total --}}
                        <td class="px-4 py-3 text-slate-600"></td>
                        <td class="px-4 py-3 text-slate-600"></td>
                        <td class="px-4 py-3 border-r border-slate-200">
                            <span class="{{ $lecWarning ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ $lecTotal }}%
                            </span>
                            @if ($lecWarning)
                                <span class="text-xs text-rose-500 block">Expected 67%</span>
                            @endif
                        </td>

                        {{-- LAB total --}}
                        @if ($courseHasLab)
                            <td class="px-4 py-3 text-slate-600"></td>
                            <td class="px-4 py-3 text-slate-600"></td>
                            <td class="px-4 py-3 border-r border-slate-200">
                                <span class="{{ $labWarning ? 'text-rose-600' : 'text-blue-700' }}">
                                    {{ $labTotal }}%
                                </span>
                                @if ($labWarning)
                                    <span class="text-xs text-rose-500 block">Expected 33%</span>
                                @endif
                            </td>
                        @endif

                        <td class="px-4 py-3 text-center text-slate-500 text-xs">
                            Min. 60%
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        {{-- Helper note below table --}}
        <div class="mt-4 space-y-2">
            <p class="text-xs text-slate-400">
                <i class="bx bx-info-circle"></i>
                Rows come from the <strong>Weekly Coverage</strong> step — only weeks with an assessment task or exam appear here.
                Exam rows are auto-detected from calendar events.
            </p>
            @if ($courseHasLab)
                <p class="text-xs text-slate-400">
                    <i class="bx bx-info-circle"></i>
                    Typical split: <strong class="text-emerald-600">LEC 67%</strong> + <strong class="text-blue-600">LAB 33%</strong> = 100%.
                    Minimum passing grade per term: <strong>60%</strong>.
                </p>
            @endif
        </div>

    @endif

</div>
