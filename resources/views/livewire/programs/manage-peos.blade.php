{{--
    manage-peos.blade.php
    Editable PEO list for a program.
    Livewire: ManagePeos  |  Alpine: peosManager()

    Row states:
      Saved   (peo.id truthy)  → emerald badge, trash submits DELETE to controller
      Unsaved (peo.id null)    → amber border + badge + "unsaved" hint, × removes from array

    Notifications: lw-toast only (no flash-message include — avoids double toasts)
--}}

<div x-data="peosManager(@entangle('peos'))" class="space-y-3">

    @include('livewire.programs.include.flash-message')
    
    {{-- ── PEO rows ────────────────────────────────────────────────────────── --}}
    <template x-for="(peo, index) in peos" :key="peo.id ?? ('new-' + index)">

        <div :class="peo.id
                ? 'border-slate-200 bg-white/90'
                : 'border-amber-200 bg-amber-50/40'"
            class="rounded-2xl border shadow-sm p-4 transition-colors duration-200">

            <div class="flex items-start gap-3">

                {{-- Code badge --}}
                <div class="shrink-0 pt-0.5">
                    <span :class="peo.id
                            ? 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'
                            : 'bg-amber-100 text-amber-700 ring-1 ring-amber-200'"
                        class="inline-flex items-center justify-center
                                w-10 h-10 rounded-xl text-xs font-bold uppercase
                                transition-colors duration-200">
                        {{-- Shows PEO1, PEO2 … matches the display in peo-display.blade --}}
                        <span x-text="'PEO' + (index + 1)"></span>
                    </span>
                </div>

                {{-- Textarea --}}
                <div class="flex-1 min-w-0">
                    <x-form.textarea
                        rows="3"
                        x-model="peo.peo_text"
                        placeholder="Describe what graduates will be professionally three to five years after graduation…" />

                    {{-- Unsaved hint --}}
                    <p x-show="!peo.id"
                        x-cloak
                        class="mt-1.5 flex items-center gap-1.5 text-xs text-amber-600">
                        <i class="bx bx-error-circle text-sm shrink-0"></i>
                        Unsaved — click <strong>Save All</strong> to persist this row.
                    </p>
                </div>

                {{-- DELETE — saved row: POST → controller (resequences codes) --}}
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
                        class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                               hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete saved PEO">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>

                {{-- REMOVE — unsaved row: just splice from array --}}
                <button x-show="!peo.id"
                    x-cloak
                    @click="peos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-400 hover:text-rose-600
                           hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved PEO">
                    <i class="bx bx-x text-base"></i>
                </button>

            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="peos.length === 0">
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50
                    py-10 text-center">
            <i class="bx bx-graduation text-4xl text-slate-300"></i>
            <p class="mt-2 text-sm text-slate-400">No PEOs yet. Add the first one below.</p>
        </div>
    </template>

    {{-- ── Action buttons ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 pt-1">

        <button @click="addPeo()" type="button"
            class="flex-1 inline-flex items-center justify-center gap-2
                   border-2 border-dashed border-emerald-300 rounded-2xl p-3
                   text-sm font-semibold text-emerald-700
                   hover:border-emerald-500 hover:bg-emerald-50
                   transition-colors duration-150">
            <i class="bx bx-plus text-base"></i>
            Add PEO
        </button>

        <button @click="savePeos()" type="button" :disabled="isSaving"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl
                    bg-emerald-600 text-white text-sm font-semibold shadow-sm
                    hover:bg-emerald-700 active:bg-emerald-800
                    disabled:opacity-50 disabled:cursor-not-allowed
                    transition-colors duration-150">
            <span x-show="!isSaving" class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i> Save All
            </span>
            <span x-show="isSaving" x-cloak class="inline-flex items-center gap-2">
                <i class="bx bx-loader-alt bx-spin text-base"></i> Saving…
            </span>
        </button>
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
