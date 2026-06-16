{{--
    manage-pos.blade.php — Editable, sortable PO list with PEO mapping.
    Livewire: ManagePos | Alpine: posManager()
--}}
<div x-data="posManager(@js($pos), @js($peos), @js($mapping))" class="space-y-2.5">

    @include('livewire.programs.partials.confirm-modal', ['confirmNs' => 'po'])
    @include('livewire.programs.include.flash-message')

    {{-- Pending changes bar --}}
    <template x-if="pendingSummary().total > 0">
        <div
            class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-[13px]">
            <i class="bx bx-info-circle text-amber-500 shrink-0"></i>
            <span class="text-amber-700 font-medium">Unsaved changes:</span>
            <template x-if="pendingSummary().added > 0">
                <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[12px] font-semibold">
                    <i class="bx bx-plus text-xs"></i>
                    <span x-text="pendingSummary().added + ' new'"></span>
                </span>
            </template>
            <template x-if="pendingSummary().modified > 0">
                <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[12px] font-semibold">
                    <i class="bx bx-edit-alt text-xs"></i>
                    <span x-text="pendingSummary().modified + ' modified'"></span>
                </span>
            </template>
            <span class="ml-auto text-[12px] text-amber-600">Click <strong>Save All</strong> to apply.</span>
        </div>
    </template>

    {{-- PO rows (sortable) --}}
    <div id="po-sortable" class="space-y-2">
        <template x-for="(po, index) in pos" :key="po._key">
            <div class="rounded-xl border transition-colors duration-200 overflow-hidden"
                :class="{
                    'border-blue-300 bg-blue-50/30': !po.id,
                    'border-amber-300 bg-amber-50/30': po.id && po._dirty,
                    'border-slate-200 bg-white': po.id && !po._dirty
                }"
                style="box-shadow:0 1px 8px rgba(0,0,0,.06);">

                {{-- PO text row --}}
                <div class="flex items-start gap-3 px-4 py-3">

                    {{-- Drag handle --}}
                    <span
                        class="po-drag-handle mt-2.5 cursor-grab active:cursor-grabbing text-slate-300 hover:text-slate-500 shrink-0">
                        <i class="bx bx-grid-vertical text-lg"></i>
                    </span>

                    {{-- Code badge --}}
                    <span
                        class="shrink-0 mt-1 inline-flex items-center justify-center w-9 h-9 rounded-lg text-[12px] font-bold transition-colors"
                        :class="{
                            'bg-blue-100 text-blue-700 ring-1 ring-blue-300': !po.id,
                            'bg-amber-100 text-amber-700 ring-1 ring-amber-300': po.id && po._dirty,
                            'bg-blue-50 text-blue-800 ring-1 ring-blue-200': po.id && !po._dirty
                        }">
                        <span x-text="'PO' + (index + 1)"></span>
                    </span>

                    {{-- Textarea --}}
                    <div class="flex-1 min-w-0">
                        <textarea x-model="po.po_text" @input="markDirty(po)" rows="3"
                            placeholder="Describe the ability or competency graduates will have by the time of graduation…"
                            class="w-full rounded-lg border px-3 py-2 text-[13px] text-slate-900 placeholder:text-slate-400 focus:outline-none transition-colors resize-none"
                            :class="{
                                'border-amber-300 bg-amber-50/50 focus:border-amber-400': po.id && po._dirty,
                                'border-blue-300 bg-blue-50/50 focus:border-blue-400': !po.id,
                                'border-slate-200 bg-white focus:border-blue-500': po.id && !po._dirty
                            }"></textarea>
                        <template x-if="!po.id">
                            <p class="mt-1 flex items-center gap-1 text-[12px] text-blue-600">
                                <i class="bx bx-plus-circle text-sm shrink-0"></i>
                                New — click <strong class="mx-0.5">Save All</strong> to persist.
                            </p>
                        </template>
                        <template x-if="po.id && po._dirty">
                            <p class="mt-1 flex items-center gap-1 text-[12px] text-amber-600">
                                <i class="bx bx-edit-alt text-sm shrink-0"></i>
                                Modified — not saved yet.
                            </p>
                        </template>
                    </div>

                    {{-- Delete saved PO --}}
                    <button x-show="po.id" type="button" @click="deletePo(po)"
                        class="mt-1 p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete PO">
                        <i class="bx bx-trash text-base"></i>
                    </button>

                    {{-- Remove unsaved PO --}}
                    <button x-show="!po.id" x-cloak type="button" @click="pos.splice(index, 1)"
                        class="mt-1 p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Remove unsaved PO">
                        <i class="bx bx-x text-lg"></i>
                    </button>

                </div>

                {{-- PEO Mapping section --}}
                <div class="border-t px-4 py-3"
                    :class="po.id ? 'border-slate-100 bg-slate-50/60' : 'border-blue-100 bg-blue-50/20'">

                    <div class="flex items-center justify-between mb-2">
                        <p
                            class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500 flex items-center gap-1.5">
                            <i class="bx bx-link text-slate-400"></i>
                            Maps to PEOs
                            <template x-if="po.id && mappedCount(po.id) > 0">
                                <span
                                    class="inline-flex items-center justify-center min-w-[1.2rem] h-4 px-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold"
                                    x-text="mappedCount(po.id)"></span>
                            </template>
                            {{-- Pending mapping indicator --}}
                            <template x-if="po.id && hasMappingPending(po.id)">
                                <span
                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-semibold animate-pulse">
                                    <i class="bx bx-loader-alt bx-spin text-xs"></i>
                                    saving…
                                </span>
                            </template>
                        </p>
                        {{-- Offcanvas trigger --}}
                        <template x-if="po.id && peos.length > 0">
                            <label for="peo-reference-drawer"
                                class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold cursor-pointer hover:underline">
                                <i class="bx bx-book-open text-sm"></i> View PEOs
                            </label>
                        </template>
                    </div>

                    {{-- Unsaved PO --}}
                    <template x-if="!po.id">
                        <p class="flex items-center gap-1.5 text-[12px] text-blue-500 py-1">
                            <i class="bx bx-lock-alt text-sm shrink-0"></i>
                            Save this PO first to enable PEO mapping.
                        </p>
                    </template>

                    {{-- No PEOs --}}
                    <template x-if="po.id && peos.length === 0">
                        <p class="flex items-center gap-1.5 text-[12px] text-slate-400 italic py-1">
                            No PEOs defined yet — go to the PEOs tab to add them.
                        </p>
                    </template>

                    {{-- Mapping chips --}}
                    <template x-if="po.id && peos.length > 0">
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="peo in peos" :key="peo.id">
                                <label :title="peo.peo_text"
                                    :class="isMappingPending(po.id, peo.id)
                                        ? 'border-amber-300 bg-amber-50 text-amber-700 ring-1 ring-amber-200 opacity-75'
                                        : isPoMappedToPeo(po.id, peo.id)
                                            ? 'border-emerald-400 bg-emerald-100 text-emerald-800 ring-1 ring-emerald-300'
                                            : 'border-slate-200 bg-white text-slate-500 hover:border-emerald-200 hover:bg-emerald-50/40'"
                                    class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border px-2.5 py-1.5 text-[12px] font-semibold transition-all duration-100 select-none">
                                    <input type="checkbox" :checked="isPoMappedToPeo(po.id, peo.id)"
                                        @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                        :disabled="isMappingPending(po.id, peo.id)"
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200 focus:ring-1">
                                    <span x-text="peo.peo_code.toUpperCase()"></span>
                                    <template x-if="isMappingPending(po.id, peo.id)">
                                        <svg class="animate-spin h-3 w-3 text-amber-500 shrink-0" viewBox="0 0 24 24"
                                            fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </template>
                                    <template x-if="isPoMappedToPeo(po.id, peo.id) && !isMappingPending(po.id, peo.id)">
                                        <i class="bx bx-check text-xs text-emerald-600"></i>
                                    </template>
                                </label>
                            </template>
                        </div>
                    </template>

                </div>

            </div>
        </template>
    </div>

    {{-- Empty state --}}
    <template x-if="pos.length === 0">
        <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 py-10 text-center">
            <i class="bx bx-target-lock text-4xl text-slate-300"></i>
            <p class="mt-2 text-[13px] font-semibold text-slate-500">No POs yet</p>
            <p class="text-[13px] text-slate-400 mt-0.5">Add the first one below.</p>
        </div>
    </template>

    {{-- Action buttons --}}
    <div class="flex items-center gap-2 pt-1">
        <x-button variant="add-dashed" type="button" @click="addPo()" class="flex-1 w-full">
            <i class="bx bx-plus text-base"></i> Add PO
        </x-button>

        <x-button variant="add-button" type="button" @click="savePos()" x-bind:disabled="isSaving"
            class="whitespace-nowrap relative">
            <template x-if="hasPending()">
                <span
                    class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-white animate-pulse"></span>
            </template>
            <span x-show="!isSaving" class="inline-flex items-center gap-1.5 leading-none">
                <i class="bx bx-save text-base leading-none"></i> Save All
            </span>
            <span x-show="isSaving" x-cloak class="inline-flex items-center gap-1.5 leading-none">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <span class="leading-none">Saving…</span>
            </span>
        </x-button>
    </div>

    @include('livewire.programs.offcanvasReference')

    {{-- Hidden delete forms rendered server-side --}}
    @foreach ($pos as $po)
        @if (!empty($po['id']))
            <form id="po-delete-form-{{ $po['id'] }}" method="POST"
                action="{{ route('programs.po.delete', $po['id']) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endforeach

    <script>
        function posManager(initialPos, initialPeos, initialMapping) {
            return {
                pos: initialPos.map((p, i) => ({
                    ...p,
                    _dirty: false,
                    _original: p.po_text,
                    _key: p.id ?? ('new-' + i)
                })),
                peos: initialPeos,
                mapping: initialMapping,
                _mappingPending: {},
                isSaving: false,
                _keyCounter: initialPos.length,
                _serverOrder: [],

                init() {
                    this._serverOrder = this.pos.filter(p => p.id).map(p => p.id);
                    this.$nextTick(() => this.initSortable());
                },

                initSortable() {
                    const el = document.getElementById('po-sortable');
                    if (!el || typeof Sortable === 'undefined') return;
                    Sortable.create(el, {
                        handle: '.po-drag-handle',
                        animation: 150,
                        onEnd: (evt) => {
                            if (evt.oldIndex === evt.newIndex) return;
                            const moved = this.pos.splice(evt.oldIndex, 1)[0];
                            this.pos.splice(evt.newIndex, 0, moved);
                            // Only mark dirty rows whose position changed vs server order
                            const savedRows = this.pos.filter(p => p.id);
                            savedRows.forEach((p, i) => {
                                if (this._serverOrder[i] !== p.id) p._dirty = true;
                            });
                        }
                    });
                },

                markDirty(po) {
                    if (po.id) po._dirty = (po.po_text !== po._original);
                },

                hasPending() {
                    return this.pos.some(p => !p.id || p._dirty);
                },

                pendingSummary() {
                    return {
                        added: this.pos.filter(p => !p.id).length,
                        modified: this.pos.filter(p => p.id && p._dirty).length,
                        get total() {
                            return this.added + this.modified;
                        }
                    };
                },

                mappedCount(poId) {
                    return (this.mapping[poId] ?? []).length;
                },

                isPoMappedToPeo(poId, peoId) {
                    return Array.isArray(this.mapping[poId]) && this.mapping[poId].includes(peoId);
                },

                isMappingPending(poId, peoId) {
                    return !!this._mappingPending[poId + '-' + peoId];
                },

                hasMappingPending(poId) {
                    return Object.keys(this._mappingPending).some(k => k.startsWith(poId + '-'));
                },

                addPo() {
                    if (this.pos.some(p => !p.po_text?.trim())) {
                        window.dispatchEvent(new CustomEvent('lw-toast', {
                            detail: { type: 'warning', message: 'Fill in the blank PO before adding another.' }
                        }));
                        return;
                    }
                    this._keyCounter++;
                    this.pos.push({
                        id: null,
                        po_code: '',
                        po_text: '',
                        _dirty: false,
                        _original: '',
                        _key: 'new-' + this._keyCounter
                    });
                },

                toggleMapping(poId, peoId, checked) {
                    if (!this.mapping[poId]) this.mapping[poId] = [];
                    this.mapping[poId] = checked
                        ? [...new Set([...this.mapping[poId], peoId])]
                        : this.mapping[poId].filter(id => id !== peoId);

                    const key = poId + '-' + peoId;
                    this._mappingPending = { ...this._mappingPending, [key]: true };

                    @this.call('toggleMapping', poId, peoId, checked)
                        .then(() => {
                            const pending = { ...this._mappingPending };
                            delete pending[key];
                            this._mappingPending = pending;
                        })
                        .catch(() => {
                            // Rollback optimistic update
                            this.mapping[poId] = checked
                                ? this.mapping[poId].filter(id => id !== peoId)
                                : [...new Set([...this.mapping[poId], peoId])];
                            const pending = { ...this._mappingPending };
                            delete pending[key];
                            this._mappingPending = pending;
                            window.dispatchEvent(new CustomEvent('lw-toast', {
                                detail: { type: 'error', message: 'Mapping update failed. Please try again.' }
                            }));
                        });
                },

                confirm(detail) {
                    return new Promise(resolve => {
                        window.dispatchEvent(new CustomEvent('confirm-dialog:po', {
                            detail: { ...detail, _resolve: resolve }
                        }));
                    });
                },

                async deletePo(po) {
                    const ok = await this.confirm({
                        title: 'Delete PO?',
                        message: 'This PO will be permanently removed. This action cannot be undone.',
                        confirmLabel: 'Delete',
                        confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white'
                    });
                    if (!ok) return;
                    const form = document.getElementById('po-delete-form-' + po.id);
                    if (form) form.submit();
                },

                async savePos() {
                    if (!this.hasPending()) {
                        window.dispatchEvent(new CustomEvent('lw-toast', {
                            detail: { type: 'info', message: 'No changes to save.' }
                        }));
                        return;
                    }
                    const ok = await this.confirm({
                        title: 'Save all POs?',
                        message: 'This will save all new and modified POs and re-sequence codes.',
                        confirmLabel: 'Save All',
                        confirmClass: 'bg-blue-600 hover:bg-blue-700 text-white'
                    });
                    if (!ok) return;

                    this.isSaving = true;
                    @this.call('savePos', this.pos.map(p => ({ id: p.id, po_text: p.po_text })), this.mapping)
                        .finally(() => { this.isSaving = false; });
                }
            };
        }
    </script>

</div>
