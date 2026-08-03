{{--
    Partial: weekly-partials/week-edit-modal.blade.php
    Single modal. Field tabs on top, one Quill editor below.
    Fixes: bullet→number Quill bug, font inheritance, list rendering in preview.
--}}
{{-- Confirm dialog for reset-week — rendered at z-[60] so it appears above the modal (z-50) --}}
<div class="[&>div]:z-60!">
    @include('livewire.programs.partials.confirm-modal', ['confirmNs' => 'week'])
</div>

<div x-data="weekEditModal()" x-on:open-week-modal.window="open($event.detail)" x-on:week-modal-saved.window="close()"
    class="relative z-50">

    {{-- Backdrop --}}
    <div x-show="isOpen" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="close()"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] z-50">
    </div>

    {{-- Modal shell --}}
    <div x-show="isOpen" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="week-modal-title"
        x-on:keydown.escape.window="close()">

        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-black/[0.06] w-full max-w-7xl flex flex-col overflow-hidden"
             style="height: min(88vh, 780px);"
             x-on:click.stop>

            {{-- ── Header ──────────────────────────────────────────── --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0"
                 style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%)">

                <div class="flex items-center gap-3 min-w-0">
                    <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-xl text-white"
                         style="background: linear-gradient(135deg, #009639 0%, #16a34a 100%); box-shadow: 0 3px 8px rgba(0,150,57,0.3);">
                        <i class="bx bx-edit-alt text-lg leading-none"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span id="week-modal-title" class="text-sm font-bold text-slate-800"
                                  x-text="'Week ' + weekNo"></span>
                            <span class="text-slate-300">·</span>
                            <span class="text-sm font-medium text-[#009639] truncate"
                                  x-text="activeFieldLabel"></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="weekDates"></p>
                    </div>
                </div>

                <button type="button" x-on:click="close()"
                    class="shrink-0 ml-4 flex items-center justify-center w-8 h-8 rounded-lg
                           text-slate-400 hover:text-slate-600 hover:bg-slate-100
                           transition-colors duration-150">
                    <i class="bx bx-x text-[20px] leading-none"></i>
                </button>

            </div>

            {{-- ── Tab strip ───────────────────────────────────────── --}}
            <div class="px-6 pt-4 pb-0 shrink-0 border-b border-slate-100">

                {{-- MVGO pill (shown instead of CO selector when relevant) --}}
                <div x-show="isMvgo" class="mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                 bg-violet-50 text-violet-700 border border-violet-200
                                 text-xs font-semibold tracking-wide uppercase">
                        <i class="bx bx-star text-[12px]"></i>
                        Mission-Vision-Goals-Objectives week
                    </span>
                </div>

                <div class="flex items-end gap-0 overflow-x-auto scrollbar-none -mb-px">

                    {{-- Rich-text tabs --}}
                    <template x-for="tab in richFields" :key="tab.key">
                        <button type="button"
                            x-on:click="switchField(tab.key)"
                            x-bind:class="activeField === tab.key
                                ? 'border-b-2 border-[#009639] text-[#009639] bg-white'
                                : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="shrink-0 inline-flex items-center gap-1.5
                                   px-4 py-2.5 text-xs font-semibold
                                   transition-all duration-150 whitespace-nowrap rounded-t-lg">

                            <i x-bind:class="{
                                'bx bx-list-ul':           tab.key === 'topic',
                                'bx bx-target-lock':       tab.key === 'learning_outcomes',
                                'bx bx-chalkboard':        tab.key === 'teaching_activities',
                                'bx bx-checkbox-checked':  tab.key === 'assessment_task',
                            }" class="text-[14px] leading-none"></i>

                            <span x-text="tab.label"></span>
                        </button>
                    </template>

                    {{-- Divider --}}
                    <div class="shrink-0 w-px h-5 bg-slate-200 mx-1 self-center"></div>

                    {{-- References & Materials tab --}}
                    <button type="button"
                        x-on:click="switchField('references_materials')"
                        x-bind:class="activeField === 'references_materials'
                            ? 'border-b-2 border-[#009639] text-[#009639] bg-white'
                            : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        class="shrink-0 inline-flex items-center gap-1.5
                               px-4 py-2.5 text-xs font-semibold
                               transition-all duration-150 whitespace-nowrap rounded-t-lg">
                        <i class="bx bx-library text-[14px] leading-none"></i>
                        <span>References & Materials</span>
                    </button>

                </div>
            </div>

            {{-- ── Body (scrollable) ───────────────────────────────── --}}
            <div class="flex-1 overflow-y-auto min-h-0">

                {{-- Rich-text panel --}}
                <template x-if="activeField !== 'references_materials'">
                    <div class="flex flex-col h-full">

                        {{-- CO selector row (always visible for non-MVGO) --}}
                        <div x-show="!isMvgo"
                             class="px-6 pt-4 pb-3 border-b border-green-100 shrink-0"
                             :class="coMissing ? 'bg-amber-50' : 'bg-green-50/60'">
                            <label class="block text-xs font-bold uppercase tracking-widest mb-1.5"
                                   :class="coMissing ? 'text-amber-600' : 'text-greeen-400'">
                                Course Outcome
                                <span x-show="coMissing"
                                      class="text-rose-500 normal-case font-medium">* Required</span>
                            </label>
                            <select x-model="fields.course_outcome_id"
                                @change="if (!coMissing && activeField !== 'references_materials') $nextTick(() => _initQuill())"
                                class="w-full max-w-xl rounded-lg border px-3 py-2 text-sm text-slate-700 transition-colors"
                                :class="coMissing
                                    ? 'border-amber-300 bg-amber-50 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-100'
                                    : 'border-green-200 bg-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100'">
                                <option value="">— Select a Course Outcome —</option>
                                @foreach ($courseOutcomes as $co)
                                    <option value="{{ $co['id'] }}">
                                        {{ $co['co_code'] }} — {{ \Illuminate\Support\Str::limit($co['description'], 80) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Blocker — CO not selected yet --}}
                        <div x-show="coMissing"
                             class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center mb-5">
                                <i class="bx bx-target-lock text-2xl text-amber-600"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-700 mb-2">Select a Course Outcome first</h3>
                            <p class="text-sm text-slate-500 max-w-sm leading-relaxed">
                                Each week must be linked to a Course Outcome. Choose one from the dropdown above,
                                then the editor will open for you to fill in the content.
                            </p>
                            <div class="mt-5 flex items-center gap-2 text-xs text-slate-400">
                                <i class="bx bx-info-circle text-[14px]"></i>
                                <span>All fields become editable once a CO is selected.</span>
                            </div>
                        </div>

                        {{-- Quill editor — shown once CO is selected --}}
                        <div x-show="!coMissing"
                             class="flex-1 flex flex-col px-6 pt-5 pb-4 min-h-0">

                            <div class="flex items-center justify-between mb-3 shrink-0">
                                <h4 class="text-sm font-semibold text-slate-700"
                                    x-text="activeFieldLabel"></h4>
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Rich text
                                </span>
                            </div>

                            {{-- Editor wrapper — flex-1 so it stretches --}}
                            <div id="week-quill-editor"
                                 class="flex-1 min-h-0 overflow-hidden rounded-xl border border-slate-200
                                        shadow-sm ring-0 focus-within:ring-2 focus-within:ring-emerald-100
                                        focus-within:border-emerald-300 transition-shadow
                                        [&_.ql-toolbar]:border-b [&_.ql-toolbar]:border-slate-100
                                        [&_.ql-toolbar]:bg-slate-50 [&_.ql-toolbar]:px-3 [&_.ql-toolbar]:py-2
                                        [&_.ql-toolbar.ql-snow]:rounded-t-xl
                                        [&_.ql-container]:border-0 [&_.ql-container]:font-[Tahoma,sans-serif]
                                        [&_.ql-editor]:min-h-[220px] [&_.ql-editor]:px-4 [&_.ql-editor]:py-3
                                        [&_.ql-editor]:text-[13px] [&_.ql-editor]:leading-relaxed
                                        [&_.ql-editor]:text-slate-700
                                        [&_.ql-editor_ul]:pl-5 [&_.ql-editor_ol]:pl-5
                                        [&_.ql-editor_li]:pl-1">
                            </div>
                        </div>

                    </div>
                </template>

                {{-- References & Materials panel --}}
                <template x-if="activeField === 'references_materials'">
                    <div class="h-full">
                        {{-- Blocker — CO not selected yet --}}
                        <div x-show="coMissing"
                             class="h-full flex flex-col items-center justify-center px-6 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center mb-5">
                                <i class="bx bx-library text-2xl text-amber-600"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-700 mb-2">Select a Course Outcome first</h3>
                            <p class="text-sm text-slate-500 max-w-sm leading-relaxed">
                                References and materials are tied to each week's Course Outcome. Pick one from
                                the dropdown above to start adding resources.
                            </p>
                        </div>

                        {{-- Actual references content — shown once CO is selected --}}
                        <div x-show="!coMissing"
                             class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- References --}}
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">References</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Books, journals, printed sources</p>
                                    </div>
                                    <x-ui.button type="button" variant="sm-success" x-on:click="addRef()">
                                        <i class="bx bx-plus text-[14px]"></i> Add
                                    </x-ui.button>
                                </div>

                                <div class="space-y-2.5">
                                    <template x-for="(ref, rIdx) in fields.references" :key="rIdx">
                                        <div class="flex items-center gap-2.5">
                                            <span class="shrink-0 w-5 h-5 rounded-full bg-slate-100 border border-slate-200
                                                         flex items-center justify-center text-[10px] font-bold text-slate-500"
                                                  x-text="rIdx + 1"></span>
                                            <input type="text" x-model="ref.text"
                                                placeholder="Author (Year). Title. Publisher."
                                                class="flex-1 text-sm rounded-lg border border-slate-200 bg-white px-3 py-2
                                                       text-slate-700 placeholder:text-slate-300
                                                       focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-50
                                                       transition-colors" />
                                            <button type="button" x-on:click="removeRef(rIdx)"
                                                x-bind:class="fields.references.length <= 1 ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                                                class="shrink-0 flex items-center justify-center w-7 h-7
                                                       text-slate-300 hover:text-rose-400 hover:bg-rose-50
                                                       rounded-md transition-colors">
                                                <i class="bx bx-trash text-[14px]"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Online Materials --}}
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Online Materials</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Links, slides, videos, web resources</p>
                                    </div>
                                    <x-ui.button type="button" variant="sm-info" x-on:click="addMat()">
                                        <i class="bx bx-plus text-[14px]"></i> Add
                                    </x-ui.button>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(mat, mIdx) in fields.materials" :key="mIdx">
                                        <div class="flex items-start gap-2.5">
                                            <span class="shrink-0 mt-2 w-5 h-5 rounded-full bg-slate-100 border border-slate-200
                                                         flex items-center justify-center text-[10px] font-bold text-slate-500"
                                                  x-text="mIdx + 1"></span>
                                            <div class="flex-1 space-y-1.5">
                                                <input type="text" x-model="mat.name"
                                                    :placeholder="'Label (e.g. Week ' + weekNo + ' Slides)'"
                                                    class="w-full text-sm rounded-lg border border-slate-200 bg-white px-3 py-2
                                                           text-slate-700 placeholder:text-slate-300
                                                           focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-50
                                                           transition-colors" />
                                                <input type="url" x-model="mat.url"
                                                    placeholder="https://…"
                                                    class="w-full text-sm rounded-lg border border-slate-200 bg-white px-3 py-2
                                                           text-slate-700 placeholder:text-slate-300 font-mono
                                                           focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-50
                                                           transition-colors" />
                                            </div>
                                            <button type="button" x-on:click="removeMat(mIdx)"
                                                x-bind:class="fields.materials.length <= 1 ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                                                class="shrink-0 mt-2 flex items-center justify-center w-7 h-7
                                                       text-slate-300 hover:text-rose-400 hover:bg-rose-50
                                                       rounded-md transition-colors">
                                                <i class="bx bx-trash text-[14px]"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

            </div>

            {{-- ── Footer ──────────────────────────────────────────── --}}
            <div class="flex items-center justify-between gap-3 px-6 py-3.5
                        border-t border-slate-100 bg-slate-50/60 shrink-0 rounded-b-2xl">

                {{-- Danger zone --}}
                <x-ui.button type="button" variant="sm-danger"
                    x-on:click="resetWeek()"
                    x-bind:disabled="saving"
                    title="This permanently clears all content for this week.">
                    <i class="bx bx-reset text-[14px]"></i> Reset week
                </x-ui.button>

                {{-- Primary actions --}}
                <div class="flex items-center gap-2">
                    {{-- Hint when CO is missing --}}
                    <span x-show="coMissing"
                          class="hidden sm:inline-flex items-center gap-1.5 text-xs text-amber-600 mr-1">
                        <i class="bx bx-info-circle text-[13px]"></i>
                        Select a Course Outcome to save
                    </span>

                    <x-ui.button type="button" variant="cancel"
                        x-on:click="close()"
                        x-bind:disabled="saving">
                        Cancel
                    </x-ui.button>

                    <x-ui.button type="button" variant="save"
                        x-on:click="save()"
                        x-bind:disabled="saving || coMissing"
                        submitting="saving" loadingText="Saving…"
                        ::class="coMissing ? 'cursor-not-allowed' : ''">
                        <i class="bx bx-save text-[14px] leading-none"></i> Save week
                    </x-ui.button>
                </div>

            </div>

        </div>
    </div>

</div>

{{-- weekEditModal() is defined in resources/js/syllabus-wizard.js --}}
