<div>
    {{-- manage-peos.blade.php --}}
    <div x-data="peosManager(@js($peos))" class="space-y-4">

        @include('livewire.programs.partials.confirm-modal', ['confirmNs' => 'peo'])
        @include('livewire.programs.include.flash-message')

        {{-- Section header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 ring-1 ring-emerald-200">
                    <i class="bx bx-medal text-emerald-700 text-sm"></i>
                </span>
                <h3 class="text-[13px] font-bold text-slate-700">Program Educational Objectives</h3>
                <span class="inline-flex items-center justify-center min-w-[1.4rem] h-5 px-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold ring-1 ring-emerald-200"
                    x-text="peos.filter(p => p.id).length"></span>
            </div>
        </div>

        {{-- Pending changes bar --}}
        <template x-if="pendingSummary().total > 0">
            <div class="flex flex-wrap items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-[13px]">
                <i class="bx bx-error-circle text-amber-500 shrink-0"></i>
                <span class="text-amber-700 font-semibold">Unsaved changes:</span>
                <template x-if="pendingSummary().added > 0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">
                        <i class="bx bx-plus text-xs"></i>
                        <span x-text="pendingSummary().added + ' new'"></span>
                    </span>
                </template>
                <template x-if="pendingSummary().modified > 0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">
                        <i class="bx bx-edit-alt text-xs"></i>
                        <span x-text="pendingSummary().modified + ' modified'"></span>
                    </span>
                </template>
                <span class="ml-auto text-[11px] text-amber-600">Click <strong>Save All</strong> to apply.</span>
            </div>
        </template>

        {{-- PEO rows --}}
        <div class="space-y-2">
            <template x-for="(peo, index) in peos" :key="peo._key">
                <div class="rounded-xl border transition-all duration-200"
                    :class="{
                        'border-emerald-300 bg-emerald-50/40 shadow-emerald-100': !peo.id,
                        'border-amber-300 bg-amber-50/30 shadow-amber-100':       peo.id && peo._dirty,
                        'border-slate-200 bg-white shadow-slate-100':             peo.id && !peo._dirty
                    }"
                    style="box-shadow:0 1px 8px rgba(0,0,0,.05);">

                    <div class="flex items-start gap-3 px-4 py-3 border-l-4 border-green-700 rounded-xl shadow-lg">

                        {{-- Code badge --}}
                        <div class="shrink-0 flex flex-col items-center gap-1 mt-0.5">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-[11px] font-bold transition-colors"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 ring-2 ring-emerald-400': !peo.id,
                                    'bg-amber-100 text-amber-700 ring-2 ring-amber-400':       peo.id && peo._dirty,
                                    'bg-emerald-50 text-emerald-800 ring-2 ring-emerald-300':  peo.id && !peo._dirty
                                }">
                                <span class="font-bold uppercase" x-text="peo.peo_code"></span>
                            </span>
                        </div>

                        {{-- Textarea --}}
                        <div class="flex-1 min-w-0">
                            <textarea
                                x-model="peo.peo_text"
                                @input="markDirty(peo)"
                                rows="3"
                                placeholder="Describe what graduates will be professionally three to five years after graduation…"
                                class="w-full rounded-lg border px-3 py-2 text-[13px] text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition-all resize-none leading-relaxed"
                                :class="{
                                    'border-amber-300 bg-amber-50/50 focus:border-amber-400 focus:ring-amber-100':       peo.id && peo._dirty,
                                    'border-emerald-300 bg-emerald-50/50 focus:border-emerald-400 focus:ring-emerald-100': !peo.id,
                                    'border-slate-200 bg-white focus:border-emerald-400 focus:ring-emerald-100':           peo.id && !peo._dirty
                                }"></textarea>

                            <template x-if="!peo.id">
                                <p class="mt-1 flex items-center gap-1 text-[11px] text-emerald-600 font-medium">
                                    <i class="bx bx-plus-circle text-sm shrink-0"></i>
                                    New — click <strong class="mx-0.5">Save All</strong> to persist.
                                </p>
                            </template>
                            <template x-if="peo.id && peo._dirty">
                                <p class="mt-1 flex items-center gap-1 text-[11px] text-amber-600 font-medium">
                                    <i class="bx bx-edit-alt text-sm shrink-0"></i>
                                    Modified — not saved yet.
                                </p>
                            </template>
                        </div>

                        {{-- Delete saved --}}
                        <button x-show="peo.id" type="button" @click="deletePeo(peo)"
                            class="shrink-0 mt-0.5 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                            title="Delete PEO">
                            <i class="bx bx-trash text-base"></i>
                        </button>

                        {{-- Remove unsaved --}}
                        <button x-show="!peo.id" x-cloak type="button" @click="peos.splice(index, 1)"
                            class="shrink-0 mt-0.5 p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                            title="Remove">
                            <i class="bx bx-x text-lg"></i>
                        </button>

                    </div>
                </div>
            </template>
        </div>

        {{-- Empty state --}}
        <template x-if="peos.length === 0">
            <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 py-12 text-center">
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 mb-3">
                    <i class="bx bx-graduation text-3xl text-slate-300"></i>
                </span>
                <p class="text-[13px] font-semibold text-slate-500">No PEOs yet</p>
                <p class="text-[12px] text-slate-400 mt-0.5">Click <strong>Add PEO</strong> below to get started.</p>
            </div>
        </template>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
            <x-ui.button variant="add-dashed" type="button" @click="addPeo()" class="flex-1">
                <i class="bx bx-plus text-base"></i> Add PEO
            </x-ui.button>

            <template x-if="hasPending()">
                <x-ui.button variant="cancel" type="button" @click="revert()">
                    <i class="bx bx-undo text-base leading-none"></i> Revert
                </x-ui.button>
            </template>

            <x-ui.button variant="add-button" type="button" @click="savePeos()"
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
            </x-ui.button>
        </div>

    </div>

    {{-- Hidden delete forms --}}
    @foreach($peos as $peo)
        @if(!empty($peo['id']))
            <form id="peo-delete-form-{{ $peo['id'] }}" method="POST"
                action="{{ route('programs.peo.delete', $peo['id']) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endforeach

    <script>
    function peosManager(initialPeos) {
        return {
            peos:        initialPeos.map((p, i) => ({ ...p, _dirty: false, _original: p.peo_text, _key: p.id ?? ('new-' + i) })),
            isSaving:    false,
            _keyCounter: initialPeos.length,

            init() {
                this.$el.closest('[wire\\:id]')?.addEventListener('peos-saved', (e) => {
                    const fresh = e.detail[0] ?? e.detail.peos ?? [];
                    this.peos = fresh.map((p, i) => ({
                        ...p, _dirty: false, _original: p.peo_text,
                        _key: p.id ?? ('new-' + i)
                    }));
                });
            },

            markDirty(peo) {
                if (peo.id) peo._dirty = (peo.peo_text !== peo._original);
            },

            hasPending() {
                return this.peos.some(p => !p.id || p._dirty);
            },

            pendingSummary() {
                return {
                    added:    this.peos.filter(p => !p.id).length,
                    modified: this.peos.filter(p => p.id && p._dirty).length,
                    get total() { return this.added + this.modified; }
                };
            },

            revert() {
                // Remove unsaved rows, reset dirty rows to their original text
                this.peos = this.peos
                    .filter(p => p.id)
                    .map(p => ({ ...p, peo_text: p._original, _dirty: false }));
            },

            addPeo() {
                if (this.peos.some(p => !p.peo_text?.trim())) {
                    window.dispatchEvent(new CustomEvent('lw-toast', {
                        detail: { type: 'warning', message: 'Fill in the blank PEO before adding another.' }
                    }));
                    return;
                }
                this._keyCounter++;
                this.peos.push({ id: null, peo_code: '', peo_text: '', _dirty: false, _original: '', _key: 'new-' + this._keyCounter });
            },

            confirm(detail) {
                return new Promise(resolve => {
                    window.dispatchEvent(new CustomEvent('confirm-dialog:peo', {
                        detail: { ...detail, _resolve: resolve }
                    }));
                });
            },

            async deletePeo(peo) {
                const ok = await this.confirm({
                    title: 'Delete PEO?',
                    message: 'This PEO will be permanently removed and all codes will be re-sequenced.',
                    confirmLabel: 'Delete',
                    confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white'
                });
                if (!ok) return;
                const form = document.getElementById('peo-delete-form-' + peo.id);
                if (form) form.submit();
            },

            async savePeos() {
                if (!this.hasPending()) {
                    window.dispatchEvent(new CustomEvent('lw-toast', { detail: { type: 'info', message: 'No changes to save.' } }));
                    return;
                }
                const ok = await this.confirm({
                    title: 'Save all PEOs?',
                    message: 'This will save all new and modified PEOs and re-sequence codes.',
                    confirmLabel: 'Save All',
                    confirmClass: 'bg-emerald-600 hover:bg-emerald-700 text-white'
                });
                if (!ok) return;

                this.isSaving = true;
                @this.call('savePeos', this.peos.map(p => ({ id: p.id, peo_text: p.peo_text })))
                    .catch(() => {})
                    .finally(() => { this.isSaving = false; });
            }
        };
    }
    </script>

</div>
