<div x-data="peosManager(@entangle('peos'))" class="space-y-4">

    {{-- Flash message --}}
    <template x-if="flashMessage">
        <div class="p-2 rounded border border-green-300 bg-green-50 text-green-800 text-sm font-medium"
            x-text="flashMessage">
        </div>
    </template>

    {{-- Loading indicator --}}
    <div x-show="isSaving"
        class="p-2 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 text-sm font-medium animate-pulse">
        <i class='bx bx-loader bx-spin mr-2'></i> Saving PEOs...
    </div>

    <template x-for="(peo, index) in peos"
            :key="peo.id ?? index">
        <div class="flex items-center gap-3 p-2 border border-gray-200 rounded-lg">
            <span class="w-16 text-center font-semibold text-gray-700"
                {{-- x-text="peo.peo_code + index" --}}
                x-text="'PEO' + (index + 1)">
            </span>

            <input type="text"
                    x-model="peo.peo_text"
                    placeholder="Enter PEO description"
                    class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">

            <form x-show="peo.id"
                    method="POST"
                    action="{{ route('programs.peo.delete', '__ID__') }}"
                x-bind:action="'/programs/peo/' + peo.id"
                @submit.prevent="
                if (confirm('Delete this PEO?')) $el.submit()
            ">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-100 transition">
                    <i class='bx bx-trash'></i>
                </button>
            </form>

            <!-- Unsaved PEO -->
            <button x-show="!peo.id" @click="peos.splice(index, 1)" type="button"
                class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-100 transition">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    </template>


    <button @click="addPeo()" type="button"
        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-medium">
        <i class='bx bx-plus'></i> Add PEO
    </button>

    <button @click="savePeos()" type="button" :disabled="isSaving"
        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
        <i class='bx bx-save'></i> Save All
    </button>

</div>

<script>
    function peosManager(initialPeos) {
        return {
            peos: initialPeos,
            isSaving: false,

            addPeo() {
                this.peos.push({
                    id: null,
                    peo_code: '',
                    peo_text: ''
                });
            },

            savePeos() {
                this.isSaving = true;

                @this.call('savePeos', this.peos)
                    .finally(() => this.isSaving = false);
            }
        }
    }
</script>
