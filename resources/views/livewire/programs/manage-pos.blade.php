<div x-data="posManager(@entangle('pos'), @entangle('peos'), @entangle('mapping'))" class="space-y-4 text-slate-800">

    {{-- Notification stack --}}
    {{-- If both are visible → they stack neatly
    If one disappears → layout auto-adjusts --}}
    <div class="fixed top-4 inset-x-0 z-50 flex justify-center pointer-events-none">
        <div class="flex flex-col gap-2 items-center">

            {{-- Loading indicator --}}
            <div x-show="isSaving"
                class="px-4 py-2 rounded-full border border-amber-200 bg-amber-50 text-amber-900 text-xs font-semibold tracking-wide shadow-sm animate-pulse">
                <i class='bx bx-loader-alt bx-spin mr-2'></i> Saving POs...
            </div>

            {{-- Flash message --}}
            <template x-if="flashMessage">
                <div
                    class="px-4 py-2 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs font-semibold tracking-wide shadow-sm"
                    x-text="flashMessage">
                </div>
            </template>

        </div>
    </div>

    {{-- PO Inputs with PEO checkboxes --}}
    <template x-for="(po, index) in pos" :key="index">
        <div class="space-y-2 rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">

            {{-- PO Input Row --}}
            <div class="flex items-center gap-3">
                <span class="w-16 text-center text-xs uppercase tracking-[0.2em] text-slate-500"
                    {{-- x-text="peo.peo_code + index" --}}
                    x-text="'PO' + (index + 1)">
                </span>

                <x-form.textarea x-model="po.po_text"
                        placeholder="Enter PO description"
                        rows="3">
                </x-form.textarea>

                <template x-if="po.id">
                    <form method="POST" :action="'/programs/po/' + po.id"
                        @submit.prevent="
                                if (confirm('Are you sure you want to delete this PO?')) {
                                    $el.submit()
                                }
                            ">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="p-2 text-rose-600 hover:text-rose-800 rounded-full hover:bg-rose-100 transition"
                            title="Delete PO">
                            <i class='bx bx-trash'></i>
                        </button>
                    </form>
                </template>

                <template x-if="!po.id">
                    <button @click="pos.splice(index, 1)" type="button"
                        class="p-2 text-rose-600 hover:text-rose-800 rounded-full hover:bg-rose-100 transition"
                        title="Remove unsaved PO">
                        <i class='bx bx-trash'></i>
                    </button>
                </template>

            </div>

            {{-- PEO Mapping Checkboxes --}}
            <div class="pl-9">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-2">Map to PEOs</div>
                <div class="flex flex-wrap gap-3">
                    <template x-for="peo in peos" :key="peo.id">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" :checked="isPoMappedToPeo(po.id, peo.id)"
                                @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
                            <span x-text="peo.peo_code"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-2">
        <button @click="addPo()" type="button"
            class="
                w-full
                border-2 border-dashed border-emerald-300
                rounded-2xl p-4
                text-sm font-semibold text-emerald-700
                hover:border-emerald-500
                hover:bg-emerald-50
                transition
                flex items-center justify-center gap-2
            ">
            <i class='bx bx-plus'></i> Add PO
        </button>

        <button @click="savePos()" type="button" :disabled="isSaving"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <i class='bx bx-save'></i> Save All
        </button>
    </div>

</div>

<script>
    function posManager(initialPos, initialPeos, initialMapping) {
        return {
            pos: initialPos,
            peos: initialPeos,
            mapping: initialMapping,
            flashMessage: '',
            isSaving: false,

            // Add a new empty PO
            addPo() {
                const hasBlank = this.pos.some(po => !po.po_text || po.po_text.trim() === '');
                if (hasBlank) {
                    this.flashMessage = 'Please fill the blank PO before adding a new one.';
                    setTimeout(() => {
                        this.flashMessage = '';
                    }, 3000);
                    return;
                }

                this.pos.push({
                    id: null,
                    po_code: '',
                    po_text: ''
                });
            },

            // Check if a PO is mapped to a PEO
            isPoMappedToPeo(poId, peoId) {
                // Check if mapping exists for this PO
                if (!this.mapping[poId]) {
                    return false;
                }

                // Check if the PEO ID is in the array
                const mappedPeoIds = this.mapping[poId];
                for (let i = 0; i < mappedPeoIds.length; i++) {
                    if (mappedPeoIds[i] === peoId) {
                        return true;
                    }
                }

                return false;
            },

            // Toggle PEO mapping for a PO
            toggleMapping(poId, peoId, checked) {
                // Get current mappings for this PO
                let currentMappings = this.mapping[poId];

                // Initialize if doesn't exist
                if (!currentMappings) {
                    currentMappings = [];
                }

                if (checked) {
                    // Add PEO if not already in the list
                    let alreadyExists = false;
                    for (let i = 0; i < currentMappings.length; i++) {
                        if (currentMappings[i] === peoId) {
                            alreadyExists = true;
                            break;
                        }
                    }

                    if (!alreadyExists) {
                        currentMappings.push(peoId);
                    }
                } else {
                    // Remove PEO from the list
                    const newMappings = [];
                    for (let i = 0; i < currentMappings.length; i++) {
                        if (currentMappings[i] !== peoId) {
                            newMappings.push(currentMappings[i]);
                        }
                    }
                    currentMappings = newMappings;
                }

                // Update mapping
                this.mapping[poId] = currentMappings;
            },

            // Save all POs
            savePos() {
                this.isSaving = true;

                @this.call('savePos', this.pos, this.mapping)
                    .then(() => {
                        this.flashMessage = 'POs saved successfully!';
                    })
                    .catch(() => {
                        this.flashMessage = 'Error saving POs!';
                    })
                    .finally(() => {
                        this.isSaving = false;
                        setTimeout(() => {
                            this.flashMessage = '';
                        }, 3000);
                    });
            }
        }
    }
</script>
