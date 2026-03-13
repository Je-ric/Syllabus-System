{{--
    course-evaluation-partials/table.blade.php
    ────────────────────────────────────────────
    Variables expected (all from CourseEvaluationStep Livewire state):
      $rows          — array  (evaluation row data)
      $inputs        — array  (wire:model targets keyed by week_content_id)
      $courseHasLab  — bool
--}}

<x-wizard.section title="Evaluation Items" icon="table" color="slate">
    <div class="overflow-x-auto -mx-5 px-5">
        <div class="rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm border-collapse">

                {{-- ══ Column group headers ══════════════════════════════════ --}}
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        {{-- CO — rowspan 2 so it spans both header rows --}}
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 border-r border-slate-200 w-24 align-middle">
                            CO
                        </th>

                        {{-- LEC group --}}
                        <th colspan="2"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-emerald-700 bg-emerald-50
                                   {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Lecture (LEC)
                            </div>
                        </th>

                        {{-- LAB group (only when course has LAB) --}}
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

                        {{-- Passing — rowspan 2 --}}
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 w-28 align-middle">
                            Passing
                        </th>
                    </tr>

                    {{-- Sub-headers --}}
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

                {{-- ══ Body rows ════════════════════════════════════════════ --}}
                <tbody class="divide-y divide-slate-100">

                    @foreach ($rows as $rowIndex => $row)
                        @php
                            $isMvgo  = $row['is_mvgo']     ?? false;
                            $isExam  = $row['is_exam']     ?? false;
                            $coAuto  = $row['co_coverage'] ?? '';   // exam CO (read-only)

                            $rowBg = $isExam
                                ? 'bg-amber-50/70 border-t-2 border-amber-200'
                                : ($isMvgo
                                    ? 'bg-violet-50/40 border-t border-violet-100'
                                    : ($rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'));

                            $lecId        = $row['lec']['week_content_id'] ?? null;
                            $lecCo        = $row['lec']['co_code']         ?? null;
                            $lecTaskLabel = $row['lec']['task_label']      ?? '';

                            $labId        = $row['lab']['week_content_id'] ?? null;
                            $labCo        = $row['lab']['co_code']         ?? null;
                            $labTaskLabel = $row['lab']['task_label']      ?? '';

                            // CO to display in the CO column:
                            //   MVGO     → violet MVGO badge
                            //   Exam     → auto-resolved co_coverage badge (amber)
                            //   Regular  → the CO code from the week content (slate badge)
                            //              or a freetext input if no CO is mapped yet
                            $displayCo      = $lecCo ?? $labCo ?? null;
                            $outcomeInputId = $lecId ?? $labId;
                        @endphp

                        <tr class="{{ $rowBg }}">

                            {{-- ── CO column ─────────────────────────────── --}}
                            <td class="px-4 py-3 border-r border-slate-200 align-middle">

                                @if ($isMvgo)
                                    {{-- Week 1 — always MVGO, never user-editable --}}
                                    <x-feedback-status.status-indicator variant="violet" icon="bx bx-star" size="sm">
                                        MVGO
                                    </x-feedback-status.status-indicator>

                                @elseif ($isExam)
                                    {{--
                                        Exam rows: CO is auto-determined from the last
                                        non-exam week before this exam.
                                        Display as a read-only amber badge.
                                        If unresolvable (e.g. very first week is exam) show '—'.
                                    --}}
                                    @if ($coAuto !== '')
                                        <x-feedback-status.status-indicator variant="amber" icon="bx bx-lock-alt" size="sm">
                                            {{ $coAuto }}
                                        </x-feedback-status.status-indicator>
                                    @else
                                        <span class="text-slate-400 text-xs italic">—</span>
                                    @endif

                                @elseif ($displayCo)
                                    {{-- Regular week with a mapped CO --}}
                                    <x-feedback-status.status-indicator variant="slate" size="sm">
                                        {{ $displayCo }}
                                    </x-feedback-status.status-indicator>

                                @elseif ($outcomeInputId)
                                    {{-- No CO mapped yet — allow freetext outcome label --}}
                                    <input type="text"
                                        wire:model.blur="inputs.{{ $outcomeInputId }}.outcome_label"
                                        placeholder="e.g. CO1"
                                        class="w-full text-xs rounded-lg border border-slate-300 bg-white
                                               px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                               focus:ring-emerald-300 focus:outline-none placeholder:text-slate-300" />

                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif

                            </td>

                            {{-- ── LEC Task ──────────────────────────────── --}}
                            @if ($lecId)
                                <td class="px-4 py-3 align-middle
                                           {{ $isExam  ? 'font-semibold text-amber-800'
                                            : ($isMvgo ? 'text-violet-800'
                                            : 'text-slate-700') }}">
                                    <div class="flex items-center gap-2">
                                        @if ($isExam)
                                            <i class="bx bx-clipboard text-amber-500 shrink-0 text-base"></i>
                                        @elseif ($isMvgo)
                                            <i class="bx bx-star text-violet-400 shrink-0 text-base"></i>
                                        @endif
                                        {{ $lecTaskLabel }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.blur="inputs.{{ $lecId }}.weight"
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

                            {{-- ── LAB Task ──────────────────────────────── --}}
                            @if ($courseHasLab)
                                @if ($labId)
                                    <td class="px-4 py-3 align-middle
                                               {{ $isExam  ? 'font-semibold text-blue-800'
                                                : ($isMvgo ? 'text-violet-700'
                                                : 'text-slate-700') }}">
                                        <div class="flex items-center gap-2">
                                            @if ($isExam)
                                                <i class="bx bx-clipboard text-blue-400 shrink-0 text-base"></i>
                                            @elseif ($isMvgo)
                                                <i class="bx bx-star text-violet-400 shrink-0 text-base"></i>
                                            @endif
                                            {{ $labTaskLabel }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle border-r border-slate-200">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number"
                                                wire:model.blur="inputs.{{ $labId }}.weight"
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

                            {{-- ── Passing standard ─────────────────────── --}}
                            <td class="px-4 py-3 text-center align-middle">
                                <x-feedback-status.status-indicator variant="emerald" size="sm">60%</x-feedback-status.status-indicator>
                            </td>

                        </tr>
                    @endforeach

                    {{-- ══ Totals row ═══════════════════════════════════════ --}}
                    @php
                        $lecTotal = 0;
                        $labTotal = 0;
                        foreach ($rows as $row) {
                            if (isset($row['lec']['week_content_id']))
                                $lecTotal += (int) ($inputs[$row['lec']['week_content_id']]['weight'] ?? 0);
                            if ($courseHasLab && isset($row['lab']['week_content_id']))
                                $labTotal += (int) ($inputs[$row['lab']['week_content_id']]['weight'] ?? 0);
                        }

                        // Parse the performance standard to its numeric target.
                        // e.g. '67%' → 67,  '100%' → 100,  null → 100 (LEC-only fallback)
                        $lecStdNum = $lecPerformanceStd !== null
                            ? (int) filter_var($lecPerformanceStd, FILTER_SANITIZE_NUMBER_INT)
                            : ($courseHasLab ? 67 : 100);
                        $labStdNum = $labPerformanceStd !== null
                            ? (int) filter_var($labPerformanceStd, FILTER_SANITIZE_NUMBER_INT)
                            : 33;

                        $lecOk   = $lecTotal === $lecStdNum;
                        $lecWarn = $lecTotal > 0 && ! $lecOk;
                        $labOk   = $labTotal === $labStdNum;
                        $labWarn = $courseHasLab && $labTotal > 0 && ! $labOk;
                    @endphp

                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-semibold text-sm">
                        <td class="px-4 py-3 text-slate-600 border-r border-slate-200 text-xs uppercase tracking-wide">
                            Total
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs"></td>
                        <td class="px-4 py-3 {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <span class="{{ $lecWarn ? 'text-rose-600' : ($lecOk ? 'text-emerald-700' : 'text-slate-500') }}">
                                {{ $lecTotal }}%
                            </span>
                            @if ($lecWarn)
                                <span class="text-xs text-rose-500 block font-normal">
                                    Expected {{ $lecStdNum }}%
                                </span>
                            @elseif ($lecOk)
                                <span class="text-xs text-emerald-600 block font-normal">✓ Matches standard</span>
                            @endif
                        </td>
                        @if ($courseHasLab)
                            <td class="px-4 py-3 text-slate-500 text-xs"></td>
                            <td class="px-4 py-3 border-r border-slate-200">
                                <span class="{{ $labWarn ? 'text-rose-600' : ($labOk ? 'text-blue-700' : 'text-slate-500') }}">
                                    {{ $labTotal }}%
                                </span>
                                @if ($labWarn)
                                    <span class="text-xs text-rose-500 block font-normal">
                                        Expected {{ $labStdNum }}%
                                    </span>
                                @elseif ($labOk)
                                    <span class="text-xs text-blue-600 block font-normal">✓ Matches standard</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs text-slate-500 font-normal">Min. per semester</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</x-wizard.section>
