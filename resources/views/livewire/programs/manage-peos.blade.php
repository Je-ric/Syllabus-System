{{--
    manage-peos.blade.php — Editable PEO list for a program.
    Livewire: ManagePeos  |  Alpine: peosManager()
--}}

<div x-data="peosManager(@entangle('peos'))" class="space-y-2.5">

    @include('livewire.programs.include.flash-message')

    {{-- ── PEO rows ──────────────────────────────────────────────────────── --}}
    <template x-for="(peo, index) in peos" :key="peo.id ?? ('new-' + index)">

        <div :class="peo.id
                ? 'border-slate-200 bg-white'
                : 'border-amber-300 bg-amber-50/50'"
            class="rounded-xl border shadow-sm p-4 transition-colors duration-200">

            <div class="flex items-start gap-3">

                {{-- Code badge --}}
                <div class="shrink-0 pt-0.5">
                    <span :class="peo.id
                            ? 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'
                            : 'bg-amber-100 text-amber-700 ring-1 ring-amber-300'"
                        class="inline-flex items-center justify-center
                                w-10 h-10 rounded-xl text-xs font-bold
                                transition-colors duration-200">
                        <span x-text="'PEO' + (index + 1)"></span>
                    </span>
                </div>

                {{-- Textarea --}}
                <div class="flex-1 min-w-0">
                    <x-form.textarea
                        rows="3"
                        x-model="peo.peo_text"
                        placeholder="Describe what graduates will be professionally three to five years after graduation…" />

                    <p x-show="!peo.id" x-cloak
                        class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-600">
                        <i class="bx bx-error-circle text-sm shrink-0"></i>
                        Unsaved — click <strong class="mx-0.5">Save All</strong> to persist this row.
                    </p>
                </div>

                {{-- DELETE saved row --}}
                <form x-show="peo.id"
                    method="POST"
                    :action="'/programs/peo/' + peo.id"
                    @submit.prevent="
                        if (confirm('Delete this PEO? All codes will be re-sequenced.')) {
                            $el.submit();
                        }
                    ">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="mt-0.5 p-2 text-slate-300 hover:text-rose-600
                               hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete saved PEO">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>

                {{-- REMOVE unsaved row --}}
                <button x-show="!peo.id" x-cloak
                    @click="peos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-300 hover:text-rose-600
                           hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved PEO">
                    <i class="bx bx-x text-lg"></i>
                </button>

            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="peos.length === 0">
        <x-empty-state
            icon="graduation"
            title="No PEOs yet"
            description="Add the first one below." />
    </template>

    {{-- ── Action buttons ────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 pt-1">

        <x-button variant="add-dashed" type="button" @click="addPeo()" class="flex-1 w-full">
            <i class="bx bx-plus"></i> Add PEO
        </x-button>

        <x-button variant="add-button" type="button" @click="savePeos()"
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
function peosManager(initialPeos) {
    return {
        peos:     initialPeos,
        isSaving: false,

        addPeo() {
            const hasBlank = this.peos.some(p => !p.peo_text || !p.peo_text.trim());
            if (hasBlank) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'Fill in the blank PEO before adding another.' }
                }));
                return;
            }
            this.peos.push({ id: null, peo_code: '', peo_text: '' });
        },

        savePeos() {
            this.isSaving = true;
            @this.call('savePeos', this.peos)
                .finally(() => { this.isSaving = false; });
        }
    };
}
</script>
