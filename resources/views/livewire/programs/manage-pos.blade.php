{{--
    manage-pos.blade.php — Editable PO list with PEO mapping.
    Livewire: ManagePos  |  Alpine: posManager()
--}}
<div>
<div x-data="posManager(@js($pos), @js($peos), @js($mapping))" class="space-y-2.5">

    @include('livewire.programs.include.flash-message')

    {{-- ── Pending changes summary bar ──────────────────────────────────── --}}
    <template x-if="pendingSummary().total > 0">
        <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-[13px]">
            <i class="bx bx-info-circle text-amber-500 shrink-0"></i>
            <span class="text-amber-700 font-medium">Unsaved changes:</span>
            <template x-if="pendingSummary().added > 0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[12px] font-semibold">
                    <i class="bx bx-plus text-xs"></i>
                    <span x-text="pendingSummary().added + ' new'"></span>
                </span>
            </template>
            <template x-if="pendingSummary().modified > 0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[12px] font-semibold">
                    <i class="bx bx-edit-alt text-xs"></i>
                    <span x-text="pendingSummary().modified + ' modified'"></span>
                </span>
            </template>
            <span class="ml-auto text-[12px] text-amber-600">Click <strong>Save All</strong> to apply.</span>
        </div>
    </template>

    {{-- ── PO rows ───────────────────────────────────────────────────────── --}}
    <template x-for="(po, index) in pos" :key="po._key">

        <div class="rounded-xl border overflow-hidden transition-colors duration-200"
            :class="{
                'border-blue-300 bg-blue-50/30':   !po.id,
                'border-amber-300 bg-amber-50/30': po.id && po._dirty,
                'border-[#e2e8f0] bg-white':       po.id && !po._dirty
            }"
            style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

            {{-- ── Text row ──────────────────────────────────────────────── --}}
            <div class="flex items-start gap-3 p-4">

                {{-- Code badge + state pill --}}
                <div class="shrink-0 pt-0.5 flex flex-col items-center gap-1">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-[13px] font-bold transition-colors duration-200"
                        :class="{
                            'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]': po.id && !po._dirty,
                            'bg-amber-100 text-amber-700 ring-1 ring-amber-300':  po.id && po._dirty,
                            'bg-blue-100 text-blue-700 ring-1 ring-blue-300':     !po.id
                        }">
                        <span x-text="'PO' + (index + 1)"></span>
                    </span>
                    <span x-show="!po.id" x-cloak
                        class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                        NEW
                    </span>
                    <span x-show="po.id && po._dirty" x-cloak
                        class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700">
                        EDITED
                    </span>
                </div>

                {{-- Textarea --}}
                <div class="flex-1 min-w-0">
                    <textarea
                        x-model="po.po_text"
                        @input="markDirty(po)"
                        rows="3"
                        placeholder="Describe the ability or competency graduates will have by the time of graduation…"
                        class="w-full rounded-lg border px-3 py-2 text-[13px] text-[#0f172a] placeholder:text-[#94a3b8]
                               focus:outline-none transition-colors resize-none"
                        :class="{
                            'border-amber-300 bg-amber-50/50 focus:border-amber-400': po.id && po._dirty,
                            'border-blue-300 bg-blue-50/50 focus:border-blue-400':    !po.id,
                            'border-[#e2e8f0] bg-white focus:border-[#16a34a]':       po.id && !po._dirty
                        }"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.18)'"
                        onblur="this.style.boxShadow='none'"></textarea>

                    <template x-if="po.id && po._dirty">
                        <p class="mt-1 flex items-center gap-1 text-[12px] text-amber-600">
                            <i class="bx bx-edit-alt text-sm shrink-0"></i>
                            Modified — not saved yet.
                        </p>
                    </template>
                    <template x-if="!po.id">
                        <p class="mt-1 flex items-center gap-1 text-[12px] text-blue-600">
                            <i class="bx bx-plus-circle text-sm shrink-0"></i>
                            New row — click <strong class="mx-0.5">Save All</strong> to persist.
                        </p>
                    </template>
                </div>

                {{-- DELETE saved --}}
                <form x-show="po.id"
                    method="POST"
                    :action="'/programs/po/' + po.id"
                    @submit.prevent="
                        if (confirm('Delete this PO? Codes will be re-sequenced.')) {
                            $el.submit();
                        }
                    ">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="mt-0.5 p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete saved PO">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>

                {{-- REMOVE unsaved --}}
                <button x-show="!po.id" x-cloak
                    @click="pos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved PO">
                    <i class="bx bx-x text-lg"></i>
                </button>

            </div>

            {{-- ── PEO mapping section ───────────────────────────────────── --}}
            <div class="border-t px-4 py-3 transition-colors"
                :class="po.id ? 'border-[#e2e8f0] bg-[#f8fafc]' : 'border-blue-200 bg-blue-50/20'">

                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        <i class="bx bx-link text-[#94a3b8] text-sm"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                            Maps to PEOs
                        </p>
                        <template x-if="po.id && mappedCount(po.id) > 0">
                            <span class="inline-flex items-center justify-center min-w-[1.2rem] h-4 px-1 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold"
                                x-text="mappedCount(po.id)"></span>
                        </template>
                    </div>
                    {{-- PEO reference off-canvas trigger --}}
                    <label for="peo-reference-drawer"
                        class="inline-flex items-center gap-1 text-[11px] text-[#16a34a] font-semibold cursor-pointer hover:underline">
                        <i class="bx bx-book-open text-sm"></i> View PEOs
                    </label>
                </div>

                <template x-if="!po.id">
                    <p class="flex items-center gap-1.5 text-[12px] text-blue-600 py-1">
                        <i class="bx bx-lock-alt text-sm shrink-0"></i>
                        Save this PO first to enable PEO mapping.
                    </p>
                </template>

                <template x-if="po.id && peos.length === 0">
                    <p class="flex items-center gap-1.5 text-[12px] text-slate-400 italic py-1">
                        No PEOs defined yet — go to the PEOs tab to add them.
                    </p>
                </template>

                <template x-if="po.id && peos.length > 0">
                    <div class="flex flex-wrap gap-1.5"
                        :class="!po.id ? 'opacity-40 pointer-events-none select-none' : ''">
                        <template x-for="peo in peos" :key="peo.id">
                            <label
                                :title="peo.peo_text ? peo.peo_text.substring(0, 100) + (peo.peo_text.length > 100 ? '…' : '') : ''"
                                :class="isPoMappedToPeo(po.id, peo.id)
                                    ? 'border-[#16a34a] bg-[#dcfce7] text-[#166534] ring-1 ring-[#16a34a]/30'
                                    : 'border-[#e2e8f0] bg-white text-[#475569] hover:border-[#bbf7d0] hover:bg-[#f0fdf4]'"
                                class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border
                                       px-2.5 py-1.5 text-[13px] font-semibold transition-all duration-100 select-none">
                                <input
                                    type="checkbox"
                                    :checked="isPoMappedToPeo(po.id, peo.id)"
                                    @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                    class="rounded border-[#e2e8f0] text-[#16a34a] focus:ring-[#bbf7d0] focus:ring-1">
                                <span x-text="peo.peo_code.toUpperCase()"></span>
                                <template x-if="isPoMappedToPeo(po.id, peo.id)">
                                    <i class="bx bx-check text-xs text-[#16a34a]"></i>
                                </template>
                            </label>
                        </template>
                    </div>
                </template>

            </div>

        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="pos.length === 0">
        <div class="rounded-xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] py-10 text-center">
            <i class="bx bx-target-lock text-4xl text-[#94a3b8]"></i>
            <p class="mt-2 text-[13px] font-semibold text-[#475569]">No POs yet</p>
            <p class="text-[13px] text-[#94a3b8] mt-0.5">Add the first one below.</p>
        </div>
    </template>

    {{-- ── Action buttons ────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 pt-1">

        <x-button variant="add-dashed" type="button" @click="addPo()" class="flex-1 w-full">
            <i class="bx bx-plus text-base"></i> Add PO
        </x-button>

        <x-button variant="add-button" type="button" @click="savePos()"
            x-bind:disabled="isSaving" class="whitespace-nowrap relative">
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
</div>{{-- end Alpine div --}}

@include('livewire.programs.offcanvasReference')

<script>
function posManager(initialPos, initialPeos, initialMapping) {
    return {
        pos:      initialPos.map((p, i) => ({ ...p, _dirty: false, _original: p.po_text, _key: p.id ?? ('new-' + i) })),
        peos:     initialPeos,
        mapping:  initialMapping,
        isSaving: false,
        _keyCounter: initialPos.length,

        markDirty(po) {
            if (po.id) po._dirty = po.po_text !== po._original;
        },

        hasPending() {
            return this.pos.some(p => !p.id || p._dirty);
        },

        pendingSummary() {
            const added    = this.pos.filter(p => !p.id).length;
            const modified = this.pos.filter(p => p.id && p._dirty).length;
            return { added, modified, total: added + modified };
        },

        mappedCount(poId) {
            return (this.mapping[poId] ?? []).length;
        },

        addPo() {
            const hasBlank = this.pos.some(p => !p.po_text || !p.po_text.trim());
            if (hasBlank) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'Fill in the blank PO before adding another.' }
                }));
                return;
            }
            this._keyCounter++;
            this.pos.push({ id: null, po_code: '', po_text: '', _dirty: false, _original: '', _key: 'new-' + this._keyCounter });
        },

        isPoMappedToPeo(poId, peoId) {
            const mapped = this.mapping[poId];
            return Array.isArray(mapped) && mapped.includes(peoId);
        },

        toggleMapping(poId, peoId, checked) {
            if (!this.mapping[poId]) this.mapping[poId] = [];
            if (checked) {
                if (!this.mapping[poId].includes(peoId)) {
                    this.mapping[poId] = [...this.mapping[poId], peoId];
                }
            } else {
                this.mapping[poId] = this.mapping[poId].filter(id => id !== peoId);
            }
            if (poId) {
                @this.call('toggleMapping', poId, peoId, checked)
                    .catch(() => {
                        window.dispatchEvent(new CustomEvent('lw-toast', {
                            detail: { type: 'error', message: 'Mapping update failed. Please try again.' }
                        }));
                        if (checked) {
                            this.mapping[poId] = this.mapping[poId].filter(id => id !== peoId);
                        } else {
                            if (!this.mapping[poId].includes(peoId)) {
                                this.mapping[poId] = [...this.mapping[poId], peoId];
                            }
                        }
                    });
            }
        },

        savePos() {
            this.isSaving = true;
            @this.call('savePos', this.pos, this.mapping)
                .then(() => {
                    this.$nextTick(() => {
                        this.pos = this.pos.map(p => ({ ...p, _dirty: false, _original: p.po_text }));
                    });
                })
                .finally(() => { this.isSaving = false; });
        }
    };
}
</script>
</div>{{-- end root wrapper --}}
