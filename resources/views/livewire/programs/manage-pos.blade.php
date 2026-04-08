{{--
    manage-pos.blade.php — Editable PO list with PEO mapping checkboxes.
    Livewire: ManagePos  |  Alpine: posManager()
--}}
<div x-data="posManager(@entangle('pos'), @entangle('peos'), @entangle('mapping'))"
    class="space-y-2.5">

    @include('livewire.programs.include.flash-message')

    {{-- ── PO rows ───────────────────────────────────────────────────────── --}}
    <template x-for="(po, index) in pos" :key="po.id ?? ('new-' + index)">

        <div :class="po.id
                ? 'border-[#e2e8f0] bg-white'
                : 'border-[#fcd34d] bg-[#fffbeb]/50'"
            class="rounded-xl border overflow-hidden transition-colors duration-200"
            style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

            {{-- ── Text row ──────────────────────────────────────────────── --}}
            <div class="flex items-start gap-3 p-4">

                {{-- Code badge --}}
                <div class="shrink-0 pt-0.5">
                    <span :class="po.id
                            ? 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]'
                            : 'bg-[#fef3c7] text-[#92400e] ring-1 ring-[#fcd34d]'"
                        class="inline-flex items-center justify-center
                               w-10 h-10 rounded-xl text-[13px] font-bold
                               transition-colors duration-200">
                        <span x-text="'PO' + (index + 1)"></span>
                    </span>
                </div>

                {{-- Textarea --}}
                <div class="flex-1 min-w-0">
                    <x-form.textarea
                        rows="3"
                        x-model="po.po_text"
                        placeholder="Describe the ability or competency graduates will have by the time of graduation…" />

                    <p x-show="!po.id" x-cloak
                        class="mt-1.5 flex items-center gap-1.5 text-[13px] text-[#92400e]">
                        <i class="bx bx-error-circle text-sm shrink-0"></i>
                        Unsaved — <strong class="mx-0.5">Save All</strong> first before mapping PEOs.
                    </p>
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
                        class="mt-0.5 p-2 text-slate-300 hover:text-rose-600
                               hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete saved PO">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>

                {{-- REMOVE unsaved --}}
                <button x-show="!po.id" x-cloak
                    @click="pos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-300 hover:text-rose-600
                           hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved PO">
                    <i class="bx bx-x text-lg"></i>
                </button>

            </div>

            {{-- ── PEO mapping section ───────────────────────────────────── --}}
            <div :class="po.id ? 'border-[#e2e8f0] bg-[#f8fafc]' : 'border-[#fcd34d] bg-[#fffbeb]/30'"
                class="border-t px-4 py-3">

                <div :class="!po.id ? 'opacity-40 pointer-events-none select-none' : ''"
                    class="transition-opacity duration-150">

                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                            Map to PEOs
                        </p>
                        <template x-if="peos.length === 0">
                            <span class="text-xs text-slate-400 italic">
                                — no PEOs defined yet
                            </span>
                        </template>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="peo in peos" :key="peo.id">
                            <label
                                :title="peo.peo_text ? peo.peo_text.substring(0, 80) + (peo.peo_text.length > 80 ? '…' : '') : ''"
                                :class="isPoMappedToPeo(po.id, peo.id)
                                    ? 'border-[#16a34a] bg-[#dcfce7] text-[#166534]'
                                    : 'border-[#e2e8f0] bg-white text-[#475569] hover:border-[#bbf7d0] hover:bg-[#f0fdf4]'"
                                class="inline-flex items-center gap-1.5 cursor-pointer
                                       rounded-lg border px-2.5 py-1.5 text-[13px] font-semibold
                                       transition-all duration-100 select-none">
                                <input
                                    type="checkbox"
                                    :checked="isPoMappedToPeo(po.id, peo.id)"
                                    @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                    class="rounded border-[#e2e8f0] text-[#16a34a] focus:ring-[#bbf7d0] focus:ring-1">
                                <span x-text="peo.peo_code.toUpperCase()"></span>
                            </label>
                        </template>
                    </div>

                </div>

                <p x-show="!po.id" x-cloak
                    class="mt-1.5 flex items-center gap-1.5 text-[13px] text-[#92400e]">
                    <i class="bx bx-lock-alt text-sm shrink-0"></i>
                    Save this PO first to enable PEO mapping.
                </p>
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
            x-bind:disabled="isSaving" class="whitespace-nowrap">
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
</div>

<script>
function posManager(initialPos, initialPeos, initialMapping) {
    return {
        pos:      initialPos,
        peos:     initialPeos,
        mapping:  initialMapping,
        isSaving: false,

        addPo() {
            const hasBlank = this.pos.some(p => !p.po_text || !p.po_text.trim());
            if (hasBlank) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'Fill in the blank PO before adding another.' }
                }));
                return;
            }
            this.pos.push({ id: null, po_code: '', po_text: '' });
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
                .finally(() => { this.isSaving = false; });
        }
    };
}
</script>
