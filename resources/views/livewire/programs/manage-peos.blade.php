{{--
    manage-peos.blade.php — Editable PEO list for a program.
    Livewire: ManagePeos  |  Alpine: peosManager()
--}}

<div x-data="peosManager(@js($peos))" class="space-y-2.5">

    @include('livewire.programs.include.flash-message')

    {{-- ── Pending changes summary bar ──────────────────────────────────── --}}
    <template x-if="pendingSummary().total > 0">
        <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-[13px]">
            <i class="bx bx-info-circle text-amber-500 shrink-0"></i>
            <span class="text-amber-700 font-medium">Unsaved changes:</span>
            <template x-if="pendingSummary().added > 0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[12px] font-semibold">
                    <i class="bx bx-plus text-xs"></i>
                    <span x-text="pendingSummary().added + ' new'"></span>
                </span>
            </template>
            <template x-if="pendingSummary().modified > 0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[12px] font-semibold">
                    <i class="bx bx-edit-alt text-xs"></i>
                    <span x-text="pendingSummary().modified + ' modified'"></span>
                </span>
            </template>
            <span class="ml-auto text-[12px] text-amber-600">Click <strong>Save All</strong> to apply.</span>
        </div>
    </template>

    {{-- ── PEO rows ──────────────────────────────────────────────────────── --}}
    <template x-for="(peo, index) in peos" :key="peo._key">

        <div class="rounded-xl border overflow-hidden transition-colors duration-200"
            :class="{
                'border-emerald-300 bg-emerald-50/40': !peo.id,
                'border-amber-300 bg-amber-50/30':     peo.id && peo._dirty,
                'border-[#e2e8f0] bg-white':           peo.id && !peo._dirty
            }"
            style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

            <div class="flex items-start gap-3 p-4">

                {{-- Code badge + state indicator --}}
                <div class="shrink-0 pt-0.5 flex flex-col items-center gap-1">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-[13px] font-bold transition-colors duration-200"
                        :class="{
                            'bg-[#dcfce7] text-[#166534] ring-1 ring-[#bbf7d0]': peo.id && !peo._dirty,
                            'bg-amber-100 text-amber-700 ring-1 ring-amber-300':  peo.id && peo._dirty,
                            'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300': !peo.id
                        }">
                        <span x-text="'PEO' + (index + 1)"></span>
                    </span>
                    {{-- State pill --}}
                    <span x-show="!peo.id" x-cloak
                        class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                        NEW
                    </span>
                    <span x-show="peo.id && peo._dirty" x-cloak
                        class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700">
                        EDITED
                    </span>
                </div>

                {{-- Textarea --}}
                <div class="flex-1 min-w-0">
                    <textarea
                        x-model="peo.peo_text"
                        @input="markDirty(peo)"
                        rows="3"
                        placeholder="Describe what graduates will be professionally three to five years after graduation…"
                        class="w-full rounded-lg border px-3 py-2 text-[13px] text-[#0f172a] placeholder:text-[#94a3b8]
                               focus:outline-none transition-colors resize-none"
                        :class="{
                            'border-amber-300 bg-amber-50/50 focus:border-amber-400': peo.id && peo._dirty,
                            'border-emerald-300 bg-emerald-50/50 focus:border-emerald-400': !peo.id,
                            'border-[#e2e8f0] bg-white focus:border-[#16a34a]': peo.id && !peo._dirty
                        }"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.18)'"
                        onblur="this.style.boxShadow='none'"></textarea>

                    <template x-if="peo.id && peo._dirty">
                        <p class="mt-1 flex items-center gap-1 text-[12px] text-amber-600">
                            <i class="bx bx-edit-alt text-sm shrink-0"></i>
                            Modified — not saved yet.
                        </p>
                    </template>
                    <template x-if="!peo.id">
                        <p class="mt-1 flex items-center gap-1 text-[12px] text-emerald-700">
                            <i class="bx bx-plus-circle text-sm shrink-0"></i>
                            New row — click <strong class="mx-0.5">Save All</strong> to persist.
                        </p>
                    </template>
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
                        class="mt-0.5 p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                        title="Delete saved PEO">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>

                {{-- REMOVE unsaved row --}}
                <button x-show="!peo.id" x-cloak
                    @click="peos.splice(index, 1)"
                    type="button"
                    class="mt-0.5 p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                    title="Remove unsaved PEO">
                    <i class="bx bx-x text-lg"></i>
                </button>

            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="peos.length === 0">
        <div class="rounded-xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] py-10 text-center">
            <i class="bx bx-graduation text-4xl text-[#94a3b8]"></i>
            <p class="mt-2 text-[13px] font-semibold text-[#475569]">No PEOs yet</p>
            <p class="text-[13px] text-[#94a3b8] mt-0.5">Add the first one below.</p>
        </div>
    </template>

    {{-- ── Action buttons ────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 pt-1">

        <x-button variant="add-dashed" type="button" @click="addPeo()" class="flex-1 w-full">
            <i class="bx bx-plus"></i> Add PEO
        </x-button>

        <x-button variant="add-button" type="button" @click="savePeos()"
            x-bind:disabled="isSaving" class="whitespace-nowrap relative">
            <template x-if="hasPending()">
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-white animate-pulse"></span>
            </template>
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
        peos:     initialPeos.map((p, i) => ({ ...p, _dirty: false, _original: p.peo_text, _key: p.id ?? ('new-' + i) })),
        isSaving: false,
        _keyCounter: initialPeos.length,

        markDirty(peo) {
            if (peo.id) peo._dirty = peo.peo_text !== peo._original;
        },

        hasPending() {
            return this.peos.some(p => !p.id || p._dirty);
        },

        pendingSummary() {
            const added    = this.peos.filter(p => !p.id).length;
            const modified = this.peos.filter(p => p.id && p._dirty).length;
            return { added, modified, total: added + modified };
        },

        addPeo() {
            const hasBlank = this.peos.some(p => !p.peo_text || !p.peo_text.trim());
            if (hasBlank) {
                window.dispatchEvent(new CustomEvent('lw-toast', {
                    detail: { type: 'warning', message: 'Fill in the blank PEO before adding another.' }
                }));
                return;
            }
            this._keyCounter++;
            this.peos.push({ id: null, peo_code: '', peo_text: '', _dirty: false, _original: '', _key: 'new-' + this._keyCounter });
        },

        savePeos() {
            this.isSaving = true;
            @this.call('savePeos', this.peos)
                .then(() => {
                    // Livewire will re-entangle peos after save; reset dirty on next tick
                    this.$nextTick(() => {
                        this.peos = this.peos.map(p => ({ ...p, _dirty: false, _original: p.peo_text }));
                    });
                })
                .finally(() => { this.isSaving = false; });
        }
    };
}
</script>
