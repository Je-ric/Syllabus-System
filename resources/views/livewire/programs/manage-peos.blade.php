<div x-data="peosManager(@entangle('peos'))" class="space-y-4">

    {{-- Flash message --}}
    <template x-if="flashMessage">
        <div class="p-2 rounded border border-green-300 bg-green-50 text-green-800 text-sm font-medium" x-text="flashMessage"></div>
    </template>

    {{-- Loading indicator --}}
    <div x-show="isSaving" class="p-2 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 text-sm font-medium animate-pulse">
        <i class='bx bx-loader bx-spin mr-2'></i> Saving PEOs...
    </div>

    {{-- PEO Inputs --}}
    <template x-for="(peo, index) in peos" :key="index">
        <div class="flex items-center gap-3 p-2 border border-gray-200 rounded-lg">

            {{-- Index number --}}
            <span class="w-6 text-center font-semibold text-gray-700" x-text="index + 1"></span>

            {{-- PEO text input --}}
            <input
                type="text"
                x-model="peo.peo_text"
                placeholder="Enter PEO description"
                class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            {{-- Remove button --}}
            <button
                @click="removePeo(index)"
                type="button"
                class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-100 transition"
                title="Remove PEO"
            >
                <i class='bx bx-trash'></i>
            </button>
        </div>
    </template>

    {{-- Add new PEO --}}
    <button
        @click="addPeo()"
        type="button"
        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-medium"
    >
        <i class='bx bx-plus'></i> Add PEO
    </button>

    {{-- Save all PEOs --}}
    <button
        @click="savePeos()"
        type="button"
        :disabled="isSaving"
        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <i class='bx bx-save'></i> Save All
    </button>

</div>

<script>
function peosManager(initialPeos) {
    return {
        peos: initialPeos,
        flashMessage: '',
        isSaving: false,

        addPeo() {
            this.peos.push({ id: null, peo_code: '', peo_text: '' });
        },

        removePeo(index) {
            const peo = this.peos[index];
            const peoText = (peo && peo.peo_text) ? peo.peo_text : '(empty)';

            if (confirm(`Are you sure you want to delete this PEO: "${peoText}"?`)) {
                // If already persisted, call Livewire delete first
                if (peo && peo.id) {
                    this.isSaving = true;
                    @this.call('deletePeo', peo.id)
                        .then(() => {
                            this.peos.splice(index, 1);
                            this.flashMessage = 'PEO deleted successfully!';
                            // Optional: sync other changes
                            this.savePeos();
                        })
                        .catch(() => {
                            this.flashMessage = 'Error deleting PEO!';
                        })
                        .finally(() => {
                            this.isSaving = false;
                            setTimeout(() => this.flashMessage = '', 3000);
                        });
                } else {
                    // Not yet saved - just remove locally
                    this.peos.splice(index, 1);
                }
            }
        },

        savePeos() {
            this.isSaving = true;
            @this.call('savePeos', this.peos)
                .then(() => {
                    this.flashMessage = 'PEOs saved successfully!';
                })
                .catch(() => {
                    this.flashMessage = 'Error saving PEOs!';
                })
                .finally(() => {
                    this.isSaving = false;
                    setTimeout(() => this.flashMessage = '', 3000);
                });
        }
    }
}
</script>
