{{-- manage-pos.blade.php --}}
<div x-data="posManager(@js($pos), @js($peos), @js($mapping))" class="space-y-4">

    @include('livewire.programs.partials.confirm-modal', ['confirmNs' => 'po'])
    @include('livewire.programs.include.flash-message')

    {{-- Section header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 ring-1 ring-blue-200">
                <i class="bx bx-target-lock text-blue-700 text-sm"></i>
            </span>
            <h3 class="text-[13px] font-bold text-slate-700">Program Outcomes & PEO Mapping</h3>
            <span class="inline-flex items-center justify-center min-w-[1.4rem] h-5 px-1.5 rounded-full bg-blue-100 text-blue-700 text-[11px] font-bold ring-1 ring-blue-200"
                x-text="pos.filter(p => p.id).length"></span>
        </div>
    </div>

    {{-- Pending changes bar --}}
    <template x-if="pendingSummary().total > 0">
        <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-[13px]">
            <i class="bx bx-error-circle text-amber-500 shrink-0"></i>
            <span class="text-amber-700 font-semibold">Unsaved changes:</span>
            <template x-if="pendingSummary().added > 0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">
                    <i class="bx bx-plus text-xs"></i>
                    <span x-text="pendingSummary().added + ' new'"></span>
                </span>
            </template>
            <template x-if="pendingSummary().modified > 0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">
                    <i class="bx bx-edit-alt text-xs"></i>
                    <span x-text="pendingSummary().modified + ' modified'"></span>
                </span>
            </template>
            <span class="ml-auto text-[11px] text-amber-600">Click <strong>Save All</strong> to apply.</span>
        </div>
    </template>

    {{-- PO rows --}}
    <div class="space-y-2">
        <template x-for="(po, index) in pos" :key="po._key">
            <div class="rounded-xl border shadow-lg transition-all duration-200 overflow-hidden"
                :class="{
                    'border-blue-300 bg-blue-50/30':       !po.id,
                    'border-amber-300 bg-amber-50/30':     po.id && po._dirty,
                    'border-slate-200 bg-white':           po.id && !po._dirty
                }">

                {{-- PO text row --}}
                <div class="flex items-start gap-3 px-4 py-3 border-l-4 border-blue-700 rounded-tl-xl">

                    {{-- Code badge --}}
                    <div class="shrink-0 flex flex-col items-center gap-1 mt-0.5">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-[11px] font-bold transition-colors"
                            :class="{
                                'bg-blue-100 text-blue-700 ring-2 ring-blue-400':     !po.id,
                                'bg-amber-100 text-amber-700 ring-2 ring-amber-400':  po.id && po._dirty,
                                'bg-blue-50 text-blue-800 ring-2 ring-blue-200':      po.id && !po._dirty
                            }">
                            <span class="font-bold uppercase" x-text="po.po_code"></span>
                        </span>
                    </div>

                    {{-- Textarea --}}
                    <div class="flex-1 min-w-0">
                        <textarea x-model="po.po_text" @input="markDirty(po)" rows="3"
                            placeholder="Describe the ability or competency graduates will have by the time of graduation…"
                            class="w-full rounded-lg border px-3 py-2 text-[13px] text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition-all resize-none leading-relaxed"
                            :class="{
                                'border-amber-300 bg-amber-50/50 focus:border-amber-400 focus:ring-amber-100':   po.id && po._dirty,
                                'border-blue-300 bg-blue-50/50 focus:border-blue-400 focus:ring-blue-100':       !po.id,
                                'border-slate-200 bg-white focus:border-blue-400 focus:ring-blue-100':           po.id && !po._dirty
                            }"></textarea>

                        <template x-if="!po.id">
                            <p class="mt-1 flex items-center gap-1 text-[11px] text-blue-600 font-medium">
                                <i class="bx bx-plus-circle text-sm shrink-0"></i>
                                New — click <strong class="mx-0.5">Save All</strong> to persist.
                            </p>
                        </template>
                        <template x-if="po.id && po._dirty">
                            <p class="mt-1 flex items-center gap-1 text-[11px] text-amber-600 font-medium">
                                <i class="bx bx-edit-alt text-sm shrink-0"></i>
                                Modified — not saved yet.
                            </p>
                        </template>
                    </div>

                    {{-- Delete saved --}}
                    <button x-show="po.id" type="button" @click="deletePo(po)"
                        class="shrink-0 mt-0.5 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete PO">
                        <i class="bx bx-trash text-base"></i>
                    </button>

                    {{-- Remove unsaved --}}
                    <button x-show="!po.id" x-cloak type="button" @click="pos.splice(index, 1)"
                        class="shrink-0 mt-0.5 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Remove">
                        <i class="bx bx-x text-lg"></i>
                    </button>

                </div>

                {{-- PEO Mapping section --}}
                <div class="border-t border-t-gray-200 px-4 py-3 border-l-4 border-blue-700 rounded-bl-xl"
                    :class="po.id">

                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-1.5">
                            <i class="bx bx-link text-slate-400 text-sm"></i>
                            <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Maps to PEOs</span>
                            <template x-if="po.id && mappedCount(po.id) > 0">
                                <span class="inline-flex items-center justify-center min-w-[1.2rem] h-4 px-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold ring-1 ring-emerald-200"
                                    x-text="mappedCount(po.id)"></span>
                            </template>
                            <template x-if="po.id && hasMappingPending(po.id)">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-semibold animate-pulse">
                                    <i class="bx bx-loader-alt bx-spin text-xs"></i>
                                    saving…
                                </span>
                            </template>
                        </div>
                        <template x-if="po.id && peos.length > 0">
                            <label for="peo-reference-drawer"
                                class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold cursor-pointer hover:text-emerald-700 hover:underline">
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
                            <i class="bx bx-info-circle text-sm shrink-0"></i>
                            No PEOs defined yet — go to the PEOs tab to add them.
                        </p>
                    </template>

                    {{-- Mapping chips --}}
                    <template x-if="po.id && peos.length > 0">
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="peo in peos" :key="peo.id">
                                <label :title="peo.peo_text"
                                    :class="isMappingPending(po.id, peo.id)
                                        ? 'border-amber-300 bg-amber-50 text-amber-700 ring-1 ring-amber-200 opacity-75 cursor-wait'
                                        : isPoMappedToPeo(po.id, peo.id)
                                            ? 'border-emerald-400 bg-emerald-100 text-emerald-800 ring-1 ring-emerald-300 shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-500 hover:border-emerald-300 hover:bg-emerald-50/60 hover:text-emerald-700'"
                                    class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border px-2.5 py-1.5 text-[12px] font-semibold transition-all duration-100 select-none">
                                    {{-- <input type="checkbox"
                                        :checked="isPoMappedToPeo(po.id, peo.id)"
                                        @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                        :disabled="isMappingPending(po.id, peo.id)"
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200 focus:ring-1"> --}}
                                        <x-form.checkbox
                                            ::checked="isPoMappedToPeo(po.id, peo.id)"
                                            x-on:change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                            ::disabled="isMappingPending(po.id, peo.id)"
                                        />
                                        <span x-text="peo.peo_code.toUpperCase()"></span>
                                    <template x-if="isMappingPending(po.id, peo.id)">
                                        <svg class="animate-spin h-3 w-3 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    </template>
                                    {{-- <template x-if="isPoMappedToPeo(po.id, peo.id) && !isMappingPending(po.id, peo.id)">
                                        <i class="bx bx-check text-xs text-emerald-600"></i>
                                    </template> --}}
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
        <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 py-12 text-center">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 mb-3">
                <i class="bx bx-target-lock text-3xl text-slate-300"></i>
            </span>
            <p class="text-[13px] font-semibold text-slate-500">No POs yet</p>
            <p class="text-[12px] text-slate-400 mt-0.5">Click <strong>Add PO</strong> below to get started.</p>
        </div>
    </template>

    {{-- Action buttons --}}
    <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
        <x-button variant="add-dashed" type="button" @click="addPo()" class="flex-1">
            <i class="bx bx-plus text-base"></i> Add PO
        </x-button>

        <template x-if="hasPending()">
            <x-button variant="cancel" type="button" @click="revert()">
                <i class="bx bx-undo text-base leading-none"></i> Revert
            </x-button>
        </template>

        <x-button variant="add-button" type="button" @click="savePos()" x-bind:disabled="isSaving"
            class="whitespace-nowrap relative">
            <template x-if="hasPending()">
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-white animate-pulse"></span>
            </template>
            <span x-show="!isSaving" class="inline-flex items-center gap-1.5 leading-none">
                <i class="bx bx-save text-base leading-none"></i> Save All
            </span>
            <span x-show="isSaving" x-cloak class="inline-flex items-center gap-1.5 leading-none">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span class="leading-none">Saving…</span>
            </span>
        </x-button>
    </div>

    @include('livewire.programs.offcanvasReference')

    {{-- Hidden delete forms --}}
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
                    ...p, _dirty: false, _original: p.po_text, _key: p.id ?? ('new-' + i)
                })),
                peos: initialPeos,
                mapping: initialMapping,
                _mappingPending: {},
                isSaving: false,
                _keyCounter: initialPos.length,

                init() {
                    this.$el.closest('[wire\\:id]')?.addEventListener('pos-saved', (e) => {
                        const detail = e.detail[0] ?? e.detail;
                        const fresh = detail.pos ?? [];
                        this.pos = fresh.map((p, i) => ({
                            ...p, _dirty: false, _original: p.po_text, _key: p.id ?? ('new-' + i)
                        }));
                        this.mapping = detail.mapping ?? this.mapping;
                    });
                },

                markDirty(po) {
                    if (po.id) po._dirty = (po.po_text !== po._original);
                },

                hasPending() { return this.pos.some(p => !p.id || p._dirty); },

                pendingSummary() {
                    return {
                        added:    this.pos.filter(p => !p.id).length,
                        modified: this.pos.filter(p => p.id && p._dirty).length,
                        get total() { return this.added + this.modified; }
                    };
                },

                mappedCount(poId) { return (this.mapping[poId] ?? []).length; },

                isPoMappedToPeo(poId, peoId) {
                    return Array.isArray(this.mapping[poId]) && this.mapping[poId].includes(peoId);
                },

                isMappingPending(poId, peoId) { return !!this._mappingPending[poId + '-' + peoId]; },

                hasMappingPending(poId) {
                    return Object.keys(this._mappingPending).some(k => k.startsWith(poId + '-'));
                },

                revert() {
                    // Remove unsaved rows, reset dirty rows to their original text
                    this.pos = this.pos
                        .filter(p => p.id)
                        .map(p => ({ ...p, po_text: p._original, _dirty: false }));
                },

                addPo() {
                    if (this.pos.some(p => !p.po_text?.trim())) {
                        window.dispatchEvent(new CustomEvent('lw-toast', {
                            detail: { type: 'warning', message: 'Fill in the blank PO before adding another.' }
                        }));
                        return;
                    }
                    this._keyCounter++;
                    this.pos.push({ id: null, po_code: '', po_text: '', _dirty: false, _original: '', _key: 'new-' + this._keyCounter });
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
                            const p = { ...this._mappingPending };
                            delete p[key];
                            this._mappingPending = p;
                        })
                        .catch(() => {
                            this.mapping[poId] = checked
                                ? this.mapping[poId].filter(id => id !== peoId)
                                : [...new Set([...this.mapping[poId], peoId])];
                            const p = { ...this._mappingPending };
                            delete p[key];
                            this._mappingPending = p;
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
                        window.dispatchEvent(new CustomEvent('lw-toast', { detail: { type: 'info', message: 'No changes to save.' } }));
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
                        .catch(() => {})
                        .finally(() => { this.isSaving = false; });
                }
            };
        }
    </script>

</div>
