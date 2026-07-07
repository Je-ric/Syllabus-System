{{--
    course-evaluation-partials/table.blade.php
--}}

<div class="overflow-x-auto -mx-5 px-5">
    <div class="rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm border-collapse">

            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th rowspan="2"
                        class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-[0.14em]
                               text-slate-500 border-r border-slate-200 w-24 align-middle">
                        CO
                    </th>

                    <th colspan="3"
                        class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-[0.14em]
                               text-emerald-700 bg-emerald-50
                               {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Lecture (LEC)
                        </div>
                    </th>

                    @if ($courseHasLab)
                        <th colspan="3"
                            class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-[0.14em]
                                   text-blue-700 bg-blue-50 border-r border-slate-200">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                Laboratory (LAB)
                            </div>
                        </th>
                    @endif

                    <th rowspan="2"
                        class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-[0.14em]
                               text-slate-500 w-28 align-middle">
                        Passing Mark
                    </th>
                </tr>

                <tr class="bg-slate-50 border-b-2 border-slate-200">
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 min-w-40">Assessment Task</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-28">Kind</th>
                    <th
                        class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-28 {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                        Weight (%)</th>
                    @if ($courseHasLab)
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 min-w-40">Assessment Task
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-28">Kind</th>
                        <th
                            class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-28 border-r border-slate-200">
                            Weight (%)</th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach ($rows as $rowIndex => $row)
                    @php
                        $isMvgo = $row['is_mvgo'] ?? false;
                        $isExam = $row['is_exam'] ?? false;
                        $coAuto = $row['co_coverage'] ?? '';

                        $rowBg = $isExam
                            ? 'bg-amber-100/60'
                            : ($isMvgo
                                ? 'bg-emerald-50/40'
                                : ($rowIndex % 2 === 0
                                    ? 'bg-white'
                                    : 'bg-slate-50/50'));

                        $lecId = $row['lec']['week_content_id'] ?? null;
                        $lecCo = $row['lec']['co_code'] ?? null;
                        $lecTaskLabel = $row['lec']['task_label'] ?? '';

                        $labId = $row['lab']['week_content_id'] ?? null;
                        $labCo = $row['lab']['co_code'] ?? null;
                        $labTaskLabel = $row['lab']['task_label'] ?? '';

                        $displayCo = $lecCo ?? ($labCo ?? null);
                        $outcomeInputId = $lecId ?? $labId;
                    @endphp

                    <tr class="{{ $rowBg }}">

                        {{-- CO column --}}
                        <td class="px-4 py-3 border-r border-slate-200 align-middle">
                            @if ($isMvgo)
                                <x-feedback-status.status-indicator variant="violet" icon="bx bx-medal"
                                    size="sm">MVGO</x-feedback-status.status-indicator>
                            @elseif ($isExam)
                                @if ($coAuto !== '')
                                    <x-feedback-status.status-indicator variant="rose" icon="bx bx-lock-alt"
                                        size="sm">{{ $coAuto }}</x-feedback-status.status-indicator>
                                @else
                                    <span class="text-xs text-slate-300 italic">—</span>
                                @endif
                            @elseif ($displayCo)
                                <x-feedback-status.status-indicator variant="emerald" icon="bx bx-check"
                                    size="sm">{{ $displayCo }}</x-feedback-status.status-indicator>
                            @elseif ($outcomeInputId)
                                <input type="text" wire:model.blur="inputs.{{ $outcomeInputId }}.outcome_label"
                                    placeholder="e.g. CO1"
                                    class="w-full text-xs rounded-lg border border-slate-200 bg-white
                   px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                   focus:ring-emerald-100 focus:outline-none placeholder:text-slate-300" />
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- LEC columns --}}
                        @if ($lecId)
                            <td class="px-4 py-3 align-middle text-slate-700 {{ $isExam ? 'font-semibold' : '' }}">
                                <div class="flex items-center gap-2">
                                    @if ($isExam)
                                        <i class="bx bx-clipboard text-slate-400 shrink-0 text-base"></i>
                                    @elseif ($isMvgo)
                                        <i class="bx bx-star text-emerald-500 shrink-0 text-base"></i>
                                    @endif
                                    {{ \Illuminate\Support\Str::limit(strip_tags($lecTaskLabel ?? ''), 60) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                @if ($isExam)
                                    <x-feedback-status.status-indicator variant="rose"
                                        icon="bx bx-clipboard">Exam</x-feedback-status.status-indicator>
                                @elseif ($isMvgo)
                                    <x-feedback-status.status-indicator variant="violet"
                                        icon="bx bx-medal">Activity</x-feedback-status.status-indicator>
                                @else
                                    <x-form.select wire:model="inputs.{{ $lecId }}.kind">
                                        <option value="activity">Activity</option>
                                        <option value="quiz">Quiz</option>
                                    </x-form.select>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                <div class="flex items-center gap-1.5">
                                    <input type="number"
                                        wire:model.blur="inputs.{{ $lecId }}.weight"
                                        x-model="lec[{{ $lecId }}]"
                                        min="0" max="100" step="1" placeholder="0"
                                        class="w-20 text-sm text-right rounded-lg border border-slate-200 bg-white
                                               px-2 py-1.5 focus:border-emerald-400 focus:ring-1
                                               focus:ring-emerald-100 focus:outline-none placeholder:text-slate-300" />
                                    <span class="text-xs text-slate-400">%</span>
                                </div>
                            </td>
                        @else
                            <td class="px-4 py-3 bg-slate-50/60 align-middle"><span
                                    class="text-xs text-slate-300 italic">No LEC task</span></td>
                            <td class="px-4 py-3 bg-slate-50/60 align-middle"><span
                                    class="text-xs text-slate-300">—</span></td>
                            <td
                                class="px-4 py-3 bg-slate-50/60 align-middle {{ $courseHasLab ? 'border-r border-slate-200' : '' }}">
                                <input type="number" disabled placeholder="—"
                                    class="w-20 text-sm text-right rounded-lg border border-slate-200
                                           bg-slate-50 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                            </td>
                        @endif

                        {{-- LAB columns --}}
                        @if ($courseHasLab)
                            @if ($labId)
                                <td class="px-4 py-3 align-middle text-slate-700 {{ $isExam ? 'font-semibold' : '' }}">
                                    <div class="flex items-center gap-2">
                                        @if ($isExam)
                                            <i class="bx bx-clipboard text-slate-400 shrink-0 text-base"></i>
                                        @elseif ($isMvgo)
                                            <i class="bx bx-star text-emerald-500 shrink-0 text-base"></i>
                                        @endif
                                        {{ $labTaskLabel }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                @if ($isExam)
                                    <x-feedback-status.status-indicator variant="rose"
                                        icon="bx bx-clipboard">Exam</x-feedback-status.status-indicator>
                                @elseif ($isMvgo)
                                    <x-feedback-status.status-indicator variant="violet"
                                        icon="bx bx-medal">Activity</x-feedback-status.status-indicator>
                                @else
                                    <x-form.select wire:model="inputs.{{ $labId }}.kind">
                                        <option value="activity">Activity</option>
                                        <option value="quiz">Quiz</option>
                                    </x-form.select>
                                @endif
                                </td>
                                <td class="px-4 py-3 align-middle border-r border-slate-200">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.blur="inputs.{{ $labId }}.weight"
                                            x-model="lab[{{ $labId }}]"
                                            min="0" max="100" step="1" placeholder="0"
                                            class="w-20 text-sm text-right rounded-lg border border-slate-200 bg-white
                                                   px-2 py-1.5 focus:border-blue-400 focus:ring-1
                                                   focus:ring-blue-100 focus:outline-none placeholder:text-slate-300" />
                                        <span class="text-xs text-slate-400">%</span>
                                    </div>
                                </td>
                            @else
                                <td class="px-4 py-3 bg-slate-50/60 align-middle"><span
                                        class="text-xs text-slate-300 italic">No LAB task</span></td>
                                <td class="px-4 py-3 bg-slate-50/60 align-middle"><span
                                        class="text-xs text-slate-300">—</span></td>
                                <td class="px-4 py-3 bg-slate-50/60 align-middle border-r border-slate-200">
                                    <input type="number" disabled placeholder="—"
                                        class="w-20 text-sm text-right rounded-lg border border-slate-200
                                               bg-slate-50 text-slate-400 px-2 py-1.5 cursor-not-allowed" />
                                </td>
                            @endif
                        @endif

                        {{-- Passing mark --}}
                        <td class="px-4 py-3 text-center align-middle">
                            <x-feedback-status.status-indicator variant="slate"
                                size="sm">{{ $lecPassingMark }}%</x-feedback-status.status-indicator>
                        </td>

                    </tr>
                @endforeach

                {{-- Totals row --}}
                <tr class="bg-slate-50 border-t-2 border-slate-200 font-semibold text-sm">
                    <td class="px-4 py-3 border-r border-slate-200 align-middle">
                        <x-feedback-status.status-indicator variant="slate">Total</x-feedback-status.status-indicator>
                    </td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3 {{ $courseHasLab ? 'border-r border-slate-200' : '' }} align-middle">
                        <template x-if="lecTotal === lecStd && lecTotal > 0">
                            <x-feedback-status.status-indicator variant="emerald" icon="bx bx-check-circle">
                                <span x-text="lecTotal + ' / {{ $lecStdNum }}%'"></span>
                            </x-feedback-status.status-indicator>
                        </template>
                        <template x-if="lecTotal > 0 && lecTotal !== lecStd">
                            <div>
                                <x-feedback-status.status-indicator variant="rose" icon="bx bx-error-circle">
                                    <span x-text="lecTotal + ' / {{ $lecStdNum }}%'"></span>
                                </x-feedback-status.status-indicator>
                                <span class="text-xs text-rose-500 block font-normal mt-0.5"
                                    x-text="'Need ' + (lecStd - lecTotal) + '% more'"></span>
                            </div>
                        </template>
                        <template x-if="lecTotal === 0">
                            <span class="text-xs text-slate-400">0 / {{ $lecStdNum }}%</span>
                        </template>
                    </td>
                    @if ($courseHasLab)
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 border-r border-slate-200 align-middle">
                            <template x-if="labTotal === labStd && labTotal > 0">
                                <x-feedback-status.status-indicator variant="emerald" icon="bx bx-check-circle">
                                    <span x-text="labTotal + ' / {{ $labStdNum }}%'"></span>
                                </x-feedback-status.status-indicator>
                            </template>
                            <template x-if="labTotal > 0 && labTotal !== labStd">
                                <div>
                                    <x-feedback-status.status-indicator variant="rose" icon="bx bx-error-circle">
                                        <span x-text="labTotal + ' / {{ $labStdNum }}%'"></span>
                                    </x-feedback-status.status-indicator>
                                    <span class="text-xs text-rose-500 block font-normal mt-0.5"
                                        x-text="'Need ' + (labStd - labTotal) + '% more'"></span>
                                </div>
                            </template>
                            <template x-if="labTotal === 0">
                                <span class="text-xs text-slate-400">0 / {{ $labStdNum }}%</span>
                            </template>
                        </td>
                    @endif
                    <td class="px-4 py-3 text-center align-middle">
                        <x-feedback-status.status-indicator variant="slate">Min:
                            {{ $lecPassingMark }}%</x-feedback-status.status-indicator>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
