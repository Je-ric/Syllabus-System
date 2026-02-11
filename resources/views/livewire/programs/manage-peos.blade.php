<div x-data="peosManager(@entangle('peos'))" class="space-y-4 text-slate-800">

    {{-- Notification stack --}}
    {{-- If both are visible → they stack neatly
    If one disappears → layout auto-adjusts --}}
    <div class="fixed top-4 inset-x-0 z-50 flex justify-center pointer-events-none">
        <div class="flex flex-col gap-2 items-center">

            {{-- Loading indicator --}}
            <div x-show="isSaving"
                class="px-4 py-2 rounded-full border border-amber-200 bg-amber-50 text-amber-900 text-xs font-semibold tracking-wide shadow-sm animate-pulse">
                <i class='bx bx-loader-alt bx-spin mr-2'></i> Saving PEOs...
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

    <template x-for="(peo, index) in peos"
            :key="peo.id ?? index">
        <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-2xl bg-white/90 shadow-sm">
            <span class="w-16 text-center text-xs uppercase tracking-[0.2em] text-slate-500"
                {{-- x-text="peo.peo_code + index" --}}
                x-text="'PEO' + (index + 1)">
            </span>

            <x-form.textarea rows="3"
                    x-model="peo.peo_text"
                    placeholder="Enter PEO description">
            </x-form.textarea>

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
                    class="p-2 text-rose-600 hover:text-rose-800 rounded-full hover:bg-rose-100 transition">
                    <i class='bx bx-trash'></i>
                </button>
            </form>

            <button x-show="!peo.id" @click="peos.splice(index, 1)" type="button"
                class="p-2 text-rose-600 hover:text-rose-800 rounded-full hover:bg-rose-100 transition">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    </template>


    <div class="flex items-center gap-2">
        <button @click="addPeo()" type="button"
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
            <i class='bx bx-plus'></i> Add PEO
        </button>

        <button @click="savePeos()" type="button" :disabled="isSaving"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <i class='bx bx-save'></i> Save All
        </button>
    </div>

</div>

<script>
    function peosManager(initialPeos) {
        return {
            peos: initialPeos,
            isSaving: false,
            flashMessage: '',

            addPeo() {
                const hasBlank = this.peos.some(peo => !peo.peo_text || peo.peo_text.trim() === '');
                if (hasBlank) {
                    this.flashMessage = 'Please fill the blank PEO before adding a new one.';
                    setTimeout(() => this.flashMessage = '', 3000);
                    return;
                }

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
