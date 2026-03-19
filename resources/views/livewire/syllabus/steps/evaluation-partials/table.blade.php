{{--
    course-evaluation-partials/table.blade.php
    ────────────────────────────────────────────
    Variables from CourseEvaluationStep:
      $rows              — array
      $inputs            — array  (wire:model targets keyed by week_content_id)
      $courseHasLab      — bool
      $lecPerformanceStd — string|null  (raw DB value e.g. "67.00")
      $labPerformanceStd — string|null
      $lecStdNum         — int   (weight total target: 67 for LEC+LAB, 100 for LEC-only)
      $labStdNum         — int   (weight total target: always 33)
      $lecTotal          — int   (running total from saved weights)
      $labTotal          — int
      $lecPassingMark    — int   (passing threshold from performance_standard, e.g. 60 or 75)
      $labPassingMark    — int

    Weight totals / targets are structural (67/33/100).
    Passing mark comes from performance_standard in Course Components.
--}}

<x-wizard.section title="Evaluation Items" icon="table" color="slate">
    <div class="overflow-x-auto -mx-5 px-5">
        <div class="rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm border-collapse">

                {{-- ══ Column group headers ══════════════════════════════════ --}}
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 border-r border-slate-200 w-24 align-middle">
                            CO
                        </th>

                        {{-- LEC group --}}
                        <th colspan="3"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-emerald-700 bg-emerald-50
                                   {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Lecture (LEC)
                            </div>
                        </th>

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

                        <th rowspan="2"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 w-28 align-middle">
                            Passing Mark
                        </th>
                    </tr>

                    {{-- Sub-headers — now 3 cols per component: Task | Kind | Weight --}}
                    <tr class="bg-slate-100 border-b-2 border-slate-300">
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-[160px]">
                            Assessment Task
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-28">
                            Kind
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-28
                                   {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            Weight (%)
                        </th>
                        @if ($courseHasLab)
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 min-w-[160px]">
                                Assessment Task
                            </th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-28">
                                Kind
                            </th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-600 w-28 border-r border-slate-200">
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
                            $coAuto  = $row['co_coverage'] ?? '';

                            $rowBg = $isExam
                                ? 'bg-amber-50/70 border-t-2 border-amber-200'
                                : ($isMvgo
                                    ? 'bg-emerald-50/40 border-t border-emerald-100'
                                    : ($rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'));

                            $lecId        = $row['lec']['week_content_id'] ?? null;
                            $lecCo        = $row['lec']['co_code']         ?? null;
                            $lecTaskLabel = $row['lec']['task_label']      ?? '';

                            $labId        = $row['lab']['week_content_id'] ?? null;
                            $labCo        = $row['lab']['co_code']         ?? null;
                            $labTaskLabel = $row['lab']['task_label']      ?? '';

                            $displayCo      = $lecCo ?? $labCo ?? null;
                            $outcomeInputId = $lecId ?? $labId;
                        @endphp

                        <tr class="{{ $rowBg }}">

                            {{-- ── CO column ─────────────────────────────── --}}
                            <td class="px-4 py-3 border-r border-slate-200 align-middle">
                                @if ($isMvgo)
                                    <x-feedback-status.status-indicator variant="emerald" icon="bx bx-star" size="sm">
                                        MVGO
                                    </x-feedback-status.status-indicator>
                                @elseif ($isExam)
                                    @if ($coAuto !== '')
                                        <x-feedback-status.status-indicator variant="amber" icon="bx bx-lock-alt" size="sm">
                                            {{ $coAuto }}
                                        </x-feedback-status.status-indicator>
                                    @else
                                        <span class="text-slate-400 text-xs italic">—</span>
                                    @endif
                                @elseif ($displayCo)
                                    <x-feedback-status.status-indicator variant="slate" size="sm">
                                        {{ $displayCo }}
                                    </x-feedback-status.status-indicator>
                                @elseif ($outcomeInputId)
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

                            {{-- ── LEC Task + Kind + Weight ──────────────── --}}
                            @if ($lecId)
                                <td class="px-4 py-3 align-middle
                                           {{ $isExam  ? 'font-semibold text-amber-800'
                                            : ($isMvgo ? 'text-emerald-800' : 'text-slate-700') }}">
                                    <div class="flex items-center gap-2">
                                        @if ($isExam)
                                            <i class="bx bx-clipboard text-amber-500 shrink-0 text-base"></i>
                                        @elseif ($isMvgo)
                                            <i class="bx bx-star text-emerald-400 shrink-0 text-base"></i>
                                        @endif
                                        {{ $lecTaskLabel }}
                                    </div>
                                </td>

                                {{-- Kind selector: disabled for exam and MVGO rows --}}
                                <td class="px-4 py-3 align-middle">
                                    @if ($isExam || $isMvgo)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                                     {{ $isExam ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ $isExam ? 'Exam' : 'Activity' }}
                                        </span>
                                    @else
                                        <select
                                            wire:model.live="inputs.{{ $lecId }}.kind"
                                            class="text-xs rounded-lg border border-slate-300 bg-white
                                                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                                   focus:ring-emerald-300 focus:outline-none">
                                            <option value="activity">Activity</option>
                                            <option value="quiz">Quiz</option>
                                        </select>
                                    @endif
                                </td>

                                <td class="px-4 py-3 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.live.debounce.250ms="inputs.{{ $lecId }}.weight"
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
                                <td class="px-4 py-3 bg-slate-50/70 align-middle">
                                    <span class="text-slate-200 text-xs">—</span>
                                </td>
                                <td class="px-4 py-3 bg-slate-50/70 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                    <input type="number" disabled placeholder="—"
                                        class="w-20 text-sm text-right rounded-lg border border-slate-200
                                               bg-slate-100 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                </td>
                            @endif

                            {{-- ── LAB Task + Kind + Weight ──────────────── --}}
                            @if ($courseHasLab)
                                @if ($labId)
                                    <td class="px-4 py-3 align-middle
                                               {{ $isExam  ? 'font-semibold text-blue-800'
                                                : ($isMvgo ? 'text-emerald-700' : 'text-slate-700') }}">
                                        <div class="flex items-center gap-2">
                                            @if ($isExam)
                                                <i class="bx bx-clipboard text-blue-400 shrink-0 text-base"></i>
                                            @elseif ($isMvgo)
                                                <i class="bx bx-star text-emerald-400 shrink-0 text-base"></i>
                                            @endif
                                            {{ $labTaskLabel }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-middle">
                                        @if ($isExam || $isMvgo)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                                         {{ $isExam ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $isExam ? 'Exam' : 'Activity' }}
                                            </span>
                                        @else
                                            <select
                                                wire:model.live="inputs.{{ $labId }}.kind"
                                                class="text-xs rounded-lg border border-slate-300 bg-white
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
                                                wire:model.live.debounce.250ms="inputs.{{ $labId }}.weight"
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
                                    <td class="px-4 py-3 bg-slate-50/70 align-middle">
                                        <span class="text-slate-200 text-xs">—</span>
                                    </td>
                                    <td class="px-4 py-3 bg-slate-50/70 align-middle border-r border-slate-200">
                                        <input type="number" disabled placeholder="—"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-200
                                                   bg-slate-100 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                    </td>
                                @endif
                            @endif

                            {{-- ── Passing mark (from performance_standard) ── --}}
                            <td class="px-4 py-3 text-center align-middle">
                                @php
                                    // Show LEC passing mark as the row standard.
                                    // For LEC+LAB courses both components use the same mark per syllabus.
                                    $rowPassingMark = $lecPassingMark ?? 60;
                                @endphp
                                <x-feedback-status.status-indicator variant="emerald" size="sm">
                                    {{ $rowPassingMark }}%
                                </x-feedback-status.status-indicator>
                            </td>

                        </tr>
                    @endforeach

                    {{-- ══ Totals row ═══════════════════════════════════════ --}}
                    {{--
                        All values pre-computed in PHP (CourseEvaluationService::loadRows).
                        Blade just displays — no arithmetic here.
                        lecTotal / labTotal  = running sum of saved weights
                        lecStdNum / labStdNum = target (from performance_standard column)
                    --}}
                    @php
                        $lecOk   = $lecTotal === $lecStdNum && $lecTotal > 0;
                        $lecWarn = $lecTotal > 0 && $lecTotal !== $lecStdNum;
                        $labOk   = $labTotal === $labStdNum && $labTotal > 0;
                        $labWarn = $courseHasLab && $labTotal > 0 && $labTotal !== $labStdNum;
                    @endphp

                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-semibold text-sm">
                        <td class="px-4 py-3 text-slate-600 border-r border-slate-200 text-xs uppercase tracking-wide">
                            Total
                        </td>
                        {{-- LEC task column (empty) --}}
                        <td class="px-4 py-3"></td>
                        {{-- LEC kind column (empty) --}}
                        <td class="px-4 py-3 {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                            <span class="{{ $lecWarn ? 'text-rose-600' : ($lecOk ? 'text-emerald-700' : 'text-slate-500') }}">
                                {{ $lecTotal }} / {{ $lecStdNum }}%
                            </span>
                            @if ($lecWarn)
                                <span class="text-xs text-rose-500 block font-normal">
                                    Need {{ $lecStdNum - $lecTotal }}% more
                                </span>
                            @elseif ($lecOk)
                                <span class="text-xs text-emerald-600 block font-normal">✓ Complete</span>
                            @endif
                        </td>
                        @if ($courseHasLab)
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 border-r border-slate-200">
                                <span class="{{ $labWarn ? 'text-rose-600' : ($labOk ? 'text-blue-700' : 'text-slate-500') }}">
                                    {{ $labTotal }} / {{ $labStdNum }}%
                                </span>
                                @if ($labWarn)
                                    <span class="text-xs text-rose-500 block font-normal">
                                        Need {{ $labStdNum - $labTotal }}% more
                                    </span>
                                @elseif ($labOk)
                                    <span class="text-xs text-blue-600 block font-normal">✓ Complete</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs text-slate-500 font-normal">
                                Min: {{ $lecPassingMark ?? 60 }}%
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</x-wizard.section>
