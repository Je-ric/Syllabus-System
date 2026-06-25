{{--
    course-evaluation-partials/table.blade.php
--}}

    <div class="overflow-x-auto -mx-5 px-5">
        <div class="rounded-xl border border-[#dedee2] overflow-hidden" style="box-shadow: 0 1px 4px rgba(0,0,0,0.06);">
            <table class="w-full text-sm border-collapse">

                <thead>
                    <tr class="bg-[#F5F5F6] border-b border-[#dedee2]">
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide
                                   text-[#797980] border-r border-[#dedee2] w-24 align-middle">
                            CO
                        </th>

                        <th colspan="3"
                            class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide
                                   text-[#15803d] bg-[#f0fdf4]
                                   {{ $courseHasLab ? 'border-r border-[#dedee2]' : '' }}">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#16a34a]"></span>
                                Lecture (LEC)
                            </div>
                        </th>

                        @if ($courseHasLab)
                            <th colspan="3"
                                class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide
                                       text-[#1d4ed8] bg-[#eff6ff] border-r border-[#dedee2]">
                                <div class="flex items-center justify-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#2563eb]"></span>
                                    Laboratory (LAB)
                                </div>
                            </th>
                        @endif

                        <th rowspan="2"
                            class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide
                                   text-[#797980] w-28 align-middle">
                            Passing Mark
                        </th>
                    </tr>

                    <tr class="bg-[#F5F5F6] border-b-2 border-[#dedee2]">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#58585e] min-w-40">Assessment Task</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#58585e] w-28">Kind</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#58585e] w-28 {{ $courseHasLab ? 'border-r border-[#dedee2]' : '' }}">Weight (%)</th>
                        @if ($courseHasLab)
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#58585e] min-w-40">Assessment Task</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#58585e] w-28">Kind</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-[#58585e] w-28 border-r border-[#dedee2]">Weight (%)</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#dedee2]">
                    @foreach ($rows as $rowIndex => $row)
                        @php
                            $isMvgo  = $row['is_mvgo']     ?? false;
                            $isExam  = $row['is_exam']     ?? false;
                            $coAuto  = $row['co_coverage'] ?? '';

                            $rowBg = $isExam
                                ? 'bg-[#fdf6e8]'
                                : ($isMvgo
                                    ? 'bg-[#f0fdf4]/50'
                                    : ($rowIndex % 2 === 0 ? 'bg-white' : 'bg-[#F5F5F6]/40'));

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

                            {{-- CO column --}}
                            <td class="px-4 py-3 border-r border-[#dedee2] align-middle">
                                @if ($isMvgo)
                                    <x-feedback-status.status-indicator variant="slate" icon="bx bx-star" size="sm">MVGO</x-feedback-status.status-indicator>
                                @elseif ($isExam)
                                    @if ($coAuto !== '')
                                        <x-feedback-status.status-indicator variant="slate" icon="bx bx-lock-alt" size="sm">{{ $coAuto }}</x-feedback-status.status-indicator>
                                    @else
                                        <span class="text-xs text-[#9d9ea4] italic">—</span>
                                    @endif
                                @elseif ($displayCo)
                                    <x-feedback-status.status-indicator variant="slate" size="sm">{{ $displayCo }}</x-feedback-status.status-indicator>
                                @elseif ($outcomeInputId)
                                    <input type="text"
                                        wire:model.blur="inputs.{{ $outcomeInputId }}.outcome_label"
                                        placeholder="e.g. CO1"
                                        class="w-full text-xs rounded-lg border border-[#dedee2] bg-white
                                               px-2 py-1.5 focus:border-[#16a34a] focus:ring-1
                                               focus:ring-[#bbf7d0] focus:outline-none placeholder:text-[#c6c6cc]" />
                                @else
                                    <span class="text-xs text-[#c6c6cc]">—</span>
                                @endif
                            </td>

                            {{-- LEC columns --}}
                            @if ($lecId)
                                <td class="px-4 py-3 align-middle text-[#36363b] {{ $isExam ? 'font-semibold' : '' }}">
                                    <div class="flex items-center gap-2">
                                        @if ($isExam)
                                            <i class="bx bx-clipboard text-[#797980] shrink-0 text-base"></i>
                                        @elseif ($isMvgo)
                                            <i class="bx bx-star text-[#16a34a] shrink-0 text-base"></i>
                                        @endif
                                        {{ \Illuminate\Support\Str::limit(strip_tags($lecTaskLabel ?? ''), 60) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    @if ($isExam || $isMvgo)
                                        <x-feedback-status.status-indicator variant="slate">{{ $isExam ? 'Exam' : 'Activity' }}</x-feedback-status.status-indicator>
                                    @else
                                        <select wire:model.live="inputs.{{ $lecId }}.kind"
                                            class="text-xs rounded-lg border border-[#dedee2] bg-white
                                                   px-2 py-1.5 focus:border-[#16a34a] focus:ring-1
                                                   focus:ring-[#bbf7d0] focus:outline-none">
                                            <option value="activity">Activity</option>
                                            <option value="quiz">Quiz</option>
                                        </select>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle {{ $courseHasLab ? 'border-r border-[#dedee2]' : '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                            wire:model.live.debounce.250ms="inputs.{{ $lecId }}.weight"
                                            min="0" max="100" step="1" placeholder="0"
                                            class="w-20 text-sm text-right rounded-lg border border-[#dedee2] bg-white
                                                   px-2 py-1.5 focus:border-[#16a34a] focus:ring-1
                                                   focus:ring-[#bbf7d0] focus:outline-none placeholder:text-[#c6c6cc]" />
                                        <span class="text-xs text-[#9d9ea4]">%</span>
                                    </div>
                                </td>
                            @else
                                <td class="px-4 py-3 bg-[#F5F5F6]/60 align-middle"><span class="text-xs text-[#c6c6cc] italic">No LEC task</span></td>
                                <td class="px-4 py-3 bg-[#F5F5F6]/60 align-middle"><span class="text-xs text-[#c6c6cc]">—</span></td>
                                <td class="px-4 py-3 bg-[#F5F5F6]/60 align-middle {{ $courseHasLab ? 'border-r border-[#dedee2]' : '' }}">
                                    <input type="number" disabled placeholder="—"
                                        class="w-20 text-sm text-right rounded-lg border border-[#dedee2]
                                               bg-[#F5F5F6] text-[#9d9ea4] px-2 py-1.5 cursor-not-allowed" />
                                </td>
                            @endif

                            {{-- LAB columns --}}
                            @if ($courseHasLab)
                                @if ($labId)
                                    <td class="px-4 py-3 align-middle text-[#36363b] {{ $isExam ? 'font-semibold' : '' }}">
                                        <div class="flex items-center gap-2">
                                            @if ($isExam)
                                                <i class="bx bx-clipboard text-[#797980] shrink-0 text-base"></i>
                                            @elseif ($isMvgo)
                                                <i class="bx bx-star text-[#16a34a] shrink-0 text-base"></i>
                                            @endif
                                            {{ $labTaskLabel }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if ($isExam || $isMvgo)
                                            <x-feedback-status.status-indicator variant="slate">{{ $isExam ? 'Exam' : 'Activity' }}</x-feedback-status.status-indicator>
                                        @else
                                            <select wire:model.live="inputs.{{ $labId }}.kind"
                                                class="text-xs rounded-lg border border-[#dedee2] bg-white
                                                       px-2 py-1.5 focus:border-[#16a34a] focus:ring-1
                                                       focus:ring-[#bbf7d0] focus:outline-none">
                                                <option value="activity">Activity</option>
                                                <option value="quiz">Quiz</option>
                                            </select>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle border-r border-[#dedee2]">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number"
                                                wire:model.live.debounce.250ms="inputs.{{ $labId }}.weight"
                                                min="0" max="100" step="1" placeholder="0"
                                                class="w-20 text-sm text-right rounded-lg border border-[#dedee2] bg-white
                                                       px-2 py-1.5 focus:border-[#16a34a] focus:ring-1
                                                       focus:ring-[#bbf7d0] focus:outline-none placeholder:text-[#c6c6cc]" />
                                            <span class="text-xs text-[#9d9ea4]">%</span>
                                        </div>
                                    </td>
                                @else
                                    <td class="px-4 py-3 bg-[#F5F5F6]/60 align-middle"><span class="text-xs text-[#c6c6cc] italic">No LAB task</span></td>
                                    <td class="px-4 py-3 bg-[#F5F5F6]/60 align-middle"><span class="text-xs text-[#c6c6cc]">—</span></td>
                                    <td class="px-4 py-3 bg-[#F5F5F6]/60 align-middle border-r border-[#dedee2]">
                                        <input type="number" disabled placeholder="—"
                                            class="w-20 text-sm text-right rounded-lg border border-[#dedee2]
                                                   bg-[#F5F5F6] text-[#9d9ea4] px-2 py-1.5 cursor-not-allowed" />
                                    </td>
                                @endif
                            @endif

                            {{-- Passing mark --}}
                            <td class="px-4 py-3 text-center align-middle">
                                @php $rowPassingMark = $lecPassingMark ?? 60; @endphp
                                <x-feedback-status.status-indicator variant="slate" size="sm">{{ $rowPassingMark }}%</x-feedback-status.status-indicator>
                            </td>

                        </tr>
                    @endforeach

                    {{-- Totals row --}}
                    @php
                        $lecOk   = $lecTotal === $lecStdNum && $lecTotal > 0;
                        $lecWarn = $lecTotal > 0 && $lecTotal !== $lecStdNum;
                        $labOk   = $labTotal === $labStdNum && $labTotal > 0;
                        $labWarn = $courseHasLab && $labTotal > 0 && $labTotal !== $labStdNum;
                    @endphp

                    <tr class="bg-[#F5F5F6] border-t-2 border-[#dedee2] font-semibold text-sm">
                        <td class="px-4 py-3 border-r border-[#dedee2] align-middle">
                            <x-feedback-status.status-indicator variant="slate">Total</x-feedback-status.status-indicator>
                        </td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 {{ $courseHasLab ? 'border-r border-[#dedee2]' : '' }} align-middle">
                            @if ($lecOk)
                                <x-feedback-status.status-indicator variant="emerald" icon="bx bx-check-circle">{{ $lecTotal }} / {{ $lecStdNum }}%</x-feedback-status.status-indicator>
                            @elseif ($lecWarn)
                                <x-feedback-status.status-indicator variant="rose" icon="bx bx-error-circle">{{ $lecTotal }} / {{ $lecStdNum }}%</x-feedback-status.status-indicator>
                                <span class="text-xs text-rose-500 block font-normal mt-0.5">Need {{ $lecStdNum - $lecTotal }}% more</span>
                            @else
                                <span class="text-xs text-[#9d9ea4]">{{ $lecTotal }} / {{ $lecStdNum }}%</span>
                            @endif
                        </td>
                        @if ($courseHasLab)
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 border-r border-[#dedee2] align-middle">
                                @if ($labOk)
                                    <x-feedback-status.status-indicator variant="emerald" icon="bx bx-check-circle">{{ $labTotal }} / {{ $labStdNum }}%</x-feedback-status.status-indicator>
                                @elseif ($labWarn)
                                    <x-feedback-status.status-indicator variant="rose" icon="bx bx-error-circle">{{ $labTotal }} / {{ $labStdNum }}%</x-feedback-status.status-indicator>
                                    <span class="text-xs text-rose-500 block font-normal mt-0.5">Need {{ $labStdNum - $labTotal }}% more</span>
                                @else
                                    <span class="text-xs text-[#9d9ea4]">{{ $labTotal }} / {{ $labStdNum }}%</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-3 text-center align-middle">
                            <x-feedback-status.status-indicator variant="slate">Min: {{ $lecPassingMark ?? 60 }}%</x-feedback-status.status-indicator>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>