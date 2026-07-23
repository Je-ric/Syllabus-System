/**
 * syllabus-wizard.js
 * Alpine component functions for the Syllabus Wizard steps.
 * Loaded once via Vite — no re-declaration on Livewire re-renders.
 */

// ── Time helpers ──────────────────────────────────────────────────────────────

window.parseTime = function parseTime(timeStr) {
    if (!timeStr) return { startTime: '', endTime: '' };
    const parts = timeStr.split(' - ');
    if (parts.length !== 2) return { startTime: '', endTime: '' };
    const toInput = (t) => {
        const m = t.trim().match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
        if (!m) return '';
        let h = parseInt(m[1]), min = m[2], period = m[3].toUpperCase();
        if (period === 'PM' && h !== 12) h += 12;
        if (period === 'AM' && h === 12) h = 0;
        return String(h).padStart(2, '0') + ':' + min;
    };
    return { startTime: toInput(parts[0]), endTime: toInput(parts[1]) };
};

window.formatTime = function formatTime(start, end) {
    if (!start || !end) return '';
    const fmt = (t) => {
        const [h, m] = t.split(':').map(Number);
        const period = h >= 12 ? 'PM' : 'AM';
        const h12 = h % 12 || 12;
        return h12 + ':' + String(m).padStart(2, '0') + ' ' + period;
    };
    return fmt(start) + ' - ' + fmt(end);
};

// ── labSection Alpine component ───────────────────────────────────────────────

window.labSection = function labSection(initUserId, initUsers, initSchedules, initHours) {
    const userMap = {};
    (initUsers || []).forEach(u => { userMap[u.id] = u; });
    const initUser = initUserId ? userMap[initUserId] : null;

    return {
        selectedUserId: initUserId || '',
        labName:   initUser?.name         || '',
        labEmail:  initUser?.email        || '',
        labPhone:  initUser?.phone_number || '',
        labOffice: initUser?.office       || '',
        schedules: (initSchedules || []).map(s => ({ day: s.day, ...parseTime(s.time) })),
        hours:     (initHours    || []).map(h => ({ day: h.day, ...parseTime(h.time) })),

        get hasInstructor() {
            return this.selectedUserId !== '' && this.selectedUserId != null;
        },

        async selectUser(id) {
            this.selectedUserId = id;
            if (!id) {
                await this.$wire.clearLabInstructor();
                return;
            }
            await this.$wire.selectLabInstructor(parseInt(id));
        },

        onInstructorSelected(detail) {
            const d = Array.isArray(detail) ? detail[0] : detail;
            this.labName   = d?.name   || '';
            this.labEmail  = d?.email  || '';
            this.labPhone  = d?.phone  || '';
            this.labOffice = d?.office || '';
            this.hours = (d?.consultationHours || []).map(h => ({ day: h.day, ...parseTime(h.time) }));
        },

        timesOverlap(aStart, aEnd, bStart, bEnd) {
            if (!aStart || !aEnd || !bStart || !bEnd) return false;
            return aStart < bEnd && bStart < aEnd;
        },
        hasConflict(hourRow) {
            return this.schedules.some(s =>
                s.day === hourRow.day &&
                this.timesOverlap(hourRow.startTime, hourRow.endTime, s.startTime, s.endTime)
            );
        },
        hasAnyConflicts() {
            return this.hours.some(h => this.hasConflict(h));
        },

        async pushToWire() {
            if (this.hasAnyConflicts()) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'error', message: 'Fix overlapping consultation hours (LAB) before saving.' }
                }));
                return Promise.reject('lab-conflict');
            }
            await this.$wire.pushLabSchedules(
                this.schedules.map(s => ({ day: s.day, time: formatTime(s.startTime, s.endTime) }))
            );
            await this.$wire.pushLabConsultationHours(
                this.hours.map(h => ({ day: h.day, time: formatTime(h.startTime, h.endTime) }))
            );
        },
    };
};

// ── coManager Alpine component ────────────────────────────────────────────────
// Batch save pattern: changes are staged locally, persisted via saveAll().
// Wire methods used: saveAll(drafts), deleteSingle(id)
// Wire events listened: co-all-saved, co-save-failed

window.coManager = function coManager(initialOutcomes, syllabusId) {
    return {
        outcomes: initialOutcomes.map((o, i) => ({
            ...o,
            _dirty:    false,
            _original: o.description,
            _key:      o.id ?? ('new-' + i),
        })),
        isSaving:    false,
        _keyCounter: initialOutcomes.length,
        viewModal:   { co_code: '', description: '' },

        markDirty(co) {
            if (co.id) co._dirty = (co.description !== co._original);
            this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: this.hasPending() });
        },

        hasPending() {
            return this.outcomes.some(o => !o.id || o._dirty);
        },

        pendingSummary() {
            return {
                added:    this.outcomes.filter(o => !o.id).length,
                modified: this.outcomes.filter(o => o.id && o._dirty).length,
            };
        },

        resequenceCodes() {
            this.outcomes.forEach((o, i) => { o.co_code = 'CO' + (i + 1); });
        },

        addCo() {
            if (this.outcomes.some(o => !o.description?.trim())) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'Fill in the blank CO before adding another.' }
                }));
                return;
            }
            this._keyCounter++;
            this.outcomes.push({
                id:          null,
                co_code:     'CO' + (this.outcomes.length + 1),
                description: '',
                _dirty:      false,
                _original:   '',
                _key:        'new-' + this._keyCounter,
            });
            this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: true });
            this.$nextTick(() => {
                const textareas = this.$el.querySelectorAll('textarea[x-model]');
                textareas[textareas.length - 1]?.focus();
            });
        },

        revert() {
            this.outcomes = this.outcomes
                .filter(o => o.id)
                .map(o => ({ ...o, description: o._original, _dirty: false }));
            this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: false });
        },

        async removeUnsaved(co, index) {
            if (co.description?.trim()) {
                const ok = await this._confirm({
                    title:        'Remove unsaved CO?',
                    message:      'This outcome has not been saved yet. Remove it?',
                    confirmLabel: 'Remove',
                    confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
                });
                if (!ok) return;
            }
            this.outcomes.splice(index, 1);
            this.resequenceCodes();
            this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: this.hasPending() });
        },

        async deleteCo(co) {
            if (!co.id) return;
            const ok = await this._confirm({
                title:        'Delete ' + co.co_code + '?',
                message:      'This course outcome will be permanently removed and codes will be re-sequenced.',
                confirmLabel: 'Delete',
                confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
            });
            if (!ok) return;
            this.isSaving = true;
            await this.$nextTick();
            try {
                await this.$wire.call('deleteSingle', co.id);
            } finally {
                this.isSaving = false;
            }
        },

        async saveAll() {
            if (!this.hasPending()) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'info', message: 'No changes to save.' }
                }));
                return;
            }
            if (this.outcomes.some(o => !o.description?.trim())) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'All course outcomes must have a description.' }
                }));
                return;
            }
            this.isSaving = true;
            await this.$nextTick();
            try {
                await this.$wire.call('saveAll',
                    this.outcomes.map(o => ({ id: o.id, description: o.description, isNew: !o.id }))
                );
            } catch {
                this.isSaving = false;
            }
        },

        onSaved(fresh) {
            this.outcomes = fresh.map((o, i) => ({
                ...o,
                _dirty:    false,
                _original: o.description,
                _key:      o.id ?? ('new-' + i),
            }));
            this.isSaving = false;
            this.$wire.dispatch('syllabus-step-dirty', { step: 'course_outcomes', dirty: false });
        },

        _confirm(detail) {
            return new Promise(resolve => {
                window.dispatchEvent(new CustomEvent('confirm-dialog:co', {
                    detail: { ...detail, _resolve: resolve }
                }));
            });
        },
    };
};

// ── weekEditModal Alpine component ────────────────────────────────────────────

window.weekEditModal = function weekEditModal() {
    return {
        isOpen:      false,
        saving:      false,
        weekNo:      null,
        weekDates:   '',
        isMvgo:      false,
        activeField: 'topic',

        fields: {
            course_outcome_id:   '',
            learning_outcomes:   '',
            assessment_task:     '',
            topic:               '',
            teaching_activities: '',
            references:  [{ text: '' }],
            materials:   [{ name: '', url: '' }],
        },

        richFields: [
            { key: 'topic',               label: 'Topics'                         },
            { key: 'learning_outcomes',   label: 'Unit Learning Outcomes'         },
            { key: 'teaching_activities', label: 'Teaching & Learning Activities' },
            { key: 'assessment_task',     label: 'Assessment Task'                },
        ],

        _quill:          null,
        _fieldsSnapshot: null,  // snapshot on open — used by Cancel to truly discard

        get activeFieldLabel() {
            if (this.activeField === 'references_materials') return 'References & Materials';
            return this.richFields.find(f => f.key === this.activeField)?.label ?? '';
        },

        get coMissing() {
            return !this.isMvgo && !this.fields.course_outcome_id;
        },

        // ── Lifecycle ──────────────────────────────────────────────────────────

        open(detail) {
            this.weekNo      = detail.weekNo;
            this.weekDates   = detail.weekDates;
            this.isMvgo      = detail.isMvgo;
            this.fields      = JSON.parse(JSON.stringify(detail.fields));
            this._fieldsSnapshot = JSON.parse(JSON.stringify(detail.fields)); // true snapshot
            this.activeField = detail.field ?? 'topic';
            this.saving      = false;
            this.isOpen      = true;
            this.$nextTick(() => this._initQuill());
        },

        /** Cancel — discard all changes, do NOT save current field. */
        close() {
            this._destroyQuill();
            this.isOpen = false;
        },

        // ── Quill ──────────────────────────────────────────────────────────────

        _initQuill() {
            const el = document.getElementById('week-quill-editor');
            if (!el || this.coMissing) return;
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
            if (content) this._quill.root.innerHTML = content;
            this._quill.focus();
        },

        _destroyQuill() {
            this._quill = null;
            const el = document.getElementById('week-quill-editor');
            if (el) el.innerHTML = '';
        },

        /**
         * Sanitize Quill HTML: convert data-list items to proper <ul>/<ol>
         * so the preview renders correct semantic lists without Quill's CSS.
         */
        _sanitizeQuillHtml(html) {
            const parser = new DOMParser();
            const doc    = parser.parseFromString(`<div>${html}</div>`, 'text/html');
            const root   = doc.body.firstChild;
            const result = document.createElement('div');
            let currentList = null, currentListType = null;

            root.childNodes.forEach(node => {
                if (node.nodeType !== Node.ELEMENT_NODE) {
                    currentList = currentListType = null;
                    result.appendChild(node.cloneNode(true));
                    return;
                }
                const listType = node.getAttribute('data-list');
                if (listType === 'bullet' || listType === 'ordered') {
                    const tag = listType === 'bullet' ? 'ul' : 'ol';
                    if (!currentList || currentListType !== tag) {
                        currentList     = document.createElement(tag);
                        currentListType = tag;
                        result.appendChild(currentList);
                    }
                    const li = document.createElement('li');
                    node.childNodes.forEach(child => {
                        if (child.nodeType === Node.ELEMENT_NODE && child.classList.contains('ql-ui')) return;
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
            if (!this._quill || this.coMissing) return;
            const rawHtml = this._quill.root.innerHTML;
            this.fields[this.activeField] = this._sanitizeQuillHtml(rawHtml);
        },

        // ── Tab switching ──────────────────────────────────────────────────────

        switchField(key) {
            if (key === this.activeField) return;
            if (this.activeField !== 'references_materials') this._saveCurrentField();
            this.activeField = key;
            if (key !== 'references_materials') {
                this.$nextTick(() => this._initQuill());
            } else {
                this._destroyQuill();
            }
        },

        // ── References & Materials ─────────────────────────────────────────────

        addRef()     { this.fields.references.push({ text: '' }); },
        removeRef(i) { this.fields.references.splice(i, 1); },
        addMat()     { this.fields.materials.push({ name: '', url: '' }); },
        removeMat(i) { this.fields.materials.splice(i, 1); },

        // ── URL validation ─────────────────────────────────────────────────────

        isValidUrl(url) {
            if (!url) return true; // empty is allowed
            return /^https?:\/\/.+/.test(url.trim());
        },

        hasInvalidUrls() {
            return this.fields.materials.some(m => m.url && !this.isValidUrl(m.url));
        },

        // ── Save / Reset ───────────────────────────────────────────────────────

        async save() {
            if (this.activeField !== 'references_materials') this._saveCurrentField();

            if (this.hasInvalidUrls()) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'error', message: 'One or more material URLs are invalid. URLs must start with http:// or https://.' }
                }));
                return;
            }

            this.saving = true;
            try {
                await this.$wire.saveWeekFromModal(this.weekNo, this.fields);
            } finally {
                this.saving = false;
            }
        },

        async resetWeek() {
            const ok = await this._confirm({
                title:        'Reset Week ' + this.weekNo + '?',
                message:      'All content for this week will be permanently cleared. This cannot be undone.',
                confirmLabel: 'Reset Week',
                confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
            });
            if (!ok) return;
            this.saving = true;
            try {
                // Destroy quill BEFORE the wire call so close() won't try to save stale content.
                this._destroyQuill();
                await this.$wire.resetWeek(this.weekNo);
                this.isOpen = false;
            } finally {
                this.saving = false;
            }
        },

        _confirm(detail) {
            return new Promise(resolve => {
                window.dispatchEvent(new CustomEvent('confirm-dialog:week', {
                    detail: { ...detail, _resolve: resolve }
                }));
            });
        },
    };
};
