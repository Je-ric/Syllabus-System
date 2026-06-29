{{--
    Partial: weekly-partials/week-edit-modal.blade.php
    Single modal. Field tabs on top, one Quill editor below.
    Fixes: bullet→number Quill bug, font inheritance, list rendering in preview.
--}}
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
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">

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
                            <span class="text-sm font-bold text-slate-800"
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
                                'bx bx-list-ul':        tab.key === 'topic',
                                'bx bx-target-lock':    tab.key === 'learning_outcomes',
                                'bx bx-chalkboard':     tab.key === 'teaching_activities'
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
                            ? 'border-b-2 border-blue-500 text-blue-700 bg-white'
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

                        {{-- CO selector row --}}
                        <div x-show="!isMvgo"
                             class="px-6 pt-4 pb-3 border-b border-slate-100 shrink-0 bg-slate-50/60">
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                                Course Outcome
                            </label>
                            <select x-model="fields.course_outcome_id"
                                class="w-full max-w-xl rounded-lg border border-slate-200 bg-white
                                       px-3 py-2 text-sm text-slate-700
                                       focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100
                                       transition-colors">
                                <option value="">— Not mapped to a specific CO —</option>
                                @foreach ($courseOutcomes as $co)
                                    <option value="{{ $co['id'] }}">
                                        {{ $co['co_code'] }} — {{ \Illuminate\Support\Str::limit($co['description'], 80) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Quill editor (grows to fill remaining space) --}}
                        <div class="flex-1 flex flex-col px-6 pt-5 pb-4 min-h-0">

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
                    <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- References --}}
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">References</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Books, journals, printed sources</p>
                                </div>
                                <button type="button" x-on:click="addRef()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                                           bg-emerald-50 text-emerald-700 border border-emerald-200
                                           hover:bg-emerald-100 transition-colors">
                                    <i class="bx bx-plus text-[14px]"></i> Add
                                </button>
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

                        {{-- Vertical divider (hidden on mobile) --}}
                        {{-- Handled by gap --}}

                        {{-- Online Materials --}}
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Online Materials</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Links, slides, videos, web resources</p>
                                </div>
                                <button type="button" x-on:click="addMat()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                                           bg-blue-50 text-blue-700 border border-blue-200
                                           hover:bg-blue-100 transition-colors">
                                    <i class="bx bx-plus text-[14px]"></i> Add
                                </button>
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
                </template>

            </div>

            {{-- ── Footer ──────────────────────────────────────────── --}}
            <div class="flex items-center justify-between gap-3 px-6 py-3.5
                        border-t border-slate-100 bg-slate-50/60 shrink-0 rounded-b-2xl">

                {{-- Danger zone --}}
                <button type="button" x-on:click="resetWeek()" x-bind:disabled="saving"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                           text-slate-400 hover:text-rose-500 hover:bg-rose-50 border border-transparent
                           hover:border-rose-200 disabled:opacity-40 transition-all duration-150">
                    <i class="bx bx-reset text-[14px]"></i>
                    Reset week
                </button>

                {{-- Primary actions --}}
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="close()" x-bind:disabled="saving"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600
                               border border-slate-200 bg-white hover:bg-slate-50
                               disabled:opacity-40 transition-colors duration-150">
                        Cancel
                    </button>

                    <button type="button" x-on:click="save()" x-bind:disabled="saving"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-sm font-semibold
                               text-white disabled:opacity-50 transition-all duration-150"
                        style="background: linear-gradient(135deg, #009639 0%, #16a34a 100%); box-shadow: 0 2px 8px rgba(0,150,57,0.35);">

                        <template x-if="!saving">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="bx bx-save text-[14px] leading-none"></i>
                                Save week
                            </span>
                        </template>

                        <template x-if="saving">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
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
        isOpen:      false,
        saving:      false,
        weekNo:      null,
        weekDates:   '',
        isMvgo:      false,
        activeField: 'learning_outcomes',

        fields: {
            course_outcome_id:  '',
            learning_outcomes:  '',
            assessment_task:    '',
            topic:              '',
            teaching_activities: '',
            references:  [{ text: '' }],
            materials:   [{ name: '', url: '' }],
        },

        richFields: [
            { key: 'learning_outcomes',   label: 'Unit Learning Outcomes' },
            { key: 'assessment_task',     label: 'Assessment Task'        },
            { key: 'topic',               label: 'Topics'                 },
            { key: 'teaching_activities', label: 'Teaching & Learning Activities' },
        ],

        _quill: null,

        get activeFieldLabel() {
            if (this.activeField === 'references_materials') return 'References & Materials';
            return this.richFields.find(f => f.key === this.activeField)?.label ?? '';
        },

        // ── Lifecycle ────────────────────────────────────────────────────────

        open(detail) {
            this.weekNo      = detail.weekNo;
            this.weekDates   = detail.weekDates;
            this.isMvgo      = detail.isMvgo;
            this.fields      = JSON.parse(JSON.stringify(detail.fields));
            this.activeField = detail.field ?? 'learning_outcomes';
            this.saving      = false;
            this.isOpen      = true;

            this.$nextTick(() => this._initQuill());
        },

        close() {
            if (this.activeField !== 'references_materials') {
                this._saveCurrentField();
            }
            this._destroyQuill();
            this.isOpen = false;
        },

        // ── Quill ────────────────────────────────────────────────────────────

        _initQuill() {
            const el = document.getElementById('week-quill-editor');
            if (!el) return;

            if (this._quill) this._destroyQuill();
            el.innerHTML = '';

            const editorDiv = document.createElement('div');
            el.appendChild(editorDiv);

            this._quill = new Quill(editorDiv, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['clean'],
                    ],
                },
            });

            const content = this.fields[this.activeField] ?? '';
            if (content) {
                this._quill.root.innerHTML = content;
            }

            // Let the cell control font size — do NOT set inline font-size here.
            this._quill.focus();
        },

        _destroyQuill() {
            this._quill = null;
            const el = document.getElementById('week-quill-editor');
            if (el) el.innerHTML = '';
        },

        // ── BUG FIX: sanitize Quill HTML before saving ───────────────────────
        //
        // Quill Snow outputs a flat <ol> with data-list="bullet" OR data-list="ordered"
        // on EVERY <li> — both bullets and numbers share the same tag.  The browser's
        // native list rendering is bypassed; Quill uses ::before counters instead.
        //
        // When that raw HTML lands in the preview, your CSS reset strips the Quill
        // stylesheet, the ::before counters fire on every item (regardless of type),
        // and everything looks like a numbered list.
        //
        // Fix: walk the saved HTML, group consecutive data-list items into proper
        // <ul> or <ol> elements, and strip Quill's .ql-ui marker spans.
        // The preview then gets real semantic lists that native CSS can style normally.
        // ─────────────────────────────────────────────────────────────────────
        _sanitizeQuillHtml(html) {
            const parser = new DOMParser();
            const doc    = parser.parseFromString(`<div>${html}</div>`, 'text/html');
            const root   = doc.body.firstChild;
            const result = document.createElement('div');

            let currentList     = null;
            let currentListType = null;

            root.childNodes.forEach(node => {
                if (node.nodeType !== Node.ELEMENT_NODE) {
                    currentList = currentListType = null;
                    result.appendChild(node.cloneNode(true));
                    return;
                }

                const listType = node.getAttribute('data-list'); // "bullet" | "ordered" | null

                if (listType === 'bullet' || listType === 'ordered') {
                    const tag = listType === 'bullet' ? 'ul' : 'ol';

                    // Start a new list when type changes
                    if (!currentList || currentListType !== tag) {
                        currentList     = document.createElement(tag);
                        currentListType = tag;
                        result.appendChild(currentList);
                    }

                    // Build <li> — skip Quill's .ql-ui marker span
                    const li = document.createElement('li');
                    node.childNodes.forEach(child => {
                        if (
                            child.nodeType === Node.ELEMENT_NODE &&
                            child.classList.contains('ql-ui')
                        ) return;
                        li.appendChild(child.cloneNode(true));
                    });
                    currentList.appendChild(li);

                } else {
                    currentList = currentListType = null;
                    result.appendChild(node.cloneNode(true));
                }
            });

            return result.innerHTML;
        },

        _saveCurrentField() {
            if (!this._quill) return;
            const rawHtml  = this._quill.root.innerHTML;
            const clean    = this._sanitizeQuillHtml(rawHtml);
            this.fields[this.activeField] = clean;
        },

        // ── Tab switching ────────────────────────────────────────────────────

        switchField(key) {
            if (key === this.activeField) return;

            if (this.activeField !== 'references_materials') {
                this._saveCurrentField();
            }

            this.activeField = key;

            if (key !== 'references_materials') {
                this.$nextTick(() => this._initQuill());
            } else {
                this._destroyQuill();
            }
        },

        // ── References & Materials helpers ───────────────────────────────────

        addRef()       { this.fields.references.push({ text: '' }); },
        removeRef(i)   { this.fields.references.splice(i, 1); },
        addMat()       { this.fields.materials.push({ name: '', url: '' }); },
        removeMat(i)   { this.fields.materials.splice(i, 1); },

        // ── Save / Reset ─────────────────────────────────────────────────────

        async save() {
            if (this.activeField !== 'references_materials') {
                this._saveCurrentField();
            }
            this.saving = true;
            try {
                await this.$wire.saveWeekFromModal(this.weekNo, this.fields);
            } finally {
                this.saving = false;
            }
        },

        async resetWeek() {
            if (!confirm(`Reset Week ${this.weekNo}? All content will be cleared. This cannot be undone.`)) return;
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
