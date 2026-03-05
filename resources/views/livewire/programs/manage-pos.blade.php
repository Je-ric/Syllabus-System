{{--
    manage-pos.blade.php
    Editable PO list with PEO mapping checkboxes.
    Livewire: ManagePos  |  Alpine: posManager()

    Row states:
      Saved   (po.id truthy) → emerald badge, mapping checkboxes active, trash → controller
      Unsaved (po.id null)   → amber badge + border, mapping section disabled with warning,
                               × removes from array

    KEY BEHAVIOUR FIX:
    Mapping checkboxes on unsaved rows (po.id === null) are visually disabled.
    The underlying toggleMapping() already skips the @this.call when poId is falsy,
    but we now also visually communicate this to the user instead of silently failing.

    PEO codes in checkboxes display as uppercase letters (A, B, C…) matching peo-display.
    A tooltip on each checkbox shows the first 60 chars of the PEO text for context.

    Notifications: lw-toast only.
--}}
<div x-data="posManager(@entangle('pos'), @entangle('peos'), @entangle('mapping'))"
    class="space-y-3">

    @include('livewire.programs.include.flash-message')


    {{-- ── PO rows ─────────────────────────────────────────────────────────── --}}
    <template x-for="(po, index) in pos" :key="po.id ?? ('new-' + index)">

        <div :class="po.id
                ? 'border-slate-200 bg-white/90'
                : 'border-amber-200 bg-amber-50/40'"
            class="border shadow-sm p-4 transition-colors duration-200">

            {{-- ── Text row ────────────────────────────────────────────────── --}}
            <div class="flex items-start gap-3">

                {{-- Code badge --}}
                <div class="shrink-0 pt-0.5">
                    <span :class="po.id
                            ? 'bg-blue-100 text-blue-700 ring-1 ring-blue-200'
                            : 'bg-amber-100 text-amber-700 ring-1 ring-amber-200'"
                        class="inline-flex items-center justify-center
                               w-10 h-10 rounded-xl text-xs font-bold uppercase
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

                    {{-- Unsaved hint --}}
                    <p x-show="!po.id"
                        x-cloak
                        class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-600">
                        <i class="bx bx-error-circle text-sm shrink-0"></i>
                        Unsaved — <strong>Save All</strong> first before mapping PEOs.
                    </p>
                </div>

                {{-- DELETE — saved: POST → controller --}}
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
                        class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                               hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete saved PO">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>

                {{-- REMOVE — unsaved: splice --}}
                <button x-show="!po.id"
                    x-cloak
                    @click="pos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                           hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved PO">
                    <i class="bx bx-x text-base"></i>
                </button>

            </div>

            {{-- ── PEO mapping section ─────────────────────────────────────── --}}
            <div class="mt-3 ml-13 pl-1">
                {{--
                    Separator between text input and mapping.
                    Disabled overlay when row is unsaved.
                --}}
                <div :class="!po.id ? 'opacity-40 pointer-events-none select-none' : ''"
                    class="transition-opacity duration-150">

                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">
                            Map to PEOs
                        </span>
                        <template x-if="peos.length === 0">
                            <span class="text-xs text-slate-400 italic">
                                — no PEOs defined yet (add them in the PEOs tab)
                            </span>
                        </template>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <template x-for="peo in peos" :key="peo.id">
                            {{--
                                title attribute shows PEO text as tooltip (first 80 chars).
                                peo_code from DB is lowercase letter; we uppercase for display.
                            --}}
                            <label
                                :title="peo.peo_text ? peo.peo_text.substring(0, 80) + (peo.peo_text.length > 80 ? '…' : '') : ''"
                                :class="isPoMappedToPeo(po.id, peo.id)
                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-700'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-emerald-200 hover:bg-emerald-50/50'"
                                class="inline-flex items-center gap-1.5 cursor-pointer
                                       rounded-lg border px-2.5 py-1.5 text-xs font-semibold
                                       transition-colors duration-100">
                                <input
                                    type="checkbox"
                                    :checked="isPoMappedToPeo(po.id, peo.id)"
                                    @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200 focus:ring-1">
                                {{-- Uppercase letter badge --}}
                                <span x-text="peo.peo_code.toUpperCase()"></span>
                            </label>
                        </template>
                    </div>

                </div>

                {{-- Disabled overlay message for unsaved rows --}}
                <p x-show="!po.id"
                    x-cloak
                    class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-600">
                    <i class="bx bx-lock-alt text-sm shrink-0"></i>
                    Save this PO first to enable PEO mapping.
                </p>
            </div>

        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="pos.length === 0">
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50
                    py-10 text-center">
            <i class="bx bx-target-lock text-4xl text-slate-300"></i>
            <p class="mt-2 text-sm text-slate-400">No POs yet. Add the first one below.</p>
        </div>
    </template>

    {{-- ── Action buttons ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 pt-1">

        <x-button
            variant="add-dashed"
            type="button"
            @click="addPo()"
            class="flex-1 w-full">
            <i class="bx bx-plus text-base"></i>
            Add PO
        </x-button>

        <x-button
            variant="save"
            type="button"
            @click="savePos()"
            x-bind:disabled="isSaving"
            class="whitespace-nowrap">
            <span x-show="!isSaving" class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i> Save All
            </span>
            <span x-show="isSaving" x-cloak class="inline-flex items-center gap-2">
                <i class="bx bx-loader-alt bx-spin text-base"></i> Saving…
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
            // Update local state first for instant UI feedback
            if (!this.mapping[poId]) this.mapping[poId] = [];

            if (checked) {
                if (!this.mapping[poId].includes(peoId)) {
                    this.mapping[poId] = [...this.mapping[poId], peoId];
                }
            } else {
                this.mapping[poId] = this.mapping[poId].filter(id => id !== peoId);
            }

            // Only persist to server for saved rows (poId is truthy)
            if (poId) {
                @this.call('toggleMapping', poId, peoId, checked)
                    .catch(() => {
                        window.dispatchEvent(new CustomEvent('lw-toast', {
                            detail: { type: 'error', message: 'Mapping update failed. Please try again.' }
                        }));
                        // Revert optimistic update on failure
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
