{{--
    Partial: weekly-partials/week-edit-modal.blade.php
    Single modal. Field tabs on the left, one Quill editor at a time on the right.
--}}
<div x-data="weekEditModal()" x-on:open-week-modal.window="open($event.detail)" x-on:week-modal-saved.window="close()"
    class="relative z-50">
    {{-- Backdrop --}}
    <div x-show="isOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-on:click="close()"
        class="fixed inset-0 bg-black/40 z-50"></div>

    {{-- Modal --}}
    <div x-show="isOpen" x-cloak x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] flex flex-col" x-on:click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0] shrink-0">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#dcfce7] text-[#16a34a]">
                        <i class="bx bx-edit text-base leading-none"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-[#0f172a]"
                            x-text="'Week ' + weekNo + ' — ' + activeFieldLabel"></p>
                        <p class="text-xs text-[#94a3b8] mt-0.5" x-text="weekDates"></p>
                    </div>
                </div>
                <button type="button" x-on:click="close()"
                    class="p-2 rounded-lg text-[#94a3b8] hover:text-[#0f172a] hover:bg-[#f1f5f9] transition-colors">
                    <i class="bx bx-x text-xl leading-none"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto flex flex-col gap-0">

                {{-- Field tab strip --}}
                <div class="flex items-center gap-1 px-5 pt-4 pb-3 border-b border-[#f1f5f9] shrink-0 overflow-x-auto">
                    <template x-for="tab in richFields" :key="tab.key">
                        <button type="button" x-on:click="switchField(tab.key)"
                            x-bind:class="activeField === tab.key ?
                                'bg-[#16a34a] text-white shadow-sm' :
                                'bg-[#f1f5f9] text-[#475569] hover:bg-[#e2e8f0]'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-[12px] font-semibold transition-colors">
                            <span x-text="tab.label"></span>
                        </button>
                    </template>
                </div>

                <div class="px-5 pt-4 pb-2 flex flex-col gap-4">

                    {{-- CO selector / MVGO --}}
                    <div x-show="!isMvgo">
                        <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1.5">
                            Course Outcome
                        </label>
                        <select x-model="fields.course_outcome_id"
                            class="w-full text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2
                                   focus:border-[#16a34a] focus:outline-none focus:bg-white transition-colors">
                            <option value="">— Select Course Outcome —</option>
                            @foreach ($courseOutcomes as $co)
                                <option value="{{ $co['id'] }}">
                                    {{ $co['co_code'] }} – {{ \Illuminate\Support\Str::limit($co['description'], 70) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="isMvgo"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg border border-[#bbf7d0] bg-[#f0fdf4]">
                        <x-feedback-status.status-indicator variant="brand"
                            icon="bx bx-star">MVGO</x-feedback-status.status-indicator>
                        <span class="text-[13px] text-[#475569]">Mission-Vision-Goals-Objectives</span>
                    </div>

                    {{-- Single Quill editor --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-1.5"
                            x-text="activeFieldLabel"></label>
                        <div id="week-quill-editor" class="rounded-lg border border-[#e2e8f0] bg-white overflow-hidden"
                            class="rounded-lg border border-[#e2e8f0] bg-white
                                    [&_.ql-toolbar]:rounded-t-lg [&_.ql-toolbar]:border-[#e2e8f0]
                                    [&_.ql-container]:rounded-b-lg [&_.ql-container]:border-[#e2e8f0]
                                    [&_.ql-editor]:text-[13px] [&_.ql-editor]:min-h-45">
                        </div>
                    </div>

                    {{-- References --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]">
                                <i class="bx bx-book-open text-[#16a34a]"></i> References
                            </p>
                            <button type="button" x-on:click="addRef()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-semibold
                                       bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0] hover:bg-[#dcfce7] transition-colors">
                                <i class="bx bx-plus text-sm"></i> Add
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(ref, rIdx) in fields.references" :key="rIdx">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="ref.text"
                                        placeholder="e.g. Author (Year). Title. Publisher."
                                        class="flex-1 text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-1.5
                                               focus:border-[#16a34a] focus:outline-none focus:bg-white
                                               placeholder:text-[#94a3b8] transition-colors" />
                                    <template x-if="fields.references.length > 1">
                                        <button type="button" x-on:click="removeRef(rIdx)"
                                            class="shrink-0 p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </template>
                                    <template x-if="fields.references.length <= 1">
                                        <span class="w-7 shrink-0"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Online Materials --}}
                    <div class="pb-2">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#1e40af]">
                                <i class="bx bx-link text-[#3b82f6]"></i> Online Materials
                            </p>
                            <button type="button" x-on:click="addMat()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-semibold
                                       bg-[#eff6ff] text-[#3b82f6] border border-[#bfdbfe] hover:bg-[#dbeafe] transition-colors">
                                <i class="bx bx-plus text-sm"></i> Add
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(mat, mIdx) in fields.materials" :key="mIdx">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1 space-y-1.5">
                                        <input type="text" x-model="mat.name"
                                            :placeholder="'Name (e.g. Week ' + weekNo + ' Slides)'"
                                            class="w-full text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-1.5
                                                   focus:border-[#16a34a] focus:outline-none focus:bg-white
                                                   placeholder:text-[#94a3b8] transition-colors" />
                                        <input type="url" x-model="mat.url" placeholder="https://…"
                                            class="w-full text-[13px] rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-1.5
                                                   focus:border-[#16a34a] focus:outline-none focus:bg-white
                                                   placeholder:text-[#94a3b8] transition-colors" />
                                    </div>
                                    <template x-if="fields.materials.length > 1">
                                        <button type="button" x-on:click="removeMat(mIdx)"
                                            class="shrink-0 mt-1 p-1.5 text-[#94a3b8] hover:text-rose-500 hover:bg-rose-50 rounded-md transition-colors">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </template>
                                    <template x-if="fields.materials.length <= 1">
                                        <span class="w-7 shrink-0 mt-1"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div
                class="flex items-center justify-between gap-3 px-5 py-4 border-t border-[#e2e8f0] bg-[#f8fafc] shrink-0 rounded-b-2xl">
                <button type="button" x-on:click="resetWeek()" x-bind:disabled="saving"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                           text-rose-600 border border-rose-200 bg-white hover:bg-rose-50
                           disabled:opacity-40 transition-colors">
                    <i class="bx bx-reset text-sm"></i> Reset Week
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="close()" x-bind:disabled="saving"
                        class="px-4 py-2 rounded-lg text-[13px] font-semibold text-[#475569]
                               border border-[#e2e8f0] bg-white hover:bg-[#f1f5f9]
                               disabled:opacity-40 transition-colors">
                        Cancel
                    </button>
                    <button type="button" x-on:click="save()" x-bind:disabled="saving"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-semibold
                               bg-[#16a34a] text-white hover:bg-[#15803d] disabled:opacity-50 transition-colors">
                        <template x-if="!saving">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="bx bx-save text-sm leading-none"></i> Save Week
                            </span>
                        </template>
                        <template x-if="saving">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Saving…
                            </span>
                        </template>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    function weekEditModal() {
        return {
            isOpen: false,
            saving: false,
            weekNo: null,
            weekDates: '',
            isMvgo: false,
            activeField: 'learning_outcomes',
            fields: {
                course_outcome_id: '',
                learning_outcomes: '',
                assessment_task: '',
                topic: '',
                teaching_activities: '',
                references: [{
                    text: ''
                }],
                materials: [{
                    name: '',
                    url: ''
                }],
            },
            richFields: [{
                    key: 'learning_outcomes',
                    label: 'Unit Learning Outcomes'
                },
                {
                    key: 'assessment_task',
                    label: 'Assessment Task'
                },
                {
                    key: 'topic',
                    label: 'Topics'
                },
                {
                    key: 'teaching_activities',
                    label: 'Teaching & Learning Activities'
                },
            ],
            _quill: null,

            get activeFieldLabel() {
                return this.richFields.find(f => f.key === this.activeField)?.label ?? '';
            },

            open(detail) {
                this.weekNo = detail.weekNo;
                this.weekDates = detail.weekDates;
                this.isMvgo = detail.isMvgo;
                this.fields = JSON.parse(JSON.stringify(detail.fields));
                this.activeField = detail.field ?? 'learning_outcomes';
                this.saving = false;
                this.isOpen = true;

                // Initialize Quill after modal is rendered
                this.$nextTick(() => {
                    this._initQuill();
                });
            },

            close() {
                // Save any pending changes before closing
                this._saveCurrentField();
                // Clean up Quill instance
                this._destroyQuill();
                this.isOpen = false;
            },

            _initQuill() {
                const el = document.getElementById('week-quill-editor');
                if (!el) return;

                // Destroy previous instance if it exists
                if (this._quill) {
                    this._destroyQuill();
                }

                // Clear container completely
                el.innerHTML = '';

                // Add placeholder div for Quill
                const editorDiv = document.createElement('div');
                el.appendChild(editorDiv);

                // Initialize Quill on the new div
                this._quill = new Quill(editorDiv, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }],
                            ['clean'],
                        ],
                    },
                });

                // Set content using Quill's API
                const content = this.fields[this.activeField] ?? '';
                if (content) {
                    this._quill.root.innerHTML = content;
                }

                // Focus the editor
                this._quill.focus();
            },

            _destroyQuill() {
                if (this._quill) {
                    this._quill = null;
                }
                const el = document.getElementById('week-quill-editor');
                if (el) {
                    el.innerHTML = '';
                }
            },

            _saveCurrentField() {
                if (this._quill) {
                    // Capture HTML from Quill editor
                    const html = this._quill.root.innerHTML;
                    this.fields[this.activeField] = html;
                }
            },

            switchField(key) {
                if (key === this.activeField) return;

                // Save current field before switching
                this._saveCurrentField();
                this.activeField = key;

                // Reinitialize Quill for the new field
                this.$nextTick(() => {
                    this._initQuill();
                });
            },

            addRef() {
                this.fields.references.push({
                    text: ''
                });
            },
            removeRef(i) {
                this.fields.references.splice(i, 1);
            },
            addMat() {
                this.fields.materials.push({
                    name: '',
                    url: ''
                });
            },
            removeMat(i) {
                this.fields.materials.splice(i, 1);
            },

            async save() {
                this._saveCurrentField();
                this.saving = true;

                try {
                    await this.$wire.saveWeekFromModal(this.weekNo, this.fields);
                } finally {
                    this.saving = false;
                }
            },

            async resetWeek() {
                if (!confirm('Reset Week ' + this.weekNo + '? This clears all content. Cannot be undone.')) return;
                this.saving = true;

                try {
                    await this.$wire.resetWeek(this.weekNo);
                    this.close();
                } finally {
                    this.saving = false;
                }
            },
        };
    }
</script>
