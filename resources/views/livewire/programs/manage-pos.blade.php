<div x-data="posManager(@entangle('pos'), @entangle('peos'), @entangle('mapping'))" class="space-y-4">

    {{-- Flash message --}}
    <template x-if="flashMessage">
        <div class="p-2 rounded border border-green-300 bg-green-50 text-green-800 text-sm font-medium"
            x-text="flashMessage">
        </div>
    </template>

    {{-- Loading indicator --}}
    <div x-show="isSaving"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-50 p-2 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 text-sm font-medium animate-pulse">
        <i class='bx bx-loader bx-spin mr-2'></i> Saving POs...
    </div>

    {{-- PO Inputs with PEO checkboxes --}}
    <template x-for="(po, index) in pos" :key="index">
        <div class="space-y-2 p-3 border border-gray-200 rounded-lg">

            {{-- PO Input Row --}}
            <div class="flex items-center gap-3">
                <span class="w-16 text-center font-semibold text-gray-700"
                    {{-- x-text="peo.peo_code + index" --}}
                    x-text="'PO' + (index + 1)">
                </span>

                <input type="text" x-model="po.po_text" placeholder="Enter PO description"
                    class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

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
                            class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-100 transition"
                            title="Delete PO">
                            <i class='bx bx-trash'></i>
                        </button>
                    </form>
                </template>

                <template x-if="!po.id">
                    <button @click="pos.splice(index, 1)" type="button"
                        class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-100 transition"
                        title="Remove unsaved PO">
                        <i class='bx bx-trash'></i>
                    </button>
                </template>

            </div>

            {{-- PEO Mapping Checkboxes --}}
            <div class="pl-9">
                <div class="text-xs text-gray-600 mb-1">Map to PEOs:</div>
                <div class="flex flex-wrap gap-3">
                    <template x-for="peo in peos" :key="peo.id">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" :checked="isPoMappedToPeo(po.id, peo.id)"
                                @change="toggleMapping(po.id, peo.id, $event.target.checked)"
                                class="rounded border-gray-300">
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
            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-medium">
            <i class='bx bx-plus'></i> Add PO
        </button>

        <button @click="savePos()" type="button" :disabled="isSaving"
            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
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
