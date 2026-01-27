<div x-data="posManager(@entangle('pos'),
                        @entangle('peos'),
                        @entangle('mapping'))"
                        class="space-y-4">

    {{-- Flash message --}}
    <template x-if="flashMessage">
        <div class="p-2 rounded border border-green-300 bg-green-50 text-green-800 text-sm font-medium" x-text="flashMessage"></div>
    </template>

    {{-- Loading indicator --}}
    <div x-show="isSaving" class="p-2 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 text-sm font-medium animate-pulse">
        <i class='bx bx-loader bx-spin mr-2'></i> Saving POs...
    </div>

    {{-- PO Inputs with PEO checkboxes --}}
    <template x-for="(po, index) in pos" :key="index">
        <div class="space-y-2 p-3 border border-gray-200 rounded-lg">
            <div class="flex items-center gap-3">
                <span class="w-6 text-center font-semibold text-gray-700" x-text="index + 1"></span>
                <input
                    type="text"
                    x-model="po.po_text"
                    placeholder="Enter PO description"
                    class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                >

                <button
                    @click="removePo(index)"
                    type="button"
                    class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-100 transition"
                    title="Remove PO"
                >
                    <i class='bx bx-trash'></i>
                </button>
            </div>

            <div class="pl-9">
                <div class="text-xs text-gray-600 mb-1">Map to PEOs:</div>
                <div class="flex flex-wrap gap-3">
                    <template x-for="peo in peos" :key="peo.id">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox"
                                    :checked="(mapping[po.id] || []).includes(peo.id)"
                                    @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                    class="rounded border-gray-300">
                                <span x-text="peo.peo_code"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <div class="flex items-center gap-2">
        <button
            @click="addPo()"
            type="button"
            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-medium"
        >
            <i class='bx bx-plus'></i> Add PO
        </button>

        <button
            @click="savePos()"
            type="button"
            :disabled="isSaving"
            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
        >
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

        addPo() {
            this.pos.push({ id: null, po_code: '', po_text: '' });
        },

        removePo(index) {
            const po = this.pos[index];
            const poText = (po && po.po_text) ? po.po_text : '(empty)';
            if (confirm(`Are you sure you want to delete this PO: "${poText}"?`)) {
                if (po && po.id) {
                    this.isSaving = true;
                    @this.call('deletePo', po.id)
                        .then(() => {
                            this.pos.splice(index, 1);
                            delete this.mapping[po.id];
                            this.flashMessage = 'PO deleted successfully!';
                            this.savePos();
                        })
                        .catch(() => {
                            this.flashMessage = 'Error deleting PO!';
                        })
                        .finally(() => {
                            this.isSaving = false;
                            setTimeout(() => this.flashMessage = '', 3000);
                        });
                } else {
                    this.pos.splice(index, 1);
                }
            }
        },

        toggleMapping(poId, peoId, checked) {
            const current = this.mapping[poId] || [];
            if (checked) {
                if (!current.includes(peoId)) current.push(peoId);
            } else {
                this.mapping[poId] = current.filter(id => id !== peoId);
                return;
            }
            this.mapping[poId] = current;
        },

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
                    setTimeout(() => this.flashMessage = '', 3000);
                });
        }
    }
}
</script>
